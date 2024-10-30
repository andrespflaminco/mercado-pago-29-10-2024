<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class presupuestos_detalle extends Model
{
    use HasFactory;

    protected $fillable = ['eliminado','comercio_id','precio','referencia_variacion','cantidad','producto_id','descuento','recargo', 'presupuesto_id','alicuota_iva','iva','nombre','barcode','eliminado'];
}
