<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class configuracion_codigos extends Model
{
    use HasFactory;
    
    protected $fillable = ['tipo_codigo','comercio_id','prefijo_pesable','digitos_prefijo','digitos_codigo','digitos_peso','sigla_variacion'];
}
