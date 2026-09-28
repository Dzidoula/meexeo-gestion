<?php
namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Models\Equipment;
use App\Models\Event;
use App\Models\EventEquipmentReservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsManager(): self
    {
        return $this->actingAs(User::factory()->manager()->create());
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'client_name' => 'Awa Traoré',
            'client_phone' => '0701020304',
            'start_date' => '2026-11-14',
            'end_date' => '2026-11-14',
            'venue' => 'Salle Prestige - Abidjan',
            'budget_total' => 3500000,
            'deposit_amount' => 500000,
            'notes' => null,
        ], $overrides);
    }

    public function test_a_guest_cannot_see_the_event_list(): void
    {
        $this->get('/evenements')->assertRedirect('/connexion');
    }

    public function test_a_manager_can_create_an_event_as_pending(): void
    {
        $this->actingAsManager()
            ->post('/evenements', $this->validPayload())
            ->assertRedirect();

        $event = Event::sole();
        $this->assertSame('Awa Traoré', $event->client_name);
        $this->assertSame(EventStatus::Pending, $event->status);
    }

    public function test_a_viewer_cannot_create_an_event(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->post('/evenements', $this->validPayload())
            ->assertForbidden();
    }

    public function test_the_client_name_is_required(): void
    {
        $this->actingAsManager()
            ->post('/evenements', $this->validPayload(['client_name' => '']))
            ->assertSessionHasErrors('client_name');
    }

    public function test_the_end_date_cannot_be_before_the_start_date(): void
    {
        $this->actingAsManager()
            ->post('/evenements', $this->validPayload(['start_date' => '2026-11-14', 'end_date' => '2026-11-13']))
            ->assertSessionHasErrors('end_date');

        $this->assertSame(0, Event::count());
    }

    public function test_an_event_can_span_a_single_day(): void
    {
        $this->actingAsManager()
            ->post('/evenements', $this->validPayload(['start_date' => '2026-11-14', 'end_date' => '2026-11-14']))
            ->assertRedirect();

        $this->assertSame(1, Event::count());
    }

    public function test_the_budget_cannot_be_negative(): void
    {
        $this->actingAsManager()
            ->post('/evenements', $this->validPayload(['budget_total' => -1]))
            ->assertSessionHasErrors('budget_total');
    }

    public function test_the_deposit_cannot_be_negative(): void
    {
        $this->actingAsManager()
            ->post('/evenements', $this->validPayload(['deposit_amount' => -1]))
            ->assertSessionHasErrors('deposit_amount');
    }

    public function test_a_manager_can_update_a_pending_event(): void
    {
        $event = Event::factory()->create(['client_name' => 'Ancien nom']);

        $this->actingAsManager()
            ->put("/evenements/{$event->id}", $this->validPayload(['client_name' => 'Nouveau nom']))
            ->assertRedirect();

        $this->assertSame('Nouveau nom', $event->fresh()->client_name);
    }

    public function test_a_completed_event_cannot_be_updated(): void
    {
        $event = Event::factory()->create(['status' => EventStatus::Completed, 'client_name' => 'Figé']);

        $this->actingAsManager()
            ->put("/evenements/{$event->id}", $this->validPayload(['client_name' => 'Modifié']))
            ->assertRedirect();

        $this->assertSame('Figé', $event->fresh()->client_name);
    }

    public function test_a_pending_event_can_be_confirmed(): void
    {
        $event = Event::factory()->create(['status' => EventStatus::Pending]);

        $this->actingAsManager()
            ->patch("/evenements/{$event->id}/confirmer")
            ->assertRedirect();

        $this->assertSame(EventStatus::Confirmed, $event->fresh()->status);
    }

    public function test_a_confirmed_event_cannot_be_confirmed_again(): void
    {
        $event = Event::factory()->create(['status' => EventStatus::Confirmed]);

        $this->actingAsManager()->patch("/evenements/{$event->id}/confirmer");

        $this->assertSame(EventStatus::Confirmed, $event->fresh()->status);
    }

    public function test_a_confirmed_event_can_be_completed(): void
    {
        $event = Event::factory()->create(['status' => EventStatus::Confirmed]);

        $this->actingAsManager()
            ->patch("/evenements/{$event->id}/terminer")
            ->assertRedirect();

        $this->assertSame(EventStatus::Completed, $event->fresh()->status);
    }

    public function test_a_pending_event_cannot_be_completed_directly(): void
    {
        $event = Event::factory()->create(['status' => EventStatus::Pending]);

        $this->actingAsManager()->patch("/evenements/{$event->id}/terminer");

        $this->assertSame(EventStatus::Pending, $event->fresh()->status);
    }

    public function test_a_pending_event_can_be_cancelled(): void
    {
        $event = Event::factory()->create(['status' => EventStatus::Pending]);

        $this->actingAsManager()
            ->patch("/evenements/{$event->id}/annuler")
            ->assertRedirect();

        $this->assertSame(EventStatus::Cancelled, $event->fresh()->status);
    }

    public function test_a_completed_event_cannot_be_cancelled(): void
    {
        $event = Event::factory()->create(['status' => EventStatus::Completed]);

        $this->actingAsManager()->patch("/evenements/{$event->id}/annuler");

        $this->assertSame(EventStatus::Completed, $event->fresh()->status);
    }

    public function test_a_viewer_cannot_confirm_an_event(): void
    {
        $event = Event::factory()->create(['status' => EventStatus::Pending]);

        $this->actingAs(User::factory()->viewer()->create())
            ->patch("/evenements/{$event->id}/confirmer")
            ->assertForbidden();

        $this->assertSame(EventStatus::Pending, $event->fresh()->status);
    }

    public function test_editing_an_events_dates_is_refused_if_it_would_overbook_its_reserved_equipment(): void
    {
        $equipment = Equipment::factory()->create(['quantity_total' => 10]);

        $eventA = Event::factory()->create(['start_date' => '2026-12-01', 'end_date' => '2026-12-01', 'status' => EventStatus::Confirmed]);
        EventEquipmentReservation::factory()->for($eventA)->for($equipment)->create(['quantity' => 10]);

        $eventB = Event::factory()->create(['start_date' => '2026-12-10', 'end_date' => '2026-12-10', 'status' => EventStatus::Confirmed]);
        EventEquipmentReservation::factory()->for($eventB)->for($equipment)->create(['quantity' => 10]);

        $this->actingAsManager()
            ->put("/evenements/{$eventB->id}", $this->validPayload(['start_date' => '2026-12-01', 'end_date' => '2026-12-01']))
            ->assertSessionHasErrors('start_date');

        $this->assertEquals('2026-12-10', $eventB->fresh()->start_date->format('Y-m-d'));
    }

    public function test_editing_an_events_dates_without_a_conflict_succeeds(): void
    {
        $equipment = Equipment::factory()->create(['quantity_total' => 10]);

        $eventA = Event::factory()->create(['start_date' => '2026-12-01', 'end_date' => '2026-12-01', 'status' => EventStatus::Confirmed]);
        EventEquipmentReservation::factory()->for($eventA)->for($equipment)->create(['quantity' => 10]);

        $eventB = Event::factory()->create(['start_date' => '2026-12-10', 'end_date' => '2026-12-10', 'status' => EventStatus::Confirmed]);
        EventEquipmentReservation::factory()->for($eventB)->for($equipment)->create(['quantity' => 10]);

        $this->actingAsManager()
            ->put("/evenements/{$eventB->id}", $this->validPayload(['start_date' => '2026-12-11', 'end_date' => '2026-12-11']))
            ->assertRedirect();

        $this->assertEquals('2026-12-11', $eventB->fresh()->start_date->format('Y-m-d'));
    }

    public function test_the_event_list_links_to_the_equipment_catalog(): void
    {
        $this->actingAsManager()
            ->get('/evenements')
            ->assertSee(route('equipment.index'), false);
    }

    public function test_the_equipment_list_links_to_the_event_list(): void
    {
        $this->actingAsManager()
            ->get('/equipements')
            ->assertSee(route('events.index'), false);
    }
}
