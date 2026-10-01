<?php
namespace Tests\Feature\Api;

use App\Models\HotelNewsletterSubscriber;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HotelNewsletterSubscriberSyncApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.touvalem_sync.token' => 'test-secret-token']);
    }

    public function test_a_new_subscriber_is_synced_in(): void
    {
        $this->postJson('/api/v1/touvalem-sync/newsletter-subscribers', ['id' => 1, 'email' => 'client@example.com'], ['X-Sync-Token' => 'test-secret-token'])
            ->assertCreated();

        $this->assertSame(1, HotelNewsletterSubscriber::count());
    }

    public function test_resyncing_the_same_email_does_not_duplicate_it(): void
    {
        $payload = ['id' => 1, 'email' => 'client@example.com'];
        $headers = ['X-Sync-Token' => 'test-secret-token'];

        $this->postJson('/api/v1/touvalem-sync/newsletter-subscribers', $payload, $headers);
        $this->postJson('/api/v1/touvalem-sync/newsletter-subscribers', $payload, $headers);

        $this->assertSame(1, HotelNewsletterSubscriber::count());
    }

    public function test_a_touvalem_subscriber_deletion_is_synced_in(): void
    {
        $subscriber = HotelNewsletterSubscriber::create(['email' => 'client@example.com', 'external_source' => 'residence_touvalem', 'external_id' => 1]);

        $this->postJson('/api/v1/touvalem-sync/newsletter-subscribers/delete', ['id' => 1], ['X-Sync-Token' => 'test-secret-token'])->assertOk();

        $this->assertNull($subscriber->fresh());
    }
}
