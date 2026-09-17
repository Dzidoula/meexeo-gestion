<?php
namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_can_add_a_vehicle_to_the_session_cart(): void
    {
        $vehicle = Vehicle::factory()->create(['model' => 'ModeleDisponible', 'stock_quantity' => 3]);

        $this->post(route('cart.add', $vehicle))->assertRedirect();

        $this->get(route('cart.show'))->assertOk()->assertSee('ModeleDisponible');
    }

    public function test_a_guest_cannot_add_an_out_of_stock_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create(['model' => 'ModeleEpuise', 'stock_quantity' => 0]);

        $this->post(route('cart.add', $vehicle))->assertRedirect();

        $this->get(route('cart.show'))->assertOk()->assertDontSee('ModeleEpuise');
    }

    public function test_adding_the_same_vehicle_twice_does_not_duplicate_it(): void
    {
        $vehicle = Vehicle::factory()->create(['stock_quantity' => 3]);

        $this->post(route('cart.add', $vehicle));
        $this->post(route('cart.add', $vehicle));

        $this->assertSame(1, \App\Support\Cart::count());
    }

    public function test_a_guest_can_remove_a_vehicle_from_the_session_cart(): void
    {
        $vehicle = Vehicle::factory()->create(['stock_quantity' => 3]);
        $this->post(route('cart.add', $vehicle));

        $this->delete(route('cart.remove', $vehicle));

        $this->assertSame(0, \App\Support\Cart::count());
    }

    public function test_a_logged_in_customer_adds_to_a_database_backed_cart(): void
    {
        $customer = Customer::factory()->create();
        $vehicle = Vehicle::factory()->create(['stock_quantity' => 3]);

        $this->actingAs($customer, 'customer')->post(route('cart.add', $vehicle));

        $this->assertDatabaseHas('cart_items', ['customer_id' => $customer->id, 'vehicle_id' => $vehicle->id]);
    }

    public function test_the_vehicle_detail_page_shows_an_active_add_to_cart_button_when_in_stock(): void
    {
        $vehicle = Vehicle::factory()->create(['stock_quantity' => 3]);

        $this->get(route('public.vehicles.show', $vehicle))
            ->assertOk()
            ->assertSee('Ajouter au panier')
            ->assertDontSee('disabled', false);
    }

    public function test_the_vehicle_detail_page_shows_a_disabled_epuise_button_when_out_of_stock(): void
    {
        $vehicle = Vehicle::factory()->create(['stock_quantity' => 0]);

        $response = $this->get(route('public.vehicles.show', $vehicle))->assertOk();
        $response->assertSee('Épuisé');
        $response->assertSee('disabled', false);
    }

    public function test_the_nav_shows_the_real_cart_count(): void
    {
        $vehicle = Vehicle::factory()->create(['stock_quantity' => 3]);
        $this->post(route('cart.add', $vehicle));

        $this->get('/')->assertOk()->assertSee('Panier (1)');
    }
}
