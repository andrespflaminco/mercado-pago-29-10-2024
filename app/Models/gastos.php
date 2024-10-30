<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gastos extends Model
{
    use HasFactory;

    protected $fillable = ['forma_pago','nombre','categoria','monto','cuenta','comercio_id','etiquetas','etiqueta_id','created_at','eliminado',
    
    
        // nuevos 23-8-2024
    'proveedor_id','alicuota_iva','iva','monto_sin_iva','deuda'];

    public function cuenta()
    {
      return $this->belongsTo(bancos::class);
    }
}
