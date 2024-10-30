<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInsumos extends Model
{
    use HasFactory;
    
    protected $table = 'sales_insumos';

    protected $fillable = [
        'datos_facturacion_id',
        'nro_venta',
        'subtotal',
        'total',
        'recargo',
        'descuento',
        'alicuota_descuento',
        'descuento_promo',
        'items',
        'tipo_comprobante',
        'cash',
        'change',
        'iva',
        'relacion_precio_iva',
        'alicuota_iva',
        'metodo_pago',
        'comercio_id',
        'cliente_id',
        'user_id',
        'observaciones',
        'canal_venta',
        'estado_pago',
        'caja',
        'deuda',
        'created_at',
        'recordatorio',
        'status',
        'nota_interna',
        'id_venta',
        'eliminado'
    ];


}
