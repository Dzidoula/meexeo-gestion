<?php
// app/Http/Controllers/TenantDocumentController.php
namespace App\Http\Controllers;

use App\Enums\TenantDocumentType;
use App\Models\Tenant;
use App\Models\TenantDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TenantDocumentController extends Controller
{
    public function store(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::enum(TenantDocumentType::class)],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'type.required' => 'Choisissez le type de pièce.',
            'file.max' => 'La pièce ne doit pas dépasser 10 Mo.',
        ]);

        $file = $request->file('file');

        $tenant->documents()->create([
            'type' => $validated['type'],
            'path' => $file->store('tenants/documents', 'public'),
            'original_name' => $file->getClientOriginalName(),
            'verified' => false,
        ]);

        return back()->with('status', 'La pièce a été ajoutée.');
    }

    public function destroy(Tenant $tenant, TenantDocument $document): RedirectResponse
    {
        abort_unless($document->tenant_id === $tenant->id, 404);

        Storage::disk('public')->delete($document->path);
        $document->delete();

        return back()->with('status', 'La pièce a été supprimée.');
    }
}
