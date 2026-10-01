{{-- resources/views/properties/form.blade.php --}}
@php($editing = $property->exists)
<x-layouts.app :title="($editing ? 'Modifier' : 'Nouveau bien').' — MEEXEO'">
    <x-page-header :title="$editing ? 'Modifier le bien' : 'Nouveau bien'"
                   :subtitle="$editing ? $property->reference : 'Renseignez la localisation et les conditions de location.'" />

    <form method="POST" action="{{ $editing ? route('properties.update', $property) : route('properties.store') }}"
          class="mt-6 max-w-3xl space-y-6">
        @csrf
        @if ($editing) @method('PUT') @endif

        <section style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:24px">
            <h2 class="font-titre text-lg">Identité</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="title" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Nom du bien</label>
                    <input id="title" name="title" value="{{ old('title', $property->title) }}" required
                           class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('title') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="type" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Type de bien</label>
                    <select id="type" name="type" class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                        @foreach ($types as $value => $label)
                            <option value="{{ $value }}" @selected(old('type', $property->type?->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">État</label>
                    <select id="status" name="status" class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $property->status?->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:24px">
            <h2 class="font-titre text-lg">Localisation</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                @foreach ([
                    'city' => 'Ville',
                    'commune' => 'Commune',
                    'district' => 'Quartier',
                    'lot_number' => 'Numéro de lot',
                    'block_number' => "Numéro d'îlot",
                ] as $field => $label)
                    <div>
                        <label for="{{ $field }}" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">{{ $label }}</label>
                        <input id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $property->{$field}) }}"
                               class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                        @error($field) <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                    </div>
                @endforeach
            </div>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="latitude" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Latitude GPS</label>
                    <input id="latitude" name="latitude" value="{{ old('latitude', $property->latitude) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('latitude') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="longitude" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Longitude GPS</label>
                    <input id="longitude" name="longitude" value="{{ old('longitude', $property->longitude) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('longitude') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:24px">
            <h2 class="font-titre text-lg">Caractéristiques et conditions</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="rooms" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Nombre de pièces</label>
                    <input id="rooms" name="rooms" type="number" min="0" value="{{ old('rooms', $property->rooms) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('rooms') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="area_sqm" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Superficie (m²)</label>
                    <input id="area_sqm" name="area_sqm" type="number" min="0" value="{{ old('area_sqm', $property->area_sqm) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('area_sqm') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="monthly_rent" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Loyer mensuel (FCFA)</label>
                    <input id="monthly_rent" name="monthly_rent" type="number" step="1" min="0" required
                           value="{{ old('monthly_rent', $property->monthly_rent) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('monthly_rent') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="deposit" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Caution (FCFA)</label>
                    <input id="deposit" name="deposit" type="number" step="1" min="0" required
                           value="{{ old('deposit', $property->deposit) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('deposit') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="notes" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="mt-1.5 w-full px-3 py-2 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">{{ old('notes', $property->notes) }}</textarea>
                    @error('notes') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="min-h-[44px] px-5 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
                {{ $editing ? 'Enregistrer les modifications' : 'Enregistrer le bien' }}
            </button>
            {{-- Chemin littéral et non route('properties.show', ...) : cette route n'existe
                 qu'à partir de la Task 10. Son URI sera exactement /biens/{id}, donc ce lien
                 restera correct sans modification une fois la route déclarée. --}}
            <a href="{{ $editing ? "/biens/{$property->id}" : route('properties.index') }}"
               class="inline-flex min-h-[44px] items-center px-4 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);color:var(--color-mc-ink)">Annuler</a>
        </div>
    </form>
</x-layouts.app>
