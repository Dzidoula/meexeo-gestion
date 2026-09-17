@props(['name', 'class' => 'h-8 w-8'])

@switch($name)
    @case('vehicle')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M6 28 L9 18 Q10 15 13 15 H35 Q38 15 39 18 L42 28" />
            <rect x="4" y="28" width="40" height="8" rx="2" />
            <circle cx="14" cy="38" r="4" fill="currentColor" stroke="none" />
            <circle cx="34" cy="38" r="4" fill="currentColor" stroke="none" />
        </svg>
        @break

    @case('taxi')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <rect x="19" y="9" width="10" height="5" rx="1" fill="currentColor" stroke="none" />
            <path d="M6 28 L9 18 Q10 15 13 15 H35 Q38 15 39 18 L42 28" />
            <rect x="4" y="28" width="40" height="8" rx="2" />
            <circle cx="14" cy="38" r="4" fill="currentColor" stroke="none" />
            <circle cx="34" cy="38" r="4" fill="currentColor" stroke="none" />
        </svg>
        @break

    @case('speaker')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <rect x="8" y="18" width="10" height="12" rx="1" />
            <path d="M18 18 L30 10 V38 L18 30 Z" />
            <path d="M35 16 Q40 24 35 32" />
            <path d="M39 12 Q46 24 39 36" />
        </svg>
        @break

    @case('stage')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M6 34 L14 20 H34 L42 34 Z" />
            <line x1="4" y1="38" x2="44" y2="38" />
            <path d="M24 6 L27 13 L34 14 L29 19 L30 26 L24 22 L18 26 L19 19 L14 14 L21 13 Z" fill="currentColor" stroke="none" />
        </svg>
        @break
@endswitch
