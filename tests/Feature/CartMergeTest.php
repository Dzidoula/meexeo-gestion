<?php
namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CartMergeTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_session_cart_is_merged_into_the_account_on_login(): void
    {
        $customer = Customer::factory()->create(['email' => 'awa@example.com', 'password' => Hash::make('password123')]);
        $vehicle = Vehicle::factory()->create(['stock_quantity' => 3]);

        $this->post(route('cart.add', $vehicle));

        $this->post('/connexion-client', ['email' => 'awa@example.com', 'password' => 'password123']);

        $this->assertDatabaseHas('cart_items', ['customer_id' => $customer->id, 'vehicle_id' => $vehicle->id]);
        $this->assertSame([], session('cart', []));
    }

    public function test_the_session_cart_is_merged_into_the_account_on_registration(): void
    {
        $vehicle = Vehicle::factory()->create(['stock_quantity' => 3]);
        $this->post(route('cart.add', $vehicle));

        $this->post('/inscription', [
            'name' => 'Awa Koné',
            'phone' => '0102030405',
            'email' => 'awa@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $customer = Customer::where('email', 'awa@example.com')->firstOrFail();
        $this->assertDatabaseHas('cart_items', ['customer_id' => $customer->id, 'vehicle_id' => $vehicle->id]);
    }

    public function test_merging_does_not_duplicate_a_vehicle_already_in_the_account_cart(): void
    {
        $customer = Customer::factory()->create(['email' => 'awa@example.com', 'password' => Hash::make('password123')]);
        $vehicle = Vehicle::factory()->create(['stock_quantity' => 3]);

        $this->actingAs($customer, 'customer')->post(route('cart.add', $vehicle));
        $this->post('/deconnexion-client');

        $this->post(route('cart.add', $vehicle));
        $this->post('/connexion-client', ['email' => 'awa@example.com', 'password' => 'password123']);

        $this->assertSame(1, \App\Models\CartItem::where('customer_id', $customer->id)->count());
    }
}
