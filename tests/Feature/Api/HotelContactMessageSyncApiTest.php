<?php
namespace Tests\Feature\Api;

use App\Models\HotelContactMessage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HotelContactMessageSyncApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.touvalem_sync.token' => 'test-secret-token']);
    }

    public function test_a_new_contact_message_is_synced_in(): void
    {
        $this->postJson('/api/v1/touvalem-sync/contact-messages', [
            'id' => 1, 'first_name' => 'Awa', 'last_name' => 'Koné', 'email' => 'awa@example.com',
            'subject' => 'Question', 'message' => 'Bonjour', 'is_read' => false,
        ], ['X-Sync-Token' => 'test-secret-token'])->assertCreated();

        $this->assertSame(1, HotelContactMessage::count());
    }

    public function test_resyncing_the_same_message_does_not_duplicate_it(): void
    {
        $payload = ['id' => 1, 'first_name' => 'Awa', 'last_name' => 'Koné', 'email' => 'awa@example.com', 'subject' => 'Q', 'message' => 'Bonjour'];
        $headers = ['X-Sync-Token' => 'test-secret-token'];

        $this->postJson('/api/v1/touvalem-sync/contact-messages', $payload, $headers);
        $this->postJson('/api/v1/touvalem-sync/contact-messages', [...$payload, 'is_read' => true], $headers);

        $this->assertSame(1, HotelContactMessage::count());
        $this->assertTrue(HotelContactMessage::sole()->is_read);
    }

    public function test_a_touvalem_message_deletion_is_synced_in(): void
    {
        $message = HotelContactMessage::create([
            'first_name' => 'Awa', 'last_name' => 'Koné', 'email' => 'awa@example.com',
            'subject' => 'Q', 'message' => 'Bonjour', 'external_source' => 'residence_touvalem', 'external_id' => 1,
        ]);

        $this->postJson('/api/v1/touvalem-sync/contact-messages/delete', ['id' => 1], ['X-Sync-Token' => 'test-secret-token'])
            ->assertOk();

        $this->assertNull($message->fresh());
    }
}
