{{-- resources/views/components/stat-card.blade.php --}}
@props(['label', 'value', 'hint' => null, 'tone' => 'lagune', 'icon' => null, 'iconColor' => null])
@php($toneColor = match ($tone) {
    'cuivre' => 'var(--color-mc-accent)',
    'terre' => 'var(--color-mc-danger)',
    default => 'var(--color-mc-ink)',
})
<div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:16px">
    @if ($icon)
        <div class="mb-2 flex h-8 w-8 items-center justify-center" style="border-radius:8px;background:{{ $iconColor }}1A;color:{{ $iconColor }}">
            <x-mc-icon :name="$icon" class="h-4 w-4" />
        </div>
    @endif
    <p style="font-size:12px;font-weight:600;color:var(--color-mc-ink-faint)">{{ $label }}</p>
    <p class="chiffre mt-1 font-semibold" style="font-size:20px;color:{{ $toneColor }}">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs" style="color:var(--color-mc-ink-faint)">{{ $hint }}</p>
    @endif
</div>
