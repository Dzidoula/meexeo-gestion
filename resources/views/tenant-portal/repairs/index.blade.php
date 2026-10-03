<x-layouts.tenant-portal title="Réparations">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-lagune">Mes signalements</h1>
        <a href="{{ route('tenant-portal.repairs.create') }}"
           class="bg-lagune text-white px-4 py-2 rounded-xl text-sm font-medium">
            + Signaler
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4 text-green-800 text-sm">✅ {{ session('success') }}</div>
    @endif

    <div class="space-y-3">
        @forelse($repairs as $repair)
            <a href="{{ route('tenant-portal.repairs.show', $repair) }}"
               class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hover:border-lagune/30 transition-colors">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-mono text-xs text-ardoise">{{ $repair->ticket_no }}</span>
                    <x-tenant-portal.repair-status-badge :status="$repair->status" />
                </div>
                <p class="font-medium text-lagune capitalize">{{ str_replace('_', ' ', $repair->type) }}</p>
                <p class="text-sm text-ardoise mt-0.5 line-clamp-1">{{ $repair->description }}</p>
                <p class="text-xs text-ardoise mt-2">{{ $repair->created_at->isoFormat('D MMM YYYY') }}</p>
            </a>
        @empty
            <div class="text-center py-12 text-ardoise">
                <p class="text-4xl mb-3">🔧</p>
                <p>Aucun signalement pour l'instant.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $repairs->links() }}</div>

</x-layouts.tenant-portal>
