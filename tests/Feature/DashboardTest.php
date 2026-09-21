<?php
namespace Tests\Feature;

use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_cannot_see_the_dashboard(): void
    {
        $this->get('/tableau-de-bord')->assertRedirect('/connexion');
    }

    public function test_every_authenticated_role_can_see_the_dashboard(): void
    {
        foreach (['admin', 'manager', 'accountant', 'viewer'] as $factoryState) {
            $this->actingAs(User::factory()->{$factoryState}()->create())
                ->get('/tableau-de-bord')
                ->assertOk();
        }
    }

    public function test_it_shows_the_total_number_of_properties(): void
    {
        Property::factory()->count(3)->create();

        $this->actingAs(User::factory()->manager()->create())
            ->get('/tableau-de-bord')
            ->assertSee('3');
    }

    public function test_it_shows_the_occupancy_rate(): void
    {
        Property::factory()->occupied()->count(2)->create();
        Property::factory()->vacant()->count(2)->create();

        $this->actingAs(User::factory()->manager()->create())
            ->get('/tableau-de-bord')
            ->assertSee('50');
    }

    public function test_it_sums_payments_within_the_selected_period(): void
    {
        $lease = Lease::factory()->create();
        Payment::factory()->for($lease)->create(['amount' => 300000, 'paid_on' => now()->toDateString()]);
        Payment::factory()->for($lease)->create(['amount' => 999000, 'paid_on' => now()->subYear()->toDateString()]);

        $this->actingAs(User::factory()->manager()->create())
            ->get('/tableau-de-bord?periode=mois')
            ->assertSee('300 000 FCFA')
            ->assertDontSee('999 000 FCFA');
    }

    public function test_it_lists_unpaid_current_month_leases_oldest_due_date_first(): void
    {
        // Date figée : sans ça, le test serait instable selon le jour réel
        // d'exécution (un due_day proche de la fin du mois pourrait ne pas
        // encore être échu de 5 jours si le test tourne tôt dans le mois).
        \Illuminate\Support\Carbon::setTestNow('2026-06-20');

        try {
            $recent = Lease::factory()->create(['due_day' => 15]); // échu depuis 5 jours
            $old = Lease::factory()->create(['due_day' => 1]); // échu depuis 19 jours
            // Aucun paiement ce mois-ci pour les deux : les deux sont donc impayés.

            $response = $this->actingAs(User::factory()->manager()->create())
                ->get('/tableau-de-bord');

            $response->assertOk();
            $html = $response->getContent();
            $this->assertLessThan(
                strpos($html, $recent->tenant->full_name),
                strpos($html, $old->tenant->full_name),
                "L'échéance la plus ancienne doit apparaître en premier."
            );
        } finally {
            \Illuminate\Support\Carbon::setTestNow();
        }
    }

    public function test_the_reminder_button_is_present_but_disabled(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->get('/tableau-de-bord')
            ->assertSee('disabled', false)
            ->assertSee('Lancer les relances');
    }

    public function test_a_fully_paid_lease_does_not_appear_in_the_due_soon_panel(): void
    {
        $lease = Lease::factory()->create(['due_day' => now()->day]);
        Payment::factory()->for($lease)->create([
            'amount' => $lease->monthly_rent,
            'month' => now()->startOfMonth()->toDateString(),
        ]);

        $response = $this->actingAs(User::factory()->manager()->create())->get('/tableau-de-bord');

        $html = $response->getContent();
        $dueSoonStart = strpos($html, 'Échéances du jour');
        $dueSoonEnd = strpos($html, 'Activité récente');
        $dueSoonSection = substr($html, $dueSoonStart, $dueSoonEnd - $dueSoonStart);

        $this->assertStringNotContainsString($lease->tenant->full_name, $dueSoonSection);
    }
}
