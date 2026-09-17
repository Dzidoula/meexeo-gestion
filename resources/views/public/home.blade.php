{{-- resources/views/public/home.blade.php --}}
<x-layouts.public title="SONOR LOCATION — Vos déplacements et vos événements, notre priorité">
    {{-- Bandeau principal --}}
    <div class="relative" style="color:#fff;padding:96px 24px;background:linear-gradient(155deg, rgba(16,24,40,.93), rgba(16,24,40,.72)), url('{{ asset('images/sonor/hero-highway.jpg') }}') center/cover no-repeat">
        <div class="relative mx-auto max-w-6xl">
            <p style="font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--color-sonor-yellow)">Sonor Location</p>
            <h1 class="mt-3 max-w-xl" style="font-size:40px;font-weight:800;line-height:1.15">
                Vos déplacements et vos événements, notre priorité !
            </h1>
            <p class="mt-4 max-w-lg" style="color:#E4E7EC;font-size:15.5px">
                Vente de véhicules, taxis, location de sonorisation et gestion de podiums. Un seul partenaire pour tous vos besoins de mobilité et d'événements.
            </p>
            <a href="{{ route('public.vehicles.index') }}" class="sonor-card mt-8 inline-flex min-h-[44px] items-center px-6" style="border-radius:var(--radius-sonor-sm);background:var(--color-sonor-yellow);color:var(--color-sonor-navy);font-weight:700;font-size:14px">
                Découvrir nos véhicules
            </a>
        </div>
    </div>

    {{-- 4 blocs d'activité --}}
    <div class="mx-auto max-w-6xl px-6 py-12">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('public.vehicles.index') }}" class="sonor-card block p-6" style="border-radius:var(--radius-sonor);background:var(--color-sonor-surface)">
                <div class="flex h-14 w-14 items-center justify-center" style="border-radius:var(--radius-sonor-sm);background:linear-gradient(155deg, var(--color-sonor-navy), var(--color-sonor-navy-soft));color:var(--color-sonor-yellow)">
                    <x-sonor-icon name="vehicle" class="h-7 w-7" />
                </div>
                <p class="mt-4" style="font-size:16px;font-weight:800">Vente de véhicules</p>
                <p class="mt-1 text-sm" style="color:var(--color-sonor-ink-soft)">Voitures neuves et d'occasion, toutes marques.</p>
                <p class="mt-3 text-sm font-semibold" style="color:var(--color-sonor-yellow)">Voir les véhicules →</p>
            </a>
            <a href="{{ route('public.taxis') }}" class="sonor-card block p-6" style="border-radius:var(--radius-sonor);background:var(--color-sonor-surface)">
                <div class="flex h-14 w-14 items-center justify-center" style="border-radius:var(--radius-sonor-sm);background:linear-gradient(155deg, var(--color-sonor-navy), var(--color-sonor-navy-soft));color:var(--color-sonor-yellow)">
                    <x-sonor-icon name="taxi" class="h-7 w-7" />
                </div>
                <p class="mt-4" style="font-size:16px;font-weight:800">Taxis</p>
                <p class="mt-1 text-sm" style="color:var(--color-sonor-ink-soft)">Trajets urbains et interurbains, disponibles 24h/7.</p>
                <p class="mt-3 text-sm font-semibold" style="color:var(--color-sonor-ink-soft)">Bientôt disponible →</p>
            </a>
            <a href="{{ route('public.sonorisation') }}" class="sonor-card block p-6" style="border-radius:var(--radius-sonor);background:var(--color-sonor-surface)">
                <div class="flex h-14 w-14 items-center justify-center" style="border-radius:var(--radius-sonor-sm);background:linear-gradient(155deg, var(--color-sonor-navy), var(--color-sonor-navy-soft));color:var(--color-sonor-yellow)">
                    <x-sonor-icon name="speaker" class="h-7 w-7" />
                </div>
                <p class="mt-4" style="font-size:16px;font-weight:800">Location de sonorisation</p>
                <p class="mt-1 text-sm" style="color:var(--color-sonor-ink-soft)">Équipements professionnels pour tous vos événements.</p>
                <p class="mt-3 text-sm font-semibold" style="color:var(--color-sonor-ink-soft)">Bientôt disponible →</p>
            </a>
            <a href="{{ route('public.podiums') }}" class="sonor-card block p-6" style="border-radius:var(--radius-sonor);background:var(--color-sonor-surface)">
                <div class="flex h-14 w-14 items-center justify-center" style="border-radius:var(--radius-sonor-sm);background:linear-gradient(155deg, var(--color-sonor-navy), var(--color-sonor-navy-soft));color:var(--color-sonor-yellow)">
                    <x-sonor-icon name="stage" class="h-7 w-7" />
                </div>
                <p class="mt-4" style="font-size:16px;font-weight:800">Gestion de podiums</p>
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
                    <a href="{{ route('public.vehicles.show', $vehicle) }}" class="sonor-card block" style="border-radius:var(--radius-sonor);background:var(--color-sonor-surface);overflow:hidden">
                        <x-vehicle-photo :vehicle="$vehicle" class="h-32 w-full" />
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
