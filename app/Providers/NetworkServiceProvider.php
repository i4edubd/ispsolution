<?php

namespace App\Providers;

use App\Contracts\IpamServiceInterface;
use App\Contracts\MikroTikServiceInterface;
use App\Contracts\RadiusServiceInterface;
use Illuminate\Support\ServiceProvider;

class NetworkServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register IPAM service
        // TODO: Uncomment when IpamService is implemented
        // $this->app->singleton(IpamServiceInterface::class, function ($app) {
        //     return new \App\Services\IpamService();
        // });

        // Register RADIUS service (singleton for connection reuse)
        // TODO: Uncomment when RadiusService is implemented
        // $this->app->singleton(RadiusServiceInterface::class, function ($app) {
        //     return new \App\Services\RadiusService();
        // });

        // Register MikroTik service (singleton for connection reuse)
        // TODO: Uncomment when MikroTikService is implemented
        // $this->app->singleton(MikroTikServiceInterface::class, function ($app) {
        //     return new \App\Services\MikroTikService(
        //         config('mikrotik.host'),
        //         config('mikrotik.port'),
        //         config('mikrotik.username'),
        //         config('mikrotik.password'),
        //         config('mikrotik.timeout'),
        //         config('mikrotik.retry_attempts')
        //     );
        // });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
