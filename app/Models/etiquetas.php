<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class etiquetas extends Model
{
    use HasFactory;

    	protected $fillable = ['nombre','eliminado','comercio_id','origen','color'];
}
