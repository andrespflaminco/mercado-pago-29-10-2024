<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class detalle_deducciones extends Model
{
    use HasFactory;

    protected $fillable = ['deduccion_id','comercio_id','pago_id','porcentaje','monto','concepto','eliminado'];

    
	public function getPorcentajeAttribute($value)
	{
		//comma por punto
		//return str_replace('.', ',', $value);
		// punto por coma
		return str_replace(',', '.', $value);
	}
	public function setPorcentajeAttribute($value)
	{
        //$this->attributes['price'] = str_replace(',', '.', $value);
		$this->attributes['porcentaje'] =str_replace(',', '.', preg_replace( '/,/', '', $value, preg_match_all( '/,/', $value) - 1));

	}
	
	
	public function getMontoAttribute($value)
	{
		//comma por punto
		//return str_replace('.', ',', $value);
		// punto por coma
		return str_replace(',', '.', $value);
	}
	public function setMontoAttribute($value)
	{
        //$this->attributes['price'] = str_replace(',', '.', $value);
		$this->attributes['monto'] =str_replace(',', '.', preg_replace( '/,/', '', $value, preg_match_all( '/,/', $value) - 1));

	}
    
}
