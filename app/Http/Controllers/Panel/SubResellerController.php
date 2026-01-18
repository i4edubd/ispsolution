<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ServicePackage;
use App\Models\User;
use Illuminate\View\View;

class SubResellerController extends Controller
{
    /**
     * Display the sub-reseller dashboard.
     */
    public function dashboard(): View
    {
        $subResellerId = auth()->id();

        $stats = [
            'total_customers' => User::where('created_by', $subResellerId)->count(),
            'active_customers' => User::where('created_by', $subResellerId)
                ->where('is_active', true)->count(),
            'commission_earned' => 0, // To be calculated
        ];

        return view('panels.sub-reseller.dashboard', compact('stats'));
    }

    /**
     * Display customers listing.
     */
    public function customers(): View
    {
        $subResellerId = auth()->id();
        $customers = User::where('created_by', $subResellerId)->latest()->paginate(20);

        return view('panels.sub-reseller.customers.index', compact('customers'));
    }

    /**
     * Display available packages.
     */
    public function packages(): View
    {
        $tenantId = auth()->user()->tenant_id;
        $packages = ServicePackage::where('tenant_id', $tenantId)->get();

        return view('panels.sub-reseller.packages.index', compact('packages'));
    }

    /**
     * Display commission reports.
     */
    public function commission(): View
    {
        return view('panels.sub-reseller.commission');
    }
}
