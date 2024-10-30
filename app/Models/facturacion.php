<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class facturacion extends Model
{
    use HasFactory;

    protected $fillable = [
                    'cae',
                    'vto_cae',
                    'nro_factura',
                    'sale_id',
                    'subtotal',
                    'iva',
                    'total',
                    'comercio_id',
                    'casa_central_id',
                    'cuit_vendedor',
                    'cuit_comprador',
                    'cliente_id',
                    'alicuota_iva',
                    'tipo_comprobante',
                    'condicion_iva',
                    'datos_facturacion_id'
	];


}
