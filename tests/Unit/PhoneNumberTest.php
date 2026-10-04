<?php

namespace Tests\Unit;

use App\Support\PhoneNumber;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PhoneNumberTest extends TestCase
{
    #[DataProvider('inputs')]
    public function test_it_normalizes_to_the_canonical_ci_format(string $raw, string $expected): void
    {
        $this->assertSame($expected, PhoneNumber::ivoirianE164($raw));
    }

    public static function inputs(): array
    {
        return [
            'bare local number'               => ['0151414430', '+2250151414430'],
            'local number with spaces'        => ['01 51 41 44 30', '+2250151414430'],
            'already canonical'               => ['+2250151414430', '+2250151414430'],
            'country code without plus'       => ['2250151414430', '+2250151414430'],
            'with dashes'                     => ['01-51-41-44-30', '+2250151414430'],
            'with a leading 00 international' => ['002250151414430', '+2250151414430'],
        ];
    }

    public function test_an_empty_value_stays_empty(): void
    {
        $this->assertSame('', PhoneNumber::ivoirianE164(''));
    }
}
