<?php
// app/Enums/PropertyType.php
namespace App\Enums;

enum PropertyType: string
{
    case LowHouse = 'low_house';
    case Villa = 'villa';
    case FlatOneBedroom = 'flat_1';
    case FlatTwoBedrooms = 'flat_2';
    case FlatThreeBedrooms = 'flat_3';
    case Studio = 'studio';
    case Compound = 'compound';
    case Shop = 'shop';

    public function label(): string
    {
        return match ($this) {
            self::LowHouse => 'Maison basse',
            self::Villa => 'Villa',
            self::FlatOneBedroom => 'Appartement chambre + salon',
            self::FlatTwoBedrooms => 'Appartement 2 chambres + salon',
            self::FlatThreeBedrooms => 'Appartement 3 chambres + salon',
            self::Studio => 'Studio',
            self::Compound => 'Mini-cité / Immeuble',
            self::Shop => 'Magasin / Bureau',
        };
    }

    /** @return array<string, string> valeur => libellé, pour les listes déroulantes */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
