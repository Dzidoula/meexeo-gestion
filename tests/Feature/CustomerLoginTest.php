<?php
namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_login_form_is_accessible(): void
    {
        $this->get('/connexion-client')->assertOk();
    }

    public function test_a_customer_can_log_in_with_correct_credentials(): void
    {
        Customer::factory()->create(['email' => 'awa@example.com', 'password' => Hash::make('password123')]);

        $response = $this->post('/connexion-client', [
            'email' => 'awa@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('customer.account'));
        $this->assertAuthenticated('customer');
    }

    public function test_login_fails_with_the_wrong_password(): void
    {
        Customer::factory()->create(['email' => 'awa@example.com', 'password' => Hash::make('password123')]);

        $response = $this->post('/connexion-client', [
            'email' => 'awa@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('customer');
    }

    public function test_a_logged_in_customer_can_log_out(): void
    {
        $customer = Customer::factory()->create();
        $this->actingAs($customer, 'customer');

        $this->post('/deconnexion-client')->assertRedirect(route('public.home'));
        $this->assertGuest('customer');
    }

    public function test_the_account_page_requires_authentication(): void
    {
        $this->get('/mon-compte')->assertRedirect('/connexion-client');
    }

    public function test_a_logged_in_customer_can_view_the_account_page(): void
    {
        $customer = Customer::factory()->create(['name' => 'Awa Koné']);
        $this->actingAs($customer, 'customer');

        $this->get('/mon-compte')->assertOk()->assertSee('Awa Koné');
    }

    public function test_the_account_page_shows_the_customers_cart(): void
    {
        $customer = Customer::factory()->create();
        $vehicle = \App\Models\Vehicle::factory()->create(['model' => 'ModeleAuCompte', 'stock_quantity' => 3]);
        \App\Models\CartItem::create(['customer_id' => $customer->id, 'vehicle_id' => $vehicle->id]);

        $this->actingAs($customer, 'customer');

        $this->get('/mon-compte')->assertOk()->assertSee('ModeleAuCompte');
    }

    public function test_admin_guests_are_still_redirected_to_the_admin_login(): void
    {
        // Régression : le changement de redirectGuestsTo (Task 1) ne doit pas
        // casser la redirection des routes admin existantes vers /connexion.
        $this->get('/tableau-de-bord')->assertRedirect('/connexion');
    }
}
