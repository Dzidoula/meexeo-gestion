<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_page_is_titled_masterclays_not_meexeo(): void
    {
        $response = $this->actingAs(User::factory()->manager()->create())->get('/tableau-de-bord');

        $response->assertSee('Tableau de bord — MASTERCLAYS', false);
        $response->assertDontSee('Vue d\'ensemble de l\'activité MEEXEO');
    }

    public function test_the_locatif_section_has_a_heading(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->get('/tableau-de-bord')
            ->assertSee('Locatif');
    }
}
