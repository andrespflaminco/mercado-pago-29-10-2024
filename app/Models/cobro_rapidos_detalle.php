<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cobro_rapidos_detalle extends Model
{
    use HasFactory;


  protected $fillable = ['concepto','cobro_rapido_id','monto','alicuota_iva','iva'];
}
