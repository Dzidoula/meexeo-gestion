@php($canWrite = in_array(auth()->user()->role, [\App\Enums\Role::Admin, \App\Enums\Role::Manager], true))
@php($balance = $event->budget_total - $event->deposit_amount - $event->payments->sum('amount'))
<x-layouts.app :title="'Événement de '.$event->client_name.' — MASTERCLAYS'">
    <x-page-header :title="'Événement de '.$event->client_name" :subtitle="$event->venue">
        <x-slot:actions>
            @if ($canWrite)
                @if ($event->status->value === 'en_attente')
                    <form method="POST" action="{{ route('events.confirm', $event) }}">
                        @csrf @method('PATCH')
                        <button class="inline-flex min-h-[44px] items-center px-4 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-success);color:#fff">Confirmer</button>
                    </form>
                @endif
                @if ($event->status->value === 'confirme')
                    <form method="POST" action="{{ route('events.complete', $event) }}">
                        @csrf @method('PATCH')
                        <button class="inline-flex min-h-[44px] items-center px-4 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">Marquer comme terminé</button>
                    </form>
                @endif
                @if (! in_array($event->status->value, ['termine', 'annule'], true))
                    <a href="{{ route('events.edit', $event) }}" class="inline-flex min-h-[44px] items-center px-4 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);color:var(--color-mc-ink)">Modifier</a>
                    <form method="POST" action="{{ route('events.cancel', $event) }}">
                        @csrf @method('PATCH')
                        <button class="inline-flex min-h-[44px] items-center px-4 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-danger);color:var(--color-mc-danger)">Annuler</button>
                    </form>
                @endif
            @endif
        </x-slot:actions>
    </x-page-header>

    @if (session('status'))
        <p class="mt-4 px-4 py-3 text-sm" style="border-radius:var(--radius-mc);border:1px solid rgba(22,163,74,.35);background:rgba(22,163,74,.08);color:var(--color-mc-success)">{{ session('status') }}</p>
    @endif
    @if (session('error'))
        <p class="mt-4 px-4 py-3 text-sm" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-danger);background:rgba(220,38,38,.08);color:var(--color-mc-danger)">{{ session('error') }}</p>
    @endif

    <div class="mt-6 grid gap-5 lg:grid-cols-2">
        <div class="p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div><dt style="color:var(--color-mc-ink-faint)">Statut</dt><dd class="mt-1"><x-status-badge :status="$event->status->value" /></dd></div>
                <div><dt style="color:var(--color-mc-ink-faint)">Téléphone</dt><dd class="mt-1">{{ $event->client_phone }}</dd></div>
                <div><dt style="color:var(--color-mc-ink-faint)">Début</dt><dd class="mt-1">{{ $event->start_date->format('d/m/Y') }}</dd></div>
                <div><dt style="color:var(--color-mc-ink-faint)">Fin</dt><dd class="mt-1">{{ $event->end_date->format('d/m/Y') }}</dd></div>
                <div><dt style="color:var(--color-mc-ink-faint)">Budget total</dt><dd class="chiffre mt-1 font-semibold">{{ \App\Support\Money::fcfa($event->budget_total) }}</dd></div>
                <div><dt style="color:var(--color-mc-ink-faint)">Acompte versé</dt><dd class="chiffre mt-1 font-semibold">{{ \App\Support\Money::fcfa($event->deposit_amount) }}</dd></div>
                <div><dt style="color:var(--color-mc-ink-faint)">Solde restant</dt><dd class="chiffre mt-1 font-semibold">{{ \App\Support\Money::fcfa($balance) }}</dd></div>
            </dl>
            @if ($event->notes)
                <p class="mt-5 border-t pt-5 text-sm" style="border-color:var(--color-mc-border-soft);color:var(--color-mc-ink-soft)">{{ $event->notes }}</p>
            @endif
        </div>

        <div class="p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
            <h3 class="font-titre text-lg">Équipements réservés</h3>
            @if ($event->reservations->isEmpty())
                <p class="mt-4 text-sm" style="color:var(--color-mc-ink-faint)">Aucun équipement réservé.</p>
            @else
                <ul class="mt-4 divide-y divide-[var(--color-mc-border-soft)]">
                    @foreach ($event->reservations as $reservation)
                        <li class="flex items-center justify-between gap-3 py-3 text-sm">
                            <span>{{ $reservation->equipment->name }} &times; {{ $reservation->quantity }}</span>
                            @if ($canWrite && ! in_array($event->status->value, ['termine', 'annule'], true))
                                <form method="POST" action="{{ route('event-equipment-reservations.destroy', [$event, $reservation]) }}">
                                    @csrf @method('DELETE')
                                    <button class="text-sm" style="color:var(--color-mc-danger)">Retirer</button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($canWrite && ! in_array($event->status->value, ['termine', 'annule'], true))
                <form method="POST" action="{{ route('event-equipment-reservations.store', $event) }}" class="mt-5 flex flex-wrap items-end gap-3 border-t pt-5" style="border-color:var(--color-mc-border-soft)">
                    @csrf
                    <div>
                        <label for="equipment_id" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Équipement</label>
                        <select id="equipment_id" name="equipment_id" required class="mt-1.5 min-h-[44px] px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                            @foreach (\App\Models\Equipment::orderBy('name')->get() as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="quantity" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Quantité</label>
                        <input id="quantity" name="quantity" type="number" step="1" min="1" required
                               class="chiffre mt-1.5 min-h-[44px] w-24 px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    </div>
                    <button class="min-h-[44px] px-4 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">Réserver</button>
                </form>
                @error('equipment_id') <p class="mt-2 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            @endif
        </div>

        <div class="p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
            <h3 class="font-titre text-lg">Paiements</h3>
            @if ($event->payments->isEmpty())
                <p class="mt-4 text-sm" style="color:var(--color-mc-ink-faint)">Aucun paiement enregistré.</p>
            @else
                <ul class="mt-4 divide-y divide-[var(--color-mc-border-soft)]">
                    @foreach ($event->payments as $payment)
                        <li class="flex items-center justify-between gap-3 py-3 text-sm">
                            <span>{{ $payment->paid_on->format('d/m/Y') }} @if ($payment->method) — {{ $payment->method }} @endif</span>
                            <span class="chiffre font-semibold">{{ \App\Support\Money::fcfa($payment->amount) }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($canWrite)
                <form method="POST" action="{{ route('event-payments.store', $event) }}" class="mt-5 flex flex-wrap items-end gap-3 border-t pt-5" style="border-color:var(--color-mc-border-soft)">
                    @csrf
                    <div>
                        <label for="amount" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Montant</label>
                        <input id="amount" name="amount" type="number" step="1" min="0" required
                               class="chiffre mt-1.5 min-h-[44px] w-32 px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    </div>
                    <div>
                        <label for="paid_on" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Date</label>
                        <input id="paid_on" name="paid_on" type="date" value="{{ now()->toDateString() }}" required
                               class="mt-1.5 min-h-[44px] px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    </div>
                    <div>
                        <label for="method" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Mode</label>
                        <input id="method" name="method" placeholder="Espèces, Mobile Money…"
                               class="mt-1.5 min-h-[44px] px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    </div>
                    <button class="min-h-[44px] px-4 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">Ajouter</button>
                </form>
                @error('amount') <p class="mt-2 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>
</x-layouts.app>
