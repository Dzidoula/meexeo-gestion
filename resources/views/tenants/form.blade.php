{{-- resources/views/tenants/form.blade.php --}}
@php($editing = $tenant->exists)
<x-layouts.app :title="($editing ? 'Modifier la fiche' : 'Nouveau locataire').' — MEEXEO'">
    <x-page-header :title="$editing ? 'Modifier la fiche locataire' : 'Nouveau locataire'"
                   :subtitle="$editing ? $tenant->reference : 'Renseignez l\'identité, l\'activité et les contacts.'" />

    <form method="POST" action="{{ $editing ? route('tenants.update', $tenant) : route('tenants.store') }}"
          class="mt-6 max-w-3xl space-y-6">
        @csrf
        @if ($editing) @method('PUT') @endif

        <section style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:24px">
            <h2 class="font-titre text-lg">Identité</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="last_name" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Nom</label>
                    <input id="last_name" name="last_name" required value="{{ old('last_name', $tenant->last_name) }}"
                           class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('last_name') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="first_names" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Prénoms</label>
                    <input id="first_names" name="first_names" required value="{{ old('first_names', $tenant->first_names) }}"
                           class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('first_names') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="birth_date" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Date de naissance</label>
                    <input id="birth_date" name="birth_date" type="date"
                           value="{{ old('birth_date', $tenant->birth_date?->format('Y-m-d')) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('birth_date') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="id_number" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">N° CNI / Passeport</label>
                    <input id="id_number" name="id_number" value="{{ old('id_number', $tenant->id_number) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('id_number') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Statut</label>
                    <select id="status" name="status" class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $tenant->status?->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div x-data="{ marital: '{{ old('marital_status', $tenant->marital_status?->value) }}' }" class="sm:col-span-2">
                    <label for="marital_status" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Situation matrimoniale</label>
                    <select id="marital_status" name="marital_status" x-model="marital"
                            class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                        <option value="">Non renseignée</option>
                        @foreach ($maritalStatuses as $value => $label)
                            <option value="{{ $value }}" @selected(old('marital_status', $tenant->marital_status?->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>

                    {{-- Révélé pour un locataire marié ou en couple : le serveur revalide de toute façon. --}}
                    <div x-show="marital === 'married' || marital === 'cohabiting'" x-cloak class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="spouse_name" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Nom du conjoint</label>
                            <input id="spouse_name" name="spouse_name" value="{{ old('spouse_name', $tenant->spouse_name) }}"
                                   class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                            @error('spouse_name') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="spouse_phone" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Contact du conjoint</label>
                            <input id="spouse_phone" name="spouse_phone" value="{{ old('spouse_phone', $tenant->spouse_phone) }}"
                                   class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                            @error('spouse_phone') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:24px">
            <h2 class="font-titre text-lg">Activité professionnelle</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="occupation" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Fonction / Profession</label>
                    <input id="occupation" name="occupation" value="{{ old('occupation', $tenant->occupation) }}"
                           class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('occupation') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="workplace" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Lieu de travail</label>
                    <input id="workplace" name="workplace" value="{{ old('workplace', $tenant->workplace) }}"
                           class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('workplace') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:24px">
            <h2 class="font-titre text-lg">Contacts</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="phone1" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Téléphone 1</label>
                    <input id="phone1" name="phone1" required value="{{ old('phone1', $tenant->phone1) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('phone1') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="phone2" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Téléphone 2</label>
                    <input id="phone2" name="phone2" value="{{ old('phone2', $tenant->phone2) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('phone2') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $tenant->email) }}"
                           class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('email') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div></div>
                <div>
                    <label for="emergency_name" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Personne à contacter en cas d'urgence</label>
                    <input id="emergency_name" name="emergency_name" value="{{ old('emergency_name', $tenant->emergency_name) }}"
                           class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('emergency_name') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="emergency_phone" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Téléphone d'urgence</label>
                    <input id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone', $tenant->emergency_phone) }}"
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('emergency_phone') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="notes" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="mt-1.5 w-full px-3 py-2 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">{{ old('notes', $tenant->notes) }}</textarea>
                </div>
            </div>
        </section>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="min-h-[44px] px-5 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
                {{ $editing ? 'Enregistrer les modifications' : 'Enregistrer le locataire' }}
            </button>
            <a href="{{ $editing ? route('tenants.show', $tenant) : route('tenants.index') }}"
               class="inline-flex min-h-[44px] items-center px-4 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);color:var(--color-mc-ink)">Annuler</a>
        </div>
    </form>
</x-layouts.app>
