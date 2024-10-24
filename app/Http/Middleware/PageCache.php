<?php

namespace App\Http\Middleware;

use Closure;

class PageCache
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->header('Cache-Control', 'max-age='.env('PAGE_CACHE_AGE',300));
        return $response;
    }
}
