<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bancos extends Model
{
    use HasFactory;

    protected $fillable = ['nombre','CBU','cuit','creador_id','comercio_id','tipo','saldo_inicial','muestra_sucursales'];
}
