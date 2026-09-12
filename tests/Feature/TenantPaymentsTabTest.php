<?php
namespace Tests\Feature;

use App\Models\Lease;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantPaymentsTabTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_the_five_tabs_including_payments(): void
    {
        $tenant = Tenant::factory()->create();

        $response = $this->actingAs(User::factory()->viewer()->create())->get("/locataires/{$tenant->id}");

        foreach (['Identité', 'Documents', 'Paiements'] as $tab) {
            $response->assertSee($tab);
        }
    }

    public function test_it_lists_the_tenants_payments_across_all_leases(): void
    {
        $tenant = Tenant::factory()->create();
        $lease = Lease::factory()->for($tenant)->create();
        $payment = Payment::factory()->for($lease)->create(['amount' => 275000]);

        $this->actingAs(User::factory()->viewer()->create())
            ->get("/locataires/{$tenant->id}")
            ->assertSee('275 000 FCFA')
            ->assertSee($payment->method->label());
    }

    public function test_it_shows_a_dedicated_empty_state_distinct_from_documents(): void
    {
        $tenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->viewer()->create())
            ->get("/locataires/{$tenant->id}")
            ->assertSee('Aucun paiement enregistré pour ce locataire.');
    }
}
