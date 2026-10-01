<x-layouts.app :title="($hotelFaq->exists ? 'Modifier' : 'Ajouter').' une question — MASTERCLAYS'">
    <x-page-header :title="$hotelFaq->exists ? 'Modifier la question' : 'Ajouter une question'" />
    @include('hotel._subnav')

    <form method="POST" action="{{ $hotelFaq->exists ? route('hotel-faqs.update', $hotelFaq) : route('hotel-faqs.store') }}" class="mt-6 max-w-lg space-y-4">
        @csrf
        @if ($hotelFaq->exists) @method('PUT') @endif

        <div>
            <label for="question" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Question</label>
            <input id="question" name="question" value="{{ old('question', $hotelFaq->question) }}" required
                   class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
            @error('question') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="answer" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Réponse</label>
            <textarea id="answer" name="answer" rows="4" required
                      class="mt-1.5 w-full px-3 py-2 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">{{ old('answer', $hotelFaq->answer) }}</textarea>
            @error('answer') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="order" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Ordre d'affichage</label>
            <input id="order" name="order" type="number" value="{{ old('order', $hotelFaq->order ?? 0) }}" required
                   class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
            @error('order') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-2">
            <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $hotelFaq->is_active ?? true))>
            <label for="is_active" class="text-sm" style="color:var(--color-mc-ink-soft)">Actif (visible sur le site)</label>
        </div>

        <button class="min-h-[44px] px-5 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
            Enregistrer
        </button>
    </form>
</x-layouts.app>
