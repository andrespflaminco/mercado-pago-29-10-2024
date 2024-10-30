<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class proveedores extends Model
{
    use HasFactory;

    protected $fillable = ['cuit','id_proveedor','nombre','direccion','localidad','telefono','mail','comercio_id','creador_id','provincia','eliminado','altura','piso','depto','codigo_postal',
    'plazo_cuenta_corriente',
    'saldo_inicial_cuenta_corriente',
    'fecha_inicial_cuenta_corriente'
    ];

}
