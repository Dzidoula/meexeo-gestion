@php($canWrite = in_array(auth()->user()->role, [\App\Enums\Role::Admin, \App\Enums\Role::Manager], true))
<x-layouts.app title="Types de véhicule — MASTERCLAYS">
    <x-page-header title="Types de véhicule" subtitle="{{ $vehicleTypes->count() }} type(s)">
        <x-slot:actions>
            @if ($canWrite)
                <a href="{{ route('vehicle-types.create') }}"
                   class="inline-flex min-h-[44px] items-center px-4 text-sm font-semibold"
                   style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
                    Ajouter un type
                </a>
            @endif
        </x-slot:actions>
    </x-page-header>

    @if (session('status'))
        <p class="mt-4 px-4 py-3 text-sm" style="border-radius:var(--radius-mc);border:1px solid rgba(22,163,74,.35);background:rgba(22,163,74,.08);color:var(--color-mc-success)">{{ session('status') }}</p>
    @endif
    @if (session('error'))
        <p class="mt-4 px-4 py-3 text-sm" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-danger);background:rgba(220,38,38,.08);color:var(--color-mc-danger)">{{ session('error') }}</p>
    @endif

    @if ($vehicleTypes->isEmpty())
        <p class="mt-8 p-8 text-center text-sm" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);color:var(--color-mc-ink-faint)">
            Aucun type de véhicule pour le moment.
        </p>
    @else
        <div class="mt-6 overflow-x-auto" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid var(--color-mc-border);background:var(--color-mc-table-head)">
                        <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">NOM</th>
                        <th class="px-4 py-3 text-right" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">VÉHICULES</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vehicleTypes as $type)
                        <tr style="border-bottom:1px solid var(--color-mc-border-soft)">
                            <td class="px-4 py-3" style="font-size:14px;font-weight:700;color:var(--color-mc-ink)">{{ $type->name }}</td>
                            <td class="chiffre px-4 py-3 text-right">{{ $type->vehicles_count }}</td>
                            <td class="px-4 py-3 text-right">
                                @if ($canWrite)
                                    <a href="{{ route('vehicle-types.edit', $type) }}" class="min-h-[44px] text-sm" style="color:var(--color-mc-ink-faint)">Modifier</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layouts.app>
