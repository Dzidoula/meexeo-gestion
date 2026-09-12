<?php
namespace Tests\Unit;

use App\Support\PaymentMonthStatus;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class PaymentMonthStatusTest extends TestCase
{
    public function test_a_month_not_yet_due_has_no_status(): void
    {
        $month = Carbon::parse('2026-09-01');
        $today = Carbon::parse('2026-09-03'); // avant l'échéance du 5

        $this->assertNull(PaymentMonthStatus::for(100000, 0, $month, 5, $today));
    }

    public function test_a_future_month_has_no_status(): void
    {
        $month = Carbon::parse('2026-11-01');
        $today = Carbon::parse('2026-09-10');

        $this->assertNull(PaymentMonthStatus::for(100000, 0, $month, 5, $today));
    }

    public function test_a_fully_paid_month_is_paid_even_before_due_day(): void
    {
        $month = Carbon::parse('2026-09-01');
        $today = Carbon::parse('2026-09-03');

        $this->assertSame('paid', PaymentMonthStatus::for(100000, 100000, $month, 5, $today));
    }

    public function test_an_overpayment_still_counts_as_paid(): void
    {
        $month = Carbon::parse('2026-09-01');
        $today = Carbon::parse('2026-09-03');

        $this->assertSame('paid', PaymentMonthStatus::for(100000, 150000, $month, 5, $today));
    }

    public function test_a_partial_payment_is_partial_regardless_of_days_elapsed(): void
    {
        $month = Carbon::parse('2026-09-01');
        $today = Carbon::parse('2026-09-20'); // largement après l'échéance

        $this->assertSame('partial', PaymentMonthStatus::for(100000, 40000, $month, 5, $today));
    }

    public function test_unpaid_on_the_due_date_itself_is_late_not_unpaid(): void
    {
        $month = Carbon::parse('2026-09-01');
        $today = Carbon::parse('2026-09-05'); // jour même de l'échéance

        $this->assertSame('late', PaymentMonthStatus::for(100000, 0, $month, 5, $today));
    }

    public function test_unpaid_four_days_after_due_is_still_late(): void
    {
        $month = Carbon::parse('2026-09-01');
        $today = Carbon::parse('2026-09-09'); // due(5) + 4 jours

        $this->assertSame('late', PaymentMonthStatus::for(100000, 0, $month, 5, $today));
    }

    public function test_unpaid_five_days_after_due_becomes_unpaid(): void
    {
        $month = Carbon::parse('2026-09-01');
        $today = Carbon::parse('2026-09-10'); // due(5) + 5 jours

        $this->assertSame('unpaid', PaymentMonthStatus::for(100000, 0, $month, 5, $today));
    }

    public function test_a_due_day_beyond_the_months_length_clamps_to_the_last_day(): void
    {
        // due_day = 31 sur un mois de 30 jours (septembre) : l'échéance est le 30.
        $month = Carbon::parse('2026-09-01');
        $today = Carbon::parse('2026-09-30');

        $this->assertSame('late', PaymentMonthStatus::for(100000, 0, $month, 31, $today));
    }
}
