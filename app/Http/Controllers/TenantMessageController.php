<?php
// app/Http/Controllers/TenantMessageController.php
namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Côté gestionnaire de la messagerie du portail locataire. */
class TenantMessageController extends Controller
{
    public function store(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'subject'   => ['nullable', 'string', 'max:150'],
            'body'      => ['required', 'string', 'min:2', 'max:5000'],
            'parent_id' => ['nullable', 'integer'],
        ], [
            'body.required' => 'Écrivez le message.',
        ]);

        $parent = null;

        if (! empty($validated['parent_id'])) {
            $parent = TenantMessage::find($validated['parent_id']);
            // Le fil visé doit bien appartenir à ce locataire.
            abort_unless($parent && $parent->tenant_id === $tenant->id, 404);
        }

        TenantMessage::create([
            'tenant_id' => $tenant->id,
            'sender'    => 'manager',
            'user_id'   => $request->user('web')->id,
            'parent_id' => $parent?->id,
            'subject'   => $parent ? null : ($validated['subject'] ?: 'Message de votre gestionnaire'),
            'body'      => $validated['body'],
            // Non lu par le locataire : c'est lui le destinataire.
            'read_at'   => null,
        ]);

        return back()->with('status', 'Message envoyé au locataire.');
    }
}
