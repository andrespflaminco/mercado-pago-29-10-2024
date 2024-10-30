<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class presupuestos extends Model
{
    use HasFactory;

      protected $fillable = ['estado','comercio_id','cliente_id','total','descuento','recargo','metodo_pago','tipo_comprobante','subtotal','iva','items','deuda','observaciones', 'tipo_factura','numero_factura','vigencia'];
}
