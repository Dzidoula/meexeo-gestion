<?php
// database/factories/TenantFactory.php
namespace Database\Factories;

use App\Enums\MaritalStatus;
use App\Enums\TenantStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'last_name' => $this->faker->lastName(),
            'first_names' => $this->faker->firstName().' '.$this->faker->firstName(),
            'birth_date' => $this->faker->dateTimeBetween('-60 years', '-20 years'),
            'id_number' => 'CI'.$this->faker->numerify('#######'),
            'marital_status' => MaritalStatus::Single,
            'occupation' => $this->faker->jobTitle(),
            'workplace' => $this->faker->company(),
            'phone1' => '07'.$this->faker->numerify('########'),
            'email' => $this->faker->safeEmail(),
            'status' => TenantStatus::Active,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => TenantStatus::Active]);
    }

    public function former(): static
    {
        return $this->state(fn () => ['status' => TenantStatus::Former]);
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => TenantStatus::Pending]);
    }

    public function blacklisted(): static
    {
        return $this->state(fn () => ['status' => TenantStatus::Blacklisted]);
    }

    public function married(): static
    {
        return $this->state(fn () => [
            'marital_status' => MaritalStatus::Married,
            'spouse_name' => $this->faker->name(),
            'spouse_phone' => '05'.$this->faker->numerify('########'),
        ]);
    }
}
