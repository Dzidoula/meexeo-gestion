{{-- resources/views/components/stat-card.blade.php --}}
@props(['label', 'value', 'hint' => null, 'tone' => 'lagune'])
<div class="rounded-meexeo border border-lin-clair bg-papier p-5">
    <p class="surtitre">{{ $label }}</p>
    <p class="chiffre mt-2 text-3xl font-semibold text-{{ $tone }}">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs text-brume">{{ $hint }}</p>
    @endif
</div>
