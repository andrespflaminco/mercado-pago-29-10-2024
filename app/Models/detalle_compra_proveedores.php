<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class detalle_compra_proveedores extends Model
{
    use HasFactory;

    protected $fillable = ['sale_detail_casa_central','estado','comercio_id','precio','cantidad','producto_id', 'descuento','porcentaje_descuento','referencia_variacion' ,'compra_id','alicuota_iva','iva','nombre','barcode','eliminado',
        // estos son las columnas a agregar en la BD de detalle_compra_proveedores
    'actualizacion','actualizacion_real','precio_final','precio_final_real'
    ];
}
