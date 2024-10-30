<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pagos_facturas extends Model
{
    use HasFactory;

    protected $fillable = ['actualizacion','pago_sucursal_id','banco_id','id','id_factura','id_ingresos_retiros','monto_ingreso_retiro','id_cobro_rapido','id_sale_casa_central','id_compra_insumos','monto_cobro_rapido','id_gasto','cambio','comercio_id','monto','monto_gasto','recargo','iva_pago','iva_recargo','eliminado','backup_monto','caja','metodo_pago','id_compra','monto_compra','created_at','tipo_pago','cliente_id','proveedor_id',
    
    'nro_comprobante','url_comprobante',
        
    //18-5-2024
    'estado_pago', 'deducciones', 
    
    'id_venta_insumos'
    ];

    public function setMontoAttribute($value)
    {
          //$this->attributes['price'] = str_replace(',', '.', $value);
      $this->attributes['monto'] =str_replace(',', '.', preg_replace( '/,/', '', $value, preg_match_all( '/,/', $value) - 1));

    }
}
