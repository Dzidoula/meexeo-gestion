<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreHotelRoomRequest;
use App\Http\Requests\UpdateHotelRoomRequest;
use App\Models\HotelRoom;
use App\Models\HotelRoomType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HotelRoomController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->filled('type') ? (int) $request->query('type') : null;
        $status = $request->filled('status') ? $request->query('status') : null;

        $rooms = HotelRoom::query()
            ->with('hotelRoomType')
            ->when($type, fn ($q) => $q->where('hotel_room_type_id', $type))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('number')
            ->paginate(25)
            ->withQueryString();

        return view('hotel-rooms.index', [
            'rooms' => $rooms,
            'hotelRoomTypes' => HotelRoomType::orderBy('name')->get(),
        ]);
    }

    public function show(HotelRoom $room): View
    {
        return view('hotel-rooms.show', ['room' => $room]);
    }

    public function create(): View
    {
        return view('hotel-rooms.form', [
            'room' => new HotelRoom(),
            'hotelRoomTypes' => HotelRoomType::orderBy('name')->get(),
        ]);
    }

    public function store(StoreHotelRoomRequest $request): RedirectResponse
    {
        $room = HotelRoom::create($request->validated());

        return redirect()->route('hotel-rooms.show', $room)->with('status', 'La chambre a été créée.');
    }

    public function edit(HotelRoom $room): View
    {
        return view('hotel-rooms.form', [
            'room' => $room,
            'hotelRoomTypes' => HotelRoomType::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateHotelRoomRequest $request, HotelRoom $room): RedirectResponse
    {
        $room->update($request->validated());

        return redirect()->route('hotel-rooms.show', $room)->with('status', 'La chambre a été mise à jour.');
    }

    public function destroy(HotelRoom $room): RedirectResponse
    {
        if ($room->stays()->exists()) {
            return back()->with('error', 'Cette chambre a des séjours associés, elle ne peut pas être supprimée.');
        }

        $room->delete();

        return redirect()->route('hotel-rooms.index')->with('status', 'La chambre a été supprimée.');
    }
}
