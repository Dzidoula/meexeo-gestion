<x-layouts.app title="Enregistrer un paiement — MEEXEO">
    <x-page-header title="Enregistrer un paiement" subtitle="Chaque paiement doit être accompagné d'une preuve." />

    <div class="mt-6 grid gap-5 lg:grid-cols-[1.4fr_1fr]" x-data="{
        montant: {{ $lease?->monthly_rent ?? 0 }},
        loyer: {{ $lease?->monthly_rent ?? 0 }},
        methode: 'cash',
        libellesReference: @js(collect($methods)->mapWithKeys(fn ($label, $value) => [$value => \App\Enums\PaymentMethod::from($value)->referenceLabel()])),
        preuveJointe: false,
    }">
        <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <section class="p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                <h2 class="font-titre text-lg">1. Locataire et bien</h2>
                <div class="mt-4">
                    <label for="lease_picker" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Bail concerné</label>
                    <select id="lease_picker" name="lease" onchange="window.location.href = '{{ route('payments.create') }}' + (this.value ? '?lease=' + this.value : '')"
                            class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                        <option value="">Choisir un bail…</option>
                        @foreach ($leases as $option)
                            <option value="{{ $option->id }}" @selected($lease?->id === $option->id)>
                                {{ $option->tenant->full_name }} — {{ $option->property->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('lease_id') <p class="mt-2 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </section>

            @if ($lease)
                <input type="hidden" name="lease_id" value="{{ $lease->id }}">

                <section class="p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                    <h2 class="font-titre text-lg">2. Mois concerné</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        @php($cursor = $lease->start_date->copy()->startOfMonth())
                        @php($today = now())
                        @while ($cursor->lte($today))
                            {{-- `month` est casté en Carbon : comparer avec une chaîne ne matcherait jamais. --}}
                            @php($paidThisMonth = $lease->payments->filter(fn ($p) => $p->month->isSameMonth($cursor))->sum('amount'))
                            @php($status = \App\Support\PaymentMonthStatus::for($lease->monthly_rent, $paidThisMonth, $cursor, $lease->due_day, $today))
                            <label class="flex min-h-[44px] cursor-pointer items-center justify-between gap-2 rounded-[var(--radius-mc-sm)] border border-[var(--color-mc-border)] px-3 py-2 text-sm has-[:checked]:border-[var(--color-mc-accent)]">
                                <span>
                                    <input type="radio" name="month" value="{{ $cursor->toDateString() }}" class="mr-2" @checked($cursor->isSameMonth($today))>
                                    {{ ucfirst($cursor->translatedFormat('F Y')) }}
                                </span>
                                @if ($status)
                                    <x-status-badge :status="$status" />
                                @endif
                            </label>
                            @php($cursor->addMonth())
                        @endwhile
                    </div>
                    @error('month') <p class="mt-2 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </section>

                <section class="p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                    <h2 class="font-titre text-lg">3. Montant et date</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="amount" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Montant encaissé (FCFA)</label>
                            <input id="amount" name="amount" type="number" step="1" min="1" required x-model="montant"
                                   class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                            @error('amount') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="paid_on" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Date de paiement</label>
                            <input id="paid_on" name="paid_on" type="date" required value="{{ old('paid_on', now()->toDateString()) }}"
                                   class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                            @error('paid_on') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <section class="p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                    <h2 class="font-titre text-lg">4. Mode de paiement</h2>
                    <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-4">
                        @foreach ($methods as $value => $label)
                            <label class="flex min-h-[44px] cursor-pointer items-center justify-center rounded-[var(--radius-mc-sm)] border border-[var(--color-mc-border)] px-3 text-center text-sm has-[:checked]:border-[var(--color-mc-accent)]">
                                <input type="radio" name="method" value="{{ $value }}" x-model="methode" class="sr-only">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    @error('method') <p class="mt-2 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror

                    <div class="mt-4" x-show="libellesReference[methode]" x-cloak>
                        <label for="reference" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)" x-text="libellesReference[methode]"></label>
                        <input id="reference" name="reference" class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    </div>
                </section>

                <section class="p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                    <h2 class="font-titre text-lg">5. Preuve de paiement <span style="color:var(--color-mc-danger)">— OBLIGATOIRE</span></h2>
                    <input type="file" name="proof" accept="image/*,.pdf" required class="mt-3 text-sm"
                           @change="preuveJointe = $event.target.files.length > 0">
                    @error('proof') <p class="mt-2 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </section>

                <button type="submit" :disabled="! preuveJointe"
                        class="min-h-[44px] w-full px-5 text-sm font-semibold disabled:cursor-not-allowed disabled:opacity-50"
                        style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
                    <span x-show="preuveJointe">Enregistrer le paiement</span>
                    <span x-show="! preuveJointe" x-cloak>Joignez une preuve de paiement pour valider</span>
                </button>
            @endif
        </form>

        @if ($lease)
            <aside class="h-fit p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                <h2 class="font-titre text-lg">Récapitulatif</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between"><dt>Loyer dû</dt><dd class="chiffre" x-text="loyer.toLocaleString('fr-FR') + ' FCFA'"></dd></div>
                    <div class="flex justify-between"><dt>Montant encaissé</dt><dd class="chiffre" x-text="montant.toLocaleString('fr-FR') + ' FCFA'"></dd></div>
                    <div class="flex justify-between border-t pt-3 font-semibold" style="border-color:var(--color-mc-border-soft)">
                        <dt>Reste à payer</dt>
                        <dd class="chiffre" x-text="Math.max(loyer - montant, 0).toLocaleString('fr-FR') + ' FCFA'"></dd>
                    </div>
                </dl>
            </aside>
        @endif
    </div>
</x-layouts.app>
