<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class movimiento_dinero_cuentas_detalle extends Model
{
    use HasFactory;
    
    protected $fillable = ['banco_id','eliminado','monto','movimiento_dinero_cuenta_id','comercio_id','tipo','estado_pago'];
}
