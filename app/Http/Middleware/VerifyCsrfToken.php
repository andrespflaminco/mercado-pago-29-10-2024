<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;
use Closure;
class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
   protected $except = [
        'livewire/*',
    ];

    public function handle($request, Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            // Si la sesión ha expirado, regeneramos el token y redirigimos
            if ($request->ajax()) {
                return response()->json(['error' => 'Token CSRF ha expirado. Por favor, actualice la página.'], 419);
            }

            return redirect()->back()->with('error', 'Tu sesión ha expirado. Por favor, intenta de nuevo.');
        }
    }
}