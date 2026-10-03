<?php

namespace App\Http\Controllers\TenantPortal;

use App\Http\Controllers\Controller;
use App\Models\TenantMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        $tenant = auth()->guard('tenant')->user();

        $threads = TenantMessage::where('tenant_id', $tenant->id)
            ->threads()
            ->with('replies')
            ->latest()
            ->get();

        return view('tenant-portal.messages.index', [
            'tenant'  => $tenant,
            'threads' => $threads,
            'thread'  => $threads->first(),
        ]);
    }

    public function show(TenantMessage $message): View
    {
        $tenant = auth()->guard('tenant')->user();

        abort_if($message->tenant_id !== $tenant->id, 403);

        // Ouvrir un fil le marque lu, lui et ses réponses du gestionnaire.
        TenantMessage::where('tenant_id', $tenant->id)
            ->where(fn ($q) => $q->where('id', $message->id)->orWhere('parent_id', $message->id))
            ->unreadByTenant()
            ->update(['read_at' => now()]);

        $threads = TenantMessage::where('tenant_id', $tenant->id)
            ->threads()->with('replies')->latest()->get();

        return view('tenant-portal.messages.index', [
            'tenant'  => $tenant,
            'threads' => $threads,
            'thread'  => $message->fresh('replies'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = auth()->guard('tenant')->user();

        $data = $request->validate([
            'subject'   => ['nullable', 'string', 'max:150'],
            'body'      => ['required', 'string', 'min:2', 'max:5000'],
            'parent_id' => ['nullable', 'integer'],
        ]);

        $parent = null;

        if (! empty($data['parent_id'])) {
            $parent = TenantMessage::find($data['parent_id']);
            // Le fil visé doit appartenir au locataire connecté.
            abort_if(! $parent || $parent->tenant_id !== $tenant->id, 403);
        }

        $message = TenantMessage::create([
            'tenant_id' => $tenant->id,
            'sender'    => 'tenant',
            'parent_id' => $parent?->id,
            'subject'   => $parent ? null : ($data['subject'] ?: 'Message'),
            'body'      => $data['body'],
            'read_at'   => now(),
        ]);

        return redirect()
            ->route('tenant-portal.messages.show', $parent?->id ?? $message->id)
            ->with('success', 'Message envoyé.');
    }
}
