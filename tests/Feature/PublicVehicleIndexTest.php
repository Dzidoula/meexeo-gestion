<?php
namespace Tests\Feature;

use App\Enums\FuelType;
use App\Enums\Transmission;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicVehicleIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_is_accessible_without_authentication(): void
    {
        $this->get('/nos-vehicules')->assertOk();
    }

    public function test_it_lists_vehicles_with_their_brand_model_and_price(): void
    {
        Vehicle::factory()->create(['brand' => 'Toyota', 'model' => 'Fortuner', 'price' => 35000000]);

        $this->get('/nos-vehicules')
            ->assertOk()
            ->assertSee('Toyota')
            ->assertSee('Fortuner')
            ->assertSee('35 000 000 FCFA');
    }

    public function test_an_out_of_stock_vehicle_still_appears_with_its_badge(): void
    {
        Vehicle::factory()->create(['model' => 'Epuise', 'stock_quantity' => 0]);

        $this->get('/nos-vehicules')
            ->assertOk()
            ->assertSee('Epuise')
            ->assertSee('Épuisé');
    }

    public function test_it_filters_by_vehicle_type(): void
    {
        $suv = VehicleType::factory()->create(['name' => 'SUV']);
        $berline = VehicleType::factory()->create(['name' => 'Berline']);
        Vehicle::factory()->for($suv, 'vehicleType')->create(['model' => 'ModeleSuv']);
        Vehicle::factory()->for($berline, 'vehicleType')->create(['model' => 'ModeleBerline']);

        $this->get("/nos-vehicules?type={$suv->id}")
            ->assertSee('ModeleSuv')
            ->assertDontSee('ModeleBerline');
    }

    public function test_it_filters_by_fuel_type(): void
    {
        Vehicle::factory()->create(['model' => 'ModeleEssence', 'fuel_type' => FuelType::Essence]);
        Vehicle::factory()->create(['model' => 'ModeleDiesel', 'fuel_type' => FuelType::Diesel]);

        $this->get('/nos-vehicules?fuel_type=essence')
            ->assertSee('ModeleEssence')
            ->assertDontSee('ModeleDiesel');
    }

    public function test_it_filters_by_transmission(): void
    {
        Vehicle::factory()->create(['model' => 'ModeleAuto', 'transmission' => Transmission::Automatique]);
        Vehicle::factory()->create(['model' => 'ModeleManuelle', 'transmission' => Transmission::Manuelle]);

        $this->get('/nos-vehicules?transmission=automatique')
            ->assertSee('ModeleAuto')
            ->assertDontSee('ModeleManuelle');
    }

    public function test_it_filters_by_price_range(): void
    {
        Vehicle::factory()->create(['model' => 'ModeleAbordable', 'price' => 5000000]);
        Vehicle::factory()->create(['model' => 'ModeleCher', 'price' => 50000000]);

        $this->get('/nos-vehicules?price_min=1000000&price_max=10000000')
            ->assertSee('ModeleAbordable')
            ->assertDontSee('ModeleCher');
    }

    public function test_it_tells_the_visitor_when_nothing_matches(): void
    {
        $this->get('/nos-vehicules?q=introuvable')
            ->assertOk()
            ->assertSee('Aucun véhicule ne correspond');
    }

    public function test_it_does_not_show_any_admin_write_action(): void
    {
        $this->get('/nos-vehicules')
            ->assertOk()
            ->assertDontSee('Ajouter un véhicule')
            ->assertDontSee('Modifier');
    }
}
