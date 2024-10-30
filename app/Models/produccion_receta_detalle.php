<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class produccion_receta_detalle extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','insumo_id','cantidad','cost','eliminado'];
}
