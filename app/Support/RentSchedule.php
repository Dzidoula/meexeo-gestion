<?php

namespace App\Support;

use App\Models\Lease;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Échéancier d'un bail : une ligne par mois depuis le début jusqu'au mois courant,
 * avec le statut réel. Source unique pour le tableau de bord, les loyers et les
 * paiements — ces trois écrans divergeaient quand chacun le recalculait.
 */
class RentSchedule
{
    /** @return Collection<int, array<string, mixed>> Plus récent en premier. */
    public static function forLease(Lease $lease): Collection
    {
        $lease->loadMissing('payments');

        $byMonth = $lease->payments->groupBy(fn ($p) => $p->month->format('Y-m'));

        $cursor = Carbon::parse($lease->start_date)->startOfMonth();
        $last   = now()->startOfMonth();
        $rows   = [];

        while ($cursor->lte($last)) {
            $key      = $cursor->format('Y-m');
            $payments = $byMonth->get($key, new Collection);

            // Une preuve envoyée par le locataire n'est pas un paiement tant que
            // le gestionnaire ne l'a pas validée : elle ne compte pas dans le réglé.
            $verified = $payments->whereNull('portal_status');
            $pending  = $payments->where('portal_status', 'pending');
            $paid     = (int) $verified->sum('amount');

            $due = RentDueDate::forMonth($cursor, $lease->due_day);

            $rows[] = [
                'key'      => $key,
                'month'    => $cursor->copy(),
                'label'    => ucfirst($cursor->isoFormat('MMMM YYYY')),
                'due_on'   => $due,
                'amount'   => (int) $lease->monthly_rent,
                'paid'     => $paid,
                'rest'     => max(0, (int) $lease->monthly_rent - $paid),
                'payments' => $verified->values(),
                'paid_on'  => $verified->max('paid_on'),
                'method'   => $verified->last()?->method,
                'status'   => self::status($lease, $paid, $pending->isNotEmpty(), $due),
            ];

            $cursor->addMonth();
        }

        return collect(array_reverse($rows));
    }

    private static function status(Lease $lease, int $paid, bool $hasPending, Carbon $due): string
    {
        if ($paid >= $lease->monthly_rent) {
            return 'paid';
        }

        if ($paid > 0) {
            return 'partial';
        }

        if ($hasPending) {
            return 'pending_proof';
        }

        return now()->gt($due) ? 'overdue' : 'pending';
    }

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            'paid'          => 'Payé',
            'partial'       => 'Partiel',
            'pending_proof' => 'En vérification',
            'pending'       => 'En attente',
            'overdue'       => 'En retard',
        ];
    }

    /** Classes Tailwind du badge, reprises de la maquette. */
    public static function tone(string $status): string
    {
        return match ($status) {
            'paid'          => 'bg-emerald-50 text-emerald-700',
            'partial'       => 'bg-amber-50 text-amber-700',
            'pending_proof' => 'bg-blue-50 text-blue-700',
            'overdue'       => 'bg-red-50 text-red-700',
            default         => 'bg-amber-50 text-amber-700',
        };
    }
}
