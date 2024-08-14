<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckForValidToken
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            return response()->json([
                'message' => 'Invalid or expired token.',
                'status' => 'error'
            ], 401);
        }

        return $next($request);
    }
}