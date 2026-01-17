<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\NetworkUser;
use App\Models\ServicePackage;
use App\Models\MikrotikRouter;
use App\Models\Nas;
use App\Models\CiscoDevice;
use App\Models\Olt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard(): View
    {
        $tenantId = auth()->user()->tenant_id;
        
        $stats = [
            'total_users' => User::where('tenant_id', $tenantId)->count(),
            'total_network_users' => NetworkUser::where('tenant_id', $tenantId)->count(),
            'active_users' => User::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'total_packages' => ServicePackage::where('tenant_id', $tenantId)->count(),
            'total_mikrotik' => MikrotikRouter::where('tenant_id', $tenantId)->count(),
            'total_nas' => Nas::where('tenant_id', $tenantId)->count(),
            'total_cisco' => CiscoDevice::where('tenant_id', $tenantId)->count(),
            'total_olt' => Olt::where('tenant_id', $tenantId)->count(),
        ];

        return view('panels.admin.dashboard', compact('stats'));
    }

    /**
     * Display users listing.
     */
    public function users(): View
    {
        $tenantId = auth()->user()->tenant_id;
        $users = User::where('tenant_id', $tenantId)->with('roles')->latest()->paginate(20);

        return view('panels.admin.users.index', compact('users'));
    }

    /**
     * Display network users listing.
     */
    public function networkUsers(): View
    {
        $tenantId = auth()->user()->tenant_id;
        $networkUsers = NetworkUser::where('tenant_id', $tenantId)->latest()->paginate(20);

        return view('panels.admin.network-users.index', compact('networkUsers'));
    }

    /**
     * Display packages listing.
     */
    public function packages(): View
    {
        $tenantId = auth()->user()->tenant_id;
        $packages = ServicePackage::where('tenant_id', $tenantId)->get();

        return view('panels.admin.packages.index', compact('packages'));
    }

    /**
     * Display settings.
     */
    public function settings(): View
    {
        return view('panels.admin.settings');
    }

    /**
     * Display MikroTik routers listing.
     */
    public function mikrotikRouters(): View
    {
        $tenantId = auth()->user()->tenant_id;
        $routers = MikrotikRouter::where('tenant_id', $tenantId)->latest()->paginate(20);

        return view('panels.admin.mikrotik.index', compact('routers'));
    }

    /**
     * Display NAS devices listing.
     */
    public function nasDevices(): View
    {
        $tenantId = auth()->user()->tenant_id;
        $devices = Nas::where('tenant_id', $tenantId)->latest()->paginate(20);

        return view('panels.admin.nas.index', compact('devices'));
    }

    /**
     * Display Cisco devices listing.
     */
    public function ciscoDevices(): View
    {
        $tenantId = auth()->user()->tenant_id;
        $devices = CiscoDevice::where('tenant_id', $tenantId)->latest()->paginate(20);

        return view('panels.admin.cisco.index', compact('devices'));
    }

    /**
     * Display OLT devices listing.
     */
    public function oltDevices(): View
    {
        $tenantId = auth()->user()->tenant_id;
        $devices = Olt::where('tenant_id', $tenantId)->latest()->paginate(20);

        return view('panels.admin.olt.index', compact('devices'));
    }
}
