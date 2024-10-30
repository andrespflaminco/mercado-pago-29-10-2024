<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cobro_rapido extends Model
{
    use HasFactory;


  protected $fillable = ['nota_credito','subtotal','total','items','cash','change','canal_venta','status','comercio_id','metodo_pago','user_id','cliente_id','observaciones','nota_interna','deuda','caja','cae','vto_cae','nro_factura','recargo','tipo_comprobante','iva'];
}
