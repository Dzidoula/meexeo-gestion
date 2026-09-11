<?php
// tests/Feature/PropertyPhotoTest.php
namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyPhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_manager_can_upload_a_photo_stored_on_the_public_disk(): void
    {
        Storage::fake('public');
        $property = Property::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->post("/biens/{$property->id}/photos", [
                'photo' => UploadedFile::fake()->image('villa.jpg', 1200, 800),
            ])
            ->assertRedirect();

        $photo = $property->fresh()->photos()->sole();

        Storage::disk('public')->assertExists($photo->path);
        // Le chemin est relatif au disque public, jamais un chemin absolu de domaine.
        $this->assertStringStartsWith('properties/photos/', $photo->path);
        $this->assertStringStartsWith('/storage/', $photo->url);
    }

    public function test_the_first_photo_becomes_the_primary_one(): void
    {
        Storage::fake('public');
        $property = Property::factory()->create();
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)->post("/biens/{$property->id}/photos", [
            'photo' => UploadedFile::fake()->image('un.jpg'),
        ]);
        $this->actingAs($manager)->post("/biens/{$property->id}/photos", [
            'photo' => UploadedFile::fake()->image('deux.jpg'),
        ]);

        $this->assertTrue($property->fresh()->photos()->orderBy('id')->first()->is_primary);
        $this->assertSame(1, $property->fresh()->photos()->where('is_primary', true)->count());
    }

    public function test_promoting_a_photo_demotes_the_previous_primary(): void
    {
        Storage::fake('public');
        $property = Property::factory()->create();
        $first = PropertyPhoto::factory()->for($property)->create(['is_primary' => true]);
        $second = PropertyPhoto::factory()->for($property)->create(['is_primary' => false]);

        $this->actingAs(User::factory()->manager()->create())
            ->patch("/biens/{$property->id}/photos/{$second->id}/principale")
            ->assertRedirect();

        $this->assertFalse($first->fresh()->is_primary);
        $this->assertTrue($second->fresh()->is_primary);
    }

    public function test_a_non_image_is_rejected(): void
    {
        Storage::fake('public');
        $property = Property::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->from("/biens/{$property->id}")
            ->post("/biens/{$property->id}/photos", [
                'photo' => UploadedFile::fake()->create('virus.exe', 10),
            ])
            ->assertSessionHasErrors('photo');

        $this->assertDatabaseCount('property_photos', 0);
    }

    public function test_deleting_a_photo_removes_the_file_too(): void
    {
        Storage::fake('public');
        $property = Property::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->post("/biens/{$property->id}/photos", ['photo' => UploadedFile::fake()->image('a.jpg')]);

        $photo = $property->fresh()->photos()->sole();

        $this->actingAs(User::factory()->manager()->create())
            ->delete("/biens/{$property->id}/photos/{$photo->id}")
            ->assertRedirect();

        Storage::disk('public')->assertMissing($photo->path);
        $this->assertDatabaseCount('property_photos', 0);
    }

    public function test_an_accountant_cannot_upload_a_photo(): void
    {
        Storage::fake('public');
        $property = Property::factory()->create();

        $this->actingAs(User::factory()->accountant()->create())
            ->post("/biens/{$property->id}/photos", ['photo' => UploadedFile::fake()->image('a.jpg')])
            ->assertForbidden();
    }
}
