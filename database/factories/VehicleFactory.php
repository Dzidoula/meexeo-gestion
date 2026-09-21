<?php

namespace Database\Factories;

use App\Enums\FuelType;
use App\Enums\Transmission;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'vehicle_type_id' => fn () => VehicleType::inRandomOrder()->value('id') ?? VehicleType::factory()->create()->id,
            'brand' => $this->faker->randomElement(['Toyota', 'Hyundai', 'Mercedes', 'Kia']),
            'model' => $this->faker->word(),
            'fuel_type' => $this->faker->randomElement(FuelType::cases()),
            'transmission' => $this->faker->randomElement(Transmission::cases()),
            'seats' => $this->faker->numberBetween(2, 15),
            'price' => $this->faker->numberBetween(200, 8000) * 10000,
            'stock_quantity' => $this->faker->numberBetween(0, 10),
        ];
    }
}
