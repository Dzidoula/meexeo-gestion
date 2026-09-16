<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VehicleTypeFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement(['Berline', 'SUV', '4x4', 'Minibus', 'Utilitaire']);

        return ['name' => $name, 'slug' => Str::slug($name)];
    }
}
