@php
    $tabs = [
        ['properties.index', 'Biens', 0],
        ['tenants.index', 'Locataires', 0],
        ['payments.create', 'Paiements', 0],
        // Le compteur rend visible ce qui attend une décision : sans lui, les
        // preuves envoyées par les locataires resteraient invisibles.
        ['portal-proofs.index', 'Preuves', \App\Models\Payment::where('portal_status', 'pending')->count()],
    ];
@endphp
<nav class="mc-scroll mt-2 flex gap-1 overflow-x-auto" style="border-bottom:1px solid var(--color-mc-border)">
    @foreach ($tabs as [$routeName, $label, $badge])
        <a href="{{ route($routeName) }}"
           class="flex items-center gap-1.5 whitespace-nowrap px-3 py-2 text-sm font-semibold"
           style="{{ request()->routeIs($routeName) ? 'color:var(--color-mc-accent);border-bottom:2px solid var(--color-mc-accent)' : 'color:var(--color-mc-ink-faint);border-bottom:2px solid transparent' }}">
            {{ $label }}
            @if ($badge > 0)
                <span style="border-radius:999px;background:var(--color-part-bg);color:var(--color-part-texte);padding:1px 7px;font-size:11px;font-weight:700">{{ $badge }}</span>
            @endif
        </a>
    @endforeach
</nav>
