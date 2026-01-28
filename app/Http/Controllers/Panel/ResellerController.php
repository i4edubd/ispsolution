<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Reseller Management Controller
 * Task 7.3: Create reseller management UI
 * 
 * Handles reseller (parent customer) management
 */
class ResellerController extends Controller
{
    /**
     * Display a listing of resellers (customers with child accounts)
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Query users who have child accounts (resellers)
        $query = User::where('tenant_id', $user->tenant_id)
            ->where('operator_level', User::OPERATOR_LEVEL_CUSTOMER) // Only customers
            ->has('childAccounts') // Must have child accounts
            ->with(['childAccounts' => function ($q) {
                $q->select('id', 'parent_id', 'name', 'status');
            }]);

        // Search by name or username
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $resellers = $query->paginate(15);

        return view('panels.admin.resellers.index', compact('resellers'));
    }
}
