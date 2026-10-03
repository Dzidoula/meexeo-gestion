<?php
// app/Http/Controllers/PortalDocumentController.php
namespace App\Http\Controllers;

use App\Enums\PortalDocumentCategory;
use App\Models\PortalDocument;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Documents publiés AU locataire dans son portail (bail signé, quittance).
 * Distinct de TenantDocumentController, qui gère les pièces justificatives
 * collectées PAR le gestionnaire au montage du dossier.
 */
class PortalDocumentController extends Controller
{
    public function store(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', Rule::enum(PortalDocumentCategory::class)],
            'file'     => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'period'   => ['nullable', 'date_format:Y-m'],
        ], [
            'category.required'   => 'Choisissez le type de document.',
            'file.required'       => 'Joignez le fichier à publier.',
            'file.max'            => 'Le document ne doit pas dépasser 10 Mo.',
            'period.date_format'  => 'Le mois doit être au format AAAA-MM.',
        ]);

        $file = $request->file('file');

        // Disque "local" et non "public" : une quittance est nominative et ne
        // doit pas être servie par une URL devinable.
        PortalDocument::create([
            'tenant_id'     => $tenant->id,
            'lease_id'      => $tenant->activeLease?->id,
            'category'      => $validated['category'],
            'path'          => $file->store('portal-documents/'.$tenant->id, 'local'),
            'original_name' => $file->getClientOriginalName(),
            'size'          => $file->getSize(),
            'period'        => ! empty($validated['period']) ? $validated['period'].'-01' : null,
            'issued_at'     => now(),
        ]);

        return back()->with('status', 'Le document est publié dans l\'espace du locataire.');
    }

    public function destroy(Tenant $tenant, PortalDocument $document): RedirectResponse
    {
        abort_unless($document->tenant_id === $tenant->id, 404);

        Storage::disk('local')->delete($document->path);
        $document->delete();

        return back()->with('status', 'Le document a été retiré du portail.');
    }
}
