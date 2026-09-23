@php($canWrite = in_array(auth()->user()->role, [\App\Enums\Role::Admin, \App\Enums\Role::Manager], true))
<x-layouts.app :title="'Chambre '.$room->number.' — MASTERCLAYS'">
    <x-page-header :title="'Chambre '.$room->number" :subtitle="$room->hotelRoomType->name">
        <x-slot:actions>
            @if ($canWrite)
                <a href="{{ route('hotel-rooms.edit', $room) }}"
                   class="inline-flex min-h-[44px] items-center px-4 text-sm font-semibold"
                   style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);color:var(--color-mc-ink)">
                    Modifier
                </a>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid gap-5 lg:grid-cols-2">
        <div class="p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div><dt style="color:var(--color-mc-ink-faint)">Statut</dt><dd class="mt-1"><x-status-badge :status="$room->status->value" /></dd></div>
                <div><dt style="color:var(--color-mc-ink-faint)">Tarif/nuit</dt><dd class="chiffre mt-1 font-semibold">{{ \App\Support\Money::fcfa($room->nightly_rate) }}</dd></div>
            </dl>
            @if ($room->amenities)
                <p class="mt-5 border-t pt-5 text-sm" style="border-color:var(--color-mc-border-soft);color:var(--color-mc-ink-soft)">{{ $room->amenities }}</p>
            @endif
        </div>
    </div>
</x-layouts.app>
