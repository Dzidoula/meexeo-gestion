<?php
namespace App\Enums;

/** Les valeurs sont les clés attendues par App\Support\StatusPresenter. */
enum HotelStayStatus: string
{
    case Reserved = 'reserve';
    case InProgress = 'en_cours';
    case Completed = 'termine';
    case Cancelled = 'annule';

    public function label(): string
    {
        return match ($this) {
            self::Reserved => 'Réservé',
            self::InProgress => 'En cours',
            self::Completed => 'Terminé',
            self::Cancelled => 'Annulé',
        };
    }
}
