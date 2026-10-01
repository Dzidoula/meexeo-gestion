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

    <div class="mt-6 overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr style="color:var(--color-mc-ink-soft)"><th class="text-left">Ordre</th><th class="text-left">Question</th><th class="text-left">Statut</th><th></th></tr></thead>
            <tbody>
                @foreach ($faqs as $faq)
                    <tr style="border-top:1px solid var(--color-mc-border)">
                        <td class="py-2">{{ $faq->order }}</td>
                        <td>{{ $faq->question }}</td>
                        <td>{{ $faq->is_active ? 'Actif' : 'Inactif' }}</td>
                        <td>
                            <a href="{{ route('hotel-faqs.edit', $faq) }}" class="text-xs font-semibold" style="color:var(--color-mc-accent)">Modifier</a>
                            <form method="POST" action="{{ route('hotel-faqs.destroy', $faq) }}" class="inline">
                                @csrf @method('DELETE')
                                <button class="ml-2 text-xs" style="color:var(--color-mc-danger)">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.app>
