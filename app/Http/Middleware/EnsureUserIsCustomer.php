<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(403, 'You must be logged in to access the portal.');
        }
        // Check Spatie role (guard_name 'web')
        if (! $user->hasRole('customer', 'web')) {
            abort(403, 'Only customers can access the portal. Your account does not have the customer role.');
        }
        if (! $user->customer) {
            abort(403, 'No customer profile linked to your account. Please contact support.');
        }

        return $next($request);
    }
}
