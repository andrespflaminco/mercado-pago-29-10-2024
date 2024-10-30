<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class beneficios extends Model
{
    use HasFactory;

    protected $fillable = ['id_padre','ingresos','cambio','recargo','descuento','gastos','eliminado','comercio_id'];
}
