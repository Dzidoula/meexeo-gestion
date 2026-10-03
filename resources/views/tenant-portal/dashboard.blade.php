<x-layouts.tenant-portal title="Accueil">

    {{-- Greeting --}}
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-lagune">
            Bonjour, {{ $tenant->first_names }} 👋
        </h1>
        <p class="text-ardoise text-sm mt-1">{{ now()->isoFormat('dddd D MMMM YYYY') }}</p>
    </div>

    {{-- Current property --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-4">
        @if($property->primaryPhoto)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($property->primaryPhoto->path) }}"
                 alt="{{ $property->title }}"
                 class="w-full h-40 object-cover">
        @else
            <div class="w-full h-32 bg-gradient-to-br from-lagune to-acier flex items-center justify-center">
                <span class="text-4xl">🏠</span>
            </div>
        @endif

        <div class="p-4">
            <h2 class="font-semibold text-lagune">{{ $property->title }}</h2>
            <p class="text-ardoise text-sm mt-0.5">{{ $property->fullAddress }}</p>
        </div>
    </div>

    {{-- Rent status --}}
    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-ardoise mb-1">Loyer du mois</p>
            <p class="text-xl font-semibold text-lagune">
                {{ number_format($lease->monthly_rent, 0, ',', ' ') }} F
            </p>
            <x-tenant-portal.rent-status-badge :status="$rentStatus" class="mt-2" />
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-ardoise mb-1">Prochaine échéance</p>
            <p class="text-xl font-semibold text-lagune">
                {{ $nextDue->format('d') }}
            </p>
            <p class="text-sm text-ardoise">{{ ucfirst($nextDue->isoFormat('MMMM YYYY')) }}</p>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('tenant-portal.payments.create') }}"
           class="bg-lagune text-white rounded-2xl p-4 flex items-center gap-3 hover:bg-opacity-90 transition-colors">
            <span class="text-2xl">💳</span>
            <span class="font-medium text-sm">Payer mon loyer</span>
        </a>

        <a href="{{ route('tenant-portal.lease') }}"
           class="bg-white border border-gray-100 rounded-2xl p-4 flex items-center gap-3 hover:bg-sable transition-colors shadow-sm">
            <span class="text-2xl">📄</span>
            <span class="font-medium text-sm text-lagune">Mon contrat</span>
        </a>

        <a href="{{ route('tenant-portal.payments') }}"
           class="bg-white border border-gray-100 rounded-2xl p-4 flex items-center gap-3 hover:bg-sable transition-colors shadow-sm">
            <span class="text-2xl">🧾</span>
            <span class="font-medium text-sm text-lagune">Mes paiements</span>
        </a>

        <a href="{{ route('tenant-portal.repairs.create') }}"
           class="bg-white border border-gray-100 rounded-2xl p-4 flex items-center gap-3 hover:bg-sable transition-colors shadow-sm">
            <span class="text-2xl">🔧</span>
            <span class="font-medium text-sm text-lagune">Signaler un problème</span>
        </a>
    </div>

</x-layouts.tenant-portal>
