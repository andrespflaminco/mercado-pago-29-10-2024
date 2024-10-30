<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetailsInsumos extends Model
{
    use HasFactory;

    protected $table = 'sale_details_insumos';

    protected $fillable = [
        'precio_original',
        'price',
        'recargo',
        'descuento',
        'quantity',
        'metodo_pago',
        'product_id',
        'referencia_variacion',
        'relacion_precio_iva',
        'product_name',
        'iva',
        'iva_total',
        'cost',
        'product_barcode',
        'seccionalmacen_id',
        'comercio_id',
        'comentario',
        'id_promo',
        'nombre_promo',
        'cantidad_promo',
        'descuento_promo',
        'tipo_unidad_medida',
        'cantidad_unidad_medida',
        'estado',
        'sale_id',
        'stock_de_sucursal_id',
        'caja',
        'canal_venta',
        'cliente_id',
        'eliminado'
    ];


}
