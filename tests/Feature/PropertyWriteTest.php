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

    public function test_a_comma_decimal_latitude_is_rejected_because_it_is_not_numeric(): void
    {
        // Un utilisateur francophone tape volontiers une virgule décimale ; la règle
        // "numeric" la rejette, et l'erreur doit rester visible nulle part masquée.
        $this->actingAs(User::factory()->manager()->create())
            ->from('/biens/nouveau')
            ->post('/biens', $this->validPayload(['latitude' => '5,3049']))
            ->assertSessionHasErrors('latitude');
    }

    public function test_an_out_of_range_latitude_is_rejected(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->from('/biens/nouveau')
            ->post('/biens', $this->validPayload(['latitude' => '200']))
            ->assertSessionHasErrors('latitude');
    }

    public function test_the_latitude_error_is_rendered_on_the_form_after_a_failed_submission(): void
    {
        // Avant le correctif, properties/form.blade.php n'avait aucun bloc
        // @error('latitude') et ce message ne s'affichait donc nulle part. Le
        // client de test HTTP ne renvoie pas automatiquement le cookie de session
        // d'une requête à l'autre : on le retransmet nous-mêmes pour reproduire
        // fidèlement le formulaire rechargé après une redirection de validation.
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->from('/biens/nouveau')
            ->post('/biens', $this->validPayload(['latitude' => '5,3049']))
            ->assertSessionHasErrors('latitude');

        $sessionId = $this->app['session']->getId();

        // Aucun fichier de langue n'est publié dans ce projet (locale "en" par
        // défaut) : Laravel affiche donc la clé de traduction brute plutôt que
        // le texte anglais habituel. Ce qui compte ici, c'est qu'un message
        // apparaisse enfin à côté du champ latitude, plutôt que nulle part.
        $this->actingAs($manager)
            ->withCookie(config('session.cookie'), $sessionId)
            ->get('/biens/nouveau')
            ->assertOk()
            ->assertSee('validation.numeric')
            ->assertSee('5,3049', false);
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
