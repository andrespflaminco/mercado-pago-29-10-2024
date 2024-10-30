<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bancos_muestra_sucursales extends Model
{
    use HasFactory;


        protected $fillable = ['banco_id','sucursal_id','muestra'];
}
