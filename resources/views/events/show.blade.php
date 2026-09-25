@php($canWrite = in_array(auth()->user()->role, [\App\Enums\Role::Admin, \App\Enums\Role::Manager], true))
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
            </dl>
            @if ($event->notes)
                <p class="mt-5 border-t pt-5 text-sm" style="border-color:var(--color-mc-border-soft);color:var(--color-mc-ink-soft)">{{ $event->notes }}</p>
            @endif
        </div>
    </div>
</x-layouts.app>
