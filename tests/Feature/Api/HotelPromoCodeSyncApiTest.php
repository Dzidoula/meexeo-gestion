<?php
namespace Tests\Feature\Api;

use App\Models\HotelPromoCode;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HotelPromoCodeSyncApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.touvalem_sync.token' => 'test-secret-token']);
    }

    public function test_a_touvalem_promo_code_is_synced_in(): void
    {
        $this->postJson('/api/v1/touvalem-sync/promo-codes', [
            'id' => 4, 'code' => 'SUMMER10', 'discount_type' => 'percent', 'discount_value' => 10, 'is_active' => true,
        ], ['X-Sync-Token' => 'test-secret-token'])->assertCreated();

        $promoCode = HotelPromoCode::where('external_source', 'residence_touvalem')->where('external_id', 4)->sole();
        $this->assertSame('SUMMER10', $promoCode->code);
    }

    public function test_resyncing_the_same_code_updates_instead_of_duplicating(): void
    {
        $payload = ['id' => 4, 'code' => 'SUMMER10', 'discount_type' => 'percent', 'discount_value' => 10, 'is_active' => true];
        $headers = ['X-Sync-Token' => 'test-secret-token'];

        $this->postJson('/api/v1/touvalem-sync/promo-codes', $payload, $headers);
        $this->postJson('/api/v1/touvalem-sync/promo-codes', [...$payload, 'discount_value' => 15], $headers);

        $this->assertSame(1, HotelPromoCode::count());
        $this->assertSame(15.0, HotelPromoCode::sole()->discount_value);
    }

    public function test_a_touvalem_promo_code_deletion_is_synced_in(): void
    {
        $promoCode = HotelPromoCode::create([
            'code' => 'SUMMER10', 'discount_type' => 'percent', 'discount_value' => 10,
            'external_source' => 'residence_touvalem', 'external_id' => 4,
        ]);

        $this->postJson('/api/v1/touvalem-sync/promo-codes/delete', ['id' => 4], ['X-Sync-Token' => 'test-secret-token'])->assertOk();

        $this->assertNull($promoCode->fresh());
    }

    public function test_sync_is_rejected_without_the_right_token(): void
    {
        $this->postJson('/api/v1/touvalem-sync/promo-codes', ['id' => 1, 'code' => 'X'])->assertForbidden();
    }
}
