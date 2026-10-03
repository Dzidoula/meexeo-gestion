<?php

namespace Tests\Feature\TenantPortal;

use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentTest extends TestCase
{
    use RefreshDatabase;

    private function setupTenant(): array
    {
        $tenant   = Tenant::factory()->create();
        $property = Property::factory()->create(['monthly_rent' => 150000]);
        $lease    = Lease::factory()->active()->create([
            'tenant_id'    => $tenant->id,
            'property_id'  => $property->id,
            'monthly_rent' => 150000,
            'start_date'   => '2026-08-01',
            'due_day'      => 5,
        ]);

        return compact('tenant', 'property', 'lease');
    }

    public function test_rents_page_renders(): void
    {
        ['tenant' => $tenant] = $this->setupTenant();

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/loyers')
            ->assertOk();
    }

    public function test_rents_page_shows_months_since_lease_start(): void
    {
        ['tenant' => $tenant] = $this->setupTenant();

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/loyers')
            ->assertSee('Août 2026')
            ->assertSee('Septembre 2026');
    }

    public function test_paid_month_shows_paid_status(): void
    {
        ['tenant' => $tenant, 'lease' => $lease] = $this->setupTenant();

        Payment::factory()->create([
            'lease_id' => $lease->id,
            'month'    => '2026-08-01',
            'amount'   => 150000,
            'paid_on'  => '2026-08-03',
        ]);

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/loyers')
            ->assertSee('Payé');
    }

    public function test_rents_page_requires_authentication(): void
    {
        $this->get('/espace-locataire/loyers')
            ->assertRedirect('/espace-locataire/connexion');
    }
}
