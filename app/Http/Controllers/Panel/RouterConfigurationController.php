<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\MikrotikRouter;
use App\Services\RouterConfigurationService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class RouterConfigurationController extends Controller
{
    public function __construct(
        private RouterConfigurationService $configurationService
    ) {}

    /**
     * Display router configuration interface.
     *
     * Note: UI views for router configuration are not yet implemented (Phase 6).
     */
    public function index(): View
    {
        abort(501, 'Router configuration UI is not implemented yet.');
    }

    /**
     * Show router configuration details.
     *
     * Note: UI views for router configuration are not yet implemented (Phase 6).
     */
    public function show(int $routerId): View
    {
        abort(501, 'Router configuration UI is not implemented yet.');
    }

    /**
     * Configure RADIUS on router.
     */
    public function configureRadius(int $routerId): JsonResponse
    {
        $router = MikrotikRouter::where('tenant_id', getCurrentTenantId())
            ->findOrFail($routerId);

        $result = $this->configurationService->configureRadius($router);

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Configure PPP settings on router.
     */
    public function configurePpp(int $routerId): JsonResponse
    {
        $router = MikrotikRouter::where('tenant_id', getCurrentTenantId())
            ->findOrFail($routerId);

        $result = $this->configurationService->configurePpp($router);

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Configure firewall on router.
     */
    public function configureFirewall(int $routerId): JsonResponse
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
