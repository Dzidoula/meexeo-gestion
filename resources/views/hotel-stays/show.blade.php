@php($canWrite = in_array(auth()->user()->role, [\App\Enums\Role::Admin, \App\Enums\Role::Manager], true))
@php($balance = $stay->total_amount - $stay->payments->sum('amount'))
<x-layouts.app :title="'Séjour de '.$stay->guest_name.' — MASTERCLAYS'">
    <x-page-header :title="'Séjour de '.$stay->guest_name" :subtitle="'Chambre '.$stay->room->number">
        <x-slot:actions>
            @if ($canWrite)
                @if ($stay->status->value === 'reserve')
                    <form method="POST" action="{{ route('hotel-stays.check-in', $stay) }}">
                        @csrf @method('PATCH')
                        <button class="inline-flex min-h-[44px] items-center px-4 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-success);color:#fff">Enregistrer l'arrivée</button>
                    </form>
                @endif
                @if ($stay->status->value === 'en_cours')
                    <form method="POST" action="{{ route('hotel-stays.check-out', $stay) }}">
                        @csrf @method('PATCH')
                        <button class="inline-flex min-h-[44px] items-center px-4 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">Enregistrer le départ</button>
                    </form>
                @endif
                @if (! in_array($stay->status->value, ['termine', 'annule'], true))
                    <form method="POST" action="{{ route('hotel-stays.cancel', $stay) }}">
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
                <div><dt style="color:var(--color-mc-ink-faint)">Statut</dt><dd class="mt-1"><x-status-badge :status="$stay->status->value" /></dd></div>
                <div><dt style="color:var(--color-mc-ink-faint)">Téléphone</dt><dd class="mt-1">{{ $stay->guest_phone }}</dd></div>
                <div><dt style="color:var(--color-mc-ink-faint)">Arrivée prévue</dt><dd class="mt-1">{{ $stay->arrival_date->format('d/m/Y') }}</dd></div>
                <div><dt style="color:var(--color-mc-ink-faint)">Départ prévu</dt><dd class="mt-1">{{ $stay->departure_date->format('d/m/Y') }}</dd></div>
                <div><dt style="color:var(--color-mc-ink-faint)">Montant total</dt><dd class="chiffre mt-1 font-semibold">{{ \App\Support\Money::fcfa($stay->total_amount) }}</dd></div>
                <div><dt style="color:var(--color-mc-ink-faint)">Solde restant</dt><dd class="chiffre mt-1 font-semibold">{{ \App\Support\Money::fcfa($balance) }}</dd></div>
            </dl>
            @if ($stay->notes)
                <p class="mt-5 border-t pt-5 text-sm" style="border-color:var(--color-mc-border-soft);color:var(--color-mc-ink-soft)">{{ $stay->notes }}</p>
            @endif
        </div>

        <div class="p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
            <h3 class="font-titre text-lg">Paiements</h3>
            @if ($stay->payments->isEmpty())
                <p class="mt-4 text-sm" style="color:var(--color-mc-ink-faint)">Aucun paiement enregistré.</p>
            @else
                <ul class="mt-4 divide-y divide-[var(--color-mc-border-soft)]">
                    @foreach ($stay->payments as $payment)
                        <li class="flex items-center justify-between gap-3 py-3 text-sm">
                            <span>{{ $payment->paid_on->format('d/m/Y') }} @if ($payment->method) — {{ $payment->method }} @endif</span>
                            <span class="chiffre font-semibold">{{ \App\Support\Money::fcfa($payment->amount) }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($canWrite)
                <form method="POST" action="{{ route('hotel-payments.store', $stay) }}" class="mt-5 flex flex-wrap items-end gap-3 border-t pt-5" style="border-color:var(--color-mc-border-soft)">
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
