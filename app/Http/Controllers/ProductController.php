<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->filled('category') ? (int) $request->query('category') : null;
        $status = in_array($request->query('status'), ['en_stock', 'rupture'], true) ? $request->query('status') : null;
        $search = trim((string) $request->query('q'));

        $products = Product::query()
            ->with('category')
            ->when($category, fn ($q) => $q->where('category_id', $category))
            ->when($status === 'rupture', fn ($q) => $q->where('stock_quantity', 0))
            ->when($status === 'en_stock', fn ($q) => $q->where('stock_quantity', '>', 0))
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
            'productsTotal' => Product::count(),
            'stockValue' => (int) Product::query()->selectRaw('COALESCE(SUM(price * stock_quantity), 0) as total')->value('total'),
            'outOfStockCount' => Product::where('stock_quantity', 0)->count(),
        ]);
    }

    public function create(): View
    {
        return view('products.form', [
            'product' => new Product(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->validated());

        return redirect()->route('products.show', $product)->with('status', 'Le produit a été créé.');
    }

    public function show(Product $product): View
    {
        return view('products.show', ['product' => $product]);
    }

    public function edit(Product $product): View
    {
        return view('products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()->route('products.show', $product)->with('status', 'Le produit a été mis à jour.');
    }
}
