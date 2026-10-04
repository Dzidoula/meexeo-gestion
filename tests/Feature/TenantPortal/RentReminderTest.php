<?php

namespace Tests\Feature\TenantPortal;

use App\Enums\LeaseStatus;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Rappels de loyer J-5 et jour J — cahier des charges, §11. */
class RentReminderTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function lease(int $dueDay, array $overrides = []): Lease
    {
        return Lease::factory()->active()->create(array_merge([
            'tenant_id'    => Tenant::factory()->create()->id,
            'property_id'  => Property::factory()->create()->id,
            'monthly_rent' => 150000,
            'start_date'   => '2026-01-01',
            'due_day'      => $dueDay,
        ], $overrides));
    }

    private function bodies(Lease $lease): array
    {
        return $lease->tenant->fresh()->notifications->pluck('data.body')->all();
    }

    private function run_(): void
    {
        $this->artisan('portal:send-rent-reminders')->assertSuccessful();
    }

    public function test_five_days_before_the_due_date_the_tenant_is_reminded(): void
    {
        Carbon::setTestNow('2026-10-03 08:00:00');
        $lease = $this->lease(dueDay: 8);

        $this->run_();

        $this->assertSame(['Votre loyer arrive à échéance dans 5 jours.'], $this->bodies($lease));
    }

    public function test_on_the_due_date_the_tenant_is_told_it_is_due_today(): void
    {
        Carbon::setTestNow('2026-10-08 08:00:00');
        $lease = $this->lease(dueDay: 8);

        $this->run_();

        $this->assertSame(['Votre loyer est dû aujourd\'hui.'], $this->bodies($lease));
    }

    public function test_other_days_stay_silent(): void
    {
        Carbon::setTestNow('2026-10-05 08:00:00'); // J-3
        $lease = $this->lease(dueDay: 8);

        $this->run_();

        $this->assertSame([], $this->bodies($lease));
    }

    public function test_a_settled_month_is_not_reminded(): void
    {
        Carbon::setTestNow('2026-10-08 08:00:00');
        $lease = $this->lease(dueDay: 8);
        Payment::factory()->create([
            'lease_id' => $lease->id, 'month' => '2026-10-01', 'amount' => 150000, 'paid_on' => '2026-10-02',
        ]);

        $this->run_();

        $this->assertSame([], $this->bodies($lease));
    }

    public function test_a_proof_under_review_is_not_nagged(): void
    {
        Carbon::setTestNow('2026-10-08 08:00:00');
        $lease = $this->lease(dueDay: 8);
        Payment::factory()->create([
            'lease_id' => $lease->id, 'month' => '2026-10-01', 'amount' => 150000,
            'paid_on' => '2026-10-07', 'portal_status' => 'pending',
        ]);

        $this->run_();

        $this->assertSame([], $this->bodies($lease));
    }

    public function test_the_reminder_looks_into_next_month_near_month_end(): void
    {
        Carbon::setTestNow('2026-10-29 08:00:00'); // échéance le 3 novembre : J-5
        $lease = $this->lease(dueDay: 3);

        $this->run_();

        $this->assertSame(['Votre loyer arrive à échéance dans 5 jours.'], $this->bodies($lease));
    }

    public function test_running_twice_the_same_day_sends_it_once(): void
    {
        Carbon::setTestNow('2026-10-03 08:00:00');
        $lease = $this->lease(dueDay: 8);

        $this->run_();
        $this->run_();

        $this->assertCount(1, $this->bodies($lease));
    }

    public function test_an_ended_lease_is_not_reminded(): void
    {
        Carbon::setTestNow('2026-10-03 08:00:00');
        $lease = $this->lease(dueDay: 8, overrides: ['status' => LeaseStatus::Ended]);

        $this->run_();

        $this->assertSame([], $this->bodies($lease));
    }

    public function test_a_due_date_before_the_lease_starts_is_not_reminded(): void
    {
        Carbon::setTestNow('2026-10-29 08:00:00');
        // Le bail ne commence que le 20 novembre : l'échéance du 3 est antérieure.
        $lease = $this->lease(dueDay: 3, overrides: ['start_date' => '2026-11-20']);

        $this->run_();

        $this->assertSame([], $this->bodies($lease));
    }

    public function test_the_command_is_scheduled_daily(): void
    {
        $this->artisan('schedule:list')->expectsOutputToContain('portal:send-rent-reminders');
    }
}
