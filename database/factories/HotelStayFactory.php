<?php
namespace Database\Factories;

use App\Enums\HotelStayStatus;
use App\Models\HotelRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

class HotelStayFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hotel_room_id' => HotelRoom::factory(),
            'guest_name' => $this->faker->name(),
            'guest_phone' => $this->faker->numerify('01#######'),
            'arrival_date' => now()->addDays(1)->toDateString(),
            'departure_date' => now()->addDays(3)->toDateString(),
            'total_amount' => $this->faker->numberBetween(20, 150) * 1000,
            'deposit_amount' => 0,
            'status' => HotelStayStatus::Reserved,
        ];
    }
}
