<?php
namespace App\Enums;

/** Les valeurs sont les clés attendues par App\Support\StatusPresenter. */
enum HotelRoomStatus: string
{
    case Available = 'disponible_chambre';
    case Occupied = 'occupee';
    case Cleaning = 'nettoyage';
    case Maintenance = 'maintenance_chambre';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Disponible',
            self::Occupied => 'Occupée',
            self::Cleaning => 'Nettoyage',
            self::Maintenance => 'Maintenance',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
