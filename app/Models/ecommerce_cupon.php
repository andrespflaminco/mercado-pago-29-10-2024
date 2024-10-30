<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ecommerce_cupon extends Model
{
    use HasFactory;

    protected $fillable = ['comercio_id','cupon','descuento'];
}
