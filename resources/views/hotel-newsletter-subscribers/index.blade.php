<x-layouts.app title="Newsletter — MASTERCLAYS">
    <x-page-header title="Abonnés newsletter" />
    @include('hotel._subnav')
    <div class="mt-6 overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr style="color:var(--color-mc-ink-soft)"><th class="text-left">Email</th><th class="text-left">Inscrit le</th><th></th></tr></thead>
            <tbody>
                @foreach ($subscribers as $subscriber)
                    <tr style="border-top:1px solid var(--color-mc-border)">
                        <td class="py-2">{{ $subscriber->email }}</td>
                        <td>{{ $subscriber->created_at->format('d/m/Y') }}</td>
                        <td>
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
