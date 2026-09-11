<?php
// app/Enums/MaritalStatus.php
namespace App\Enums;

enum MaritalStatus: string
{
    case Single = 'single';
    case Married = 'married';
    case Cohabiting = 'cohabiting';
    case Divorced = 'divorced';
    case Widowed = 'widowed';

    public function label(): string
    {
        return match ($this) {
            self::Single => 'Célibataire',
            self::Married => 'Marié(e)',
            self::Cohabiting => 'En couple',
            self::Divorced => 'Divorcé(e)',
            self::Widowed => 'Veuf / Veuve',
        };
    }

    /** Le brief demande le conjoint et son contact dès que le locataire est en couple. */
    public function requiresSpouse(): bool
    {
        return in_array($this, [self::Married, self::Cohabiting], true);
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
