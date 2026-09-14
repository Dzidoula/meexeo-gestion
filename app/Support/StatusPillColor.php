<?php
namespace App\Support;

class StatusPillColor
{
    /** Reprend exactement statusColorMap du mockup — aucune couleur inventée. */
    private const MAP = [
        'Confirmée' => '#16A34A', 'Confirmé' => '#16A34A', 'Payé' => '#16A34A',
        'Disponible' => '#16A34A', 'Actif' => '#16A34A', 'OK' => '#16A34A',
        'Livrée' => '#16A34A', 'Régulier' => '#16A34A',
        'En attente' => '#D97706', 'En préparation' => '#D97706', 'Loué' => '#D97706', 'Congé' => '#D97706',
        'Nouvelle' => '#2563EB', 'Nouveau' => '#2563EB', 'Expédiée' => '#2563EB',
        'Impayé' => '#DC2626', 'En maintenance' => '#DC2626', 'Stock faible' => '#DC2626',
        'Annulée' => '#DC2626', 'En retard' => '#DC2626',
        'Terminée' => '#64748B',
    ];

    public static function hex(string $status): string
    {
        return self::MAP[$status] ?? '#64748B';
    }
}
