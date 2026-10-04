<?php

namespace Tests\Feature\TenantPortal;

use App\Enums\Role;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RepairRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Textes et événements : cahier des charges, §11. */
class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    private function setup_(): array
    {
        $tenant = Tenant::factory()->create();
        $lease  = Lease::factory()->active()->create([
            'tenant_id'    => $tenant->id,
            'property_id'  => Property::factory()->create()->id,
            'monthly_rent' => 150000,
            'start_date'   => now()->subMonth()->startOfMonth(),
            'due_day'      => 5,
        ]);

        return compact('tenant', 'lease');
    }

    private function bodies(Tenant $tenant): array
    {
        return $tenant->fresh()->notifications->pluck('data.body')->all();
    }

    private function manager(): User
    {
        return User::factory()->create(['role' => Role::Manager]);
    }

    private function pendingProof(Lease $lease): Payment
    {
        return Payment::factory()->create([
            'lease_id'      => $lease->id,
            'month'         => now()->subMonth()->startOfMonth()->toDateString(),
            'amount'        => 150000,
            'paid_on'       => now()->toDateString(),
            'portal_status' => 'pending',
        ]);
    }

    public function test_uploading_a_proof_notifies_the_tenant(): void
    {
        Storage::fake('public');
        ['tenant' => $tenant] = $this->setup_();

        $this->actingAs($tenant, 'tenant')->post('/espace-locataire/preuves', [
            'month'          => now()->subMonth()->format('Y-m'),
            'amount'         => 150000,
            'payment_method' => 'wave',
            'proof'          => UploadedFile::fake()->image('recu.jpg'),
        ])->assertRedirect();

        $this->assertContains('Votre preuve est en cours de vérification.', $this->bodies($tenant));
    }

    public function test_approving_a_proof_notifies_the_tenant(): void
    {
        ['tenant' => $tenant, 'lease' => $lease] = $this->setup_();
        $payment = $this->pendingProof($lease);

        $this->actingAs($this->manager(), 'web')->patch("/preuves/{$payment->id}/valider")->assertRedirect();

        $this->assertContains('Votre paiement a été validé.', $this->bodies($tenant));
    }

    public function test_rejecting_a_proof_notifies_the_tenant_with_the_reason(): void
    {
        ['tenant' => $tenant, 'lease' => $lease] = $this->setup_();
        $payment = $this->pendingProof($lease);

        $this->actingAs($this->manager(), 'web')
            ->patch("/preuves/{$payment->id}/refuser", ['reason' => 'Le reçu est illisible'])
            ->assertRedirect();

        $this->assertContains('Votre preuve a été rejetée : Le reçu est illisible.', $this->bodies($tenant));
    }

    public function test_reporting_a_repair_notifies_the_tenant_with_its_ticket(): void
    {
        ['tenant' => $tenant] = $this->setup_();

        $this->actingAs($tenant, 'tenant')->post('/espace-locataire/reparations', [
            'type'        => 'plomberie',
            'description' => 'Fuite sous l\'évier de la cuisine',
            'urgency'     => 'moyenne',
        ])->assertRedirect();

        $ticket = RepairRequest::where('tenant_id', $tenant->id)->value('ticket_no');

        $this->assertMatchesRegularExpression('/^REP-\d+$/', $ticket, 'Le ticket final doit être attribué.');
        $this->assertContains("Votre signalement #{$ticket} a été enregistré.", $this->bodies($tenant));
    }

    public function test_a_repair_moving_to_in_progress_or_closed_notifies_the_tenant(): void
    {
        ['tenant' => $tenant, 'lease' => $lease] = $this->setup_();
        $repair = RepairRequest::create([
            'tenant_id' => $tenant->id, 'lease_id' => $lease->id, 'type' => 'serrure',
            'description' => 'La serrure force beaucoup', 'urgency' => 'urgente', 'status' => 'recu',
        ]);

        $repair->update(['status' => 'en_cours']);
        $repair->update(['status' => 'cloture']);

        $bodies = $this->bodies($tenant);
        $this->assertContains("Votre réparation #{$repair->ticket_no} est maintenant en cours.", $bodies);
        $this->assertContains("Votre réparation #{$repair->ticket_no} a été clôturée.", $bodies);
    }

    public function test_editing_a_repair_without_changing_its_status_stays_silent(): void
    {
        ['tenant' => $tenant, 'lease' => $lease] = $this->setup_();
        $repair = RepairRequest::create([
            'tenant_id' => $tenant->id, 'lease_id' => $lease->id, 'type' => 'serrure',
            'description' => 'La serrure force beaucoup', 'urgency' => 'urgente', 'status' => 'recu',
        ]);

        $repair->update(['notes' => 'Un technicien passera demain.']);

        $this->assertSame([], $this->bodies($tenant));
    }

    public function test_the_notifications_page_lists_them_and_marks_them_read(): void
    {
        ['tenant' => $tenant, 'lease' => $lease] = $this->setup_();
        $payment = $this->pendingProof($lease);
        $this->actingAs($this->manager(), 'web')->patch("/preuves/{$payment->id}/valider");

        $this->assertSame(1, $tenant->fresh()->unreadNotifications()->count());

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/notifications')
            ->assertOk()
            ->assertSee('Votre paiement a été validé.');

        $this->assertSame(0, $tenant->fresh()->unreadNotifications()->count());
    }

    public function test_the_bell_shows_an_unread_dot(): void
    {
        ['tenant' => $tenant, 'lease' => $lease] = $this->setup_();
        $payment = $this->pendingProof($lease);
        $this->actingAs($this->manager(), 'web')->patch("/preuves/{$payment->id}/valider");

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire')
            ->assertOk()
            ->assertSee('Votre paiement a été validé.')
            ->assertSee('animate-pulse-soft');
    }

    public function test_a_tenant_never_sees_another_tenants_notifications(): void
    {
        ['tenant' => $a, 'lease' => $leaseA] = $this->setup_();
        ['tenant' => $b] = $this->setup_();
        $payment = $this->pendingProof($leaseA);
        $this->actingAs($this->manager(), 'web')->patch("/preuves/{$payment->id}/valider");

        $this->actingAs($b, 'tenant')
            ->get('/espace-locataire/notifications')
            ->assertOk()
            ->assertDontSee('Votre paiement a été validé.');
    }
}
