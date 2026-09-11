<?php
// tests/Feature/PropertyShowTest.php
namespace Tests\Feature;

use App\Enums\DocumentType;
use App\Models\Property;
use App\Models\PropertyDocument;
use App\Models\PropertyPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_cannot_see_a_property(): void
    {
        $property = Property::factory()->create();

        $this->get("/biens/{$property->id}")->assertRedirect('/connexion');
    }

    public function test_it_shows_the_identity_panel(): void
    {
        $property = Property::factory()->vacant()->create([
            'title' => 'Villa Aghien',
            'monthly_rent' => 450000,
            'deposit' => 900000,
            'rooms' => 4,
            'area_sqm' => 180,
            'commune' => 'Cocody',
        ]);

        $this->actingAs(User::factory()->viewer()->create())
            ->get("/biens/{$property->id}")
            ->assertOk()
            ->assertSee('Villa Aghien')
            ->assertSee($property->reference)
            ->assertSee('450 000 FCFA')
            ->assertSee('900 000 FCFA')
            ->assertSee('Cocody')
            ->assertSee('Libre');
    }

    public function test_it_lists_the_documents_with_their_french_label(): void
    {
        $property = Property::factory()->create();
        PropertyDocument::factory()->for($property)->create([
            'type' => DocumentType::Acd,
            'original_name' => 'acd-cocody.pdf',
        ]);

        $this->actingAs(User::factory()->viewer()->create())
            ->get("/biens/{$property->id}")
            ->assertSee('ACD')
            ->assertSee('acd-cocody.pdf');
    }

    public function test_it_shows_the_four_tabs(): void
    {
        $property = Property::factory()->create();

        $response = $this->actingAs(User::factory()->viewer()->create())->get("/biens/{$property->id}");

        foreach (['Détails', 'Photos', 'Documents', 'Historique'] as $tab) {
            $response->assertSee($tab);
        }
    }

    public function test_a_viewer_sees_no_write_action(): void
    {
        $property = Property::factory()->create();

        $this->actingAs(User::factory()->viewer()->create())
            ->get("/biens/{$property->id}")
            ->assertDontSee('Modifier le bien');
    }

    public function test_a_manager_sees_the_edit_action(): void
    {
        $property = Property::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->get("/biens/{$property->id}")
            ->assertSee('Modifier le bien');
    }

    public function test_it_shows_the_primary_photo_url(): void
    {
        $property = Property::factory()->create();
        $photo = PropertyPhoto::factory()->for($property)->create(['is_primary' => true]);

        $this->actingAs(User::factory()->viewer()->create())
            ->get("/biens/{$property->id}")
            ->assertSee($photo->url);
    }

    public function test_it_tells_the_user_when_there_is_no_photo_yet(): void
    {
        $property = Property::factory()->create();

        $this->actingAs(User::factory()->viewer()->create())
            ->get("/biens/{$property->id}")
            ->assertSee('Aucune photo');
    }
}
