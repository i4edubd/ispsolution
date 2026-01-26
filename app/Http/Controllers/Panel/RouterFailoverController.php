<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\MikrotikRouter;
use App\Services\RouterRadiusFailoverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RouterFailoverController extends Controller
{
    public function __construct(
        private RouterRadiusFailoverService $failoverService
    ) {}

    /**
     * Display router failover management interface.
     */
    public function index(): View
    {
        $routers = MikrotikRouter::with('nas')
            ->where('tenant_id', getCurrentTenantId())
            ->orderBy('name')
            ->get();

        return view('panels.admin.routers.failover.index', compact('routers'));
    }

    /**
     * Show failover status for a specific router.
     */
    public function show(int $routerId): View
    {
        $router = MikrotikRouter::where('tenant_id', getCurrentTenantId())
            ->findOrFail($routerId);

        $status = $this->failoverService->getRadiusStatus($router);

        return view('panels.admin.routers.failover.show', compact('router', 'status'));
    }

    /**
     * Configure failover for a router.
     */
    public function configure(Request $request, int $routerId): JsonResponse
    {
        $router = MikrotikRouter::where('tenant_id', getCurrentTenantId())
            ->findOrFail($routerId);

        $success = $this->failoverService->configureFailover($router);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Failover configured successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to configure failover.',
        ], 500);
    }

    /**
     * Switch router to RADIUS authentication mode.
     */
    public function switchToRadius(int $routerId): JsonResponse
    {
        $router = MikrotikRouter::where('tenant_id', getCurrentTenantId())
            ->findOrFail($routerId);

        $success = $this->failoverService->switchToRadiusMode($router);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Switched to RADIUS authentication mode.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to switch to RADIUS mode.',
        ], 500);
    }

    /**
     * Switch router to local authentication mode.
     */
    public function switchToRouter(int $routerId): JsonResponse
    {
        $router = MikrotikRouter::where('tenant_id', getCurrentTenantId())
            ->findOrFail($routerId);

        $success = $this->failoverService->switchToRouterMode($router);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Switched to local authentication mode.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to switch to local mode.',
        ], 500);
    }

    /**
     * Get RADIUS status and failover information.
     */
    public function status(int $routerId): JsonResponse
    {
        $router = MikrotikRouter::where('tenant_id', getCurrentTenantId())
            ->findOrFail($routerId);

        $status = $this->failoverService->getRadiusStatus($router);

        return response()->json($status);
    }

    /**
     * Test RADIUS server connectivity.
     */
    public function testConnection(int $routerId): JsonResponse
    {
        $router = MikrotikRouter::where('tenant_id', getCurrentTenantId())
            ->findOrFail($routerId);

        $success = $this->failoverService->testRadiusConnection($router);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'RADIUS server is reachable.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'RADIUS server is unreachable.',
        ], 500);
    }
}
