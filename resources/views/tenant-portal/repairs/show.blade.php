<x-layouts.tenant-portal title="{{ $repair->ticket_no }}">

    <div class="mb-6">
        <a href="{{ route('tenant-portal.repairs') }}" class="text-ardoise text-sm flex items-center gap-1 mb-3">← Mes signalements</a>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-semibold text-lagune">{{ $repair->ticket_no }}</h1>
            <x-tenant-portal.repair-status-badge :status="$repair->status" />
        </div>
        <p class="text-sm text-ardoise mt-1">{{ $repair->created_at->isoFormat('D MMMM YYYY') }}</p>
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-ardoise mb-1">Type</p>
            <p class="font-medium text-lagune capitalize">{{ str_replace('_', ' ', $repair->type) }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-ardoise mb-1">Description</p>
            <p class="text-lagune">{{ $repair->description }}</p>
        </div>

        @if($repair->notes)
            <div class="bg-blue-50 rounded-2xl p-5">
                <p class="text-xs text-blue-600 mb-1">Note du gestionnaire</p>
                <p class="text-blue-800">{{ $repair->notes }}</p>
            </div>
        @endif

        @if($repair->photos)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <p class="text-xs text-ardoise mb-3">Photos</p>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($repair->photos as $photo)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($photo) }}" class="rounded-xl object-cover w-full h-28">
                    @endforeach
                </div>
            </div>
        @endif
    </div>

</x-layouts.tenant-portal>
