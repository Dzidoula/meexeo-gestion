<x-layouts.app :title="($hotelTestimonial->exists ? 'Modifier' : 'Ajouter').' un témoignage — MASTERCLAYS'">
    <x-page-header :title="$hotelTestimonial->exists ? 'Modifier le témoignage' : 'Ajouter un témoignage'" />
    @include('hotel._subnav')

    <form method="POST" action="{{ $hotelTestimonial->exists ? route('hotel-testimonials.update', $hotelTestimonial) : route('hotel-testimonials.store') }}" class="mt-6 max-w-lg space-y-4">
        @csrf
        @if ($hotelTestimonial->exists) @method('PUT') @endif

        <div>
            <label for="author_name" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Nom de l'auteur</label>
            <input id="author_name" name="author_name" value="{{ old('author_name', $hotelTestimonial->author_name) }}" required
                   class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
            @error('author_name') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="author_subtitle" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Sous-titre (ex: ville, pays)</label>
            <input id="author_subtitle" name="author_subtitle" value="{{ old('author_subtitle', $hotelTestimonial->author_subtitle) }}"
                   class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
        </div>

        <div>
            <label for="author_image" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Photo de l'auteur (URL)</label>
            <input id="author_image" name="author_image" value="{{ old('author_image', $hotelTestimonial->author_image) }}"
                   class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
            @error('author_image') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="rating" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Note (1 à 5)</label>
            <input id="rating" name="rating" type="number" step="0.1" min="1" max="5" value="{{ old('rating', $hotelTestimonial->rating) }}" required
                   class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
            @error('rating') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="title" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Titre</label>
            <input id="title" name="title" value="{{ old('title', $hotelTestimonial->title) }}" required
                   class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
            @error('title') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="content" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Contenu</label>
            <textarea id="content" name="content" rows="4" required
                      class="mt-1.5 w-full px-3 py-2 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">{{ old('content', $hotelTestimonial->content) }}</textarea>
            @error('content') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-2">
            <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $hotelTestimonial->is_active ?? true))>
            <label for="is_active" class="text-sm" style="color:var(--color-mc-ink-soft)">Actif (visible sur le site)</label>
        </div>

        <button class="min-h-[44px] px-5 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
            Enregistrer
        </button>
    </form>
</x-layouts.app>
