<x-layouts.app title="Tableau de bord — MEEXEO">
    <x-page-header title="Tableau de bord" subtitle="Vue d'ensemble de l'activité MEEXEO">
        <x-slot:actions>
            <form method="GET" class="flex gap-2">
                @foreach (['mois' => 'Ce mois', 'trimestre' => 'Ce trimestre', 'annee' => 'Cette année'] as $value => $label)
                    <button name="periode" value="{{ $value }}"
                            class="min-h-[44px] rounded-meexeo border px-3 text-sm {{ $period === $value ? 'border-cuivre bg-cuivre/10' : 'border-galet' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </form>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Biens gérés" :value="$propertiesTotal" />
        <x-stat-card label="Taux d'occupation" :value="$occupancyRate.'%'" :hint="$occupiedCount.' occupé(s) sur '.$propertiesTotal" />
        <x-stat-card label="Revenu de la période" :value="\App\Support\Money::fcfa($revenue)" tone="cuivre" />
        <x-stat-card label="Impayés" :value="$unpaid->count()" tone="terre" />
    </div>

    <div class="mt-6 grid gap-5 lg:grid-cols-[1.62fr_1fr]">
        <div class="rounded-meexeo border border-lin-clair bg-papier p-6">
            <h2 class="font-titre text-lg">Loyers encaissés — 12 derniers mois</h2>
            <p class="mt-8 text-sm text-brume">Graphique ajouté à l'étape suivante.</p>
        </div>
        <div class="rounded-meexeo border border-lin-clair bg-papier p-6">
            <h2 class="font-titre text-lg">Revenu par commune</h2>
            <p class="mt-8 text-sm text-brume">Graphique ajouté à l'étape suivante.</p>
        </div>
    </div>

    <div class="mt-6 grid gap-5 lg:grid-cols-[1.62fr_1fr]">
        <div class="rounded-meexeo border border-lin-clair bg-papier p-6">
            <h2 class="font-titre text-lg">Loyers impayés</h2>
            @if ($unpaid->isEmpty())
                <p class="mt-4 text-sm text-brume">Aucun impayé pour le moment.</p>
            @else
                <ul class="mt-4 divide-y divide-lin-pale">
                    @foreach ($unpaid as $row)
                        <li class="flex items-center justify-between gap-3 py-3 text-sm">
                            <a href="{{ route('tenants.show', $row['lease']->tenant) }}" class="hover:text-cuivre">
                                {{ $row['lease']->tenant->full_name }} — {{ $row['lease']->property->title }}
                            </a>
                            <x-status-badge :status="$row['status']" />
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="rounded-meexeo border border-lin-clair bg-papier p-6">
            <div class="flex items-center justify-between">
                <h2 class="font-titre text-lg">Échéances du jour</h2>
                <button type="button" disabled title="Bientôt disponible"
                        class="min-h-[44px] rounded-meexeo border border-galet bg-papier px-3 text-xs text-brume opacity-60">
                    Lancer les relances
                </button>
            </div>
            @if ($dueSoon->isEmpty())
                <p class="mt-4 text-sm text-brume">Aucune échéance dans les prochains jours.</p>
            @else
                <ul class="mt-4 divide-y divide-lin-pale">
                    @foreach ($dueSoon as $row)
                        <li class="py-3 text-sm">
                            <p>{{ $row['lease']->tenant->full_name }}</p>
                            <p class="text-xs text-brume">{{ $row['label'] }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-layouts.app>
