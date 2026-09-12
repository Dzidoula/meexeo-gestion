<?php
namespace Tests\Unit;

use App\Enums\PaymentMethod;
use PHPUnit\Framework\TestCase;

class PaymentMethodTest extends TestCase
{
    public function test_the_seven_methods_of_the_brief_exist(): void
    {
        $this->assertCount(7, PaymentMethod::cases());
        $this->assertSame('Espèces', PaymentMethod::Cash->label());
        $this->assertSame('Wave', PaymentMethod::Wave->label());
    }

    public function test_cash_has_no_reference_label(): void
    {
        $this->assertNull(PaymentMethod::Cash->referenceLabel());
    }

    public function test_cheque_and_mobile_money_have_distinct_reference_labels(): void
    {
        $this->assertSame('Numéro de chèque', PaymentMethod::Cheque->referenceLabel());
        $this->assertSame('Référence de la transaction', PaymentMethod::Wave->referenceLabel());
        $this->assertSame('Référence de la transaction', PaymentMethod::OrangeMoney->referenceLabel());
    }
}
