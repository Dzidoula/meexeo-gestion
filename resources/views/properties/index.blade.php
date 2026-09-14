{{-- resources/views/properties/index.blade.php --}}
@php($canWrite = in_array(auth()->user()->role, [\App\Enums\Role::Admin, \App\Enums\Role::Manager], true))
<x-layouts.app title="Biens — MEEXEO Immobilier">
    <x-page-header title="Biens" subtitle="{{ $properties->total() }} bien(s) au portefeuille">
        <x-slot:actions>
            @if ($canWrite)
                <a href="{{ route('properties.create') }}"
                   class="inline-flex min-h-[44px] items-center rounded-meexeo bg-cuivre px-4 text-sm font-semibold text-papier">
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
            <label for="q" class="text-xs font-semibold text-ardoise">Recherche</label>
            <input id="q" name="q" value="{{ request('q') }}" placeholder="Titre, quartier, lot…"
                   class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin bg-papier px-3 text-sm">
        </div>
        <div>
            <label for="commune" class="text-xs font-semibold text-ardoise">Commune</label>
            <select id="commune" name="commune" class="mt-1.5 min-h-[44px] rounded-meexeo border border-lin bg-papier px-3 text-sm">
                <option value="">Toutes</option>
                @foreach ($communes as $commune)
                    <option value="{{ $commune }}" @selected(request('commune') === $commune)>{{ $commune }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="type" class="text-xs font-semibold text-ardoise">Type</label>
            <select id="type" name="type" class="mt-1.5 min-h-[44px] rounded-meexeo border border-lin bg-papier px-3 text-sm">
                <option value="">Tous</option>
                @foreach ($types as $value => $label)
                    <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="status" class="text-xs font-semibold text-ardoise">État</label>
            <select id="status" name="status" class="mt-1.5 min-h-[44px] rounded-meexeo border border-lin bg-papier px-3 text-sm">
                <option value="">Tous</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button class="min-h-[44px] rounded-meexeo bg-lagune px-4 text-sm font-semibold text-sable">Filtrer</button>
        @if (request()->hasAny(['q', 'commune', 'type', 'status', 'city']))
            <a href="{{ route('properties.index') }}" class="inline-flex min-h-[44px] items-center px-2 text-sm text-acier">Réinitialiser</a>
        @endif
    </form>

    @if ($properties->isEmpty())
        <p class="mt-8 rounded-meexeo border border-lin-clair bg-papier p-8 text-center text-sm text-brume">
            Aucun bien ne correspond à cette recherche.
        </p>
    @else
        {{-- Tableau au-delà de 1024 px --}}
        <div class="mt-6 hidden overflow-x-auto rounded-meexeo border border-lin-clair bg-papier lg:block">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-lin-clair text-left">
                        <th class="surtitre px-4 py-3">Référence</th>
                        <th class="surtitre px-4 py-3">Bien</th>
                        <th class="surtitre px-4 py-3">Localisation</th>
                        <th class="surtitre px-4 py-3">Type</th>
                        <th class="surtitre px-4 py-3 text-right">Loyer</th>
                        <th class="surtitre px-4 py-3">État</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($properties as $property)
                        <tr class="border-b border-lin-pale last:border-0">
                            <td class="chiffre px-4 py-3 text-xs text-brume">{{ $property->reference }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('properties.show', $property) }}" class="font-titre text-base text-lagune hover:text-cuivre">
                                    {{ $property->title }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-xs text-ardoise">{{ $property->full_address }}</td>
                            <td class="px-4 py-3 text-xs text-ardoise">{{ $property->type->label() }}</td>
                            <td class="chiffre px-4 py-3 text-right font-semibold">{{ \App\Support\Money::fcfa($property->monthly_rent) }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$property->status->value" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Cartes empilées sur téléphone : le loyer et l'état restent visibles sans défilement latéral --}}
        <div class="mt-6 space-y-3 lg:hidden">
            @foreach ($properties as $property)
                <a href="{{ route('properties.show', $property) }}" class="block rounded-meexeo border border-lin-clair bg-papier p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="chiffre text-[11px] text-brume">{{ $property->reference }}</p>
                            <p class="font-titre text-lg">{{ $property->title }}</p>
                            <p class="mt-0.5 text-xs text-ardoise">{{ $property->full_address }}</p>
                        </div>
                        <x-status-badge :status="$property->status->value" />
                    </div>
                    <p class="chiffre mt-3 text-lg font-semibold">{{ \App\Support\Money::fcfa($property->monthly_rent) }}</p>
                </a>
            @endforeach
        </div>

        <div class="mt-6">{{ $properties->links() }}</div>
    @endif
</x-layouts.app>
