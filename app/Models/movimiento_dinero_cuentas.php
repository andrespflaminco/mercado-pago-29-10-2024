<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class movimiento_dinero_cuentas extends Model
{
    use HasFactory;
    
    protected $fillable = ['banco_origen_id','banco_destino_id','comercio_id','eliminado','monto','nro_comprobante','url_comprobante'];
}
