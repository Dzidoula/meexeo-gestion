<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreHotelRoomTypeRequest;
use App\Http\Requests\UpdateHotelRoomTypeRequest;
use App\Models\HotelRoomType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HotelRoomTypeController extends Controller
{
    public function index(): View
    {
        return view('hotel-room-types.index', [
            'hotelRoomTypes' => HotelRoomType::withCount('rooms')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('hotel-room-types.form', ['hotelRoomType' => new HotelRoomType()]);
    }

    public function store(StoreHotelRoomTypeRequest $request): RedirectResponse
    {
        HotelRoomType::create($request->validated());

        return redirect()->route('hotel-room-types.index')->with('status', 'Le type de chambre a été créé.');
    }

    public function edit(HotelRoomType $hotelRoomType): View
    {
        return view('hotel-room-types.form', ['hotelRoomType' => $hotelRoomType]);
    }

    public function update(UpdateHotelRoomTypeRequest $request, HotelRoomType $hotelRoomType): RedirectResponse
    {
        $hotelRoomType->update($request->validated());

        return redirect()->route('hotel-room-types.index')->with('status', 'Le type de chambre a été mis à jour.');
    }

    public function destroy(HotelRoomType $hotelRoomType): RedirectResponse
    {
        if ($hotelRoomType->rooms()->exists()) {
            return back()->with('error', 'Ce type de chambre contient des chambres, il ne peut pas être supprimé.');
        }

        $hotelRoomType->delete();

        return redirect()->route('hotel-room-types.index')->with('status', 'Le type de chambre a été supprimé.');
    }
}
