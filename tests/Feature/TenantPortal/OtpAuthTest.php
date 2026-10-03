<?php

namespace Tests\Feature\TenantPortal;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class OtpAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders(): void
    {
        $this->get('/espace-locataire/connexion')->assertOk();
    }

    public function test_send_otp_with_unknown_phone_returns_error(): void
    {
        $this->post('/espace-locataire/connexion', ['phone' => '0700000000'])
            ->assertSessionHasErrors('phone');
    }

    public function test_send_otp_with_known_phone_redirects_to_verify(): void
    {
        Tenant::factory()->create(['phone1' => '0701234567']);

        $this->post('/espace-locataire/connexion', ['phone' => '0701234567'])
            ->assertRedirect('/espace-locataire/verification');
    }

    public function test_send_otp_stores_otp_on_tenant(): void
    {
        $tenant = Tenant::factory()->create(['phone1' => '0701234567']);

        $this->post('/espace-locataire/connexion', ['phone' => '0701234567']);

        $this->assertNotNull($tenant->fresh()->otp_code);
    }

    public function test_verify_page_renders(): void
    {
        $tenant = Tenant::factory()->create();
        $tenant->generateOtp();

        $this->withSession(['tenant_otp_id' => $tenant->id])
            ->get('/espace-locataire/verification')
            ->assertOk();
    }

    public function test_correct_otp_logs_in_tenant_and_redirects_to_dashboard(): void
    {
        $tenant = Tenant::factory()->create();
        $code = $tenant->generateOtp();

        $this->withSession(['tenant_otp_id' => $tenant->id])
            ->post('/espace-locataire/verification', ['otp' => $code])
            ->assertRedirect('/espace-locataire/');

        $this->assertTrue(Auth::guard('tenant')->check());
        $this->assertEquals($tenant->id, Auth::guard('tenant')->id());
    }

    public function test_wrong_otp_returns_error(): void
    {
        $tenant = Tenant::factory()->create();
        $tenant->generateOtp();

        $this->withSession(['tenant_otp_id' => $tenant->id])
            ->post('/espace-locataire/verification', ['otp' => '000000'])
            ->assertSessionHasErrors('otp');

        $this->assertFalse(Auth::guard('tenant')->check());
    }

    public function test_expired_otp_returns_error(): void
    {
        $tenant = Tenant::factory()->create();
        $code = $tenant->generateOtp();
        $tenant->forceFill(['otp_expires_at' => now()->subMinute()])->save();

        $this->withSession(['tenant_otp_id' => $tenant->id])
            ->post('/espace-locataire/verification', ['otp' => $code])
            ->assertSessionHasErrors('otp');
    }

    public function test_verify_without_session_redirects_to_login(): void
    {
        $this->post('/espace-locataire/verification', ['otp' => '123456'])
            ->assertRedirect('/espace-locataire/connexion');
    }

    public function test_logout_clears_session_and_redirects_to_login(): void
    {
        $tenant = Tenant::factory()->create();

        $this->actingAs($tenant, 'tenant')
            ->post('/espace-locataire/deconnexion')
            ->assertRedirect('/espace-locataire/connexion');

        $this->assertFalse(Auth::guard('tenant')->check());
    }

    public function test_rate_limit_blocks_after_five_otp_requests(): void
    {
        $tenant = Tenant::factory()->create(['phone1' => '0701234567']);
        $key = 'otp-generate:'.$this->app['request']->ip();

        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit($key, 3600);
        }

        $this->post('/espace-locataire/connexion', ['phone' => '0701234567'])
            ->assertStatus(429);
    }
}
