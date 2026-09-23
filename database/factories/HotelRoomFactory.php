<?php
namespace Database\Factories;

use App\Enums\HotelRoomStatus;
use App\Models\HotelRoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

class HotelRoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hotel_room_type_id' => HotelRoomType::factory(),
            'number' => (string) $this->faker->unique()->numberBetween(100, 599),
            'nightly_rate' => $this->faker->numberBetween(20, 150) * 1000,
            'amenities' => 'Climatisation, Wifi, Télévision',
            'status' => HotelRoomStatus::Available,
        ];
    }
}
