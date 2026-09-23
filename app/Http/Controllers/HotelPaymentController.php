<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreHotelPaymentRequest;
use App\Models\HotelStay;
use Illuminate\Http\RedirectResponse;

class HotelPaymentController extends Controller
{
    public function store(StoreHotelPaymentRequest $request, HotelStay $stay): RedirectResponse
    {
        $stay->payments()->create($request->validated());

        return redirect()->route('hotel-stays.show', $stay)->with('status', 'Le paiement a été enregistré.');
    }
}
