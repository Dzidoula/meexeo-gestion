<?php
namespace Tests\Feature;

use App\Models\HotelPromoCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HotelPromoCodeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.touvalem_sync.token' => 'test-secret-token']);
    }

    private function actingAsManager(): self
    {
        return $this->actingAs(User::factory()->manager()->create());
    }

    public function test_a_manager_can_create_a_promo_code(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['synced' => true], 201)]);

        $this->actingAsManager()->post('/codes-promo', [
            'code' => 'summer10', 'discount_type' => 'percent', 'discount_value' => 10, 'is_active' => '1',
        ])->assertRedirect();

        $promoCode = HotelPromoCode::sole();
        $this->assertSame('SUMMER10', $promoCode->code);
        Http::assertSent(fn ($r) => $r->url() === 'https://residencetouvalem.com/api/masterclays-sync/promo-codes');
    }

    public function test_a_manager_can_update_a_promo_code(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['synced' => true], 201)]);
        $promoCode = HotelPromoCode::create(['code' => 'OLD10', 'discount_type' => 'percent', 'discount_value' => 10]);

        $this->actingAsManager()->put("/codes-promo/{$promoCode->id}", [
            'code' => 'NEW10', 'discount_type' => 'percent', 'discount_value' => 10,
        ])->assertRedirect();

        $this->assertSame('NEW10', $promoCode->fresh()->code);
    }

    public function test_a_manager_can_toggle_a_promo_code_status(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['synced' => true], 201)]);
        $promoCode = HotelPromoCode::create(['code' => 'X10', 'discount_type' => 'percent', 'discount_value' => 10, 'is_active' => true]);

        $this->actingAsManager()->patch("/codes-promo/{$promoCode->id}/basculer")->assertRedirect();

        $this->assertFalse($promoCode->fresh()->is_active);
        Http::assertSent(fn ($r) => $r->url() === 'https://residencetouvalem.com/api/masterclays-sync/promo-codes');
    }

    public function test_a_manager_can_delete_a_promo_code(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['deleted' => true])]);
        $promoCode = HotelPromoCode::create(['code' => 'X10', 'discount_type' => 'percent', 'discount_value' => 10]);

        $this->actingAsManager()->delete("/codes-promo/{$promoCode->id}")->assertRedirect();

        $this->assertNull($promoCode->fresh());
        Http::assertSent(fn ($r) => str_ends_with($r->url(), '/promo-codes/delete'));
    }

    public function test_a_viewer_cannot_create_a_promo_code(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->post('/codes-promo', ['code' => 'X10', 'discount_type' => 'percent', 'discount_value' => 10])
            ->assertForbidden();
    }
}
