<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lista_precios_muestra_sucursales extends Model
{
    use HasFactory;

        protected $fillable = ['lista_id','sucursal_id','muestra'];

    
}
