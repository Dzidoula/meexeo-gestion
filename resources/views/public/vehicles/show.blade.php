<x-layouts.public :title="$vehicle->brand.' '.$vehicle->model.' — SONOR LOCATION'">
    <div class="mx-auto max-w-6xl px-6 py-10">
        <a href="{{ route('public.vehicles.index') }}" class="text-sm" style="color:var(--color-sonor-ink-soft)">← Retour aux véhicules</a>

        <div class="mt-4 grid gap-8 lg:grid-cols-[1.3fr_1fr]">
            <div>
                @if ($primary = $vehicle->photos->firstWhere('is_primary', true))
                    <img src="{{ $primary->url }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="h-[360px] w-full object-cover" style="border-radius:var(--radius-sonor)">
                @else
                    <div class="flex h-[360px] items-center justify-center text-sm" style="border-radius:var(--radius-sonor);background:var(--color-sonor-navy-soft);color:#fff">
                        Aucune photo pour ce véhicule
                    </div>
                @endif

                @if ($vehicle->photos->count() > 1)
                    <div class="mt-3 grid grid-cols-4 gap-2">
                        @foreach ($vehicle->photos as $photo)
                            <img src="{{ $photo->url }}" alt="" class="h-20 w-full object-cover" style="border-radius:var(--radius-sonor-sm)">
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <div class="flex items-start justify-between gap-3">
                    <h1 style="font-size:24px;font-weight:800">{{ $vehicle->brand }} {{ $vehicle->model }}</h1>
                    <x-status-badge :status="\App\Support\VehicleStockStatus::for($vehicle->stock_quantity)" />
                </div>
                <p class="mt-1" style="color:var(--color-sonor-ink-soft)">{{ $vehicle->vehicleType->name }}</p>
                <p class="chiffre mt-4" style="font-size:28px;font-weight:800">{{ \App\Support\Money::fcfa($vehicle->price) }}</p>

                <dl class="mt-6 grid grid-cols-2 gap-4 border-t pt-5 text-sm" style="border-color:var(--color-sonor-border)">
                    <div><dt style="color:var(--color-sonor-ink-soft)">Carburant</dt><dd class="mt-0.5 font-semibold">{{ $vehicle->fuel_type->label() }}</dd></div>
                    <div><dt style="color:var(--color-sonor-ink-soft)">Transmission</dt><dd class="mt-0.5 font-semibold">{{ $vehicle->transmission->label() }}</dd></div>
                    <div><dt style="color:var(--color-sonor-ink-soft)">Places</dt><dd class="chiffre mt-0.5 font-semibold">{{ $vehicle->seats }}</dd></div>
                </dl>

                @if ($vehicle->description)
                    <p class="mt-5 border-t pt-5 text-sm" style="border-color:var(--color-sonor-border);color:var(--color-sonor-ink-soft)">{{ $vehicle->description }}</p>
                @endif

                @if ($vehicle->stock_quantity > 0)
                    <form method="POST" action="{{ route('cart.add', $vehicle) }}" class="mt-6">
                        @csrf
                        <button type="submit"
                                class="min-h-[44px] w-full text-sm font-semibold"
                                style="border-radius:var(--radius-sonor-sm);border:none;background:var(--color-sonor-yellow);color:var(--color-sonor-navy)">
                            Ajouter au panier
                        </button>
                    </form>
                @else
                    <button type="button" disabled
                            class="mt-6 min-h-[44px] w-full text-sm font-semibold"
                            style="border-radius:var(--radius-sonor-sm);border:none;background:var(--color-sonor-navy-soft);color:#fff;opacity:.7">
                        Épuisé
                    </button>
                @endif
            </div>
        </div>
    </div>
</x-layouts.public>
