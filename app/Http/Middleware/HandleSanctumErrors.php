<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HandleSanctumErrors
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the request has a bearer token
        if (!$request->bearerToken()) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'No token provided.'
            ], 401);
        }

        try {
            // Attempt to authenticate the user
            if (!Auth::guard('sanctum')->check()) {
                throw new AuthenticationException('Unauthenticated.');
            }

            return $next($request);
        } catch (AuthenticationException $e) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'The provided token is invalid or has expired.'
            ], 401);
        }
    }
}