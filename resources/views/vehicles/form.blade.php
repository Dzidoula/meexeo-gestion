<x-layouts.app :title="($vehicle->exists ? 'Modifier' : 'Ajouter').' un véhicule — MASTERCLAYS'">
    <x-page-header :title="$vehicle->exists ? 'Modifier le véhicule' : 'Ajouter un véhicule'" />

    @if ($vehicleTypes->isEmpty())
        <p class="mt-6 p-6 text-sm" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);color:var(--color-mc-ink-soft)">
            Aucun type de véhicule n'existe encore. <a href="{{ route('vehicle-types.create') }}" style="color:var(--color-mc-accent)">Créez-en un d'abord</a>.
        </p>
    @else
        <form method="POST" action="{{ $vehicle->exists ? route('vehicles.update', $vehicle) : route('vehicles.store') }}" class="mt-6 max-w-lg space-y-4">
            @csrf
            @if ($vehicle->exists) @method('PUT') @endif

            <div>
                <label for="vehicle_type_id" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Type</label>
                <select id="vehicle_type_id" name="vehicle_type_id" required class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @foreach ($vehicleTypes as $type)
                        <option value="{{ $type->id }}" @selected(old('vehicle_type_id', $vehicle->vehicle_type_id) == $type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
                @error('vehicle_type_id') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="brand" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Marque</label>
                    <input id="brand" name="brand" value="{{ old('brand', $vehicle->brand) }}" required
                           class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('brand') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="model" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Modèle</label>
                    <input id="model" name="model" value="{{ old('model', $vehicle->model) }}" required
                           class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('model') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="fuel_type" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Carburant</label>
                    <select id="fuel_type" name="fuel_type" required class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                        @foreach ($fuelTypes as $value => $label)
                            <option value="{{ $value }}" @selected(old('fuel_type', $vehicle->fuel_type?->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('fuel_type') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="transmission" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Transmission</label>
                    <select id="transmission" name="transmission" required class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                        @foreach ($transmissions as $value => $label)
                            <option value="{{ $value }}" @selected(old('transmission', $vehicle->transmission?->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('transmission') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label for="seats" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Places</label>
                    <input id="seats" name="seats" type="number" step="1" min="1" value="{{ old('seats', $vehicle->seats) }}" required
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('seats') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="price" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Prix (FCFA)</label>
                    <input id="price" name="price" type="number" step="1" min="0" value="{{ old('price', $vehicle->price) }}" required
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('price') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="stock_quantity" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Quantité en stock</label>
                    <input id="stock_quantity" name="stock_quantity" type="number" step="1" min="0" value="{{ old('stock_quantity', $vehicle->stock_quantity) }}" required
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('stock_quantity') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="description" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="mt-1.5 w-full px-3 py-2 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">{{ old('description', $vehicle->description) }}</textarea>
            </div>

            <button class="min-h-[44px] px-5 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
                Enregistrer
            </button>
        </form>
    @endif
</x-layouts.app>
