<?php
namespace Tests\Feature;

use App\Models\HotelPayment;
use App\Models\HotelStay;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelPaymentTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsManager(): self
    {
        return $this->actingAs(User::factory()->manager()->create());
    }

    public function test_a_manager_can_record_a_payment(): void
    {
        $stay = HotelStay::factory()->create(['total_amount' => 100000]);

        $this->actingAsManager()
            ->post("/sejours/{$stay->id}/paiements", [
                'amount' => 40000,
                'paid_on' => '2026-10-10',
                'method' => 'Mobile Money',
            ])
            ->assertRedirect();

        $payment = HotelPayment::sole();
        $this->assertSame($stay->id, $payment->hotel_stay_id);
        $this->assertSame(40000, $payment->amount);
    }

    public function test_a_viewer_cannot_record_a_payment(): void
    {
        $stay = HotelStay::factory()->create();

        $this->actingAs(User::factory()->viewer()->create())
            ->post("/sejours/{$stay->id}/paiements", [
                'amount' => 40000,
                'paid_on' => '2026-10-10',
            ])
            ->assertForbidden();
    }

    public function test_the_amount_cannot_be_negative(): void
    {
        $stay = HotelStay::factory()->create();

        $this->actingAsManager()
            ->post("/sejours/{$stay->id}/paiements", [
                'amount' => -5000,
                'paid_on' => '2026-10-10',
            ])
            ->assertSessionHasErrors('amount');
    }

    public function test_the_stay_show_page_displays_the_recalculated_balance(): void
    {
        $stay = HotelStay::factory()->create(['total_amount' => 100000]);
        HotelPayment::factory()->for($stay, 'stay')->create(['amount' => 30000]);

        $this->actingAsManager()
            ->get("/sejours/{$stay->id}")
            ->assertOk()
            ->assertSee('70 000 FCFA');
    }
}
