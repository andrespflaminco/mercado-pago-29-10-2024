<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class unidad_medida_relacion extends Model
{
    use HasFactory;

    protected $fillable = ['unidad_1','unidad_2','relacion'];
}
