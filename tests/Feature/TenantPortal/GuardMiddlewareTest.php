<?php

namespace Tests\Feature\TenantPortal;

use App\Models\Lease;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuardMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_visitor_on_portal_is_redirected_to_tenant_login(): void
    {
        $this->get('/espace-locataire/')
            ->assertRedirect('/espace-locataire/connexion');
    }

    public function test_unauthenticated_visitor_is_not_redirected_to_admin_login(): void
    {
        $response = $this->get('/espace-locataire/');
        $response->assertRedirect('/espace-locataire/connexion');
        $this->assertStringNotContainsString('/connexion', str_replace('/espace-locataire/connexion', '', $response->headers->get('Location') ?? ''));
    }

    public function test_admin_user_cannot_access_tenant_portal_dashboard(): void
    {
        $admin = \App\Models\User::factory()->create();
        $this->actingAs($admin) // guard web
            ->get('/espace-locataire/')
            ->assertRedirect('/espace-locataire/connexion');
    }

    public function test_authenticated_tenant_without_active_lease_sees_no_lease_page(): void
    {
        $tenant = Tenant::factory()->create();
        // No active lease created

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/')
            ->assertRedirect(route('tenant-portal.no-lease'));
    }

    public function test_authenticated_tenant_with_active_lease_can_access_dashboard(): void
    {
        $tenant = Tenant::factory()->create();
        $property = Property::factory()->create();
        Lease::factory()->active()->create(['tenant_id' => $tenant->id, 'property_id' => $property->id]);

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/')
            ->assertOk();
    }
}
