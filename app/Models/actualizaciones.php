<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class actualizaciones extends Model
{
    use HasFactory;
    
    protected $fillable = ['relacion_detail_id','porcentaje_saldo','valor_viejo_real','valor_nuevo_real','relacion_id','product_id','referencia_variacion','comercio_id','valor_viejo','valor_nuevo','porcentaje_actualizacion','origen','monto_total','saldo','porcentaje_producto'];
}
