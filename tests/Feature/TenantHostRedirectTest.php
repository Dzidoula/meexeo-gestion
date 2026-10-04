<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantHostRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_tenant_hostname_root_redirects_into_the_portal(): void
    {
        config(['tenant-portal.tenant_host' => 'locataire.masterclays.net']);

        $this->get('http://locataire.masterclays.net/')
            ->assertRedirect(route('tenant-portal.login'));
    }

    public function test_other_hostnames_still_see_the_public_storefront(): void
    {
        config(['tenant-portal.tenant_host' => 'locataire.masterclays.net']);

        $this->get('http://admin.masterclays.net/')
            ->assertOk()
            ->assertSee('véhicule');
    }

    public function test_the_bare_masterclays_domain_still_sees_the_storefront(): void
    {
        config(['tenant-portal.tenant_host' => 'locataire.masterclays.net']);

        $this->get('http://masterclays.net/')
            ->assertOk()
            ->assertSee('véhicule');
    }
}
