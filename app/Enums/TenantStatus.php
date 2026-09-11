<?php
// app/Enums/TenantStatus.php
namespace App\Enums;

/** Les valeurs sont les clés attendues par App\Support\StatusPresenter. */
enum TenantStatus: string
{
    case Active = 'active';
    case Former = 'former';
    case Pending = 'pending';
    case Blacklisted = 'blacklisted';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Actif',
            self::Former => 'Ancien locataire',
            self::Pending => 'En attente',
            self::Blacklisted => 'Blacklisté',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
