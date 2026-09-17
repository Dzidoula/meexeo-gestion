{{-- resources/views/public/home.blade.php --}}
<x-layouts.public title="SONOR LOCATION — Vos déplacements et vos événements, notre priorité">
    {{-- Bandeau principal --}}
    <div style="background:var(--color-sonor-navy);color:#fff;padding:64px 24px">
        <div class="mx-auto max-w-6xl">
            <p style="font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--color-sonor-yellow)">Sonor Location</p>
            <h1 class="mt-3 max-w-xl" style="font-size:36px;font-weight:800;line-height:1.15">
                Vos déplacements et vos événements, notre priorité !
            </h1>
            <p class="mt-4 max-w-lg" style="color:#C3C9D3">
                Vente de véhicules, taxis, location de sonorisation et gestion de podiums. Un seul partenaire pour tous vos besoins de mobilité et d'événements.
            </p>
            <a href="{{ route('public.vehicles.index') }}" class="mt-8 inline-flex min-h-[44px] items-center px-6" style="border-radius:var(--radius-sonor-sm);background:var(--color-sonor-yellow);color:var(--color-sonor-navy);font-weight:700;font-size:14px">
                Découvrir nos véhicules
            </a>
        </div>
    </div>

    {{-- 4 blocs d'activité --}}
    <div class="mx-auto max-w-6xl px-6 py-12">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('public.vehicles.index') }}" class="block p-6" style="border-radius:var(--radius-sonor);border:1px solid var(--color-sonor-border)">
                <p style="font-size:16px;font-weight:800">Vente de véhicules</p>
                <p class="mt-1 text-sm" style="color:var(--color-sonor-ink-soft)">Voitures neuves et d'occasion, toutes marques.</p>
                <p class="mt-3 text-sm font-semibold" style="color:var(--color-sonor-yellow)">Voir les véhicules →</p>
            </a>
            <a href="{{ route('public.taxis') }}" class="block p-6" style="border-radius:var(--radius-sonor);border:1px solid var(--color-sonor-border)">
                <p style="font-size:16px;font-weight:800">Taxis</p>
                <p class="mt-1 text-sm" style="color:var(--color-sonor-ink-soft)">Trajets urbains et interurbains, disponibles 24h/7.</p>
                <p class="mt-3 text-sm font-semibold" style="color:var(--color-sonor-ink-soft)">Bientôt disponible →</p>
            </a>
            <a href="{{ route('public.sonorisation') }}" class="block p-6" style="border-radius:var(--radius-sonor);border:1px solid var(--color-sonor-border)">
                <p style="font-size:16px;font-weight:800">Location de sonorisation</p>
                <p class="mt-1 text-sm" style="color:var(--color-sonor-ink-soft)">Équipements professionnels pour tous vos événements.</p>
                <p class="mt-3 text-sm font-semibold" style="color:var(--color-sonor-ink-soft)">Bientôt disponible →</p>
            </a>
            <a href="{{ route('public.podiums') }}" class="block p-6" style="border-radius:var(--radius-sonor);border:1px solid var(--color-sonor-border)">
                <p style="font-size:16px;font-weight:800">Gestion de podiums</p>
                <p class="mt-1 text-sm" style="color:var(--color-sonor-ink-soft)">Scènes et podiums pour tous types d'événements.</p>
                <p class="mt-3 text-sm font-semibold" style="color:var(--color-sonor-ink-soft)">Bientôt disponible →</p>
            </a>
        </div>
    </div>

    {{-- Véhicules en vedette --}}
    <div class="mx-auto max-w-6xl px-6 pb-12">
        <div class="flex items-center justify-between">
            <h2 style="font-size:20px;font-weight:800">Nos véhicules en vedette</h2>
            <a href="{{ route('public.vehicles.index') }}" class="text-sm font-semibold" style="color:var(--color-sonor-yellow)">Voir tous les véhicules →</a>
        </div>

        @if ($featuredVehicles->isEmpty())
            <p class="mt-6 text-sm" style="color:var(--color-sonor-ink-soft)">Aucun véhicule au catalogue pour le moment.</p>
        @else
            <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($featuredVehicles as $vehicle)
                    <a href="{{ route('public.vehicles.show', $vehicle) }}" class="block" style="border-radius:var(--radius-sonor);border:1px solid var(--color-sonor-border);overflow:hidden">
                        @if ($primary = $vehicle->photos->firstWhere('is_primary', true))
                            <img src="{{ $primary->url }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="h-32 w-full object-cover">
                        @else
                            <div class="flex h-32 items-center justify-center text-xs" style="background:var(--color-sonor-navy-soft);color:#fff">Aucune photo</div>
                        @endif
                        <div class="p-3">
                            <p style="font-size:13.5px;font-weight:700">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                            <p class="chiffre mt-1" style="font-size:14px;font-weight:700;color:var(--color-sonor-ink)">{{ \App\Support\Money::fcfa($vehicle->price) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Bandeau statistiques --}}
    <div style="background:var(--color-sonor-navy-soft);color:#fff;padding:32px 24px">
        <div class="mx-auto max-w-6xl text-center">
            <p class="chiffre" style="font-size:32px;font-weight:800;color:var(--color-sonor-yellow)">{{ $vehiclesTotal }}</p>
            <p class="mt-1 text-sm" style="color:#C3C9D3">Nos véhicules</p>
        </div>
    </div>
</x-layouts.public>
