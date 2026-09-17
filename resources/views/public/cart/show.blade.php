<x-layouts.public title="Mon panier — MASTERCLAYS">
    <div class="mx-auto max-w-4xl px-6 py-10">
        <h1 style="font-size:26px;font-weight:800">Mon panier</h1>

        @if (session('cart_status'))
            <p class="mt-3 text-sm" style="color:var(--color-mc-store-ink-soft)">{{ session('cart_status') }}</p>
        @endif
        @if (session('cart_error'))
            <p class="mt-3 text-sm" style="color:#B4622F">{{ session('cart_error') }}</p>
        @endif

        @if ($vehicles->isEmpty())
            <p class="mt-8 p-8 text-center text-sm" style="border-radius:var(--radius-mc-store);border:1px solid var(--color-mc-store-border);color:var(--color-mc-store-ink-soft)">
                Votre panier est vide.
            </p>
        @else
            <div class="mt-6 space-y-4">
                @foreach ($vehicles as $vehicle)
                    <div class="flex items-center justify-between gap-4 p-4" style="border-radius:var(--radius-mc-store);background:var(--color-mc-store-surface);box-shadow:var(--shadow-mc-store-card)">
                        <div class="flex items-center gap-4">
                            <x-vehicle-photo :vehicle="$vehicle" class="h-16 w-24" icon-class="h-6 w-6" radius="var(--radius-mc-store-sm)" />
                            <div>
                                <div class="flex items-center gap-2">
                                    <p style="font-size:15px;font-weight:700">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                                    <x-status-badge :status="\App\Support\VehicleStockStatus::for($vehicle->stock_quantity)" />
                                </div>
                                <p class="chiffre mt-1" style="font-size:14px;color:var(--color-mc-store-ink-soft)">{{ \App\Support\Money::fcfa($vehicle->price) }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('cart.remove', $vehicle) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="min-h-[44px] text-sm font-semibold" style="border-radius:var(--radius-mc-store-sm);border:1px solid var(--color-mc-store-border);background:transparent;padding:0 16px">
                                Retirer
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.public>
