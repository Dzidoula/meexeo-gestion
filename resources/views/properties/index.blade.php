{{-- resources/views/properties/index.blade.php --}}
@php($canWrite = in_array(auth()->user()->role, [\App\Enums\Role::Admin, \App\Enums\Role::Manager], true))
<x-layouts.app title="Biens — MEEXEO Immobilier">
    <x-page-header title="Biens" subtitle="{{ $properties->total() }} bien(s) au portefeuille">
        <x-slot:actions>
            @if ($canWrite)
                <a href="{{ route('properties.create') }}"
                   class="inline-flex min-h-[44px] items-center"
                   style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);padding:0 16px;font-size:13px;font-weight:700;color:#fff">
                    Ajouter un bien
                </a>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid gap-3.5" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr))">
        <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:16px">
            <div style="font-size:12px;color:var(--color-mc-ink-faint);font-weight:600">Biens loués</div>
            <div style="font-size:20px;font-weight:800;margin-top:4px">{{ $occupiedCount }}</div>
        </div>
        <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:16px">
            <div style="font-size:12px;color:var(--color-mc-ink-faint);font-weight:600">Contrats actifs</div>
            <div style="font-size:20px;font-weight:800;margin-top:4px">{{ $activeLeasesCount }}</div>
        </div>
        <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:16px">
            <div style="font-size:12px;color:var(--color-mc-ink-faint);font-weight:600">Loyers impayés</div>
            <div style="font-size:20px;font-weight:800;margin-top:4px">{{ $unpaidCount }}</div>
        </div>
        <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:16px">
            <div style="font-size:12px;color:var(--color-mc-ink-faint);font-weight:600">Montant dû</div>
            <div style="font-size:20px;font-weight:800;margin-top:4px">{{ \App\Support\Money::fcfa($unpaidAmount) }}</div>
        </div>
    </div>

    <form method="GET" class="mt-6 flex flex-wrap items-end gap-3">
        <div class="min-w-[220px] flex-1">
            <label for="q" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">Recherche</label>
            <input id="q" name="q" value="{{ request('q') }}" placeholder="Titre, quartier, lot…"
                   class="mt-1.5 min-h-[44px] w-full"
                   style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);padding:0 12px;font-size:13px;color:var(--color-mc-ink)">
        </div>
        <div>
            <label for="commune" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">Commune</label>
            <select id="commune" name="commune" class="mt-1.5 min-h-[44px]"
                    style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);padding:0 12px;font-size:13px;color:var(--color-mc-ink)">
                <option value="">Toutes</option>
                @foreach ($communes as $commune)
                    <option value="{{ $commune }}" @selected(request('commune') === $commune)>{{ $commune }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="type" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">Type</label>
            <select id="type" name="type" class="mt-1.5 min-h-[44px]"
                    style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);padding:0 12px;font-size:13px;color:var(--color-mc-ink)">
                <option value="">Tous</option>
                @foreach ($types as $value => $label)
                    <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="status" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">État</label>
            <select id="status" name="status" class="mt-1.5 min-h-[44px]"
                    style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);padding:0 12px;font-size:13px;color:var(--color-mc-ink)">
                <option value="">Tous</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button class="min-h-[44px]" style="border-radius:var(--radius-mc-sm);border:none;background:var(--color-mc-accent);color:#fff;padding:0 18px;font-size:13px;font-weight:700">Filtrer</button>
        @if (request()->hasAny(['q', 'commune', 'type', 'status', 'city']))
            <a href="{{ route('properties.index') }}" class="inline-flex min-h-[44px] items-center px-2" style="font-size:13px;color:var(--color-mc-ink-faint)">Réinitialiser</a>
        @endif
    </form>

    @if ($properties->isEmpty())
        <p class="mt-8 text-center" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);padding:32px;font-size:13px;color:var(--color-mc-ink-soft)">
            Aucun bien ne correspond à cette recherche.
        </p>
    @else
        {{-- Tableau au-delà de 1024 px --}}
        <div class="mt-6 hidden overflow-x-auto lg:block" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid var(--color-mc-border);background:var(--color-mc-table-head)">
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">RÉFÉRENCE</th>
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">BIEN</th>
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">LOCALISATION</th>
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">TYPE</th>
                        <th class="px-4 py-3 text-right" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">LOYER</th>
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">LOCATAIRE ACTUEL</th>
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">ÉTAT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($properties as $property)
                        <tr style="border-bottom:1px solid var(--color-mc-border-soft)">
                            <td class="px-4 py-3" style="font-size:12px;color:var(--color-mc-ink-faint)">{{ $property->reference }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('properties.show', $property) }}" style="font-size:14px;font-weight:700;color:var(--color-mc-ink)">
                                    {{ $property->title }}
                                </a>
                            </td>
                            <td class="px-4 py-3" style="font-size:12px;color:var(--color-mc-ink-soft)">{{ $property->full_address }}</td>
                            <td class="px-4 py-3" style="font-size:12px;color:var(--color-mc-ink-soft)">{{ $property->type->label() }}</td>
                            <td class="px-4 py-3 text-right" style="font-size:13px;font-weight:700;color:var(--color-mc-ink)">{{ \App\Support\Money::fcfa($property->monthly_rent) }}</td>
                            <td class="px-4 py-3" style="font-size:13px;color:var(--color-mc-ink)">{{ $property->activeLease?->tenant?->full_name ?? '—' }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$property->status->value" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Cartes empilées sur téléphone : le loyer et l'état restent visibles sans défilement latéral --}}
        <div class="mt-6 space-y-3 lg:hidden">
            @foreach ($properties as $property)
                <a href="{{ route('properties.show', $property) }}" class="block" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);padding:16px">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p style="font-size:11px;color:var(--color-mc-ink-faint)">{{ $property->reference }}</p>
                            <p style="font-size:16px;font-weight:700;color:var(--color-mc-ink)">{{ $property->title }}</p>
                            <p class="mt-0.5" style="font-size:12px;color:var(--color-mc-ink-soft)">{{ $property->full_address }}</p>
                        </div>
                        <x-status-badge :status="$property->status->value" />
                    </div>
                    <p class="mt-3" style="font-size:12px;color:var(--color-mc-ink-soft)">Locataire : {{ $property->activeLease?->tenant?->full_name ?? '—' }}</p>
                    <p class="mt-1" style="font-size:16px;font-weight:700;color:var(--color-mc-ink)">{{ \App\Support\Money::fcfa($property->monthly_rent) }}</p>
                </a>
            @endforeach
        </div>

        <div class="mt-6">{{ $properties->links() }}</div>
    @endif
</x-layouts.app>
