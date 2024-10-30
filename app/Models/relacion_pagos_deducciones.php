<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class relacion_pagos_deducciones extends Model
{
    use HasFactory;
    
        protected $fillable = ['pago_id_deduccion','pago_id','venta_id','gasto_id','comercio_id'];

}
