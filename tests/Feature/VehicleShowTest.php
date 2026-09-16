<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_cannot_see_the_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create();

        $this->get("/vehicules/{$vehicle->id}")->assertRedirect('/connexion');
    }

    public function test_it_shows_the_vehicle_details(): void
    {
        $vehicle = Vehicle::factory()->create(['brand' => 'Toyota', 'model' => 'Corolla', 'price' => 18500000]);

        $this->actingAs(User::factory()->viewer()->create())
            ->get("/vehicules/{$vehicle->id}")
            ->assertOk()
            ->assertSee('Toyota')
            ->assertSee('Corolla')
            ->assertSee('18 500 000 FCFA');
    }

    public function test_a_vehicle_in_stock_shows_the_disponible_badge(): void
    {
        $vehicle = Vehicle::factory()->create(['stock_quantity' => 3]);

        $this->actingAs(User::factory()->viewer()->create())
            ->get("/vehicules/{$vehicle->id}")
            ->assertSee('Disponible');
    }

    public function test_a_vehicle_with_no_stock_shows_the_epuise_badge(): void
    {
        $vehicle = Vehicle::factory()->create(['stock_quantity' => 0]);

        $this->actingAs(User::factory()->viewer()->create())
            ->get("/vehicules/{$vehicle->id}")
            ->assertSee('Épuisé');
    }
}
