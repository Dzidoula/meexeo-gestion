<?php
// tests/Feature/LeaseTest.php
namespace Tests\Feature;

use App\Enums\LeaseStatus;
use App\Enums\PropertyStatus;
use App\Models\Lease;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaseTest extends TestCase
{
    use RefreshDatabase;

    private function payload(Property $property, Tenant $tenant, array $overrides = []): array
    {
        return array_merge([
            'property_id' => $property->id,
            'tenant_id' => $tenant->id,
            'start_date' => '2026-09-01',
            'expected_end_date' => '2027-08-31',
            'monthly_rent' => 450000,
            'deposit_paid' => 900000,
            'due_day' => 5,
        ], $overrides);
    }

    public function test_a_manager_can_assign_a_vacant_property_to_a_tenant(): void
    {
        $property = Property::factory()->vacant()->create();
        $tenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->post('/affectations', $this->payload($property, $tenant))
            ->assertRedirect();

        $lease = Lease::sole();
        $this->assertSame($property->id, $lease->property_id);
        $this->assertSame(LeaseStatus::Active, $lease->status);
        $this->assertSame(450000, $lease->monthly_rent);
    }

    public function test_assigning_a_property_marks_it_occupied(): void
    {
        $property = Property::factory()->vacant()->create();
        $tenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->post('/affectations', $this->payload($property, $tenant));

        $this->assertSame(PropertyStatus::Occupied, $property->fresh()->status);
    }

    public function test_a_property_cannot_carry_two_active_leases(): void
    {
        $property = Property::factory()->create();
        Lease::factory()->for($property)->create(['status' => LeaseStatus::Active]);
        $newTenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->from('/affectations/nouvelle')
            ->post('/affectations', $this->payload($property, $newTenant))
            ->assertSessionHasErrors('property_id');

        $this->assertSame(1, $property->fresh()->leases()->count());
    }

    public function test_a_property_can_be_reassigned_once_the_previous_lease_ended(): void
    {
        $property = Property::factory()->create();
        Lease::factory()->for($property)->create([
            'status' => LeaseStatus::Ended,
            'actual_end_date' => '2026-08-31',
        ]);
        $newTenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->post('/affectations', $this->payload($property, $newTenant))
            ->assertRedirect();

        $this->assertSame(2, $property->fresh()->leases()->count());
    }

    public function test_the_expected_end_date_must_follow_the_start_date(): void
    {
        $property = Property::factory()->vacant()->create();
        $tenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->from('/affectations/nouvelle')
            ->post('/affectations', $this->payload($property, $tenant, [
                'start_date' => '2026-09-01',
                'expected_end_date' => '2026-08-01',
            ]))
            ->assertSessionHasErrors('expected_end_date');
    }

    public function test_the_due_day_must_be_a_valid_day_of_month(): void
    {
        $property = Property::factory()->vacant()->create();
        $tenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->from('/affectations/nouvelle')
            ->post('/affectations', $this->payload($property, $tenant, ['due_day' => 32]))
            ->assertSessionHasErrors('due_day');
    }

    public function test_ending_a_lease_frees_the_property_and_records_the_real_end_date(): void
    {
        $property = Property::factory()->occupied()->create();
        $lease = Lease::factory()->for($property)->create(['status' => LeaseStatus::Active]);

        $this->actingAs(User::factory()->manager()->create())
            ->patch("/affectations/{$lease->id}/fin", ['actual_end_date' => '2026-09-30'])
            ->assertRedirect();

        $lease->refresh();
        $this->assertSame(LeaseStatus::Ended, $lease->status);
        $this->assertSame('2026-09-30', $lease->actual_end_date->format('Y-m-d'));
        $this->assertSame(PropertyStatus::Vacant, $property->fresh()->status);
    }

    public function test_ending_a_lease_does_not_reopen_a_property_under_works(): void
    {
        $property = Property::factory()->works()->create();
        $lease = Lease::factory()->for($property)->create(['status' => LeaseStatus::Active]);

        $this->actingAs(User::factory()->manager()->create())
            ->patch("/affectations/{$lease->id}/fin", ['actual_end_date' => '2026-09-30']);

        // Un bien en travaux ne redevient pas « libre » du seul fait qu'un bail se termine.
        $this->assertSame(PropertyStatus::Works, $property->fresh()->status);
    }

    public function test_an_accountant_cannot_assign_a_property(): void
    {
        $property = Property::factory()->vacant()->create();
        $tenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->accountant()->create())
            ->post('/affectations', $this->payload($property, $tenant))
            ->assertForbidden();
    }

    public function test_the_active_lease_is_reachable_from_both_sides(): void
    {
        $property = Property::factory()->create();
        $tenant = Tenant::factory()->create();
        Lease::factory()->for($property)->for($tenant)->create(['status' => LeaseStatus::Active]);

        $this->assertNotNull($property->fresh()->activeLease);
        $this->assertSame($tenant->id, $property->fresh()->activeLease->tenant_id);
        $this->assertNotNull($tenant->fresh()->activeLease);
    }
}
