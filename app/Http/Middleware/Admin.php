<?php

namespace App\Http\Middleware;

use App\Exceptions\UnAuthorizedException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Admin
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
        if(Session::get('CURRENT_USER_ID')){
           return $next($request);
        }
        throw new UnauthorizedException(UnauthorizedException::UNAUTHORIZED_REQUEST,401);
    }
}
