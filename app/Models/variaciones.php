<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class variaciones extends Model
{
    use HasFactory;


    protected $fillable = ['nombre','comercio_id','atributo_id','eliminado'];

}
