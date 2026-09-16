<?php
namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehiclePhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VehiclePhotoController extends Controller
{
    public function store(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ], [
            'photo.image' => 'Le fichier doit être une image (JPG, PNG ou WebP).',
            'photo.max' => 'La photo ne doit pas dépasser 8 Mo.',
        ]);

        $path = $request->file('photo')->store('vehicules/photos', 'public');

        $vehicle->photos()->create([
            'path' => $path,
            'is_primary' => ! $vehicle->photos()->exists(),
            'position' => (int) $vehicle->photos()->max('position') + 1,
        ]);

        return back()->with('status', 'La photo a été ajoutée.');
    }

    public function primary(Vehicle $vehicle, VehiclePhoto $photo): RedirectResponse
    {
        abort_unless($photo->vehicle_id === $vehicle->id, 404);

        DB::transaction(function () use ($vehicle, $photo) {
            $vehicle->photos()->update(['is_primary' => false]);
            $photo->update(['is_primary' => true]);
        });

        return back()->with('status', 'Photo principale mise à jour.');
    }

    public function destroy(Vehicle $vehicle, VehiclePhoto $photo): RedirectResponse
    {
        abort_unless($photo->vehicle_id === $vehicle->id, 404);

        Storage::disk('public')->delete($photo->path);
        $wasPrimary = $photo->is_primary;
        $photo->delete();

        if ($wasPrimary && $next = $vehicle->photos()->first()) {
            $next->update(['is_primary' => true]);
        }

        return back()->with('status', 'La photo a été supprimée.');
    }
}
