<x-layouts.app title="Modifier une photo — MASTERCLAYS">
    <x-page-header title="Modifier la photo" />
    @include('hotel._subnav')

    <div class="mt-6 max-w-lg">
        <img src="{{ $gallery->display_url }}" alt="{{ $gallery->title }}" class="h-40 w-full object-cover" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
    </div>

    <form method="POST" action="{{ route('hotel-galleries.update', $gallery) }}" class="mt-4 max-w-lg space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label for="title" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Titre</label>
            <input id="title" name="title" value="{{ old('title', $gallery->title) }}" class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
        </div>
        <div>
            <label for="category" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Catégorie</label>
            <input id="category" name="category" value="{{ old('category', $gallery->category) }}" class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
        </div>
        <button class="min-h-[44px] px-5 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">Enregistrer</button>
    </form>
</x-layouts.app>
