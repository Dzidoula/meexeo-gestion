<?php
namespace Tests\Feature;

use App\Models\HotelTestimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HotelTestimonialTest extends TestCase
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

    public function test_a_manager_can_create_a_testimonial(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['synced' => true], 201)]);

        $this->actingAsManager()->post('/temoignages', [
            'author_name' => 'Jean Kouassi',
            'rating' => 4.5,
            'title' => 'Excellent séjour',
            'content' => 'Tout était parfait.',
            'is_active' => '1',
        ])->assertRedirect();

        $testimonial = HotelTestimonial::sole();
        $this->assertSame('Jean Kouassi', $testimonial->author_name);
        $this->assertTrue($testimonial->is_active);
        Http::assertSent(fn ($r) => $r->url() === 'https://residencetouvalem.com/api/masterclays-sync/testimonials');
    }

    public function test_a_manager_can_update_a_testimonial(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['synced' => true], 201)]);
        $testimonial = HotelTestimonial::create([
            'author_name' => 'Jean', 'rating' => 5, 'title' => 'Ancien', 'content' => 'Ancien contenu',
        ]);

        $this->actingAsManager()->put("/temoignages/{$testimonial->id}", [
            'author_name' => 'Jean', 'rating' => 5, 'title' => 'Nouveau titre', 'content' => 'Nouveau contenu',
        ])->assertRedirect();

        $this->assertSame('Nouveau titre', $testimonial->fresh()->title);
        $this->assertFalse($testimonial->fresh()->is_active);
        Http::assertSent(fn ($r) => $r->url() === 'https://residencetouvalem.com/api/masterclays-sync/testimonials');
    }

    public function test_a_manager_can_delete_a_testimonial(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['deleted' => true])]);
        $testimonial = HotelTestimonial::create([
            'author_name' => 'Jean', 'rating' => 5, 'title' => 'T', 'content' => 'C',
        ]);

        $this->actingAsManager()->delete("/temoignages/{$testimonial->id}")->assertRedirect();

        $this->assertNull($testimonial->fresh());
        Http::assertSent(fn ($r) => str_ends_with($r->url(), '/testimonials/delete'));
    }

    public function test_a_viewer_cannot_create_a_testimonial(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->post('/temoignages', ['author_name' => 'Jean', 'rating' => 5, 'title' => 'T', 'content' => 'C'])
            ->assertForbidden();
    }
}
