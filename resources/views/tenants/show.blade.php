{{-- resources/views/tenants/show.blade.php --}}
@php($canWrite = in_array(auth()->user()->role, [\App\Enums\Role::Admin, \App\Enums\Role::Manager], true))
<x-layouts.app :title="$tenant->full_name.' — MEEXEO'">
    <x-page-header :title="$tenant->full_name" :subtitle="$tenant->reference">
        <x-slot:actions>
            @if ($canWrite)
                <a href="{{ route('tenants.edit', $tenant) }}"
                   class="inline-flex min-h-[44px] items-center px-4 text-sm"
                   style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                    Modifier la fiche
                </a>
            @endif
            <a href="{{ route('tenants.index') }}"
               class="inline-flex min-h-[44px] items-center px-2 text-sm" style="color:var(--color-mc-ink-faint)">Retour à la liste</a>
        </x-slot:actions>
    </x-page-header>

    @if (session('status'))
        <p class="mt-4 px-4 py-3 text-sm" style="border-radius:var(--radius-mc);border:1px solid rgba(22,163,74,.35);background:rgba(22,163,74,.08);color:var(--color-mc-success)">{{ session('status') }}</p>
    @endif

    <div class="mt-6 flex flex-wrap items-center gap-4 p-5" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
        <span class="flex h-14 w-14 items-center justify-center rounded-full font-titre text-lg" style="background:var(--color-mc-canvas)">{{ $tenant->initials }}</span>
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-3">
                <p class="font-titre text-xl">{{ $tenant->full_name }}</p>
                <x-status-badge :status="$tenant->status->value" />
            </div>
            <p class="mt-1 text-xs" style="color:var(--color-mc-ink-soft)">
                <span class="chiffre">{{ $tenant->phone1 }}</span>
                @if ($tenant->occupation) · {{ $tenant->occupation }} @endif
                @if ($tenant->workplace) · {{ $tenant->workplace }} @endif
            </p>
        </div>
    </div>

    @if ($lease = $tenant->activeLease)
        <div class="mt-4 p-4" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
            <p style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Bien occupé</p>
            <a href="{{ route('properties.show', $lease->property) }}" class="mt-1 block font-titre text-lg hover:[color:var(--color-mc-accent)]">
                {{ $lease->property->title }}
            </a>
            <p class="mt-0.5 text-xs" style="color:var(--color-mc-ink-soft)">{{ $lease->property->full_address }}</p>
            <p class="chiffre mt-1 text-xs" style="color:var(--color-mc-ink-faint)">
                Loyer {{ \App\Support\Money::fcfa($lease->monthly_rent) }}
                · Échéance le {{ $lease->due_day }} du mois
            </p>
        </div>
    @else
        <p class="mt-4 text-sm" style="color:var(--color-mc-ink-faint)">Ce locataire n'occupe aucun bien actuellement.</p>
    @endif

    {{-- Une erreur de validation sur l'envoi d'un document doit ouvrir l'onglet
         Documents au chargement : sinon elle reste invisible sous l'onglet Identité. --}}
    @php($initialTab = $errors->hasAny(['file', 'type']) ? 'documents' : null)
    <x-tabs :tabs="['identite' => 'Identité', 'documents' => 'Documents', 'paiements' => 'Paiements']" :initial="$initialTab">
    <x-slot:panel_identite>
        <div class="p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
            <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                <div><dt style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Date de naissance</dt><dd class="chiffre mt-0.5">{{ $tenant->birth_date?->format('d/m/Y') ?? '—' }}</dd></div>
                <div><dt style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">N° CNI / Passeport</dt><dd class="chiffre mt-0.5">{{ $tenant->id_number ?? '—' }}</dd></div>
                <div><dt style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Situation matrimoniale</dt><dd class="mt-0.5">{{ $tenant->marital_status?->label() ?? '—' }}</dd></div>
                <div><dt style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Profession</dt><dd class="mt-0.5">{{ $tenant->occupation ?? '—' }}</dd></div>
                <div><dt style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Lieu de travail</dt><dd class="mt-0.5">{{ $tenant->workplace ?? '—' }}</dd></div>
                <div><dt style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Téléphone</dt><dd class="chiffre mt-0.5">{{ $tenant->phone1 }}{{ $tenant->phone2 ? ' · '.$tenant->phone2 : '' }}</dd></div>
                <div><dt style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Email</dt><dd class="mt-0.5">{{ $tenant->email ?? '—' }}</dd></div>
                <div><dt style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Contact d'urgence</dt><dd class="mt-0.5">{{ $tenant->emergency_name ? $tenant->emergency_name.' · '.$tenant->emergency_phone : '—' }}</dd></div>
                @if ($tenant->marital_status?->requiresSpouse())
                    <div><dt style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Conjoint</dt><dd class="mt-0.5">{{ $tenant->spouse_name }} · {{ $tenant->spouse_phone }}</dd></div>
                @endif
            </dl>
        </div>
    </x-slot:panel_identite>

        <x-slot:panel_documents>
            @if ($canWrite)
                <form method="POST" action="{{ route('tenants.documents.store', $tenant) }}" enctype="multipart/form-data"
                      class="mb-5 flex flex-wrap items-end gap-3 border border-dashed p-4"
                      style="border-radius:var(--radius-mc);border-color:var(--color-mc-border);background:var(--color-mc-surface)">
                    @csrf
                    <div>
                        <label for="doc_type" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Type de pièce</label>
                        <select id="doc_type" name="type" class="mt-1.5 min-h-[44px] px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                            @foreach ($documentTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="file" name="file" accept=".pdf,image/*" required class="text-sm">
                    <button class="min-h-[44px] px-4 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">Joindre</button>
                    @error('file') <p class="text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                    @error('type') <p class="text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </form>
            @endif

            @if ($tenant->documents->isEmpty())
                <p class="p-8 text-center text-sm" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);color:var(--color-mc-ink-faint)">
                    Aucune pièce justificative pour ce locataire.
                </p>
            @else
                <ul class="divide-y divide-[var(--color-mc-border-soft)]" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                    @foreach ($tenant->documents as $document)
                        <li class="flex flex-wrap items-center justify-between gap-3 p-4">
                            <div>
                                <p style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">{{ $document->type->label() }}</p>
                                <a href="{{ $document->url }}" target="_blank" rel="noopener"
                                   class="text-sm hover:underline" style="color:var(--color-mc-ink-faint)">{{ $document->original_name }}</a>
                            </div>
                            <div class="flex items-center gap-3">
                                <x-status-badge :status="$document->verified ? 'occupied' : 'works'" />
                                @if ($canWrite)
                                    <form method="POST" action="{{ route('tenants.documents.destroy', [$tenant, $document]) }}">
                                        @csrf @method('DELETE')
                                        <button class="min-h-[44px] text-xs" style="color:var(--color-mc-danger)">Supprimer</button>
                                    </form>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-slot:panel_documents>

        <x-slot:panel_paiements>
            @if ($tenant->payments->isEmpty())
                <p class="p-8 text-center text-sm" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);color:var(--color-mc-ink-faint)">
                    Aucun paiement enregistré pour ce locataire.
                </p>
            @else
                <div class="overflow-x-auto" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-left" style="border-color:var(--color-mc-border);background:var(--color-mc-table-head)">
                                <th style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px" class="px-4 py-3">Mois</th>
                                <th style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px" class="px-4 py-3">Mode</th>
                                <th style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px" class="px-4 py-3">Date</th>
                                <th style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px" class="px-4 py-3 text-right">Montant</th>
                                <th style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px" class="px-4 py-3">Preuve</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tenant->payments as $payment)
                                <tr class="border-b last:border-0" style="border-color:var(--color-mc-border-soft)">
                                    <td class="px-4 py-3">{{ ucfirst($payment->month->translatedFormat('F Y')) }}</td>
                                    <td class="px-4 py-3">{{ $payment->method->label() }}</td>
                                    <td class="chiffre px-4 py-3">{{ $payment->paid_on->format('d/m/Y') }}</td>
                                    <td class="chiffre px-4 py-3 text-right font-semibold">{{ \App\Support\Money::fcfa($payment->amount) }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ $payment->url }}" target="_blank" rel="noopener" class="hover:underline" style="color:var(--color-mc-ink-faint)">Voir</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-slot:panel_paiements>
    </x-tabs>
</x-layouts.app>
