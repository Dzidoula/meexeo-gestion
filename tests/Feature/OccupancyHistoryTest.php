<?php
// tests/Feature/OccupancyHistoryTest.php
namespace Tests\Feature;

use App\Enums\LeaseStatus;
use App\Models\Lease;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OccupancyHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_property_record_names_its_current_occupant(): void
    {
        $property = Property::factory()->occupied()->create();
        $tenant = Tenant::factory()->create(['last_name' => 'Bamba', 'first_names' => 'Adama']);
        Lease::factory()->for($property)->for($tenant)->create([
            'status' => LeaseStatus::Active,
            'monthly_rent' => 300000,
        ]);

        $this->actingAs(User::factory()->viewer()->create())
            ->get("/biens/{$property->id}")
            ->assertOk()
            ->assertSee('Adama Bamba')
            ->assertSee('300 000 FCFA');
    }

    public function test_the_property_record_says_when_nobody_lives_there(): void
    {
        $property = Property::factory()->vacant()->create();

        $this->actingAs(User::factory()->viewer()->create())
            ->get("/biens/{$property->id}")
            ->assertSee('Aucun locataire en place');
    }

    public function test_the_history_lists_past_occupants_newest_first(): void
    {
        $property = Property::factory()->create();

        $older = Tenant::factory()->create(['last_name' => 'Ancien', 'first_names' => 'Premier']);
        Lease::factory()->for($property)->for($older)->create([
            'status' => LeaseStatus::Ended,
            'start_date' => '2023-01-01',
            'actual_end_date' => '2024-06-30',
            'monthly_rent' => 200000,
        ]);

        $recent = Tenant::factory()->create(['last_name' => 'Récent', 'first_names' => 'Second']);
        Lease::factory()->for($property)->for($recent)->create([
            'status' => LeaseStatus::Ended,
            'start_date' => '2024-08-01',
            'actual_end_date' => '2026-07-31',
            'monthly_rent' => 250000,
        ]);

        $response = $this->actingAs(User::factory()->viewer()->create())
            ->get("/biens/{$property->id}");

        $response->assertSee('Premier Ancien')->assertSee('Second Récent');

        // Le plus récent d'abord : la frise se lit du présent vers le passé.
        $html = $response->getContent();
        $this->assertLessThan(
            strpos($html, 'Premier Ancien'),
            strpos($html, 'Second Récent'),
            "L'historique doit présenter le bail le plus récent en premier."
        );
    }

    public function test_the_history_shows_the_rent_of_the_time_not_the_current_one(): void
    {
        $property = Property::factory()->create(['monthly_rent' => 500000]);
        Lease::factory()->for($property)->create([
            'status' => LeaseStatus::Ended,
            'monthly_rent' => 180000,
            'actual_end_date' => '2026-01-31',
        ]);

        $this->actingAs(User::factory()->viewer()->create())
            ->get("/biens/{$property->id}")
            ->assertSee('180 000 FCFA');
    }

    public function test_the_history_is_empty_when_the_property_was_never_rented(): void
    {
        $property = Property::factory()->create();

        $this->actingAs(User::factory()->viewer()->create())
            ->get("/biens/{$property->id}")
            ->assertSee("Ce bien n'a jamais été loué");
    }

    public function test_the_tenant_record_names_the_property_they_occupy(): void
    {
        $tenant = Tenant::factory()->create();
        $property = Property::factory()->occupied()->create(['title' => 'Villa Bietry']);
        Lease::factory()->for($property)->for($tenant)->create(['status' => LeaseStatus::Active]);

        $this->actingAs(User::factory()->viewer()->create())
            ->get("/locataires/{$tenant->id}")
            ->assertSee('Villa Bietry');
    }
}
