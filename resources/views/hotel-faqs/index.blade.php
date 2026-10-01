<x-layouts.app title="FAQ — MASTERCLAYS">
    <x-page-header title="FAQ">
        <x-slot:actions>
            <a href="{{ route('hotel-faqs.create') }}" class="min-h-[44px] inline-flex items-center px-4 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">Ajouter une question</a>
        </x-slot:actions>
    </x-page-header>
    @include('hotel._subnav')

    @if (session('status'))
        <p class="mt-4 text-sm" style="color:var(--color-mc-success)">{{ session('status') }}</p>
    @endif

    <div class="mt-6 overflow-x-auto" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom:1px solid var(--color-mc-border);background:var(--color-mc-table-head)">
                    <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">ORDRE</th>
                    <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">QUESTION</th>
                    <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">STATUT</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($faqs as $faq)
                    <tr style="border-bottom:1px solid var(--color-mc-border-soft)">
                        <td class="px-4 py-3" style="font-size:14px;font-weight:700;color:var(--color-mc-ink)">{{ $faq->order }}</td>
                        <td class="px-4 py-3">{{ $faq->question }}</td>
                        <td class="px-4 py-3">{{ $faq->is_active ? 'Actif' : 'Inactif' }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('hotel-faqs.edit', $faq) }}" title="Modifier" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-[color:var(--color-mc-ink-faint)] hover:bg-[var(--color-mc-canvas)] hover:text-[color:var(--color-mc-accent)]">
                                <x-mc-icon name="pencil" class="h-4 w-4" />
                            </a>
                            <form method="POST" action="{{ route('hotel-faqs.destroy', $faq) }}" class="inline" onsubmit="return confirm('Supprimer définitivement cette question ?')">
                                @csrf @method('DELETE')
                                <button type="submit" title="Supprimer" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-[color:var(--color-mc-danger)] hover:bg-red-50">
                                    <x-mc-icon name="trash-2" class="h-4 w-4" />
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.app>
