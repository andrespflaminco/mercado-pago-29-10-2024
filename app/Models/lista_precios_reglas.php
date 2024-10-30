<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lista_precios_reglas extends Model
{
    use HasFactory;

    protected $fillable = [
        'lista_id',
        'regla',
        'comercio_id',
        'porcentaje_defecto'
        ];

}
