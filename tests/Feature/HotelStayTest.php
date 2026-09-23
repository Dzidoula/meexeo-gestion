<?php
namespace Tests\Feature;

use App\Enums\HotelRoomStatus;
use App\Enums\HotelStayStatus;
use App\Models\HotelRoom;
use App\Models\HotelStay;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelStayTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsManager(): self
    {
        return $this->actingAs(User::factory()->manager()->create());
    }

    public function test_a_guest_cannot_see_the_stay_list(): void
    {
        $this->get('/sejours')->assertRedirect('/connexion');
    }

    public function test_a_manager_can_create_a_stay(): void
    {
        $room = HotelRoom::factory()->create(['status' => HotelRoomStatus::Available]);

        $this->actingAsManager()
            ->post('/sejours', [
                'hotel_room_id' => $room->id,
                'guest_name' => 'Awa Koné',
                'guest_phone' => '0102030405',
                'arrival_date' => '2026-10-10',
                'departure_date' => '2026-10-12',
                'total_amount' => 90000,
                'deposit_amount' => 30000,
            ])
            ->assertRedirect();

        $stay = HotelStay::sole();
        $this->assertSame('Awa Koné', $stay->guest_name);
        $this->assertSame(HotelStayStatus::Reserved, $stay->status);
    }

    public function test_creating_a_stay_without_dates_does_not_crash(): void
    {
        $room = HotelRoom::factory()->create();

        $this->actingAsManager()
            ->post('/sejours', [
                'hotel_room_id' => $room->id,
                'guest_name' => 'Awa Koné',
                'guest_phone' => '0102030405',
                'total_amount' => 90000,
                'deposit_amount' => 30000,
            ])
            ->assertSessionHasErrors(['arrival_date', 'departure_date']);

        $this->assertSame(0, HotelStay::count());
    }

    public function test_a_viewer_cannot_create_a_stay(): void
    {
        $room = HotelRoom::factory()->create();

        $this->actingAs(User::factory()->viewer()->create())
            ->post('/sejours', [
                'hotel_room_id' => $room->id,
                'guest_name' => 'Awa Koné',
                'guest_phone' => '0102030405',
                'arrival_date' => '2026-10-10',
                'departure_date' => '2026-10-12',
                'total_amount' => 90000,
                'deposit_amount' => 0,
            ])
            ->assertForbidden();
    }

    public function test_the_departure_date_must_be_after_the_arrival_date(): void
    {
        $room = HotelRoom::factory()->create();

        $this->actingAsManager()
            ->post('/sejours', [
                'hotel_room_id' => $room->id,
                'guest_name' => 'Awa Koné',
                'guest_phone' => '0102030405',
                'arrival_date' => '2026-10-10',
                'departure_date' => '2026-10-10',
                'total_amount' => 90000,
                'deposit_amount' => 0,
            ])
            ->assertSessionHasErrors('departure_date');
    }

    public function test_a_room_cannot_be_double_booked_on_overlapping_dates(): void
    {
        $room = HotelRoom::factory()->create();
        HotelStay::factory()->for($room, 'room')->create([
            'arrival_date' => '2026-10-10',
            'departure_date' => '2026-10-15',
            'status' => HotelStayStatus::Reserved,
        ]);

        $this->actingAsManager()
            ->post('/sejours', [
                'hotel_room_id' => $room->id,
                'guest_name' => 'Yao Michel',
                'guest_phone' => '0102030406',
                'arrival_date' => '2026-10-12',
                'departure_date' => '2026-10-18',
                'total_amount' => 90000,
                'deposit_amount' => 0,
            ])
            ->assertSessionHasErrors('hotel_room_id');
    }

    public function test_a_room_can_be_booked_on_non_overlapping_dates(): void
    {
        $room = HotelRoom::factory()->create();
        HotelStay::factory()->for($room, 'room')->create([
            'arrival_date' => '2026-10-10',
            'departure_date' => '2026-10-15',
            'status' => HotelStayStatus::Reserved,
        ]);

        $this->actingAsManager()
            ->post('/sejours', [
                'hotel_room_id' => $room->id,
                'guest_name' => 'Yao Michel',
                'guest_phone' => '0102030406',
                'arrival_date' => '2026-10-15',
                'departure_date' => '2026-10-18',
                'total_amount' => 90000,
                'deposit_amount' => 0,
            ])
            ->assertRedirect();

        $this->assertSame(2, HotelStay::count());
    }

    public function test_a_cancelled_stay_does_not_block_the_same_dates(): void
    {
        $room = HotelRoom::factory()->create();
        HotelStay::factory()->for($room, 'room')->create([
            'arrival_date' => '2026-10-10',
            'departure_date' => '2026-10-15',
            'status' => HotelStayStatus::Cancelled,
        ]);

        $this->actingAsManager()
            ->post('/sejours', [
                'hotel_room_id' => $room->id,
                'guest_name' => 'Yao Michel',
                'guest_phone' => '0102030406',
                'arrival_date' => '2026-10-10',
                'departure_date' => '2026-10-15',
                'total_amount' => 90000,
                'deposit_amount' => 0,
            ])
            ->assertRedirect();
    }

    public function test_the_total_amount_cannot_be_negative(): void
    {
        $room = HotelRoom::factory()->create();

        $this->actingAsManager()
            ->post('/sejours', [
                'hotel_room_id' => $room->id,
                'guest_name' => 'Awa Koné',
                'guest_phone' => '0102030405',
                'arrival_date' => '2026-10-10',
                'departure_date' => '2026-10-12',
                'total_amount' => -1000,
                'deposit_amount' => 0,
            ])
            ->assertSessionHasErrors('total_amount');
    }

    public function test_check_in_marks_the_stay_in_progress_and_the_room_occupied(): void
    {
        $room = HotelRoom::factory()->create(['status' => HotelRoomStatus::Available]);
        $stay = HotelStay::factory()->for($room, 'room')->create(['status' => HotelStayStatus::Reserved]);

        $this->actingAsManager()
            ->patch("/sejours/{$stay->id}/arrivee")
            ->assertRedirect();

        $stay->refresh();
        $this->assertSame(HotelStayStatus::InProgress, $stay->status);
        $this->assertNotNull($stay->checked_in_at);
        $this->assertSame(HotelRoomStatus::Occupied, $room->fresh()->status);
    }

    public function test_check_out_marks_the_stay_completed_and_the_room_available(): void
    {
        $room = HotelRoom::factory()->create(['status' => HotelRoomStatus::Occupied]);
        $stay = HotelStay::factory()->for($room, 'room')->create([
            'status' => HotelStayStatus::InProgress,
            'checked_in_at' => now(),
        ]);

        $this->actingAsManager()
            ->patch("/sejours/{$stay->id}/depart")
            ->assertRedirect();

        $stay->refresh();
        $this->assertSame(HotelStayStatus::Completed, $stay->status);
        $this->assertNotNull($stay->checked_out_at);
        $this->assertSame(HotelRoomStatus::Available, $room->fresh()->status);
    }

    public function test_check_out_is_refused_if_the_stay_was_never_checked_in(): void
    {
        $stay = HotelStay::factory()->create(['status' => HotelStayStatus::Reserved]);

        $this->actingAsManager()
            ->patch("/sejours/{$stay->id}/depart")
            ->assertRedirect();

        $this->assertSame(HotelStayStatus::Reserved, $stay->fresh()->status);
        $this->assertNull($stay->fresh()->checked_out_at);
    }

    public function test_a_completed_stay_cannot_be_cancelled(): void
    {
        $stay = HotelStay::factory()->create([
            'status' => HotelStayStatus::Completed,
            'checked_in_at' => now()->subDay(),
            'checked_out_at' => now(),
        ]);

        $this->actingAsManager()
            ->patch("/sejours/{$stay->id}/annuler")
            ->assertRedirect();

        $this->assertSame(HotelStayStatus::Completed, $stay->fresh()->status);
    }

    public function test_the_deposit_is_shown_and_deducted_from_the_balance(): void
    {
        $stay = HotelStay::factory()->create([
            'total_amount' => 90000,
            'deposit_amount' => 30000,
        ]);

        $expectedBalance = \App\Support\Money::fcfa(90000 - 30000);
        $expectedDeposit = \App\Support\Money::fcfa(30000);

        $this->actingAsManager()
            ->get("/sejours/{$stay->id}")
            ->assertOk()
            ->assertSee($expectedDeposit)
            ->assertSee($expectedBalance);
    }

    public function test_cancelling_an_in_progress_stay_frees_the_room(): void
    {
        $room = HotelRoom::factory()->create(['status' => HotelRoomStatus::Occupied]);
        $stay = HotelStay::factory()->for($room, 'room')->create([
            'status' => HotelStayStatus::InProgress,
            'checked_in_at' => now(),
        ]);

        $this->actingAsManager()
            ->patch("/sejours/{$stay->id}/annuler")
            ->assertRedirect();

        $this->assertSame(HotelStayStatus::Cancelled, $stay->fresh()->status);
        $this->assertSame(HotelRoomStatus::Available, $room->fresh()->status);
    }
}
