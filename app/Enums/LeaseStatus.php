<?php
// app/Enums/LeaseStatus.php
namespace App\Enums;

/** Les valeurs sont les clés attendues par App\Support\StatusPresenter. */
enum LeaseStatus: string
{
    case Active = 'active';
    case Ended = 'ended';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'En cours',
            self::Ended => 'Terminé',
        };
    }
}
