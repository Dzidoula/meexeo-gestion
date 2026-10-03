@php
    use App\Enums\PortalDocumentCategory;

    // Les quatre premières catégories alimentent les cartes du haut, comme la maquette.
    $cards = [
        PortalDocumentCategory::Lease,
        PortalDocumentCategory::Receipt,
        PortalDocumentCategory::Invoice,
        PortalDocumentCategory::Insurance,
    ];

    $filters = [
        ''          => 'Tous',
        'bail'      => 'Baux',
        'quittance' => 'Quittances',
        'autre'     => 'Autres',
    ];

    $active = request('category', '');
@endphp

<x-layouts.tenant-portal title="Documents">
<div class="space-y-6 animate-fade-in max-w-5xl mx-auto">

    {{-- ===== Cartes de synthèse ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($cards as $cat)
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="w-10 h-10 rounded-xl {{ $cat->tone() }} flex items-center justify-center">
                    <x-tenant-portal.icon :name="$cat->icon()" class="w-5 h-5" />
                </div>
                <p class="text-2xl font-bold text-gray-900 mt-3 chiffre">{{ $counts[$cat->value] ?? 0 }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $cat->label() }}</p>
            </div>
        @endforeach
    </div>

    {{-- ===== Liste ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <h3 class="font-semibold text-gray-900">Mes documents</h3>

                <form method="GET" class="relative">
                    @if($active)<input type="hidden" name="category" value="{{ $active }}">@endif
                    <x-tenant-portal.icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher..."
                           class="pl-10 pr-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-pl-500 focus:border-transparent transition w-48 sm:w-56">
                </form>
            </div>

            <div class="flex gap-1 bg-gray-100 rounded-lg p-1 mt-4 w-fit">
                @foreach($filters as $value => $label)
                    <a href="{{ route('tenant-portal.documents', array_filter(['category' => $value, 'q' => request('q')])) }}"
                       class="px-3 py-1.5 rounded-md text-xs font-medium transition
                              {{ $active === $value ? 'bg-white text-pl-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        @forelse($documents as $doc)
            @if($loop->first)<div class="divide-y divide-gray-50">@endif

            <a href="{{ route('tenant-portal.documents.download', $doc) }}"
               class="flex items-center gap-4 p-4 sm:px-6 hover:bg-gray-50/50 transition">
                <div class="w-11 h-11 rounded-xl {{ $doc->category->tone() }} flex items-center justify-center shrink-0">
                    <x-tenant-portal.icon :name="$doc->category->icon()" class="w-5 h-5" />
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $doc->original_name }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs text-gray-400">{{ $doc->category->label() }}</span>
                        <span class="text-gray-300">•</span>
                        <span class="text-xs text-gray-400">{{ $doc->size_label }}</span>
                        <span class="text-gray-300">•</span>
                        <span class="text-xs text-gray-400">
                            {{ $doc->issued_at?->isoFormat('D MMMM YYYY') ?? $doc->created_at->isoFormat('D MMMM YYYY') }}
                        </span>
                    </div>
                </div>
                <span class="p-2.5 rounded-lg text-gray-400 shrink-0" title="Télécharger">
                    <x-tenant-portal.icon name="download" class="w-4 h-4" />
                </span>
            </a>

            @if($loop->last)</div>@endif
        @empty
            <div class="py-16 text-center">
                <x-tenant-portal.icon name="file-text" class="w-10 h-10 text-gray-300 mx-auto mb-3" />
                <p class="text-sm text-gray-400">Aucun document trouvé</p>
            </div>
        @endforelse
    </div>

</div>
</x-layouts.tenant-portal>
