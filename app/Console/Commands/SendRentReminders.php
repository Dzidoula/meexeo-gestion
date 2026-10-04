<?php

namespace App\Console\Commands;

use App\Enums\LeaseStatus;
use App\Models\Lease;
use App\Models\Tenant;
use App\Notifications\PortalNotice;
use App\Support\RentDueDate;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Rappels de loyer du portail locataire : cinq jours avant l'échéance, puis le
 * jour même (cahier des charges, §11). Notifications en base uniquement.
 */
class SendRentReminders extends Command
{
    protected $signature = 'portal:send-rent-reminders';

    protected $description = 'Notifie les locataires dont le loyer arrive à échéance dans 5 jours ou aujourd\'hui';

    /** Jours avant l'échéance qui déclenchent un rappel. */
    private const REMINDER_DAYS = [5, 0];

    public function handle(): int
    {
        $today = now()->startOfDay();
        $sent  = 0;

        Lease::where('status', LeaseStatus::Active->value)
            ->with(['tenant', 'payments'])
            ->each(function (Lease $lease) use ($today, &$sent) {
                // Le mois courant et le suivant : à la fin du mois, l'échéance à
                // venir tombe dans le mois d'après.
                foreach ([$today->copy()->startOfMonth(), $today->copy()->addMonth()->startOfMonth()] as $month) {
                    $due = RentDueDate::forMonth($month, $lease->due_day)->startOfDay();

                    if ($due->lt($lease->start_date->copy()->startOfDay())) {
                        continue;
                    }

                    $daysAhead = (int) $today->diffInDays($due, false);

                    if (! in_array($daysAhead, self::REMINDER_DAYS, true)) {
                        continue;
                    }

                    if ($this->needsNoReminder($lease, $month)) {
                        continue;
                    }

                    $notice = PortalNotice::rentDue($due, $daysAhead);

                    if ($this->alreadySent($lease->tenant, $notice->key)) {
                        continue;
                    }

                    $lease->tenant->notify($notice);
                    $sent++;
                }
            });

        $this->info("{$sent} rappel(s) envoyé(s).");

        return self::SUCCESS;
    }

    /** Mois déjà réglé, ou preuve en cours de vérification : relancer serait du bruit. */
    private function needsNoReminder(Lease $lease, Carbon $month): bool
    {
        $forMonth = $lease->payments->filter(fn ($p) => $p->month->isSameMonth($month));

        $paid = (int) $forMonth->whereNull('portal_status')->sum('amount');

        return $paid >= $lease->monthly_rent
            || $forMonth->where('portal_status', 'pending')->isNotEmpty();
    }

    private function alreadySent(Tenant $tenant, ?string $key): bool
    {
        return $tenant->notifications()
            ->where('created_at', '>=', now()->subDays(45))
            ->get()
            ->contains(fn ($n) => ($n->data['key'] ?? null) === $key);
    }
}
