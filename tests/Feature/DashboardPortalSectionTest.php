<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RepairRequest;
use App\Models\Tenant;
use App\Models\TenantMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardPortalSectionTest extends TestCase
{
    use RefreshDatabase;

    private function manager(): User
    {
        return User::factory()->create(['role' => Role::Manager]);
    }

    private function tenantWithLease(): array
    {
        $tenant   = Tenant::factory()->create();
        $property = Property::factory()->create();
        $lease    = Lease::factory()->active()->create([
            'tenant_id'    => $tenant->id,
            'property_id'  => $property->id,
            'monthly_rent' => 150000,
        ]);

        return compact('tenant', 'lease');
    }

    public function test_the_dashboard_shows_the_portal_section(): void
    {
        $this->actingAs($this->manager(), 'web')
            ->get('/tableau-de-bord')
            ->assertOk()
            ->assertSee('Portail locataire')
            ->assertSee('Preuves à vérifier');
    }

    public function test_a_pending_proof_is_counted_on_the_dashboard(): void
    {
        ['lease' => $lease] = $this->tenantWithLease();

        Payment::factory()->create([
            'lease_id'      => $lease->id,
            'month'         => now()->startOfMonth()->toDateString(),
            'amount'        => 150000,
            'paid_on'       => now()->toDateString(),
            'portal_status' => 'pending',
        ]);

        $this->actingAs($this->manager(), 'web')
            ->get('/tableau-de-bord')
            ->assertOk()
            ->assertViewHas('portalPendingProofs', 1);
    }

    public function test_a_validated_proof_leaves_the_count(): void
    {
        ['lease' => $lease] = $this->tenantWithLease();

        $payment = Payment::factory()->create([
            'lease_id'      => $lease->id,
            'month'         => now()->startOfMonth()->toDateString(),
            'amount'        => 150000,
            'paid_on'       => now()->toDateString(),
            'portal_status' => 'pending',
        ]);

        $manager = $this->manager();
        $this->actingAs($manager, 'web')->patch("/preuves/{$payment->id}/valider");

        $this->actingAs($manager, 'web')
            ->get('/tableau-de-bord')
            ->assertViewHas('portalPendingProofs', 0);
    }

    public function test_only_tenant_written_messages_count_as_unanswered(): void
    {
        ['tenant' => $tenant] = $this->tenantWithLease();

        TenantMessage::create([
            'tenant_id' => $tenant->id, 'sender' => 'tenant',
            'subject' => 'Question', 'body' => 'Bonjour', 'read_at' => null,
        ]);

        // Un message du gestionnaire non lu par le locataire ne lui réclame rien.
        TenantMessage::create([
            'tenant_id' => $tenant->id, 'sender' => 'manager',
            'subject' => 'Info', 'body' => 'Bonjour', 'read_at' => null,
        ]);

        $this->actingAs($this->manager(), 'web')
            ->get('/tableau-de-bord')
            ->assertViewHas('portalUnreadMessages', 1);
    }

    public function test_only_open_repairs_are_counted(): void
    {
        ['tenant' => $tenant, 'lease' => $lease] = $this->tenantWithLease();

        foreach (['recu', 'en_cours', 'repare', 'cloture'] as $status) {
            RepairRequest::create([
                'tenant_id'   => $tenant->id,
                'lease_id'    => $lease->id,
                'type'        => 'plomberie',
                'description' => 'Fuite sous évier de la cuisine',
                'urgency'     => 'moyenne',
                'status'      => $status,
            ]);
        }

        $this->actingAs($this->manager(), 'web')
            ->get('/tableau-de-bord')
            ->assertViewHas('portalOpenRepairs', 2);
    }

    public function test_the_oldest_pending_proof_age_is_surfaced(): void
    {
        ['lease' => $lease] = $this->tenantWithLease();

        $old = Payment::factory()->create([
            'lease_id'      => $lease->id,
            'month'         => now()->subMonth()->startOfMonth()->toDateString(),
            'amount'        => 150000,
            'paid_on'       => now()->toDateString(),
            'portal_status' => 'pending',
        ]);
        $old->forceFill(['created_at' => now()->subDays(4)])->save();

        $this->actingAs($this->manager(), 'web')
            ->get('/tableau-de-bord')
            ->assertOk()
            ->assertSee('La plus ancienne attend depuis');
    }
}
