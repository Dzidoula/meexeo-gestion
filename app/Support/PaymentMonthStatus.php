<?php
namespace App\Support;

use Carbon\Carbon;

class PaymentMonthStatus
{
    /** Nombre de jours après l'échéance à partir duquel un impayé devient "impayé" plutôt que "en retard". */
    private const LATE_THRESHOLD_DAYS = 5;

    /**
     * Statut d'un mois de loyer pour un bail — calculé, jamais stocké.
     * Retourne null si le mois n'est pas encore dû (mois futur, ou mois
     * courant avant le jour d'échéance).
     */
    public static function for(int $monthlyRent, int $paidThisMonth, Carbon $month, int $dueDay, Carbon $today): ?string
    {
        if ($paidThisMonth > 0 && $paidThisMonth < $monthlyRent) {
            return 'partial';
        }

        if ($paidThisMonth >= $monthlyRent) {
            return 'paid';
        }

        $dueDate = $month->copy()->startOfMonth()->day(min($dueDay, $month->daysInMonth));

        if ($today->lt($dueDate)) {
            return null;
        }

        return $dueDate->diffInDays($today) >= self::LATE_THRESHOLD_DAYS ? 'unpaid' : 'late';
    }
}
