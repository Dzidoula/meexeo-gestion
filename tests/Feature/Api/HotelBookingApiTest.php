<?php

namespace Tests\Feature\Api;

use App\Models\HotelRoom;
use App\Models\HotelRoomType;
use App\Models\HotelStay;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelBookingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_a_room_booking_and_returns_the_expected_contract(): void
    {
        $type = HotelRoomType::factory()->create(['base_price' => 150000]);
        HotelRoom::factory()->create(['hotel_room_type_id' => $type->id]);

        $response = $this->postJson('/api/v1/bookings', [
            'type' => 'chambre',
            'hotel_room_type_id' => $type->id,
            'guest_name' => 'Awa Diallo',
            'guest_phone' => '+2250700000001',
            'check_in' => '2026-11-01',
            'check_out' => '2026-11-03',
            'guests' => 2,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'status', 'totalPrice', 'whatsappUrl']);
        $response->assertJsonPath('totalPrice', 300000);
        $this->assertDatabaseHas('hotel_stays', ['guest_name' => 'Awa Diallo']);
    }

    public function test_rejects_a_booking_with_missing_required_fields(): void
    {
        $response = $this->postJson('/api/v1/bookings', ['type' => 'chambre']);

        $response->assertStatus(422);
    }

    public function test_returns_409_when_nothing_is_available(): void
    {
        $type = HotelRoomType::factory()->create();
        $room = HotelRoom::factory()->create(['hotel_room_type_id' => $type->id]);
        HotelStay::factory()->create([
            'hotel_room_id' => $room->id,
            'type' => 'chambre',
            'arrival_date' => '2026-11-01',
            'departure_date' => '2026-11-03',
            'status' => 'reserve',
        ]);

        $response = $this->postJson('/api/v1/bookings', [
            'type' => 'chambre',
            'hotel_room_type_id' => $type->id,
            'guest_name' => 'Awa Diallo',
            'guest_phone' => '+2250700000001',
            'check_in' => '2026-11-01',
            'check_out' => '2026-11-03',
            'guests' => 2,
        ]);

        $response->assertStatus(409);
    }

    public function test_creates_a_privatisation_booking_with_no_room_type_required(): void
    {
        $response = $this->postJson('/api/v1/bookings', [
            'type' => 'privatisation',
            'guest_name' => 'Awa Diallo',
            'guest_phone' => '+2250700000001',
            'check_in' => '2026-11-06',
            'check_out' => '2026-11-09',
            'guests' => 10,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('totalPrice', 900000);
    }
}
