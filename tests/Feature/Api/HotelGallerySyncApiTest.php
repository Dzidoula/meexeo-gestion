<?php
namespace Tests\Feature\Api;

use App\Models\HotelGallery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HotelGallerySyncApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.touvalem_sync.token' => 'test-secret-token']);
    }

    public function test_a_touvalem_gallery_image_is_synced_in(): void
    {
        $this->postJson('/api/v1/touvalem-sync/galleries', [
            'id' => 4, 'image_path' => 'gallery/photo.jpg', 'title' => 'Jardin', 'category' => 'Extérieur',
        ], ['X-Sync-Token' => 'test-secret-token'])->assertCreated();

        $gallery = HotelGallery::where('external_source', 'residence_touvalem')->where('external_id', 4)->sole();
        $this->assertSame('Jardin', $gallery->title);
    }

    public function test_resyncing_the_same_image_updates_instead_of_duplicating(): void
    {
        $payload = ['id' => 4, 'image_path' => 'gallery/photo.jpg', 'title' => 'Jardin', 'category' => 'Extérieur'];
        $headers = ['X-Sync-Token' => 'test-secret-token'];

        $this->postJson('/api/v1/touvalem-sync/galleries', $payload, $headers);
        $this->postJson('/api/v1/touvalem-sync/galleries', [...$payload, 'title' => 'Jardin renommé'], $headers);

        $this->assertSame(1, HotelGallery::count());
        $this->assertSame('Jardin renommé', HotelGallery::sole()->title);
    }

    public function test_sync_is_rejected_without_the_right_token(): void
    {
        $this->postJson('/api/v1/touvalem-sync/galleries', ['id' => 1, 'image_path' => 'x.jpg'])
            ->assertForbidden();
    }
}
