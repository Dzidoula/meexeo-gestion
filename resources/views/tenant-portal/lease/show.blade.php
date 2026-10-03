@php
    use App\Support\Money;
    use Illuminate\Support\Facades\Storage;

    $horsCharges = max(0, (int) $lease->monthly_rent - (int) $lease->charges);
    $isActive    = $lease->status->value === 'active';
    $photo       = $property?->primaryPhoto;

    $infos = [
        ['calendar', 'Date de début',      $lease->start_date->isoFormat('D MMMM YYYY')],
        ['calendar', 'Date de fin',        $lease->expected_end_date?->isoFormat('D MMMM YYYY') ?? 'Non précisée'],
        ['coins',    'Loyer hors charges', Money::fcfa($horsCharges)],
        ['coins',    'Charges',            Money::fcfa((int) $lease->charges)],
        ['coins',    'Dépôt de garantie',  Money::fcfa((int) $lease->deposit_paid)],
        ['calendar', "Jour d'échéance",    'Le '.$lease->due_day.' de chaque mois'],
    ];
@endphp

<x-layouts.tenant-portal title="Mon logement">
<div class="space-y-6 animate-fade-in max-w-5xl mx-auto">

    {{-- ===== Visuel du bien ===== --}}
    <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100">
        @if($photo)
            <div class="h-56 sm:h-72 relative">
                <img src="{{ Storage::url($photo->path) }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $isActive ? 'bg-emerald-500/90' : 'bg-gray-500/90' }} text-white">
                            {{ $isActive ? 'Bail actif' : 'Bail clos' }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm">
                            {{ $property->type->label() }}
                        </span>
                    </div>
                    <h1 class="text-2xl font-bold">{{ $property->title }}</h1>
                    <p class="text-white/80 text-sm mt-1 flex items-center gap-1.5">
                        <x-tenant-portal.icon name="map-pin" class="w-4 h-4" />
                        {{ $property->fullAddress }}
                    </p>
                </div>
            </div>
        @else
            <div class="h-40 bg-gradient-to-br from-pl-100 to-pl-200 flex items-center justify-center">
                <x-tenant-portal.icon name="building" class="w-12 h-12 text-pl-400" />
            </div>
            <div class="p-6">
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $isActive ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $isActive ? 'Bail actif' : 'Bail clos' }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                        {{ $property->type->label() }}
                    </span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $property->title }}</h1>
                <p class="text-gray-500 text-sm mt-1 flex items-center gap-1.5">
                    <x-tenant-portal.icon name="map-pin" class="w-4 h-4" />
                    {{ $property->fullAddress }}
                </p>
            </div>
        @endif
    </div>

    {{-- ===== Description ===== --}}
    @if($property?->notes)
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-3">Description du logement</h3>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $property->notes }}</p>
        </div>
    @endif

    {{-- ===== Détails du bail ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <div class="flex items-center gap-2 mb-5">
            <x-tenant-portal.icon name="file-text" class="w-5 h-5 text-pl-600" />
            <h3 class="font-semibold text-gray-900">Détails du bail</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($infos as [$icon, $label, $value])
                <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
                    <div class="flex items-center gap-2 text-gray-400 mb-2">
                        <x-tenant-portal.icon :name="$icon" class="w-4 h-4" />
                        <span class="text-xs font-medium uppercase tracking-wide">{{ $label }}</span>
                    </div>
                    <p class="text-base font-semibold text-gray-900 chiffre">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-4 rounded-xl bg-gradient-to-r from-pl-50 to-gold-50 border border-pl-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Loyer mensuel total</p>
                    <p class="text-2xl font-bold text-pl-900 mt-1 chiffre">{{ Money::fcfa((int) $lease->monthly_rent) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center shadow-sm">
                    <x-tenant-portal.icon name="coins" class="w-6 h-6 text-pl-600" />
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Documents du bail ===== --}}
    @if($documents->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Documents liés au bail</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($documents as $doc)
                    <a href="{{ route('tenant-portal.documents.download', $doc) }}"
                       class="flex items-center gap-3 rounded-xl border border-gray-100 p-4 hover:border-gray-200 hover:shadow-sm transition">
                        <div class="w-10 h-10 rounded-lg bg-pl-50 text-pl-600 flex items-center justify-center shrink-0">
                            <x-tenant-portal.icon name="file-text" class="w-5 h-5" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $doc->original_name }}</p>
                            <p class="text-xs text-gray-400">{{ $doc->size_label }}</p>
                        </div>
                        <span class="p-2 rounded-lg text-gray-400">
                            <x-tenant-portal.icon name="download" class="w-4 h-4" />
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
</x-layouts.tenant-portal>
