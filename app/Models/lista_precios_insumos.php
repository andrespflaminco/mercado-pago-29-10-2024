<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lista_precios_insumos extends Model
{
    use HasFactory;

    protected $fillable = ['nombre','comercio_id','descripcion'];

}
