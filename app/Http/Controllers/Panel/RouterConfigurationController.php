<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\MikrotikRouter;
use App\Services\RouterConfigurationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RouterConfigurationController extends Controller
{
    public function __construct(
        private RouterConfigurationService $configurationService
    ) {}

    /**
     * Display router configuration interface.
     */
    public function index(): View
    {
        $routers = MikrotikRouter::with('nas')
            ->where('tenant_id', getCurrentTenantId())
            ->orderBy('name')
            ->get();

        return view('panels.admin.routers.configuration.index', compact('routers'));
    }

    /**
     * Show router configuration details.
     */
    public function show(int $routerId): View
    {
        $router = MikrotikRouter::where('tenant_id', getCurrentTenantId())
            ->findOrFail($routerId);

        $radiusStatus = $this->configurationService->getRadiusStatus($router);

        return view('panels.admin.routers.configuration.show', compact('router', 'radiusStatus'));
    }

    /**
     * Configure RADIUS on router.
     */
    public function configureRadius(Request $request, int $routerId): JsonResponse
    {
        $router = MikrotikRouter::where('tenant_id', getCurrentTenantId())
            ->findOrFail($routerId);

        $result = $this->configurationService->configureRadius($router);

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Configure PPP settings on router.
     */
    public function configurePpp(Request $request, int $routerId): JsonResponse
    {
        $router = MikrotikRouter::where('tenant_id', getCurrentTenantId())
            ->findOrFail($routerId);

        $result = $this->configurationService->configurePpp($router);

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Configure firewall on router.
     */
    public function configureFirewall(Request $request, int $routerId): JsonResponse
    {
        $router = MikrotikRouter::where('tenant_id', getCurrentTenantId())
            ->findOrFail($routerId);

        $result = $this->configurationService->configureFirewall($router);

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Get RADIUS configuration status.
     */
    public function radiusStatus(int $routerId): JsonResponse
    {
        $router = MikrotikRouter::where('tenant_id', getCurrentTenantId())
            ->findOrFail($routerId);

        $status = $this->configurationService->getRadiusStatus($router);

        return response()->json($status);
    }
}
