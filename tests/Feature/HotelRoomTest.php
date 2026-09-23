<?php
namespace Tests\Feature;

use App\Enums\HotelRoomStatus;
use App\Models\HotelRoom;
use App\Models\HotelRoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelRoomTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsManager(): self
    {
        return $this->actingAs(User::factory()->manager()->create());
    }

    public function test_a_guest_cannot_see_the_room_list(): void
    {
        $this->get('/chambres')->assertRedirect('/connexion');
    }

    public function test_it_lists_rooms_with_their_type_and_status(): void
    {
        $type = HotelRoomType::factory()->create(['name' => 'Suite']);
        HotelRoom::factory()->for($type, 'hotelRoomType')->create(['number' => '101', 'status' => HotelRoomStatus::Available]);

        $this->actingAsManager()
            ->get('/chambres')
            ->assertOk()
            ->assertSee('101')
            ->assertSee('Suite')
            ->assertSee('Disponible');
    }

    public function test_it_links_to_the_stays_and_room_types_pages(): void
    {
        $this->actingAsManager()
            ->get('/chambres')
            ->assertOk()
            ->assertSee(route('hotel-stays.index'), false)
            ->assertSee(route('hotel-room-types.index'), false);
    }

    public function test_it_filters_by_status(): void
    {
        HotelRoom::factory()->create(['number' => 'ModeleDispo', 'status' => HotelRoomStatus::Available]);
        HotelRoom::factory()->create(['number' => 'ModeleMaint', 'status' => HotelRoomStatus::Maintenance]);

        $this->actingAsManager()
            ->get('/chambres?status=maintenance_chambre')
            ->assertSee('ModeleMaint')
            ->assertDontSee('ModeleDispo');
    }

    public function test_a_manager_can_create_a_room(): void
    {
        $type = HotelRoomType::factory()->create();

        $this->actingAsManager()
            ->post('/chambres', [
                'hotel_room_type_id' => $type->id,
                'number' => '204',
                'nightly_rate' => 45000,
                'amenities' => 'Climatisation, Wifi',
                'status' => 'disponible_chambre',
            ])
            ->assertRedirect();

        $room = HotelRoom::sole();
        $this->assertSame('204', $room->number);
        $this->assertSame(45000, $room->nightly_rate);
        $this->assertSame(HotelRoomStatus::Available, $room->status);
    }

    public function test_a_viewer_cannot_create_a_room(): void
    {
        $type = HotelRoomType::factory()->create();

        $this->actingAs(User::factory()->viewer()->create())
            ->post('/chambres', [
                'hotel_room_type_id' => $type->id,
                'number' => '204',
                'nightly_rate' => 45000,
                'status' => 'disponible_chambre',
            ])
            ->assertForbidden();
    }

    public function test_the_nightly_rate_cannot_be_negative(): void
    {
        $type = HotelRoomType::factory()->create();

        $this->actingAsManager()
            ->post('/chambres', [
                'hotel_room_type_id' => $type->id,
                'number' => '204',
                'nightly_rate' => -1000,
                'status' => 'disponible_chambre',
            ])
            ->assertSessionHasErrors('nightly_rate');
    }

    public function test_a_room_number_must_be_unique(): void
    {
        $type = HotelRoomType::factory()->create();
        HotelRoom::factory()->for($type, 'hotelRoomType')->create(['number' => '101']);

        $this->actingAsManager()
            ->post('/chambres', [
                'hotel_room_type_id' => $type->id,
                'number' => '101',
                'nightly_rate' => 30000,
                'status' => 'disponible_chambre',
            ])
            ->assertSessionHasErrors('number');
    }

    public function test_a_manager_can_view_a_room(): void
    {
        $room = HotelRoom::factory()->create(['number' => '305']);

        $this->actingAsManager()
            ->get("/chambres/{$room->id}")
            ->assertOk()
            ->assertSee('305');
    }

    public function test_a_manager_can_update_a_room_keeping_its_own_number(): void
    {
        $type = HotelRoomType::factory()->create();
        $room = HotelRoom::factory()->for($type, 'hotelRoomType')->create(['number' => '101', 'nightly_rate' => 30000]);

        // Le numéro ne change pas : si la règle « unique en ignorant soi-même »
        // référence le mauvais nom de paramètre de route, cette requête échoue
        // à tort avec « ce numéro existe déjà ».
        $this->actingAsManager()
            ->put("/chambres/{$room->id}", [
                'hotel_room_type_id' => $type->id,
                'number' => '101',
                'nightly_rate' => 35000,
                'status' => 'disponible_chambre',
            ])
            ->assertRedirect()
            ->assertSessionDoesntHaveErrors();

        $this->assertSame(35000, $room->fresh()->nightly_rate);
    }
}
