<x-layouts.app title="Témoignages — MASTERCLAYS">
    <x-page-header title="Témoignages">
        <x-slot:actions>
            <a href="{{ route('hotel-testimonials.create') }}" class="min-h-[44px] inline-flex items-center px-4 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">Ajouter un témoignage</a>
        </x-slot:actions>
    </x-page-header>
    @include('hotel._subnav')

    @if (session('status'))
        <p class="mt-4 text-sm" style="color:var(--color-mc-success)">{{ session('status') }}</p>
    @endif

    <div class="mt-6 overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr style="color:var(--color-mc-ink-soft)"><th class="text-left">Auteur</th><th class="text-left">Titre</th><th class="text-left">Note</th><th class="text-left">Statut</th><th></th></tr></thead>
            <tbody>
                @foreach ($testimonials as $testimonial)
                    <tr style="border-top:1px solid var(--color-mc-border)">
                        <td class="py-2">{{ $testimonial->author_name }}</td>
                        <td>{{ $testimonial->title }}</td>
                        <td>{{ str_repeat('★', (int) round($testimonial->rating)) }}</td>
                        <td>{{ $testimonial->is_active ? 'Actif' : 'Inactif' }}</td>
                        <td>
                            <a href="{{ route('hotel-testimonials.edit', $testimonial) }}" class="text-xs font-semibold" style="color:var(--color-mc-accent)">Modifier</a>
                            <form method="POST" action="{{ route('hotel-testimonials.destroy', $testimonial) }}" class="inline">
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
