<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\NetworkUser;
use App\Models\MikrotikRouter;
use App\Models\Nas;
use App\Models\CiscoDevice;
use App\Models\Olt;
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

        // Add device stats if user has permissions
        $user = auth()->user();
        if ($user->hasPermission('devices.mikrotik.view')) {
            $stats['total_mikrotik'] = MikrotikRouter::where('tenant_id', $tenantId)->count();
        }
        if ($user->hasPermission('devices.nas.view')) {
            $stats['total_nas'] = Nas::where('tenant_id', $tenantId)->count();
        }
        if ($user->hasPermission('devices.cisco.view')) {
            $stats['total_cisco'] = CiscoDevice::where('tenant_id', $tenantId)->count();
        }
        if ($user->hasPermission('devices.olt.view')) {
            $stats['total_olt'] = Olt::where('tenant_id', $tenantId)->count();
        }

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

    /**
     * Display MikroTik routers listing (if permitted).
     */
    public function mikrotikRouters(): View
    {
        if (!auth()->user()->hasPermission('devices.mikrotik.view')) {
            abort(403, 'Unauthorized access to MikroTik routers.');
        }

        $tenantId = auth()->user()->tenant_id;
        $routers = MikrotikRouter::where('tenant_id', $tenantId)->latest()->paginate(20);

        return view('panels.staff.mikrotik.index', compact('routers'));
    }

    /**
     * Display NAS devices listing (if permitted).
     */
    public function nasDevices(): View
    {
        if (!auth()->user()->hasPermission('devices.nas.view')) {
            abort(403, 'Unauthorized access to NAS devices.');
        }

        $tenantId = auth()->user()->tenant_id;
        $devices = Nas::where('tenant_id', $tenantId)->latest()->paginate(20);

        return view('panels.staff.nas.index', compact('devices'));
    }

    /**
     * Display Cisco devices listing (if permitted).
     */
    public function ciscoDevices(): View
    {
        if (!auth()->user()->hasPermission('devices.cisco.view')) {
            abort(403, 'Unauthorized access to Cisco devices.');
        }

        $tenantId = auth()->user()->tenant_id;
        $devices = CiscoDevice::where('tenant_id', $tenantId)->latest()->paginate(20);

        return view('panels.staff.cisco.index', compact('devices'));
    }

    /**
     * Display OLT devices listing (if permitted).
     */
    public function oltDevices(): View
    {
        if (!auth()->user()->hasPermission('devices.olt.view')) {
            abort(403, 'Unauthorized access to OLT devices.');
        }

        $tenantId = auth()->user()->tenant_id;
        $devices = Olt::where('tenant_id', $tenantId)->latest()->paginate(20);

        return view('panels.staff.olt.index', compact('devices'));
    }
}
