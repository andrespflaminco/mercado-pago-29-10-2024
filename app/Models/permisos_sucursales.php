<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class permisos_sucursales extends Model
{
    use HasFactory;
    
    protected $fillable = ['comercio_id','sucursal_id','permiso_id','status'];
}
