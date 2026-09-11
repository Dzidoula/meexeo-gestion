<?php
// tests/Unit/PropertyEnumsTest.php
namespace Tests\Unit;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use PHPUnit\Framework\TestCase;

class PropertyEnumsTest extends TestCase
{
    public function test_the_eight_property_types_of_the_brief_exist(): void
    {
        $this->assertCount(8, PropertyType::cases());
        $this->assertSame('Maison basse', PropertyType::LowHouse->label());
        $this->assertSame('Appartement 2 chambres + salon', PropertyType::FlatTwoBedrooms->label());
        $this->assertSame('Magasin / Bureau', PropertyType::Shop->label());
    }

    public function test_property_status_values_match_the_status_presenter_keys(): void
    {
        $this->assertSame('occupied', PropertyStatus::Occupied->value);
        $this->assertSame('vacant', PropertyStatus::Vacant->value);
        $this->assertSame('works', PropertyStatus::Works->value);
    }
}
