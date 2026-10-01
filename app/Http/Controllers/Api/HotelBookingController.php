<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\HotelStayUnavailableException;
use App\Http\Controllers\Controller;
use App\Services\HotelBookingService;
use Illuminate\Http\Request;

class HotelBookingController extends Controller
{
    public function store(Request $request, HotelBookingService $service)
    {
        $validated = $request->validate([
            'type' => 'nullable|in:chambre,privatisation',
            'hotel_room_type_id' => 'required_if:type,chambre|nullable|exists:hotel_room_types,id',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'nullable|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
        ]);

        try {
            $result = $service->createBooking($validated);
        } catch (HotelStayUnavailableException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        return response()->json($result, 201);
    }
}
