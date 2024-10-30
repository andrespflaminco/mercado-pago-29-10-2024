<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class descargas_etiquetas extends Model
{
    use HasFactory;
    
    
      protected $fillable = [
	 'user_id',
         'comercio_id',
         'descargas_id',
         'producto_id',
         'referencia_variacion',
         'cantidad'
	];
}
