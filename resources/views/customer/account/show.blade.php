<x-layouts.public title="Mon compte — SONOR LOCATION">
    <div class="mx-auto max-w-2xl px-6 py-12">
        <h1 style="font-size:24px;font-weight:800">Mon compte</h1>

        <dl class="mt-6 grid grid-cols-1 gap-4 border-t pt-5 text-sm sm:grid-cols-2" style="border-color:var(--color-sonor-border)">
            <div><dt style="color:var(--color-sonor-ink-soft)">Nom</dt><dd class="mt-0.5 font-semibold">{{ $customer->name }}</dd></div>
            <div><dt style="color:var(--color-sonor-ink-soft)">Téléphone</dt><dd class="mt-0.5 font-semibold">{{ $customer->phone }}</dd></div>
            <div><dt style="color:var(--color-sonor-ink-soft)">Email</dt><dd class="mt-0.5 font-semibold">{{ $customer->email }}</dd></div>
        </dl>

        <form method="POST" action="{{ route('customer.logout') }}" class="mt-8">
            @csrf
            <button type="submit" class="min-h-[44px] text-sm font-semibold" style="border-radius:var(--radius-sonor-sm);border:1px solid var(--color-sonor-border);background:transparent;padding:0 18px">
                Se déconnecter
            </button>
        </form>

        <div class="mt-10 border-t pt-6" style="border-color:var(--color-sonor-border)">
            <h2 style="font-size:18px;font-weight:800">Mon panier</h2>

            @if ($vehicles->isEmpty())
                <p class="mt-3 text-sm" style="color:var(--color-sonor-ink-soft)">Votre panier est vide.</p>
            @else
                <div class="mt-4 space-y-3">
                    @foreach ($vehicles as $vehicle)
                        <div class="flex items-center justify-between gap-3 p-3 text-sm" style="border-radius:var(--radius-sonor-sm);border:1px solid var(--color-sonor-border)">
                            <span class="flex items-center gap-2">
                                <span style="font-weight:700">{{ $vehicle->brand }} {{ $vehicle->model }}</span>
                                <x-status-badge :status="\App\Support\VehicleStockStatus::for($vehicle->stock_quantity)" />
                            </span>
                            <span class="chiffre" style="color:var(--color-sonor-ink-soft)">{{ \App\Support\Money::fcfa($vehicle->price) }}</span>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('cart.show') }}" class="mt-4 inline-block text-sm font-semibold" style="color:var(--color-sonor-ink)">Voir mon panier →</a>
            @endif
        </div>
    </div>
</x-layouts.public>
