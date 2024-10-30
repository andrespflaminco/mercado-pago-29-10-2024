<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lista_precios_sucursales extends Model
{
    use HasFactory;

    protected $fillable = ['lista_precio_id','sucursal_id','comercio_id'];
}
