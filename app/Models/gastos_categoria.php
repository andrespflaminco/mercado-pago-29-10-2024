<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gastos_categoria extends Model
{
    use HasFactory;

    protected $fillable = ['nombre','comercio_id','eliminado'];
}
