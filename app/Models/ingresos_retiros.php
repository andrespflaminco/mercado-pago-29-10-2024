<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ingresos_retiros extends Model
{
    use HasFactory;

    protected $fillable = ['tipo','monto','comercio_id','categoria','banco_id','eliminado','descripcion'];
}
