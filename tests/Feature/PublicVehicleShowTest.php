<?php
namespace Tests\Feature;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicVehicleShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_is_accessible_without_authentication(): void
    {
        $vehicle = Vehicle::factory()->create();

        $this->get("/nos-vehicules/{$vehicle->id}")->assertOk();
    }

    public function test_it_shows_the_vehicle_characteristics(): void
    {
        $vehicle = Vehicle::factory()->create([
            'brand' => 'Toyota', 'model' => 'Fortuner', 'price' => 35000000, 'seats' => 7,
        ]);

        $this->get("/nos-vehicules/{$vehicle->id}")
            ->assertOk()
            ->assertSee('Toyota')
            ->assertSee('Fortuner')
            ->assertSee('35 000 000 FCFA')
            ->assertSee($vehicle->fuel_type->label())
            ->assertSee($vehicle->transmission->label());
    }

    public function test_an_available_vehicle_shows_an_active_add_to_cart_button(): void
    {
        $vehicle = Vehicle::factory()->create(['stock_quantity' => 3]);

        $this->get("/nos-vehicules/{$vehicle->id}")
            ->assertOk()
            ->assertSee('Disponible')
            ->assertSee('Ajouter au panier')
            ->assertDontSee('disabled', false);
    }

    public function test_an_out_of_stock_vehicle_shows_a_disabled_epuise_button(): void
    {
        $vehicle = Vehicle::factory()->create(['stock_quantity' => 0]);

        $this->get("/nos-vehicules/{$vehicle->id}")
            ->assertOk()
            ->assertSee('Épuisé')
            ->assertSee('disabled', false);
    }

    public function test_it_does_not_show_any_admin_write_action(): void
    {
        $vehicle = Vehicle::factory()->create();

        $this->get("/nos-vehicules/{$vehicle->id}")
            ->assertOk()
            ->assertDontSee('Modifier le véhicule')
            ->assertDontSee('Ajouter un véhicule');
    }
}
