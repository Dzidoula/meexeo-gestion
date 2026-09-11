<?php
// tests/Feature/TenantIndexTest.php
namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_cannot_see_the_list(): void
    {
        $this->get('/locataires')->assertRedirect('/connexion');
    }

    public function test_the_full_name_joins_first_names_and_last_name(): void
    {
        $tenant = Tenant::factory()->create(['last_name' => 'Koné', 'first_names' => 'Awa Mariam']);

        $this->assertSame('Awa Mariam Koné', $tenant->full_name);
        $this->assertSame('AK', $tenant->initials);
    }

    public function test_the_reference_is_derived_from_the_identifier(): void
    {
        $tenant = Tenant::factory()->create();

        $this->assertSame('LOC-'.str_pad((string) $tenant->id, 4, '0', STR_PAD_LEFT), $tenant->reference);
    }

    public function test_it_lists_tenants_with_their_status(): void
    {
        Tenant::factory()->create(['last_name' => 'Diabaté', 'first_names' => 'Sékou']);

        $this->actingAs(User::factory()->manager()->create())
            ->get('/locataires')
            ->assertOk()
            ->assertSee('Sékou Diabaté')
            ->assertSee('Actif');
    }

    public function test_it_filters_by_status(): void
    {
        Tenant::factory()->create(['last_name' => 'Actif', 'first_names' => 'Un']);
        Tenant::factory()->blacklisted()->create(['last_name' => 'Banni', 'first_names' => 'Deux']);

        $this->actingAs(User::factory()->manager()->create())
            ->get('/locataires?status=blacklisted')
            ->assertSee('Deux Banni')
            ->assertDontSee('Un Actif');
    }

    public function test_the_search_matches_name_phone_and_id_number(): void
    {
        Tenant::factory()->create([
            'last_name' => 'Ouattara', 'first_names' => 'Ibrahim',
            'phone1' => '0708091011', 'id_number' => 'CI0099887',
        ]);
        Tenant::factory()->create(['last_name' => 'Autre', 'first_names' => 'Personne']);

        $manager = $this->actingAs(User::factory()->manager()->create());

        $manager->get('/locataires?q=Ouattara')->assertSee('Ibrahim Ouattara')->assertDontSee('Personne Autre');
        $manager->get('/locataires?q=0708091011')->assertSee('Ibrahim Ouattara');
        $manager->get('/locataires?q=CI0099887')->assertSee('Ibrahim Ouattara');
    }

    public function test_it_tells_the_user_when_nothing_matches(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->get('/locataires?q=introuvable')
            ->assertSee('Aucun locataire ne correspond');
    }

    public function test_a_viewer_does_not_see_the_add_button(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/locataires')
            ->assertOk()
            ->assertDontSee('Ajouter un locataire');
    }

    public function test_a_manager_sees_the_add_button(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->get('/locataires')
            ->assertOk()
            ->assertSee('Ajouter un locataire');
    }
}
