<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EquipmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Chaises pliantes', 'Tables rondes', 'Système de sonorisation',
                'Podium modulaire', 'Tente de réception', 'Éclairage scénique',
            ]),
            'quantity_total' => $this->faker->numberBetween(10, 200),
        ];
    }
}
