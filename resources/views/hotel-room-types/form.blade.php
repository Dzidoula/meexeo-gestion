<x-layouts.app :title="($hotelRoomType->exists ? 'Modifier' : 'Ajouter').' un type de chambre — MASTERCLAYS'">
    <x-page-header :title="$hotelRoomType->exists ? 'Modifier le type de chambre' : 'Ajouter un type de chambre'" />

    <form method="POST" action="{{ $hotelRoomType->exists ? route('hotel-room-types.update', $hotelRoomType) : route('hotel-room-types.store') }}" class="mt-6 max-w-lg space-y-4">
        @csrf
        @if ($hotelRoomType->exists) @method('PUT') @endif

        <div>
            <label for="name" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Nom</label>
            <input id="name" name="name" value="{{ old('name', $hotelRoomType->name) }}" required
                   class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
            @error('name') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Description</label>
            <textarea id="description" name="description" rows="3"
                      class="mt-1.5 w-full px-3 py-2 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">{{ old('description', $hotelRoomType->description) }}</textarea>
            @error('description') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="base_price" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Prix de base (FCFA/nuit)</label>
                <input id="base_price" name="base_price" type="number" step="1" min="0" value="{{ old('base_price', $hotelRoomType->base_price) }}"
                       class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                @error('base_price') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="rating" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Note (sur 5, laisser vide si inconnue)</label>
                <input id="rating" name="rating" type="number" step="0.1" min="0" max="5" value="{{ old('rating', $hotelRoomType->rating) }}"
                       class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                @error('rating') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-4 gap-4">
            <div>
                <label for="capacity" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Capacité</label>
                <input id="capacity" name="capacity" type="number" step="1" min="1" value="{{ old('capacity', $hotelRoomType->capacity) }}"
                       class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                @error('capacity') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="bed_count" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Lits</label>
                <input id="bed_count" name="bed_count" type="number" step="1" min="0" value="{{ old('bed_count', $hotelRoomType->bed_count) }}"
                       class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                @error('bed_count') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="bath_count" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Salles de bain</label>
                <input id="bath_count" name="bath_count" type="number" step="1" min="0" value="{{ old('bath_count', $hotelRoomType->bath_count) }}"
                       class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                @error('bath_count') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="area" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Surface (m²)</label>
                <input id="area" name="area" type="number" step="1" min="0" value="{{ old('area', $hotelRoomType->area) }}"
                       class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                @error('area') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="amenities" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Équipements (un par ligne)</label>
            <textarea id="amenities" name="amenities" rows="5"
                      class="mt-1.5 w-full px-3 py-2 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">{{ old('amenities', $hotelRoomType->amenities ? implode("\n", $hotelRoomType->amenities) : null) }}</textarea>
            @error('amenities') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
        </div>

        @if ($hotelRoomType->exists && !empty($hotelRoomType->images))
            <div>
                <span class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Photos</span>
                <p class="mt-1 text-xs" style="color:var(--color-mc-ink-faint)">{{ count($hotelRoomType->images) }} photo(s) importée(s) — la gestion des photos depuis ce formulaire n'est pas encore disponible.</p>
            </div>
        @endif

        <button class="min-h-[44px] px-5 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
            Enregistrer
        </button>
    </form>
</x-layouts.app>
