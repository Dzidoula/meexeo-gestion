<?php

namespace Tests\Unit;

use App\Enums\Role;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    public function test_every_role_has_a_french_label(): void
    {
        $this->assertSame('Administrateur', Role::Admin->label());
        $this->assertSame('Gestionnaire', Role::Manager->label());
        $this->assertSame('Comptable', Role::Accountant->label());
        $this->assertSame('Lecture seule', Role::Viewer->label());
    }

    public function test_the_four_roles_of_the_brief_exist_and_no_more(): void
    {
        $this->assertSame(
            ['admin', 'manager', 'accountant', 'viewer'],
            array_map(fn (Role $r) => $r->value, Role::cases())
        );
    }
}
