<?php
// tests/Feature/PropertyWriteTest.php
namespace Tests\Feature;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyWriteTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Villa Palmeraie',
            'type' => PropertyType::Villa->value,
            'status' => PropertyStatus::Vacant->value,
            'city' => 'Abidjan',
            'commune' => 'Cocody',
            'district' => 'Palmeraie',
            'lot_number' => '88',
            'block_number' => '4',
            'rooms' => 5,
            'area_sqm' => 220,
            'monthly_rent' => 650000,
            'deposit' => 1300000,
        ], $overrides);
    }

    public function test_a_manager_can_create_a_property(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->post('/biens', $this->validPayload())
            ->assertRedirect();

        $this->assertDatabaseHas('properties', [
            'title' => 'Villa Palmeraie',
            'monthly_rent' => 650000,
            'commune' => 'Cocody',
        ]);
    }

    public function test_an_accountant_cannot_create_a_property(): void
    {
        $this->actingAs(User::factory()->accountant()->create())
            ->post('/biens', $this->validPayload())
            ->assertForbidden();

        $this->assertDatabaseCount('properties', 0);
    }

    public function test_a_viewer_cannot_reach_the_creation_form(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/biens/nouveau')
            ->assertForbidden();
    }

    public function test_the_title_city_and_type_are_required(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->from('/biens/nouveau')
            ->post('/biens', ['title' => '', 'city' => '', 'type' => ''])
            ->assertSessionHasErrors(['title', 'city', 'type']);
    }

    public function test_an_unknown_type_is_rejected(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->from('/biens/nouveau')
            ->post('/biens', $this->validPayload(['type' => 'chateau']))
            ->assertSessionHasErrors('type');
    }

    public function test_a_negative_rent_is_rejected(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->from('/biens/nouveau')
            ->post('/biens', $this->validPayload(['monthly_rent' => -1]))
            ->assertSessionHasErrors('monthly_rent');
    }

    public function test_a_decimal_rent_is_rejected_because_the_franc_has_no_cents(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->from('/biens/nouveau')
            ->post('/biens', $this->validPayload(['monthly_rent' => 450000.75]))
            ->assertSessionHasErrors('monthly_rent');
    }

    public function test_a_manager_can_update_a_property(): void
    {
        $property = Property::factory()->create(['title' => 'Ancien titre']);

        $this->actingAs(User::factory()->manager()->create())
            ->put("/biens/{$property->id}", $this->validPayload(['title' => 'Nouveau titre']))
            ->assertRedirect();

        $this->assertSame('Nouveau titre', $property->fresh()->title);
    }

    public function test_a_manager_can_open_the_edit_form_for_an_existing_property(): void
    {
        // Régression : le lien "Annuler" du formulaire appelait route('properties.show', ...),
        // une route qui n'existe pas avant la Task 10. Comme Blade évalue route() à l'affichage,
        // ça faisait planter GET /biens/{property}/modifier avec un 500 sur toute propriété
        // existante ($editing est alors vrai). Ce test rend réellement la page pour l'attraper.
        $property = Property::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->get("/biens/{$property->id}/modifier")
            ->assertOk();
    }
}
