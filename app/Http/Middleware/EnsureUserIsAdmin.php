<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->hasRole(['super_admin', 'admin', 'support_agent'])) {
            abort(403, 'You do not have access to the admin panel.');
        }

        return $next($request);
    }
}
