<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class historico_stock_insumo extends Model
{
    use HasFactory;

    protected $fillable = [ 'tipo_movimiento',
                          'insumo_id',
                          'produccion_detalle_id',
                          'cantidad_receta',
                          'unidad_medida_receta',
                          'cantidad_movimiento',
                          'cantidad_contenido',
                          'unidad_medida_insumo',
                          'relacion_unidad_medida',
                          'stock',
                          'comercio_id',
                          'usuario_id' ];
}
