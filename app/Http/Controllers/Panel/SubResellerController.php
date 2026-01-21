<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Commission;
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
        $packages = ServicePackage::where('tenant_id', $tenantId)->paginate(20);

        return view('panels.sub-reseller.packages.index', compact('packages'));
    }

    /**
     * Display commission reports.
     */
    public function commission(): View
    {
        $user = auth()->user();

        // Get commission transactions for this sub-reseller
        $transactions = Commission::where('reseller_id', $user->id)
            ->with(['payment', 'invoice'])
            ->latest()
            ->paginate(20);

        $summary = Commission::where('reseller_id', $user->id)
            ->selectRaw('SUM(commission_amount) as total_earnings')
            ->selectRaw('SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN commission_amount ELSE 0 END) as this_month', [now()->month, now()->year])
            ->selectRaw('SUM(CASE WHEN status = ? THEN commission_amount ELSE 0 END) as pending', ['pending'])
            ->selectRaw('SUM(CASE WHEN status = ? THEN commission_amount ELSE 0 END) as paid', ['paid'])
            ->first();

        $summary = [
            'total_earnings' => $summary->total_earnings ?? 0,
            'this_month' => $summary->this_month ?? 0,
            'pending' => $summary->pending ?? 0,
            'paid' => $summary->paid ?? 0,
        ];

        return view('panels.sub-reseller.commission', compact('transactions', 'summary'));
    }
}
