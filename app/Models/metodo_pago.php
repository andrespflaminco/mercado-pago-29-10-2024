<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class metodo_pago extends Model
{
    use HasFactory;

    protected $fillable = ['comercio_id','nombre','recargo','cuenta','categoria','muestra_sucursales','creador_id','acreditacion_inmediata'];

}
