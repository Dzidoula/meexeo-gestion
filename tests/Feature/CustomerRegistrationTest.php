<?php
namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_registration_form_is_accessible(): void
    {
        $this->get('/inscription')->assertOk();
    }

    public function test_a_visitor_can_register_and_is_logged_in_automatically(): void
    {
        $response = $this->post('/inscription', [
            'name' => 'Awa Koné',
            'phone' => '0102030405',
            'email' => 'awa@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('customer.account'));
        $this->assertAuthenticated('customer');
        $this->assertDatabaseHas('customers', ['email' => 'awa@example.com', 'name' => 'Awa Koné']);
    }

    public function test_registration_requires_a_unique_email(): void
    {
        Customer::factory()->create(['email' => 'awa@example.com']);

        $response = $this->post('/inscription', [
            'name' => 'Awa Koné',
            'phone' => '0102030405',
            'email' => 'awa@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('customer');
    }

    public function test_registration_requires_matching_password_confirmation(): void
    {
        $response = $this->post('/inscription', [
            'name' => 'Awa Koné',
            'phone' => '0102030405',
            'email' => 'awa@example.com',
            'password' => 'password123',
            'password_confirmation' => 'somethingelse',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest('customer');
    }
}
