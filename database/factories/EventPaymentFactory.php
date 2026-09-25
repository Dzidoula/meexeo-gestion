<?php
namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventPaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'amount' => $this->faker->numberBetween(50000, 1000000),
            'paid_on' => now()->toDateString(),
            'method' => $this->faker->randomElement(['Espèces', 'Mobile Money', 'Virement', null]),
        ];
    }
}
