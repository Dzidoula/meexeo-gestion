@php
    $kinds = [
        'payment'     => ['credit-card', 'bg-pl-50 text-pl-600'],
        'maintenance' => ['wrench',      'bg-amber-50 text-amber-600'],
        'rent'        => ['calendar',    'bg-amber-50 text-amber-600'],
    ];
@endphp

<x-layouts.tenant-portal title="Notifications">
<div class="space-y-6 animate-fade-in max-w-3xl mx-auto">

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Notifications</h3>
            <p class="text-sm text-gray-500 mt-1">Les événements qui concernent vos loyers et vos demandes.</p>
        </div>

        @forelse($notifications as $n)
            @php
                [$icon, $tone] = $kinds[$n->data['kind'] ?? ''] ?? ['bell', 'bg-gray-100 text-gray-500'];
                $isNew = in_array($n->id, $unreadIds, true);
            @endphp
            @if($loop->first)<div class="divide-y divide-gray-50">@endif

            <a href="{{ $n->data['url'] ?? '#' }}"
               class="flex items-start gap-4 p-4 sm:px-6 hover:bg-gray-50/50 transition {{ $isNew ? 'bg-pl-50/40' : '' }}">
                <div class="w-10 h-10 rounded-xl {{ $tone }} flex items-center justify-center shrink-0">
                    <x-tenant-portal.icon :name="$icon" class="w-5 h-5" />
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm {{ $isNew ? 'font-semibold' : 'font-medium' }} text-gray-900">{{ $n->data['title'] ?? '' }}</p>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $n->data['body'] ?? '' }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                </div>
                @if($isNew)
                    <span class="w-2 h-2 rounded-full bg-gold-400 mt-2 shrink-0"></span>
                @endif
            </a>

            @if($loop->last)</div>@endif
        @empty
            <div class="py-16 text-center">
                <x-tenant-portal.icon name="bell" class="w-10 h-10 text-gray-300 mx-auto mb-3" />
                <p class="text-sm text-gray-400">Aucune notification pour le moment</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div>{{ $notifications->links() }}</div>
    @endif

</div>
</x-layouts.tenant-portal>
