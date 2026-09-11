<?php
// tests/Feature/PropertyIndexTest.php
namespace Tests\Feature;

use App\Enums\PropertyType;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyIndexTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsManager(): self
    {
        return $this->actingAs(User::factory()->manager()->create());
    }

    public function test_a_guest_cannot_see_the_list(): void
    {
        $this->get('/biens')->assertRedirect('/connexion');
    }

    public function test_it_lists_properties_with_their_reference_and_formatted_rent(): void
    {
        $property = Property::factory()->create(['title' => 'Villa Bassam', 'monthly_rent' => 450000]);

        $this->actingAsManager()
            ->get('/biens')
            ->assertOk()
            ->assertSee('Villa Bassam')
            ->assertSee($property->reference)
            ->assertSee('450 000 FCFA');
    }

    public function test_it_filters_by_commune(): void
    {
        Property::factory()->create(['title' => 'Bien Cocody', 'commune' => 'Cocody']);
        Property::factory()->create(['title' => 'Bien Yopougon', 'commune' => 'Yopougon']);

        $this->actingAsManager()
            ->get('/biens?commune=Cocody')
            ->assertSee('Bien Cocody')
            ->assertDontSee('Bien Yopougon');
    }

    public function test_it_filters_by_status(): void
    {
        Property::factory()->occupied()->create(['title' => 'Bien occupé']);
        Property::factory()->vacant()->create(['title' => 'Bien libre']);

        $this->actingAsManager()
            ->get('/biens?status=vacant')
            ->assertSee('Bien libre')
            ->assertDontSee('Bien occupé');
    }

    public function test_it_filters_by_type(): void
    {
        Property::factory()->create(['title' => 'Le studio', 'type' => PropertyType::Studio]);
        Property::factory()->create(['title' => 'La villa', 'type' => PropertyType::Villa]);

        $this->actingAsManager()
            ->get('/biens?type=studio')
            ->assertSee('Le studio')
            ->assertDontSee('La villa');
    }

    public function test_the_search_matches_title_district_and_lot(): void
    {
        Property::factory()->create(['title' => 'Résidence Aghien', 'district' => 'Angré', 'lot_number' => '777']);
        Property::factory()->create(['title' => 'Autre bien', 'district' => 'Marcory', 'lot_number' => '12']);

        $manager = $this->actingAsManager();

        $manager->get('/biens?q=Aghien')->assertSee('Résidence Aghien')->assertDontSee('Autre bien');
        $manager->get('/biens?q=777')->assertSee('Résidence Aghien')->assertDontSee('Autre bien');
    }

    public function test_an_unknown_filter_value_is_ignored_rather_than_crashing(): void
    {
        Property::factory()->create(['title' => 'Un bien']);

        $this->actingAsManager()->get('/biens?status=pas-un-statut')->assertOk()->assertSee('Un bien');
    }

    public function test_it_tells_the_user_when_nothing_matches(): void
    {
        $this->actingAsManager()
            ->get('/biens?q=introuvable')
            ->assertOk()
            ->assertSee('Aucun bien ne correspond');
    }

    public function test_a_viewer_does_not_see_the_add_button(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/biens')
            ->assertOk()
            ->assertDontSee('Ajouter un bien');
    }

    public function test_a_manager_sees_the_add_button(): void
    {
        $this->actingAsManager()
            ->get('/biens')
            ->assertOk()
            ->assertSee('Ajouter un bien');
    }
}
