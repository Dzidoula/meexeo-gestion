<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantHostRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_tenant_hostname_root_redirects_into_the_portal(): void
    {
        config(['tenant-portal.tenant_host' => 'locataire.masterclays.net']);

        $this->get('http://locataire.masterclays.net/')
            ->assertRedirect(route('tenant-portal.login'));
    }

    public function test_the_admin_hostname_root_redirects_a_guest_to_login(): void
    {
        config(['tenant-portal.admin_host' => 'admin.masterclays.net']);

        $this->get('http://admin.masterclays.net/')
            ->assertRedirect('/connexion');
    }

    public function test_the_admin_hostname_root_redirects_a_logged_in_manager_to_the_dashboard(): void
    {
        config(['tenant-portal.admin_host' => 'admin.masterclays.net']);
        $user = \App\Models\User::factory()->create(['role' => \App\Enums\Role::Manager]);

        $this->actingAs($user, 'web')
            ->get('http://admin.masterclays.net/')
            ->assertRedirect(route('dashboard'));
    }

    public function test_the_bare_masterclays_domain_still_sees_the_storefront(): void
    {
        config([
            'tenant-portal.tenant_host' => 'locataire.masterclays.net',
            'tenant-portal.admin_host' => 'admin.masterclays.net',
        ]);

        $this->get('http://masterclays.net/')
            ->assertOk()
            ->assertSee('véhicule');
    }
}
