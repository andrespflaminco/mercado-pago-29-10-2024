<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cajas extends Model
{
    use HasFactory;

      protected $fillable = ['monto_inicial','monto_final','comercio_id','estado','fecha_cierre','fecha_inicio','user_id','nro_caja','faltante_caja','last_caja','eliminado' ];

    
    public function setMontoInicialAttribute($value)
	{
		$this->attributes['monto_inicial'] =str_replace(',', '.', preg_replace( '/,/', '', $value, preg_match_all( '/,/', $value) - 1));

	}
	
	public function setMontoFinalAttribute($value)
	{
		$this->attributes['monto_final'] =str_replace(',', '.', preg_replace( '/,/', '', $value, preg_match_all( '/,/', $value) - 1));

	}
}
