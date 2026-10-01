<?php
namespace Tests\Feature;

use App\Enums\HotelStayStatus;
use App\Models\HotelStay;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HotelStayConfirmationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.touvalem_sync.token' => 'test-secret-token']);
    }

    private function pendingTouvalemStay(): HotelStay
    {
        return HotelStay::factory()->create([
            'status' => HotelStayStatus::Reserved,
            'confirmation_status' => 'pending',
            'external_source' => 'residence_touvalem',
            'external_id' => 42,
        ]);
    }

    public function test_a_manager_can_confirm_a_pending_stay(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['synced' => true], 201)]);
        $stay = $this->pendingTouvalemStay();

        $this->actingAs(User::factory()->manager()->create())
            ->patch(route('hotel-stays.confirm', $stay))
            ->assertRedirect();

        $this->assertSame('confirmed', $stay->fresh()->confirmation_status);
        Http::assertSent(fn ($r) =>
            $r->url() === 'https://residencetouvalem.com/api/masterclays-sync/booking-status'
            && $r['touvalem_id'] === 42
            && $r['status'] === 'confirmed'
        );
    }

    public function test_a_manager_can_refuse_a_pending_stay(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['synced' => true], 201)]);
        $stay = $this->pendingTouvalemStay();

        $this->actingAs(User::factory()->manager()->create())
            ->patch(route('hotel-stays.refuse', $stay))
            ->assertRedirect();

        $fresh = $stay->fresh();
        $this->assertSame('refused', $fresh->confirmation_status);
        $this->assertSame(HotelStayStatus::Cancelled, $fresh->status);
        Http::assertSent(fn ($r) => $r['status'] === 'cancelled');
    }

    public function test_a_stay_that_is_not_pending_cannot_be_confirmed_again(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['synced' => true], 201)]);
        $stay = HotelStay::factory()->create([
            'status' => HotelStayStatus::Reserved,
            'confirmation_status' => 'confirmed',
            'external_source' => 'residence_touvalem',
            'external_id' => 42,
        ]);

        $this->actingAs(User::factory()->manager()->create())
            ->patch(route('hotel-stays.confirm', $stay))
            ->assertRedirect();

        Http::assertNothingSent();
    }

    public function test_refusing_a_stay_already_in_progress_frees_its_room(): void
    {
        Http::fake(['residencetouvalem.com/*' => Http::response(['synced' => true], 201)]);
        $room = \App\Models\HotelRoom::factory()->create(['status' => \App\Enums\HotelRoomStatus::Occupied]);
        $stay = HotelStay::factory()->create([
            'hotel_room_id' => $room->id,
            'status' => HotelStayStatus::InProgress,
            'confirmation_status' => 'pending',
            'external_source' => 'residence_touvalem',
            'external_id' => 42,
        ]);

        $this->actingAs(User::factory()->manager()->create())
            ->patch(route('hotel-stays.refuse', $stay))
            ->assertRedirect();

        $this->assertSame(\App\Enums\HotelRoomStatus::Available, $room->fresh()->status);
    }

    public function test_cancelling_a_stay_clears_a_pending_confirmation_status_so_it_cannot_be_confirmed_afterward(): void
    {
        $stay = $this->pendingTouvalemStay();

        $this->actingAs(User::factory()->manager()->create())
            ->patch(route('hotel-stays.cancel', $stay))
            ->assertRedirect();

        $this->assertNull($stay->fresh()->confirmation_status);
        $this->get(route('hotel-stays.show', $stay))->assertDontSee('Confirmer');
    }

    public function test_the_confirm_and_refuse_buttons_only_show_for_pending_stays(): void
    {
        $pending = $this->pendingTouvalemStay();
        $native = HotelStay::factory()->create(['status' => HotelStayStatus::Reserved, 'confirmation_status' => null]);

        $this->actingAs(User::factory()->manager()->create());

        $this->get(route('hotel-stays.show', $pending))
            ->assertSee('Confirmer')
            ->assertSee('Refuser');

        $this->get(route('hotel-stays.show', $native))
            ->assertDontSee('Confirmer')
            ->assertDontSee('Refuser');
    }
}
