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
}
