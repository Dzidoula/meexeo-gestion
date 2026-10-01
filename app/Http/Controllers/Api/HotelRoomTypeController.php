<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HotelRoomType;

/**
 * Public JSON contract matching Résidence Touvalem's own
 * Api\RoomTypeController field-for-field (basePrice, bedCount, bathCount…
 * camelCase, rating nullable/never defaulted), so existing clients (the
 * public website, the Flutter app) can point here without changing their
 * parsing code — only the base URL changes.
 */
class HotelRoomTypeController extends Controller
{
    public function index()
    {
        return response()->json(
            HotelRoomType::all()->map(fn (HotelRoomType $t) => $this->serialize($t))
        );
    }

    public function show(HotelRoomType $hotelRoomType)
    {
        $similar = HotelRoomType::where('id', '!=', $hotelRoomType->id)->inRandomOrder()->take(3)->get();

        return response()->json([
            ...$this->serialize($hotelRoomType),
            'similarRoomTypes' => $similar->map(fn (HotelRoomType $t) => $this->serialize($t)),
        ]);
    }

    private function serialize(HotelRoomType $t): array
    {
        return [
            'id' => $t->id,
            'category' => $t->name,
            'name' => $t->name,
            'description' => $t->description,
            'basePrice' => $t->base_price !== null ? (float) $t->base_price : null,
            'rating' => $t->rating,
            'capacity' => $t->capacity,
            'bedCount' => $t->bed_count,
            'bathCount' => $t->bath_count,
            'area' => $t->area,
            'images' => $t->images ?? [],
            'amenities' => $t->amenities ?? [],
        ];
    }
}
