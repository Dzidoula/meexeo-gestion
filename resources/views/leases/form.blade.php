{{-- resources/views/leases/form.blade.php --}}
<x-layouts.app title="Nouvelle affectation — MEEXEO">
    <x-page-header title="Nouvelle affectation" subtitle="Liez un bien à un locataire." />

    @error('property_id')
        <p class="mt-4 px-4 py-3 text-sm" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-danger);background:rgba(220,38,38,.08);color:var(--color-mc-danger)">{{ $message }}</p>
    @enderror

    <form method="POST" action="{{ route('leases.store') }}" class="mt-6 max-w-3xl space-y-6">
        @csrf

        <section style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:24px">
            <h2 class="font-titre text-lg">Bien et locataire</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="property_id" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Bien concerné</label>
                    <select id="property_id" name="property_id" required
                            class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                        <option value="">Choisir un bien…</option>
                        @foreach ($properties as $option)
                            <option value="{{ $option->id }}"
                                @selected((int) old('property_id', $property?->id) === $option->id)>
                                {{ $option->title }} — {{ $option->reference }} ({{ $option->commune ?? $option->city }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs" style="color:var(--color-mc-ink-faint)">Seuls les biens libres ou en travaux sont proposés.</p>
                </div>
                <div>
                    <label for="tenant_id" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Locataire</label>
                    <select id="tenant_id" name="tenant_id" required
                            class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                        <option value="">Choisir un locataire…</option>
                        @foreach ($tenants as $tenant)
                            <option value="{{ $tenant->id }}" @selected((int) old('tenant_id') === $tenant->id)>
                                {{ $tenant->full_name }} — {{ $tenant->reference }}
                            </option>
                        @endforeach
                    </select>
                    @error('tenant_id') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:24px">
            <h2 class="font-titre text-lg">Période</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="start_date" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Date de début</label>
                    <input id="start_date" name="start_date" type="date" required value="{{ old('start_date') }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('start_date') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="expected_end_date" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Date de fin prévue</label>
                    <input id="expected_end_date" name="expected_end_date" type="date" value="{{ old('expected_end_date') }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('expected_end_date') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:24px">
            <h2 class="font-titre text-lg">Conditions financières</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-3">
                <div>
                    <label for="monthly_rent" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Loyer à cette date (FCFA)</label>
                    <input id="monthly_rent" name="monthly_rent" type="number" step="1" min="0" required
                           value="{{ old('monthly_rent', $property?->monthly_rent) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('monthly_rent') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="deposit_paid" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Caution versée (FCFA)</label>
                    <input id="deposit_paid" name="deposit_paid" type="number" step="1" min="0" required
                           value="{{ old('deposit_paid', $property?->deposit) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('deposit_paid') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="due_day" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Jour d'échéance</label>
                    <input id="due_day" name="due_day" type="number" min="1" max="31" required value="{{ old('due_day', 5) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('due_day') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-3">
                    <label for="notes" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="mt-1.5 w-full px-3 py-2 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">{{ old('notes') }}</textarea>
                </div>
            </div>
        </section>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="min-h-[44px] px-5 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
                Enregistrer l'affectation
            </button>
            <a href="{{ $property ? route('properties.show', $property) : route('properties.index') }}"
               class="inline-flex min-h-[44px] items-center px-4 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);color:var(--color-mc-ink)">Annuler</a>
        </div>
    </form>
</x-layouts.app>
