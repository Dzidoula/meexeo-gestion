<?php
namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_amount_is_stored_as_a_whole_franc_integer(): void
    {
        $payment = Payment::factory()->create(['amount' => 450000]);

        $this->assertSame(450000, $payment->fresh()->amount);
        $this->assertIsInt($payment->fresh()->amount);
    }

    public function test_the_method_is_cast_to_its_enum(): void
    {
        $payment = Payment::factory()->method(PaymentMethod::Wave)->create();

        $this->assertSame(PaymentMethod::Wave, $payment->fresh()->method);
    }

    public function test_the_url_uses_the_public_disk_convention(): void
    {
        Storage::fake('public');
        $payment = Payment::factory()->create(['proof_path' => 'payments/proofs/x.jpg']);

        $this->assertStringStartsWith('/storage/payments/proofs/', $payment->url);
    }

    public function test_a_lease_has_many_payments(): void
    {
        $lease = Lease::factory()->create();
        Payment::factory()->for($lease)->count(2)->create();

        $this->assertCount(2, $lease->fresh()->payments);
    }

    public function test_a_tenant_reaches_payments_through_their_leases(): void
    {
        $tenant = Tenant::factory()->create();
        $lease = Lease::factory()->for($tenant)->create();
        Payment::factory()->for($lease)->create();

        $this->assertCount(1, $tenant->fresh()->payments);
    }

    public function test_the_forMonth_state_sets_the_month_column(): void
    {
        $month = now()->startOfMonth();
        $payment = Payment::factory()->forMonth($month)->create();

        $this->assertTrue($payment->fresh()->month->isSameMonth($month));
    }
}
