@props(['vehicle', 'class' => 'h-32 w-full', 'iconClass' => 'h-8 w-8', 'radius' => 'var(--radius-mc-store)'])
@php($primary = $vehicle->photos->firstWhere('is_primary', true))

@if ($primary)
    <img src="{{ $primary->url }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="{{ $class }} object-cover" style="border-radius:{{ $radius }}">
@else
    <div class="{{ $class }} flex flex-col items-center justify-center gap-1.5" style="border-radius:{{ $radius }};background:linear-gradient(155deg, var(--color-mc-store-navy), var(--color-mc-store-navy-soft));color:#fff">
        <x-mc-store-icon name="vehicle" :class="$iconClass.' opacity-70'" />
        <span class="text-[11px]" style="color:#C3C9D3">Aucune photo</span>
    </div>
@endif
