<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    protected $fillable = ['precio_original','alicuota_iva','iva_total','relacion_precio_iva','product_name','product_barcode','cost','price','quantity','product_id','referencia_variacion','sale_id','canal_venta','comercio_id','seccionalmacen_id','metodo_pago','estado','cliente_id','comentario','eliminado','caja','created_at','recargo','descuento','iva','stock_de_sucursal_id',
    
    // a agregar en SaleDetail por promos
    'descuento_promo','cantidad_promo','nombre_promo','id_promo'
    ];

}
