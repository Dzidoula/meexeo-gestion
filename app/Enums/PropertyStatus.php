<?php
// app/Enums/PropertyStatus.php
namespace App\Enums;

/** Les valeurs sont les clés attendues par App\Support\StatusPresenter. */
enum PropertyStatus: string
{
    case Occupied = 'occupied';
    case Vacant = 'vacant';
    case Works = 'works';

    public function label(): string
    {
        return match ($this) {
            self::Occupied => 'Occupé',
            self::Vacant => 'Libre',
            self::Works => 'En travaux',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
