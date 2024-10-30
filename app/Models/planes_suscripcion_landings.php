<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class planes_suscripcion_landings extends Model
{
    use HasFactory;
    
    protected $fillable = ['nombre','url','eliminado','url_registro'];
}
