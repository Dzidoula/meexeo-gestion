<x-layouts.app :title="($hotelPromoCode->exists ? 'Modifier' : 'Ajouter').' un code promo — MASTERCLAYS'">
    <x-page-header :title="$hotelPromoCode->exists ? 'Modifier le code promo' : 'Ajouter un code promo'" />

    <form method="POST" action="{{ $hotelPromoCode->exists ? route('hotel-promo-codes.update', $hotelPromoCode) : route('hotel-promo-codes.store') }}" class="mt-6 max-w-lg space-y-4">
        @csrf
        @if ($hotelPromoCode->exists) @method('PUT') @endif

        <div>
            <label for="code" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Code</label>
            <input id="code" name="code" value="{{ old('code', $hotelPromoCode->code) }}" required
                   class="mt-1.5 min-h-[44px] w-full px-3 text-sm uppercase" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
            @error('code') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="discount_type" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Type de remise</label>
                <select id="discount_type" name="discount_type" class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    <option value="percent" @selected(old('discount_type', $hotelPromoCode->discount_type) === 'percent')>Pourcentage</option>
                    <option value="fixed" @selected(old('discount_type', $hotelPromoCode->discount_type) === 'fixed')>Montant fixe</option>
                </select>
            </div>
            <div>
                <label for="discount_value" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Valeur</label>
                <input id="discount_value" name="discount_value" type="number" step="0.01" min="0.01" value="{{ old('discount_value', $hotelPromoCode->discount_value) }}" required
                       class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                @error('discount_value') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="min_total" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Montant minimum (FCFA)</label>
                <input id="min_total" name="min_total" type="number" step="1" min="0" value="{{ old('min_total', $hotelPromoCode->min_total) }}"
                       class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
            </div>
            <div>
                <label for="max_uses" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Utilisations maximum</label>
                <input id="max_uses" name="max_uses" type="number" step="1" min="1" value="{{ old('max_uses', $hotelPromoCode->max_uses) }}"
                       class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="starts_at" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Début</label>
                <input id="starts_at" name="starts_at" type="date" value="{{ old('starts_at', optional($hotelPromoCode->starts_at)->format('Y-m-d')) }}"
                       class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
            </div>
            <div>
                <label for="expires_at" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Fin</label>
                <input id="expires_at" name="expires_at" type="date" value="{{ old('expires_at', optional($hotelPromoCode->expires_at)->format('Y-m-d')) }}"
                       class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                @error('expires_at') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center gap-2">
            <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $hotelPromoCode->is_active ?? true))>
            <label for="is_active" class="text-sm" style="color:var(--color-mc-ink-soft)">Actif</label>
        </div>

        <button class="min-h-[44px] px-5 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
            Enregistrer
        </button>
    </form>
</x-layouts.app>
