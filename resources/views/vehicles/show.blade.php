@php($canWrite = in_array(auth()->user()->role, [\App\Enums\Role::Admin, \App\Enums\Role::Manager], true))
<x-layouts.app :title="$vehicle->brand.' '.$vehicle->model.' — MASTERCLAYS'">
    <x-page-header :title="$vehicle->brand.' '.$vehicle->model" :subtitle="$vehicle->vehicleType->name">
        <x-slot:actions>
            @if ($canWrite)
                <a href="{{ route('vehicles.edit', $vehicle) }}"
                   class="inline-flex min-h-[44px] items-center px-4 text-sm"
                   style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                    Modifier le véhicule
                </a>
            @endif
            <a href="{{ route('vehicles.index') }}"
               class="inline-flex min-h-[44px] items-center px-2 text-sm" style="color:var(--color-mc-ink-faint)">Retour au catalogue</a>
        </x-slot:actions>
    </x-page-header>

    @if (session('status'))
        <p class="mt-4 px-4 py-3 text-sm" style="border-radius:var(--radius-mc);border:1px solid rgba(22,163,74,.35);background:rgba(22,163,74,.08);color:var(--color-mc-success)">{{ session('status') }}</p>
    @endif

    <div class="mt-6 grid gap-5 lg:grid-cols-[1.2fr_1fr]">
        <div class="p-4" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
            @if ($primary = $vehicle->photos->firstWhere('is_primary', true))
                <img src="{{ $primary->url }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="h-[296px] w-full object-cover" style="border-radius:var(--radius-mc-sm)">
            @else
                <div class="flex h-[296px] items-center justify-center border border-dashed text-sm" style="border-radius:var(--radius-mc-sm);border-color:var(--color-mc-border);color:var(--color-mc-ink-faint)">
                    Aucune photo pour ce véhicule
                </div>
            @endif

            @if ($canWrite)
                <form method="POST" action="{{ route('vehicles.photos.store', $vehicle) }}" enctype="multipart/form-data"
                      class="mt-3 flex flex-wrap items-center gap-3 border border-dashed p-3"
                      style="border-radius:var(--radius-mc-sm);border-color:var(--color-mc-border)">
                    @csrf
                    <input type="file" name="photo" accept="image/*" required class="text-sm">
                    <button class="min-h-[44px] px-3 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">Ajouter</button>
                    @error('photo') <p class="text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </form>
            @endif

            @if ($vehicle->photos->count() > 0)
                <div class="mt-3 grid grid-cols-4 gap-2">
                    @foreach ($vehicle->photos as $photo)
                        <div>
                            <img src="{{ $photo->url }}" alt="" class="h-16 w-full object-cover" style="border-radius:var(--radius-mc-sm)">
                            @if ($canWrite)
                                <div class="mt-1 flex items-center justify-between gap-1">
                                    @if (! $photo->is_primary)
                                        <form method="POST" action="{{ route('vehicles.photos.primary', [$vehicle, $photo]) }}">
                                            @csrf @method('PATCH')
                                            <button class="min-h-[44px] text-[11px]" style="color:var(--color-mc-ink-faint)">Principale</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('vehicles.photos.destroy', [$vehicle, $photo]) }}">
                                        @csrf @method('DELETE')
                                        <button class="min-h-[44px] text-[11px]" style="color:var(--color-mc-danger)">Suppr.</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="p-6" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
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
    </div>
</x-layouts.app>
