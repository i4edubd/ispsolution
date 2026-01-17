<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\NetworkUser;
use App\Models\NetworkUserSession;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManagerController extends Controller
{
    /**
     * Display the manager dashboard.
     */
    public function dashboard(): View
    {
        $tenantId = auth()->user()->tenant_id;
        
        $stats = [
            'total_network_users' => NetworkUser::where('tenant_id', $tenantId)->count(),
            'active_sessions' => NetworkUserSession::where('tenant_id', $tenantId)
                ->whereNull('end_time')->count(),
            'pppoe_users' => NetworkUser::where('tenant_id', $tenantId)
                ->where('service_type', 'pppoe')->count(),
            'hotspot_users' => NetworkUser::where('tenant_id', $tenantId)
                ->where('service_type', 'hotspot')->count(),
        ];

        return view('panels.manager.dashboard', compact('stats'));
    }

    /**
     * Display network users listing.
     */
    public function networkUsers(): View
    {
        $tenantId = auth()->user()->tenant_id;
        $networkUsers = NetworkUser::where('tenant_id', $tenantId)->latest()->paginate(20);

        return view('panels.manager.network-users.index', compact('networkUsers'));
    }

    /**
     * Display active sessions.
     */
    public function sessions(): View
    {
        $tenantId = auth()->user()->tenant_id;
        $sessions = NetworkUserSession::where('tenant_id', $tenantId)
            ->whereNull('end_time')
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('panels.manager.sessions.index', compact('sessions'));
    }

    /**
     * Display reports.
     */
    public function reports(): View
    {
        return view('panels.manager.reports');
    }
}
