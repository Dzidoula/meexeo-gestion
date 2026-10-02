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
                    <div class="mt-2 flex items-center">
                        <a href="{{ route('hotel-galleries.edit', $gallery) }}" title="Modifier" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-[color:var(--color-mc-ink-faint)] hover:bg-[var(--color-mc-canvas)] hover:text-[color:var(--color-mc-accent)]">
                            <x-mc-icon name="pencil" class="h-4 w-4" />
                        </a>
                        <form method="POST" action="{{ route('hotel-galleries.destroy', $gallery) }}" onsubmit="return confirm('Supprimer définitivement cette photo ?')">
                            @csrf @method('DELETE')
                            <button type="submit" title="Supprimer" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-[color:var(--color-mc-danger)] hover:bg-red-50">
                                <x-mc-icon name="trash-2" class="h-4 w-4" />
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $galleries->links() }}</div>
</x-layouts.app>
