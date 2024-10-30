<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class insumos_stock_sucursales extends Model
{
    use HasFactory;

    protected $fillable = ['stock','sucursal_id','insumo_id','comercio_id','eliminado'];
    
    
    /*
    	public function setStockAttribute($value)
	{
        //$this->attributes['price'] = str_replace(',', '.', $value);
		$this->attributes['stock'] =str_replace(',', '.', preg_replace( '/,/', '', $value, preg_match_all( '/,/', $value) - 1));
		

	}
	*/
}
