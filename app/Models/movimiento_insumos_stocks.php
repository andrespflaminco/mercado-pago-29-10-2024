<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class movimiento_insumos_stocks extends Model
{
    use HasFactory;


  protected $fillable = ['nro_movimiento','sucursal_origen','sucursal_destino','user_id','total','items'];
}
