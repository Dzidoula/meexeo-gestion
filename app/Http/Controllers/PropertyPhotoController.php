<?php
// app/Http/Controllers/PropertyPhotoController.php
namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PropertyPhotoController extends Controller
{
    public function store(Request $request, Property $property): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ], [
            'photo.image' => 'Le fichier doit être une image (JPG, PNG ou WebP).',
            'photo.max' => 'La photo ne doit pas dépasser 8 Mo.',
        ]);

        $path = $request->file('photo')->store('properties/photos', 'public');

        $property->photos()->create([
            'path' => $path,
            // Le parc démarre sans photo : la première téléversée devient la principale.
            'is_primary' => ! $property->photos()->exists(),
            'position' => (int) $property->photos()->max('position') + 1,
        ]);

        return back()->with('status', 'La photo a été ajoutée.');
    }

    public function primary(Property $property, PropertyPhoto $photo): RedirectResponse
    {
        abort_unless($photo->property_id === $property->id, 404);

        DB::transaction(function () use ($property, $photo) {
            $property->photos()->update(['is_primary' => false]);
            $photo->update(['is_primary' => true]);
        });

        return back()->with('status', 'Photo principale mise à jour.');
    }

    public function destroy(Property $property, PropertyPhoto $photo): RedirectResponse
    {
        abort_unless($photo->property_id === $property->id, 404);

        Storage::disk('public')->delete($photo->path);
        $wasPrimary = $photo->is_primary;
        $photo->delete();

        // Ne jamais laisser un bien avec des photos mais sans photo principale.
        if ($wasPrimary && $next = $property->photos()->first()) {
            $next->update(['is_primary' => true]);
        }

        return back()->with('status', 'La photo a été supprimée.');
    }
}
