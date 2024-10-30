<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class datos_facturacion extends Model
{
    use HasFactory;

      protected $fillable = ['habilitado_afip','provincia','razon_social','fecha_inicio_actividades','relacion_precio_iva','cuit','localidad','condicion_iva','comercio_id','iibb','domicilio_fiscal','iva_defecto','pto_venta',
      
      'eliminado','predeterminado'
      ];
}
