<?php
namespace Tests\Feature;

use App\Models\HotelRoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelRoomTypeTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsManager(): self
    {
        return $this->actingAs(User::factory()->manager()->create());
    }

    public function test_a_guest_cannot_see_the_room_type_list(): void
    {
        $this->get('/types-chambres')->assertRedirect('/connexion');
    }

    public function test_a_manager_can_create_a_room_type(): void
    {
        $this->actingAsManager()
            ->post('/types-chambres', ['name' => 'Suite'])
            ->assertRedirect();

        $type = HotelRoomType::sole();
        $this->assertSame('Suite', $type->name);
        $this->assertSame('suite', $type->slug);
    }

    public function test_a_viewer_cannot_create_a_room_type(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->post('/types-chambres', ['name' => 'Suite'])
            ->assertForbidden();
    }

    public function test_the_name_is_required(): void
    {
        $this->actingAsManager()
            ->post('/types-chambres', ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    public function test_the_name_must_be_unique(): void
    {
        HotelRoomType::factory()->create(['name' => 'Standard']);

        $this->actingAsManager()
            ->post('/types-chambres', ['name' => 'Standard'])
            ->assertSessionHasErrors('name');
    }

    public function test_a_manager_can_update_a_room_type(): void
    {
        $type = HotelRoomType::factory()->create(['name' => 'Ancien nom']);

        $this->actingAsManager()
            ->put("/types-chambres/{$type->id}", ['name' => 'Nouveau nom'])
            ->assertRedirect();

        $this->assertSame('Nouveau nom', $type->fresh()->name);
    }

    public function test_a_room_type_with_rooms_cannot_be_deleted(): void
    {
        $type = HotelRoomType::factory()->create();
        \App\Models\HotelRoom::factory()->for($type, 'hotelRoomType')->create();

        $this->actingAsManager()
            ->delete("/types-chambres/{$type->id}")
            ->assertRedirect();

        $this->assertNotNull($type->fresh());
    }

    public function test_a_room_type_without_rooms_can_be_deleted(): void
    {
        $type = HotelRoomType::factory()->create();

        $this->actingAsManager()
            ->delete("/types-chambres/{$type->id}")
            ->assertRedirect();

        $this->assertNull($type->fresh());
    }
}
