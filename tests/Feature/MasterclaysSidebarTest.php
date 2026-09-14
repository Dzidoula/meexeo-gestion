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

    public function test_the_sidebar_still_shows_the_historical_meexeo_entries(): void
    {
        $response = $this->actingAs(User::factory()->viewer()->create())->get('/tableau-de-bord');

        $response->assertSee('Biens')->assertSee('Locataires')->assertSee('Paiements');
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
