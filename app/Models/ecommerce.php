<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ecommerce extends Model
{
    use HasFactory;

      protected $fillable = ['color','background_color','registro','comercio_id','slug','efectivo_habilitado','mp_habilitado','transferencia_habilitado','mp_key','mp_token','banco_id','mensaje_efectivo','mensaje_transferencia','mensaje_mp','envio_habilitado','retiro_habilitado','tipo','comunicacion'];
}
