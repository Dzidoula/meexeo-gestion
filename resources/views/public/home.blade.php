{{-- resources/views/public/home.blade.php --}}
<x-layouts.public title="MASTERCLAYS — Trouvez votre prochain véhicule, en toute confiance">
    {{-- Bandeau principal --}}
    <div class="relative" style="color:#fff;padding:96px 24px;background:linear-gradient(155deg, rgba(16,24,40,.93), rgba(16,24,40,.72)), url('{{ asset('images/mc-store/hero-highway.jpg') }}') center/cover no-repeat">
        <div class="relative mx-auto max-w-6xl">
            <p style="font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--color-mc-store-yellow)">MASTERCLAYS</p>
            <h1 class="mt-3 max-w-xl" style="font-size:40px;font-weight:800;line-height:1.15">
                Trouvez votre prochain véhicule, en toute confiance
            </h1>
            <p class="mt-4 max-w-lg" style="color:#E4E7EC;font-size:15.5px">
                Véhicules neufs et d'occasion sélectionnés, ajoutez au panier en quelques clics. Taxis, sonorisation et podiums arrivent bientôt sur ce site.
            </p>
            <a href="{{ route('public.vehicles.index') }}" class="mc-store-card mt-8 inline-flex min-h-[44px] items-center px-6" style="border-radius:var(--radius-mc-store-sm);background:var(--color-mc-store-yellow);color:var(--color-mc-store-navy);font-weight:700;font-size:14px">
                Découvrir nos véhicules
            </a>
        </div>
    </div>

    {{-- 4 blocs d'activité --}}
    <div class="mx-auto max-w-6xl px-6 py-12">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('public.vehicles.index') }}" class="mc-store-card block p-6" style="border-radius:var(--radius-mc-store);background:var(--color-mc-store-surface)">
                <div class="flex h-14 w-14 items-center justify-center" style="border-radius:var(--radius-mc-store-sm);background:linear-gradient(155deg, var(--color-mc-store-navy), var(--color-mc-store-navy-soft));color:var(--color-mc-store-yellow)">
                    <x-mc-store-icon name="vehicle" class="h-7 w-7" />
                </div>
                <p class="mt-4" style="font-size:16px;font-weight:800">Vente de véhicules</p>
                <p class="mt-1 text-sm" style="color:var(--color-mc-store-ink-soft)">Voitures neuves et d'occasion, toutes marques.</p>
                <p class="mt-3 text-sm font-semibold" style="color:var(--color-mc-store-yellow)">Voir les véhicules →</p>
            </a>
            <a href="{{ route('public.taxis') }}" class="mc-store-card block p-6" style="border-radius:var(--radius-mc-store);background:var(--color-mc-store-surface)">
                <div class="flex h-14 w-14 items-center justify-center" style="border-radius:var(--radius-mc-store-sm);background:linear-gradient(155deg, var(--color-mc-store-navy), var(--color-mc-store-navy-soft));color:var(--color-mc-store-yellow)">
                    <x-mc-store-icon name="taxi" class="h-7 w-7" />
                </div>
                <p class="mt-4" style="font-size:16px;font-weight:800">Taxis</p>
                <p class="mt-1 text-sm" style="color:var(--color-mc-store-ink-soft)">Trajets urbains et interurbains, disponibles 24h/7.</p>
                <p class="mt-3 text-sm font-semibold" style="color:var(--color-mc-store-ink-soft)">Bientôt disponible →</p>
            </a>
            <a href="{{ route('public.sonorisation') }}" class="mc-store-card block p-6" style="border-radius:var(--radius-mc-store);background:var(--color-mc-store-surface)">
                <div class="flex h-14 w-14 items-center justify-center" style="border-radius:var(--radius-mc-store-sm);background:linear-gradient(155deg, var(--color-mc-store-navy), var(--color-mc-store-navy-soft));color:var(--color-mc-store-yellow)">
                    <x-mc-store-icon name="speaker" class="h-7 w-7" />
                </div>
                <p class="mt-4" style="font-size:16px;font-weight:800">Location de sonorisation</p>
                <p class="mt-1 text-sm" style="color:var(--color-mc-store-ink-soft)">Équipements professionnels pour tous vos événements.</p>
                <p class="mt-3 text-sm font-semibold" style="color:var(--color-mc-store-ink-soft)">Bientôt disponible →</p>
            </a>
            <a href="{{ route('public.podiums') }}" class="mc-store-card block p-6" style="border-radius:var(--radius-mc-store);background:var(--color-mc-store-surface)">
                <div class="flex h-14 w-14 items-center justify-center" style="border-radius:var(--radius-mc-store-sm);background:linear-gradient(155deg, var(--color-mc-store-navy), var(--color-mc-store-navy-soft));color:var(--color-mc-store-yellow)">
                    <x-mc-store-icon name="stage" class="h-7 w-7" />
                </div>
                <p class="mt-4" style="font-size:16px;font-weight:800">Gestion de podiums</p>
                <p class="mt-1 text-sm" style="color:var(--color-mc-store-ink-soft)">Scènes et podiums pour tous types d'événements.</p>
                <p class="mt-3 text-sm font-semibold" style="color:var(--color-mc-store-ink-soft)">Bientôt disponible →</p>
            </a>
        </div>
    </div>

    {{-- Véhicules en vedette --}}
    <div class="mx-auto max-w-6xl px-6 pb-12">
        <div class="flex items-center justify-between">
            <h2 style="font-size:20px;font-weight:800">Nos véhicules en vedette</h2>
            <a href="{{ route('public.vehicles.index') }}" class="text-sm font-semibold" style="color:var(--color-mc-store-yellow)">Voir tous les véhicules →</a>
        </div>

        @if ($featuredVehicles->isEmpty())
            <p class="mt-6 text-sm" style="color:var(--color-mc-store-ink-soft)">Aucun véhicule au catalogue pour le moment.</p>
        @else
            <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($featuredVehicles as $vehicle)
                    <a href="{{ route('public.vehicles.show', $vehicle) }}" class="mc-store-card block" style="border-radius:var(--radius-mc-store);background:var(--color-mc-store-surface);overflow:hidden">
                        <x-vehicle-photo :vehicle="$vehicle" class="h-32 w-full" />
                        <div class="p-3">
                            <p style="font-size:13.5px;font-weight:700">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                            <p class="chiffre mt-1" style="font-size:14px;font-weight:700;color:var(--color-mc-store-ink)">{{ \App\Support\Money::fcfa($vehicle->price) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Bandeau statistiques --}}
    <div style="background:var(--color-mc-store-navy-soft);color:#fff;padding:32px 24px">
        <div class="mx-auto max-w-6xl text-center">
            <p class="chiffre" style="font-size:32px;font-weight:800;color:var(--color-mc-store-yellow)">{{ $vehiclesTotal }}</p>
            <p class="mt-1 text-sm" style="color:#C3C9D3">Nos véhicules</p>
        </div>
    </div>
</x-layouts.public>
