<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleIndexTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsManager(): self
    {
        return $this->actingAs(User::factory()->manager()->create());
    }

    public function test_a_guest_cannot_see_the_catalog(): void
    {
        $this->get('/vehicules')->assertRedirect('/connexion');
    }

    public function test_it_shows_the_three_kpis(): void
    {
        Vehicle::factory()->create(['price' => 10000000, 'stock_quantity' => 2]);
        Vehicle::factory()->create(['price' => 20000000, 'stock_quantity' => 0]);

        $html = $this->actingAsManager()->get('/vehicules')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/Véhicules actifs<\/div>\s*<div[^>]*>\s*2\s*<\/div>/u', $html);
        $this->assertMatchesRegularExpression('/Valeur du stock<\/div>\s*<div[^>]*>\s*20 000 000 FCFA\s*<\/div>/u', $html);
        $this->assertMatchesRegularExpression('/Véhicules épuisés<\/div>\s*<div[^>]*>\s*1\s*<\/div>/u', $html);
    }

    public function test_the_kpis_are_zero_on_an_empty_catalog(): void
    {
        $this->actingAsManager()
            ->get('/vehicules')
            ->assertOk()
            ->assertSee('0 FCFA');
    }

    public function test_it_filters_by_vehicle_type(): void
    {
        $suv = VehicleType::factory()->create(['name' => 'SUV']);
        $berline = VehicleType::factory()->create(['name' => 'Berline']);
        Vehicle::factory()->for($suv)->create(['brand' => 'Toyota', 'model' => 'Fortuner']);
        Vehicle::factory()->for($berline)->create(['brand' => 'Toyota', 'model' => 'Corolla']);

        $this->actingAsManager()
            ->get("/vehicules?type={$suv->id}")
            ->assertSee('Fortuner')
            ->assertDontSee('Corolla');
    }

    public function test_it_filters_by_status(): void
    {
        Vehicle::factory()->create(['model' => 'ModeleEnStock', 'stock_quantity' => 5]);
        Vehicle::factory()->create(['model' => 'ModeleEpuise', 'stock_quantity' => 0]);

        $this->actingAsManager()
            ->get('/vehicules?status=epuise')
            ->assertSee('ModeleEpuise')
            ->assertDontSee('ModeleEnStock');
    }

    public function test_the_search_matches_brand_and_model(): void
    {
        Vehicle::factory()->create(['brand' => 'Toyota', 'model' => 'Fortuner']);
        Vehicle::factory()->create(['brand' => 'Mercedes', 'model' => 'Classe E']);

        $this->actingAsManager()
            ->get('/vehicules?q=Fortuner')
            ->assertSee('Fortuner')
            ->assertDontSee('Classe E');
    }

    public function test_it_tells_the_user_when_nothing_matches(): void
    {
        $this->actingAsManager()
            ->get('/vehicules?q=introuvable')
            ->assertOk()
            ->assertSee('Aucun véhicule ne correspond');
    }

    public function test_a_viewer_does_not_see_the_add_button(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/vehicules')
            ->assertOk()
            ->assertDontSee('Ajouter un véhicule');
    }

    public function test_a_manager_sees_the_add_button(): void
    {
        $this->actingAsManager()
            ->get('/vehicules')
            ->assertOk()
            ->assertSee('Ajouter un véhicule');
    }
}
