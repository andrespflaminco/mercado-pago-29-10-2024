<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class movimiento_stocks extends Model
{
    use HasFactory;


  protected $fillable = ['sucursal_origen','sucursal_destino','user_id','total','items','casa_central_id'];
}
