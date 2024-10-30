<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class configuracion_cajas extends Model
{
    use HasFactory;

    protected $fillable = ['configuracion_caja','comercio_id'];
}
