<?php
namespace Tests\Feature\Api;

use App\Models\HotelFaq;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HotelFaqSyncApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.touvalem_sync.token' => 'test-secret-token']);
    }

    public function test_a_touvalem_faq_is_synced_in(): void
    {
        $this->postJson('/api/v1/touvalem-sync/faqs', [
            'id' => 4, 'question' => 'Q', 'answer' => 'A', 'order' => 2, 'is_active' => true,
        ], ['X-Sync-Token' => 'test-secret-token'])->assertCreated();

        $faq = HotelFaq::where('external_source', 'residence_touvalem')->where('external_id', 4)->sole();
        $this->assertSame('Q', $faq->question);
    }

    public function test_resyncing_the_same_faq_updates_instead_of_duplicating(): void
    {
        $payload = ['id' => 4, 'question' => 'Q', 'answer' => 'A', 'order' => 2, 'is_active' => true];
        $headers = ['X-Sync-Token' => 'test-secret-token'];

        $this->postJson('/api/v1/touvalem-sync/faqs', $payload, $headers);
        $this->postJson('/api/v1/touvalem-sync/faqs', [...$payload, 'question' => 'Question renommée'], $headers);

        $this->assertSame(1, HotelFaq::count());
        $this->assertSame('Question renommée', HotelFaq::sole()->question);
    }

    public function test_a_touvalem_faq_deletion_is_synced_in(): void
    {
        $faq = HotelFaq::create(['question' => 'Q', 'answer' => 'A', 'external_source' => 'residence_touvalem', 'external_id' => 4]);

        $this->postJson('/api/v1/touvalem-sync/faqs/delete', ['id' => 4], ['X-Sync-Token' => 'test-secret-token'])->assertOk();

        $this->assertNull($faq->fresh());
    }

    public function test_sync_is_rejected_without_the_right_token(): void
    {
        $this->postJson('/api/v1/touvalem-sync/faqs', ['id' => 1, 'question' => 'X'])->assertForbidden();
    }
}
