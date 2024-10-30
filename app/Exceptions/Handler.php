<?php

namespace App\Exceptions;
use Illuminate\Session\TokenMismatchException;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        
        $this->renderable(function (TokenMismatchException $e, $request) {
            return redirect()->route('login')->with('message', 'La sesión ha expirado. Por favor, inicia sesión de nuevo.'); // Cambia 'nombre_de_la_ruta' por la ruta a la que quieres redirigir
        });
        
        
        $this->reportable(function (Throwable $e) {
            //
        });
    }

}
