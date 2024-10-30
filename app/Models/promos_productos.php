<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class promos_productos extends Model
{
    use HasFactory;
    
    protected $fillable = ['nombre_promo','promo_id','product_id','referencia_variacion','cantidad','porcentaje_descuento','comercio_id','activo','eliminado'];
}
