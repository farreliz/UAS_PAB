<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        // Untuk demo UAS, validasi sederhana
        // Di production seharusnya validasi ke database
        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Token valid jika ada Bearer token (untuk demo)
        return $next($request);
    }
}
