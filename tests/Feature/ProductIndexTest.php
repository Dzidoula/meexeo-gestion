<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductIndexTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsManager(): self
    {
        return $this->actingAs(User::factory()->manager()->create());
    }

    public function test_a_guest_cannot_see_the_catalog(): void
    {
        $this->get('/produits')->assertRedirect('/connexion');
    }

    public function test_it_shows_the_three_kpis(): void
    {
        Product::factory()->create(['price' => 10000, 'stock_quantity' => 5]);
        Product::factory()->create(['price' => 20000, 'stock_quantity' => 0]);

        $html = $this->actingAsManager()->get('/produits')->assertOk()->getContent();

        // Ancrées sur la carte KPI précise (libellé puis valeur), pas un chiffre isolé.
        $this->assertMatchesRegularExpression('/Produits actifs<\/div>\s*<div[^>]*>\s*2\s*<\/div>/u', $html);
        $this->assertMatchesRegularExpression('/Valeur du stock<\/div>\s*<div[^>]*>\s*50 000 FCFA\s*<\/div>/u', $html);
        $this->assertMatchesRegularExpression('/Produits en rupture<\/div>\s*<div[^>]*>\s*1\s*<\/div>/u', $html);
    }

    public function test_the_kpis_are_zero_on_an_empty_catalog(): void
    {
        $this->actingAsManager()
            ->get('/produits')
            ->assertOk()
            ->assertSee('0 FCFA');
    }

    public function test_it_filters_by_category(): void
    {
        $vetements = Category::factory()->create(['name' => 'Vêtements']);
        $electronique = Category::factory()->create(['name' => 'Électronique']);
        Product::factory()->for($vetements)->create(['name' => 'T-shirt']);
        Product::factory()->for($electronique)->create(['name' => 'Casque audio']);

        $this->actingAsManager()
            ->get("/produits?category={$vetements->id}")
            ->assertSee('T-shirt')
            ->assertDontSee('Casque audio');
    }

    public function test_it_filters_by_status(): void
    {
        Product::factory()->create(['name' => 'Disponible', 'stock_quantity' => 5]);
        Product::factory()->create(['name' => 'Épuisé', 'stock_quantity' => 0]);

        $this->actingAsManager()
            ->get('/produits?status=rupture')
            ->assertSee('Épuisé')
            ->assertDontSee('Disponible');
    }

    public function test_the_search_matches_the_name(): void
    {
        Product::factory()->create(['name' => 'Sac en cuir']);
        Product::factory()->create(['name' => 'Montre connectée']);

        $this->actingAsManager()
            ->get('/produits?q=cuir')
            ->assertSee('Sac en cuir')
            ->assertDontSee('Montre connectée');
    }

    public function test_it_tells_the_user_when_nothing_matches(): void
    {
        $this->actingAsManager()
            ->get('/produits?q=introuvable')
            ->assertOk()
            ->assertSee('Aucun produit ne correspond');
    }

    public function test_a_viewer_does_not_see_the_add_button(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get('/produits')
            ->assertOk()
            ->assertDontSee('Ajouter un produit');
    }

    public function test_a_manager_sees_the_add_button(): void
    {
        $this->actingAsManager()
            ->get('/produits')
            ->assertOk()
            ->assertSee('Ajouter un produit');
    }
}
