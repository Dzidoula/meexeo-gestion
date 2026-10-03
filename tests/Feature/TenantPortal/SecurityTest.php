<?php

namespace Tests\Feature\TenantPortal;

use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RepairRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    private function createTenantWithData(): array
    {
        $tenant   = Tenant::factory()->create();
        $property = Property::factory()->create();
        $lease    = Lease::factory()->active()->create([
            'tenant_id'   => $tenant->id,
            'property_id' => $property->id,
        ]);
        $payment = Payment::factory()->create([
            'lease_id' => $lease->id,
            'month'    => '2026-09-01',
            'amount'   => 150000,
            'paid_on'  => '2026-09-01',
        ]);
        $repair = RepairRequest::create([
            'tenant_id'   => $tenant->id,
            'lease_id'    => $lease->id,
            'type'        => 'plomberie',
            'description' => 'Fuite sous évier',
            'urgency'     => 'faible',
            'status'      => 'recu',
            'ticket_no'   => 'REP-001',
        ]);

        return compact('tenant', 'lease', 'property', 'payment', 'repair');
    }

    public function test_all_protected_routes_redirect_unauthenticated(): void
    {
        $routes = [
            '/espace-locataire/',
            '/espace-locataire/loyers',
            '/espace-locataire/paiements',
            '/espace-locataire/paiements/nouveau',
            '/espace-locataire/preuves/envoyer',
            '/espace-locataire/reparations',
            '/espace-locataire/reparations/nouveau',
            '/espace-locataire/mon-contrat',
            '/espace-locataire/profil',
        ];

        foreach ($routes as $url) {
            $this->get($url)->assertRedirect('/espace-locataire/connexion');
        }
    }

    public function test_admin_user_cannot_access_tenant_portal(): void
    {
        $admin = User::factory()->create(['role' => \App\Enums\Role::Admin]);

        $this->actingAs($admin)
            ->get('/espace-locataire/')
            ->assertRedirect('/espace-locataire/connexion');
    }

    public function test_unauthenticated_user_cannot_access_admin_dashboard(): void
    {
        // Guards use separate sessions in production — a tenant session never
        // carries a web (admin) credential.
        $this->get('/tableau-de-bord')
            ->assertRedirect('/connexion');
    }

    public function test_tenant_cannot_view_another_tenants_repair(): void
    {
        ['repair' => $repair1] = $this->createTenantWithData();

        $tenant2   = Tenant::factory()->create();
        $property2 = Property::factory()->create();
        Lease::factory()->active()->create(['tenant_id' => $tenant2->id, 'property_id' => $property2->id]);

        $this->actingAs($tenant2, 'tenant')
            ->get('/espace-locataire/reparations/'.$repair1->id)
            ->assertStatus(403);
    }

    public function test_protected_profile_fields_are_immutable(): void
    {
        ['tenant' => $tenant] = $this->createTenantWithData();

        $original = [
            'last_name'   => $tenant->last_name,
            'first_names' => $tenant->first_names,
            'phone1'      => $tenant->phone1,
            'id_number'   => $tenant->id_number,
        ];

        $this->actingAs($tenant, 'tenant')
            ->patch('/espace-locataire/profil', [
                'email'       => 'legit@example.com',
                'last_name'   => 'HACKED',
                'first_names' => 'HACKED',
                'phone1'      => '0000000000',
                'id_number'   => 'HACKED123',
            ]);

        $tenant->refresh();
        $this->assertEquals($original['last_name'],   $tenant->last_name);
        $this->assertEquals($original['first_names'], $tenant->first_names);
        $this->assertEquals($original['phone1'],      $tenant->phone1);
        $this->assertEquals($original['id_number'],   $tenant->id_number);
        $this->assertEquals('legit@example.com',      $tenant->email);
    }
}
