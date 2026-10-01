<x-layouts.app title="Ajouter une photo — MASTERCLAYS">
    <x-page-header title="Ajouter une photo à la galerie" />
    @include('hotel._subnav')

    <form method="POST" action="{{ route('hotel-galleries.store') }}" enctype="multipart/form-data" class="mt-6 max-w-lg space-y-4">
        @csrf
        <div>
            <label for="image" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Photo</label>
            <input id="image" name="image" type="file" accept="image/*" required class="mt-1.5 w-full text-sm">
            @error('image') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="title" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Titre</label>
            <input id="title" name="title" value="{{ old('title') }}" class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
        </div>
        <div>
            <label for="category" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Catégorie</label>
            <input id="category" name="category" value="{{ old('category') }}" class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
        </div>
        <button class="min-h-[44px] px-5 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">Enregistrer</button>
    </form>
</x-layouts.app>
