<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class configuracion_lista_precios extends Model
{
    use HasFactory;
    
    protected $fillable = ['forma_mostrar','casa_central_id'];
}
