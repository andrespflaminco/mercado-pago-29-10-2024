<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class produccion_detalles_insumos extends Model
{
    use HasFactory;

    protected $fillable = [      'produccion_detalles_id',
      'insumo_id',
      'insumo_codigo',
      'insumo_nombre',
      'tipo_unidad_medida',
       // Receta
      'cantidad_consumida',
      'unidad_medida_consumida',
       // Insumo
       'cantidad_consumida_envase',
       'unidad_medida_envase' ,
       // Costos unitario de lo consumido
       'costo_unitario_consumido' ,
       'costo_unitario_consumido_envase' ,
       // Costos unitario de lo consumido
       'costo_total'
       ];


}
