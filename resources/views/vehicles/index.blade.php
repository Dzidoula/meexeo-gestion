@php($canWrite = in_array(auth()->user()->role, [\App\Enums\Role::Admin, \App\Enums\Role::Manager], true))
<x-layouts.app title="Catalogue véhicules — SONOR LOCATION">
    <x-page-header title="Catalogue véhicules" subtitle="{{ $vehiclesTotal }} véhicule(s)">
        <x-slot:actions>
            @if ($canWrite)
                <a href="{{ route('vehicles.create') }}"
                   class="inline-flex min-h-[44px] items-center px-4 text-sm font-semibold"
                   style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
                    Ajouter un véhicule
                </a>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid gap-3.5" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr))">
        <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:16px">
            <div style="font-size:12px;color:var(--color-mc-ink-faint);font-weight:600">Véhicules actifs</div>
            <div style="font-size:20px;font-weight:800;margin-top:4px">{{ $vehiclesTotal }}</div>
        </div>
        <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:16px">
            <div style="font-size:12px;color:var(--color-mc-ink-faint);font-weight:600">Valeur du stock</div>
            <div style="font-size:20px;font-weight:800;margin-top:4px">{{ \App\Support\Money::fcfa($stockValue) }}</div>
        </div>
        <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:16px">
            <div style="font-size:12px;color:var(--color-mc-ink-faint);font-weight:600">Véhicules épuisés</div>
            <div style="font-size:20px;font-weight:800;margin-top:4px">{{ $outOfStockCount }}</div>
        </div>
    </div>

    <form method="GET" class="mt-6 flex flex-wrap items-end gap-3">
        <div class="min-w-[220px] flex-1">
            <label for="q" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">Recherche</label>
            <input id="q" name="q" value="{{ request('q') }}" placeholder="Marque, modèle…"
                   class="mt-1.5 min-h-[44px] w-full" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);padding:0 12px;font-size:13px">
        </div>
        <div>
            <label for="type" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">Type</label>
            <select id="type" name="type" class="mt-1.5 min-h-[44px]" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);padding:0 12px;font-size:13px">
                <option value="">Tous</option>
                @foreach ($vehicleTypes as $type)
                    <option value="{{ $type->id }}" @selected(request('type') == $type->id)>{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="status" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">Statut</label>
            <select id="status" name="status" class="mt-1.5 min-h-[44px]" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);padding:0 12px;font-size:13px">
                <option value="">Tous</option>
                <option value="disponible" @selected(request('status') === 'disponible')>Disponible</option>
                <option value="epuise" @selected(request('status') === 'epuise')>Épuisé</option>
            </select>
        </div>
        <button class="min-h-[44px]" style="border-radius:var(--radius-mc-sm);border:none;background:var(--color-mc-accent);color:var(--color-mc-on-accent);padding:0 18px;font-size:13px;font-weight:700">Filtrer</button>
        @if (request()->hasAny(['q', 'type', 'status']))
            <a href="{{ route('vehicles.index') }}" class="inline-flex min-h-[44px] items-center px-2 text-sm" style="color:var(--color-mc-ink-faint)">Réinitialiser</a>
        @endif
    </form>

    @if ($vehicles->isEmpty())
        <p class="mt-8 p-8 text-center text-sm" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);color:var(--color-mc-ink-faint)">
            Aucun véhicule ne correspond à cette recherche.
        </p>
    @else
        <div class="mt-6 overflow-x-auto" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid var(--color-mc-border);background:var(--color-mc-table-head)">
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">VÉHICULE</th>
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">TYPE</th>
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">CARBURANT</th>
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">TRANSMISSION</th>
                        <th class="px-4 py-3 text-right" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">PRIX</th>
                        <th class="px-4 py-3 text-right" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">STOCK</th>
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">STATUT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vehicles as $vehicle)
                        <tr style="border-bottom:1px solid var(--color-mc-border-soft)">
                            <td class="px-4 py-3">
                                <a href="{{ route('vehicles.show', $vehicle) }}" style="font-size:14px;font-weight:700;color:var(--color-mc-ink)">{{ $vehicle->brand }} {{ $vehicle->model }}</a>
                            </td>
                            <td class="px-4 py-3" style="font-size:12px;color:var(--color-mc-ink-soft)">{{ $vehicle->vehicleType->name }}</td>
                            <td class="px-4 py-3" style="font-size:12px;color:var(--color-mc-ink-soft)">{{ $vehicle->fuel_type->label() }}</td>
                            <td class="px-4 py-3" style="font-size:12px;color:var(--color-mc-ink-soft)">{{ $vehicle->transmission->label() }}</td>
                            <td class="chiffre px-4 py-3 text-right font-semibold">{{ \App\Support\Money::fcfa($vehicle->price) }}</td>
                            <td class="chiffre px-4 py-3 text-right">{{ $vehicle->stock_quantity }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="\App\Support\VehicleStockStatus::for($vehicle->stock_quantity)" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $vehicles->links() }}</div>
    @endif
</x-layouts.app>
