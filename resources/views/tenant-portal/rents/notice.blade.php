<x-layouts.tenant-portal title="Avis d'échéance">

    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-lagune">Avis d'échéance</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <p class="text-ardoise text-sm mb-2">Prochaine échéance</p>
        <p class="text-3xl font-semibold text-lagune">
            {{ $nextDue->isoFormat('D MMMM YYYY') }}
        </p>
        <p class="text-ardoise text-sm mt-2">
            Montant : {{ number_format($lease->monthly_rent, 0, ',', ' ') }} F
        </p>
    </div>

</x-layouts.tenant-portal>
