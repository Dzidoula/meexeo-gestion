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

    <div class="mt-6 overflow-x-auto" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom:1px solid var(--color-mc-border);background:var(--color-mc-table-head)">
                    <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">AUTEUR</th>
                    <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">TITRE</th>
                    <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">NOTE</th>
                    <th class="px-4 py-3 text-left" style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">STATUT</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($testimonials as $testimonial)
                    <tr style="border-bottom:1px solid var(--color-mc-border-soft)">
                        <td class="px-4 py-3" style="font-size:14px;font-weight:700;color:var(--color-mc-ink)">{{ $testimonial->author_name }}</td>
                        <td class="px-4 py-3">{{ $testimonial->title }}</td>
                        <td class="px-4 py-3">{{ str_repeat('★', (int) round($testimonial->rating)) }}</td>
                        <td class="px-4 py-3">{{ $testimonial->is_active ? 'Actif' : 'Inactif' }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('hotel-testimonials.edit', $testimonial) }}" title="Modifier" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-[color:var(--color-mc-ink-faint)] hover:bg-[var(--color-mc-canvas)] hover:text-[color:var(--color-mc-accent)]">
                                <x-mc-icon name="pencil" class="h-4 w-4" />
                            </a>
                            <form method="POST" action="{{ route('hotel-testimonials.destroy', $testimonial) }}" class="inline" onsubmit="return confirm('Supprimer définitivement ce témoignage ?')">
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
