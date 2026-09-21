<?php
namespace Tests\Feature;

use App\Models\Category;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DashboardModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_page_is_titled_masterclays_not_meexeo(): void
    {
        $response = $this->actingAs(User::factory()->manager()->create())->get('/tableau-de-bord');

        $response->assertSee('Tableau de bord — MASTERCLAYS', false);
        $response->assertDontSee('Vue d\'ensemble de l\'activité MEEXEO');
    }

    public function test_the_locatif_section_has_a_heading(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->get('/tableau-de-bord')
            ->assertSee('Locatif');
    }

    public function test_it_shows_the_vehicles_section_with_real_totals(): void
    {
        $suv = VehicleType::factory()->create(['name' => 'SUV']);
        Vehicle::factory()->for($suv, 'vehicleType')->create(['price' => 10000000, 'stock_quantity' => 2]);
        Vehicle::factory()->for($suv, 'vehicleType')->create(['price' => 5000000, 'stock_quantity' => 0]);

        $response = $this->actingAs(User::factory()->manager()->create())->get('/tableau-de-bord');

        $response->assertSee('Véhicules');
        $response->assertSee('2'); // total véhicules
        $response->assertSee('20 000 000 FCFA'); // valeur du stock : 10M*2 + 5M*0
        $response->assertSee('1'); // 1 véhicule épuisé
    }

    public function test_the_vehicles_section_links_to_the_vehicle_catalog(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->get('/tableau-de-bord')
            ->assertSee(route('vehicles.index'), false);
    }

    public function test_it_shows_the_products_section_with_real_totals(): void
    {
        $category = Category::factory()->create(['name' => 'Boissons']);
        Product::factory()->for($category)->create(['price' => 5000, 'stock_quantity' => 10]);
        Product::factory()->for($category)->create(['price' => 3000, 'stock_quantity' => 0]);

        $response = $this->actingAs(User::factory()->manager()->create())->get('/tableau-de-bord');

        $response->assertSee('Produits');
        $response->assertSee('50 000 FCFA'); // valeur du stock : 5000*10 + 3000*0
    }

    public function test_the_products_section_links_to_the_product_catalog(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->get('/tableau-de-bord')
            ->assertSee(route('products.index'), false);
    }

    public function test_it_shows_recent_activity_across_modules_newest_first(): void
    {
        Carbon::setTestNow('2026-06-20 12:00:00');
        try {
            $oldVehicle = Vehicle::factory()->create(['brand' => 'Toyota', 'model' => 'AncienModele']);
            Carbon::setTestNow('2026-06-21 12:00:00');
            $newVehicle = Vehicle::factory()->create(['brand' => 'Kia', 'model' => 'NouveauModele']);

            $response = $this->actingAs(User::factory()->manager()->create())->get('/tableau-de-bord');

            $response->assertOk();
            $html = $response->getContent();
            $this->assertLessThan(
                strpos($html, 'AncienModele'),
                strpos($html, 'NouveauModele'),
                'Le véhicule le plus récent doit apparaître avant le plus ancien.'
            );
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_recent_activity_is_capped_at_eight_entries(): void
    {
        Vehicle::factory()->count(10)->create();

        $response = $this->actingAs(User::factory()->manager()->create())->get('/tableau-de-bord');

        $response->assertOk();
        $this->assertSame(8, substr_count($response->getContent(), 'Véhicule ajouté —'));
    }

    public function test_recent_activity_links_to_the_source_record(): void
    {
        $vehicle = Vehicle::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->get('/tableau-de-bord')
            ->assertSee(route('vehicles.show', $vehicle), false);
    }
}
