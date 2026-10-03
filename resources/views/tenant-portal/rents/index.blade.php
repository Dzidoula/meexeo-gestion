<x-layouts.tenant-portal title="Mes loyers">

    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-lagune">Mes loyers</h1>
        <p class="text-ardoise text-sm mt-1">{{ number_format($lease->monthly_rent, 0, ',', ' ') }} F / mois</p>
    </div>

    <div class="space-y-3">
        @forelse($months as $month)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-lagune">{{ $month['label'] }}</p>
                        <p class="text-sm text-ardoise mt-0.5">
                            {{ number_format($month['amount'], 0, ',', ' ') }} F
                            @if($month['payment'])
                                · Payé le {{ \Carbon\Carbon::parse($month['payment']->paid_on)->format('d/m/Y') }}
                            @endif
                        </p>
                    </div>
                    <x-tenant-portal.rent-status-badge :status="$month['status']" />
                </div>

                @if($month['rest'] > 0 && $month['payment'])
                    <div class="mt-2 pt-2 border-t border-gray-50">
                        <p class="text-xs text-orange-600">
                            Reste à payer : {{ number_format($month['rest'], 0, ',', ' ') }} F
                        </p>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-12 text-ardoise">
                <p class="text-4xl mb-3">📋</p>
                <p>Aucun loyer à afficher.</p>
            </div>
        @endforelse
    </div>

</x-layouts.tenant-portal>
