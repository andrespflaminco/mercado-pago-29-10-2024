<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class productos_descuentos extends Model
{
    use HasFactory;

    protected $fillable = ['lista_descuento_id','referencia_variacion','product_id','descuento','nro_descuento'];
}
