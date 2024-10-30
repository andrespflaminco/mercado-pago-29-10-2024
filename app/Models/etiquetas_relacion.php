<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class etiquetas_relacion extends Model
{
    use HasFactory;
    
    protected $fillable = ['etiqueta_id','nombre_etiqueta','estado','comercio_id','relacion_id','origen'];
}
