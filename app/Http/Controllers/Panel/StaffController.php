<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\NetworkUser;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffController extends Controller
{
    /**
     * Display the staff dashboard.
     */
    public function dashboard(): View
    {
        $tenantId = auth()->user()->tenant_id;
        
        $stats = [
            'assigned_users' => NetworkUser::where('tenant_id', $tenantId)->count(),
            'pending_tickets' => 0, // To be implemented
        ];

        return view('panels.staff.dashboard', compact('stats'));
    }

    /**
     * Display network users listing.
     */
    public function networkUsers(): View
    {
        $tenantId = auth()->user()->tenant_id;
        $networkUsers = NetworkUser::where('tenant_id', $tenantId)->latest()->paginate(20);

        return view('panels.staff.network-users.index', compact('networkUsers'));
    }

    /**
     * Display tickets listing.
     */
    public function tickets(): View
    {
        // To be implemented with ticket system
        return view('panels.staff.tickets.index');
    }
}
