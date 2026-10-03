<?php

namespace Tests\Feature\TenantPortal;

use App\Models\Lease;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function tenantWithLease(): array
    {
        $tenant   = Tenant::factory()->create();
        $property = Property::factory()->create(['title' => 'Appartement Les Cocotiers', 'monthly_rent' => 150000]);
        $lease    = Lease::factory()->active()->create([
            'tenant_id'    => $tenant->id,
            'property_id'  => $property->id,
            'monthly_rent' => 150000,
            'due_day'      => 5,
        ]);

        return compact('tenant', 'property', 'lease');
    }

    public function test_dashboard_shows_property_name(): void
    {
        ['tenant' => $tenant] = $this->tenantWithLease();

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/')
            ->assertOk()
            ->assertSee('Appartement Les Cocotiers');
    }

    public function test_dashboard_shows_monthly_rent(): void
    {
        ['tenant' => $tenant] = $this->tenantWithLease();

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/')
            ->assertOk()
            ->assertSee('150');
    }

    public function test_dashboard_shows_tenant_first_name(): void
    {
        $tenant = Tenant::factory()->create(['first_names' => 'Kouadio']);
        $property = Property::factory()->create();
        Lease::factory()->active()->create(['tenant_id' => $tenant->id, 'property_id' => $property->id]);

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/')
            ->assertSee('Kouadio');
    }

    public function test_dashboard_is_not_accessible_without_auth(): void
    {
        $this->get('/espace-locataire/')
            ->assertRedirect('/espace-locataire/connexion');
    }

    public function test_dashboard_does_not_show_other_tenants_data(): void
    {
        ['tenant' => $tenant1] = $this->tenantWithLease();

        $tenant2   = Tenant::factory()->create();
        $property2 = Property::factory()->create(['title' => 'Villa Riviera']);
        Lease::factory()->active()->create(['tenant_id' => $tenant2->id, 'property_id' => $property2->id]);

        $this->actingAs($tenant1, 'tenant')
            ->get('/espace-locataire/')
            ->assertDontSee('Villa Riviera');
    }
}
