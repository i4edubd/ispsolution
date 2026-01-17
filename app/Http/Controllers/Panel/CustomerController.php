<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\NetworkUser;
use App\Models\NetworkUserSession;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display the customer dashboard.
     */
    public function dashboard(): View
    {
        $user = auth()->user();
        
        // Get customer's network user account
        $networkUser = NetworkUser::where('user_id', $user->id)->first();
        
        $stats = [
            'current_package' => $user->currentPackage()?->name ?? 'No Package',
            'account_status' => $user->is_active ? 'Active' : 'Inactive',
            'data_usage' => 0, // To be calculated
            'billing_due' => 0, // To be calculated
        ];

        return view('panels.customer.dashboard', compact('stats', 'networkUser'));
    }

    /**
     * Display profile.
     */
    public function profile(): View
    {
        $user = auth()->user();

        return view('panels.customer.profile', compact('user'));
    }

    /**
     * Display billing history.
     */
    public function billing(): View
    {
        // To be implemented with billing system
        return view('panels.customer.billing');
    }

    /**
     * Display usage statistics.
     */
    public function usage(): View
    {
        $user = auth()->user();
        $networkUser = NetworkUser::where('user_id', $user->id)->first();
        
        $sessions = [];
        if ($networkUser) {
            $sessions = NetworkUserSession::where('user_id', $networkUser->id)
                ->latest()
                ->paginate(20);
        }

        return view('panels.customer.usage', compact('sessions'));
    }

    /**
     * Display tickets.
     */
    public function tickets(): View
    {
        // To be implemented with ticket system
        return view('panels.customer.tickets.index');
    }
}
