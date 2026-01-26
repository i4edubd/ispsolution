<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Nas;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NasController extends Controller
{
    /**
     * Display NAS devices list.
     */
    public function index(): View
    {
        $devices = Nas::where('tenant_id', getCurrentTenantId())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('panels.admin.nas.index', compact('devices'));
    }

    /**
     * Show create NAS form.
     */
    public function create(): View
    {
        return view('panels.admin.nas.create');
    }

    /**
     * Store new NAS device.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'nas_name' => 'required|string|max:100',
            'short_name' => 'required|string|max:50',
            'server' => 'required|ip|max:100|unique:nas,server',
            'secret' => 'required|string|max:100',
            'type' => 'required|string|max:50',
            'ports' => 'nullable|integer|min:0',
            'community' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        $validated['tenant_id'] = getCurrentTenantId();

        Nas::create($validated);

        return redirect()->route('panel.admin.network.nas')
            ->with('success', 'NAS device created successfully.');
    }

    /**
     * Show NAS device details.
     */
    public function show($id): View
    {
        $device = Nas::where('tenant_id', getCurrentTenantId())->findOrFail($id);

        return view('panels.admin.nas.show', compact('device'));
    }

    /**
     * Show edit NAS form.
     */
    public function edit($id): View
    {
        $device = Nas::where('tenant_id', getCurrentTenantId())->findOrFail($id);

        return view('panels.admin.nas.edit', compact('device'));
    }

    /**
     * Update NAS device.
     */
    public function update(Request $request, $id)
    {
        $device = Nas::where('tenant_id', getCurrentTenantId())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'nas_name' => 'required|string|max:100',
            'short_name' => 'required|string|max:50',
            'server' => 'required|ip|max:100|unique:nas,server,' . $id,
            'secret' => 'nullable|string|max:100',
            'type' => 'required|string|max:50',
            'ports' => 'nullable|integer|min:0',
            'community' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        // Preserve existing secret if the field was left empty in the update form
        if (array_key_exists('secret', $validated) && ($validated['secret'] === null || $validated['secret'] === '')) {
            unset($validated['secret']);
        }

        $device->update($validated);

        return redirect()->route('panel.admin.network.nas')
            ->with('success', 'NAS device updated successfully.');
    }

    /**
     * Delete NAS device.
     */
    public function destroy($id)
    {
        $device = Nas::where('tenant_id', getCurrentTenantId())->findOrFail($id);
        $device->delete();

        return redirect()->route('panel.admin.network.nas')
            ->with('success', 'NAS device deleted successfully.');
    }

    /**
     * Test NAS connection.
     */
    public function testConnection($id)
    {
        $device = Nas::where('tenant_id', getCurrentTenantId())->findOrFail($id);

        // Validate server is a valid IP address before executing command
        if (! filter_var($device->server, FILTER_VALIDATE_IP)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid IP address format',
            ], 400);
        }

        // Simple ping test with sanitized IP
        $output = [];
        $returnCode = 0;
        $sanitizedIp = escapeshellarg($device->server);
        exec("ping -c 1 -W 2 {$sanitizedIp}", $output, $returnCode);

        if ($returnCode === 0) {
            return response()->json([
                'success' => true,
                'message' => 'Connection successful',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Connection failed - Device unreachable',
        ], 500);
    }
}
