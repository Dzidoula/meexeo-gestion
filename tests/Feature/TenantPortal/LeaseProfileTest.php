<?php

namespace Tests\Feature\TenantPortal;

use App\Models\Lease;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaseProfileTest extends TestCase
{
    use RefreshDatabase;

    private function tenant(): array
    {
        $tenant   = Tenant::factory()->create(['first_names' => 'Kouamé', 'email' => 'k@example.com']);
        $property = Property::factory()->create(['title' => 'Studio Deux Plateaux', 'monthly_rent' => 80000]);
        $lease    = Lease::factory()->active()->create([
            'tenant_id'    => $tenant->id,
            'property_id'  => $property->id,
            'monthly_rent' => 80000,
            'start_date'   => '2026-01-01',
        ]);

        return compact('tenant', 'lease', 'property');
    }

    public function test_lease_page_shows_property_and_amounts(): void
    {
        ['tenant' => $tenant] = $this->tenant();

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/mon-contrat')
            ->assertOk()
            ->assertSee('Studio Deux Plateaux')
            ->assertSee('80');
    }

    public function test_profile_page_shows_tenant_name(): void
    {
        ['tenant' => $tenant] = $this->tenant();

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/profil')
            ->assertOk()
            ->assertSee('Kouamé');
    }

    public function test_profile_allows_updating_email(): void
    {
        ['tenant' => $tenant] = $this->tenant();

        $this->actingAs($tenant, 'tenant')
            ->patch('/espace-locataire/profil', ['email' => 'nouveau@example.com', 'phone2' => '', 'occupation' => ''])
            ->assertRedirect();

        $this->assertEquals('nouveau@example.com', $tenant->fresh()->email);
    }

    public function test_profile_cannot_update_protected_fields(): void
    {
        ['tenant' => $tenant] = $this->tenant();

        $originalFirstNames = $tenant->first_names;
        $originalPhone1     = $tenant->phone1;

        $this->actingAs($tenant, 'tenant')
            ->patch('/espace-locataire/profil', [
                'email'       => 'ok@example.com',
                'first_names' => 'Hacker',
                'phone1'      => '0000000000',
                'id_number'   => 'HACKED',
            ]);

        $tenant->refresh();
        $this->assertEquals($originalFirstNames, $tenant->first_names);
        $this->assertEquals($originalPhone1, $tenant->phone1);
    }
}
