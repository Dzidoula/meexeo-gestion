<x-layouts.app title="Messages de contact — MASTERCLAYS">
    <x-page-header title="Messages de contact" />
    <div class="mt-6 overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr style="color:var(--color-mc-ink-soft)"><th class="text-left">De</th><th class="text-left">Sujet</th><th class="text-left">Reçu le</th><th></th></tr></thead>
            <tbody>
                @foreach ($messages as $message)
                    <tr style="border-top:1px solid var(--color-mc-border);{{ $message->is_read ? '' : 'font-weight:700' }}">
                        <td class="py-2"><a href="{{ route('hotel-contact-messages.show', $message) }}">{{ $message->first_name }} {{ $message->last_name }}</a></td>
                        <td>{{ $message->subject }}</td>
                        <td>{{ $message->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <form method="POST" action="{{ route('hotel-contact-messages.destroy', $message) }}">
                                @csrf @method('DELETE')
                                <button class="text-xs" style="color:var(--color-mc-danger)">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $messages->links() }}</div>
</x-layouts.app>
