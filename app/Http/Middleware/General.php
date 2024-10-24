<?php

namespace App\Http\Middleware;

use App\Exceptions\UnAuthorizedException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class General
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws UnAuthorizedException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $userAgent = $request->header('User-Agent');
        Log::info($userAgent);

        if ($userAgent && (str_contains($userAgent, 'Mozilla') || str_contains($userAgent, 'Chrome'))) {
            return $next($request);
        }

        throw new UnauthorizedException(UnauthorizedException::UNAUTHORIZED_REQUEST,401);

    }
}
