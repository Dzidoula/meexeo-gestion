<?php
// database/factories/PropertyFactory.php
namespace Database\Factories;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    public function definition(): array
    {
        $communes = ['Cocody', 'Yopougon', 'Marcory', 'Treichville', 'Plateau', 'Abobo'];

        return [
            'title' => 'Villa '.$this->faker->numberBetween(1, 99),
            'type' => $this->faker->randomElement(PropertyType::cases()),
            'status' => PropertyStatus::Vacant,
            'city' => 'Abidjan',
            'commune' => $this->faker->randomElement($communes),
            'district' => 'Riviera '.$this->faker->numberBetween(1, 4),
            'lot_number' => (string) $this->faker->numberBetween(1, 500),
            'block_number' => (string) $this->faker->numberBetween(1, 40),
            'rooms' => $this->faker->numberBetween(1, 6),
            'area_sqm' => $this->faker->numberBetween(25, 400),
            // Multiples de 5 000 F : des loyers réalistes en Côte d'Ivoire
            'monthly_rent' => $this->faker->numberBetween(15, 200) * 5000,
            'deposit' => $this->faker->numberBetween(15, 400) * 5000,
        ];
    }

    public function occupied(): static
    {
        return $this->state(fn () => ['status' => PropertyStatus::Occupied]);
    }

    public function vacant(): static
    {
        return $this->state(fn () => ['status' => PropertyStatus::Vacant]);
    }

    public function works(): static
    {
        return $this->state(fn () => ['status' => PropertyStatus::Works]);
    }
}
