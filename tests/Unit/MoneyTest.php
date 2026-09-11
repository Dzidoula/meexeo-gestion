<?php
// tests/Unit/MoneyTest.php
namespace Tests\Unit;

use App\Support\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_it_formats_whole_francs_with_thin_group_separators(): void
    {
        $this->assertSame('450 000 FCFA', Money::fcfa(450000));
        $this->assertSame('0 FCFA', Money::fcfa(0));
        $this->assertSame('1 250 000 FCFA', Money::fcfa(1250000));
    }
}
