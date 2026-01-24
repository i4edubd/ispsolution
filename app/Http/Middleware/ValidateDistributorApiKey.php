<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateDistributorApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key');
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is required',
            ], 401);
        }
        
        // Validate API key and get distributor
        $user = \App\Models\User::where('api_key', $apiKey)
            ->where('role', 'distributor')
            ->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key',
            ], 401);
        }
        
        // Set authenticated user
        auth()->setUser($user);
        
        return $next($request);
    }
}
