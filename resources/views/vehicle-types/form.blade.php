<x-layouts.app :title="($vehicleType->exists ? 'Modifier' : 'Ajouter').' un type de véhicule — SONOR LOCATION'">
    <x-page-header :title="$vehicleType->exists ? 'Modifier le type de véhicule' : 'Ajouter un type de véhicule'" />

    <form method="POST" action="{{ $vehicleType->exists ? route('vehicle-types.update', $vehicleType) : route('vehicle-types.store') }}" class="mt-6 max-w-md">
        @csrf
        @if ($vehicleType->exists) @method('PUT') @endif

        <label for="name" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Nom</label>
        <input id="name" name="name" value="{{ old('name', $vehicleType->name) }}" required
               class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
        @error('name') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror

        <button class="mt-4 min-h-[44px] px-5 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
            Enregistrer
        </button>
    </form>
</x-layouts.app>
