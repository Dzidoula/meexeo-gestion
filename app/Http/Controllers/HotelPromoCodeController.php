<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreHotelPromoCodeRequest;
use App\Http\Requests\UpdateHotelPromoCodeRequest;
use App\Models\HotelPromoCode;
use App\Services\TouvalemSyncPusher;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HotelPromoCodeController extends Controller
{
    public function index(): View
    {
        return view('hotel-promo-codes.index', [
            'promoCodes' => HotelPromoCode::latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('hotel-promo-codes.form', ['hotelPromoCode' => new HotelPromoCode()]);
    }

    public function store(StoreHotelPromoCodeRequest $request, TouvalemSyncPusher $pusher): RedirectResponse
    {
        $promoCode = HotelPromoCode::create($request->validated());
        $pusher->push('promo-codes', $promoCode, $request->validated());

        return redirect()->route('hotel-promo-codes.index')->with('status', 'Code promo créé.');
    }

    public function edit(HotelPromoCode $hotelPromoCode): View
    {
        return view('hotel-promo-codes.form', ['hotelPromoCode' => $hotelPromoCode]);
    }

    public function update(UpdateHotelPromoCodeRequest $request, HotelPromoCode $hotelPromoCode, TouvalemSyncPusher $pusher): RedirectResponse
    {
        $hotelPromoCode->update($request->validated());
        $pusher->push('promo-codes', $hotelPromoCode, $request->validated());

        return redirect()->route('hotel-promo-codes.index')->with('status', 'Code promo mis à jour.');
    }

    public function destroy(HotelPromoCode $hotelPromoCode, TouvalemSyncPusher $pusher): RedirectResponse
    {
        $pusher->pushDelete('promo-codes', $hotelPromoCode);
        $hotelPromoCode->delete();

        return redirect()->route('hotel-promo-codes.index')->with('status', 'Code promo supprimé.');
    }

    public function toggleStatus(HotelPromoCode $hotelPromoCode, TouvalemSyncPusher $pusher): RedirectResponse
    {
        $hotelPromoCode->update(['is_active' => !$hotelPromoCode->is_active]);
        $pusher->push('promo-codes', $hotelPromoCode, [
            'code' => $hotelPromoCode->code,
            'discount_type' => $hotelPromoCode->discount_type,
            'discount_value' => $hotelPromoCode->discount_value,
            'min_total' => $hotelPromoCode->min_total,
            'max_uses' => $hotelPromoCode->max_uses,
            'starts_at' => $hotelPromoCode->starts_at,
            'expires_at' => $hotelPromoCode->expires_at,
            'is_active' => $hotelPromoCode->is_active,
        ]);

        return redirect()->route('hotel-promo-codes.index')->with('status', 'Statut modifié.');
    }
}
