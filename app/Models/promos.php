<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class promos extends Model
{
    use HasFactory;
    
    protected $fillable = ['limitar_cantidad','tipo_promo','precio_promo','nombre_promo','productos','cantidad','porcentaje_descuento','comercio_id','activo','vigencia_desde','vigencia_hasta','limitar_vigencia','eliminado'];
}
