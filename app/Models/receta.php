<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class receta extends Model
{
    use HasFactory;

    protected $fillable = ['relacion_cantidades','insumo_id','product_id','nombre','cantidad','rinde','tipo_unidad_medida','unidad_medida','eliminado','relacion_medida','costo_unitario','referencia_variacion'];
    
    
    	public function setCantidadAttribute($value)
	{
        //$this->attributes['price'] = str_replace(',', '.', $value);
		$this->attributes['cantidad'] =str_replace(',', '.', preg_replace( '/,/', '', $value, preg_match_all( '/,/', $value) - 1));

	}
}
