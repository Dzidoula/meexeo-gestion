<?php
namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventPayment;
use App\Models\User;
use App\Support\Money;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventPaymentTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsManager(): self
    {
        return $this->actingAs(User::factory()->manager()->create());
    }

    public function test_a_manager_can_record_a_payment(): void
    {
        $event = Event::factory()->create();

        $this->actingAsManager()
            ->post("/evenements/{$event->id}/paiements", ['amount' => 150000, 'paid_on' => '2026-11-01', 'method' => 'Mobile Money'])
            ->assertRedirect(route('events.show', $event));

        $payment = EventPayment::sole();
        $this->assertSame($event->id, $payment->event_id);
        $this->assertSame(150000, $payment->amount);
    }

    public function test_a_viewer_cannot_record_a_payment(): void
    {
        $event = Event::factory()->create();

        $this->actingAs(User::factory()->viewer()->create())
            ->post("/evenements/{$event->id}/paiements", ['amount' => 150000, 'paid_on' => '2026-11-01'])
            ->assertForbidden();
    }

    public function test_the_amount_is_required(): void
    {
        $event = Event::factory()->create();

        $this->actingAsManager()
            ->post("/evenements/{$event->id}/paiements", ['amount' => '', 'paid_on' => '2026-11-01'])
            ->assertSessionHasErrors('amount');
    }

    public function test_the_amount_cannot_be_negative(): void
    {
        $event = Event::factory()->create();

        $this->actingAsManager()
            ->post("/evenements/{$event->id}/paiements", ['amount' => -1, 'paid_on' => '2026-11-01'])
            ->assertSessionHasErrors('amount');

        $this->assertSame(0, EventPayment::count());
    }

    public function test_the_deposit_is_shown_and_deducted_from_the_balance(): void
    {
        $event = Event::factory()->create(['budget_total' => 3500000, 'deposit_amount' => 500000]);
        EventPayment::factory()->for($event)->create(['amount' => 1000000]);

        $expectedBalance = 3500000 - 500000 - 1000000;

        $this->actingAsManager()
            ->get(route('events.show', $event))
            ->assertSee(Money::fcfa(500000))
            ->assertSee(Money::fcfa($expectedBalance));
    }
}
