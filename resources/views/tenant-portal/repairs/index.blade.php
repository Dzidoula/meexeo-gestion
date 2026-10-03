@php
    $types = [
        'plomberie'     => ['Plomberie',     'droplets'],
        'electricite'   => ['Électricité',   'zap'],
        'climatisation' => ['Climatisation', 'flame'],
        'serrure'       => ['Serrure',       'package'],
        'peinture'      => ['Peinture',      'package'],
        'autre'         => ['Autre',         'package'],
    ];

    $statuses = [
        'recu'     => ['Reçu',    'bg-blue-50 text-blue-700',       'clock'],
        'en_cours' => ['En cours','bg-amber-50 text-amber-700',     'hammer'],
        'repare'   => ['Réparé',  'bg-emerald-50 text-emerald-700', 'check-circle-2'],
        'cloture'  => ['Clôturé', 'bg-gray-100 text-gray-600',      'check-circle-2'],
    ];

    $urgencies = [
        'faible'  => ['Faible',  'bg-gray-100 text-gray-600'],
        'moyenne' => ['Normale', 'bg-blue-50 text-blue-700'],
        'urgente' => ['Urgente', 'bg-red-50 text-red-700'],
    ];
@endphp

<x-layouts.tenant-portal title="Entretien">
<div class="space-y-6 animate-fade-in max-w-5xl mx-auto" x-data="{ form: {{ $errors->any() ? 'true' : 'false' }} }">

    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Demandes d'entretien</h3>
            <p class="text-sm text-gray-500 mt-1">
                Signalez un problème technique ou suivez une intervention en cours.
            </p>
        </div>
        <button type="button" @click="form = true"
                class="flex items-center gap-2 bg-pl-600 hover:bg-pl-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-all hover:shadow-lg hover:shadow-pl-500/25">
            <x-tenant-portal.icon name="plus" class="w-4 h-4" />
            Nouvelle demande
        </button>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800 flex items-center gap-2">
            <x-tenant-portal.icon name="check" class="w-4 h-4" />
            {{ session('success') }}
        </div>
    @endif

    {{-- ===== Liste ===== --}}
    @forelse($repairs as $req)
        @if($loop->first)<div class="grid grid-cols-1 gap-4">@endif
        @php
            [$typeLabel, $typeIcon]            = $types[$req->type]      ?? $types['autre'];
            [$statLabel, $statTone, $statIcon] = $statuses[$req->status] ?? $statuses['recu'];
            [$urgLabel, $urgTone]              = $urgencies[$req->urgency] ?? $urgencies['moyenne'];
        @endphp

        <a href="{{ route('tenant-portal.repairs.show', $req) }}"
           class="block bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-md transition">
            <div class="p-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3 min-w-0 flex-1">
                        <div class="w-10 h-10 rounded-xl bg-pl-50 text-pl-600 flex items-center justify-center shrink-0">
                            <x-tenant-portal.icon :name="$typeIcon" class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-semibold text-gray-900">{{ $req->ticket_no }} — {{ $typeLabel }}</h4>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $req->description }}</p>
                            <div class="flex items-center gap-2 mt-3 flex-wrap">
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $statTone }} flex items-center gap-1">
                                    <x-tenant-portal.icon :name="$statIcon" class="w-3 h-3" />
                                    {{ $statLabel }}
                                </span>
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $urgTone }}">
                                    Priorité : {{ $urgLabel }}
                                </span>
                                <span class="text-xs text-gray-400">•</span>
                                <span class="text-xs text-gray-400">{{ $req->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    <x-tenant-portal.icon name="arrow-right" class="w-4 h-4 text-gray-300 shrink-0 mt-1" />
                </div>
            </div>
        </a>

        @if($loop->last)</div>@endif
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 py-16 text-center">
            <x-tenant-portal.icon name="wrench" class="w-10 h-10 text-gray-300 mx-auto mb-3" />
            <p class="text-sm text-gray-400">Aucune demande d'entretien pour le moment</p>
            <button type="button" @click="form = true" class="mt-4 text-sm text-pl-600 hover:text-pl-700 font-medium">
                Créer une première demande
            </button>
        </div>
    @endforelse

    @if($repairs->hasPages())
        <div>{{ $repairs->links() }}</div>
    @endif

    {{-- ===== Nouvelle demande ===== --}}
    <div x-show="form" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="form = false">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm animate-fade-in" @click="form = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg animate-scale-in max-h-[90vh] overflow-y-auto scrollbar-thin">
            <div class="flex items-center justify-between p-6 border-b border-gray-100 sticky top-0 bg-white">
                <h3 class="font-semibold text-gray-900">Nouvelle demande</h3>
                <button type="button" @click="form = false" class="text-gray-400 hover:text-gray-600 transition">
                    <x-tenant-portal.icon name="close" class="w-5 h-5" />
                </button>
            </div>

            <form method="POST" action="{{ route('tenant-portal.repairs.store') }}"
                  enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1.5">Type de problème</label>
                    <select id="type" name="type" required
                            class="w-full px-3 py-2.5 rounded-lg border bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-pl-500 focus:border-transparent transition @error('type') border-red-300 @else border-gray-200 @enderror">
                        <option value="">Choisir...</option>
                        @foreach($types as $value => [$label, $ic])
                            <option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                    <textarea id="description" name="description" rows="5" required
                              placeholder="Décrivez le problème le plus précisément possible..."
                              class="w-full px-3 py-2.5 rounded-lg border bg-gray-50 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-pl-500 focus:border-transparent transition @error('description') border-red-300 @else border-gray-200 @enderror">{{ old('description') }}</textarea>
                    @error('description')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Urgence</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach($urgencies as $value => [$label, $tone])
                            <label class="relative cursor-pointer">
                                <input type="radio" name="urgency" value="{{ $value }}"
                                       @checked(old('urgency', 'moyenne') === $value) class="sr-only peer">
                                <div class="text-center py-2.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-500 peer-checked:border-pl-500 peer-checked:bg-pl-50 peer-checked:text-pl-700 transition">
                                    {{ $label }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('urgency')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="photos" class="block text-sm font-medium text-gray-700 mb-1.5">Photos (optionnel)</label>
                    <p class="text-xs text-gray-400 mb-2">Jusqu'à 5 photos, 5 Mo chacune. Une photo accélère beaucoup le diagnostic.</p>
                    <input id="photos" type="file" name="photos[]" multiple accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-pl-600 file:text-white file:text-sm file:font-medium file:cursor-pointer">
                    @error('photos')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    @error('photos.*')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="form = false"
                            class="flex-1 px-4 py-2.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 bg-pl-600 hover:bg-pl-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition flex items-center justify-center gap-2">
                        <x-tenant-portal.icon name="send" class="w-4 h-4" />
                        Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-layouts.tenant-portal>
