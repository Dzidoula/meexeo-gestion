<?php
namespace Tests\Feature\Api;

use App\Models\HotelGallery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HotelGallerySyncDeleteApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.touvalem_sync.token' => 'test-secret-token']);
    }

    public function test_a_touvalem_origin_gallery_deletion_is_synced_in(): void
    {
        $gallery = HotelGallery::create([
            'image_path' => 'gallery/x.jpg', 'external_source' => 'residence_touvalem', 'external_id' => 4,
        ]);

        $this->postJson('/api/v1/touvalem-sync/galleries/delete', ['id' => 4], ['X-Sync-Token' => 'test-secret-token'])
            ->assertOk();

        $this->assertNull($gallery->fresh());
    }

    public function test_delete_sync_is_rejected_without_the_right_token(): void
    {
        $this->postJson('/api/v1/touvalem-sync/galleries/delete', ['id' => 4])->assertForbidden();
    }
}
