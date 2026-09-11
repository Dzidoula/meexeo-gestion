<?php
// database/factories/PropertyDocumentFactory.php
namespace Database\Factories;

use App\Enums\DocumentType;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyDocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'type' => DocumentType::LandTitle,
            'path' => 'properties/documents/'.$this->faker->uuid().'.pdf',
            'original_name' => 'titre-foncier.pdf',
            'verified' => false,
        ];
    }
}
