<?php
namespace Tests\Feature\Api;

use App\Enums\HotelStayStatus;
use App\Models\HotelStay;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HotelStayBookingStatusSyncApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.touvalem_sync.token' => 'test-secret-token']);
    }

    public function test_a_touvalem_confirmation_updates_the_matching_stay(): void
    {
        $stay = HotelStay::factory()->create([
            'confirmation_status' => 'pending',
            'external_source' => 'residence_touvalem', 'external_id' => 42,
        ]);

        $this->postJson('/api/v1/touvalem-sync/booking-status', [
            'touvalem_id' => 42, 'status' => 'confirmed',
        ], ['X-Sync-Token' => 'test-secret-token'])->assertOk();

        $this->assertSame('confirmed', $stay->fresh()->confirmation_status);
    }

    public function test_a_touvalem_cancellation_also_cancels_the_operational_status(): void
    {
        $stay = HotelStay::factory()->create([
            'status' => HotelStayStatus::Reserved,
            'confirmation_status' => 'pending',
            'external_source' => 'residence_touvalem', 'external_id' => 42,
        ]);

        $this->postJson('/api/v1/touvalem-sync/booking-status', [
            'touvalem_id' => 42, 'status' => 'cancelled',
        ], ['X-Sync-Token' => 'test-secret-token'])->assertOk();

        $fresh = $stay->fresh();
        $this->assertSame('refused', $fresh->confirmation_status);
        $this->assertSame(HotelStayStatus::Cancelled, $fresh->status);
    }

    public function test_an_unknown_touvalem_id_does_not_500(): void
    {
        $this->postJson('/api/v1/touvalem-sync/booking-status', [
            'touvalem_id' => 9999, 'status' => 'confirmed',
        ], ['X-Sync-Token' => 'test-secret-token'])->assertNotFound();
    }

    public function test_sync_is_rejected_without_the_right_token(): void
    {
        $this->postJson('/api/v1/touvalem-sync/booking-status', ['touvalem_id' => 42, 'status' => 'confirmed'])
            ->assertForbidden();
    }
}
