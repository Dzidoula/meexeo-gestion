<?php

namespace Tests\Feature\TenantPortal;

use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProofSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function tenant(): array
    {
        $tenant   = Tenant::factory()->create();
        $property = Property::factory()->create();
        $lease    = Lease::factory()->active()->create([
            'tenant_id'    => $tenant->id,
            'property_id'  => $property->id,
            'monthly_rent' => 150000,
            'start_date'   => now()->subMonths(3)->startOfMonth(),
            'due_day'      => 5,
        ]);

        return compact('tenant', 'lease');
    }

    public function test_upload_does_not_overwrite_a_manager_recorded_payment(): void
    {
        Storage::fake('public');
        ['tenant' => $tenant, 'lease' => $lease] = $this->tenant();

        $month    = now()->subMonth()->startOfMonth();
        $original = Payment::factory()->create([
            'lease_id'   => $lease->id,
            'month'      => $month->toDateString(),
            'amount'     => 150000,
            'paid_on'    => $month->copy()->setDay(3)->toDateString(),
            'proof_path' => 'payments/proofs/manager_receipt.jpg',
        ]);

        $this->actingAs($tenant, 'tenant')
            ->post('/espace-locataire/preuves', [
                'month'  => $month->format('Y-m'),
                'amount' => 150000,
                'proof'  => UploadedFile::fake()->image('tenant_upload.jpg'),
            ]);

        $this->assertEquals(
            'payments/proofs/manager_receipt.jpg',
            $original->fresh()->proof_path,
            'A tenant upload must never replace the proof on a manager-recorded payment.'
        );
    }

    public function test_portal_upload_is_marked_pending(): void
    {
        Storage::fake('public');
        ['tenant' => $tenant, 'lease' => $lease] = $this->tenant();

        $month = now()->subMonths(2)->startOfMonth();

        $this->actingAs($tenant, 'tenant')
            ->post('/espace-locataire/preuves', [
                'month'  => $month->format('Y-m'),
                'amount' => 150000,
                'proof'  => UploadedFile::fake()->image('receipt.jpg'),
            ]);

        $payment = $lease->payments()->whereNotNull('portal_status')->first();

        $this->assertNotNull($payment, 'A portal upload must create a payment row.');
        $this->assertEquals('pending', $payment->portal_status);
    }

    public function test_pending_payment_does_not_show_as_paid_on_rents_page(): void
    {
        Storage::fake('public');
        ['tenant' => $tenant, 'lease' => $lease] = $this->tenant();

        $month = now()->subMonth()->startOfMonth();

        Payment::factory()->create([
            'lease_id'      => $lease->id,
            'month'         => $month->toDateString(),
            'amount'        => 150000,
            'paid_on'       => now()->toDateString(),
            'portal_status' => 'pending',
        ]);

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/loyers')
            ->assertOk()
            ->assertSee('En attente de vérification');
    }

    public function test_month_dropdown_does_not_skip_short_months(): void
    {
        $tenant   = Tenant::factory()->create();
        $property = Property::factory()->create();
        Lease::factory()->active()->create([
            'tenant_id'    => $tenant->id,
            'property_id'  => $property->id,
            'monthly_rent' => 150000,
            // A 31st start date skips February unless the cursor is normalised.
            'start_date'   => '2026-01-31',
            'due_day'      => 5,
        ]);

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/preuves/envoyer')
            ->assertOk()
            ->assertSee('Février 2026')
            ->assertSee('Mars 2026');
    }

    public function test_invalid_month_is_rejected(): void
    {
        Storage::fake('public');
        ['tenant' => $tenant] = $this->tenant();

        $this->actingAs($tenant, 'tenant')
            ->post('/espace-locataire/preuves', [
                'month'  => '2026-13',
                'amount' => 150000,
                'proof'  => UploadedFile::fake()->image('receipt.jpg'),
            ])
            ->assertSessionHasErrors('month');
    }

    public function test_month_before_lease_start_is_rejected(): void
    {
        Storage::fake('public');
        ['tenant' => $tenant] = $this->tenant();

        $this->actingAs($tenant, 'tenant')
            ->post('/espace-locataire/preuves', [
                'month'  => now()->subYears(2)->format('Y-m'),
                'amount' => 150000,
                'proof'  => UploadedFile::fake()->image('receipt.jpg'),
            ])
            ->assertSessionHasErrors('month');
    }

    public function test_future_month_is_rejected(): void
    {
        Storage::fake('public');
        ['tenant' => $tenant] = $this->tenant();

        $this->actingAs($tenant, 'tenant')
            ->post('/espace-locataire/preuves', [
                'month'  => now()->addMonths(3)->format('Y-m'),
                'amount' => 150000,
                'proof'  => UploadedFile::fake()->image('receipt.jpg'),
            ])
            ->assertSessionHasErrors('month');
    }
}
