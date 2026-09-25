<x-layouts.app :title="($equipment->exists ? 'Modifier' : 'Ajouter').' un équipement — MASTERCLAYS'">
    <x-page-header :title="$equipment->exists ? 'Modifier l\'équipement' : 'Ajouter un équipement'" />

    <form method="POST" action="{{ $equipment->exists ? route('equipment.update', $equipment) : route('equipment.store') }}" class="mt-6 max-w-md space-y-4">
        @csrf
        @if ($equipment->exists) @method('PUT') @endif

        <div>
            <label for="name" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Nom</label>
            <input id="name" name="name" value="{{ old('name', $equipment->name) }}" required
                   class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
            @error('name') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="quantity_total" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Quantité totale</label>
            <input id="quantity_total" name="quantity_total" type="number" step="1" min="0" value="{{ old('quantity_total', $equipment->quantity_total) }}" required
                   class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
            @error('quantity_total') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
        </div>

        <button class="mt-4 min-h-[44px] px-5 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
            Enregistrer
        </button>
    </form>
</x-layouts.app>
