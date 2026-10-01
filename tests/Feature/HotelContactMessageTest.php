<?php
namespace Tests\Feature;

use App\Models\HotelContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HotelContactMessageTest extends TestCase
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

    public function test_opening_a_message_marks_it_read_and_pushes_the_change(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['synced' => true], 201)]);
        $message = HotelContactMessage::create([
            'first_name' => 'Awa', 'last_name' => 'Koné', 'email' => 'awa@example.com',
            'subject' => 'Question', 'message' => 'Bonjour', 'is_read' => false,
        ]);

        $this->actingAsManager()->get("/messages-contact/{$message->id}")->assertOk();

        $this->assertTrue($message->fresh()->is_read);
        Http::assertSent(fn ($r) => $r->url() === 'https://residencetouvalem.com/api/masterclays-sync/contact-messages');
    }

    public function test_a_manager_can_delete_a_message(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['deleted' => true])]);
        $message = HotelContactMessage::create([
            'first_name' => 'Awa', 'last_name' => 'Koné', 'email' => 'awa@example.com',
            'subject' => 'Question', 'message' => 'Bonjour',
        ]);

        $this->actingAsManager()->delete("/messages-contact/{$message->id}")->assertRedirect();

        $this->assertNull($message->fresh());
        Http::assertSent(fn ($r) => str_ends_with($r->url(), '/contact-messages/delete'));
    }
}
