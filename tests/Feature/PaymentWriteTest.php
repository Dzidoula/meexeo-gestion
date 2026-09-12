<?php
namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Models\Lease;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentWriteTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(Lease $lease, array $overrides = []): array
    {
        return array_merge([
            'lease_id' => $lease->id,
            'month' => now()->startOfMonth()->toDateString(),
            'paid_on' => now()->toDateString(),
            'amount' => $lease->monthly_rent,
            'method' => PaymentMethod::Wave->value,
            'reference' => 'TX-0099',
            'proof' => UploadedFile::fake()->image('recu.jpg'),
        ], $overrides);
    }

    public function test_a_manager_can_record_a_payment_with_proof(): void
    {
        Storage::fake('public');
        $lease = Lease::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->post('/paiements', $this->validPayload($lease))
            ->assertRedirect();

        $payment = $lease->fresh()->payments()->sole();
        $this->assertSame($lease->monthly_rent, $payment->amount);
        Storage::disk('public')->assertExists($payment->proof_path);
    }

    public function test_an_accountant_can_also_record_a_payment(): void
    {
        Storage::fake('public');
        $lease = Lease::factory()->create();

        $this->actingAs(User::factory()->accountant()->create())
            ->post('/paiements', $this->validPayload($lease))
            ->assertRedirect();

        $this->assertDatabaseCount('payments', 1);
    }

    public function test_a_viewer_cannot_record_a_payment(): void
    {
        Storage::fake('public');
        $lease = Lease::factory()->create();

        $this->actingAs(User::factory()->viewer()->create())
            ->post('/paiements', $this->validPayload($lease))
            ->assertForbidden();
    }

    public function test_a_payment_without_proof_is_rejected(): void
    {
        Storage::fake('public');
        $lease = Lease::factory()->create();
        $payload = $this->validPayload($lease);
        unset($payload['proof']);

        $this->actingAs(User::factory()->manager()->create())
            ->from('/paiements/nouveau')
            ->post('/paiements', $payload)
            ->assertSessionHasErrors('proof');

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_a_decimal_amount_is_rejected(): void
    {
        Storage::fake('public');
        $lease = Lease::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->from('/paiements/nouveau')
            ->post('/paiements', $this->validPayload($lease, ['amount' => '450000.50']))
            ->assertSessionHasErrors('amount');
    }

    public function test_a_zero_amount_is_rejected(): void
    {
        Storage::fake('public');
        $lease = Lease::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->from('/paiements/nouveau')
            ->post('/paiements', $this->validPayload($lease, ['amount' => 0]))
            ->assertSessionHasErrors('amount');
    }

    public function test_an_unknown_method_is_rejected(): void
    {
        Storage::fake('public');
        $lease = Lease::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->from('/paiements/nouveau')
            ->post('/paiements', $this->validPayload($lease, ['method' => 'bitcoin']))
            ->assertSessionHasErrors('method');
    }

    public function test_a_future_payment_date_is_rejected(): void
    {
        Storage::fake('public');
        $lease = Lease::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->from('/paiements/nouveau')
            ->post('/paiements', $this->validPayload($lease, ['paid_on' => now()->addDay()->toDateString()]))
            ->assertSessionHasErrors('paid_on');
    }

    public function test_the_creation_form_lists_active_leases_and_shows_month_cards(): void
    {
        $lease = Lease::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->get("/paiements/nouveau?lease={$lease->id}")
            ->assertOk()
            ->assertSee($lease->tenant->full_name)
            ->assertSee($lease->property->title);
    }
}
