<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ApiKeyController extends Controller
{
    /**
     * Display API key management page.
     */
    public function index(): View
    {
        $apiKeys = ApiKey::where('tenant_id', auth()->user()->tenant_id)
            ->latest()
            ->paginate(20);

        return view('panels.shared.api-keys.index', compact('apiKeys'));
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('panels.shared.api-keys.create');
    }

    /**
     * Store new API key.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'rate_limit' => 'nullable|integer|min:1|max:1000',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $key = Str::random(64);

        $apiKey = ApiKey::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $validated['name'],
            'key' => $key,
            'permissions' => $validated['permissions'] ?? [],
            'rate_limit' => $validated['rate_limit'] ?? 60,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('api-keys.show', $apiKey)
            ->with('success', 'API key created successfully!')
            ->with('new_key', $key);
    }

    /**
     * Show API key details (only shown once after creation).
     */
    public function show(ApiKey $apiKey): View
    {
        // Ensure user can only view keys from their tenant
        if ($apiKey->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $newKey = session('new_key');

        return view('panels.shared.api-keys.show', compact('apiKey', 'newKey'));
    }

    /**
     * Show edit form.
     */
    public function edit(ApiKey $apiKey): View
    {
        if ($apiKey->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        return view('panels.shared.api-keys.edit', compact('apiKey'));
    }

    /**
     * Update API key.
     */
    public function update(Request $request, ApiKey $apiKey): RedirectResponse
    {
        if ($apiKey->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'rate_limit' => 'nullable|integer|min:1|max:1000',
            'expires_at' => 'nullable|date|after:today',
            'is_active' => 'boolean',
        ]);

        $apiKey->update($validated);

        return redirect()
            ->route('api-keys.index')
            ->with('success', 'API key updated successfully!');
    }

    /**
     * Revoke API key.
     */
    public function destroy(ApiKey $apiKey): RedirectResponse
    {
        if ($apiKey->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $apiKey->update(['is_active' => false]);

        return redirect()
            ->route('api-keys.index')
            ->with('success', 'API key revoked successfully!');
    }
}
