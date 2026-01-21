<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    /**
     * Display audit log viewer.
     */
    public function index(Request $request): View
    {
        $query = AuditLog::with('user')
            ->where('tenant_id', auth()->user()->tenant_id)
            ->latest();

        // Apply filters
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }

        $logs = $query->paginate(50);

        // Get unique events for filter
        $events = AuditLog::where('tenant_id', auth()->user()->tenant_id)
            ->select('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        // Get unique tags
        $allTags = AuditLog::where('tenant_id', auth()->user()->tenant_id)
            ->whereNotNull('tags')
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        return view('panels.shared.audit-logs.index', compact('logs', 'events', 'allTags'));
    }

    /**
     * Display specific audit log details.
     */
    public function show(AuditLog $auditLog): View
    {
        // Ensure user can only view logs from their tenant
        if ($auditLog->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        return view('panels.shared.audit-logs.show', compact('auditLog'));
    }
}
