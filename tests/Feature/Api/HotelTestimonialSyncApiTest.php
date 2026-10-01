<?php
namespace Tests\Feature\Api;

use App\Models\HotelTestimonial;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HotelTestimonialSyncApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.touvalem_sync.token' => 'test-secret-token']);
    }

    public function test_a_touvalem_testimonial_is_synced_in(): void
    {
        $this->postJson('/api/v1/touvalem-sync/testimonials', [
            'id' => 4, 'author_name' => 'Awa', 'rating' => 5, 'title' => 'Super', 'content' => 'Top', 'is_active' => true,
        ], ['X-Sync-Token' => 'test-secret-token'])->assertCreated();

        $testimonial = HotelTestimonial::where('external_source', 'residence_touvalem')->where('external_id', 4)->sole();
        $this->assertSame('Awa', $testimonial->author_name);
    }

    public function test_resyncing_the_same_testimonial_updates_instead_of_duplicating(): void
    {
        $payload = ['id' => 4, 'author_name' => 'Awa', 'rating' => 5, 'title' => 'Super', 'content' => 'Top', 'is_active' => true];
        $headers = ['X-Sync-Token' => 'test-secret-token'];

        $this->postJson('/api/v1/touvalem-sync/testimonials', $payload, $headers);
        $this->postJson('/api/v1/touvalem-sync/testimonials', [...$payload, 'title' => 'Titre renommé'], $headers);

        $this->assertSame(1, HotelTestimonial::count());
        $this->assertSame('Titre renommé', HotelTestimonial::sole()->title);
    }

    public function test_a_touvalem_testimonial_deletion_is_synced_in(): void
    {
        $testimonial = HotelTestimonial::create([
            'author_name' => 'Awa', 'rating' => 5, 'title' => 'Super', 'content' => 'Top',
            'external_source' => 'residence_touvalem', 'external_id' => 4,
        ]);

        $this->postJson('/api/v1/touvalem-sync/testimonials/delete', ['id' => 4], ['X-Sync-Token' => 'test-secret-token'])
            ->assertOk();

        $this->assertNull($testimonial->fresh());
    }

    public function test_sync_is_rejected_without_the_right_token(): void
    {
        $this->postJson('/api/v1/touvalem-sync/testimonials', ['id' => 1, 'author_name' => 'X'])
            ->assertForbidden();
    }
}
