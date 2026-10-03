<x-layouts.tenant-portal title="Mon contrat">

    <h1 class="text-2xl font-semibold text-lagune mb-6">Mon contrat</h1>

    <div class="space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-ardoise mb-3">Bien loué</p>
            <p class="font-semibold text-lagune text-lg">{{ $lease->property->title }}</p>
            <p class="text-ardoise text-sm mt-0.5">{{ $lease->property->fullAddress }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs text-ardoise mb-1">Loyer mensuel</p>
                <p class="font-semibold text-lagune">{{ number_format($lease->monthly_rent, 0, ',', ' ') }} F</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs text-ardoise mb-1">Caution versée</p>
                <p class="font-semibold text-lagune">{{ number_format($lease->deposit_paid ?? 0, 0, ',', ' ') }} F</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs text-ardoise mb-1">Début du bail</p>
                <p class="font-semibold text-lagune">{{ $lease->start_date->format('d/m/Y') }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs text-ardoise mb-1">Échéance le</p>
                <p class="font-semibold text-lagune">{{ $lease->due_day }} de chaque mois</p>
            </div>
        </div>

        @if($lease->expected_end_date)
            <div class="bg-amber-50 rounded-2xl p-4">
                <p class="text-xs text-amber-700 mb-1">Date de fin prévue</p>
                <p class="font-medium text-amber-900">{{ $lease->expected_end_date->format('d/m/Y') }}</p>
            </div>
        @endif
    </div>

</x-layouts.tenant-portal>
