<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectUsersIfAutenticatedMiddleware
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {

        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if ($guard === 'sanctum') {

                    return response()->noContent();
                }
            }
        }

        return $next($request);
    }
}
