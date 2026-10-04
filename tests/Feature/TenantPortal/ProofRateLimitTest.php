<?php

namespace Tests\Feature\TenantPortal;

use App\Models\Lease;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Cahier des charges, §7 : « Upload preuves : 10/heure par tenant ». */
class ProofRateLimitTest extends TestCase
{
    use RefreshDatabase;

    private function tenant(): Tenant
    {
        $tenant = Tenant::factory()->create();
        Lease::factory()->active()->create([
            'tenant_id'    => $tenant->id,
            'property_id'  => Property::factory()->create()->id,
            'monthly_rent' => 150000,
            'start_date'   => now()->subMonth()->startOfMonth(),
        ]);

        return $tenant;
    }

    private function upload(Tenant $tenant)
    {
        return $this->actingAs($tenant, 'tenant')->post('/espace-locataire/preuves', [
            'month'          => now()->subMonth()->format('Y-m'),
            'amount'         => 150000,
            'payment_method' => 'wave',
            'proof'          => UploadedFile::fake()->image('recu.jpg'),
        ]);
    }

    public function test_the_eleventh_upload_within_an_hour_is_refused(): void
    {
        Storage::fake('public');
        $tenant = $this->tenant();

        for ($i = 1; $i <= 10; $i++) {
            $this->upload($tenant)->assertRedirect();
        }

        $this->upload($tenant)->assertStatus(429);
    }

    public function test_one_tenant_hitting_the_limit_does_not_block_another(): void
    {
        Storage::fake('public');
        $a = $this->tenant();
        $b = $this->tenant();

        for ($i = 1; $i <= 11; $i++) {
            $this->upload($a);
        }

        $this->upload($b)->assertRedirect();
    }
}
