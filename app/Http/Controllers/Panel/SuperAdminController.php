<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\NetworkUser;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    /**
     * Display the super admin dashboard.
     */
    public function dashboard(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_network_users' => NetworkUser::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_roles' => Role::count(),
        ];

        return view('panels.super-admin.dashboard', compact('stats'));
    }

    /**
     * Display users listing.
     */
    public function users(): View
    {
        $users = User::with('roles')->latest()->paginate(20);

        return view('panels.super-admin.users.index', compact('users'));
    }

    /**
     * Display roles listing.
     */
    public function roles(): View
    {
        $roles = Role::withCount('users')->get();

        return view('panels.super-admin.roles.index', compact('roles'));
    }

    /**
     * Display system settings.
     */
    public function settings(): View
    {
        return view('panels.super-admin.settings');
    }
}
