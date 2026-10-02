<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterclaysSidebarTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_sidebar_shows_all_sixteen_masterclays_entries(): void
    {
        $response = $this->actingAs(User::factory()->viewer()->create())->get('/tableau-de-bord');

        $response->assertOk();
        foreach (config('masterclays_modules.modules') as $module) {
            $response->assertSee($module['label']);
        }
    }

    public function test_the_duplicated_historical_meexeo_sidebar_section_is_gone(): void
    {
        // Biens/Locataires/Paiements used to appear twice: once as "Gestion
        // locative" (the real module entry) and again as a separate
        // "MEEXEO (historique)" section lower down — confusing, and the
        // "historique" label made the actively-used module look deprecated.
        // Now reached via "Gestion locative" → the locative._subnav tabs.
        $response = $this->actingAs(User::factory()->viewer()->create())->get('/tableau-de-bord');

        $response->assertDontSee('MEEXEO (historique)');
    }

    public function test_the_sidebar_groups_entries_under_their_section_titles(): void
    {
        $response = $this->actingAs(User::factory()->viewer()->create())->get('/tableau-de-bord');

        foreach (array_keys(config('masterclays_modules.nav_groups')) as $title) {
            $response->assertSee($title);
        }
    }

    public function test_the_collapse_toggle_button_is_present(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/tableau-de-bord')
            ->assertSee('aria-label="Réduire ou déplier le menu"', false);
    }
}
