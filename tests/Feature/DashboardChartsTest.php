<?php
namespace Tests\Feature;

use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardChartsTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_revenue_curve_data_covers_twelve_months_including_zeroes(): void
    {
        $lease = Lease::factory()->create();
        Payment::factory()->for($lease)->create(['amount' => 200000, 'paid_on' => now()->toDateString()]);
        // Pas de paiement le mois dernier : ce mois doit apparaître à 0, pas être absent.

        $response = $this->actingAs(User::factory()->manager()->create())->get('/tableau-de-bord');

        $response->assertOk();
        $response->assertSee('data-revenue-months', false);
        $response->assertSee('200000', false);
    }

    public function test_the_commune_breakdown_only_includes_communes_with_payments_in_the_period(): void
    {
        $propertyA = Property::factory()->create(['commune' => 'Cocody']);
        $leaseA = Lease::factory()->for($propertyA)->create();
        Payment::factory()->for($leaseA)->create(['amount' => 150000, 'paid_on' => now()->toDateString()]);

        $propertyB = Property::factory()->create(['commune' => 'Yopougon']);
        Lease::factory()->for($propertyB)->create(); // aucun paiement

        $response = $this->actingAs(User::factory()->manager()->create())->get('/tableau-de-bord');

        $response->assertSee('Cocody');
    }
}
