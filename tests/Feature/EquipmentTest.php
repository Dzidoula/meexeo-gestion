<?php
namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipmentTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsManager(): self
    {
        return $this->actingAs(User::factory()->manager()->create());
    }

    public function test_a_guest_cannot_see_the_equipment_list(): void
    {
        $this->get('/equipements')->assertRedirect('/connexion');
    }

    public function test_a_manager_can_create_equipment(): void
    {
        $this->actingAsManager()
            ->post('/equipements', ['name' => 'Chaises pliantes', 'quantity_total' => 200])
            ->assertRedirect();

        $equipment = Equipment::sole();
        $this->assertSame('Chaises pliantes', $equipment->name);
        $this->assertSame(200, $equipment->quantity_total);
    }

    public function test_a_viewer_cannot_create_equipment(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->post('/equipements', ['name' => 'Chaises pliantes', 'quantity_total' => 200])
            ->assertForbidden();
    }

    public function test_the_name_is_required(): void
    {
        $this->actingAsManager()
            ->post('/equipements', ['name' => '', 'quantity_total' => 10])
            ->assertSessionHasErrors('name');
    }

    public function test_the_name_must_be_unique(): void
    {
        Equipment::factory()->create(['name' => 'Podium modulaire']);

        $this->actingAsManager()
            ->post('/equipements', ['name' => 'Podium modulaire', 'quantity_total' => 5])
            ->assertSessionHasErrors('name');
    }

    public function test_the_quantity_cannot_be_negative(): void
    {
        $this->actingAsManager()
            ->post('/equipements', ['name' => 'Tente de réception', 'quantity_total' => -1])
            ->assertSessionHasErrors('quantity_total');
    }

    public function test_a_manager_can_update_equipment(): void
    {
        $equipment = Equipment::factory()->create(['quantity_total' => 10]);

        $this->actingAsManager()
            ->put("/equipements/{$equipment->id}", ['name' => $equipment->name, 'quantity_total' => 25])
            ->assertRedirect();

        $this->assertSame(25, $equipment->fresh()->quantity_total);
    }

    public function test_a_manager_can_delete_equipment(): void
    {
        $equipment = Equipment::factory()->create();

        $this->actingAsManager()
            ->delete("/equipements/{$equipment->id}")
            ->assertRedirect();

        $this->assertNull($equipment->fresh());
    }
}
