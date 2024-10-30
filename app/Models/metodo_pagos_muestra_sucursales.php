<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class metodo_pagos_muestra_sucursales extends Model
{
    use HasFactory;


            protected $fillable = ['metodo_id','sucursal_id','muestra'];
}
