<?php

namespace Tests\Feature\Api;

use App\Models\HotelRoom;
use App\Models\HotelRoomType;
use App\Models\HotelStay;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TouvalemSyncApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.touvalem_sync.token' => 'test-secret-token']);
    }

    private function row(array $overrides = []): array
    {
        return array_merge([
            'id' => 500,
            'type' => 'room',
            'room_type_id' => 1,
            'guest_name' => 'Awa Diallo',
            'guest_email' => 'awa@example.com',
            'guest_phone' => '+2250700000001',
            'check_in' => '2026-12-01',
            'check_out' => '2026-12-03',
            'guests' => 2,
            'total_price' => 300000,
            'status' => 'pending',
            'special_requests' => null,
        ], $overrides);
    }

    public function test_syncs_a_booking_from_touvalem_into_a_hotel_stay(): void
    {
        $type = HotelRoomType::factory()->create(['external_source' => 'residence_touvalem', 'external_id' => 1]);
        HotelRoom::factory()->create(['hotel_room_type_id' => $type->id]);

        $response = $this->withHeader('X-Sync-Token', 'test-secret-token')
            ->postJson('/api/v1/touvalem-sync/bookings', $this->row());

        $response->assertStatus(201);
        $stay = HotelStay::where('external_source', 'residence_touvalem')->where('external_id', 500)->first();
        $this->assertNotNull($stay);
        $this->assertSame('Awa Diallo', $stay->guest_name);
        $this->assertSame(300000, $stay->total_amount);
    }

    public function test_rejects_a_request_without_the_correct_sync_token(): void
    {
        $response = $this->withHeader('X-Sync-Token', 'wrong-token')
            ->postJson('/api/v1/touvalem-sync/bookings', $this->row());

        $response->assertStatus(403);
        $this->assertSame(0, HotelStay::count());
    }

    public function test_rejects_a_request_with_no_sync_token_at_all(): void
    {
        $response = $this->postJson('/api/v1/touvalem-sync/bookings', $this->row());

        $response->assertStatus(403);
    }

    public function test_syncing_the_same_booking_twice_updates_instead_of_duplicating(): void
    {
        $this->withHeader('X-Sync-Token', 'test-secret-token')
            ->postJson('/api/v1/touvalem-sync/bookings', $this->row())
            ->assertStatus(201);

        $this->withHeader('X-Sync-Token', 'test-secret-token')
            ->postJson('/api/v1/touvalem-sync/bookings', $this->row(['guest_name' => 'Awa Diallo (mise à jour)']))
            ->assertStatus(201);

        $this->assertSame(1, HotelStay::count());
        $this->assertSame('Awa Diallo (mise à jour)', HotelStay::first()->guest_name);
    }

    public function test_syncs_a_privatisation_booking_with_no_room_type(): void
    {
        $response = $this->withHeader('X-Sync-Token', 'test-secret-token')
            ->postJson('/api/v1/touvalem-sync/bookings', $this->row(['id' => 501, 'type' => 'privatisation', 'room_type_id' => null]));

        $response->assertStatus(201);
        $stay = HotelStay::where('external_id', 501)->first();
        $this->assertSame('privatisation', $stay->type);
        $this->assertNull($stay->hotel_room_id);
    }
}
