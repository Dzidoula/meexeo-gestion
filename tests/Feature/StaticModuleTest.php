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
        $this->get('/modules/residence')->assertRedirect('/connexion');
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
            ['residence', 'Résidence'],
            ['hotel', 'Hôtel'],
            ['evenementiel', 'Événementiel'],
            ['vehicules', 'Véhicules'],
            ['stock', 'Gestion de stock'],
            ['rh', 'Ressources humaines'],
            ['clients', 'Clients'],
            ['fournisseurs', 'Fournisseurs'],
            ['ecommerce', 'E-commerce'],
        ];
    }

    public function test_an_unknown_module_id_is_a_404(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/modules/inexistant')
            ->assertNotFound();
    }

    public function test_the_residence_table_shows_a_row_and_its_status_pill(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/modules/residence')
            ->assertSee('Kouassi Jean')
            ->assertSee('Villa Deluxe - Cocody')
            ->assertSee('Confirmée');
    }

    public function test_the_add_button_is_present_but_disabled(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/modules/residence')
            ->assertSee('disabled', false)
            ->assertSee('Réservation');
    }
}
