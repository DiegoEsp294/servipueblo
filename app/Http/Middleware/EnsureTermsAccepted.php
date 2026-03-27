<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTermsAccepted
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && !$user->hasAcceptedTerms()) {
            // Evitar loop de redirección
            if (!$request->routeIs('terms.*')) {
                return redirect()->route('terms.show');
            }
        }

        return $next($request);
    }
}
