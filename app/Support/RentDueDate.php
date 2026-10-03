<?php

namespace App\Support;

use Carbon\Carbon;

class RentDueDate
{
    /** Due date inside a given month, clamped so day 31 does not spill into the next month. */
    public static function forMonth(Carbon $month, int $dueDay): Carbon
    {
        $m = $month->copy()->startOfMonth();

        return $m->setDay(min($dueDay, $m->daysInMonth));
    }

    /** The next due date from today — this month if it has not passed, else next month. */
    public static function next(int $dueDay): Carbon
    {
        $thisMonth = self::forMonth(now(), $dueDay);

        return now()->lte($thisMonth)
            ? $thisMonth
            : self::forMonth(now()->startOfMonth()->addMonth(), $dueDay);
    }
}
