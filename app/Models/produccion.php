<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class produccion extends Model
{
    use HasFactory;


  protected $fillable = ['comercio_id','user_id','produccion_id','inicio_produccion','estado','total','items','observaciones'];
}
