<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class configuracion_stock extends Model
{
    use HasFactory;
    
    protected $fillable = ['digitos_cantidad_unidades','digitos_cantidad_kg','comercio_id','muestra_stock_otras_sucursales','muestra_stock_casa_central'];
}
