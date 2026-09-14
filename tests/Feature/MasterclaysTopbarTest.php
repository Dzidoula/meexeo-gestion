<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterclaysTopbarTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_search_bar_placeholder_is_present(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/tableau-de-bord')
            ->assertSee('Rechercher...');
    }

    public function test_the_notifications_dropdown_shows_an_honest_empty_state(): void
    {
        $response = $this->actingAs(User::factory()->viewer()->create())->get('/tableau-de-bord');

        $response->assertSee('Aucune notification pour le moment.');
        // Aucun chiffre de badge inventé.
        $response->assertDontSee('notif-badge-count', false);
    }

    public function test_the_mail_dropdown_shows_an_honest_empty_state(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/tableau-de-bord')
            ->assertSee('Aucun message pour le moment.');
    }

    public function test_the_profile_menu_shows_the_real_authenticated_user(): void
    {
        $manager = User::factory()->manager()->create(['name' => 'Awa Koné']);

        $this->actingAs($manager)
            ->get('/tableau-de-bord')
            ->assertSee('Awa Koné')
            ->assertSee(route('logout'), false);
    }

    public function test_the_collapse_button_is_now_present(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/tableau-de-bord')
            ->assertSee('aria-label="Réduire ou déplier le menu"', false);
    }
}
