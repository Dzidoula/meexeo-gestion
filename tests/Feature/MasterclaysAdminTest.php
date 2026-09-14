<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterclaysAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_cannot_see_any_admin_page(): void
    {
        foreach (['/comptabilite', '/rapports', '/permissions', '/notifications', '/securite', '/parametres'] as $path) {
            $this->get($path)->assertRedirect('/connexion');
        }
    }

    public function test_finance_shows_the_mockups_exact_totals(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/comptabilite')
            ->assertSee('12 450 000 FCFA')
            ->assertSee('3 200 000 FCFA')
            ->assertSee('9 250 000 FCFA')
            ->assertSee('Paiement réservation');
    }

    public function test_reports_lists_nine_report_cards_with_disabled_export_buttons(): void
    {
        $response = $this->actingAs(User::factory()->viewer()->create())->get('/rapports');

        $response->assertOk()->assertSee('Exporter PDF')->assertSee('Exporter Excel');
        foreach (['Résidence', 'Hôtel', 'Locatif', 'Événementiel', 'Véhicules', 'Stock', 'RH', 'Clients', 'Finances'] as $label) {
            $response->assertSee('Rapport '.$label);
        }
    }

    public function test_permissions_lists_the_mockups_users(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/permissions')
            ->assertSee("N'Da Marie")
            ->assertSee('Comptabilité, Rapports');
    }

    public function test_notifications_page_lists_the_mockups_notifications(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/notifications')
            ->assertSee('Loyer impayé')
            ->assertSee('5 locataires');
    }

    public function test_security_shows_toggles_and_the_action_log(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/securite')
            ->assertSee('Authentification à deux facteurs')
            ->assertSee('Connexion réussie');
    }

    public function test_settings_shows_the_company_name_and_currency(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/parametres')
            ->assertSee('MASTERCLAYS')
            ->assertSee('FCFA');
    }
}
