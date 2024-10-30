<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class configuracion_ctas_ctes extends Model
{
    use HasFactory;

    protected $fillable = ['valor','sucursales_agregan_pago','comercio_id'];
}
