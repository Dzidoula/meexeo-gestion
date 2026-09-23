<x-layouts.app :title="($hotelRoomType->exists ? 'Modifier' : 'Ajouter').' un type de chambre — MASTERCLAYS'">
    <x-page-header :title="$hotelRoomType->exists ? 'Modifier le type de chambre' : 'Ajouter un type de chambre'" />

    <form method="POST" action="{{ $hotelRoomType->exists ? route('hotel-room-types.update', $hotelRoomType) : route('hotel-room-types.store') }}" class="mt-6 max-w-md">
        @csrf
        @if ($hotelRoomType->exists) @method('PUT') @endif

        <label for="name" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Nom</label>
        <input id="name" name="name" value="{{ old('name', $hotelRoomType->name) }}" required
               class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
        @error('name') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror

        <button class="mt-4 min-h-[44px] px-5 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
            Enregistrer
        </button>
    </form>
</x-layouts.app>
