<?php
namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocativeSubnavTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_locative_page_links_to_every_other_locative_section(): void
    {
        $this->actingAs(User::factory()->manager()->create());

        foreach (['properties.index', 'tenants.index'] as $routeName) {
            $this->get(route($routeName))
                ->assertOk()
                ->assertSee(route('properties.index'), false)
                ->assertSee(route('tenants.index'), false)
                ->assertSee(route('payments.create'), false);
        }
    }

    public function test_the_duplicated_legacy_sidebar_section_is_gone(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->get(route('properties.index'))
            ->assertOk()
            ->assertDontSee('MEEXEO (historique)');
    }
}
