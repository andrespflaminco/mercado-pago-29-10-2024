<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categorias_monotributo extends Model
{
    use HasFactory;
    
    protected $fillable = ['pais','categoria','minimo','maximo'];
}
