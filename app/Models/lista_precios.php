<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lista_precios extends Model
{
    use HasFactory;

    protected $fillable = ['nombre','comercio_id','descripcion','eliminado','wc_key','tipo'];

}
