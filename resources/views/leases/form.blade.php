{{-- resources/views/leases/form.blade.php --}}
<x-layouts.app title="Nouvelle affectation — MEEXEO">
    <x-page-header title="Nouvelle affectation" subtitle="Liez un bien à un locataire." />

    @error('property_id')
        <p class="mt-4 rounded-meexeo border border-impaye-bord bg-impaye-bg px-4 py-3 text-sm text-impaye-texte">{{ $message }}</p>
    @enderror

    <form method="POST" action="{{ route('leases.store') }}" class="mt-6 max-w-3xl space-y-6">
        @csrf

        <section class="rounded-meexeo border border-lin-clair bg-papier p-6">
            <h2 class="font-titre text-lg">Bien et locataire</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="property_id" class="text-xs font-semibold text-ardoise">Bien concerné</label>
                    <select id="property_id" name="property_id" required
                            class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                        <option value="">Choisir un bien…</option>
                        @foreach ($properties as $option)
                            <option value="{{ $option->id }}"
                                @selected((int) old('property_id', $property?->id) === $option->id)>
                                {{ $option->title }} — {{ $option->reference }} ({{ $option->commune ?? $option->city }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-brume">Seuls les biens libres ou en travaux sont proposés.</p>
                </div>
                <div>
                    <label for="tenant_id" class="text-xs font-semibold text-ardoise">Locataire</label>
                    <select id="tenant_id" name="tenant_id" required
                            class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                        <option value="">Choisir un locataire…</option>
                        @foreach ($tenants as $tenant)
                            <option value="{{ $tenant->id }}" @selected((int) old('tenant_id') === $tenant->id)>
                                {{ $tenant->full_name }} — {{ $tenant->reference }}
                            </option>
                        @endforeach
                    </select>
                    @error('tenant_id') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="rounded-meexeo border border-lin-clair bg-papier p-6">
            <h2 class="font-titre text-lg">Période</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="start_date" class="text-xs font-semibold text-ardoise">Date de début</label>
                    <input id="start_date" name="start_date" type="date" required value="{{ old('start_date') }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('start_date') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="expected_end_date" class="text-xs font-semibold text-ardoise">Date de fin prévue</label>
                    <input id="expected_end_date" name="expected_end_date" type="date" value="{{ old('expected_end_date') }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('expected_end_date') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="rounded-meexeo border border-lin-clair bg-papier p-6">
            <h2 class="font-titre text-lg">Conditions financières</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-3">
                <div>
                    <label for="monthly_rent" class="text-xs font-semibold text-ardoise">Loyer à cette date (FCFA)</label>
                    <input id="monthly_rent" name="monthly_rent" type="number" step="1" min="0" required
                           value="{{ old('monthly_rent', $property?->monthly_rent) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('monthly_rent') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="deposit_paid" class="text-xs font-semibold text-ardoise">Caution versée (FCFA)</label>
                    <input id="deposit_paid" name="deposit_paid" type="number" step="1" min="0" required
                           value="{{ old('deposit_paid', $property?->deposit) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('deposit_paid') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="due_day" class="text-xs font-semibold text-ardoise">Jour d'échéance</label>
                    <input id="due_day" name="due_day" type="number" min="1" max="31" required value="{{ old('due_day', 5) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('due_day') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-3">
                    <label for="notes" class="text-xs font-semibold text-ardoise">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="mt-1.5 w-full rounded-meexeo border border-lin px-3 py-2 text-sm">{{ old('notes') }}</textarea>
                </div>
            </div>
        </section>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="min-h-[44px] rounded-meexeo bg-cuivre px-5 text-sm font-semibold text-papier">
                Enregistrer l'affectation
            </button>
            <a href="{{ $property ? route('properties.show', $property) : route('properties.index') }}"
               class="inline-flex min-h-[44px] items-center rounded-meexeo border border-galet bg-papier px-4 text-sm">Annuler</a>
        </div>
    </form>
</x-layouts.app>
