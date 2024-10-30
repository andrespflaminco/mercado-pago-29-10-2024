<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	use HasFactory;

	protected $fillable = ['wc_image_url','marca_id','wc_image','etiquetas','name','precio_interno','barcode','cost','price','stock','alerts','image','category_id','comercio_id','seccionalmacen_id','stock_descubierto','inv_ideal',
	'proveedor_id','cod_proveedor','eliminado','unidad_medida','wc_product_id','wc_push','mostrador_canal','ecommerce_canal','wc_canal','descripcion','tipo_producto','receta_id','relacion_precio_iva','iva','producto_tipo',
	
	
	'tipo_unidad_medida','cantidad','relacion_unidad_medida', // 5-9-2024
	
	'es_insumo',
	'regla_precio_interno','porcentaje_regla_precio_interno','subcategoria_id'
	
	
	
	
	];


	public function category()
	{
		return $this->belongsTo(Category::class);
	}
	
	public function marcas()
	{
		return $this->belongsTo(marcas::class);
	}

	public function ventas()
	{
		return $this->hasMany(SaleDetail::class);
	}




	public function getPriceAttribute($value)
	{
		//comma por punto
		//return str_replace('.', ',', $value);
		// punto por coma
		return str_replace(',', '.', $value);
	}
	public function setPriceAttribute($value)
	{
        //$this->attributes['price'] = str_replace(',', '.', $value);
		$this->attributes['price'] =str_replace(',', '.', preg_replace( '/,/', '', $value, preg_match_all( '/,/', $value) - 1));

	}

		public function setCostAttribute($value)
	{
		$this->attributes['cost'] =str_replace(',', '.', preg_replace( '/,/', '', $value, preg_match_all( '/,/', $value) - 1));

	}
	
	public function setCantidadAttribute($value)
	{
		$this->attributes['cantidad'] =str_replace(',', '.', preg_replace( '/,/', '', $value, preg_match_all( '/,/', $value) - 1));

	}


}
