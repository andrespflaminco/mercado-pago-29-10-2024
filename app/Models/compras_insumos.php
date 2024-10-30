<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class compras_insumos extends Model
{
    use HasFactory;

    protected $fillable = ['nro_compra','comercio_id','proveedor_id','total','subtotal','iva','items','deuda','observaciones', 'tipo_factura','numero_factura','eliminado','alicuota_iva'];
}
