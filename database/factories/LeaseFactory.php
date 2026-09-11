<?php
// database/factories/LeaseFactory.php
namespace Database\Factories;

use App\Enums\LeaseStatus;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaseFactory extends Factory
{
    public function definition(): array
    {
        // Les deux parents sont créés explicitement : jamais d'ID codé en dur.
        return [
            'property_id' => Property::factory(),
            'tenant_id' => Tenant::factory(),
            'start_date' => now()->subMonths(6)->toDateString(),
            'expected_end_date' => now()->addMonths(6)->toDateString(),
            'monthly_rent' => $this->faker->numberBetween(15, 200) * 5000,
            'deposit_paid' => $this->faker->numberBetween(15, 400) * 5000,
            'due_day' => 5,
            'status' => LeaseStatus::Active,
        ];
    }

    public function ended(): static
    {
        return $this->state(fn () => [
            'status' => LeaseStatus::Ended,
            'actual_end_date' => now()->subMonth()->toDateString(),
        ]);
    }
}
