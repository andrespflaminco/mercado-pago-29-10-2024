<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class recetas_costos extends Model
{
    use HasFactory;
    
    
      protected $fillable = ['product_id','referencia_variacion','lista_id','costo','comercio_id'];
}
