<?php
// tests/Feature/TenantWriteTest.php
namespace Tests\Feature;

use App\Enums\MaritalStatus;
use App\Enums\TenantStatus;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantWriteTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'last_name' => 'Koné',
            'first_names' => 'Awa',
            'phone1' => '0701020304',
            'marital_status' => MaritalStatus::Single->value,
            'status' => TenantStatus::Pending->value,
            'occupation' => 'Comptable',
        ], $overrides);
    }

    public function test_a_manager_can_create_a_tenant(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->post('/locataires', $this->validPayload())
            ->assertRedirect();

        $this->assertDatabaseHas('tenants', ['last_name' => 'Koné', 'phone1' => '0701020304']);
    }

    public function test_the_name_and_first_phone_are_required(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->from('/locataires/nouveau')
            ->post('/locataires', ['last_name' => '', 'first_names' => '', 'phone1' => ''])
            ->assertSessionHasErrors(['last_name', 'first_names', 'phone1']);
    }

    public function test_a_married_tenant_must_declare_a_spouse(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->from('/locataires/nouveau')
            ->post('/locataires', $this->validPayload([
                'marital_status' => MaritalStatus::Married->value,
                'spouse_name' => '',
                'spouse_phone' => '',
            ]))
            ->assertSessionHasErrors(['spouse_name', 'spouse_phone']);
    }

    public function test_a_single_tenant_needs_no_spouse(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->post('/locataires', $this->validPayload(['marital_status' => MaritalStatus::Single->value]))
            ->assertRedirect();

        $this->assertDatabaseCount('tenants', 1);
    }

    public function test_an_accountant_cannot_create_a_tenant(): void
    {
        $this->actingAs(User::factory()->accountant()->create())
            ->post('/locataires', $this->validPayload())
            ->assertForbidden();
    }

    public function test_the_record_shows_identity_and_contacts(): void
    {
        $tenant = Tenant::factory()->married()->create([
            'last_name' => 'Traoré', 'first_names' => 'Mamadou',
            'occupation' => 'Ingénieur', 'workplace' => 'SODECI',
            'phone1' => '0705060708',
        ]);

        $this->actingAs(User::factory()->viewer()->create())
            ->get("/locataires/{$tenant->id}")
            ->assertOk()
            ->assertSee('Mamadou Traoré')
            ->assertSee($tenant->reference)
            ->assertSee('Ingénieur')
            ->assertSee('SODECI')
            ->assertSee('0705060708')
            ->assertSee('Marié(e)')
            ->assertSee($tenant->spouse_name);
    }

    public function test_a_manager_can_update_a_tenant(): void
    {
        $tenant = Tenant::factory()->create(['last_name' => 'Ancien']);

        $this->actingAs(User::factory()->manager()->create())
            ->put("/locataires/{$tenant->id}", $this->validPayload(['last_name' => 'Nouveau']))
            ->assertRedirect();

        $this->assertSame('Nouveau', $tenant->fresh()->last_name);
    }
}
