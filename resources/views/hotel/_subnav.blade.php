@php
    $tabs = [
        ['hotel-rooms.index', 'Chambres'],
        ['hotel-room-types.index', 'Types de chambres'],
        ['hotel-stays.index', 'Séjours'],
        ['hotel-galleries.index', 'Galerie'],
        ['hotel-testimonials.index', 'Témoignages'],
        ['hotel-faqs.index', 'FAQ'],
        ['hotel-contact-messages.index', 'Contacts'],
        ['hotel-newsletter-subscribers.index', 'Newsletter'],
        ['hotel-promo-codes.index', 'Codes promo'],
    ];
@endphp
<nav class="mc-scroll mt-2 flex gap-1 overflow-x-auto" style="border-bottom:1px solid var(--color-mc-border)">
    @foreach ($tabs as [$routeName, $label])
        <a href="{{ route($routeName) }}"
           class="whitespace-nowrap px-3 py-2 text-sm font-semibold"
           style="{{ request()->routeIs($routeName) ? 'color:var(--color-mc-accent);border-bottom:2px solid var(--color-mc-accent)' : 'color:var(--color-mc-ink-faint);border-bottom:2px solid transparent' }}">
            {{ $label }}
        </a>
    @endforeach
</nav>
