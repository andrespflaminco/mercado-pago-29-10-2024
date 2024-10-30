<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = RouteServiceProvider::HOME;

    // Sobrescribe el método reset
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                function ($attribute, $value, $fail) use ($request) {
                    $user = User::where('email', $request->email)->first();

                    if ($user && Hash::check($value, $user->password)) {
                        throw ValidationException::withMessages([
                            'password' => ['La nueva contraseña debe ser diferente de la contraseña anterior.'],
                        ]);
                    }
                },
            ],
        ]);

        // Restablecer la contraseña aquí...

        return redirect($this->redirectPath());
    }
}
