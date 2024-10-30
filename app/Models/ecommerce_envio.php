<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ecommerce_envio extends Model
{
    use HasFactory;

    protected $fillable = ['metodo_entrega','comercio_id','telefono','sale_id','nombre_destinatario','dni','direccion','depto','ciudad','provincia','pais','codigo_postal'];
}
