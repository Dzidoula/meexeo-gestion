<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhoneNormalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_tenant_created_with_a_bare_local_number_is_stored_canonically(): void
    {
        $tenant = Tenant::factory()->create(['phone1' => '0151414430']);

        $this->assertSame('+2250151414430', $tenant->fresh()->phone1);
    }

    public function test_a_tenant_created_with_spaces_is_stored_canonically(): void
    {
        $tenant = Tenant::factory()->create(['phone1' => '01 51 41 44 30']);

        $this->assertSame('+2250151414430', $tenant->fresh()->phone1);
    }

    public function test_logging_in_with_just_the_local_number_finds_the_tenant(): void
    {
        Tenant::factory()->create(['phone1' => '0151414430']);

        // Stored canonically as +2250151414430, but the tenant only ever types
        // the local part — the UI shows the +225 prefix, they don't.
        $this->post('/espace-locataire/connexion', ['phone' => '0151414430'])
            ->assertRedirect(route('tenant-portal.verify'));
    }

    public function test_logging_in_with_spaces_or_dashes_still_finds_the_tenant(): void
    {
        Tenant::factory()->create(['phone1' => '0151414430']);

        $this->post('/espace-locataire/connexion', ['phone' => '01-51-41-44-30'])
            ->assertRedirect(route('tenant-portal.verify'));
    }
}
