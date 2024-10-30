<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class movimiento_stocks_detalles extends Model
{
    use HasFactory;

      protected $fillable = ['product_id','product_barcode','product_name','costo','cantidad','total','movimiento_id','referencia_variacion'];
}
