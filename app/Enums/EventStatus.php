<?php
namespace App\Enums;

/** Les valeurs sont les clés attendues par App\Support\StatusPresenter. */
enum EventStatus: string
{
    case Pending = 'en_attente';
    case Confirmed = 'confirme';
    case Completed = 'termine';
    case Cancelled = 'annule';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Confirmed => 'Confirmé',
            self::Completed => 'Terminé',
            self::Cancelled => 'Annulé',
        };
    }
}
