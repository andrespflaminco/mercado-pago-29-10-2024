<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientesMostrador extends Model
{
    use HasFactory;


  	protected $fillable = ['creador_id','pais','codigo_postal','depto','piso','altura','codigo_postal','eliminado','id_cliente','nombre','email','sucursal_id','lista_precio','telefono','observaciones','localidad','barrio','provincia','direccion','dni','comercio_id','status','image','tipo_comprobante','condicion_iva','wc_customer_id','last_sale',
  	// 12-3-2024
  	'recontacto','plazo_cuenta_corriente','saldo_inicial_cuenta_corriente','fecha_inicial_cuenta_corriente','monto_maximo_cuenta_corriente'
  	];


    public function getImagenAttribute()
  	{
  		if($this->image != null)
  			return (file_exists('storage/products/' . $this->image) ? $this->image : 'noimg.jpg');
  		else
  			return 'noimg.jpg';

  	}
}
