<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class nota_credito extends Model
{
    use HasFactory;

      protected $fillable = [
      'cae', 
      'vto_cae' ,
      'nro_nota_credito',
      'nro_factura',
      'venta_id',
      'comercio_id',
      'factura_id'
    ];
}
