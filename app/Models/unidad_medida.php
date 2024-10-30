<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class unidad_medida extends Model
{
    use HasFactory;

    protected $fillable = ['tipo_unidad_medida','nombre'];
}
