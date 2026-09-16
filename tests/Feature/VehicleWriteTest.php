<?php

namespace Tests\Feature;

use App\Enums\FuelType;
use App\Enums\Transmission;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleWriteTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsManager(): self
    {
        return $this->actingAs(User::factory()->manager()->create());
    }

    public function test_a_guest_cannot_create_a_vehicle(): void
    {
        $this->get('/vehicules/nouveau')->assertRedirect('/connexion');
    }

    public function test_a_manager_can_create_a_vehicle(): void
    {
        $type = VehicleType::factory()->create();

        $this->actingAsManager()
            ->post('/vehicules', [
                'vehicle_type_id' => $type->id,
                'brand' => 'Toyota',
                'model' => 'Fortuner',
                'fuel_type' => FuelType::Diesel->value,
                'transmission' => Transmission::Automatique->value,
                'seats' => 7,
                'price' => 35000000,
                'stock_quantity' => 2,
            ])
            ->assertRedirect();

        $vehicle = Vehicle::sole();
        $this->assertSame('Toyota', $vehicle->brand);
        $this->assertSame('Fortuner', $vehicle->model);
        $this->assertSame(35000000, $vehicle->price);
        $this->assertSame(2, $vehicle->stock_quantity);
        $this->assertSame($type->id, $vehicle->vehicle_type_id);
    }

    public function test_a_viewer_cannot_create_a_vehicle(): void
    {
        $type = VehicleType::factory()->create();

        $this->actingAs(User::factory()->viewer()->create())
            ->post('/vehicules', [
                'vehicle_type_id' => $type->id, 'brand' => 'X', 'model' => 'Y',
                'fuel_type' => FuelType::Essence->value, 'transmission' => Transmission::Manuelle->value,
                'seats' => 5, 'price' => 1000, 'stock_quantity' => 1,
            ])
            ->assertForbidden();
    }

    public function test_the_price_must_be_a_non_negative_integer(): void
    {
        $type = VehicleType::factory()->create();

        $this->actingAsManager()
            ->post('/vehicules', [
                'vehicle_type_id' => $type->id, 'brand' => 'X', 'model' => 'Y',
                'fuel_type' => FuelType::Essence->value, 'transmission' => Transmission::Manuelle->value,
                'seats' => 5, 'price' => -100, 'stock_quantity' => 1,
            ])
            ->assertSessionHasErrors('price');
    }

    public function test_the_vehicle_type_must_exist(): void
    {
        $this->actingAsManager()
            ->post('/vehicules', [
                'vehicle_type_id' => 999999, 'brand' => 'X', 'model' => 'Y',
                'fuel_type' => FuelType::Essence->value, 'transmission' => Transmission::Manuelle->value,
                'seats' => 5, 'price' => 1000, 'stock_quantity' => 1,
            ])
            ->assertSessionHasErrors('vehicle_type_id');
    }

    public function test_the_fuel_type_must_be_a_valid_enum_value(): void
    {
        $type = VehicleType::factory()->create();

        $this->actingAsManager()
            ->post('/vehicules', [
                'vehicle_type_id' => $type->id, 'brand' => 'X', 'model' => 'Y',
                'fuel_type' => 'nucleaire', 'transmission' => Transmission::Manuelle->value,
                'seats' => 5, 'price' => 1000, 'stock_quantity' => 1,
            ])
            ->assertSessionHasErrors('fuel_type');
    }

    public function test_a_manager_can_update_a_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create(['stock_quantity' => 5]);

        $this->actingAsManager()
            ->put("/vehicules/{$vehicle->id}", [
                'vehicle_type_id' => $vehicle->vehicle_type_id,
                'brand' => $vehicle->brand,
                'model' => $vehicle->model,
                'fuel_type' => $vehicle->fuel_type->value,
                'transmission' => $vehicle->transmission->value,
                'seats' => $vehicle->seats,
                'price' => $vehicle->price,
                'stock_quantity' => 0,
            ])
            ->assertRedirect();

        $this->assertSame(0, $vehicle->fresh()->stock_quantity);
    }
}
