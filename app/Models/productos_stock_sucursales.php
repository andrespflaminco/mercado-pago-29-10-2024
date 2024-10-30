<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class productos_stock_sucursales extends Model
{
    use HasFactory;

    protected $fillable = ['almacen_id','stock','stock_real','sucursal_id','product_id','comercio_id','referencia_variacion','eliminado'];
    
    	public function setStockAttribute($value)
	{
        //$this->attributes['price'] = str_replace(',', '.', $value);
		$this->attributes['stock'] =str_replace(',', '.', preg_replace( '/,/', '', $value, preg_match_all( '/,/', $value) - 1));
		
		$this->attributes['stock_real'] =str_replace(',', '.', preg_replace( '/,/', '', $value, preg_match_all( '/,/', $value) - 1));

	}
}
