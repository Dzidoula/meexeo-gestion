<?php
// tests/Feature/PropertyModelTest.php
namespace Tests\Feature;

use App\Enums\PropertyStatus;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_reference_is_derived_from_the_identifier(): void
    {
        $property = Property::factory()->create();

        $this->assertSame('BIEN-'.str_pad((string) $property->id, 4, '0', STR_PAD_LEFT), $property->reference);
    }

    public function test_the_full_address_joins_the_location_fields_it_has(): void
    {
        $property = Property::factory()->create([
            'district' => 'Riviera 3',
            'commune' => 'Cocody',
            'city' => 'Abidjan',
            'lot_number' => '142',
            'block_number' => '7',
        ]);

        $this->assertSame('Riviera 3, Cocody, Abidjan — Lot 142, Îlot 7', $property->full_address);
    }

    public function test_the_full_address_skips_missing_pieces(): void
    {
        $property = Property::factory()->create([
            'district' => null,
            'commune' => 'Yopougon',
            'city' => 'Abidjan',
            'lot_number' => null,
            'block_number' => null,
        ]);

        $this->assertSame('Yopougon, Abidjan', $property->full_address);
    }

    public function test_rent_and_deposit_are_stored_as_whole_francs(): void
    {
        $property = Property::factory()->create(['monthly_rent' => 450000, 'deposit' => 900000]);

        $this->assertSame(450000, $property->fresh()->monthly_rent);
        $this->assertIsInt($property->fresh()->monthly_rent);
    }

    public function test_the_status_is_cast_to_its_enum(): void
    {
        $property = Property::factory()->vacant()->create();

        $this->assertSame(PropertyStatus::Vacant, $property->fresh()->status);
    }
}
