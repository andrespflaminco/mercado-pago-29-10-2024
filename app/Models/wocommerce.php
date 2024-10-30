<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class wocommerce extends Model
{
    use HasFactory;

        protected $fillable = ['cs','ck','url','user','pass','comercio_id','last_order_id','last_sinc_productos'];
}
