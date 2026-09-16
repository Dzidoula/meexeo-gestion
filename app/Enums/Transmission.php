<?php

namespace App\Enums;

enum Transmission: string
{
    case Manuelle = 'manuelle';
    case Automatique = 'automatique';

    public function label(): string
    {
        return match ($this) {
            self::Manuelle => 'Manuelle',
            self::Automatique => 'Automatique',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
