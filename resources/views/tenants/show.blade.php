{{-- resources/views/tenants/show.blade.php --}}
@php($canWrite = in_array(auth()->user()->role, [\App\Enums\Role::Admin, \App\Enums\Role::Manager], true))
<x-layouts.app :title="$tenant->full_name.' — MEEXEO'">
    <x-page-header :title="$tenant->full_name" :subtitle="$tenant->reference">
        <x-slot:actions>
            @if ($canWrite)
                <a href="{{ route('tenants.edit', $tenant) }}"
                   class="inline-flex min-h-[44px] items-center rounded-meexeo border border-galet bg-papier px-4 text-sm">
                    Modifier la fiche
                </a>
            @endif
            <a href="{{ route('tenants.index') }}"
               class="inline-flex min-h-[44px] items-center px-2 text-sm text-acier">Retour à la liste</a>
        </x-slot:actions>
    </x-page-header>

    @if (session('status'))
        <p class="mt-4 rounded-meexeo border border-ok-bord bg-ok-bg px-4 py-3 text-sm text-ok-texte">{{ session('status') }}</p>
    @endif

    <div class="mt-6 flex flex-wrap items-center gap-4 rounded-meexeo border border-lin-clair bg-papier p-5">
        <span class="flex h-14 w-14 items-center justify-center rounded-full bg-sable font-titre text-lg">{{ $tenant->initials }}</span>
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-3">
                <p class="font-titre text-xl">{{ $tenant->full_name }}</p>
                <x-status-badge :status="$tenant->status->value" />
            </div>
            <p class="mt-1 text-xs text-ardoise">
                <span class="chiffre">{{ $tenant->phone1 }}</span>
                @if ($tenant->occupation) · {{ $tenant->occupation }} @endif
                @if ($tenant->workplace) · {{ $tenant->workplace }} @endif
            </p>
        </div>
    </div>

    @if ($lease = $tenant->activeLease)
        <div class="mt-4 rounded-meexeo border border-lin-clair bg-papier p-4">
            <p class="surtitre">Bien occupé</p>
            <a href="{{ route('properties.show', $lease->property) }}" class="mt-1 block font-titre text-lg hover:text-cuivre">
                {{ $lease->property->title }}
            </a>
            <p class="mt-0.5 text-xs text-ardoise">{{ $lease->property->full_address }}</p>
            <p class="chiffre mt-1 text-xs text-brume">
                Loyer {{ \App\Support\Money::fcfa($lease->monthly_rent) }}
                · Échéance le {{ $lease->due_day }} du mois
            </p>
        </div>
    @else
        <p class="mt-4 text-sm text-brume">Ce locataire n'occupe aucun bien actuellement.</p>
    @endif

    {{-- Une erreur de validation sur l'envoi d'un document doit ouvrir l'onglet
         Documents au chargement : sinon elle reste invisible sous l'onglet Identité. --}}
    @php($initialTab = $errors->hasAny(['file', 'type']) ? 'documents' : null)
    <x-tabs :tabs="['identite' => 'Identité', 'documents' => 'Documents']" :initial="$initialTab">
    <x-slot:panel_identite>
        <div class="rounded-meexeo border border-lin-clair bg-papier p-6">
            <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                <div><dt class="surtitre">Date de naissance</dt><dd class="chiffre mt-0.5">{{ $tenant->birth_date?->format('d/m/Y') ?? '—' }}</dd></div>
                <div><dt class="surtitre">N° CNI / Passeport</dt><dd class="chiffre mt-0.5">{{ $tenant->id_number ?? '—' }}</dd></div>
                <div><dt class="surtitre">Situation matrimoniale</dt><dd class="mt-0.5">{{ $tenant->marital_status?->label() ?? '—' }}</dd></div>
                <div><dt class="surtitre">Profession</dt><dd class="mt-0.5">{{ $tenant->occupation ?? '—' }}</dd></div>
                <div><dt class="surtitre">Lieu de travail</dt><dd class="mt-0.5">{{ $tenant->workplace ?? '—' }}</dd></div>
                <div><dt class="surtitre">Téléphone</dt><dd class="chiffre mt-0.5">{{ $tenant->phone1 }}{{ $tenant->phone2 ? ' · '.$tenant->phone2 : '' }}</dd></div>
                <div><dt class="surtitre">Email</dt><dd class="mt-0.5">{{ $tenant->email ?? '—' }}</dd></div>
                <div><dt class="surtitre">Contact d'urgence</dt><dd class="mt-0.5">{{ $tenant->emergency_name ? $tenant->emergency_name.' · '.$tenant->emergency_phone : '—' }}</dd></div>
                @if ($tenant->marital_status?->requiresSpouse())
                    <div><dt class="surtitre">Conjoint</dt><dd class="mt-0.5">{{ $tenant->spouse_name }} · {{ $tenant->spouse_phone }}</dd></div>
                @endif
            </dl>
        </div>
    </x-slot:panel_identite>

        <x-slot:panel_documents>
            @if ($canWrite)
                <form method="POST" action="{{ route('tenants.documents.store', $tenant) }}" enctype="multipart/form-data"
                      class="mb-5 flex flex-wrap items-end gap-3 rounded-meexeo border border-dashed border-lin bg-papier p-4">
                    @csrf
                    <div>
                        <label for="doc_type" class="text-xs font-semibold text-ardoise">Type de pièce</label>
                        <select id="doc_type" name="type" class="mt-1.5 min-h-[44px] rounded-meexeo border border-lin px-3 text-sm">
                            @foreach ($documentTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="file" name="file" accept=".pdf,image/*" required class="text-sm">
                    <button class="min-h-[44px] rounded-meexeo bg-cuivre px-4 text-sm font-semibold text-papier">Joindre</button>
                    @error('file') <p class="text-xs text-terre">{{ $message }}</p> @enderror
                    @error('type') <p class="text-xs text-terre">{{ $message }}</p> @enderror
                </form>
            @endif

            @if ($tenant->documents->isEmpty())
                <p class="rounded-meexeo border border-lin-clair bg-papier p-8 text-center text-sm text-brume">
                    Aucune pièce justificative pour ce locataire.
                </p>
            @else
                <ul class="divide-y divide-lin-pale rounded-meexeo border border-lin-clair bg-papier">
                    @foreach ($tenant->documents as $document)
                        <li class="flex flex-wrap items-center justify-between gap-3 p-4">
                            <div>
                                <p class="surtitre">{{ $document->type->label() }}</p>
                                <a href="{{ $document->url }}" target="_blank" rel="noopener"
                                   class="text-sm text-acier hover:underline">{{ $document->original_name }}</a>
                            </div>
                            <div class="flex items-center gap-3">
                                <x-status-badge :status="$document->verified ? 'occupied' : 'works'" />
                                @if ($canWrite)
                                    <form method="POST" action="{{ route('tenants.documents.destroy', [$tenant, $document]) }}">
                                        @csrf @method('DELETE')
                                        <button class="min-h-[44px] text-xs text-terre">Supprimer</button>
                                    </form>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-slot:panel_documents>
    </x-tabs>
</x-layouts.app>
