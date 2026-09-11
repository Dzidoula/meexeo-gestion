{{-- resources/views/properties/form.blade.php --}}
@php($editing = $property->exists)
<x-layouts.app :title="($editing ? 'Modifier' : 'Nouveau bien').' — MEEXEO'">
    <x-page-header :title="$editing ? 'Modifier le bien' : 'Nouveau bien'"
                   :subtitle="$editing ? $property->reference : 'Renseignez la localisation et les conditions de location.'" />

    <form method="POST" action="{{ $editing ? route('properties.update', $property) : route('properties.store') }}"
          class="mt-6 max-w-3xl space-y-6">
        @csrf
        @if ($editing) @method('PUT') @endif

        <section class="rounded-meexeo border border-lin-clair bg-papier p-6">
            <h2 class="font-titre text-lg">Identité</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="title" class="text-xs font-semibold text-ardoise">Nom du bien</label>
                    <input id="title" name="title" value="{{ old('title', $property->title) }}" required
                           class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('title') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="type" class="text-xs font-semibold text-ardoise">Type de bien</label>
                    <select id="type" name="type" class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                        @foreach ($types as $value => $label)
                            <option value="{{ $value }}" @selected(old('type', $property->type?->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="text-xs font-semibold text-ardoise">État</label>
                    <select id="status" name="status" class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $property->status?->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="rounded-meexeo border border-lin-clair bg-papier p-6">
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
                        <label for="{{ $field }}" class="text-xs font-semibold text-ardoise">{{ $label }}</label>
                        <input id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $property->{$field}) }}"
                               class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                        @error($field) <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                    </div>
                @endforeach
            </div>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="latitude" class="text-xs font-semibold text-ardoise">Latitude GPS</label>
                    <input id="latitude" name="latitude" value="{{ old('latitude', $property->latitude) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('latitude') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="longitude" class="text-xs font-semibold text-ardoise">Longitude GPS</label>
                    <input id="longitude" name="longitude" value="{{ old('longitude', $property->longitude) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('longitude') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="rounded-meexeo border border-lin-clair bg-papier p-6">
            <h2 class="font-titre text-lg">Caractéristiques et conditions</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="rooms" class="text-xs font-semibold text-ardoise">Nombre de pièces</label>
                    <input id="rooms" name="rooms" type="number" min="0" value="{{ old('rooms', $property->rooms) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('rooms') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="area_sqm" class="text-xs font-semibold text-ardoise">Superficie (m²)</label>
                    <input id="area_sqm" name="area_sqm" type="number" min="0" value="{{ old('area_sqm', $property->area_sqm) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('area_sqm') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="monthly_rent" class="text-xs font-semibold text-ardoise">Loyer mensuel (FCFA)</label>
                    <input id="monthly_rent" name="monthly_rent" type="number" step="1" min="0" required
                           value="{{ old('monthly_rent', $property->monthly_rent) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('monthly_rent') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="deposit" class="text-xs font-semibold text-ardoise">Caution (FCFA)</label>
                    <input id="deposit" name="deposit" type="number" step="1" min="0" required
                           value="{{ old('deposit', $property->deposit) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('deposit') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="notes" class="text-xs font-semibold text-ardoise">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="mt-1.5 w-full rounded-meexeo border border-lin px-3 py-2 text-sm">{{ old('notes', $property->notes) }}</textarea>
                    @error('notes') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="min-h-[44px] rounded-meexeo bg-cuivre px-5 text-sm font-semibold text-papier">
                {{ $editing ? 'Enregistrer les modifications' : 'Enregistrer le bien' }}
            </button>
            {{-- Chemin littéral et non route('properties.show', ...) : cette route n'existe
                 qu'à partir de la Task 10. Son URI sera exactement /biens/{id}, donc ce lien
                 restera correct sans modification une fois la route déclarée. --}}
            <a href="{{ $editing ? "/biens/{$property->id}" : route('properties.index') }}"
               class="inline-flex min-h-[44px] items-center rounded-meexeo border border-galet bg-papier px-4 text-sm">Annuler</a>
        </div>
    </form>
</x-layouts.app>
