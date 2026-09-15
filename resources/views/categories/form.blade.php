<x-layouts.app :title="($category->exists ? 'Modifier' : 'Ajouter').' une catégorie — MEEXEO'">
    <x-page-header :title="$category->exists ? 'Modifier la catégorie' : 'Ajouter une catégorie'" />

    <form method="POST" action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}" class="mt-6 max-w-md">
        @csrf
        @if ($category->exists) @method('PUT') @endif

        <label for="name" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Nom</label>
        <input id="name" name="name" value="{{ old('name', $category->name) }}" required
               class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
        @error('name') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror

        <button class="mt-4 min-h-[44px] px-5 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
            Enregistrer
        </button>
    </form>
</x-layouts.app>
