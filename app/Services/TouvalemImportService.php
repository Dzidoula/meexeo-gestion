<?php

namespace App\Services;

use App\Models\HotelGallery;
use App\Models\HotelRoom;
use App\Models\HotelRoomType;
use App\Models\HotelStay;
use App\Models\HotelContactMessage;
use App\Models\HotelFaq;
use App\Models\HotelTestimonial;
use Carbon\Carbon;

/**
 * Imports real data from Résidence Touvalem's own database (room_types,
 * rooms, bookings — read elsewhere via the `touvalem` connection) into this
 * app's Hotel module. Every write is keyed on (external_source='residence_touvalem',
 * external_id=<source row id>), so re-running an import updates existing
 * rows instead of duplicating them.
 *
 * Touvalem never assigns a specific physical room to a booking (it only
 * checks remaining capacity per room type), so a 'room'-type booking is
 * assigned, on import, to the first imported HotelRoom of the matching
 * type — a reasonable placeholder, not a reconstruction of history.
 */
class TouvalemImportService
{
    private const SOURCE = 'residence_touvalem';

    /** @param array<int, array<string, mixed>> $rows */
    public function importRoomTypes(array $rows): void
    {
        foreach ($rows as $row) {
            HotelRoomType::updateOrCreate(
                ['external_source' => self::SOURCE, 'external_id' => $row['id']],
                [
                    'name' => $row['name'],
                    'description' => $row['description'] ?? null,
                    'base_price' => isset($row['base_price']) ? (int) round((float) $row['base_price']) : null,
                    'rating' => isset($row['rating']) && $row['rating'] !== null ? (float) $row['rating'] : null,
                    'capacity' => $row['capacity'] ?? null,
                    'bed_count' => $row['bed_count'] ?? null,
                    'bath_count' => $row['bath_count'] ?? null,
                    'area' => $row['area'] ?? null,
                    'image' => $row['image'] ?? null,
                    'images' => $this->decodeJsonColumn($row['images'] ?? null),
                    'amenities' => $this->decodeJsonColumn($row['amenities'] ?? null),
                ],
            );
        }
    }

    /** @param array<int, array<string, mixed>> $rows */
    public function importRooms(array $rows): void
    {
        foreach ($rows as $row) {
            $hotelRoomType = HotelRoomType::where('external_source', self::SOURCE)
                ->where('external_id', $row['room_type_id'])
                ->first();

            HotelRoom::updateOrCreate(
                ['external_source' => self::SOURCE, 'external_id' => $row['id']],
                [
                    'hotel_room_type_id' => $hotelRoomType?->id,
                    'number' => $row['room_number'],
                    'nightly_rate' => $hotelRoomType?->base_price ?? 0,
                    'status' => $this->mapRoomStatus($row['status']),
                ],
            );
        }
    }

    /** @param array<int, array<string, mixed>> $rows */
    public function importBookings(array $rows): void
    {
        foreach ($rows as $row) {
            $type = $row['type'] === 'privatisation' ? 'privatisation' : 'chambre';
            $hotelRoomId = null;

            if ($type === 'chambre' && $row['room_type_id'] !== null) {
                $hotelRoomType = HotelRoomType::where('external_source', self::SOURCE)
                    ->where('external_id', $row['room_type_id'])
                    ->first();

                $hotelRoomId = $hotelRoomType
                    ?->rooms()
                    ->orderBy('id')
                    ->value('id');
            }

            HotelStay::updateOrCreate(
                ['external_source' => self::SOURCE, 'external_id' => $row['id']],
                [
                    'hotel_room_id' => $hotelRoomId,
                    'type' => $type,
                    'guest_name' => $row['guest_name'],
                    'guest_email' => $row['guest_email'] ?? null,
                    'guest_phone' => $row['guest_phone'] ?? null,
                    'arrival_date' => $row['check_in'],
                    'departure_date' => $row['check_out'],
                    'guests' => $row['guests'] ?? null,
                    'total_amount' => (int) round((float) $row['total_price']),
                    'deposit_amount' => 0,
                    'status' => $this->mapBookingStatus($row['status'], $row['check_in'], $row['check_out']),
                    'special_requests' => $row['special_requests'] ?? null,
                ],
            );
        }
    }

    /** @param array<int, array<string, mixed>> $rows */
    public function importGalleries(array $rows): void
    {
        foreach ($rows as $row) {
            HotelGallery::updateOrCreate(
                ['external_source' => self::SOURCE, 'external_id' => $row['id']],
                [
                    'image_path' => $row['image_path'],
                    'title' => $row['title'] ?? null,
                    'category' => $row['category'] ?? null,
                ],
            );
        }
    }

    /** @param array<int, array<string, mixed>> $rows */
    public function importTestimonials(array $rows): void
    {
        foreach ($rows as $row) {
            HotelTestimonial::updateOrCreate(
                ['external_source' => self::SOURCE, 'external_id' => $row['id']],
                [
                    'author_name' => $row['author_name'],
                    'author_subtitle' => $row['author_subtitle'] ?? null,
                    'author_image' => $row['author_image'] ?? null,
                    'rating' => (float) ($row['rating'] ?? 5),
                    'title' => $row['title'],
                    'content' => $row['content'],
                    'is_active' => (bool) ($row['is_active'] ?? true),
                ],
            );
        }
    }

    /** @param array<int, array<string, mixed>> $rows */
    public function importFaqs(array $rows): void
    {
        foreach ($rows as $row) {
            HotelFaq::updateOrCreate(
                ['external_source' => self::SOURCE, 'external_id' => $row['id']],
                [
                    'question' => $row['question'],
                    'answer' => $row['answer'],
                    'order' => (int) ($row['order'] ?? 0),
                    'is_active' => (bool) ($row['is_active'] ?? true),
                ],
            );
        }
    }

    /** @param array<int, array<string, mixed>> $rows */
    public function importContactMessages(array $rows): void
    {
        foreach ($rows as $row) {
            HotelContactMessage::updateOrCreate(
                ['external_source' => self::SOURCE, 'external_id' => $row['id']],
                [
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'email' => $row['email'],
                    'phone' => $row['phone'] ?? null,
                    'subject' => $row['subject'],
                    'message' => $row['message'],
                    'is_read' => (bool) ($row['is_read'] ?? false),
                ],
            );
        }
    }

    private function mapRoomStatus(string $touvalemStatus): string
    {
        return match ($touvalemStatus) {
            'booked' => 'occupee',
            'maintenance' => 'maintenance_chambre',
            default => 'disponible_chambre',
        };
    }

    private function mapBookingStatus(string $touvalemStatus, string $checkIn, string $checkOut): string
    {
        if ($touvalemStatus === 'cancelled') {
            return 'annule';
        }

        $today = Carbon::today();
        $arrival = Carbon::parse($checkIn);
        $departure = Carbon::parse($checkOut);

        if ($today->gte($departure)) {
            return 'termine';
        }

        if ($today->gte($arrival) && $today->lt($departure)) {
            return 'en_cours';
        }

        return 'reserve';
    }

    private function decodeJsonColumn(mixed $value): ?array
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? $decoded : null;
    }
}
