<?php
namespace Database\Factories;

use App\Models\HotelRoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

class HotelRoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hotel_room_type_id' => HotelRoomType::factory(),
        ];
    }
}
