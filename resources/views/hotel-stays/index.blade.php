<x-layouts.app title="Séjours — MASTERCLAYS">
    <x-page-header title="Séjours" subtitle="{{ $stays->total() }} séjour(s)">
        <x-slot:actions>
            <a href="{{ route('hotel-stays.create') }}"
               class="inline-flex min-h-[44px] items-center px-4 text-sm font-semibold"
               style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
                Nouveau séjour
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="mt-6 flex flex-wrap items-end gap-3">
        <div>
            <label for="status" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">Statut</label>
            <select id="status" name="status" class="mt-1.5 min-h-[44px]" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);padding:0 12px;font-size:13px">
                <option value="">Tous</option>
                <option value="reserve" @selected(request('status') === 'reserve')>Réservé</option>
                <option value="en_cours" @selected(request('status') === 'en_cours')>En cours</option>
                <option value="termine" @selected(request('status') === 'termine')>Terminé</option>
                <option value="annule" @selected(request('status') === 'annule')>Annulé</option>
            </select>
        </div>
        <button class="min-h-[44px]" style="border-radius:var(--radius-mc-sm);border:none;background:var(--color-mc-accent);color:var(--color-mc-on-accent);padding:0 18px;font-size:13px;font-weight:700">Filtrer</button>
        @if (request()->hasAny(['status']))
            <a href="{{ route('hotel-stays.index') }}" class="inline-flex min-h-[44px] items-center px-2 text-sm" style="color:var(--color-mc-ink-faint)">Réinitialiser</a>
        @endif
    </form>

    @if ($stays->isEmpty())
        <p class="mt-8 p-8 text-center text-sm" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);color:var(--color-mc-ink-faint)">
            Aucun séjour ne correspond à cette recherche.
        </p>
    @else
        <div class="mt-6 overflow-x-auto" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid var(--color-mc-border);background:var(--color-mc-table-head)">
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">CLIENT</th>
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">CHAMBRE</th>
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">ARRIVÉE</th>
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">DÉPART</th>
                        <th class="px-4 py-3 text-right" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">MONTANT</th>
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">STATUT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stays as $stay)
                        <tr style="border-bottom:1px solid var(--color-mc-border-soft)">
                            <td class="px-4 py-3">
                                <a href="{{ route('hotel-stays.show', $stay) }}" style="font-size:14px;font-weight:700;color:var(--color-mc-ink)">{{ $stay->guest_name }}</a>
                            </td>
                            <td class="px-4 py-3" style="font-size:12px;color:var(--color-mc-ink-soft)">{{ $stay->room->number }}</td>
                            <td class="px-4 py-3" style="font-size:12px;color:var(--color-mc-ink-soft)">{{ $stay->arrival_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3" style="font-size:12px;color:var(--color-mc-ink-soft)">{{ $stay->departure_date->format('d/m/Y') }}</td>
                            <td class="chiffre px-4 py-3 text-right font-semibold">{{ \App\Support\Money::fcfa($stay->total_amount) }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$stay->status->value" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $stays->links() }}</div>
    @endif
</x-layouts.app>
