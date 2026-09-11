<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_login_screen_is_reachable(): void
    {
        $this->get('/connexion')->assertOk()->assertSee('Connexion');
    }

    public function test_a_user_can_log_in_with_the_right_password(): void
    {
        $user = User::factory()->manager()->create([
            'email' => 'gestionnaire@meexeo.ci',
            'password' => 'MotDePasse!1',
        ]);

        $this->post('/connexion', [
            'email' => 'gestionnaire@meexeo.ci',
            'password' => 'MotDePasse!1',
        ])->assertRedirect('/biens');

        $this->assertAuthenticatedAs($user);
    }

    public function test_a_wrong_password_is_rejected_without_saying_which_field_is_wrong(): void
    {
        User::factory()->create([
            'email' => 'gestionnaire@meexeo.ci',
            'password' => 'MotDePasse!1',
        ]);

        $this->from('/connexion')
            ->post('/connexion', ['email' => 'gestionnaire@meexeo.ci', 'password' => 'faux'])
            ->assertRedirect('/connexion')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_requires_both_fields(): void
    {
        $this->from('/connexion')
            ->post('/connexion', [])
            ->assertSessionHasErrors(['email', 'password']);
    }

    public function test_a_user_can_log_out(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/deconnexion')
            ->assertRedirect('/connexion');

        $this->assertGuest();
    }

    public function test_an_authenticated_user_is_sent_away_from_the_login_screen(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/connexion')
            ->assertRedirect('/biens');
    }
}
