<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cheques extends Model
{
    use HasFactory;

    protected $fillable = ['nro_cheque','banco','comercio_id','status','emisor','cliente_id','monto','fecha_emision','sale_id','fecha_cobro'];

}
