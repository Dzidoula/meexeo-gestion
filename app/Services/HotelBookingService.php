<?php

namespace App\Services;

use App\Exceptions\HotelStayUnavailableException;
use App\Models\HotelRoomType;
use App\Models\HotelStay;
use Carbon\Carbon;

/**
 * Availability/pricing/creation logic for the public booking API, mirroring
 * Résidence Touvalem's own BookingPricingService + Api\BookingController
 * (checkAvailability/calculateSubtotal/the response contract) — minus promo
 * codes, which this module has no table for yet.
 */
class HotelBookingService
{
    private const WEEKDAY_RATE = 250000;
    private const WEEKEND_RATE = 300000;
    private const WHATSAPP_NUMBER = '2250797548857';

    /** @return array{available: bool, message?: string} */
    public function checkAvailability(string $type, ?int $hotelRoomTypeId, string $checkIn, string $checkOut): array
    {
        $anyOverlap = fn () => HotelStay::whereNotIn('status', ['annule'])
            ->where('arrival_date', '<', $checkOut)
            ->where('departure_date', '>', $checkIn)
            ->exists();

        if ($type === 'privatisation') {
            if ($anyOverlap()) {
                return ['available' => false, 'message' => "Désolé, la résidence ne peut pas être privatisée sur ces dates car il y a déjà des réservations."];
            }

            return ['available' => true];
        }

        // A privatisation anywhere in range blocks every room.
        $privatisationOverlap = HotelStay::where('type', 'privatisation')
            ->whereNotIn('status', ['annule'])
            ->where('arrival_date', '<', $checkOut)
            ->where('departure_date', '>', $checkIn)
            ->exists();

        if ($privatisationOverlap) {
            return ['available' => false, 'message' => 'Désolé, la résidence entière est privatisée sur ces dates.'];
        }

        $hotelRoomType = HotelRoomType::find($hotelRoomTypeId);
        $totalRooms = $hotelRoomType?->rooms()->count() ?? 0;

        $overlappingCount = HotelStay::where('type', 'chambre')
            ->whereHas('room', fn ($q) => $q->where('hotel_room_type_id', $hotelRoomTypeId))
            ->whereNotIn('status', ['annule'])
            ->where('arrival_date', '<', $checkOut)
            ->where('departure_date', '>', $checkIn)
            ->count();

        if ($overlappingCount >= $totalRooms) {
            return ['available' => false, 'message' => "Désolé, ce type de chambre n'est plus disponible pour ces dates."];
        }

        return ['available' => true];
    }

    public function calculateSubtotal(string $type, ?HotelRoomType $hotelRoomType, string $checkIn, string $checkOut): int
    {
        $nights = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));

        if ($type === 'privatisation') {
            return $this->privatisationTotal($checkIn, $checkOut);
        }

        return (int) ($hotelRoomType?->base_price ?? 0) * max(1, $nights);
    }

    private function privatisationTotal(string $checkIn, string $checkOut): int
    {
        $total = 0;
        $night = Carbon::parse($checkIn)->startOfDay();
        $end = Carbon::parse($checkOut)->startOfDay();

        while ($night->lt($end)) {
            $isWeekend = in_array($night->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY, Carbon::SUNDAY], true);
            $total += $isWeekend ? self::WEEKEND_RATE : self::WEEKDAY_RATE;
            $night = $night->addDay();
        }

        return $total;
    }

    /**
     * @param array{type: string, hotel_room_type_id: ?int, guest_name: string, guest_email: ?string,
     *     guest_phone: string, check_in: string, check_out: string, guests: int, special_requests?: ?string} $data
     * @return array{id: int, status: string, totalPrice: float, whatsappUrl: string}
     */
    public function createBooking(array $data): array
    {
        $type = $data['type'] === 'privatisation' ? 'privatisation' : 'chambre';

        $availability = $this->checkAvailability($type, $data['hotel_room_type_id'] ?? null, $data['check_in'], $data['check_out']);
        if (!$availability['available']) {
            throw new HotelStayUnavailableException($availability['message']);
        }

        $hotelRoomType = $type === 'chambre' ? HotelRoomType::find($data['hotel_room_type_id']) : null;
        $totalAmount = $this->calculateSubtotal($type, $hotelRoomType, $data['check_in'], $data['check_out']);

        $hotelRoomId = null;
        if ($type === 'chambre') {
            $hotelRoomId = $hotelRoomType
                ?->rooms()
                ->whereDoesntHave('stays', function ($q) use ($data) {
                    $q->whereNotIn('status', ['annule'])
                        ->where('arrival_date', '<', $data['check_out'])
                        ->where('departure_date', '>', $data['check_in']);
                })
                ->orderBy('id')
                ->value('id');
        }

        $stay = HotelStay::create([
            'hotel_room_id' => $hotelRoomId,
            'type' => $type,
            'guest_name' => $data['guest_name'],
            'guest_email' => $data['guest_email'] ?? null,
            'guest_phone' => $data['guest_phone'],
            'arrival_date' => $data['check_in'],
            'departure_date' => $data['check_out'],
            'guests' => $data['guests'],
            'total_amount' => $totalAmount,
            'deposit_amount' => 0,
            'status' => 'reserve',
            'special_requests' => $data['special_requests'] ?? null,
        ]);

        $priceFormatted = number_format($totalAmount, 0, ',', ' ');
        $message = "Bonjour, je viens d'effectuer ma réservation #MC-" . str_pad((string) $stay->id, 5, '0', STR_PAD_LEFT)
            . " (Total: {$priceFormatted} FCFA) et je souhaite la confirmer avec vous. Merci !";
        $whatsappUrl = 'https://wa.me/' . self::WHATSAPP_NUMBER . '?text=' . rawurlencode($message);

        return [
            'id' => $stay->id,
            'status' => $stay->status->value,
            'totalPrice' => (float) $totalAmount,
            'whatsappUrl' => $whatsappUrl,
        ];
    }
}
