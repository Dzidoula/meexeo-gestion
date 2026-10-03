<?php

namespace App\Http\Controllers\TenantPortal;

use App\Enums\PortalDocumentCategory;
use App\Http\Controllers\Controller;
use App\Models\PortalDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $tenant = auth()->guard('tenant')->user();

        $documents = $tenant->portalDocuments()
            ->when($request->filled('q'), fn ($q) => $q->where('original_name', 'like', '%'.$request->q.'%'))
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->get();

        // Compteurs sur l'ensemble, pas sur le résultat filtré : les cartes du haut
        // décrivent le dossier du locataire, pas sa recherche en cours.
        // reorder() : la relation trie par issued_at, ce qu'ONLY_FULL_GROUP_BY refuse
        // dès qu'on agrège sur category.
        $counts = $tenant->portalDocuments()
            ->reorder()
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('tenant-portal.documents.index', [
            'tenant'     => $tenant,
            'documents'  => $documents,
            'counts'     => $counts,
            'categories' => PortalDocumentCategory::cases(),
        ]);
    }

    public function download(PortalDocument $document): StreamedResponse
    {
        $tenant = auth()->guard('tenant')->user();

        // Une quittance est nominative : on vérifie la propriété avant de servir
        // le fichier, plutôt que de l'exposer par une URL publique devinable.
        abort_if($document->tenant_id !== $tenant->id, 403);
        abort_unless(Storage::disk('local')->exists($document->path), 404);

        return Storage::disk('local')->download($document->path, $document->original_name);
    }
}
