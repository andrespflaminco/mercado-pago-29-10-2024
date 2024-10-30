<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class actualizacion_precios extends Model
{
    use HasFactory;

        protected $fillable = ['comercio_id','user_id','product_id','precio_nuevo','precio_viejo','referencia_variacion','lista_id','porcentaje_actualizacion'];
}
