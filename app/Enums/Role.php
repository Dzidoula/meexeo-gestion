<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case Accountant = 'accountant';
    case Viewer = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrateur',
            self::Manager => 'Gestionnaire',
            self::Accountant => 'Comptable',
            self::Viewer => 'Lecture seule',
        };
    }
}
