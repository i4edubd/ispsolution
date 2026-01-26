<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\NetworkUser;
use App\Models\RadReply;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerSpeedLimitController extends Controller
{
    /**
     * Display speed limit management for a customer.
     */
    public function show(User $customer): View
    {
        $this->authorize('editSpeedLimit', $customer);

        $networkUser = NetworkUser::where('user_id', $customer->id)->first();
        
        // Get current speed limit from RADIUS
        $speedLimit = null;
        if ($networkUser) {
            $radReply = RadReply::where('username', $networkUser->username)
                ->where('attribute', 'Mikrotik-Rate-Limit')
                ->first();
            
            if ($radReply) {
                // Parse format: upload/download (e.g., "512k/1024k")
                $parts = explode('/', $radReply->value);
                if (count($parts) === 2) {
                    $speedLimit = [
                        'upload' => (int) str_replace('k', '', $parts[0]),
                        'download' => (int) str_replace('k', '', $parts[1]),
                    ];
                }
            }
        }

        // Get package default speeds
        $packageSpeed = null;
        if ($networkUser && $networkUser->package) {
            $packageSpeed = [
                'upload' => $networkUser->package->bandwidth_upload,
                'download' => $networkUser->package->bandwidth_download,
            ];
        }

        return view('panel.customers.speed-limit.show', compact('customer', 'networkUser', 'speedLimit', 'packageSpeed'));
    }

    /**
     * Update or create speed limit for a customer.
     */
    public function update(Request $request, User $customer): RedirectResponse
    {
        $this->authorize('editSpeedLimit', $customer);

        $request->validate([
            'upload_speed' => 'required|integer|min:0',
            'download_speed' => 'required|integer|min:0',
            'use_package_default' => 'boolean',
        ]);

        $networkUser = NetworkUser::where('user_id', $customer->id)->firstOrFail();

        DB::beginTransaction();
        try {
            $uploadSpeed = $request->input('upload_speed');
            $downloadSpeed = $request->input('download_speed');
            $usePackageDefault = $request->boolean('use_package_default');

            // If "0 = managed by router" option is selected
            if ($uploadSpeed === 0 && $downloadSpeed === 0) {
                // Remove custom rate limit, let router/package manage
                RadReply::where('username', $networkUser->username)
                    ->where('attribute', 'Mikrotik-Rate-Limit')
                    ->delete();

                $this->logAction($customer, 'Speed limit removed - managed by router');

                DB::commit();
                return back()->with('success', 'Speed limit removed. Now managed by router/package settings.');
            }

            // Use package default speeds if requested
            if ($usePackageDefault && $networkUser->package) {
                $uploadSpeed = $networkUser->package->bandwidth_upload;
                $downloadSpeed = $networkUser->package->bandwidth_download;
            }

            // Validate speeds
            if ($uploadSpeed <= 0 || $downloadSpeed <= 0) {
                return back()->withErrors(['error' => 'Upload and download speeds must be greater than 0']);
            }

            // Format: upload/download (in Kbps)
            $rateLimit = "{$uploadSpeed}k/{$downloadSpeed}k";

            // Update RADIUS attribute
            RadReply::updateOrCreate(
                ['username' => $networkUser->username, 'attribute' => 'Mikrotik-Rate-Limit'],
                ['op' => ':=', 'value' => $rateLimit]
            );

            // Log action
            $this->logAction($customer, "Speed limit updated to {$uploadSpeed}Kbps upload / {$downloadSpeed}Kbps download");

            DB::commit();

            return back()->with('success', 'Speed limit updated successfully. Customer needs to reconnect for changes to take effect.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update speed limit: ' . $e->getMessage()]);
        }
    }

    /**
     * Reset speed limit to package default.
     */
    public function reset(User $customer): RedirectResponse
    {
        $this->authorize('editSpeedLimit', $customer);

        $networkUser = NetworkUser::where('user_id', $customer->id)->firstOrFail();

        DB::beginTransaction();
        try {
            if (!$networkUser->package) {
                return back()->withErrors(['error' => 'Customer has no package assigned.']);
            }

            $uploadSpeed = $networkUser->package->bandwidth_upload;
            $downloadSpeed = $networkUser->package->bandwidth_download;

            if (!$uploadSpeed || !$downloadSpeed) {
                return back()->withErrors(['error' => 'Package has no speed limits defined.']);
            }

            // Format: upload/download (in Kbps)
            $rateLimit = "{$uploadSpeed}k/{$downloadSpeed}k";

            // Update RADIUS attribute
            RadReply::updateOrCreate(
                ['username' => $networkUser->username, 'attribute' => 'Mikrotik-Rate-Limit'],
                ['op' => ':=', 'value' => $rateLimit]
            );

            // Log action
            $this->logAction($customer, "Speed limit reset to package default: {$uploadSpeed}Kbps / {$downloadSpeed}Kbps");

            DB::commit();

            return back()->with('success', 'Speed limit reset to package default successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to reset speed limit: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove speed limit (let router manage).
     */
    public function destroy(User $customer): RedirectResponse
    {
        $this->authorize('editSpeedLimit', $customer);

        $networkUser = NetworkUser::where('user_id', $customer->id)->firstOrFail();

        DB::beginTransaction();
        try {
            // Remove custom rate limit
            RadReply::where('username', $networkUser->username)
                ->where('attribute', 'Mikrotik-Rate-Limit')
                ->delete();

            // Log action
            $this->logAction($customer, 'Speed limit removed - now managed by router');

            DB::commit();

            return back()->with('success', 'Speed limit removed successfully. Router will manage bandwidth.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to remove speed limit: ' . $e->getMessage()]);
        }
    }

    /**
     * Log action to audit log.
     */
    protected function logAction(User $customer, string $description): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'customer.speed_limit.update',
            'description' => $description,
            'model_type' => User::class,
            'model_id' => $customer->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
