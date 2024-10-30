<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['nro_venta','idVenta','subtotal','total','items','relacion_precio_iva','alicuota_iva','cash','recordatorio','change','canal_venta','status','comercio_id','metodo_pago','user_id','cliente_id','observaciones','fecha_entrega','hoja_ruta','estado_pago','nota_interna','deuda','caja','cae','vto_cae','nro_factura','created_at','recargo','descuento','tipo_comprobante','iva','wc_order_id',
    
    // a agregar en Sale por promos
    'descuento_promo','alicuota_descuento','datos_facturacion_id',
    
    'sucursal_retiro','codigo_retiro'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
