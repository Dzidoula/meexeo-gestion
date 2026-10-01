<x-layouts.app title="Newsletter — MASTERCLAYS">
    <x-page-header title="Abonnés newsletter" />
    @include('hotel._subnav')
    <div class="mt-6 overflow-x-auto" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom:1px solid var(--color-mc-border);background:var(--color-mc-table-head)">
                    <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">EMAIL</th>
                    <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">INSCRIT LE</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($subscribers as $subscriber)
                    <tr style="border-bottom:1px solid var(--color-mc-border-soft)">
                        <td class="px-4 py-3" style="font-size:14px;font-weight:700;color:var(--color-mc-ink)">{{ $subscriber->email }}</td>
                        <td class="px-4 py-3">{{ $subscriber->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('hotel-newsletter-subscribers.destroy', $subscriber) }}" onsubmit="return confirm('Supprimer définitivement cet abonné ?')">
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
    <div class="mt-6">{{ $subscribers->links() }}</div>
</x-layouts.app>
