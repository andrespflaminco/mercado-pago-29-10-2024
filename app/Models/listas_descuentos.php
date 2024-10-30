<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class listas_descuentos extends Model
{
    use HasFactory;

    protected $fillable = ['nro_descuento','comercio_id'];
}
