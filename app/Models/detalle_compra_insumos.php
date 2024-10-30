<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class detalle_compra_insumos extends Model
{
    use HasFactory;

    protected $fillable = ['comercio_id','precio','cantidad','producto_id', 'compra_id','alicuota_iva','iva','nombre','barcode','eliminado'];
}
