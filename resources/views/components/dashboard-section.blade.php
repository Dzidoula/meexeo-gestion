@props(['title', 'icon', 'iconColor', 'href' => null, 'linkLabel' => null])
<section class="mt-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center" style="width:40px;height:40px;border-radius:10px;background:color-mix(in srgb, {{ $iconColor }} 12%, transparent);color:{{ $iconColor }}">
                <x-mc-icon :name="$icon" class="h-5 w-5" />
            </div>
            <h2 class="font-titre text-lg">{{ $title }}</h2>
        </div>
        @if ($href)
            <a href="{{ $href }}" class="text-sm font-semibold" style="color:{{ $iconColor }}">{{ $linkLabel }} →</a>
        @endif
    </div>
    <div class="mt-4">
        {{ $slot }}
    </div>
</section>
