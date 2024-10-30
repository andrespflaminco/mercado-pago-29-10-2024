<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class productos_variaciones_datos extends Model
{
    use HasFactory;


        protected $fillable = [
          'wc_product_id',
          'wc_variacion_id',
          'cost',
          'product_id',
          'referencia_variacion',
          'codigo_variacion',
          'variaciones',
          'comercio_id',
          'eliminado',
          'variaciones_id',
          'imagen',
          'precio_interno',
          'porcentaje_regla_precio_interno',
          'wc_push'
        ];
}
