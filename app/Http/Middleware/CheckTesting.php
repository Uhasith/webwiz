<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTesting
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!env("TEST_APIS")) {
            return response()->json(['error' => 'Test APIs are currently disabled.'],Response::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}
