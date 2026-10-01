@php($canWrite = in_array(auth()->user()->role, [\App\Enums\Role::Admin, \App\Enums\Role::Manager], true))
<x-layouts.app title="Équipements — MASTERCLAYS">
    <x-page-header title="Équipements" subtitle="{{ $equipment->count() }} équipement(s)">
        <x-slot:actions>
            @if ($canWrite)
                <a href="{{ route('equipment.create') }}"
                   class="inline-flex min-h-[44px] items-center px-4 text-sm font-semibold"
                   style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
                    Ajouter un équipement
                </a>
            @endif
            <a href="{{ route('events.index') }}"
               class="inline-flex min-h-[44px] items-center px-2 text-sm" style="color:var(--color-mc-ink-faint)">Événements</a>
        </x-slot:actions>
    </x-page-header>

    @if (session('status'))
        <p class="mt-4 px-4 py-3 text-sm" style="border-radius:var(--radius-mc);border:1px solid rgba(22,163,74,.35);background:rgba(22,163,74,.08);color:var(--color-mc-success)">{{ session('status') }}</p>
    @endif
    @if (session('error'))
        <p class="mt-4 px-4 py-3 text-sm" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-danger);background:rgba(220,38,38,.08);color:var(--color-mc-danger)">{{ session('error') }}</p>
    @endif

    @if ($equipment->isEmpty())
        <p class="mt-8 p-8 text-center text-sm" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);color:var(--color-mc-ink-faint)">
            Aucun équipement pour le moment.
        </p>
    @else
        <div class="mt-6 overflow-x-auto" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid var(--color-mc-border);background:var(--color-mc-table-head)">
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">NOM</th>
                        <th class="px-4 py-3 text-right" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">QUANTITÉ TOTALE</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($equipment as $item)
                        <tr style="border-bottom:1px solid var(--color-mc-border-soft)">
                            <td class="px-4 py-3" style="font-size:14px;font-weight:700;color:var(--color-mc-ink)">{{ $item->name }}</td>
                            <td class="chiffre px-4 py-3 text-right">{{ $item->quantity_total }}</td>
                            <td class="px-4 py-3 text-right">
                                @if ($canWrite)
                                    <a href="{{ route('equipment.edit', $item) }}" title="Modifier" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-[color:var(--color-mc-ink-faint)] hover:bg-[var(--color-mc-canvas)] hover:text-[color:var(--color-mc-accent)]">
                                        <x-mc-icon name="pencil" class="h-4 w-4" />
                                    </a>
                                    <form method="POST" action="{{ route('equipment.destroy', $item) }}" class="inline" onsubmit="return confirm('Supprimer définitivement cet équipement ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Supprimer" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-[color:var(--color-mc-danger)] hover:bg-red-50">
                                            <x-mc-icon name="trash-2" class="h-4 w-4" />
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layouts.app>
