<?php
// tests/Unit/TenantEnumsTest.php
namespace Tests\Unit;

use App\Enums\MaritalStatus;
use App\Enums\TenantStatus;
use PHPUnit\Framework\TestCase;

class TenantEnumsTest extends TestCase
{
    public function test_tenant_status_values_match_the_status_presenter_keys(): void
    {
        $this->assertSame(
            ['active', 'former', 'pending', 'blacklisted'],
            array_map(fn (TenantStatus $s) => $s->value, TenantStatus::cases())
        );
        $this->assertSame('Blacklisté', TenantStatus::Blacklisted->label());
    }

    public function test_marital_status_has_a_french_label_for_every_case(): void
    {
        foreach (MaritalStatus::cases() as $case) {
            $this->assertNotSame('', $case->label());
        }
        $this->assertSame('Marié(e)', MaritalStatus::Married->label());
    }
}
