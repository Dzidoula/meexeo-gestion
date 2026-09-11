{{-- resources/views/components/page-header.blade.php --}}
@props(['title', 'subtitle' => null])
<div class="flex flex-wrap items-end justify-between gap-4 border-b border-lin-clair pb-5">
    <div>
        <h1 class="font-titre text-3xl text-lagune">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1 text-sm text-ardoise">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
