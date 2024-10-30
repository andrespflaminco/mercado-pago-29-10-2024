<?php

namespace App\Services;

use App\Models\SuscripcionCobros;
use Exception;
use Illuminate\Support\Facades\Log;

class SuscripcionesCobrosService
{

    public function insert($data){
        Log::info('SuscripcionesCobrosService - insert - ');
        try{
            $suscripcionCobro = SuscripcionCobros::create($data);
            return $suscripcionCobro;
        }
        catch(Exception $e){
            Log::info('SuscripcionesCobrosService - insert - '.$e->getMessage());
        }

        return null;
    }

}