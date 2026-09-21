<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StaticModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_cannot_see_a_module_page(): void
    {
        $this->get('/modules/hotel')->assertRedirect('/connexion');
    }

    #[DataProvider('genericModules')]
    public function test_each_generic_module_renders_its_label_and_kpis(string $id, string $label): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get("/modules/{$id}")
            ->assertOk()
            ->assertSee($label);
    }

    public static function genericModules(): array
    {
        return [
            ['hotel', 'Hôtel'],
            ['evenementiel', 'Événementiel'],
            ['stock', 'Gestion de stock'],
            ['rh', 'Ressources humaines'],
            ['clients', 'Clients'],
            ['fournisseurs', 'Fournisseurs'],
        ];
    }

    public function test_an_unknown_module_id_is_a_404(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/modules/inexistant')
            ->assertNotFound();
    }

    public function test_the_hotel_table_shows_a_row_and_its_status_pill(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/modules/hotel')
            ->assertSee("N'Guessan A.")
            ->assertSee('Confirmée');
    }

    public function test_the_add_button_is_present_but_disabled(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/modules/hotel')
            ->assertSee('disabled', false)
            ->assertSee('Réservation');
    }

    public function test_ecommerce_is_no_longer_a_generic_static_page(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/modules/ecommerce')
            ->assertNotFound();
    }

    public function test_vehicules_is_no_longer_a_generic_static_page(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/modules/vehicules')
            ->assertNotFound();
    }

    public function test_residence_is_no_longer_a_generic_static_page(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/modules/residence')
            ->assertNotFound();
    }

    public function test_the_sidebar_no_longer_lists_residence(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/tableau-de-bord')
            ->assertDontSee('Résidence');
    }
}
