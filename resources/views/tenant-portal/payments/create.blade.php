<x-layouts.tenant-portal title="Payer mon loyer">

    <div class="mb-6">
        <a href="{{ route('tenant-portal.payments') }}" class="text-ardoise text-sm flex items-center gap-1 mb-3">
            ← Retour
        </a>
        <h1 class="text-2xl font-semibold text-lagune">Payer mon loyer</h1>
    </div>

    @if(empty($unpaid))
        <div class="bg-green-50 rounded-2xl p-6 text-center">
            <p class="text-2xl mb-2">✅</p>
            <p class="font-medium text-green-800">Tous vos loyers sont à jour !</p>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
            <p class="text-sm font-medium text-lagune mb-3">Mois concerné</p>
            <select name="month" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-lagune">
                @foreach($unpaid as $m)
                    <option value="{{ $m['key'] }}">{{ $m['label'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
            <p class="text-sm font-medium text-lagune mb-1">Montant</p>
            <p class="text-3xl font-semibold text-lagune">
                {{ number_format($lease->monthly_rent, 0, ',', ' ') }} <span class="text-lg text-ardoise">FCFA</span>
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-sm font-medium text-lagune mb-3">Choisissez votre mode de paiement</p>

            <div class="space-y-2" x-data="{ method: null }">
                @foreach([
                    ['id' => 'wave',     'label' => 'Wave',              'icon' => '💙'],
                    ['id' => 'orange',   'label' => 'Orange Money',      'icon' => '🟠'],
                    ['id' => 'mtn',      'label' => 'MTN MoMo',          'icon' => '🟡'],
                    ['id' => 'moov',     'label' => 'Moov Money',        'icon' => '🔵'],
                    ['id' => 'virement', 'label' => 'Virement bancaire', 'icon' => '🏦'],
                ] as $m)
                    <button type="button"
                        @click="method = '{{ $m['id'] }}'"
                        :class="method === '{{ $m['id'] }}' ? 'border-lagune bg-lagune/5' : 'border-gray-200'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border transition-colors text-left">
                        <span class="text-xl">{{ $m['icon'] }}</span>
                        <span class="font-medium text-lagune">{{ $m['label'] }}</span>
                    </button>
                @endforeach

                <div x-show="method" class="mt-4 bg-amber-50 rounded-xl p-4">
                    <p class="text-sm text-amber-800">
                        🚧 Le paiement en ligne sera disponible prochainement.
                        En attendant, <a href="{{ route('tenant-portal.proofs.create') }}" class="underline font-medium">envoyez votre preuve de paiement</a>.
                    </p>
                </div>
            </div>
        </div>
    @endif

</x-layouts.tenant-portal>
