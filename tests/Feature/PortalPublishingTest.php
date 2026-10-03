<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Lease;
use App\Models\PortalDocument;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\TenantMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PortalPublishingTest extends TestCase
{
    use RefreshDatabase;

    private function setupTenant(): array
    {
        $manager  = User::factory()->create(['role' => Role::Manager]);
        $tenant   = Tenant::factory()->create();
        $property = Property::factory()->create();
        Lease::factory()->active()->create([
            'tenant_id'   => $tenant->id,
            'property_id' => $property->id,
        ]);

        return compact('manager', 'tenant');
    }

    public function test_a_manager_publishes_a_document_the_tenant_can_see(): void
    {
        Storage::fake('local');
        ['manager' => $manager, 'tenant' => $tenant] = $this->setupTenant();

        $this->actingAs($manager, 'web')
            ->post("/locataires/{$tenant->id}/portail/documents", [
                'category' => 'quittance',
                'period'   => '2026-09',
                'file'     => UploadedFile::fake()->create('quittance-septembre.pdf', 90, 'application/pdf'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('portal_documents', [
            'tenant_id' => $tenant->id,
            'category'  => 'quittance',
        ]);

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/documents')
            ->assertOk()
            ->assertSee('quittance-septembre.pdf');
    }

    public function test_a_published_document_is_not_reachable_by_another_tenant(): void
    {
        Storage::fake('local');
        ['manager' => $manager, 'tenant' => $tenant] = $this->setupTenant();

        $this->actingAs($manager, 'web')->post("/locataires/{$tenant->id}/portail/documents", [
            'category' => 'bail',
            'file'     => UploadedFile::fake()->create('bail.pdf', 50, 'application/pdf'),
        ]);

        $document = PortalDocument::where('tenant_id', $tenant->id)->firstOrFail();
        ['tenant' => $other] = $this->setupTenant();

        $this->actingAs($other, 'tenant')
            ->get("/espace-locataire/documents/{$document->id}")
            ->assertForbidden();
    }

    public function test_a_zip_is_refused(): void
    {
        Storage::fake('local');
        ['manager' => $manager, 'tenant' => $tenant] = $this->setupTenant();

        $this->actingAs($manager, 'web')
            ->post("/locataires/{$tenant->id}/portail/documents", [
                'category' => 'autre',
                'file'     => UploadedFile::fake()->create('archive.zip', 100, 'application/zip'),
            ])
            ->assertSessionHasErrors('file');
    }

    public function test_a_manager_message_reaches_the_tenant_unread(): void
    {
        ['manager' => $manager, 'tenant' => $tenant] = $this->setupTenant();

        $this->actingAs($manager, 'web')
            ->post("/locataires/{$tenant->id}/portail/messages", [
                'subject' => 'Rappel de loyer',
                'body'    => 'Merci de régulariser votre loyer du mois.',
            ])
            ->assertRedirect();

        $message = TenantMessage::where('tenant_id', $tenant->id)->firstOrFail();
        $this->assertSame('manager', $message->sender);
        $this->assertNull($message->read_at, 'Le locataire ne peut pas avoir déjà lu un message qu\'on vient de lui écrire.');

        $this->actingAs($tenant, 'tenant')
            ->get('/espace-locataire/messages')
            ->assertOk()
            ->assertSee('Rappel de loyer');
    }

    public function test_a_manager_cannot_reply_into_another_tenants_thread(): void
    {
        ['manager' => $manager, 'tenant' => $tenant] = $this->setupTenant();
        ['tenant' => $other] = $this->setupTenant();

        $thread = TenantMessage::create([
            'tenant_id' => $other->id,
            'sender'    => 'tenant',
            'subject'   => 'Question',
            'body'      => 'Bonjour',
        ]);

        $this->actingAs($manager, 'web')
            ->post("/locataires/{$tenant->id}/portail/messages", [
                'body'      => 'Réponse injectée',
                'parent_id' => $thread->id,
            ])
            ->assertNotFound();
    }

    public function test_opening_a_thread_marks_the_managers_message_read(): void
    {
        ['manager' => $manager, 'tenant' => $tenant] = $this->setupTenant();

        $this->actingAs($manager, 'web')->post("/locataires/{$tenant->id}/portail/messages", [
            'subject' => 'Information',
            'body'    => 'Votre quittance est disponible.',
        ]);

        $message = TenantMessage::where('tenant_id', $tenant->id)->firstOrFail();

        $this->actingAs($tenant, 'tenant')->get("/espace-locataire/messages/{$message->id}")->assertOk();

        $this->assertNotNull($message->fresh()->read_at);
    }
}
