{{-- Icônes Lucide (lucide.dev, licence ISC) — tracés officiels, pas de dessin fait main. --}}
@props(['name', 'class' => 'h-8 w-8'])

@switch($name)
    @case('vehicle')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2" />
            <circle cx="7" cy="17" r="2" />
            <path d="M9 17h6" />
            <circle cx="17" cy="17" r="2" />
        </svg>
        @break

    @case('taxi')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M10 2h4" />
            <path d="m21 8-2 2-1.5-3.7A2 2 0 0 0 15.646 5H8.4a2 2 0 0 0-1.903 1.257L5 10 3 8" />
            <path d="M7 14h.01" />
            <path d="M17 14h.01" />
            <rect width="18" height="8" x="3" y="10" rx="2" />
            <path d="M5 18v2" />
            <path d="M19 18v2" />
        </svg>
        @break

    @case('speaker')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <rect width="16" height="20" x="4" y="2" rx="2" />
            <path d="M12 6h.01" />
            <circle cx="12" cy="14" r="4" />
            <path d="M12 14h.01" />
        </svg>
        @break

    @case('stage')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M2 10s3-3 3-8" />
            <path d="M22 10s-3-3-3-8" />
            <path d="M10 2c0 4.4-3.6 8-8 8" />
            <path d="M14 2c0 4.4 3.6 8 8 8" />
            <path d="M2 10s2 2 2 5" />
            <path d="M22 10s-2 2-2 5" />
            <path d="M8 15h8" />
            <path d="M2 22v-1a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1" />
            <path d="M14 22v-1a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1" />
        </svg>
        @break
@endswitch
