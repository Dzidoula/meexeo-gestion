<?php

namespace Tests\Feature\TenantPortal;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantAuthModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_is_authenticatable(): void
    {
        $tenant = Tenant::factory()->create();
        $this->assertInstanceOf(\Illuminate\Foundation\Auth\User::class, $tenant);
    }

    public function test_generate_otp_returns_six_digit_string(): void
    {
        $tenant = Tenant::factory()->create();
        $code = $tenant->generateOtp();

        $this->assertMatchesRegularExpression('/^\d{6}$/', $code);
    }

    public function test_generate_otp_stores_code_and_expiry_on_model(): void
    {
        $tenant = Tenant::factory()->create();
        $tenant->generateOtp();
        $tenant->refresh();

        $this->assertNotNull($tenant->otp_code);
        $this->assertNotNull($tenant->otp_expires_at);
        $this->assertTrue($tenant->otp_expires_at->isFuture());
    }

    public function test_verify_otp_returns_true_for_correct_unexpired_code(): void
    {
        $tenant = Tenant::factory()->create();
        $code = $tenant->generateOtp();

        $this->assertTrue($tenant->fresh()->verifyOtp($code));
    }

    public function test_verify_otp_returns_false_for_wrong_code(): void
    {
        $tenant = Tenant::factory()->create();
        $tenant->generateOtp();

        $this->assertFalse($tenant->fresh()->verifyOtp('000000'));
    }

    public function test_verify_otp_returns_false_for_expired_code(): void
    {
        $tenant = Tenant::factory()->create();
        $code = $tenant->generateOtp();

        $tenant->forceFill(['otp_expires_at' => now()->subMinute()])->save();

        $this->assertFalse($tenant->fresh()->verifyOtp($code));
    }

    public function test_verify_otp_clears_code_after_success(): void
    {
        $tenant = Tenant::factory()->create();
        $code = $tenant->generateOtp();
        $tenant->fresh()->verifyOtp($code);

        $this->assertNull($tenant->fresh()->otp_code);
    }
}
