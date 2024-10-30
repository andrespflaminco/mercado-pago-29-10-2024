<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class saldos_iniciales extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'tipo',
        'concepto',
        'referencia_id',
        'comercio_id',
        'eliminado',
        'monto',
        'metodo_pago',
        'fecha',
        'caja_id',
        'estado_pago',
        'sucursal_id'
        ];
}
