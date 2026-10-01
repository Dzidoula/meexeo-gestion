<?php

namespace Tests\Feature\Api;

use App\Models\HotelRoom;
use App\Models\HotelRoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelRoomTypeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_room_types_with_the_same_field_names_as_the_touvalem_contract(): void
    {
        HotelRoomType::factory()->create([
            'name' => 'Chambre Standard',
            'description' => 'Une belle chambre.',
            'base_price' => 150000,
            'rating' => 4.9,
            'capacity' => 2,
            'bed_count' => 1,
            'bath_count' => 1,
            'area' => 28,
            'images' => ['/images/a.jpg'],
            'amenities' => ['Wi-Fi gratuit'],
        ]);

        $response = $this->getJson('/api/v1/room-types');

        $response->assertStatus(200)->assertJsonCount(1);
        $response->assertJsonFragment([
            'name' => 'Chambre Standard',
            'description' => 'Une belle chambre.',
            'basePrice' => 150000.0,
            'rating' => 4.9,
            'capacity' => 2,
            'bedCount' => 1,
            'bathCount' => 1,
            'area' => 28,
        ]);
        $response->assertJsonPath('0.images', ['/images/a.jpg']);
        $response->assertJsonPath('0.amenities', ['Wi-Fi gratuit']);
    }

    public function test_rating_is_null_when_unknown_not_defaulted(): void
    {
        HotelRoomType::factory()->create(['name' => 'Suite Royale', 'rating' => null]);

        $response = $this->getJson('/api/v1/room-types');

        $response->assertJsonFragment(['name' => 'Suite Royale', 'rating' => null]);
    }

    public function test_shows_a_single_room_type_with_similar_room_types(): void
    {
        $type = HotelRoomType::factory()->create(['name' => 'Chambre Deluxe']);
        HotelRoomType::factory()->count(3)->create();

        $response = $this->getJson("/api/v1/room-types/{$type->id}");

        $response->assertStatus(200)
            ->assertJsonPath('name', 'Chambre Deluxe')
            ->assertJsonCount(3, 'similarRoomTypes');
    }

    public function test_returns_404_for_a_missing_room_type(): void
    {
        $response = $this->getJson('/api/v1/room-types/999999');

        $response->assertStatus(404);
    }

    public function test_images_default_to_an_empty_array_when_the_imported_type_has_none(): void
    {
        HotelRoomType::factory()->create(['name' => 'Chambre Sans Photos', 'images' => null, 'amenities' => null]);

        $response = $this->getJson('/api/v1/room-types');

        $response->assertJsonFragment(['name' => 'Chambre Sans Photos', 'images' => [], 'amenities' => []]);
    }
}
