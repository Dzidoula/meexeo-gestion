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
                            <form method="POST" action="{{ route('hotel-newsletter-subscribers.destroy', $subscriber) }}">
                                @csrf @method('DELETE')
                                <button class="text-xs" style="color:var(--color-mc-danger)">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $subscribers->links() }}</div>
</x-layouts.app>
