<?php
namespace Tests\Feature;

use App\Models\HotelFaq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HotelFaqTest extends TestCase
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

    public function test_a_manager_can_create_a_faq(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['synced' => true], 201)]);

        $this->actingAsManager()->post('/faq', [
            'question' => 'Quels sont les horaires ?', 'answer' => '24h/24.', 'order' => 1, 'is_active' => '1',
        ])->assertRedirect();

        $faq = HotelFaq::sole();
        $this->assertSame('Quels sont les horaires ?', $faq->question);
        Http::assertSent(fn ($r) => $r->url() === 'https://residencetouvalem.com/api/masterclays-sync/faqs');
    }

    public function test_a_manager_can_update_a_faq(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['synced' => true], 201)]);
        $faq = HotelFaq::create(['question' => 'Q', 'answer' => 'A', 'order' => 1]);

        $this->actingAsManager()->put("/faq/{$faq->id}", ['question' => 'Nouvelle question', 'answer' => 'A', 'order' => 1])
            ->assertRedirect();

        $this->assertSame('Nouvelle question', $faq->fresh()->question);
    }

    public function test_a_manager_can_delete_a_faq(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['deleted' => true])]);
        $faq = HotelFaq::create(['question' => 'Q', 'answer' => 'A', 'order' => 1]);

        $this->actingAsManager()->delete("/faq/{$faq->id}")->assertRedirect();

        $this->assertNull($faq->fresh());
        Http::assertSent(fn ($r) => str_ends_with($r->url(), '/faqs/delete'));
    }

    public function test_a_viewer_cannot_create_a_faq(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->post('/faq', ['question' => 'Q', 'answer' => 'A', 'order' => 1])
            ->assertForbidden();
    }
}
