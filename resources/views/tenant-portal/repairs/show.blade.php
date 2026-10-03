@php
    use Illuminate\Support\Facades\Storage;

    $types = [
        'plomberie' => ['Plomberie', 'droplets'], 'electricite' => ['Électricité', 'zap'],
        'climatisation' => ['Climatisation', 'flame'], 'serrure' => ['Serrure', 'package'],
        'peinture' => ['Peinture', 'package'], 'autre' => ['Autre', 'package'],
    ];
    $statuses = [
        'recu'     => ['Reçu',    'bg-blue-50 text-blue-700',       'clock'],
        'en_cours' => ['En cours','bg-amber-50 text-amber-700',     'hammer'],
        'repare'   => ['Réparé',  'bg-emerald-50 text-emerald-700', 'check-circle-2'],
        'cloture'  => ['Clôturé', 'bg-gray-100 text-gray-600',      'check-circle-2'],
    ];
    $urgencies = [
        'faible' => ['Faible', 'bg-gray-100 text-gray-600'],
        'moyenne' => ['Normale', 'bg-blue-50 text-blue-700'],
        'urgente' => ['Urgente', 'bg-red-50 text-red-700'],
    ];

    [$typeLabel, $typeIcon]            = $types[$repair->type]      ?? $types['autre'];
    [$statLabel, $statTone, $statIcon] = $statuses[$repair->status] ?? $statuses['recu'];
    [$urgLabel, $urgTone]              = $urgencies[$repair->urgency] ?? $urgencies['moyenne'];

    $steps = ['recu' => 'Reçue', 'en_cours' => 'En cours', 'repare' => 'Réparé', 'cloture' => 'Clôturé'];
    $order = array_keys($steps);
    $pos   = array_search($repair->status, $order, true);
@endphp

<x-layouts.tenant-portal title="Entretien">
<div class="space-y-6 animate-fade-in max-w-3xl mx-auto">

    <a href="{{ route('tenant-portal.repairs') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition">
        <x-tenant-portal.icon name="arrow-right" class="w-4 h-4 rotate-180" />
        Mes demandes
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-pl-50 text-pl-600 flex items-center justify-center shrink-0">
                <x-tenant-portal.icon :name="$typeIcon" class="w-6 h-6" />
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-xl font-bold text-gray-900">{{ $repair->ticket_no }}</h1>
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $statTone }} flex items-center gap-1">
                        <x-tenant-portal.icon :name="$statIcon" class="w-3 h-3" />
                        {{ $statLabel }}
                    </span>
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $urgTone }}">Priorité : {{ $urgLabel }}</span>
                </div>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $typeLabel }} · Signalé le {{ $repair->created_at->isoFormat('D MMMM YYYY') }}
                </p>
            </div>
        </div>

        {{-- Frise d'avancement --}}
        <div class="mt-6 flex items-center">
            @foreach($steps as $key => $label)
                @php $done = $pos !== false && array_search($key, $order, true) <= $pos @endphp
                <div class="flex items-center {{ ! $loop->last ? 'flex-1' : '' }}">
                    <div class="flex flex-col items-center">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold
                                     {{ $done ? 'bg-pl-600 text-white' : 'bg-gray-100 text-gray-400' }}">
                            @if($done)<x-tenant-portal.icon name="check" class="w-3.5 h-3.5" />@else{{ $loop->iteration }}@endif
                        </span>
                        <span class="text-[10px] mt-1.5 {{ $done ? 'text-gray-700 font-medium' : 'text-gray-400' }}">{{ $label }}</span>
                    </div>
                    @if(! $loop->last)
                        <span class="flex-1 h-0.5 mx-1 -mt-5 {{ $done ? 'bg-pl-600' : 'bg-gray-100' }}"></span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-900 mb-3">Description</h3>
        <p class="text-sm text-gray-600 leading-relaxed">{{ $repair->description }}</p>
    </div>

    @if($repair->notes)
        <div class="rounded-2xl bg-pl-50 border border-pl-100 p-6">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-white flex items-center justify-center shrink-0 shadow-sm">
                    <x-tenant-portal.icon name="building" class="w-4 h-4 text-pl-600" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-pl-700 uppercase tracking-wide">Note du gestionnaire</p>
                    <p class="text-sm text-gray-700 mt-1 leading-relaxed">{{ $repair->notes }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($repair->photos)
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Photos</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($repair->photos as $photo)
                    <a href="{{ Storage::url($photo) }}" target="_blank" rel="noopener"
                       class="block rounded-xl overflow-hidden border border-gray-100 hover:border-gray-200 transition">
                        <img src="{{ Storage::url($photo) }}" alt="" class="w-full h-28 object-cover">
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
</x-layouts.tenant-portal>
