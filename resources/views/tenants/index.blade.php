{{-- resources/views/tenants/index.blade.php --}}
@php($canWrite = in_array(auth()->user()->role, [\App\Enums\Role::Admin, \App\Enums\Role::Manager], true))
<x-layouts.app title="Locataires — MEEXEO Immobilier">
    <x-page-header title="Locataires" subtitle="{{ $tenants->total() }} locataire(s) enregistré(s)">
        <x-slot:actions>
            @if ($canWrite)
                <a href="{{ route('tenants.create') }}"
                   class="inline-flex min-h-[44px] items-center"
                   style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);padding:0 16px;font-size:13px;font-weight:700;color:var(--color-mc-on-accent)">
                    Ajouter un locataire
                </a>
            @endif
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="mt-6 flex flex-wrap items-end gap-3">
        <div class="min-w-[220px] flex-1">
            <label for="q" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">Recherche</label>
            <input id="q" name="q" value="{{ request('q') }}" placeholder="Nom, téléphone, CNI…"
                   class="mt-1.5 min-h-[44px] w-full"
                   style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);padding:0 12px;font-size:13px;color:var(--color-mc-ink)">
        </div>
        <div>
            <label for="status" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">Statut</label>
            <select id="status" name="status" class="mt-1.5 min-h-[44px]"
                    style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);padding:0 12px;font-size:13px;color:var(--color-mc-ink)">
                <option value="">Tous</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button class="min-h-[44px]" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);padding:0 16px;font-size:13px;font-weight:700;color:var(--color-mc-on-accent)">Filtrer</button>
    </form>

    @if ($tenants->isEmpty())
        <p class="mt-8 p-8 text-center text-sm" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);color:var(--color-mc-ink-faint)">
            Aucun locataire ne correspond à cette recherche.
        </p>
    @else
        <div class="mt-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
            @foreach ($tenants as $tenant)
                <div class="flex flex-wrap items-center justify-between gap-4 p-4" style="{{ !$loop->first ? 'border-top:1px solid var(--color-mc-border-soft)' : '' }}">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full font-titre text-sm" style="background:var(--color-mc-canvas);color:var(--color-mc-ink)">
                            {{ $tenant->initials }}
                        </span>
                        <div>
                            <a href="{{ route('tenants.show', $tenant) }}" class="font-titre text-base" style="color:var(--color-mc-ink)">
                                {{ $tenant->full_name }}
                            </a>
                            <p class="chiffre text-[11px]" style="color:var(--color-mc-ink-faint)">{{ $tenant->reference }} · {{ $tenant->phone1 }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="hidden text-xs sm:block" style="color:var(--color-mc-ink-soft)">{{ $tenant->occupation ?? '—' }}</span>
                        <x-status-badge :status="$tenant->status->value" />
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $tenants->links() }}</div>
    @endif
</x-layouts.app>
