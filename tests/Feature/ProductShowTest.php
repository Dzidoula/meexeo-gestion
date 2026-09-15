<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_cannot_see_the_product(): void
    {
        $product = Product::factory()->create();

        $this->get("/produits/{$product->id}")->assertRedirect('/connexion');
    }

    public function test_it_shows_the_product_details(): void
    {
        $product = Product::factory()->create(['name' => 'Sac à main', 'price' => 45000]);

        $this->actingAs(User::factory()->viewer()->create())
            ->get("/produits/{$product->id}")
            ->assertOk()
            ->assertSee('Sac à main')
            ->assertSee('45 000 FCFA');
    }

    public function test_a_product_in_stock_shows_the_en_stock_badge(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 3]);

        $this->actingAs(User::factory()->viewer()->create())
            ->get("/produits/{$product->id}")
            ->assertSee('En stock');
    }

    public function test_a_product_with_no_stock_shows_the_rupture_badge(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 0]);

        $this->actingAs(User::factory()->viewer()->create())
            ->get("/produits/{$product->id}")
            ->assertSee('Rupture');
    }
}
