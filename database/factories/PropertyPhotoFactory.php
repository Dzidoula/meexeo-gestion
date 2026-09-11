<?php
// database/factories/PropertyPhotoFactory.php
namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyPhotoFactory extends Factory
{
    public function definition(): array
    {
        // La ligne parente est créée explicitement : jamais d'ID codé en dur.
        return [
            'property_id' => Property::factory(),
            'path' => 'properties/photos/'.$this->faker->uuid().'.jpg',
            'is_primary' => false,
            'position' => 0,
        ];
    }
}
