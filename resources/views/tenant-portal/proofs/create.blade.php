@php use App\Support\Money; @endphp

<x-layouts.tenant-portal title="Envoyer une preuve">
<div class="space-y-6 animate-fade-in max-w-2xl mx-auto">

    <a href="{{ route('tenant-portal.payments') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition">
        <x-tenant-portal.icon name="arrow-right" class="w-4 h-4 rotate-180" />
        Loyers & paiements
    </a>

    <div class="rounded-2xl bg-gradient-to-r from-pl-600 to-pl-700 p-6 text-white">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center shrink-0">
                <x-tenant-portal.icon name="receipt" class="w-5 h-5" />
            </div>
            <div>
                <h2 class="font-semibold text-lg">Envoyer une preuve de paiement</h2>
                <p class="text-sm text-white/75 mt-1">
                    Votre gestionnaire la vérifie, puis le loyer passe en « payé ».
                    Tant qu'elle n'est pas validée, le mois reste marqué « en vérification ».
                </p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800 flex items-center gap-2">
            <x-tenant-portal.icon name="check" class="w-4 h-4" />
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="rounded-lg bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-800 flex items-center gap-2">
            <x-tenant-portal.icon name="alert-circle" class="w-4 h-4" />
            {{ session('info') }}
        </div>
    @endif

    <form method="POST" action="{{ route('tenant-portal.proofs.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8 space-y-5">
        @csrf

        <div>
            <label for="month" class="block text-sm font-medium text-gray-700 mb-1.5">Mois concerné</label>
            <select id="month" name="month" required
                    class="w-full px-3 py-2.5 rounded-lg border bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-pl-500 focus:border-transparent transition @error('month') border-red-300 @else border-gray-200 @enderror">
                @foreach($months as $m)
                    <option value="{{ $m['key'] }}" @selected(old('month') === $m['key'])>{{ $m['label'] }}</option>
                @endforeach
            </select>
            @error('month')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1.5">Montant payé (FCFA)</label>
            <div class="relative">
                <x-tenant-portal.icon name="coins" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input id="amount" type="number" name="amount" required min="1"
                       value="{{ old('amount', $lease->monthly_rent) }}"
                       class="w-full pl-10 pr-3 py-2.5 rounded-lg border bg-gray-50 text-sm chiffre focus:outline-none focus:ring-2 focus:ring-pl-500 focus:border-transparent transition @error('amount') border-red-300 @else border-gray-200 @enderror">
            </div>
            <p class="text-xs text-gray-400 mt-1">
                Loyer mensuel : {{ Money::fcfa((int) $lease->monthly_rent) }}.
                Indiquez le montant réellement versé, même s'il est partiel.
            </p>
            @error('amount')<p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="proof" class="block text-sm font-medium text-gray-700 mb-1.5">Preuve de paiement</label>
            <p class="text-xs text-gray-400 mb-2">
                Capture Wave, reçu Orange Money, bordereau bancaire. JPEG, PNG, WebP ou PDF, 5 Mo maximum.
            </p>
            <input id="proof" type="file" name="proof" required accept="image/*,.pdf"
                   class="w-full text-sm text-gray-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-pl-600 file:text-white file:text-sm file:font-medium file:cursor-pointer">
            @error('proof')
                <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
                    <x-tenant-portal.icon name="alert-circle" class="w-3.5 h-3.5" />
                    {{ $message }}
                </p>
            @enderror
        </div>

        <button type="submit"
                class="w-full bg-pl-600 hover:bg-pl-700 text-white text-sm font-semibold px-4 py-3 rounded-lg transition-all hover:shadow-lg hover:shadow-pl-500/25 flex items-center justify-center gap-2">
            <x-tenant-portal.icon name="send" class="w-4 h-4" />
            Envoyer la preuve
        </button>
    </form>

</div>
</x-layouts.tenant-portal>
