<?php
namespace App\Http\Controllers;

use App\Enums\HotelRoomStatus;
use App\Enums\HotelStayStatus;
use App\Http\Requests\StoreHotelStayRequest;
use App\Models\HotelRoom;
use App\Models\HotelStay;
use App\Services\TouvalemSyncPusher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HotelStayController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->filled('status') ? $request->query('status') : null;

        $stays = HotelStay::query()
            ->with('room')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('arrival_date')
            ->paginate(25)
            ->withQueryString();

        return view('hotel-stays.index', ['stays' => $stays]);
    }

    public function create(): View
    {
        return view('hotel-stays.create', [
            'rooms' => HotelRoom::where('status', HotelRoomStatus::Available)->orderBy('number')->get(),
        ]);
    }

    public function store(StoreHotelStayRequest $request): RedirectResponse
    {
        $stay = HotelStay::create($request->validated() + ['status' => HotelStayStatus::Reserved]);

        return redirect()->route('hotel-stays.show', $stay)->with('status', 'Le séjour a été créé.');
    }

    public function show(HotelStay $stay): View
    {
        return view('hotel-stays.show', [
            'stay' => $stay->load(['room', 'payments']),
        ]);
    }

    public function checkIn(HotelStay $stay): RedirectResponse
    {
        if ($stay->status !== HotelStayStatus::Reserved) {
            return back()->with('error', 'Seul un séjour réservé peut être marqué comme arrivé.');
        }

        $stay->update(['status' => HotelStayStatus::InProgress, 'checked_in_at' => now()]);
        $stay->room?->update(['status' => HotelRoomStatus::Occupied]);

        return redirect()->route('hotel-stays.show', $stay)->with('status', 'Arrivée enregistrée.');
    }

    public function checkOut(HotelStay $stay): RedirectResponse
    {
        if ($stay->status !== HotelStayStatus::InProgress) {
            return back()->with('error', 'Seul un séjour en cours peut être marqué comme terminé.');
        }

        $stay->update(['status' => HotelStayStatus::Completed, 'checked_out_at' => now()]);

        if ($stay->room?->status === HotelRoomStatus::Occupied) {
            $stay->room->update(['status' => HotelRoomStatus::Available]);
        }

        return redirect()->route('hotel-stays.show', $stay)->with('status', 'Départ enregistré.');
    }

    public function cancel(HotelStay $stay): RedirectResponse
    {
        if (in_array($stay->status, [HotelStayStatus::Completed, HotelStayStatus::Cancelled], true)) {
            return back()->with('error', 'Ce séjour est déjà terminé ou annulé.');
        }

        $wasOccupying = $stay->status === HotelStayStatus::InProgress;
        $stay->update(['status' => HotelStayStatus::Cancelled, 'confirmation_status' => null]);

        if ($wasOccupying && $stay->room?->status === HotelRoomStatus::Occupied) {
            $stay->room->update(['status' => HotelRoomStatus::Available]);
        }

        return redirect()->route('hotel-stays.show', $stay)->with('status', 'Séjour annulé.');
    }

    public function confirm(HotelStay $stay, TouvalemSyncPusher $pusher): RedirectResponse
    {
        if ($stay->confirmation_status !== 'pending') {
            return back()->with('error', "Ce séjour n'est pas en attente de confirmation.");
        }

        $stay->update(['confirmation_status' => 'confirmed']);
        $pusher->push('booking-status', $stay, ['status' => 'confirmed']);

        return redirect()->route('hotel-stays.show', $stay)->with('status', 'Réservation confirmée.');
    }

    public function refuse(HotelStay $stay, TouvalemSyncPusher $pusher): RedirectResponse
    {
        if ($stay->confirmation_status !== 'pending') {
            return back()->with('error', "Ce séjour n'est pas en attente de confirmation.");
        }

        $wasOccupying = $stay->status === HotelStayStatus::InProgress;
        $stay->update(['confirmation_status' => 'refused', 'status' => HotelStayStatus::Cancelled]);

        if ($wasOccupying && $stay->room?->status === HotelRoomStatus::Occupied) {
            $stay->room->update(['status' => HotelRoomStatus::Available]);
        }

        $pusher->push('booking-status', $stay, ['status' => 'cancelled']);

        return redirect()->route('hotel-stays.show', $stay)->with('status', 'Réservation refusée.');
    }
}
