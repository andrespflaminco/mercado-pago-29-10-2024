<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class recordatorios_comentarios extends Model
{
    use HasFactory;

      protected $fillable = ['recordatorio_id','comentario','comercio_id','user_id'];


}
