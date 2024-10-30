<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class historico_stock extends Model
{
    use HasFactory;

    protected $fillable = ['tipo_movimiento','sale_id','producto_id','cantidad_movimiento','stock','usuario_id','comercio_id','referencia_variacion'];
}
