@php
    use App\Support\Money;
    use App\Support\RentSchedule;

    $labels   = RentSchedule::labels();
    $horsChar = max(0, (int) $lease->monthly_rent - (int) $lease->charges);

    $stats = [
        [
            'label' => 'Loyer mensuel',
            'value' => Money::fcfa((int) $lease->monthly_rent),
            'sub'   => $lease->charges > 0
                ? Money::fcfa($horsChar).' + '.Money::fcfa((int) $lease->charges).' charges'
                : 'Charges comprises',
            'icon'  => 'credit-card',
            'color' => 'from-pl-500 to-pl-700',
            'href'  => route('tenant-portal.payments'),
        ],
        [
            'label' => 'Prochain loyer',
            'value' => $nextDue ? $nextDue['due_on']->isoFormat('D MMM YYYY') : 'À jour',
            'sub'   => $nextDue ? ($labels[$nextDue['status']] ?? '') : 'Aucun paiement en attente',
            'icon'  => 'calendar',
            'color' => ($nextDue && $nextDue['status'] === 'overdue') ? 'from-red-500 to-red-700' : 'from-gold-500 to-gold-700',
            'href'  => route('tenant-portal.payments'),
        ],
        [
            'label' => 'Demandes en cours',
            'value' => (string) $openRepairs,
            'sub'   => $openRepairs > 0 ? 'Interventions à suivre' : 'Aucune demande ouverte',
            'icon'  => 'wrench',
            'color' => 'from-amber-500 to-amber-700',
            'href'  => route('tenant-portal.repairs'),
        ],
        [
            'label' => 'Paiements réglés',
            'value' => (string) $paidCount,
            'sub'   => 'Historique complet',
            'icon'  => 'trending-up',
            'color' => 'from-emerald-500 to-emerald-700',
            'href'  => route('tenant-portal.payments'),
        ],
    ];

    $repairStatus = [
        'recu'     => ['Reçu',    'bg-blue-50 text-blue-700'],
        'en_cours' => ['En cours','bg-amber-50 text-amber-700'],
        'repare'   => ['Réparé',  'bg-emerald-50 text-emerald-700'],
        'cloture'  => ['Clôturé', 'bg-gray-100 text-gray-600'],
    ];
@endphp

<x-layouts.tenant-portal title="Tableau de bord">
<div class="space-y-6 animate-fade-in max-w-7xl mx-auto">

    {{-- ===== Bannière d'accueil ===== --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-pl-800 via-pl-700 to-pl-600 p-6 sm:p-8 text-white">
        <div class="absolute top-0 right-0 w-64 h-64 rounded-full bg-gold-400/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/2 w-48 h-48 rounded-full bg-white/5 blur-2xl"></div>
        <div class="relative z-10">
            <p class="text-white/60 text-sm">{{ now()->isoFormat('dddd D MMMM YYYY') }}</p>
            <h1 class="text-2xl sm:text-3xl font-bold mt-2">Bonjour {{ $tenant->first_names }}</h1>
            <p class="text-white/70 mt-2 max-w-lg">
                Bienvenue sur votre espace locataire. Voici un aperçu de votre situation.
            </p>
            @if($property)
                <div class="mt-4 inline-flex items-center gap-2 rounded-lg bg-white/10 border border-white/20 px-4 py-2 backdrop-blur-sm">
                    <x-tenant-portal.icon name="home" class="w-4 h-4 text-gold-300" />
                    <span class="text-sm font-medium">{{ $property->title }}</span>
                    <span class="text-white/40">•</span>
                    <span class="text-sm text-white/70">{{ $property->commune ?: $property->city }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- ===== Cartes de synthèse ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($stats as $stat)
            <a href="{{ $stat['href'] }}"
               class="group text-left bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-lg hover:shadow-gray-200/50 hover:-translate-y-0.5 transition-all duration-200">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br {{ $stat['color'] }} flex items-center justify-center shrink-0">
                        <x-tenant-portal.icon :name="$stat['icon']" class="w-5 h-5 text-white" />
                    </div>
                    <x-tenant-portal.icon name="arrow-right"
                        class="w-4 h-4 text-gray-300 group-hover:text-gray-500 group-hover:translate-x-0.5 transition" />
                </div>
                <p class="text-2xl font-bold text-gray-900 mt-4 chiffre">{{ $stat['value'] }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $stat['label'] }}</p>
                @if($stat['sub'])
                    <p class="text-xs text-gray-400 mt-0.5">{{ $stat['sub'] }}</p>
                @endif
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===== Derniers paiements ===== --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Derniers paiements</h3>
                <a href="{{ route('tenant-portal.payments') }}"
                   class="text-sm text-pl-600 hover:text-pl-700 font-medium flex items-center gap-1 transition">
                    Tout voir <x-tenant-portal.icon name="arrow-right" class="w-3.5 h-3.5" />
                </a>
            </div>

            @forelse($schedule->take(5) as $row)
                <div class="flex items-center justify-between py-3 px-3 rounded-lg hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        <div @class([
                            'w-9 h-9 rounded-lg flex items-center justify-center shrink-0',
                            'bg-emerald-50 text-emerald-600' => $row['status'] === 'paid',
                            'bg-red-50 text-red-600'         => $row['status'] === 'overdue',
                            'bg-amber-50 text-amber-600'     => ! in_array($row['status'], ['paid', 'overdue'], true),
                        ])>
                            <x-tenant-portal.icon :name="$row['status'] === 'paid' ? 'check-circle-2' : 'clock'" class="w-4 h-4" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 chiffre">{{ Money::fcfa($row['amount']) }}</p>
                            <p class="text-xs text-gray-400">
                                Échéance : {{ $row['due_on']->format('d/m/Y') }}
                                @if($row['paid_on'])
                                    · Payé le {{ \Carbon\Carbon::parse($row['paid_on'])->format('d/m/Y') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ RentSchedule::tone($row['status']) }}">
                        {{ $labels[$row['status']] }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-gray-400 py-8 text-center">Aucun paiement enregistré</p>
            @endforelse
        </div>

        {{-- ===== Activité récente ===== --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Activité récente</h3>
            @forelse($repairs->take(4) as $r)
                <div class="flex gap-3 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <x-tenant-portal.icon name="wrench" class="w-4 h-4" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $r->ticket_no }} — {{ ucfirst($r->type) }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $r->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 py-8 text-center">Aucune activité</p>
            @endforelse
        </div>
    </div>

    {{-- ===== Demandes d'entretien ===== --}}
    @if($repairs->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Demandes d'entretien</h3>
                <a href="{{ route('tenant-portal.repairs') }}"
                   class="text-sm text-pl-600 hover:text-pl-700 font-medium flex items-center gap-1 transition">
                    Tout voir <x-tenant-portal.icon name="arrow-right" class="w-3.5 h-3.5" />
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($repairs->take(4) as $r)
                    @php [$lbl, $tone] = $repairStatus[$r->status] ?? ['—', 'bg-gray-100 text-gray-600'] @endphp
                    <a href="{{ route('tenant-portal.repairs.show', $r) }}"
                       class="rounded-xl border border-gray-100 p-4 hover:border-gray-200 transition block">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-medium text-gray-900 flex-1 line-clamp-2">{{ $r->description }}</p>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full whitespace-nowrap {{ $tone }}">{{ $lbl }}</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">{{ $r->created_at->diffForHumans() }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
</x-layouts.tenant-portal>
