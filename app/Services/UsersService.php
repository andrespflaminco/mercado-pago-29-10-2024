<?php

namespace App\Services;

use App\Models\Suscripcion;
use App\Models\User;
use AziendeGlobal\LaravelMercadoPago\MP;
use Exception;
use Illuminate\Support\Facades\Log;

class UsersService
{
    public function cancelUserSuscripcion($user_id)
    {
        $user = User::where('id', $user_id)->first();

        if ($user) {
            $user->confirmed = 0;
            $user->confirmed_at = null;

            $user->save();
        }
        return $user;
    }
}
