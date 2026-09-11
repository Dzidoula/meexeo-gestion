<?php
// app/Support/StatusPresenter.php
namespace App\Support;

use InvalidArgumentException;

class StatusPresenter
{
    /**
     * Libellé français et classes Tailwind de chaque statut.
     * Un statut n'est jamais porté par la couleur seule : le libellé est toujours affiché.
     */
    private const MAP = [
        // Biens
        'occupied'    => ['Occupé', 'ok'],
        'vacant'      => ['Libre', 'neutre'],
        'works'       => ['En travaux', 'part'],
        // Locataires
        'active'      => ['Actif', 'ok'],
        'former'      => ['Ancien locataire', 'neutre'],
        'pending'     => ['En attente', 'part'],
        'blacklisted' => ['Blacklisté', 'impaye'],
        // Baux
        'ended'       => ['Terminé', 'neutre'],
    ];

    /** @return array{label: string, wrapper: string, dot: string} */
    public static function for(string $status): array
    {
        if (! isset(self::MAP[$status])) {
            throw new InvalidArgumentException("Statut inconnu : {$status}");
        }

        [$label, $tone] = self::MAP[$status];

        return [
            'label' => $label,
            'wrapper' => "bg-{$tone}-bg border-{$tone}-bord text-{$tone}-texte",
            'dot' => "bg-{$tone}-puce",
        ];
    }
}
