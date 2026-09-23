<x-layouts.app :title="($room->exists ? 'Modifier' : 'Ajouter').' une chambre — MASTERCLAYS'">
    <x-page-header :title="$room->exists ? 'Modifier la chambre' : 'Ajouter une chambre'" />

    @if ($hotelRoomTypes->isEmpty())
        <p class="mt-6 p-6 text-sm" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);color:var(--color-mc-ink-soft)">
            Aucun type de chambre n'existe encore. <a href="{{ route('hotel-room-types.create') }}" style="color:var(--color-mc-accent)">Créez-en un d'abord</a>.
        </p>
    @else
        <form method="POST" action="{{ $room->exists ? route('hotel-rooms.update', $room) : route('hotel-rooms.store') }}" class="mt-6 max-w-lg space-y-4">
            @csrf
            @if ($room->exists) @method('PUT') @endif

            <div>
                <label for="hotel_room_type_id" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Type</label>
                <select id="hotel_room_type_id" name="hotel_room_type_id" required class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @foreach ($hotelRoomTypes as $type)
                        <option value="{{ $type->id }}" @selected(old('hotel_room_type_id', $room->hotel_room_type_id) == $type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
                @error('hotel_room_type_id') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="number" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Numéro</label>
                    <input id="number" name="number" value="{{ old('number', $room->number) }}" required
                           class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('number') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="nightly_rate" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Tarif/nuit (FCFA)</label>
                    <input id="nightly_rate" name="nightly_rate" type="number" step="1" min="0" value="{{ old('nightly_rate', $room->nightly_rate) }}" required
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('nightly_rate') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="status" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Statut</label>
                <select id="status" name="status" required class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @foreach (\App\Enums\HotelRoomStatus::options() as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $room->status?->value) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="amenities" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Équipements</label>
                <textarea id="amenities" name="amenities" rows="3"
                          class="mt-1.5 w-full px-3 py-2 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">{{ old('amenities', $room->amenities) }}</textarea>
            </div>

            <button class="min-h-[44px] px-5 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
                Enregistrer
            </button>
        </form>
    @endif
</x-layouts.app>
