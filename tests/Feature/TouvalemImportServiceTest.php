<?php

namespace Tests\Feature;

use App\Models\HotelRoom;
use App\Models\HotelRoomType;
use App\Models\HotelStay;
use App\Services\TouvalemImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TouvalemImportServiceTest extends TestCase
{
    use RefreshDatabase;

    private function roomTypeRow(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'category' => 'Chambres de Luxe',
            'name' => 'Chambre Standard',
            'description' => 'Une belle chambre.',
            'base_price' => '150000.00',
            'rating' => '4.9',
            'capacity' => 2,
            'bed_count' => 1,
            'bath_count' => 1,
            'area' => 28,
            'image' => '/images/a.jpg',
            'images' => json_encode(['/images/a.jpg', '/images/b.jpg']),
            'amenities' => json_encode(['Wi-Fi gratuit', 'Climatisation']),
        ], $overrides);
    }

    private function roomRow(array $overrides = []): array
    {
        return array_merge([
            'id' => 10,
            'room_type_id' => 1,
            'room_number' => '101',
            'status' => 'available',
        ], $overrides);
    }

    private function bookingRow(array $overrides = []): array
    {
        return array_merge([
            'id' => 100,
            'type' => 'room',
            'room_type_id' => 1,
            'guest_name' => 'Jean Kouassi',
            'guest_email' => 'jean@example.com',
            'guest_phone' => '+2250700000000',
            'check_in' => '2026-11-01',
            'check_out' => '2026-11-03',
            'guests' => 2,
            'total_price' => '300000.00',
            'status' => 'confirmed',
            'special_requests' => null,
        ], $overrides);
    }

    public function test_imports_a_room_type_with_every_real_field_preserved(): void
    {
        $service = app(TouvalemImportService::class);

        $service->importRoomTypes([$this->roomTypeRow()]);

        $type = HotelRoomType::where('external_source', 'residence_touvalem')->where('external_id', 1)->first();
        $this->assertNotNull($type);
        $this->assertSame('Chambre Standard', $type->name);
        $this->assertSame('Une belle chambre.', $type->description);
        $this->assertSame(150000, $type->base_price);
        $this->assertSame(4.9, $type->rating);
        $this->assertSame(2, $type->capacity);
        $this->assertSame(['/images/a.jpg', '/images/b.jpg'], $type->images);
        $this->assertSame(['Wi-Fi gratuit', 'Climatisation'], $type->amenities);
    }

    public function test_importing_room_types_twice_does_not_duplicate_rows(): void
    {
        $service = app(TouvalemImportService::class);

        $service->importRoomTypes([$this->roomTypeRow()]);
        $service->importRoomTypes([$this->roomTypeRow(['name' => 'Chambre Standard (renamed)'])]);

        $this->assertSame(1, HotelRoomType::count());
        $this->assertSame('Chambre Standard (renamed)', HotelRoomType::first()->name);
    }

    public function test_imports_a_room_linked_to_its_already_imported_room_type(): void
    {
        $service = app(TouvalemImportService::class);
        $service->importRoomTypes([$this->roomTypeRow()]);

        $service->importRooms([$this->roomRow()]);

        $room = HotelRoom::where('external_source', 'residence_touvalem')->where('external_id', 10)->first();
        $this->assertNotNull($room);
        $this->assertSame('101', $room->number);
        $this->assertSame('disponible_chambre', $room->status->value);
        $this->assertSame(
            HotelRoomType::where('external_id', 1)->first()->id,
            $room->hotel_room_type_id,
        );
    }

    public function test_maps_touvalem_room_statuses_to_hotel_room_statuses(): void
    {
        $service = app(TouvalemImportService::class);
        $service->importRoomTypes([$this->roomTypeRow()]);

        $service->importRooms([
            $this->roomRow(['id' => 10, 'room_number' => '101', 'status' => 'available']),
            $this->roomRow(['id' => 11, 'room_number' => '102', 'status' => 'booked']),
            $this->roomRow(['id' => 12, 'room_number' => '103', 'status' => 'maintenance']),
        ]);

        $this->assertSame('disponible_chambre', HotelRoom::where('external_id', 10)->first()->status->value);
        $this->assertSame('occupee', HotelRoom::where('external_id', 11)->first()->status->value);
        $this->assertSame('maintenance_chambre', HotelRoom::where('external_id', 12)->first()->status->value);
    }

    public function test_imports_a_room_booking_assigned_to_a_room_of_the_matching_imported_type(): void
    {
        $service = app(TouvalemImportService::class);
        $service->importRoomTypes([$this->roomTypeRow()]);
        $service->importRooms([$this->roomRow()]);

        $service->importBookings([$this->bookingRow()]);

        $stay = HotelStay::where('external_source', 'residence_touvalem')->where('external_id', 100)->first();
        $this->assertNotNull($stay);
        $this->assertSame('chambre', $stay->type);
        $this->assertSame('Jean Kouassi', $stay->guest_name);
        $this->assertSame('jean@example.com', $stay->guest_email);
        $this->assertSame(2, $stay->guests);
        $this->assertSame(300000, $stay->total_amount);
        $this->assertSame(
            HotelRoom::where('external_id', 10)->first()->id,
            $stay->hotel_room_id,
        );
    }

    public function test_imports_a_privatisation_booking_with_no_room_assigned(): void
    {
        $service = app(TouvalemImportService::class);

        $service->importBookings([$this->bookingRow([
            'id' => 101,
            'type' => 'privatisation',
            'room_type_id' => null,
        ])]);

        $stay = HotelStay::where('external_id', 101)->first();
        $this->assertSame('privatisation', $stay->type);
        $this->assertNull($stay->hotel_room_id);
    }

    public function test_maps_booking_status_using_cancelled_first_then_dates(): void
    {
        $service = app(TouvalemImportService::class);

        $service->importBookings([
            $this->bookingRow(['id' => 1, 'type' => 'privatisation', 'room_type_id' => null, 'status' => 'cancelled', 'check_in' => '2020-01-01', 'check_out' => '2020-01-02']),
            $this->bookingRow(['id' => 2, 'type' => 'privatisation', 'room_type_id' => null, 'status' => 'confirmed', 'check_in' => '2020-01-01', 'check_out' => '2020-01-02']),
            $this->bookingRow(['id' => 3, 'type' => 'privatisation', 'room_type_id' => null, 'status' => 'confirmed', 'check_in' => now()->subDay()->toDateString(), 'check_out' => now()->addDay()->toDateString()]),
            $this->bookingRow(['id' => 4, 'type' => 'privatisation', 'room_type_id' => null, 'status' => 'pending', 'check_in' => now()->addMonth()->toDateString(), 'check_out' => now()->addMonth()->addDay()->toDateString()]),
        ]);

        $this->assertSame('annule', HotelStay::where('external_id', 1)->first()->status->value);
        $this->assertSame('termine', HotelStay::where('external_id', 2)->first()->status->value); // past, not cancelled
        $this->assertSame('en_cours', HotelStay::where('external_id', 3)->first()->status->value); // today is inside the stay
        $this->assertSame('reserve', HotelStay::where('external_id', 4)->first()->status->value); // future
    }

    public function test_importing_bookings_twice_does_not_duplicate_stays(): void
    {
        $service = app(TouvalemImportService::class);
        $service->importRoomTypes([$this->roomTypeRow()]);
        $service->importRooms([$this->roomRow()]);

        $service->importBookings([$this->bookingRow()]);
        $service->importBookings([$this->bookingRow(['guest_name' => 'Jean Kouassi (updated)'])]);

        $this->assertSame(1, HotelStay::count());
        $this->assertSame('Jean Kouassi (updated)', HotelStay::first()->guest_name);
    }

    public function test_confirmation_status_mirrors_the_touvalem_status_pending_and_confirmed_cancelled_clears_it(): void
    {
        $service = app(TouvalemImportService::class);

        $service->importBookings([$this->bookingRow(['id' => 201, 'status' => 'pending'])]);
        $service->importBookings([$this->bookingRow(['id' => 202, 'status' => 'confirmed'])]);
        $service->importBookings([$this->bookingRow(['id' => 203, 'status' => 'cancelled'])]);

        $this->assertSame('pending', HotelStay::where('external_id', 201)->first()->confirmation_status);
        $this->assertSame('confirmed', HotelStay::where('external_id', 202)->first()->confirmation_status);
        $this->assertNull(HotelStay::where('external_id', 203)->first()->confirmation_status);
    }

    public function test_a_room_booking_whose_type_has_no_imported_room_is_imported_with_no_room_assigned(): void
    {
        // Defensive case: a booking references a room_type_id that was
        // never imported (or has zero rooms imported for it) — must not
        // throw, must still record the stay for visibility.
        $service = app(TouvalemImportService::class);

        $service->importBookings([$this->bookingRow(['room_type_id' => 999])]);

        $stay = HotelStay::where('external_id', 100)->first();
        $this->assertNotNull($stay);
        $this->assertNull($stay->hotel_room_id);
    }
}
