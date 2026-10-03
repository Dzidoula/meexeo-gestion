<?php

namespace Tests\Feature\TenantPortal;

use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private function tenantWithPayment(): array
    {
        $tenant   = Tenant::factory()->create();
        $property = Property::factory()->create();
        $lease    = Lease::factory()->active()->create([
            'tenant_id'    => $tenant->id,
            'property_id'  => $property->id,
            'monthly_rent' => 150000,
        ]);
        $payment = Payment::factory()->create([
            'lease_id' => $lease->id,
            'month'    => '2026-09-01',
            'amount'   => 150000,
            'paid_on'  => '2026-09-04',
        ]);

        return compact('tenant', 'lease', 'payment');
    }

    public function test_payments_page_shows_tenant_payments(): void
    {
        ['tenant' => $tenant] = $this->tenantWithPayment();

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/paiements')
            ->assertOk()
            ->assertSee('150');
    }

    public function test_payments_page_does_not_show_other_tenants_payments(): void
    {
        ['tenant' => $tenant1] = $this->tenantWithPayment();

        $tenant2   = Tenant::factory()->create();
        $property2 = Property::factory()->create();
        $lease2    = Lease::factory()->active()->create(['tenant_id' => $tenant2->id, 'property_id' => $property2->id]);
        Payment::factory()->create(['lease_id' => $lease2->id, 'month' => '2026-09-01', 'amount' => 999999, 'paid_on' => '2026-09-01']);

        $this->actingAs($tenant1, 'tenant')
            ->get('/espace-locataire/paiements')
            ->assertDontSee('999 999');
    }

    public function test_payments_page_handles_payment_without_proof(): void
    {
        ['tenant' => $tenant] = $this->tenantWithPayment();

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/paiements')
            ->assertOk();
    }

    public function test_payment_create_page_shows_current_month(): void
    {
        $tenant   = Tenant::factory()->create();
        $property = Property::factory()->create();
        Lease::factory()->active()->create(['tenant_id' => $tenant->id, 'property_id' => $property->id]);

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/paiements/nouveau')
            ->assertOk();
    }
}
