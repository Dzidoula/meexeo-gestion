<?php
// database/factories/TenantDocumentFactory.php
namespace Database\Factories;

use App\Enums\TenantDocumentType;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantDocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'type' => TenantDocumentType::IdCard,
            'path' => 'tenants/documents/'.$this->faker->uuid().'.jpg',
            'original_name' => 'cni.jpg',
            'verified' => false,
        ];
    }
}
