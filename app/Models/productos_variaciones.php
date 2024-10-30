<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class productos_variaciones extends Model
{
    use HasFactory;


    protected $fillable = [
      'atributo_id',
      'variacion_id',
      'comercio_id',
      'producto_id',
      'referencia_id',
      'eliminado'
    ];

}
