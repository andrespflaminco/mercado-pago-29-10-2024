<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class configuracion_compras extends Model
{
    use HasFactory;
    
    protected $fillable = ['costo_igual_precio','actualizar_costo','actualizar_precio_interno','casa_central_id','actualizar_precio_base'];
}
