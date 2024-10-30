<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hoja_ruta extends Model
{
    use HasFactory;

    protected $fillable = ['id','nro_hoja','fecha','turno','comercio_id','id_pedido','nombre','tipo','observaciones'];
}
