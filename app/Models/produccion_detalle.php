<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class produccion_detalle extends Model
{
    use HasFactory;

      protected $fillable = ['comercio_id','costo','cantidad','produccion_id','producto_id','nombre','barcode','comentario','estado','referencia_variacion','sale_details_id'];
}
