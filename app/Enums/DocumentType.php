<?php
// app/Enums/DocumentType.php
namespace App\Enums;

enum DocumentType: string
{
    case LandTitle = 'land_title';
    case Acd = 'acd';
    case Plan = 'plan';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::LandTitle => 'Titre foncier',
            self::Acd => 'ACD',
            self::Plan => 'Plan',
            self::Other => 'Autre document',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
