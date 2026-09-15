<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductWriteTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsManager(): self
    {
        return $this->actingAs(User::factory()->manager()->create());
    }

    public function test_a_guest_cannot_create_a_product(): void
    {
        $this->get('/produits/nouveau')->assertRedirect('/connexion');
    }

    public function test_a_manager_can_create_a_product(): void
    {
        $category = Category::factory()->create();

        $this->actingAsManager()
            ->post('/produits', [
                'category_id' => $category->id,
                'name' => 'T-shirt MASTERCLAYS',
                'description' => 'Coton, taille unique',
                'price' => 12000,
                'stock_quantity' => 25,
            ])
            ->assertRedirect();

        $product = Product::sole();
        $this->assertSame('T-shirt MASTERCLAYS', $product->name);
        $this->assertSame(12000, $product->price);
        $this->assertSame(25, $product->stock_quantity);
        $this->assertSame($category->id, $product->category_id);
    }

    public function test_a_viewer_cannot_create_a_product(): void
    {
        $category = Category::factory()->create();

        $this->actingAs(User::factory()->viewer()->create())
            ->post('/produits', [
                'category_id' => $category->id, 'name' => 'X', 'price' => 1000, 'stock_quantity' => 1,
            ])
            ->assertForbidden();
    }

    public function test_the_price_must_be_a_non_negative_integer(): void
    {
        $category = Category::factory()->create();

        $this->actingAsManager()
            ->post('/produits', [
                'category_id' => $category->id, 'name' => 'X', 'price' => -100, 'stock_quantity' => 1,
            ])
            ->assertSessionHasErrors('price');
    }

    public function test_the_category_must_exist(): void
    {
        $this->actingAsManager()
            ->post('/produits', [
                'category_id' => 999999, 'name' => 'X', 'price' => 1000, 'stock_quantity' => 1,
            ])
            ->assertSessionHasErrors('category_id');
    }

    public function test_a_manager_can_update_a_product(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 5]);

        $this->actingAsManager()
            ->put("/produits/{$product->id}", [
                'category_id' => $product->category_id,
                'name' => $product->name,
                'price' => $product->price,
                'stock_quantity' => 0,
            ])
            ->assertRedirect();

        $this->assertSame(0, $product->fresh()->stock_quantity);
    }
}
