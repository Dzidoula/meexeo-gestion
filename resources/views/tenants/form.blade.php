{{-- resources/views/tenants/form.blade.php --}}
@php($editing = $tenant->exists)
<x-layouts.app :title="($editing ? 'Modifier la fiche' : 'Nouveau locataire').' — MEEXEO'">
    <x-page-header :title="$editing ? 'Modifier la fiche locataire' : 'Nouveau locataire'"
                   :subtitle="$editing ? $tenant->reference : 'Renseignez l\'identité, l\'activité et les contacts.'" />

    <form method="POST" action="{{ $editing ? route('tenants.update', $tenant) : route('tenants.store') }}"
          class="mt-6 max-w-3xl space-y-6">
        @csrf
        @if ($editing) @method('PUT') @endif

        <section class="rounded-meexeo border border-lin-clair bg-papier p-6">
            <h2 class="font-titre text-lg">Identité</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="last_name" class="text-xs font-semibold text-ardoise">Nom</label>
                    <input id="last_name" name="last_name" required value="{{ old('last_name', $tenant->last_name) }}"
                           class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('last_name') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="first_names" class="text-xs font-semibold text-ardoise">Prénoms</label>
                    <input id="first_names" name="first_names" required value="{{ old('first_names', $tenant->first_names) }}"
                           class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('first_names') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="birth_date" class="text-xs font-semibold text-ardoise">Date de naissance</label>
                    <input id="birth_date" name="birth_date" type="date"
                           value="{{ old('birth_date', $tenant->birth_date?->format('Y-m-d')) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('birth_date') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="id_number" class="text-xs font-semibold text-ardoise">N° CNI / Passeport</label>
                    <input id="id_number" name="id_number" value="{{ old('id_number', $tenant->id_number) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('id_number') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="text-xs font-semibold text-ardoise">Statut</label>
                    <select id="status" name="status" class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $tenant->status?->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div x-data="{ marital: '{{ old('marital_status', $tenant->marital_status?->value) }}' }" class="sm:col-span-2">
    <label for="marital_status" class="text-xs font-semibold text-ardoise">Situation matrimoniale</label>
    <select id="marital_status" name="marital_status" x-model="marital"
            class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
        <option value="">Non renseignée</option>
        @foreach ($maritalStatuses as $value => $label)
            <option value="{{ $value }}" @selected(old('marital_status', $tenant->marital_status?->value) === $value)>{{ $label }}</option>
        @endforeach
    </select>

    {{-- Révélé pour un locataire marié ou en couple : le serveur revalide de toute façon. --}}
    <div x-show="marital === 'married' || marital === 'cohabiting'" x-cloak class="mt-4 grid gap-4 sm:grid-cols-2">
        <div>
            <label for="spouse_name" class="text-xs font-semibold text-ardoise">Nom du conjoint</label>
            <input id="spouse_name" name="spouse_name" value="{{ old('spouse_name', $tenant->spouse_name) }}"
                   class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
            @error('spouse_name') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="spouse_phone" class="text-xs font-semibold text-ardoise">Contact du conjoint</label>
            <input id="spouse_phone" name="spouse_phone" value="{{ old('spouse_phone', $tenant->spouse_phone) }}"
                   class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
            @error('spouse_phone') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
        </div>
    </div>
                </div>
            </div>
        </section>

        <section class="rounded-meexeo border border-lin-clair bg-papier p-6">
            <h2 class="font-titre text-lg">Activité professionnelle</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="occupation" class="text-xs font-semibold text-ardoise">Fonction / Profession</label>
                    <input id="occupation" name="occupation" value="{{ old('occupation', $tenant->occupation) }}"
                           class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('occupation') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="workplace" class="text-xs font-semibold text-ardoise">Lieu de travail</label>
                    <input id="workplace" name="workplace" value="{{ old('workplace', $tenant->workplace) }}"
                           class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('workplace') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="rounded-meexeo border border-lin-clair bg-papier p-6">
            <h2 class="font-titre text-lg">Contacts</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="phone1" class="text-xs font-semibold text-ardoise">Téléphone 1</label>
                    <input id="phone1" name="phone1" required value="{{ old('phone1', $tenant->phone1) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('phone1') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="phone2" class="text-xs font-semibold text-ardoise">Téléphone 2</label>
                    <input id="phone2" name="phone2" value="{{ old('phone2', $tenant->phone2) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('phone2') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="text-xs font-semibold text-ardoise">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $tenant->email) }}"
                           class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('email') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div></div>
                <div>
                    <label for="emergency_name" class="text-xs font-semibold text-ardoise">Personne à contacter en cas d'urgence</label>
                    <input id="emergency_name" name="emergency_name" value="{{ old('emergency_name', $tenant->emergency_name) }}"
                           class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('emergency_name') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="emergency_phone" class="text-xs font-semibold text-ardoise">Téléphone d'urgence</label>
                    <input id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone', $tenant->emergency_phone) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin px-3 text-sm">
                    @error('emergency_phone') <p class="mt-1 text-xs text-terre">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="notes" class="text-xs font-semibold text-ardoise">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="mt-1.5 w-full rounded-meexeo border border-lin px-3 py-2 text-sm">{{ old('notes', $tenant->notes) }}</textarea>
                </div>
            </div>
        </section>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="min-h-[44px] rounded-meexeo bg-cuivre px-5 text-sm font-semibold text-papier">
                {{ $editing ? 'Enregistrer les modifications' : 'Enregistrer le locataire' }}
            </button>
            <a href="{{ $editing ? route('tenants.show', $tenant) : route('tenants.index') }}"
               class="inline-flex min-h-[44px] items-center rounded-meexeo border border-galet bg-papier px-4 text-sm">Annuler</a>
        </div>
    </form>
</x-layouts.app>
