<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth', 'role:manager'])
            ->get('/_test/managers-only', fn () => response('ok'));
    }

    public function test_a_manager_passes(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->get('/_test/managers-only')
            ->assertOk();
    }

    public function test_an_admin_always_passes(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/_test/managers-only')
            ->assertOk();
    }

    public function test_an_accountant_is_forbidden(): void
    {
        $this->actingAs(User::factory()->accountant()->create())
            ->get('/_test/managers-only')
            ->assertForbidden();
    }

    public function test_a_guest_is_redirected_to_the_login_screen(): void
    {
        $this->get('/_test/managers-only')->assertRedirect('/connexion');
    }
}
