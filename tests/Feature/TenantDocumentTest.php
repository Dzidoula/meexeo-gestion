<?php
// tests/Feature/TenantDocumentTest.php
namespace Tests\Feature;

use App\Enums\TenantDocumentType;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TenantDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_manager_can_attach_an_id_card(): void
    {
        Storage::fake('public');
        $tenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->post("/locataires/{$tenant->id}/documents", [
                'type' => TenantDocumentType::IdCard->value,
                'file' => UploadedFile::fake()->image('cni.jpg'),
            ])
            ->assertRedirect();

        $document = $tenant->fresh()->documents()->sole();

        $this->assertSame(TenantDocumentType::IdCard, $document->type);
        $this->assertStringStartsWith('tenants/documents/', $document->path);
        Storage::disk('public')->assertExists($document->path);
    }

    public function test_several_payslips_can_be_attached(): void
    {
        Storage::fake('public');
        $tenant = Tenant::factory()->create();
        $manager = User::factory()->manager()->create();

        foreach (['mai.pdf', 'juin.pdf', 'juillet.pdf'] as $name) {
            $this->actingAs($manager)->post("/locataires/{$tenant->id}/documents", [
                'type' => TenantDocumentType::Payslip->value,
                'file' => UploadedFile::fake()->create($name, 100, 'application/pdf'),
            ]);
        }

        $this->assertSame(3, $tenant->fresh()->documents()->where('type', TenantDocumentType::Payslip)->count());
    }

    public function test_deleting_a_document_removes_the_file(): void
    {
        Storage::fake('public');
        $tenant = Tenant::factory()->create();

        $this->actingAs(User::factory()->manager()->create())
            ->post("/locataires/{$tenant->id}/documents", [
                'type' => TenantDocumentType::WorkContract->value,
                'file' => UploadedFile::fake()->create('contrat.pdf', 50, 'application/pdf'),
            ]);

        $document = $tenant->fresh()->documents()->sole();

        $this->actingAs(User::factory()->manager()->create())
            ->delete("/locataires/{$tenant->id}/documents/{$document->id}")
            ->assertRedirect();

        Storage::disk('public')->assertMissing($document->path);
        $this->assertDatabaseCount('tenant_documents', 0);
    }
}
