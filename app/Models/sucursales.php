<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sucursales extends Model
{
    use HasFactory;

      protected $fillable = ['casa_central_id','eliminado','sucursal_id','precio_interno','tipo','solo_ver_clientes_propios'];
}
