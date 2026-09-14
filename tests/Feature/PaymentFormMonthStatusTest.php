<?php
namespace Tests\Feature;

use App\Models\Lease;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PaymentFormMonthStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * Régression : `Payment::month` est casté en Carbon, donc une comparaison
     * de collection `where('month', '2026-06-01')` ne correspond jamais
     * (Carbon rendu en chaîne vaut 'Y-m-d H:i:s'). Le mois payé s'affichait
     * alors comme impayé sur le sélecteur de mois.
     */
    public function test_a_fully_paid_month_shows_the_paid_badge_on_the_form(): void
    {
        Carbon::setTestNow('2026-06-20');

        $lease = Lease::factory()->create([
            'start_date' => '2026-06-01',
            'due_day' => 5,
            'monthly_rent' => 150000,
        ]);
        Payment::factory()->for($lease)->create([
            'amount' => 150000,
            'month' => '2026-06-01',
        ]);

        $this->actingAs(User::factory()->manager()->create())
            ->get('/paiements/nouveau?lease='.$lease->id)
            ->assertOk()
            ->assertSee('Payé')
            ->assertDontSee('Impayé')
            ->assertDontSee('En retard');
    }

    public function test_a_partially_paid_month_shows_the_partial_badge_on_the_form(): void
    {
        Carbon::setTestNow('2026-06-20');

        $lease = Lease::factory()->create([
            'start_date' => '2026-06-01',
            'due_day' => 5,
            'monthly_rent' => 150000,
        ]);
        Payment::factory()->for($lease)->create([
            'amount' => 50000,
            'month' => '2026-06-01',
        ]);

        $this->actingAs(User::factory()->manager()->create())
            ->get('/paiements/nouveau?lease='.$lease->id)
            ->assertOk()
            ->assertSee('Partiel')
            ->assertDontSee('Impayé');
    }

    public function test_an_unpaid_month_still_shows_the_unpaid_badge(): void
    {
        Carbon::setTestNow('2026-06-20');

        $lease = Lease::factory()->create([
            'start_date' => '2026-06-01',
            'due_day' => 5,
            'monthly_rent' => 150000,
        ]);

        $this->actingAs(User::factory()->manager()->create())
            ->get('/paiements/nouveau?lease='.$lease->id)
            ->assertOk()
            ->assertSee('Impayé')
            ->assertDontSee('Payé');
    }
}
