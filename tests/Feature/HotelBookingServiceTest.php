<?php

namespace Tests\Feature;

use App\Models\HotelRoom;
use App\Models\HotelRoomType;
use App\Models\HotelStay;
use App\Services\HotelBookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelBookingServiceTest extends TestCase
{
    use RefreshDatabase;

    private function service(): HotelBookingService
    {
        return app(HotelBookingService::class);
    }

    public function test_room_type_is_available_when_fewer_overlapping_stays_than_rooms(): void
    {
        $type = HotelRoomType::factory()->create(['base_price' => 150000]);
        HotelRoom::factory()->count(2)->create(['hotel_room_type_id' => $type->id]);

        $result = $this->service()->checkAvailability('chambre', $type->id, '2026-11-01', '2026-11-03');

        $this->assertTrue($result['available']);
    }

    public function test_room_type_is_unavailable_once_every_room_overlaps_an_existing_stay(): void
    {
        $type = HotelRoomType::factory()->create();
        $rooms = HotelRoom::factory()->count(2)->create(['hotel_room_type_id' => $type->id]);
        foreach ($rooms as $room) {
            HotelStay::factory()->create([
                'hotel_room_id' => $room->id,
                'type' => 'chambre',
                'arrival_date' => '2026-11-01',
                'departure_date' => '2026-11-03',
                'status' => 'reserve',
            ]);
        }

        $result = $this->service()->checkAvailability('chambre', $type->id, '2026-11-02', '2026-11-04');

        $this->assertFalse($result['available']);
        $this->assertNotEmpty($result['message']);
    }

    public function test_a_cancelled_overlapping_stay_does_not_block_availability(): void
    {
        $type = HotelRoomType::factory()->create();
        $room = HotelRoom::factory()->create(['hotel_room_type_id' => $type->id]);
        HotelStay::factory()->create([
            'hotel_room_id' => $room->id,
            'type' => 'chambre',
            'arrival_date' => '2026-11-01',
            'departure_date' => '2026-11-03',
            'status' => 'annule',
        ]);

        $result = $this->service()->checkAvailability('chambre', $type->id, '2026-11-01', '2026-11-03');

        $this->assertTrue($result['available']);
    }

    public function test_room_type_is_unavailable_during_an_existing_privatisation(): void
    {
        $type = HotelRoomType::factory()->create();
        HotelRoom::factory()->create(['hotel_room_type_id' => $type->id]);
        HotelStay::factory()->create([
            'hotel_room_id' => null,
            'type' => 'privatisation',
            'arrival_date' => '2026-11-01',
            'departure_date' => '2026-11-05',
            'status' => 'reserve',
        ]);

        $result = $this->service()->checkAvailability('chambre', $type->id, '2026-11-02', '2026-11-03');

        $this->assertFalse($result['available']);
    }

    public function test_privatisation_is_unavailable_when_any_stay_overlaps(): void
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

        $result = $this->service()->checkAvailability('privatisation', null, '2026-11-02', '2026-11-04');

        $this->assertFalse($result['available']);
    }

    public function test_privatisation_is_available_with_no_overlapping_stays(): void
    {
        $result = $this->service()->checkAvailability('privatisation', null, '2026-11-01', '2026-11-03');

        $this->assertTrue($result['available']);
    }

    public function test_calculate_subtotal_multiplies_nights_by_the_room_types_base_price(): void
    {
        $type = HotelRoomType::factory()->create(['base_price' => 150000]);

        $subtotal = $this->service()->calculateSubtotal('chambre', $type, '2026-11-01', '2026-11-04');

        $this->assertSame(450000, $subtotal); // 3 nights
    }

    public function test_calculate_subtotal_for_privatisation_uses_the_flat_weekday_weekend_rule(): void
    {
        // Friday 2026-11-06 -> Monday 2026-11-09: Fri+Sat+Sun weekend nights.
        $subtotal = $this->service()->calculateSubtotal('privatisation', null, '2026-11-06', '2026-11-09');

        $this->assertSame(900000, $subtotal); // 3 weekend nights at 300 000
    }

    public function test_create_booking_assigns_an_available_room_and_returns_the_expected_contract(): void
    {
        $type = HotelRoomType::factory()->create(['base_price' => 150000]);
        $room = HotelRoom::factory()->create(['hotel_room_type_id' => $type->id]);

        $result = $this->service()->createBooking([
            'type' => 'chambre',
            'hotel_room_type_id' => $type->id,
            'guest_name' => 'Awa Diallo',
            'guest_email' => 'awa@example.com',
            'guest_phone' => '+2250700000001',
            'check_in' => '2026-11-01',
            'check_out' => '2026-11-03',
            'guests' => 2,
        ]);

        $this->assertArrayHasKey('id', $result);
        $this->assertSame('reserve', $result['status']);
        $this->assertSame(300000.0, $result['totalPrice']);
        $this->assertStringContainsString('wa.me', $result['whatsappUrl']);

        $stay = HotelStay::find($result['id']);
        $this->assertSame($room->id, $stay->hotel_room_id);
        $this->assertSame('Awa Diallo', $stay->guest_name);
    }

    public function test_create_booking_throws_when_nothing_is_available(): void
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

        $this->expectException(\App\Exceptions\HotelStayUnavailableException::class);

        $this->service()->createBooking([
            'type' => 'chambre',
            'hotel_room_type_id' => $type->id,
            'guest_name' => 'Awa Diallo',
            'guest_phone' => '+2250700000001',
            'check_in' => '2026-11-01',
            'check_out' => '2026-11-03',
            'guests' => 2,
        ]);
    }
}
