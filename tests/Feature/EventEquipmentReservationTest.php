<?php
namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Models\Equipment;
use App\Models\Event;
use App\Models\EventEquipmentReservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventEquipmentReservationTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsManager(): self
    {
        return $this->actingAs(User::factory()->manager()->create());
    }

    public function test_a_manager_can_reserve_equipment_for_an_event(): void
    {
        $equipment = Equipment::factory()->create(['quantity_total' => 50]);
        $event = Event::factory()->create(['start_date' => '2026-12-01', 'end_date' => '2026-12-01']);

        $this->actingAsManager()
            ->post("/evenements/{$event->id}/equipements", ['equipment_id' => $equipment->id, 'quantity' => 20])
            ->assertRedirect(route('events.show', $event));

        $reservation = EventEquipmentReservation::sole();
        $this->assertSame($event->id, $reservation->event_id);
        $this->assertSame(20, $reservation->quantity);
    }

    public function test_a_viewer_cannot_reserve_equipment(): void
    {
        $equipment = Equipment::factory()->create(['quantity_total' => 50]);
        $event = Event::factory()->create();

        $this->actingAs(User::factory()->viewer()->create())
            ->post("/evenements/{$event->id}/equipements", ['equipment_id' => $equipment->id, 'quantity' => 5])
            ->assertForbidden();
    }

    public function test_the_quantity_is_required_and_must_be_at_least_one(): void
    {
        $equipment = Equipment::factory()->create(['quantity_total' => 50]);
        $event = Event::factory()->create();

        $this->actingAsManager()
            ->post("/evenements/{$event->id}/equipements", ['equipment_id' => $equipment->id, 'quantity' => 0])
            ->assertSessionHasErrors('quantity');

        $this->assertSame(0, EventEquipmentReservation::count());
    }

    public function test_reserving_more_than_the_available_quantity_on_overlapping_dates_is_refused(): void
    {
        $equipment = Equipment::factory()->create(['quantity_total' => 100]);
        $eventA = Event::factory()->create(['start_date' => '2026-12-01', 'end_date' => '2026-12-03', 'status' => EventStatus::Confirmed]);
        EventEquipmentReservation::factory()->for($eventA)->for($equipment)->create(['quantity' => 100]);

        $eventB = Event::factory()->create(['start_date' => '2026-12-03', 'end_date' => '2026-12-05']);

        $this->actingAsManager()
            ->post("/evenements/{$eventB->id}/equipements", ['equipment_id' => $equipment->id, 'quantity' => 1])
            ->assertSessionHasErrors('equipment_id');

        $this->assertSame(1, EventEquipmentReservation::count());
    }

    public function test_the_same_equipment_can_be_fully_reserved_again_once_the_earlier_event_ends(): void
    {
        $equipment = Equipment::factory()->create(['quantity_total' => 100]);
        $eventA = Event::factory()->create(['start_date' => '2026-12-01', 'end_date' => '2026-12-03', 'status' => EventStatus::Confirmed]);
        EventEquipmentReservation::factory()->for($eventA)->for($equipment)->create(['quantity' => 100]);

        $eventB = Event::factory()->create(['start_date' => '2026-12-04', 'end_date' => '2026-12-06']);

        $this->actingAsManager()
            ->post("/evenements/{$eventB->id}/equipements", ['equipment_id' => $equipment->id, 'quantity' => 100])
            ->assertRedirect(route('events.show', $eventB));

        $this->assertSame(2, EventEquipmentReservation::count());
    }

    public function test_a_cancelled_events_reservation_does_not_block_the_equipment(): void
    {
        $equipment = Equipment::factory()->create(['quantity_total' => 100]);
        $eventA = Event::factory()->create(['start_date' => '2026-12-01', 'end_date' => '2026-12-03', 'status' => EventStatus::Cancelled]);
        EventEquipmentReservation::factory()->for($eventA)->for($equipment)->create(['quantity' => 100]);

        $eventB = Event::factory()->create(['start_date' => '2026-12-02', 'end_date' => '2026-12-02']);

        $this->actingAsManager()
            ->post("/evenements/{$eventB->id}/equipements", ['equipment_id' => $equipment->id, 'quantity' => 100])
            ->assertRedirect(route('events.show', $eventB));
    }

    public function test_a_manager_can_remove_a_reservation_from_an_active_event(): void
    {
        $event = Event::factory()->create();
        $reservation = EventEquipmentReservation::factory()->for($event)->create();

        $this->actingAsManager()
            ->delete("/evenements/{$event->id}/equipements/{$reservation->id}")
            ->assertRedirect(route('events.show', $event));

        $this->assertNull($reservation->fresh());
    }

    public function test_a_reservation_cannot_be_removed_from_a_completed_event(): void
    {
        $event = Event::factory()->create(['status' => EventStatus::Completed]);
        $reservation = EventEquipmentReservation::factory()->for($event)->create();

        $this->actingAsManager()
            ->delete("/evenements/{$event->id}/equipements/{$reservation->id}")
            ->assertRedirect();

        $this->assertNotNull($reservation->fresh());
    }

    public function test_a_reservation_cannot_be_deleted_through_a_different_events_url(): void
    {
        $eventA = Event::factory()->create(['status' => EventStatus::Pending]);
        EventEquipmentReservation::factory()->for($eventA)->create();

        $eventB = Event::factory()->create(['status' => EventStatus::Completed]);
        $reservationB = EventEquipmentReservation::factory()->for($eventB)->create();

        $this->actingAsManager()
            ->delete("/evenements/{$eventA->id}/equipements/{$reservationB->id}")
            ->assertNotFound();

        $this->assertNotNull($reservationB->fresh());
    }

    public function test_a_reservation_cannot_be_added_to_a_completed_event(): void
    {
        $equipment = Equipment::factory()->create(['quantity_total' => 50]);
        $event = Event::factory()->create(['status' => EventStatus::Completed]);

        $this->actingAsManager()
            ->post("/evenements/{$event->id}/equipements", ['equipment_id' => $equipment->id, 'quantity' => 5])
            ->assertRedirect();

        $this->assertSame(0, EventEquipmentReservation::count());
    }
}
