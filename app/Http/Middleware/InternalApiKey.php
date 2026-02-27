<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InternalApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $headerKey = $request->header('X-API-KEY');

        $allowedKeys = explode(',', env('INTERNAL_API_KEYS'));

        if (!$headerKey || !in_array($headerKey, $allowedKeys)) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        return $next($request);
    }
}
