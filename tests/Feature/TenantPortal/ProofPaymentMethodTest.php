<?php

namespace Tests\Feature\TenantPortal;

use App\Enums\PaymentMethod;
use App\Models\Lease;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProofPaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    private function tenant(): array
    {
        $tenant = Tenant::factory()->create();
        $lease  = Lease::factory()->active()->create([
            'tenant_id'    => $tenant->id,
            'property_id'  => Property::factory()->create()->id,
            'monthly_rent' => 150000,
            'start_date'   => now()->subMonth()->startOfMonth(),
        ]);

        return compact('tenant', 'lease');
    }

    private function submitProof(array $extra = [])
    {
        ['tenant' => $tenant] = $this->tenant();

        return $this->actingAs($tenant, 'tenant')->post('/espace-locataire/preuves', array_merge([
            'month'  => now()->subMonth()->format('Y-m'),
            'amount' => 150000,
            'proof'  => UploadedFile::fake()->image('recu.jpg'),
        ], $extra));
    }

    public function test_the_form_asks_for_the_payment_method(): void
    {
        ['tenant' => $tenant] = $this->tenant();

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/preuves/envoyer')
            ->assertOk()
            ->assertSee('Mode de paiement')
            ->assertSee('Wave')
            ->assertSee('Orange Money');
    }

    public function test_the_chosen_method_is_stored_not_a_default(): void
    {
        Storage::fake('public');

        $this->submitProof(['payment_method' => PaymentMethod::Wave->value])->assertRedirect();

        // Avant : toute preuve était enregistrée « Espèces », même payée par Wave.
        $this->assertDatabaseHas('payments', ['method' => 'wave', 'portal_status' => 'pending']);
        $this->assertDatabaseMissing('payments', ['method' => 'cash']);
    }

    public function test_the_method_is_required(): void
    {
        Storage::fake('public');

        $this->submitProof()->assertSessionHasErrors('payment_method');
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_an_unknown_method_is_rejected(): void
    {
        Storage::fake('public');

        $this->submitProof(['payment_method' => 'bitcoin'])->assertSessionHasErrors('payment_method');
    }

    public function test_the_manager_sees_the_method_on_the_review_screen(): void
    {
        Storage::fake('public');
        $manager = \App\Models\User::factory()->create(['role' => \App\Enums\Role::Manager]);

        $this->submitProof(['payment_method' => PaymentMethod::OrangeMoney->value])->assertRedirect();

        $this->actingAs($manager, 'web')
            ->get('/preuves')
            ->assertOk()
            ->assertSee('Orange Money');
    }
}
