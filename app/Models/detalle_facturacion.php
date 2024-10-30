<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class detalle_facturacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'comercio_id',
        'cliente_id',
        'price',
        'quantity',
        'recargo',
        'descuento',
        'iva',
        'iva_total',
        'product_id',
        'product_barcode',
        'referencia_variacion',
        'product_name',
        'relacion_precio_iva',
        'factura_id',
        'nro_factura',
        'sale_id',
        'id_promo',
        'nombre_promo',
        'cantidad_promo',
        'descuento_promo'
        ];

}
