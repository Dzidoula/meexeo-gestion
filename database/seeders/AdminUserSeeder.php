<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /** Idempotent : relancer le seeder ne crée pas de doublon et ne réécrit pas le mot de passe. */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@meexeo.ci')],
            [
                'name' => env('ADMIN_NAME', 'Administrateur MEEXEO'),
                'password' => env('ADMIN_PASSWORD') ?: str()->random(32),
                'role' => Role::Admin,
            ]
        );
    }
}
