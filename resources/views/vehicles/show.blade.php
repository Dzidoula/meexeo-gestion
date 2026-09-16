@php($canWrite = in_array(auth()->user()->role, [\App\Enums\Role::Admin, \App\Enums\Role::Manager], true))
<x-layouts.app :title="$vehicle->brand.' '.$vehicle->model.' — SONOR LOCATION'">
    <x-page-header :title="$vehicle->brand.' '.$vehicle->model" :subtitle="$vehicle->vehicleType->name">
        <x-slot:actions>
            @if ($canWrite)
                <a href="{{ route('vehicles.edit', $vehicle) }}"
                   class="inline-flex min-h-[44px] items-center px-4 text-sm"
                   style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                    Modifier le véhicule
                </a>
            @endif
        </x-slot:actions>
    </x-page-header>

    @if (session('status'))
        <p class="mt-4 px-4 py-3 text-sm" style="border-radius:var(--radius-mc);border:1px solid rgba(22,163,74,.35);background:rgba(22,163,74,.08);color:var(--color-mc-success)">{{ session('status') }}</p>
    @endif

    <div class="mt-6 p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
        <div class="flex items-start justify-between gap-3">
            <p style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">{{ $vehicle->vehicleType->name }} · {{ $vehicle->fuel_type->label() }} · {{ $vehicle->transmission->label() }}</p>
            <x-status-badge :status="\App\Support\VehicleStockStatus::for($vehicle->stock_quantity)" />
        </div>

        @if ($vehicle->description)
            <p class="mt-3 text-sm" style="color:var(--color-mc-ink-soft)">{{ $vehicle->description }}</p>
        @endif

        <div class="mt-5 grid grid-cols-3 gap-4 border-t pt-5" style="border-color:var(--color-mc-border-soft)">
            <div>
                <p style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Prix</p>
                <p class="chiffre mt-1 text-2xl font-semibold">{{ \App\Support\Money::fcfa($vehicle->price) }}</p>
            </div>
            <div>
                <p style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Places</p>
                <p class="chiffre mt-1 text-2xl font-semibold">{{ $vehicle->seats }}</p>
            </div>
            <div>
                <p style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Quantité en stock</p>
                <p class="chiffre mt-1 text-2xl font-semibold">{{ $vehicle->stock_quantity }}</p>
            </div>
        </div>
    </div>
</x-layouts.app>
