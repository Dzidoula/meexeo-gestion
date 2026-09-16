<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleTypeWriteTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsManager(): self
    {
        return $this->actingAs(User::factory()->manager()->create());
    }

    public function test_a_guest_cannot_see_the_vehicle_type_list(): void
    {
        $this->get('/types-vehicules')->assertRedirect('/connexion');
    }

    public function test_a_manager_can_create_a_vehicle_type(): void
    {
        $this->actingAsManager()
            ->post('/types-vehicules', ['name' => 'Berline'])
            ->assertRedirect();

        $type = VehicleType::sole();
        $this->assertSame('Berline', $type->name);
        $this->assertSame('berline', $type->slug);
    }

    public function test_a_viewer_cannot_create_a_vehicle_type(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->post('/types-vehicules', ['name' => 'Berline'])
            ->assertForbidden();
    }

    public function test_the_name_is_required(): void
    {
        $this->actingAsManager()
            ->post('/types-vehicules', ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    public function test_the_name_must_be_unique(): void
    {
        VehicleType::factory()->create(['name' => 'SUV']);

        $this->actingAsManager()
            ->post('/types-vehicules', ['name' => 'SUV'])
            ->assertSessionHasErrors('name');
    }

    public function test_a_manager_can_update_a_vehicle_type(): void
    {
        $type = VehicleType::factory()->create(['name' => 'Ancien nom']);

        $this->actingAsManager()
            ->put("/types-vehicules/{$type->id}", ['name' => 'Nouveau nom'])
            ->assertRedirect();

        $this->assertSame('Nouveau nom', $type->fresh()->name);
        $this->assertSame('nouveau-nom', $type->fresh()->slug);
    }

    public function test_a_vehicle_type_without_vehicles_can_be_deleted(): void
    {
        $type = VehicleType::factory()->create();

        $this->actingAsManager()
            ->delete("/types-vehicules/{$type->id}")
            ->assertRedirect();

        $this->assertModelMissing($type);
    }

    public function test_a_vehicle_type_with_vehicles_cannot_be_deleted(): void
    {
        $type = VehicleType::factory()->create();
        \App\Models\Vehicle::factory()->for($type)->create();

        $this->actingAsManager()
            ->delete("/types-vehicules/{$type->id}")
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertModelExists($type);
    }

    public function test_it_lists_vehicle_types(): void
    {
        VehicleType::factory()->create(['name' => 'SUV']);

        $this->actingAsManager()
            ->get('/types-vehicules')
            ->assertOk()
            ->assertSee('SUV');
    }
}
