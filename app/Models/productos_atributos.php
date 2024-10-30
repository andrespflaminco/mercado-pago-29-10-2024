<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class productos_atributos extends Model
{
    use HasFactory;

    protected $fillable = ['atributo_id','producto_id','eliminado'];
}
