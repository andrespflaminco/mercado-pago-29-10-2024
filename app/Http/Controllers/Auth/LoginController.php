<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */


    protected $redirectTo = RouteServiceProvider::HOME;



    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    
    // Personaliza el método para manejar una respuesta de inicio de sesión fallida
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.failed')];

        // Personaliza el mensaje de error aquí, si lo deseas
        //$errors = [$this->username() => 'Mensaje de error personalizado'];
        //$errors = ['password' => 'La contraseña es incorrecta.'];

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->route('login')
            ->withErrors($errors);
    }
}



