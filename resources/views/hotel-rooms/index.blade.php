@php($canWrite = in_array(auth()->user()->role, [\App\Enums\Role::Admin, \App\Enums\Role::Manager], true))
<x-layouts.app title="Chambres — MASTERCLAYS">
    <x-page-header title="Chambres" subtitle="{{ $rooms->total() }} chambre(s)">
        <x-slot:actions>
            @if ($canWrite)
                <a href="{{ route('hotel-rooms.create') }}"
                   class="inline-flex min-h-[44px] items-center px-4 text-sm font-semibold"
                   style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
                    Ajouter une chambre
                </a>
            @endif
            <a href="{{ route('hotel-stays.index') }}"
               class="inline-flex min-h-[44px] items-center px-2 text-sm" style="color:var(--color-mc-ink-faint)">Séjours</a>
            <a href="{{ route('hotel-room-types.index') }}"
               class="inline-flex min-h-[44px] items-center px-2 text-sm" style="color:var(--color-mc-ink-faint)">Types de chambre</a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="mt-6 flex flex-wrap items-end gap-3">
        <div>
            <label for="type" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">Type</label>
            <select id="type" name="type" class="mt-1.5 min-h-[44px]" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);padding:0 12px;font-size:13px">
                <option value="">Tous</option>
                @foreach ($hotelRoomTypes as $type)
                    <option value="{{ $type->id }}" @selected(request('type') == $type->id)>{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="status" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">Statut</label>
            <select id="status" name="status" class="mt-1.5 min-h-[44px]" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);padding:0 12px;font-size:13px">
                <option value="">Tous</option>
                @foreach (\App\Enums\HotelRoomStatus::options() as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button class="min-h-[44px]" style="border-radius:var(--radius-mc-sm);border:none;background:var(--color-mc-accent);color:var(--color-mc-on-accent);padding:0 18px;font-size:13px;font-weight:700">Filtrer</button>
        @if (request()->hasAny(['type', 'status']))
            <a href="{{ route('hotel-rooms.index') }}" class="inline-flex min-h-[44px] items-center px-2 text-sm" style="color:var(--color-mc-ink-faint)">Réinitialiser</a>
        @endif
    </form>

    @if ($rooms->isEmpty())
        <p class="mt-8 p-8 text-center text-sm" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);color:var(--color-mc-ink-faint)">
            Aucune chambre ne correspond à cette recherche.
        </p>
    @else
        <div class="mt-6 overflow-x-auto" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid var(--color-mc-border);background:var(--color-mc-table-head)">
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">CHAMBRE</th>
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">TYPE</th>
                        <th class="px-4 py-3 text-right" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">TARIF/NUIT</th>
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">STATUT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rooms as $room)
                        <tr style="border-bottom:1px solid var(--color-mc-border-soft)">
                            <td class="px-4 py-3">
                                <a href="{{ route('hotel-rooms.show', $room) }}" style="font-size:14px;font-weight:700;color:var(--color-mc-ink)">{{ $room->number }}</a>
                            </td>
                            <td class="px-4 py-3" style="font-size:12px;color:var(--color-mc-ink-soft)">{{ $room->hotelRoomType->name }}</td>
                            <td class="chiffre px-4 py-3 text-right font-semibold">{{ \App\Support\Money::fcfa($room->nightly_rate) }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$room->status->value" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $rooms->links() }}</div>
    @endif
</x-layouts.app>
