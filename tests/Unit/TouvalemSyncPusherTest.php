<?php
namespace Tests\Unit;

use App\Models\HotelFaq;
use App\Services\TouvalemSyncPusher;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TouvalemSyncPusherTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.touvalem_sync.token' => 'test-secret-token']);
    }

    public function test_a_touvalem_origin_row_is_pushed_with_its_touvalem_id(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['synced' => true], 201)]);
        $faq = HotelFaq::create([
            'question' => 'Q', 'answer' => 'A',
            'external_source' => 'residence_touvalem', 'external_id' => 9,
        ]);

        app(TouvalemSyncPusher::class)->push('faqs', $faq, ['question' => 'Q', 'answer' => 'A']);

        Http::assertSent(fn ($request) =>
            $request->url() === 'https://residencetouvalem.com/api/masterclays-sync/faqs'
            && $request->hasHeader('X-Sync-Token', 'test-secret-token')
            && $request['touvalem_id'] === 9
            && !isset($request['masterclays_id'])
        );
    }

    public function test_a_masterclays_native_row_is_pushed_with_its_own_id(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['synced' => true], 201)]);
        $faq = HotelFaq::create(['question' => 'Q', 'answer' => 'A']);

        app(TouvalemSyncPusher::class)->push('faqs', $faq, ['question' => 'Q', 'answer' => 'A']);

        Http::assertSent(fn ($request) =>
            $request['masterclays_id'] === $faq->id && !isset($request['touvalem_id'])
        );
    }

    public function test_a_failed_push_is_logged_and_never_throws(): void
    {
        Http::fake(['residencetouvalem.com/*' => fn () => throw new \Illuminate\Http\Client\ConnectionException('timeout')]);
        Log::spy();
        $faq = HotelFaq::create(['question' => 'Q', 'answer' => 'A']);

        app(TouvalemSyncPusher::class)->push('faqs', $faq, ['question' => 'Q']);

        Log::shouldHaveReceived('warning')->once();
    }

    public function test_push_delete_sends_the_right_identity(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['deleted' => true])]);
        $faq = HotelFaq::create(['question' => 'Q', 'answer' => 'A', 'external_source' => 'residence_touvalem', 'external_id' => 3]);

        app(TouvalemSyncPusher::class)->pushDelete('faqs', $faq);

        Http::assertSent(fn ($request) =>
            $request->url() === 'https://residencetouvalem.com/api/masterclays-sync/faqs/delete'
            && $request['touvalem_id'] === 3
        );
    }
}
