<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHotspotAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authData = session('hotspot_auth');

        if (!$authData || !isset($authData['user_id'], $authData['session_id'])) {
            return redirect()
                ->route('hotspot.login')
                ->withErrors(['error' => 'Please login to continue.']);
        }

        return $next($request);
    }
}
