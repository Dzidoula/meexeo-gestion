<?php
namespace Database\Factories;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('+1 week', '+3 months')->format('Y-m-d');

        return [
            'client_name' => $this->faker->name(),
            'client_phone' => '07'.$this->faker->numerify('########'),
            'start_date' => $start,
            'end_date' => $start,
            'venue' => $this->faker->randomElement(['Salle Prestige - Abidjan', 'Villa Deluxe', 'Résidence Les Palmiers']),
            'budget_total' => $this->faker->numberBetween(200000, 5000000),
            'deposit_amount' => 0,
            'status' => EventStatus::Pending,
            'notes' => null,
        ];
    }
}
