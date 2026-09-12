<?php
namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Lease;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        // Le bail parent est créé explicitement : jamais d'ID codé en dur.
        return [
            'lease_id' => Lease::factory(),
            'month' => now()->startOfMonth()->toDateString(),
            'paid_on' => now()->toDateString(),
            'amount' => $this->faker->numberBetween(15, 200) * 5000,
            'method' => PaymentMethod::Cash,
            'reference' => null,
            'proof_path' => 'payments/proofs/'.$this->faker->uuid().'.jpg',
            'notes' => null,
        ];
    }

    public function forMonth(\Carbon\Carbon $month): static
    {
        return $this->state(fn () => ['month' => $month->copy()->startOfMonth()->toDateString()]);
    }

    public function method(PaymentMethod $method): static
    {
        return $this->state(fn () => ['method' => $method]);
    }
}
