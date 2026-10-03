<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalProofReviewTest extends TestCase
{
    use RefreshDatabase;

    private function pendingProof(): array
    {
        $manager  = User::factory()->create(['role' => Role::Manager]);
        $tenant   = Tenant::factory()->create();
        $property = Property::factory()->create();
        $lease    = Lease::factory()->active()->create([
            'tenant_id'    => $tenant->id,
            'property_id'  => $property->id,
            'monthly_rent' => 150000,
            'start_date'   => now()->subMonths(2)->startOfMonth(),
            'due_day'      => 5,
        ]);

        $payment = Payment::factory()->create([
            'lease_id'      => $lease->id,
            'month'         => now()->startOfMonth()->toDateString(),
            'amount'        => 150000,
            'paid_on'       => now()->toDateString(),
            'portal_status' => 'pending',
        ]);

        return compact('manager', 'tenant', 'lease', 'payment');
    }

    public function test_pending_proofs_are_listed_for_a_manager(): void
    {
        ['manager' => $manager, 'tenant' => $tenant] = $this->pendingProof();

        $this->actingAs($manager, 'web')
            ->get('/preuves')
            ->assertOk()
            ->assertSee($tenant->fullName);
    }

    public function test_approving_turns_the_proof_into_a_real_payment(): void
    {
        ['manager' => $manager, 'payment' => $payment] = $this->pendingProof();

        $this->actingAs($manager, 'web')
            ->patch("/preuves/{$payment->id}/valider")
            ->assertRedirect();

        $this->assertNull($payment->fresh()->portal_status);
    }

    public function test_an_approved_month_reads_as_paid_for_the_tenant(): void
    {
        ['manager' => $manager, 'tenant' => $tenant, 'payment' => $payment] = $this->pendingProof();

        // Avant validation, le mois ne doit pas compter comme réglé.
        $before = $this->actingAs($tenant, 'tenant')->get('/espace-locataire/paiements');
        $this->assertStringContainsString('En vérification', $before->getContent(), 'AVANT validation');

        $this->actingAs($manager, 'web')->patch("/preuves/{$payment->id}/valider")->assertRedirect();
        $this->assertNull($payment->fresh()->portal_status, 'La validation doit avoir persisté');

        $after = $this->actingAs($tenant, 'tenant')->get('/espace-locataire/paiements');
        $this->assertStringNotContainsString('En vérification', $after->getContent(), 'APRES validation');
    }

    public function test_rejecting_requires_a_reason(): void
    {
        ['manager' => $manager, 'payment' => $payment] = $this->pendingProof();

        $this->actingAs($manager, 'web')
            ->patch("/preuves/{$payment->id}/refuser", ['reason' => ''])
            ->assertSessionHasErrors('reason');

        $this->assertEquals('pending', $payment->fresh()->portal_status);
    }

    public function test_the_rejection_reason_reaches_the_tenant(): void
    {
        ['manager' => $manager, 'tenant' => $tenant, 'payment' => $payment] = $this->pendingProof();

        $this->actingAs($manager, 'web')
            ->patch("/preuves/{$payment->id}/refuser", ['reason' => 'Le reçu est illisible']);

        $this->assertEquals('rejected', $payment->fresh()->portal_status);

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/paiements')
            ->assertSee('Preuve refusée')
            ->assertSee('Le reçu est illisible');
    }

    public function test_a_rejected_proof_never_counts_as_paid(): void
    {
        ['manager' => $manager, 'lease' => $lease, 'payment' => $payment] = $this->pendingProof();

        $this->actingAs($manager, 'web')
            ->patch("/preuves/{$payment->id}/refuser", ['reason' => 'Montant incorrect']);

        $schedule = \App\Support\RentSchedule::forLease($lease->fresh());
        $current  = $schedule->firstWhere('key', now()->format('Y-m'));

        $this->assertSame(0, $current['paid'], 'Une preuve refusée ne doit rien créditer.');
        $this->assertSame('rejected', $current['status']);
    }

    public function test_an_already_decided_proof_cannot_be_decided_again(): void
    {
        ['manager' => $manager, 'payment' => $payment] = $this->pendingProof();

        $this->actingAs($manager, 'web')->patch("/preuves/{$payment->id}/valider");

        $this->actingAs($manager, 'web')
            ->patch("/preuves/{$payment->id}/valider")
            ->assertNotFound();
    }

    public function test_a_tenant_cannot_reach_the_review_screen(): void
    {
        ['tenant' => $tenant] = $this->pendingProof();

        $this->actingAs($tenant, 'tenant')
            ->get('/preuves')
            ->assertForbidden();
    }
}
