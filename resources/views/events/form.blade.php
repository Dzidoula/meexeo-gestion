<x-layouts.app :title="($event->exists ? 'Modifier' : 'Ajouter').' un événement — MASTERCLAYS'">
    <x-page-header :title="$event->exists ? 'Modifier l\'événement' : 'Nouvel événement'" />

    <form method="POST" action="{{ $event->exists ? route('events.update', $event) : route('events.store') }}" class="mt-6 max-w-lg space-y-4">
        @csrf
        @if ($event->exists) @method('PUT') @endif

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="client_name" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Nom du client</label>
                <input id="client_name" name="client_name" value="{{ old('client_name', $event->client_name) }}" required
                       class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                @error('client_name') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="client_phone" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Téléphone</label>
                <input id="client_phone" name="client_phone" value="{{ old('client_phone', $event->client_phone) }}" required
                       class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                @error('client_phone') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="start_date" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Date de début</label>
                <input id="start_date" name="start_date" type="date" value="{{ old('start_date', optional($event->start_date)->toDateString()) }}" required
                       class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                @error('start_date') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="end_date" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Date de fin</label>
                <input id="end_date" name="end_date" type="date" value="{{ old('end_date', optional($event->end_date)->toDateString()) }}" required
                       class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                @error('end_date') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="venue" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Lieu</label>
            <input id="venue" name="venue" value="{{ old('venue', $event->venue) }}" required
                   class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
            @error('venue') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="budget_total" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Budget total (FCFA)</label>
                <input id="budget_total" name="budget_total" type="number" step="1" min="0" value="{{ old('budget_total', $event->budget_total) }}" required
                       class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                @error('budget_total') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="deposit_amount" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Acompte (FCFA)</label>
                <input id="deposit_amount" name="deposit_amount" type="number" step="1" min="0" value="{{ old('deposit_amount', $event->deposit_amount ?? 0) }}" required
                       class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                @error('deposit_amount') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="notes" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Observations</label>
            <textarea id="notes" name="notes" rows="3"
                      class="mt-1.5 w-full px-3 py-2 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">{{ old('notes', $event->notes) }}</textarea>
        </div>

        <button class="min-h-[44px] px-5 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
            Enregistrer
        </button>
    </form>
</x-layouts.app>
