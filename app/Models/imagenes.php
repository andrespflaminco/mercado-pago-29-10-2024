<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class imagenes extends Model
{
    use HasFactory;

    protected $fillable = ['name','url','base64','eliminado','comercio_id'];
}
