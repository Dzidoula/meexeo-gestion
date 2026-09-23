<?php
namespace Database\Factories;

use App\Models\HotelStay;
use Illuminate\Database\Eloquent\Factories\Factory;

class HotelPaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hotel_stay_id' => HotelStay::factory(),
            'amount' => $this->faker->numberBetween(10, 100) * 1000,
            'paid_on' => now()->toDateString(),
            'method' => $this->faker->randomElement(['Espèces', 'Mobile Money', 'Virement']),
        ];
    }
}
