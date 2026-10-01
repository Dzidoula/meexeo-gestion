<x-layouts.app title="Galerie — MASTERCLAYS">
    <x-page-header title="Galerie photos">
        <x-slot:actions>
            <a href="{{ route('hotel-galleries.create') }}" class="min-h-[44px] inline-flex items-center px-4 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">Ajouter une photo</a>
        </x-slot:actions>
    </x-page-header>
    @include('hotel._subnav')

    @if (session('status'))
        <p class="mt-4 text-sm" style="color:var(--color-mc-success)">{{ session('status') }}</p>
    @endif

    <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
        @foreach ($galleries as $gallery)
            <div style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);overflow:hidden">
                <img src="{{ $gallery->display_url }}" alt="{{ $gallery->title }}" class="h-36 w-full object-cover">
                <div style="padding:10px">
                    <p class="text-sm font-semibold" style="color:var(--color-mc-ink)">{{ $gallery->title ?? 'Sans titre' }}</p>
                    @if ($gallery->category)
                        <p class="text-xs" style="color:var(--color-mc-ink-faint)">{{ $gallery->category }}</p>
                    @endif
                    <form method="POST" action="{{ route('hotel-galleries.destroy', $gallery) }}" class="mt-2">
                        @csrf @method('DELETE')
                        <button class="text-xs font-semibold" style="color:var(--color-mc-danger)">Supprimer</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $galleries->links() }}</div>
</x-layouts.app>
