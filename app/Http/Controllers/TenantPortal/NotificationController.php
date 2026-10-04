<?php

namespace App\Http\Controllers\TenantPortal;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $tenant = auth()->guard('tenant')->user();

        $notifications = $tenant->notifications()->latest()->paginate(20);

        // Lues à l'ouverture de la page : la liste est chargée avant, pour que
        // la vue puisse encore distinguer ce qui était nouveau.
        $unreadIds = $notifications->whereNull('read_at')->pluck('id')->all();
        $tenant->unreadNotifications()->update(['read_at' => now()]);

        return view('tenant-portal.notifications.index', compact('tenant', 'notifications', 'unreadIds'));
    }
}
