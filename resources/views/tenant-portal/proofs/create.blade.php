<x-layouts.tenant-portal title="Envoyer une preuve">

    <div class="mb-6">
        <a href="{{ route('tenant-portal.payments') }}" class="text-ardoise text-sm flex items-center gap-1 mb-3">← Retour</a>
        <h1 class="text-2xl font-semibold text-lagune">Envoyer une preuve</h1>
        <p class="text-ardoise text-sm mt-1">Envoyez votre reçu de paiement pour validation</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4 text-green-800 text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('tenant-portal.proofs.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <label class="block text-sm font-medium text-lagune mb-2">Mois concerné</label>
                <select name="month" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-lagune @error('month') border-red-400 @enderror">
                    @php
                        $start = \Carbon\Carbon::parse($lease->start_date);
                        $cur   = now();
                    @endphp
                    @while($start->lte($cur))
                        <option value="{{ $start->format('Y-m') }}">
                            {{ ucfirst($start->isoFormat('MMMM YYYY')) }}
                        </option>
                        @php $start->addMonth() @endphp
                    @endwhile
                </select>
                @error('month')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <label class="block text-sm font-medium text-lagune mb-2">Montant payé (FCFA)</label>
                <input type="number" name="amount" value="{{ old('amount', $lease->monthly_rent) }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 text-lagune text-lg @error('amount') border-red-400 @enderror">
                @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <label class="block text-sm font-medium text-lagune mb-2">Preuve de paiement</label>
                <p class="text-xs text-ardoise mb-3">Capture Wave, reçu Orange Money, bordereau bancaire (JPEG, PNG, PDF · max 5 Mo)</p>
                <input type="file" name="proof" accept="image/*,.pdf"
                       class="w-full text-sm text-ardoise file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-lagune file:text-white file:cursor-pointer @error('proof') border border-red-400 rounded-xl p-2 @enderror">
                @error('proof')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit"
                class="w-full bg-lagune text-white py-4 rounded-2xl font-medium text-base hover:bg-opacity-90 transition-colors">
                Envoyer la preuve
            </button>
        </div>
    </form>

</x-layouts.tenant-portal>
