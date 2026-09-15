<x-layouts.app :title="($product->exists ? 'Modifier' : 'Ajouter').' un produit — MEEXEO'">
    <x-page-header :title="$product->exists ? 'Modifier le produit' : 'Ajouter un produit'" />

    @if ($categories->isEmpty())
        <p class="mt-6 p-6 text-sm" style="border-radius:var(--radius-mc);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);color:var(--color-mc-ink-soft)">
            Aucune catégorie n'existe encore. <a href="{{ route('categories.create') }}" style="color:var(--color-mc-accent)">Créez-en une d'abord</a>.
        </p>
    @else
        <form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}" class="mt-6 max-w-lg space-y-4">
            @csrf
            @if ($product->exists) @method('PUT') @endif

            <div>
                <label for="category_id" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Catégorie</label>
                <select id="category_id" name="category_id" required class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="name" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Nom</label>
                <input id="name" name="name" value="{{ old('name', $product->name) }}" required
                       class="mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                @error('name') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="mt-1.5 w-full px-3 py-2 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="price" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Prix (FCFA)</label>
                    <input id="price" name="price" type="number" step="1" min="0" value="{{ old('price', $product->price) }}" required
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('price') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="stock_quantity" class="text-xs font-semibold" style="color:var(--color-mc-ink-soft)">Quantité en stock</label>
                    <input id="stock_quantity" name="stock_quantity" type="number" step="1" min="0" value="{{ old('stock_quantity', $product->stock_quantity) }}" required
                           class="chiffre mt-1.5 min-h-[44px] w-full px-3 text-sm" style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border)">
                    @error('stock_quantity') <p class="mt-1 text-xs" style="color:var(--color-mc-danger)">{{ $message }}</p> @enderror
                </div>
            </div>

            <button class="min-h-[44px] px-5 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">
                Enregistrer
            </button>
        </form>
    @endif
</x-layouts.app>
