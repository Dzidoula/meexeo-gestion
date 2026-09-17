<?php
namespace Tests\Feature;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicHomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_is_accessible_without_authentication(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_it_shows_the_real_vehicle_count(): void
    {
        Vehicle::factory()->count(3)->create();

        $this->get('/')->assertOk()->assertSee('3');
    }

    public function test_it_shows_up_to_four_featured_vehicles(): void
    {
        Vehicle::factory()->create(['brand' => 'Toyota', 'model' => 'Fortuner']);
        Vehicle::factory()->create(['brand' => 'Hyundai', 'model' => 'H1']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Fortuner')
            ->assertSee('H1');
    }

    public function test_it_shows_no_featured_vehicles_when_the_catalog_is_empty(): void
    {
        $this->get('/')->assertOk()->assertSee('Nos véhicules');
    }

    public function test_the_vehicle_sales_block_links_to_the_vehicle_list(): void
    {
        $this->get('/')->assertSee(route('public.vehicles.index'), false);
    }

    public function test_the_three_other_activities_are_marked_coming_soon_and_link_to_their_pages(): void
    {
        $response = $this->get('/');

        $response->assertSee('Bientôt disponible');
        $response->assertSee(route('public.taxis'), false);
        $response->assertSee(route('public.sonorisation'), false);
        $response->assertSee(route('public.podiums'), false);
    }
}
