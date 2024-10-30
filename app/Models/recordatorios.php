<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class recordatorios extends Model
{
    use HasFactory;

    protected $fillable = ['fecha','titulo','descripcion','telefono','cheque_id','sale_id','comercio_id','estado','color','tipo_contacto','contacto_id'];

}
