<?php

namespace Tests\Feature\TenantPortal;

use App\Models\Lease;
use App\Models\Property;
use App\Models\RepairRequest;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RepairTest extends TestCase
{
    use RefreshDatabase;

    private function tenantWithLease(): array
    {
        $tenant   = Tenant::factory()->create();
        $property = Property::factory()->create();
        $lease    = Lease::factory()->active()->create([
            'tenant_id'   => $tenant->id,
            'property_id' => $property->id,
        ]);

        return compact('tenant', 'property', 'lease');
    }

    public function test_repairs_list_page_renders(): void
    {
        ['tenant' => $tenant] = $this->tenantWithLease();

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/reparations')
            ->assertOk();
    }

    public function test_creating_a_repair_generates_ticket_number(): void
    {
        Storage::fake('public');
        ['tenant' => $tenant, 'lease' => $lease] = $this->tenantWithLease();

        $this->actingAs($tenant, 'tenant')
            ->post('/espace-locataire/reparations', [
                'type'        => 'plomberie',
                'description' => 'Fuite sous l\'évier de la cuisine.',
                'urgency'     => 'moyenne',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('repair_requests', [
            'tenant_id' => $tenant->id,
            'lease_id'  => $lease->id,
            'type'      => 'plomberie',
        ]);

        $repair = RepairRequest::where('tenant_id', $tenant->id)->first();
        $this->assertMatchesRegularExpression('/^REP-\d+$/', $repair->ticket_no);
    }

    public function test_repair_lease_id_comes_from_active_lease_not_request(): void
    {
        Storage::fake('public');
        ['tenant' => $tenant, 'lease' => $lease] = $this->tenantWithLease();

        $fakeLease = Lease::factory()->active()->create([
            'tenant_id'   => Tenant::factory()->create()->id,
            'property_id' => Property::factory()->create()->id,
        ]);

        $this->actingAs($tenant, 'tenant')
            ->post('/espace-locataire/reparations', [
                'type'        => 'electricite',
                'description' => 'Panne électrique grave dans le salon.',
                'urgency'     => 'urgente',
                'lease_id'    => $fakeLease->id,
            ]);

        $repair = RepairRequest::where('tenant_id', $tenant->id)->first();
        $this->assertEquals($lease->id, $repair->lease_id);
    }

    public function test_tenant_can_only_see_own_repairs(): void
    {
        ['tenant' => $tenant1] = $this->tenantWithLease();
        ['tenant' => $tenant2, 'lease' => $lease2] = $this->tenantWithLease();

        RepairRequest::create([
            'tenant_id'   => $tenant2->id,
            'lease_id'    => $lease2->id,
            'type'        => 'peinture',
            'description' => 'Problème autre tenant',
            'urgency'     => 'faible',
            'status'      => 'recu',
            'ticket_no'   => 'REP-999',
        ]);

        $this->actingAs($tenant1, 'tenant')
            ->get('/espace-locataire/reparations')
            ->assertDontSee('REP-999');
    }
}
