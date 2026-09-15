<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductPhotoController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ], [
            'photo.image' => 'Le fichier doit être une image (JPG, PNG ou WebP).',
            'photo.max' => 'La photo ne doit pas dépasser 8 Mo.',
        ]);

        $path = $request->file('photo')->store('produits/photos', 'public');

        $product->photos()->create([
            'path' => $path,
            'is_primary' => ! $product->photos()->exists(),
            'position' => (int) $product->photos()->max('position') + 1,
        ]);

        return back()->with('status', 'La photo a été ajoutée.');
    }

    public function primary(Product $product, ProductPhoto $photo): RedirectResponse
    {
        abort_unless($photo->product_id === $product->id, 404);

        DB::transaction(function () use ($product, $photo) {
            $product->photos()->update(['is_primary' => false]);
            $photo->update(['is_primary' => true]);
        });

        return back()->with('status', 'Photo principale mise à jour.');
    }

    public function destroy(Product $product, ProductPhoto $photo): RedirectResponse
    {
        abort_unless($photo->product_id === $product->id, 404);

        Storage::disk('public')->delete($photo->path);
        $wasPrimary = $photo->is_primary;
        $photo->delete();

        if ($wasPrimary && $next = $product->photos()->first()) {
            $next->update(['is_primary' => true]);
        }

        return back()->with('status', 'La photo a été supprimée.');
    }
}
