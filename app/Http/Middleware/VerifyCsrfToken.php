<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;
use Closure;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        //
    ];

    public function handle($request, Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            // Regenerar sesión limpia y redirigir al login
            $request->session()->regenerate();
            return redirect()->route('login')
                ->with('error', 'Tu sesión expiró. Por favor iniciá sesión nuevamente.');
        }
    }
}
