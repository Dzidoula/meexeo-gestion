<?php

namespace App\Enums;

enum FuelType: string
{
    case Essence = 'essence';
    case Diesel = 'diesel';
    case Hybride = 'hybride';

    public function label(): string
    {
        return match ($this) {
            self::Essence => 'Essence',
            self::Diesel => 'Diesel',
            self::Hybride => 'Hybride',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
