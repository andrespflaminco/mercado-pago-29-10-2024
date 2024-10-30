<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class configuracion_impresion extends Model
{
    use HasFactory;

    protected $fillable = ['size','comercio_id','user_id','muestra_cta_cte'];
}
