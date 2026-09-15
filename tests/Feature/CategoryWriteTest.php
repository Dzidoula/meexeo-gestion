<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryWriteTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsManager(): self
    {
        return $this->actingAs(User::factory()->manager()->create());
    }

    public function test_a_guest_cannot_see_the_category_list(): void
    {
        $this->get('/categories')->assertRedirect('/connexion');
    }

    public function test_a_manager_can_create_a_category(): void
    {
        $this->actingAsManager()
            ->post('/categories', ['name' => 'Vêtements'])
            ->assertRedirect();

        $category = Category::sole();
        $this->assertSame('Vêtements', $category->name);
        $this->assertSame('vetements', $category->slug);
    }

    public function test_a_viewer_cannot_create_a_category(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->post('/categories', ['name' => 'Vêtements'])
            ->assertForbidden();
    }

    public function test_the_name_is_required(): void
    {
        $this->actingAsManager()
            ->post('/categories', ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    public function test_the_name_must_be_unique(): void
    {
        Category::factory()->create(['name' => 'Vêtements']);

        $this->actingAsManager()
            ->post('/categories', ['name' => 'Vêtements'])
            ->assertSessionHasErrors('name');
    }

    public function test_a_manager_can_update_a_category(): void
    {
        $category = Category::factory()->create(['name' => 'Ancien nom']);

        $this->actingAsManager()
            ->put("/categories/{$category->id}", ['name' => 'Nouveau nom'])
            ->assertRedirect();

        $this->assertSame('Nouveau nom', $category->fresh()->name);
        $this->assertSame('nouveau-nom', $category->fresh()->slug);
    }

    public function test_a_category_without_products_can_be_deleted(): void
    {
        $category = Category::factory()->create();

        $this->actingAsManager()
            ->delete("/categories/{$category->id}")
            ->assertRedirect();

        $this->assertModelMissing($category);
    }

    public function test_a_category_with_products_cannot_be_deleted(): void
    {
        $category = Category::factory()->create();
        \App\Models\Product::factory()->for($category)->create();

        $this->actingAsManager()
            ->delete("/categories/{$category->id}")
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertModelExists($category);
    }

    public function test_it_lists_categories(): void
    {
        Category::factory()->create(['name' => 'Vêtements']);

        $this->actingAsManager()
            ->get('/categories')
            ->assertOk()
            ->assertSee('Vêtements');
    }
}
