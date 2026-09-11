<?php
// app/Http/Controllers/PropertyDocumentController.php
namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Models\Property;
use App\Models\PropertyDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PropertyDocumentController extends Controller
{
    public function store(Request $request, Property $property): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::enum(DocumentType::class)],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'type.required' => 'Choisissez le type de document.',
            'file.max' => 'Le document ne doit pas dépasser 10 Mo.',
        ]);

        $file = $request->file('file');

        $property->documents()->create([
            'type' => $validated['type'],
            'path' => $file->store('properties/documents', 'public'),
            'original_name' => $file->getClientOriginalName(),
            'verified' => false,
        ]);

        return back()->with('status', 'Le document a été ajouté.');
    }

    public function destroy(Property $property, PropertyDocument $document): RedirectResponse
    {
        abort_unless($document->property_id === $property->id, 404);

        Storage::disk('public')->delete($document->path);
        $document->delete();

        return back()->with('status', 'Le document a été supprimé.');
    }
}
