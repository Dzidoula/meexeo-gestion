<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => ucfirst($this->faker->words(2, true)),
            'description' => $this->faker->sentence(),
            // Multiples de 500 F : des prix réalistes en Côte d'Ivoire.
            'price' => $this->faker->numberBetween(10, 400) * 500,
            'stock_quantity' => $this->faker->numberBetween(0, 100),
        ];
    }
}
