@php
    use App\Support\Money;
    use App\Support\RentSchedule;

    $labels = RentSchedule::labels();

    $cards = [
        ['Total réglé',   Money::fcfa($totalPaid),                  'trending-up', 'bg-emerald-50 text-emerald-600'],
        ['En attente',    $pendingCount.' paiement'.($pendingCount > 1 ? 's' : ''), 'clock', 'bg-amber-50 text-amber-600'],
        ['En retard',     (string) $overdueCount,                   'alert-circle', 'bg-red-50 text-red-600'],
        ['Loyer mensuel', Money::fcfa((int) $lease->monthly_rent),  'coins', 'bg-pl-50 text-pl-600'],
    ];

    $filters = ['' => 'Tous', 'pending' => 'En attente', 'paid' => 'Payés', 'overdue' => 'En retard'];

    $rowIcon = fn ($s) => match ($s) {
        'paid'    => ['check-circle-2', 'bg-emerald-50', 'text-emerald-700'],
        'overdue' => ['alert-circle',   'bg-red-50',     'text-red-600'],
        default   => ['clock',          'bg-amber-50',   'text-amber-600'],
    };
@endphp

<x-layouts.tenant-portal title="Loyers & paiements">
<div class="space-y-6 animate-fade-in max-w-5xl mx-auto">

    {{-- ===== Synthèse ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($cards as [$label, $value, $icon, $tone])
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="w-10 h-10 rounded-xl {{ $tone }} flex items-center justify-center">
                    <x-tenant-portal.icon :name="$icon" class="w-5 h-5" />
                </div>
                <p class="text-xl font-bold text-gray-900 mt-3 chiffre">{{ $value }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    {{-- ===== Envoyer une preuve — absent de la maquette, central ici :
           les loyers se règlent par mobile money et se justifient par capture. ===== --}}
    <a href="{{ route('tenant-portal.proofs.create') }}"
       class="group flex items-center gap-4 rounded-2xl bg-gradient-to-r from-pl-600 to-pl-700 p-5 text-white hover:shadow-lg hover:shadow-pl-500/25 transition">
        <div class="w-11 h-11 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center shrink-0">
            <x-tenant-portal.icon name="receipt" class="w-5 h-5" />
        </div>
        <div class="min-w-0 flex-1">
            <p class="font-semibold">Envoyer une preuve de paiement</p>
            <p class="text-sm text-white/70 mt-0.5">
                Capture Wave, reçu Orange Money, bordereau bancaire — votre gestionnaire la vérifie.
            </p>
        </div>
        <x-tenant-portal.icon name="arrow-right" class="w-5 h-5 text-white/70 group-hover:translate-x-0.5 transition shrink-0" />
    </a>

    {{-- ===== Historique ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <h3 class="font-semibold text-gray-900">Historique des paiements</h3>
                <div class="flex gap-1 bg-gray-100 rounded-lg p-1">
                    @foreach($filters as $value => $label)
                        <a href="{{ route('tenant-portal.payments', array_filter(['filter' => $value])) }}"
                           class="px-3 py-1.5 rounded-md text-xs font-medium transition
                                  {{ $filter === $value ? 'bg-white text-pl-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        @forelse($rows as $row)
            @if($loop->first)<div class="divide-y divide-gray-50">@endif
            @php [$icon, $bg, $text] = $rowIcon($row['status']) @endphp

            <div class="flex items-center justify-between p-4 sm:px-6 hover:bg-gray-50/50 transition">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-10 h-10 rounded-xl {{ $bg }} {{ $text }} flex items-center justify-center shrink-0">
                        <x-tenant-portal.icon :name="$icon" class="w-5 h-5" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 chiffre">{{ Money::fcfa($row['amount']) }}</p>
                        <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                            <x-tenant-portal.icon name="calendar" class="w-3 h-3 text-gray-400" />
                            <p class="text-xs text-gray-400">Échéance : {{ $row['due_on']->isoFormat('D MMMM YYYY') }}</p>
                            @if($row['paid_on'])
                                <span class="text-gray-300">•</span>
                                <p class="text-xs text-emerald-600">
                                    Payé le {{ \Carbon\Carbon::parse($row['paid_on'])->isoFormat('D MMMM YYYY') }}
                                </p>
                            @endif
                        </div>
                        @if($row['method'])
                            <p class="text-xs text-gray-400 mt-0.5">Mode : {{ $row['method']->label() }}</p>
                        @endif
                        @if($row['status'] === 'partial')
                            <p class="text-xs text-amber-600 mt-0.5 chiffre">
                                Reste à payer : {{ Money::fcfa($row['rest']) }}
                            </p>
                        @endif
                        @if($row['status'] === 'rejected' && $row['rejected']?->notes)
                            <p class="text-xs text-red-600 mt-1 leading-snug">
                                {{ \Illuminate\Support\Str::after($row['rejected']->notes, '— ') }}
                            </p>
                            <a href="{{ route('tenant-portal.proofs.create') }}"
                               class="text-xs text-pl-600 hover:text-pl-700 font-medium mt-1 inline-block">
                                Envoyer une nouvelle preuve
                            </a>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ RentSchedule::tone($row['status']) }}">
                        {{ $labels[$row['status']] }}
                    </span>
                </div>
            </div>

            @if($loop->last)</div>@endif
        @empty
            <div class="py-16 text-center">
                <x-tenant-portal.icon name="credit-card" class="w-10 h-10 text-gray-300 mx-auto mb-3" />
                <p class="text-sm text-gray-400">Aucun paiement dans cette catégorie</p>
            </div>
        @endforelse
    </div>

    {{-- ===== Informations de paiement ===== --}}
    <div class="rounded-2xl bg-gradient-to-r from-pl-50 to-gold-50 border border-pl-100 p-6">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shrink-0 shadow-sm">
                <x-tenant-portal.icon name="credit-card" class="w-5 h-5 text-pl-600" />
            </div>
            <div>
                <h4 class="font-semibold text-gray-900">Informations de paiement</h4>
                <p class="text-sm text-gray-600 mt-1">
                    Le loyer est dû le <span class="font-semibold text-pl-700">{{ $lease->due_day }} de chaque mois</span>.
                    Vous pouvez régler par Wave, Orange Money, MTN MoMo, Moov Money ou virement bancaire,
                    puis envoyer la preuve ci-dessus. Les quittances sont disponibles dans
                    <a href="{{ route('tenant-portal.documents') }}" class="font-medium text-pl-700 underline">Documents</a>
                    une fois le paiement validé.
                </p>
            </div>
        </div>
    </div>

</div>
</x-layouts.tenant-portal>
