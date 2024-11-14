<?php

namespace App\Services;

use App\Models\SuscripcionControl;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class SuscripcionControlService
{

    public function insert($data)
    {
        Log::info('SuscripcionControlService - insert - ');
        try {
            $suscripcionControl = SuscripcionControl::create($data);
            return $suscripcionControl;
        } catch (Exception $e) {
            Log::info('SuscripcionControlService - insert - ' . $e->getMessage());
        }

        return null;
    }

   

   
}
