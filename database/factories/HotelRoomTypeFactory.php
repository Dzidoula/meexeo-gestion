<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class HotelRoomTypeFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement(['Standard', 'Suite', 'Deluxe', 'Familiale', 'Prestige']);

        return ['name' => $name, 'slug' => Str::slug($name)];
    }
}
