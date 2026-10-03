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

class ProofUploadTest extends TestCase
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
            'start_date'   => now()->subMonth()->startOfMonth(),
        ]);

        return compact('tenant', 'lease');
    }

    public function test_proof_upload_page_renders(): void
    {
        ['tenant' => $tenant] = $this->tenant();

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/preuves/envoyer')
            ->assertOk();
    }

    public function test_uploading_a_valid_image_stores_it_and_redirects(): void
    {
        Storage::fake('public');
        ['tenant' => $tenant, 'lease' => $lease] = $this->tenant();

        $this->actingAs($tenant, 'tenant')
            ->post('/espace-locataire/preuves', [
                'month'  => now()->subMonth()->format('Y-m'),
                'amount' => 150000,
                'proof'  => UploadedFile::fake()->image('wave_receipt.jpg'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('payments', ['lease_id' => $lease->id]);
    }

    public function test_uploading_a_zip_is_rejected(): void
    {
        Storage::fake('public');
        ['tenant' => $tenant] = $this->tenant();

        $this->actingAs($tenant, 'tenant')
            ->post('/espace-locataire/preuves', [
                'month'  => now()->subMonth()->format('Y-m'),
                'amount' => 150000,
                'proof'  => UploadedFile::fake()->create('malware.zip', 100, 'application/zip'),
            ])
            ->assertSessionHasErrors('proof');
    }

    public function test_proof_is_associated_with_tenant_lease(): void
    {
        Storage::fake('public');
        ['tenant' => $tenant, 'lease' => $lease] = $this->tenant();

        $this->actingAs($tenant, 'tenant')
            ->post('/espace-locataire/preuves', [
                'month'  => now()->subMonth()->format('Y-m'),
                'amount' => 150000,
                'proof'  => UploadedFile::fake()->image('receipt.png'),
            ]);

        $this->assertDatabaseHas('payments', [
            'lease_id' => $lease->id,
            'amount'   => 150000,
        ]);
    }
}
