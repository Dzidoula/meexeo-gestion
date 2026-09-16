<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VehiclePhotoTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsManager(): self
    {
        return $this->actingAs(User::factory()->manager()->create());
    }

    public function test_the_first_uploaded_photo_becomes_primary(): void
    {
        Storage::fake('public');
        $vehicle = Vehicle::factory()->create();

        $this->actingAsManager()
            ->post("/vehicules/{$vehicle->id}/photos", ['photo' => UploadedFile::fake()->image('voiture.jpg')])
            ->assertRedirect();

        $photo = $vehicle->photos()->sole();
        $this->assertTrue($photo->is_primary);
        Storage::disk('public')->assertExists($photo->path);
    }

    public function test_a_second_photo_is_not_primary_by_default(): void
    {
        Storage::fake('public');
        $vehicle = Vehicle::factory()->create();
        $vehicle->photos()->create(['path' => 'x.jpg', 'is_primary' => true, 'position' => 1]);

        $this->actingAsManager()
            ->post("/vehicules/{$vehicle->id}/photos", ['photo' => UploadedFile::fake()->image('voiture2.jpg')]);

        $this->assertSame(1, $vehicle->photos()->where('is_primary', true)->count());
    }

    public function test_a_viewer_cannot_upload_a_photo(): void
    {
        Storage::fake('public');
        $vehicle = Vehicle::factory()->create();

        $this->actingAs(User::factory()->viewer()->create())
            ->post("/vehicules/{$vehicle->id}/photos", ['photo' => UploadedFile::fake()->image('voiture.jpg')])
            ->assertForbidden();
    }

    public function test_a_manager_can_set_another_photo_as_primary(): void
    {
        Storage::fake('public');
        $vehicle = Vehicle::factory()->create();
        $first = $vehicle->photos()->create(['path' => 'a.jpg', 'is_primary' => true, 'position' => 1]);
        $second = $vehicle->photos()->create(['path' => 'b.jpg', 'is_primary' => false, 'position' => 2]);

        $this->actingAsManager()
            ->patch("/vehicules/{$vehicle->id}/photos/{$second->id}/principale")
            ->assertRedirect();

        $this->assertFalse($first->fresh()->is_primary);
        $this->assertTrue($second->fresh()->is_primary);
    }

    public function test_deleting_the_primary_photo_promotes_another_one(): void
    {
        Storage::fake('public');
        $vehicle = Vehicle::factory()->create();
        $first = $vehicle->photos()->create(['path' => 'a.jpg', 'is_primary' => true, 'position' => 1]);
        $second = $vehicle->photos()->create(['path' => 'b.jpg', 'is_primary' => false, 'position' => 2]);

        $this->actingAsManager()->delete("/vehicules/{$vehicle->id}/photos/{$first->id}");

        $this->assertModelMissing($first);
        $this->assertTrue($second->fresh()->is_primary);
    }

    public function test_deleting_a_vehicle_removes_its_photo_files(): void
    {
        Storage::fake('public');
        $vehicle = Vehicle::factory()->create();
        $path = $vehicle->photos()->create(['path' => 'vehicules/photos/a.jpg', 'is_primary' => true, 'position' => 1])->path;
        Storage::disk('public')->put($path, 'contenu');

        $vehicle->delete();

        Storage::disk('public')->assertMissing($path);
    }
}
