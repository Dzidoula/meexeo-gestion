{{-- resources/views/components/stat-card.blade.php --}}
@props(['label', 'value', 'hint' => null, 'tone' => 'lagune'])
@php($toneColor = match ($tone) {
    'cuivre' => 'var(--color-mc-accent)',
    'terre' => 'var(--color-mc-danger)',
    default => 'var(--color-mc-ink)',
})
<div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:20px">
    <p style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">{{ $label }}</p>
    <p class="chiffre mt-2 text-3xl font-semibold" style="color:{{ $toneColor }}">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs" style="color:var(--color-mc-ink-faint)">{{ $hint }}</p>
    @endif
</div>
