<?php
namespace Tests\Feature;

use App\Models\HotelNewsletterSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HotelNewsletterSubscriberTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.touvalem_sync.token' => 'test-secret-token']);
    }

    public function test_a_manager_can_delete_a_subscriber(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['deleted' => true])]);
        $subscriber = HotelNewsletterSubscriber::create(['email' => 'client@example.com']);

        $this->actingAs(User::factory()->manager()->create())
            ->delete("/newsletter/{$subscriber->id}")
            ->assertRedirect();

        $this->assertNull($subscriber->fresh());
        Http::assertSent(fn ($r) => str_ends_with($r->url(), '/newsletter-subscribers/delete'));
    }
}
