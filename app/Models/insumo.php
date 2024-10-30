<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class insumo extends Model
{
    use HasFactory;

    protected $fillable = ['name','barcode','cost','stock','stock_real','alerts','comercio_id','proveedor_id','cod_proveedor','eliminado','tipo_unidad_medida','unidad_medida','cantidad','relacion_unidad_medida'];

    
    	public function setCostAttribute($value)
	{
        //$this->attributes['price'] = str_replace(',', '.', $value);
		$this->attributes['cost'] =str_replace(',', '.', preg_replace( '/,/', '', $value, preg_match_all( '/,/', $value) - 1));

	}
	
	public function setCantidadAttribute($value)
	{
        //$this->attributes['price'] = str_replace(',', '.', $value);
		$this->attributes['cantidad'] =str_replace(',', '.', preg_replace( '/,/', '', $value, preg_match_all( '/,/', $value) - 1));

	}
}
