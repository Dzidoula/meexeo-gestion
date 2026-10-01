<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreHotelGalleryRequest;
use App\Models\HotelGallery;
use App\Services\TouvalemSyncPusher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HotelGalleryController extends Controller
{
    public function index(): View
    {
        return view('hotel-galleries.index', [
            'galleries' => HotelGallery::latest()->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('hotel-galleries.create');
    }

    public function store(StoreHotelGalleryRequest $request, TouvalemSyncPusher $pusher): RedirectResponse
    {
        $path = $request->file('image')->store('hotel-galleries', 'public');

        $gallery = HotelGallery::create([
            'image_path' => $path,
            'title' => $request->validated('title'),
            'category' => $request->validated('category'),
        ]);

        $pusher->push('galleries', $gallery, [
            'image_path' => $gallery->image_path,
            'title' => $gallery->title,
            'category' => $gallery->category,
        ]);

        return redirect()->route('hotel-galleries.index')->with('status', 'Image ajoutée à la galerie.');
    }

    public function destroy(HotelGallery $hotelGallery, TouvalemSyncPusher $pusher): RedirectResponse
    {
        if ($hotelGallery->external_source === null) {
            Storage::disk('public')->delete($hotelGallery->image_path);
        }

        $pusher->pushDelete('galleries', $hotelGallery);
        $hotelGallery->delete();

        return redirect()->route('hotel-galleries.index')->with('status', 'Image supprimée.');
    }
}
