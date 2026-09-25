<?php
namespace Database\Factories;

use App\Models\Equipment;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventEquipmentReservationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'equipment_id' => Equipment::factory(),
            'quantity' => $this->faker->numberBetween(1, 10),
        ];
    }
}
