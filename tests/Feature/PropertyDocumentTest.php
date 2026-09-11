<?php
// tests/Feature/PropertyDocumentTest.php
namespace Tests\Feature;

use App\Enums\DocumentType;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_manager_can_attach_a_land_title_as_pdf(): void
    {
        Storage::fake('public');
        $property = Property::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->post("/biens/{$property->id}/documents", [
                'type' => DocumentType::LandTitle->value,
                'file' => UploadedFile::fake()->create('titre.pdf', 200, 'application/pdf'),
            ])
            ->assertRedirect();

        $document = $property->fresh()->documents()->sole();

        $this->assertSame(DocumentType::LandTitle, $document->type);
        $this->assertSame('titre.pdf', $document->original_name);
        $this->assertFalse($document->verified);
        Storage::disk('public')->assertExists($document->path);
    }

    public function test_an_unknown_document_type_is_rejected(): void
    {
        Storage::fake('public');
        $property = Property::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->from("/biens/{$property->id}")
            ->post("/biens/{$property->id}/documents", [
                'type' => 'permis-de-conduire',
                'file' => UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf'),
            ])
            ->assertSessionHasErrors('type');
    }

    public function test_a_file_above_ten_megabytes_is_rejected(): void
    {
        Storage::fake('public');
        $property = Property::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->from("/biens/{$property->id}")
            ->post("/biens/{$property->id}/documents", [
                'type' => DocumentType::Plan->value,
                'file' => UploadedFile::fake()->create('plan.pdf', 11 * 1024, 'application/pdf'),
            ])
            ->assertSessionHasErrors('file');
    }

    public function test_a_failed_upload_reopens_the_record_on_the_documents_tab(): void
    {
        // Régression : le formulaire d'envoi vit dans l'onglet Documents, qui n'est
        // pas le premier onglet. Sans ce correctif, la page se rechargeait sur
        // l'onglet Détails et l'erreur restait invisible derrière un panneau caché.
        // Le client de test HTTP ne renvoie pas automatiquement le cookie de session
        // d'une requête à l'autre : on le retransmet nous-mêmes pour reproduire
        // fidèlement le comportement d'un vrai navigateur suivant la redirection.
        Storage::fake('public');
        $property = Property::factory()->create();
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->from("/biens/{$property->id}")
            ->post("/biens/{$property->id}/documents", [
                'type' => 'permis-de-conduire',
                'file' => UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf'),
            ])
            ->assertSessionHasErrors('type');

        $sessionId = $this->app['session']->getId();

        $response = $this->actingAs($manager)
            ->withCookie(config('session.cookie'), $sessionId)
            ->get("/biens/{$property->id}");

        $response->assertOk();
        // Le tabulateur doit s'initialiser sur l'onglet Documents, pas sur le premier.
        $response->assertSee("onglet: 'documents'", false);
    }

    public function test_a_record_with_no_error_still_opens_on_the_first_tab(): void
    {
        $property = Property::factory()->create();

        $response = $this->actingAs(User::factory()->manager()->create())
            ->get("/biens/{$property->id}");

        $response->assertOk()->assertSee("onglet: 'details'", false);
    }
}
