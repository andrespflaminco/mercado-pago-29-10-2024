<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class importaciones extends Model
{
    use HasFactory;


      protected $fillable = ['nombre','user_id','tipo','datos_filtros','estado','comercio_id','proceso','proceso_validacion','errores','terminado','errores_bug','locked'];
}
