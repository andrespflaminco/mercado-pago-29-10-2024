<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class compras_proveedores extends Model
{
    use HasFactory;

    protected $fillable = ['recargos','nro_compra','created_at','alicuota_iva','comercio_id','descuento','porcentaje_descuento','proveedor_id','total','subtotal','iva','items','deuda','observaciones', 'tipo_factura','numero_factura','eliminado','status','sale_casa_central',
        // estos son las columnas a agregar en la BD de compras_proveedores
    'actualizacion'
    ];
}
