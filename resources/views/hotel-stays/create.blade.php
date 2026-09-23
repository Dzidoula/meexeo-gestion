<x-layouts.app title="Nouveau séjour — MASTERCLAYS">
    <x-page-header title="Nouveau séjour" />

    @if ($rooms->isEmpty())
        <p class="mt-6 p-6 text-sm" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);color:var(--color-mc-ink-soft)">
            Aucune chambre disponible actuellement. <a href="{{ route('hotel-rooms.index') }}" style="color:var(--color-mc-accent)">Voir les chambres</a>.
        </p>
    @else
        <form method="POST" action="{{ route('hotel-stays.store') }}" class="mt-6 max-w-lg space-y-4">
            @csrf

            <div>
                <label for="hotel_room_id" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Chambre disponible</label>
                <select id="hotel_room_id" name="hotel_room_id" required class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" @selected(old('hotel_room_id') == $room->id)>{{ $room->number }} — {{ $room->hotelRoomType->name }}</option>
                    @endforeach
                </select>
                @error('hotel_room_id') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="guest_name" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Nom du client</label>
                    <input id="guest_name" name="guest_name" value="{{ old('guest_name') }}" required
                           class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('guest_name') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="guest_phone" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Téléphone</label>
                    <input id="guest_phone" name="guest_phone" value="{{ old('guest_phone') }}" required
                           class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('guest_phone') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="arrival_date" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Arrivée prévue</label>
                    <input id="arrival_date" name="arrival_date" type="date" value="{{ old('arrival_date') }}" required
                           class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('arrival_date') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="departure_date" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Départ prévu</label>
                    <input id="departure_date" name="departure_date" type="date" value="{{ old('departure_date') }}" required
                           class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('departure_date') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="total_amount" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Montant total (FCFA)</label>
                    <input id="total_amount" name="total_amount" type="number" step="1" min="0" value="{{ old('total_amount') }}" required
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('total_amount') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="deposit_amount" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Acompte (FCFA)</label>
                    <input id="deposit_amount" name="deposit_amount" type="number" step="1" min="0" value="{{ old('deposit_amount', 0) }}" required
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('deposit_amount') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="notes" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Observations</label>
                <textarea id="notes" name="notes" rows="3"
                          class="mt-1.5 w-full px-3 py-2 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">{{ old('notes') }}</textarea>
            </div>

            <button class="min-h-[44px] px-5 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
                Créer le séjour
            </button>
        </form>
    @endif
</x-layouts.app>
