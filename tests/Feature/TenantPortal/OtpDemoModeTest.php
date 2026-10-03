<?php

namespace Tests\Feature\TenantPortal;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class OtpDemoModeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('otp-generate:127.0.0.1');
    }

    private function tenant(): Tenant
    {
        return Tenant::factory()->create(['phone1' => '0708091011']);
    }

    private function requestCode(): void
    {
        $this->post('/espace-locataire/connexion', ['phone' => '0708091011']);
    }

    public function test_code_is_shown_on_screen_when_demo_mode_is_enabled(): void
    {
        config(['tenant-portal.show_otp_on_screen' => true]);
        $tenant = $this->tenant();

        $this->requestCode();

        $this->get('/espace-locataire/verification')
            ->assertOk()
            ->assertSee($tenant->fresh()->otp_code);
    }

    public function test_demo_mode_warns_that_the_portal_is_not_secure(): void
    {
        config(['tenant-portal.show_otp_on_screen' => true]);
        $this->tenant();

        $this->requestCode();

        $this->get('/espace-locataire/verification')
            ->assertOk()
            ->assertSee('Démonstration');
    }

    public function test_code_is_never_shown_when_demo_mode_is_disabled(): void
    {
        // The production default: an OTP on screen would hand any visitor the
        // account of any phone number they can guess.
        config(['tenant-portal.show_otp_on_screen' => false]);
        $tenant = $this->tenant();

        $this->requestCode();

        $this->get('/espace-locataire/verification')
            ->assertOk()
            ->assertDontSee($tenant->fresh()->otp_code)
            ->assertDontSee('Démonstration');
    }

    public function test_demo_mode_defaults_to_off(): void
    {
        $this->assertFalse(
            (bool) config('tenant-portal.show_otp_on_screen'),
            'Showing OTP codes must be opt-in, never the default.'
        );
    }
}
