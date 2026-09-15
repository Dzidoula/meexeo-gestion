@php($canWrite = in_array(auth()->user()->role, [\App\Enums\Role::Admin, \App\Enums\Role::Manager], true))
<x-layouts.app :title="$product->name.' — MEEXEO'">
    <x-page-header :title="$product->name" :subtitle="$product->category->name">
        <x-slot:actions>
            @if ($canWrite)
                <a href="{{ route('products.edit', $product) }}"
                   class="inline-flex min-h-[44px] items-center px-4 text-sm"
                   style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
                    Modifier le produit
                </a>
            @endif
            <a href="{{ route('products.index') }}"
               class="inline-flex min-h-[44px] items-center px-2 text-sm" style="color:var(--color-mc-ink-faint)">Retour au catalogue</a>
        </x-slot:actions>
    </x-page-header>

    @if (session('status'))
        <p class="mt-4 px-4 py-3 text-sm" style="border-radius:var(--radius-mc);border:1px solid rgba(22,163,74,.35);background:rgba(22,163,74,.08);color:var(--color-mc-success)">{{ session('status') }}</p>
    @endif

    <div class="mt-6 grid gap-5 lg:grid-cols-[1.2fr_1fr]">
        <div class="p-4" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface)">
            @if ($primary = $product->photos->firstWhere('is_primary', true))
                <img src="{{ $primary->url }}" alt="{{ $product->name }}" class="h-[296px] w-full object-cover" style="border-radius:var(--radius-mc-sm)">
            @else
                <div class="flex h-[296px] items-center justify-center border border-dashed text-sm" style="border-radius:var(--radius-mc-sm);border-color:var(--color-mc-border);color:var(--color-mc-ink-faint)">
                    Aucune photo pour ce produit
                </div>
            @endif

            @if ($canWrite)
                <form method="POST" action="{{ route('products.photos.store', $product) }}" enctype="multipart/form-data"
                      class="mt-3 flex flex-wrap items-center gap-3 border border-dashed p-3"
                      style="border-radius:var(--radius-mc-sm);border-color:var(--color-mc-border)">
                    @csrf
                    <input type="file" name="photo" accept="image/*" required class="text-sm">
                    <button class="min-h-[44px] px-3 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">Ajouter</button>
                    @error('photo') <p class="text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </form>
            @endif

            @if ($product->photos->count() > 0)
                <div class="mt-3 grid grid-cols-4 gap-2">
                    @foreach ($product->photos as $photo)
                        <div>
                            <img src="{{ $photo->url }}" alt="" class="h-16 w-full object-cover" style="border-radius:var(--radius-mc-sm)">
                            @if ($canWrite)
                                <div class="mt-1 flex items-center justify-between gap-1">
                                    @if (! $photo->is_primary)
                                        <form method="POST" action="{{ route('products.photos.primary', [$product, $photo]) }}">
                                            @csrf @method('PATCH')
                                            <button class="min-h-[44px] text-[11px]" style="color:var(--color-mc-ink-faint)">Principale</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('products.photos.destroy', [$product, $photo]) }}">
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
                <p style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">{{ $product->category->name }}</p>
                <x-status-badge :status="\App\Support\ProductStockStatus::for($product->stock_quantity)" />
            </div>

            @if ($product->description)
                <p class="mt-3 text-sm" style="color:var(--color-mc-ink-soft)">{{ $product->description }}</p>
            @endif

            <div class="mt-5 grid grid-cols-2 gap-4 border-t pt-5" style="border-color:var(--color-mc-border-soft)">
                <div>
                    <p style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Prix</p>
                    <p class="chiffre mt-1 text-2xl font-semibold">{{ \App\Support\Money::fcfa($product->price) }}</p>
                </div>
                <div>
                    <p style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">Quantité en stock</p>
                    <p class="chiffre mt-1 text-2xl font-semibold">{{ $product->stock_quantity }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
