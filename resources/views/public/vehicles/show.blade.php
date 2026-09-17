<x-layouts.public :title="$vehicle->brand.' '.$vehicle->model.' — MASTERCLAYS'">
    <div class="mx-auto max-w-6xl px-6 py-10">
        <a href="{{ route('public.vehicles.index') }}" class="text-sm" style="color:var(--color-mc-store-ink-soft)">← Retour aux véhicules</a>

        <div class="mt-4 grid gap-8 lg:grid-cols-[1.3fr_1fr]">
            <div>
                @if ($primary = $vehicle->photos->firstWhere('is_primary', true))
                    <img src="{{ $primary->url }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="h-[360px] w-full object-cover" style="border-radius:var(--radius-mc-store);box-shadow:var(--shadow-mc-store-card)">
                @else
                    <div class="flex h-[360px] flex-col items-center justify-center gap-2 text-sm" style="border-radius:var(--radius-mc-store);background:linear-gradient(155deg, var(--color-mc-store-navy), var(--color-mc-store-navy-soft));color:#fff;box-shadow:var(--shadow-mc-store-card)">
                        <x-mc-store-icon name="vehicle" class="h-12 w-12 opacity-70" />
                        Aucune photo pour ce véhicule
                    </div>
                @endif

                @if ($vehicle->photos->count() > 1)
                    <div class="mt-3 grid grid-cols-4 gap-2">
                        @foreach ($vehicle->photos as $photo)
                            <img src="{{ $photo->url }}" alt="" class="h-20 w-full object-cover" style="border-radius:var(--radius-mc-store-sm);box-shadow:var(--shadow-mc-store-card)">
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="p-6" style="border-radius:var(--radius-mc-store);background:var(--color-mc-store-surface);box-shadow:var(--shadow-mc-store-card)">
                <div class="flex items-start justify-between gap-3">
                    <h1 style="font-size:24px;font-weight:800">{{ $vehicle->brand }} {{ $vehicle->model }}</h1>
                    <x-status-badge :status="\App\Support\VehicleStockStatus::for($vehicle->stock_quantity)" />
                </div>
                <p class="mt-1" style="color:var(--color-mc-store-ink-soft)">{{ $vehicle->vehicleType->name }}</p>
                <p class="chiffre mt-4" style="font-size:28px;font-weight:800">{{ \App\Support\Money::fcfa($vehicle->price) }}</p>

                <dl class="mt-6 grid grid-cols-2 gap-4 border-t pt-5 text-sm" style="border-color:var(--color-mc-store-border)">
                    <div><dt style="color:var(--color-mc-store-ink-soft)">Carburant</dt><dd class="mt-0.5 font-semibold">{{ $vehicle->fuel_type->label() }}</dd></div>
                    <div><dt style="color:var(--color-mc-store-ink-soft)">Transmission</dt><dd class="mt-0.5 font-semibold">{{ $vehicle->transmission->label() }}</dd></div>
                    <div><dt style="color:var(--color-mc-store-ink-soft)">Places</dt><dd class="chiffre mt-0.5 font-semibold">{{ $vehicle->seats }}</dd></div>
                </dl>

                @if ($vehicle->description)
                    <p class="mt-5 border-t pt-5 text-sm" style="border-color:var(--color-mc-store-border);color:var(--color-mc-store-ink-soft)">{{ $vehicle->description }}</p>
                @endif

                @if ($vehicle->stock_quantity > 0)
                    <form method="POST" action="{{ route('cart.add', $vehicle) }}" class="mt-6">
                        @csrf
                        <button type="submit"
                                class="min-h-[44px] w-full text-sm font-semibold"
                                style="border-radius:var(--radius-mc-store-sm);border:none;background:var(--color-mc-store-yellow);color:var(--color-mc-store-navy)">
                            Ajouter au panier
                        </button>
                    </form>
                @else
                    <button type="button" disabled
                            class="mt-6 min-h-[44px] w-full text-sm font-semibold"
                            style="border-radius:var(--radius-mc-store-sm);border:none;background:var(--color-mc-store-navy-soft);color:#fff;opacity:.7">
                        Épuisé
                    </button>
                @endif
            </div>
        </div>
    </div>
</x-layouts.public>
