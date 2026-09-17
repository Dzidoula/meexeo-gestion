<x-layouts.public title="Nos véhicules — SONOR LOCATION">
    <div class="mx-auto max-w-6xl px-6 py-10">
        <h1 style="font-size:26px;font-weight:800">Nos véhicules</h1>
        <p class="mt-1" style="color:var(--color-sonor-ink-soft)">Découvrez notre sélection de véhicules disponibles à la vente.</p>

        <form method="GET" class="mt-6 flex flex-wrap items-end gap-3 p-5" style="border-radius:var(--radius-sonor);background:var(--color-sonor-surface);box-shadow:var(--shadow-sonor-card)">
            <div class="min-w-[220px] flex-1">
                <label for="q" style="font-size:12px;font-weight:700;color:var(--color-sonor-ink-soft)">Recherche</label>
                <input id="q" name="q" value="{{ request('q') }}" placeholder="Marque, modèle…"
                       class="mt-1.5 min-h-[44px] w-full" style="border-radius:var(--radius-sonor-sm);border:1px solid var(--color-sonor-border);padding:0 12px;font-size:13px">
            </div>
            <div>
                <label for="type" style="font-size:12px;font-weight:700;color:var(--color-sonor-ink-soft)">Type</label>
                <select id="type" name="type" class="mt-1.5 min-h-[44px]" style="border-radius:var(--radius-sonor-sm);border:1px solid var(--color-sonor-border);padding:0 12px;font-size:13px">
                    <option value="">Tous</option>
                    @foreach ($vehicleTypes as $vehicleType)
                        <option value="{{ $vehicleType->id }}" @selected(request('type') == $vehicleType->id)>{{ $vehicleType->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="fuel_type" style="font-size:12px;font-weight:700;color:var(--color-sonor-ink-soft)">Carburant</label>
                <select id="fuel_type" name="fuel_type" class="mt-1.5 min-h-[44px]" style="border-radius:var(--radius-sonor-sm);border:1px solid var(--color-sonor-border);padding:0 12px;font-size:13px">
                    <option value="">Tous</option>
                    @foreach ($fuelTypes as $value => $label)
                        <option value="{{ $value }}" @selected(request('fuel_type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="transmission" style="font-size:12px;font-weight:700;color:var(--color-sonor-ink-soft)">Transmission</label>
                <select id="transmission" name="transmission" class="mt-1.5 min-h-[44px]" style="border-radius:var(--radius-sonor-sm);border:1px solid var(--color-sonor-border);padding:0 12px;font-size:13px">
                    <option value="">Toutes</option>
                    @foreach ($transmissions as $value => $label)
                        <option value="{{ $value }}" @selected(request('transmission') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="price_min" style="font-size:12px;font-weight:700;color:var(--color-sonor-ink-soft)">Prix min.</label>
                <input id="price_min" name="price_min" type="number" min="0" value="{{ request('price_min') }}" placeholder="0"
                       class="mt-1.5 min-h-[44px] w-28" style="border-radius:var(--radius-sonor-sm);border:1px solid var(--color-sonor-border);padding:0 12px;font-size:13px">
            </div>
            <div>
                <label for="price_max" style="font-size:12px;font-weight:700;color:var(--color-sonor-ink-soft)">Prix max.</label>
                <input id="price_max" name="price_max" type="number" min="0" value="{{ request('price_max') }}" placeholder="Aucun"
                       class="mt-1.5 min-h-[44px] w-28" style="border-radius:var(--radius-sonor-sm);border:1px solid var(--color-sonor-border);padding:0 12px;font-size:13px">
            </div>
            <button class="min-h-[44px]" style="border-radius:var(--radius-sonor-sm);border:none;background:var(--color-sonor-yellow);color:var(--color-sonor-navy);padding:0 18px;font-size:13px;font-weight:700">Filtrer</button>
            @if (request()->hasAny(['q', 'type', 'fuel_type', 'transmission', 'price_min', 'price_max']))
                <a href="{{ route('public.vehicles.index') }}" class="inline-flex min-h-[44px] items-center px-2 text-sm" style="color:var(--color-sonor-ink-soft)">Réinitialiser</a>
            @endif
        </form>

        @if ($vehicles->isEmpty())
            <p class="mt-8 p-8 text-center text-sm" style="border-radius:var(--radius-sonor);border:1px solid var(--color-sonor-border);color:var(--color-sonor-ink-soft)">
                Aucun véhicule ne correspond à cette recherche.
            </p>
        @else
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($vehicles as $vehicle)
                    <a href="{{ route('public.vehicles.show', $vehicle) }}" class="sonor-card block" style="border-radius:var(--radius-sonor);background:var(--color-sonor-surface);overflow:hidden">
                        <x-vehicle-photo :vehicle="$vehicle" class="h-40 w-full" />
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-2">
                                <p style="font-size:15px;font-weight:700;color:var(--color-sonor-ink)">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                                <x-status-badge :status="\App\Support\VehicleStockStatus::for($vehicle->stock_quantity)" />
                            </div>
                            <p class="mt-1" style="font-size:12px;color:var(--color-sonor-ink-soft)">{{ $vehicle->vehicleType->name }}</p>
                            <p class="chiffre mt-2 font-semibold" style="font-size:16px;color:var(--color-sonor-ink)">{{ \App\Support\Money::fcfa($vehicle->price) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">{{ $vehicles->links() }}</div>
        @endif
    </div>
</x-layouts.public>
