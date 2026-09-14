{{-- resources/views/properties/show.blade.php --}}
@php($canWrite = in_array(auth()->user()->role, [\App\Enums\Role::Admin, \App\Enums\Role::Manager], true))
<x-layouts.app :title="$property->title.' — MEEXEO'">
    <x-page-header :title="$property->title" :subtitle="$property->reference">
        <x-slot:actions>
            @if ($canWrite)
                <a href="{{ route('properties.edit', $property) }}"
                   class="inline-flex min-h-[44px] items-center px-4 text-sm"
                   style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                    Modifier le bien
                </a>
            @endif
            <a href="{{ route('properties.index') }}"
               class="inline-flex min-h-[44px] items-center px-2 text-sm" style="color:var(--color-mc-ink-faint)">Retour à la liste</a>
        </x-slot:actions>
    </x-page-header>

    @if (session('status'))
        <p class="mt-4 px-4 py-3 text-sm" style="border-radius:var(--radius-mc);border:1px solid rgba(22,163,74,.35);background:rgba(22,163,74,.08);color:var(--color-mc-success)">{{ session('status') }}</p>
    @endif

    {{-- Bandeau : galerie à gauche, identité à droite --}}
    <div class="mt-6 grid gap-5 lg:grid-cols-[1.2fr_1fr]">
        <div class="p-4" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
            @if ($photo = $property->primaryPhoto)
                <img src="{{ $photo->url }}" alt="{{ $property->title }}"
                     class="h-[296px] w-full object-cover" style="border-radius:var(--radius-mc-sm)">
            @else
                <div class="flex h-[296px] items-center justify-center border border-dashed text-sm"
                     style="border-radius:var(--radius-mc-sm);border-color:var(--color-mc-border);color:var(--color-mc-ink-faint)">
                    Aucune photo pour ce bien
                </div>
            @endif

            @if ($property->photos->count() > 1)
                <div class="mt-3 grid grid-cols-4 gap-2">
                    @foreach ($property->photos->take(4) as $thumb)
                        <img src="{{ $thumb->url }}" alt="" class="h-16 w-full object-cover" style="border-radius:var(--radius-mc-sm)">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
            <div class="flex items-start justify-between gap-3">
                <p style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">{{ $property->type->label() }}</p>
                <x-status-badge :status="$property->status->value" />
            </div>
            <p class="mt-3 text-sm" style="color:var(--color-mc-ink-soft)">{{ $property->full_address }}</p>

            <div class="mt-5 grid grid-cols-2 gap-4 border-t pt-5" style="border-color:var(--color-mc-border-soft)">
                <div>
                    <p style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Loyer mensuel</p>
                    <p class="chiffre mt-1 text-2xl font-semibold">{{ \App\Support\Money::fcfa($property->monthly_rent) }}</p>
                </div>
                <div>
                    <p style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Caution</p>
                    <p class="chiffre mt-1 text-2xl font-semibold">{{ \App\Support\Money::fcfa($property->deposit) }}</p>
                </div>
            </div>

            <dl class="mt-5 grid grid-cols-2 gap-x-4 gap-y-3 border-t pt-5 text-sm" style="border-color:var(--color-mc-border-soft)">
                <div><dt style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Pièces</dt><dd class="chiffre mt-0.5">{{ $property->rooms ?? '—' }}</dd></div>
                <div><dt style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Superficie</dt><dd class="chiffre mt-0.5">{{ $property->area_sqm ? $property->area_sqm.' m²' : '—' }}</dd></div>
                <div><dt style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Lot</dt><dd class="chiffre mt-0.5">{{ $property->lot_number ?? '—' }}</dd></div>
                <div><dt style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Îlot</dt><dd class="chiffre mt-0.5">{{ $property->block_number ?? '—' }}</dd></div>
            </dl>

            <div class="mt-5 border-t pt-5" style="border-color:var(--color-mc-border-soft)">
                <p style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Locataire en place</p>
                @if ($lease = $property->activeLease)
                    <a href="{{ route('tenants.show', $lease->tenant) }}" class="mt-1 block font-titre text-lg hover:[color:var(--color-mc-accent)]">
                        {{ $lease->tenant->full_name }}
                    </a>
                    <p class="chiffre mt-0.5 text-xs" style="color:var(--color-mc-ink-faint)">
                        Depuis le {{ $lease->start_date->format('d/m/Y') }}
                        · Loyer {{ \App\Support\Money::fcfa($lease->monthly_rent) }}
                        · Échéance le {{ $lease->due_day }} du mois
                    </p>
                    @if ($canWrite)
                        <form method="POST" action="{{ route('leases.end', $lease) }}" class="mt-3 flex flex-wrap items-end gap-2">
                            @csrf @method('PATCH')
                            <div>
                                <label for="actual_end_date" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Date de fin réelle</label>
                                <input id="actual_end_date" name="actual_end_date" type="date" required
                                       class="chiffre mt-1.5 min-h-[44px] px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                            </div>
                            <button class="min-h-[44px] px-2 text-sm" style="color:var(--color-mc-danger)">Clôturer le bail</button>
                            @error('actual_end_date') <p class="text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                        </form>
                    @endif
                @else
                    <p class="mt-1 text-sm" style="color:var(--color-mc-ink-faint)">Aucun locataire en place.</p>
                    @if ($canWrite)
                        <a href="{{ route('leases.create', ['property' => $property->id]) }}"
                           class="mt-3 inline-flex min-h-[44px] items-center px-4 text-sm font-semibold"
                           style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
                            Affecter à un locataire
                        </a>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Une erreur de validation sur l'envoi d'un document doit ouvrir l'onglet
         Documents au chargement : sinon elle reste invisible sous l'onglet Détails. --}}
    @php($initialTab = $errors->hasAny('photo') ? 'photos' : ($errors->hasAny(['file', 'type']) ? 'documents' : null))
    <x-tabs :tabs="['details' => 'Détails', 'photos' => 'Photos', 'documents' => 'Documents', 'historique' => 'Historique']" :initial="$initialTab">
        <x-slot:panel_details>
            <div class="p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                <h2 class="font-titre text-lg">Localisation et repérage</h2>
                <dl class="mt-4 grid gap-4 sm:grid-cols-2 text-sm">
                    <div><dt style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Ville</dt><dd class="mt-0.5">{{ $property->city }}</dd></div>
                    <div><dt style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Commune</dt><dd class="mt-0.5">{{ $property->commune ?? '—' }}</dd></div>
                    <div><dt style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Quartier</dt><dd class="mt-0.5">{{ $property->district ?? '—' }}</dd></div>
                    <div>
                        <dt style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Coordonnées GPS</dt>
                        <dd class="chiffre mt-0.5">
                            {{ $property->latitude && $property->longitude ? $property->latitude.', '.$property->longitude : 'Non renseignées' }}
                        </dd>
                    </div>
                </dl>
                @if ($property->notes)
                    <div class="mt-5 border-t pt-5" style="border-color:var(--color-mc-border-soft)">
                        <p style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Notes</p>
                        <p class="mt-1 text-sm" style="color:var(--color-mc-ink-soft)">{{ $property->notes }}</p>
                    </div>
                @endif
            </div>
        </x-slot:panel_details>

        <x-slot:panel_photos>
            @if ($canWrite)
                <form method="POST" action="{{ route('properties.photos.store', $property) }}" enctype="multipart/form-data"
                      class="mb-5 flex flex-wrap items-center gap-3 border border-dashed p-4"
                      style="border-radius:var(--radius-mc);border-color:var(--color-mc-border);background:var(--color-mc-surface)">
                    @csrf
                    <input type="file" name="photo" accept="image/*" required class="text-sm">
                    <button class="min-h-[44px] px-4 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">Ajouter la photo</button>
                    @error('photo') <p class="text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </form>
            @endif

            @if ($property->photos->isEmpty())
                <p class="p-8 text-center text-sm" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);color:var(--color-mc-ink-faint)">Aucune photo pour ce bien.</p>
            @else
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($property->photos as $photo)
                        <div class="p-2" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                            <img src="{{ $photo->url }}" alt="" class="h-36 w-full object-cover" style="border-radius:var(--radius-mc-sm)">
                            <div class="mt-2 flex items-center justify-between gap-2">
                                @if ($photo->is_primary)
                                    <span style="font-size:11.5px;font-weight:700;letter-spacing:.4px;color:var(--color-mc-accent)">Principale</span>
                                @elseif ($canWrite)
                                    <form method="POST" action="{{ route('properties.photos.primary', [$property, $photo]) }}">
                                        @csrf @method('PATCH')
                                        <button class="min-h-[44px] text-xs" style="color:var(--color-mc-ink-faint)">Définir principale</button>
                                    </form>
                                @endif
                                @if ($canWrite)
                                    <form method="POST" action="{{ route('properties.photos.destroy', [$property, $photo]) }}">
                                        @csrf @method('DELETE')
                                        <button class="min-h-[44px] text-xs" style="color:var(--color-mc-danger)">Supprimer</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-slot:panel_photos>

        <x-slot:panel_documents>
            @if ($canWrite)
                <form method="POST" action="{{ route('properties.documents.store', $property) }}" enctype="multipart/form-data"
                      class="mb-5 flex flex-wrap items-end gap-3 border border-dashed p-4"
                      style="border-radius:var(--radius-mc);border-color:var(--color-mc-border);background:var(--color-mc-surface)">
                    @csrf
                    <div>
                        <label for="doc_type" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Type</label>
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

            @if ($property->documents->isEmpty())
                <p class="p-8 text-center text-sm" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);color:var(--color-mc-ink-faint)">Aucun document joint.</p>
            @else
                <ul class="divide-y divide-[var(--color-mc-border-soft)]" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                    @foreach ($property->documents as $document)
                        <li class="flex flex-wrap items-center justify-between gap-3 p-4">
                            <div>
                                <p style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">{{ $document->type->label() }}</p>
                                <a href="{{ $document->url }}" target="_blank" rel="noopener"
                                   class="text-sm hover:underline" style="color:var(--color-mc-ink-faint)">{{ $document->original_name }}</a>
                            </div>
                            <div class="flex items-center gap-3">
                                <x-status-badge :status="$document->verified ? 'occupied' : 'works'" />
                                @if ($canWrite)
                                    <form method="POST" action="{{ route('properties.documents.destroy', [$property, $document]) }}">
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

        <x-slot:panel_historique>
            @if ($property->leases->isEmpty())
                <p class="p-8 text-center text-sm" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);color:var(--color-mc-ink-faint)">
                    Ce bien n&#039;a jamais été loué.
                </p>
            @else
                {{-- Frise verticale, du bail le plus récent au plus ancien --}}
                <ol class="p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                    @foreach ($property->leases as $lease)
                        <li class="relative border-l pl-6 pb-6 last:pb-0" style="border-color:var(--color-mc-border)">
                            <span class="absolute -left-[5px] top-1.5 h-2.5 w-2.5 rounded-full" style="background:{{ $lease->status === \App\Enums\LeaseStatus::Active ? 'var(--color-mc-accent)' : 'var(--color-mc-border)' }}"></span>
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <a href="{{ route('tenants.show', $lease->tenant) }}" class="font-titre text-base hover:[color:var(--color-mc-accent)]">
                                        {{ $lease->tenant->full_name }}
                                    </a>
                                    <p class="chiffre mt-0.5 text-xs" style="color:var(--color-mc-ink-faint)">
                                        {{ $lease->start_date->format('d/m/Y') }}
                                        —
                                        {{ $lease->actual_end_date?->format('d/m/Y') ?? 'en cours' }}
                                        · {{ $lease->duration_in_months }} mois
                                    </p>
                                </div>
                                <div class="text-right">
                                    {{-- Le loyer de l'époque, pas celui du bien aujourd'hui --}}
                                    <p class="chiffre font-semibold">{{ \App\Support\Money::fcfa($lease->monthly_rent) }}</p>
                                    <p class="mt-1"><x-status-badge :status="$lease->status === \App\Enums\LeaseStatus::Active ? 'occupied' : 'ended'" /></p>
                                </div>
                            </div>
                            @if ($lease->notes)
                                <p class="mt-2 text-xs" style="color:var(--color-mc-ink-soft)">{{ $lease->notes }}</p>
                            @endif
                        </li>
                    @endforeach
                </ol>
            @endif
        </x-slot:panel_historique>
    </x-tabs>
</x-layouts.app>
