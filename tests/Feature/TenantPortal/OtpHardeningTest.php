<?php

namespace Tests\Feature\TenantPortal;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class OtpHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('otp-generate:127.0.0.1');
    }

    public function test_unknown_phone_attempts_are_rate_limited(): void
    {
        // Enumeration probes must consume the budget, or an attacker can test
        // every number for free.
        for ($i = 0; $i < 5; $i++) {
            $this->post('/espace-locataire/connexion', ['phone' => '0700000'.$i]);
        }

        $this->post('/espace-locataire/connexion', ['phone' => '0700000099'])
            ->assertStatus(429);
    }

    public function test_otp_is_invalidated_after_three_failed_attempts(): void
    {
        $tenant = Tenant::factory()->create(['phone1' => '0701020304']);

        $this->post('/espace-locataire/connexion', ['phone' => '0701020304']);

        $realCode = $tenant->fresh()->otp_code;
        $this->assertNotNull($realCode);

        for ($i = 0; $i < 3; $i++) {
            $this->post('/espace-locataire/verification', ['otp' => '000000']);
        }

        // One more attempt trips the limit and must burn the code.
        $this->post('/espace-locataire/verification', ['otp' => '000000']);

        $this->assertNull(
            $tenant->fresh()->otp_code,
            'After the attempt limit the stored OTP must be cleared, not left valid.'
        );
    }
}
