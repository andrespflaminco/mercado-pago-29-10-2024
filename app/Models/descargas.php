<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class descargas extends Model
{
    use HasFactory;
    
    
      protected $fillable = ['nombre','user_id','tipo','datos_filtros','estado','comercio_id'];
}
