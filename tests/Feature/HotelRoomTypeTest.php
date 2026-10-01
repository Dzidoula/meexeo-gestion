<?php
namespace Tests\Feature;

use App\Models\HotelRoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_a_manager_can_set_the_real_content_fields(): void
    {
        $this->actingAsManager()
            ->post('/types-chambres', [
                'name' => 'Suite Royale',
                'description' => 'Une suite spacieuse avec vue sur mer.',
                'base_price' => 600000,
                'rating' => 4.8,
                'capacity' => 4,
                'bed_count' => 4,
                'bath_count' => 4,
                'area' => 76,
                'amenities' => "Wi-Fi gratuit\nClimatisation\nMini-bar",
            ])
            ->assertRedirect();

        $type = HotelRoomType::sole();
        $this->assertSame('Une suite spacieuse avec vue sur mer.', $type->description);
        $this->assertSame(600000, $type->base_price);
        $this->assertSame(4.8, $type->rating);
        $this->assertSame(4, $type->capacity);
        $this->assertSame(['Wi-Fi gratuit', 'Climatisation', 'Mini-bar'], $type->amenities);
    }

    public function test_the_real_content_fields_are_optional(): void
    {
        $this->actingAsManager()
            ->post('/types-chambres', ['name' => 'Chambre Simple'])
            ->assertRedirect();

        $type = HotelRoomType::sole();
        $this->assertNull($type->description);
        $this->assertNull($type->rating);
        $this->assertNull($type->amenities);
    }

    public function test_the_rating_must_stay_within_zero_and_five(): void
    {
        $this->actingAsManager()
            ->post('/types-chambres', ['name' => 'Chambre Test', 'rating' => 7.2])
            ->assertSessionHasErrors('rating');
    }

    public function test_the_edit_form_shows_the_existing_real_content(): void
    {
        $type = HotelRoomType::factory()->create([
            'name' => 'Chambre Perle',
            'description' => 'Une vraie description.',
            'amenities' => ['Wi-Fi gratuit', 'Climatisation'],
        ]);

        $this->actingAsManager()
            ->get("/types-chambres/{$type->id}/modifier")
            ->assertOk()
            ->assertSee('Une vraie description.')
            ->assertSee('Wi-Fi gratuit');
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

    public function test_a_manager_can_upload_photos_when_creating_a_room_type(): void
    {
        Storage::fake('public');

        $this->actingAsManager()
            ->post('/types-chambres', [
                'name' => 'Suite avec Photos',
                'photos' => [
                    UploadedFile::fake()->image('vue-mer.jpg'),
                    UploadedFile::fake()->image('salon.jpg'),
                ],
            ])
            ->assertRedirect();

        $type = HotelRoomType::sole();
        $this->assertCount(2, $type->images);
        foreach ($type->images as $path) {
            Storage::disk('public')->assertExists(ltrim(str_replace('/storage/', '', $path), '/'));
        }
    }

    public function test_uploaded_photos_are_appended_to_existing_ones_on_update(): void
    {
        Storage::fake('public');
        $type = HotelRoomType::factory()->create(['images' => ['/storage/rooms/gallery/ancienne.jpg']]);

        $this->actingAsManager()
            ->put("/types-chambres/{$type->id}", [
                'name' => $type->name,
                'photos' => [UploadedFile::fake()->image('nouvelle.jpg')],
            ])
            ->assertRedirect();

        $images = $type->fresh()->images;
        $this->assertCount(2, $images);
        $this->assertContains('/storage/rooms/gallery/ancienne.jpg', $images);
    }

    public function test_a_photo_must_be_a_real_image(): void
    {
        Storage::fake('public');

        $this->actingAsManager()
            ->post('/types-chambres', [
                'name' => 'Suite Invalide',
                'photos' => [UploadedFile::fake()->create('document.pdf', 100)],
            ])
            ->assertSessionHasErrors('photos.0');

        $this->assertSame(0, HotelRoomType::count());
    }
}
