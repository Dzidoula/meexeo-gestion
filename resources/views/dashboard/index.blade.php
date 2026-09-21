<x-layouts.app title="Tableau de bord — MASTERCLAYS">
    <x-page-header title="Tableau de bord" subtitle="Vue d'ensemble des activités MASTERCLAYS">
        <x-slot:actions>
            <form method="GET" class="flex gap-2">
                @foreach (['mois' => 'Ce mois', 'trimestre' => 'Ce trimestre', 'annee' => 'Cette année'] as $value => $label)
                    <button name="periode" value="{{ $value }}"
                            class="min-h-[44px] px-3 text-sm"
                            style="border-radius:var(--radius-mc-sm);{{ $period === $value ? 'border:1px solid var(--color-mc-accent);background:rgba(67,56,202,.08)' : 'border:1px solid var(--color-mc-border)' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </form>
        </x-slot:actions>
    </x-page-header>

    <x-dashboard-section title="Locatif" icon="building" icon-color="var(--color-mc-accent)" :href="route('properties.index')" link-label="Voir les biens">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-card label="Biens gérés" :value="$propertiesTotal" icon="building" icon-color="var(--color-mc-accent)" />
            <x-stat-card label="Taux d'occupation" :value="$occupancyRate.'%'" :hint="$occupiedCount.' occupé(s) sur '.$propertiesTotal" />
            <x-stat-card label="Revenu de la période" :value="\App\Support\Money::fcfa($revenue)" tone="cuivre" />
            <x-stat-card label="Impayés" :value="$unpaid->count()" tone="terre" />
        </div>

        <div class="mt-6 grid gap-5 lg:grid-cols-[1.62fr_1fr]">
            <div class="p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                <h3 class="font-titre text-lg">Loyers encaissés — 12 derniers mois</h3>
                <canvas id="revenue-chart" height="220" data-revenue-months="{{ json_encode($revenueByMonth) }}"></canvas>
                <a href="#" class="mt-3 inline-block text-xs" style="color:var(--color-mc-ink-faint)">Voir en tableau</a>
            </div>
            <div class="p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                <h3 class="font-titre text-lg">Revenu par commune</h3>
                <canvas id="commune-chart" height="220" data-commune-totals="{{ json_encode($revenueByCommune) }}"></canvas>
                <a href="#" class="mt-3 inline-block text-xs" style="color:var(--color-mc-ink-faint)">Voir en tableau</a>
            </div>
        </div>

        <div class="mt-6 grid gap-5 lg:grid-cols-[1.62fr_1fr]">
            <div class="p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                <h3 class="font-titre text-lg">Loyers impayés</h3>
                @if ($unpaid->isEmpty())
                    <p class="mt-4 text-sm" style="color:var(--color-mc-ink-faint)">Aucun impayé pour le moment.</p>
                @else
                    <ul class="mt-4 divide-y divide-[var(--color-mc-border-soft)]">
                        @foreach ($unpaid as $row)
                            <li class="flex items-center justify-between gap-3 py-3 text-sm">
                                <a href="{{ route('tenants.show', $row['lease']->tenant) }}" class="hover:[color:var(--color-mc-accent)]">
                                    {{ $row['lease']->tenant->full_name }} — {{ $row['lease']->property->title }}
                                </a>
                                <x-status-badge :status="$row['status']" />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                <div class="flex items-center justify-between">
                    <h3 class="font-titre text-lg">Échéances du jour</h3>
                    <button type="button" disabled title="Bientôt disponible"
                            class="min-h-[44px] px-3 text-xs opacity-60"
                            style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);color:var(--color-mc-ink-faint)">
                        Lancer les relances
                    </button>
                </div>
                @if ($dueSoon->isEmpty())
                    <p class="mt-4 text-sm" style="color:var(--color-mc-ink-faint)">Aucune échéance dans les prochains jours.</p>
                @else
                    <ul class="mt-4 divide-y divide-[var(--color-mc-border-soft)]">
                        @foreach ($dueSoon as $row)
                            <li class="py-3 text-sm">
                                <p>{{ $row['lease']->tenant->full_name }}</p>
                                <p class="text-xs" style="color:var(--color-mc-ink-faint)">{{ $row['label'] }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </x-dashboard-section>

    <x-dashboard-section title="Véhicules" icon="car" icon-color="var(--color-acier)" :href="route('vehicles.index')" link-label="Voir le catalogue">
        <div class="grid gap-4 sm:grid-cols-3">
            <x-stat-card label="Total véhicules" :value="$vehiclesTotal" icon="car" icon-color="var(--color-acier)" />
            <x-stat-card label="Valeur du stock" :value="\App\Support\Money::fcfa($vehiclesStockValue)" />
            <x-stat-card label="Véhicules épuisés" :value="$vehiclesOutOfStock" :tone="$vehiclesOutOfStock > 0 ? 'terre' : 'lagune'" />
        </div>

        @if ($vehiclesOutOfStock > 0)
            <p class="mt-3 text-sm" style="color:var(--color-mc-danger)">
                {{ $vehiclesOutOfStock }} véhicule(s) épuisé(s) —
                <a href="{{ route('vehicles.index', ['status' => 'epuise']) }}" style="text-decoration:underline">voir la liste</a>
            </p>
        @endif

        <div class="mt-6 p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
            <h3 class="font-titre text-lg">Répartition par type</h3>
            <canvas id="vehicle-type-chart" height="180" data-vehicle-types="{{ json_encode($vehiclesByType) }}"></canvas>
        </div>
    </x-dashboard-section>
</x-layouts.app>
