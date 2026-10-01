<?php
namespace Tests\Feature;

use App\Models\HotelGallery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HotelGalleryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.touvalem_sync.token' => 'test-secret-token']);
    }

    private function actingAsManager(): self
    {
        return $this->actingAs(User::factory()->manager()->create());
    }

    public function test_a_manager_can_add_a_gallery_image(): void
    {
        Storage::fake('public');
        Http::fake(['residencetouvalem.com/*' => Http::response(['synced' => true], 201)]);

        $this->actingAsManager()->post('/galerie', [
            'image' => UploadedFile::fake()->image('vue.jpg'),
            'title' => 'Vue sur mer',
            'category' => 'Extérieur',
        ])->assertRedirect();

        $gallery = HotelGallery::sole();
        $this->assertSame('Vue sur mer', $gallery->title);
        Storage::disk('public')->assertExists($gallery->image_path);
        Http::assertSent(fn ($r) => $r->url() === 'https://residencetouvalem.com/api/masterclays-sync/galleries');
    }

    public function test_a_manager_can_delete_a_gallery_image(): void
    {
        Storage::fake('public');
        Http::fake(['residencetouvalem.com/*' => Http::response(['deleted' => true])]);
        $path = UploadedFile::fake()->image('x.jpg')->store('hotel-galleries', 'public');
        $gallery = HotelGallery::create(['image_path' => $path]);

        $this->actingAsManager()->delete("/galerie/{$gallery->id}")->assertRedirect();

        $this->assertNull($gallery->fresh());
        Storage::disk('public')->assertMissing($path);
        Http::assertSent(fn ($r) => str_ends_with($r->url(), '/galleries/delete'));
    }

    public function test_a_viewer_cannot_add_a_gallery_image(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->viewer()->create())
            ->post('/galerie', ['image' => UploadedFile::fake()->image('x.jpg')])
            ->assertForbidden();
    }
}
