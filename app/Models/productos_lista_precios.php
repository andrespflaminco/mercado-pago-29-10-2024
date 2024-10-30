<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class productos_lista_precios extends Model
{
    use HasFactory;

      protected $fillable = ['lista_id','precio_lista','comercio_id','product_id','referencia_variacion','eliminado','porcentaje_regla_precio','regla_precio'];
      
      	public function setPriceAttribute($value)
	{
	    
        //$this->attributes['price'] = str_replace(',', '.', $value);
		$this->attributes['precio_lista'] =str_replace(',', '.', preg_replace( '/,/', '', $value, preg_match_all( '/,/', $value) - 1));

	}
}
