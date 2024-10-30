<?php
namespace App\Traits;

use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

use Carbon\Carbon;

// Modelos
use App\Models\SalesInsumos;
use App\Models\SaleDetailsInsumos;
use App\Models\compras_insumos;


use App\Models\productos_ivas;
use App\Models\User;
use App\Models\provincias;
use App\Models\paises;
use App\Models\promos;
use App\Models\promos_productos;
use App\Models\ColumnConfiguration;
use App\Models\historico_stock;
use App\Models\cajas;
use App\Models\hoja_ruta;
use App\Models\beneficios;
use App\Models\bancos;
use App\Models\nota_credito;
use App\Models\productos_variaciones_datos;
use App\Models\productos_variaciones;
use App\Models\productos_stock_sucursales;
use App\Models\productos_lista_precios;
use App\Models\variaciones;
use App\Models\detalle_compra_proveedores;
use App\Models\metodo_pago;
use App\Models\pagos_facturas;
use App\Models\datos_facturacion;
use App\Models\Sale;
use App\Models\ClientesMostrador;
use App\Models\Product;
use App\Models\sucursales;
use App\Models\lista_precios;
use App\Models\compras_proveedores;
use App\Models\wocommerce;
use App\Models\facturacion;
use App\Models\ecommerce_envio;
use App\Models\SaleDetail;


use App\Traits\DeduccionesTrait; // 30-6-2024

trait VentasTrait {

use DeduccionesTrait;

public $id_pedido,$relacion_precio_iva,$recargo,$recargo_total,$monto_ap,$total_pago;

        
public function ActualizarTotalesVenta($venta) {
        
        $sale = Sale::find($venta);
        
        $relacion_precio_iva = $sale->relacion_precio_iva;
        
        $this->details = SaleDetail::where('sale_id',$venta)->where('eliminado',0)->get();
        
               //
               $subtotal = $this->details->sum(function($item){
                  return $item->price * $item->quantity;
               });
    
              $descuento = $this->details->sum(function($item){
                  return $item->descuento;
              });
              
              $descuento_promo = $this->details->sum(function($item){
                  return $item->descuento_promo * $item->cantidad_promo;
              });
    
              $this->descuento_promo = $descuento_promo;
              $this->descuento = $descuento;
              $this->subtotal_venta_nuevo = $subtotal;
    
              $iva = $this->sumarIVASUBTOTAL($this->details);

              $subtotal_sin_recargo = $subtotal - $descuento - $descuento_promo;
              $alicuota_iva = $iva/$subtotal_sin_recargo;
            //  dd($alicuota_iva);
              
              $this->SetearRecargoIvaNuevo($venta,$alicuota_iva,$relacion_precio_iva);
              
              $recargo = $this->CalcularRecargo($venta);
              $iva_recargo = $this->CalcularIVARecargo($venta);
              
              $this->recargo = $recargo;
              $alicuota_recargo = $recargo / ($subtotal - $descuento_promo - $descuento);
              
              $iva = $iva + $iva_recargo;
              $this->iva_venta_nuevo = $iva; 
              $this->total_venta_nuevo = $subtotal + $iva + $recargo - $descuento - $descuento_promo;
    
              $this->items_venta_nuevo = $this->details->sum('quantity');
                
              $this->venta = Sale::find($venta);
              
              $array_sale = [
                'subtotal' => $this->subtotal_venta_nuevo,
                'total' => $this->total_venta_nuevo,
                'iva' =>  $this->iva_venta_nuevo,
                'descuento' => $this->descuento,
                'descuento_promo' => $this->descuento_promo,
                'recargo' => $this->recargo,
                'alicuota_iva' => $alicuota_iva
                ];        
              
              //dd($array_sale);
              
              $this->venta->update($array_sale);
                
            // Si es una venta a una sucursal
            if($this->venta->canal_venta == "Venta a sucursales") {
              
              $compra = compras_proveedores::where('sale_casa_central',$this->venta->id)->first();
              
              $this->ActualizarTotalCompra($compra->id);
              $this->ActualizarEstadoDeudaCompra($compra->id);
              
            }
            
            
        }

public function sumarIVASUBTOTAL($carro){  
        
        $sum_iva = $carro->sum(function($item) {
              
            // Diferencia precio
            $diferencia_precio = ($item->precio_original - $item->price) * $item->quantity;
    
            // Alicuota IVA
            $iva = $item->iva;
    
            // Subtotal 
            $subtotal_sin_iva = $item->price * $item->quantity;
    
            // Descuento promo
            $descuento_promo_sin_iva = $item->descuento_promo * $item->cantidad_promo;
            $diferencia_descuento_promo = $descuento_promo_sin_iva * $iva;
     
            $subtotal_sin_promo = $subtotal_sin_iva - $descuento_promo_sin_iva;
            
            // Descuento gral
            $descuento_gral = $item->descuento;
            $diferencia_descuento_gral = $descuento_gral * $iva;
     
            $subtotal_sin_promo_sin_desc_gral = $subtotal_sin_promo - $descuento_gral;
            
            if($item->relacion_precio_iva == 2) { 
                $iva_total = $diferencia_precio - $diferencia_descuento_gral - $diferencia_descuento_promo;  
            }
            
            if($item->relacion_precio_iva == 1) { 
                $iva_total = ($subtotal_sin_iva - $descuento_promo_sin_iva - $descuento_gral) * $iva; 
            }
            
            if($item->relacion_precio_iva == 0) { 
                $iva_total = 0; 
            }
            
            return $iva_total;
        });
            
        return $sum_iva ?? 0;
    }

public function SetearRecargoIvaNuevo($id_venta,$alicuota_iva,$relacion_precio_iva){

        //////////////// PAGOS //////////////
        $pagos = pagos_facturas::where('pagos_facturas.id_factura', $id_venta)
        ->where('pagos_facturas.eliminado',0)
        ->get();       

        foreach($pagos as $p){
            
            if($relacion_precio_iva == 1){
              $iva_pago_nuevo = $p->monto * $alicuota_iva;
              $iva_recargo_nuevo = $p->recargo * $alicuota_iva;
              
              $array = [
                'iva_pago' => $iva_pago_nuevo,
                'iva_recargo' => $iva_recargo_nuevo
                ];
            }
             
            if($relacion_precio_iva == 2){
            $total_pago = $p->monto + $p->iva_pago; 
            $total_recargo = $p->recargo + $p->iva_recargo; 
            
            $monto_nuevo = $total_pago / (1 + $alicuota_iva);
            $iva_pago_nuevo = $total_pago - $monto_nuevo;
            
            $recargo_nuevo = $total_recargo / (1 + $alicuota_iva);
            $iva_recargo_nuevo = $total_recargo - $recargo_nuevo;

            $array = [
                'monto' => $monto_nuevo,
                'iva_pago' => $iva_pago_nuevo,
                'recargo' => $recargo_nuevo,
                'iva_recargo' => $iva_recargo_nuevo
                ];
            }

                        
            if($relacion_precio_iva == 0){
              $array = [];
            }

                        
            $pagos_facturas = pagos_facturas::find($p->id);
            $pagos_facturas->update($array);
            
        }

}

public function CalcularRecargoInsumos($id_venta){  
       
        $venta = SalesInsumos::find($id_venta);
        
        $recargo_original = $venta->recargo;
        
        //////////////// PAGOS //////////////
        $pagos = pagos_facturas::where('pagos_facturas.id_venta_insumos', $id_venta)
        ->where('pagos_facturas.eliminado',0)
        ->get();       

           $sum_recargo = $pagos->sum(function($item){
            return $item->recargo;
        });  
   
        //dd($sum_recargo);
        $this->alicuota_iva_recargo = $venta->alicuota_iva;

        return $sum_recargo;
  }
  
public function CalcularRecargo($id_venta){  
       
        $venta = Sale::find($id_venta);
        
        $recargo_original = $venta->recargo;
        
        //////////////// PAGOS //////////////
        $pagos = pagos_facturas::where('pagos_facturas.id_factura', $id_venta)
        ->where('pagos_facturas.eliminado',0)
        ->get();       

           $sum_recargo = $pagos->sum(function($item){
            return $item->recargo;
        });  
   
        //dd($sum_recargo);
        $this->alicuota_iva_recargo = $venta->alicuota_iva;

        return $sum_recargo;
  }
  
public function CalcularIVARecargo($id_venta){  
        
        //////////////// PAGOS //////////////
        $pagos = pagos_facturas::where('pagos_facturas.id_factura', $id_venta)
        ->where('pagos_facturas.eliminado',0)
        ->get();       

           $sum_iva_recargo = $pagos->sum(function($item){
            return $item->iva_recargo;
        });  
   

        return $sum_iva_recargo;
  }

public function CalcularIVARecargoInsumos($id_venta){  
        
        //////////////// PAGOS //////////////
        $pagos = pagos_facturas::where('pagos_facturas.id_venta_insumos', $id_venta)
        ->where('pagos_facturas.eliminado',0)
        ->get();       

           $sum_iva_recargo = $pagos->sum(function($item){
            return $item->iva_recargo;
        });  
   

        return $sum_iva_recargo;
  }
  
public function ActualizarTotalCompra($compra_id) {

      $compra = compras_proveedores::find($compra_id);
      $venta = Sale::find($compra->sale_casa_central);
      
      $subtotal = $venta->subtotal;
      $total = $venta->total;
      $iva = $venta->iva;
      $items = $venta->items;
      $recargo = $venta->recargo;
      
      $compra = compras_proveedores::find($compra_id);
      
      $compra->update([
        'subtotal' => $subtotal,
        'total' => $total,
        'items' => $items,
        'recargos' => $recargo,
        'iva' => $iva
        ]);

     // dd($compra);

}

public function ActualizarEstadoDeuda($ventaId)
{
         /////////////////   ACTUALIZAR ESTADO DE PAGO   /////////////////////
    
    
              $this->data_cash = Sale::select('sales.cash','sales.created_at as fecha_factura')
              ->where('sales.id', $ventaId)
              ->get();
    
              $this->data_total = Sale::select('sales.total','sales.status','sales.recargo','sales.descuento')
              ->where('sales.id', $ventaId)
              ->first();
    
              $this->pagos2 = pagos_facturas::join('metodo_pagos as mp','mp.id','pagos_facturas.metodo_pago')
              ->select('mp.nombre as metodo_pago',
              'pagos_facturas.id',
              'pagos_facturas.iva_recargo',
              'pagos_facturas.iva_pago',
              'pagos_facturas.recargo',
              'pagos_facturas.monto',
              'pagos_facturas.created_at as fecha_pago')
              ->where('pagos_facturas.id_factura', $ventaId)
              ->where('pagos_facturas.eliminado',0)
              ->get();
    
              // dd($this->data_total);
              
              //dd($this->pagos2);
              
              // Pagos
              $suma_monto = $this->pagos2->sum('monto');
              
              // Recargos
              $recargo = $this->pagos2->sum('recargo');
              
              // iva pago
              $iva_pago = $this->pagos2->sum('iva_pago');
                            
              // IVA Recargo
              $iva_recargo = $this->pagos2->sum('iva_recargo');
              
              // total de la factura
              $total = $this->data_total->total;
              
              $sumas_pagos = $suma_monto + $recargo + $iva_pago + $iva_recargo; 
              
              if($this->data_total->status != "Cancelado") {
              $deuda = floatval($total) - $sumas_pagos;
              } else {
                  $deuda = 0;
              }
              
            $deuda_vieja = Sale::find($ventaId);
            
            $deuda_vieja->update([
            'deuda' => $deuda
            ]);
     }

public function ActualizarEstadoDeudaCompra($ventaId)
{
  /////////////////   ACTUALIZAR ESTADO DE PAGO   /////////////////////
        $compra = compras_proveedores::select('compras_proveedores.total')
       ->where('compras_proveedores.id', $ventaId)
       ->first();

       $pagos = pagos_facturas::where('pagos_facturas.id_compra', $ventaId)
       ->where('pagos_facturas.eliminado',0)
       ->get();


       $suma_pagos = $pagos->sum('monto_compra');

       $suma_compra = $compra->total;

       //dd($suma_compra,$suma_pagos);
       
       $deuda = $suma_compra - $suma_pagos;

     $this->deuda_vieja = compras_proveedores::find($ventaId);

       $this->deuda_vieja->update([
         'deuda' => $deuda
         ]);


       ///////////////////////////////////////////////////////////////////
}

public function ActualizarPago($id_pago) {

    if($this->metodo_pago_agregar_pago == 1 && $this->tipo_pago != 1) {
        $this->emit("msg-error","El metodo de pago no puede ser efectivo para un banco/plataforma");
        return;
    }
    
      $pagos = pagos_facturas::find($id_pago);

      $ventaId = $pagos->id_factura;


    $alicuota_iva = $this->GetAlicuotaIVA($this->id_pedido);    
    // seteamos los montos
    $monto_original = $this->monto_ap;
    $monto_real = $this->setMontoReal($this->monto_ap,$alicuota_iva,$this->relacion_precio_iva);

    
    //dd($monto_original,$monto_real,);
    $recargo = $this->setRecargoPagos($monto_real,$this->recargo);
    $iva_pago = $this->sumarIVAPago($monto_real,$alicuota_iva);
    
    $iva_recargo = $this->sumarIVARecargo($recargo,$alicuota_iva,$this->relacion_precio_iva);
    $monto = $this->setMontoPagos($monto_original,$monto_real,$this->recargo_total,$iva_pago,$iva_recargo,$this->relacion_precio_iva);
    
  $array = [
       'monto' => $monto_real,
       'iva_pago' => $iva_pago,
       'iva_recargo' => $iva_recargo,
       'recargo' => $recargo,
       'caja' => $this->caja,
       'nro_comprobante' => $this->nro_comprobante,
       'created_at' => $this->fecha_ap,
       'metodo_pago' => $this->metodo_pago_agregar_pago,
       'banco_id' => $this->tipo_pago,

       ];
  
 // dd($array);
  
     $pagos->update($array);

    
    if($this->comprobante != $pagos->url_comprobante)
	{
		$customFileName = uniqid() . '_.' . $this->comprobante->extension();
		$this->comprobante->storeAs('public/comprobantes', $customFileName);
		$pagos->url_comprobante = $customFileName;
		$pagos->save();
	}
	
	
      $ventas_vieja = Sale::find($ventaId);

      $rec = $this->CalcularRecargo($ventaId);
    
      // dd($rec);
      
      $ventas_vieja->update([
        'recargo' => $rec,
        'metodo_pago' => $this->metodo_pago_agregar_pago
      ]);
      
      
      $this->ActualizarTotalesVenta($ventaId);
      
      $this->ActualizarEstadoDeuda($ventaId);
      
      // si es una venta a sucursal, tenemos que actualizar el stock de la compra en la sucursal 
      
      $cliente_id = Sale::find($ventaId)->cliente_id;
      $cliente = ClientesMostrador::find($cliente_id);
      
      if($cliente->sucursal_id != 0) {
       
      $this->ActualizarPagoCompra($pagos->id);    
      
      }  
    
      //

      $this->emit('agregar-pago-hide', 'hide');

      $this->emit('pago-actualizado', 'El pago fue actualizado.');

   //   $this->RenderFactura($ventaId);

      $this->ResetPago();
      $this->estado = "display: block;";
    }
    
public function GetMetodoPagoAgregar($value){
       
      $metodo_pago_agregar =  metodo_pago::join('bancos','bancos.id','metodo_pagos.cuenta')
       ->join('metodo_pagos_muestra_sucursales','metodo_pagos_muestra_sucursales.metodo_id','metodo_pagos.id')
      ->select('metodo_pagos.*','bancos.nombre as nombre_banco')
      ->where('metodo_pagos_muestra_sucursales.sucursal_id', 'like', $this->sucursal_id)
      ->where('metodo_pagos_muestra_sucursales.muestra', 1)
      ->where('metodo_pagos.cuenta', $value)
      ->get();
      
      return $metodo_pago_agregar;
   }

public function TipoPago($value)
{

   //dd($value);
   
   if($value == '1') {

   $this->metodo_pago = $value;

   if($value == 1) {
     $this->metodo_pago_agregar_pago = 1;
     $this->recargo = 0;
     $this->recargo_total = 0;
     $this->total_pago = $this->monto_ap;
   }
    $this->MetodoPago(1);

   } else {
   	$this->metodo_pago = 'Elegir';
   	
   	$this->metodo_pago_agregar = $this->GetMetodoPagoAgregar($value);
   }
    $alicuota_iva = $this->GetAlicuotaIVA($this->id_pedido);   
    $this->relacion_precio_iva  = $this->GetRelacionPrecioIVA($this->id_pedido);
    $this->monto_real = $this->setMontoReal($this->monto_ap,$alicuota_iva,$this->relacion_precio_iva);
    }

public function GetAlicuotaIVAInsumos($ventaId){
	    $t = SalesInsumos::find($ventaId);
	    $alicuota_iva = $t->total/($t->subtotal + $t->recargo - $t->descuento - $t->descuento_promo);
        $this->alicuota_iva = $alicuota_iva - 1;
        return $this->alicuota_iva;
	}
	
public function GetAlicuotaIVA($ventaId){
	    $t = Sale::find($ventaId);
	    $alicuota_iva = $t->total/($t->subtotal + $t->recargo - $t->descuento - $t->descuento_promo);
        $this->alicuota_iva = $alicuota_iva - 1;
        return $this->alicuota_iva;
	}
	
	   
public function setMontoReal($monto,$alicuota_iva,$relacion_precio_iva){
 $valor = $monto / (1 + $alicuota_iva);
 $value = floatval($valor);
 return $value;     
 }    
 
 public function GetRelacionPrecioIVAInsumos($ventaId){
     return SalesInsumos::find($ventaId)->relacion_precio_iva;
}

 public function GetRelacionPrecioIVA($ventaId){
     return Sale::find($ventaId)->relacion_precio_iva;
}


public function MetodoPago($value)
{
  $metodo_pago = metodo_pago::join('bancos','bancos.id','metodo_pagos.cuenta')->select('metodo_pagos.*','bancos.nombre as nombre_banco')->find($this->metodo_pago_agregar_pago);

  $this->recargo = $metodo_pago->recargo/100;
  
  $this->recargo_total = $this->monto_ap * $this->recargo;
 
  $this->total_pago = $this->recargo_total + $this->monto_ap;
  
  $alicuota_iva = $this->GetAlicuotaIVA($this->id_pedido);   
  $this->monto_real = $this->setMontoReal($this->monto_ap,$alicuota_iva,$this->relacion_precio_iva);
  
//  $this->RenderFactura($this->id_pedido);
  $this->RecalcularDeduccionesMetodoPago($this->id_pago,$metodo_pago->id,$this->total_pago);
  }

public function MontoPagoEditarPago($value)
{
    // modificar aca
    $metodo_pago = metodo_pago::find($this->metodo_pago_agregar_pago);
    
    $this->MontoPagoEditarPago = $value;

    $this->recargo = $metodo_pago->recargo/100;
    
    $this->recargo_total = $this->MontoPagoEditarPago * $this->recargo;

    $this->total_pago = $this->recargo_total + $this->MontoPagoEditarPago;
   
    $alicuota_iva = $this->GetAlicuotaIVA($this->id_pedido);   
    $this->monto_real = $this->setMontoReal($this->monto_ap,$alicuota_iva,$this->relacion_precio_iva);
//     $this->RenderFactura($this->id_pedido);

    $this->RecalcularDeduccionesMonto($this->id_pago,$this->total_pago);
    } 
    
    
     
 
     public function setRecargoPagos($monto,$recargo){
        return $monto * $recargo;
     }
     
 
       public function sumarIVAPago($monto_real,$alicuota_iva) {
      return $monto_real * $alicuota_iva;    
      }
    
      
      public function sumarIVARecargo($recargo_total,$alicuota_iva) {
      return $recargo_total * $alicuota_iva;
      }
      
    public function setMontoPagos($monto_original,$monto_real,$recargo,$iva_pago,$iva_recargo,$relacion_precio_iva){
      //dd($relacion_precio_iva);
      // si el pago es total
      if($relacion_precio_iva == 2) {      
      $monto_seted = $monto_real;    
      }
      if($relacion_precio_iva == 1 || $relacion_precio_iva == 0) {      
      $monto_seted = $monto_original;    
      }  
      return $monto_seted;
    }
    
    public function ElegirCaja($caja_id)
    {
    $this->caja = $caja_id;
    $this->caja_seleccionada = cajas::find($caja_id);
    $this->emit('modal-estado-hide','close');
    }
    
   public function ResetPago() {
     $this->metodo_pago_agregar_pago = 1;
     $this->monto_ap = 0;
     $this->formato_modal = 0;
     $this->recargo = 0;
     $this->tipo_pago = 1;
     $this->recargo_total = 0;
     $this->total_pago = 0;
     $this->recargo_mp = 0;
     $this->metodo_pago_ap = 1;
     $this->fecha_ap = Carbon::now()->format('d-m-Y');  
     $this->nro_comprobante = null;
     $this->comprobante = null;
     
     $this->CerrarModalPago();
   }
   

// Insumos

public function ActualizarTotalesVentaInsumos($venta) {

        $sale = SalesInsumos::find($venta);

        $relacion_precio_iva = $sale->relacion_precio_iva;

        $this->details = SaleDetailsInsumos::where('sale_id',$venta)->where('eliminado',0)->get();

               //
               $subtotal = $this->details->sum(function($item){
                  return $item->price * $item->quantity;
               });

              $descuento = $this->details->sum(function($item){
                  return $item->descuento;
              });

              $descuento_promo = $this->details->sum(function($item){
                  return $item->descuento_promo * $item->cantidad_promo;
              });

              $this->descuento_promo = $descuento_promo;
              $this->descuento = $descuento;
              $this->subtotal_venta_nuevo = $subtotal;

              $iva = $this->sumarIVASUBTOTAL($this->details);

              $subtotal_sin_recargo = $subtotal - $descuento - $descuento_promo;
              $alicuota_iva = $iva/$subtotal_sin_recargo;
            //  dd($alicuota_iva);

              $this->SetearRecargoIvaNuevo($venta,$alicuota_iva,$relacion_precio_iva);

              $recargo = $this->CalcularRecargoInsumos($venta);
              $iva_recargo = $this->CalcularIVARecargoInsumos($venta);

              $this->recargo = $recargo;
              $alicuota_recargo = $recargo / ($subtotal - $descuento_promo - $descuento);

              $iva = $iva + $iva_recargo;
              $this->iva_venta_nuevo = $iva;
              $this->total_venta_nuevo = $subtotal + $iva + $recargo - $descuento - $descuento_promo;

              $this->items_venta_nuevo = $this->details->sum('quantity');

              $this->venta = SalesInsumos::find($venta);

              $array_sale = [
                'subtotal' => $this->subtotal_venta_nuevo,
                'total' => $this->total_venta_nuevo,
                'iva' =>  $this->iva_venta_nuevo,
                'descuento' => $this->descuento,
                'descuento_promo' => $this->descuento_promo,
                'recargo' => $this->recargo,
                'alicuota_iva' => $alicuota_iva
                ];

              //dd($array_sale);

              $this->venta->update($array_sale);

            // Si es una venta a una sucursal
            if($this->venta->canal_venta == "Venta a sucursales") {

              $compra = compras_insumos::where('sale_id',$this->venta->id)->first();

              $this->ActualizarTotalCompraInsumos($this->venta->id);
              $this->ActualizarEstadoDeudaCompraInsumos($this->venta->id);

            }


        }
   


public function ActualizarTotalCompraInsumos($venta_id) {

      $venta = SalesInsumos::find($venta_id);

      $subtotal = $venta->subtotal;
      $total = $venta->total;
      $iva = $venta->iva;
      $items = $venta->items;
      $recargo = $venta->recargo;

      $compra = compras_insumos::where('sale_id',$venta_id)->first();

      $compra->update([
        'subtotal' => $subtotal,
        'total' => $total,
        'items' => $items,
        'recargos' => $recargo,
        'iva' => $iva
        ]);
    return $compra->id;
    }    

public function ActualizarEstadoDeudaCompraInsumos($ventaId)
{
  /////////////////   ACTUALIZAR ESTADO DE PAGO   /////////////////////
        $compra = compras_insumos::select('compras_insumos.id','compras_insumos.total')
       ->where('compras_insumos.sale_id', $ventaId)
       ->first();

       $pagos = pagos_facturas::where('pagos_facturas.id_compra_insumos', $compra->id)
       ->where('pagos_facturas.eliminado',0)
       ->get();

       $suma_pagos = $pagos->sum('monto_compra');

       $suma_compra = $compra->total;

       $deuda = $suma_compra - $suma_pagos;

     $this->deuda_vieja = compras_insumos::find($compra->id);

       $this->deuda_vieja->update([
         'deuda' => $deuda
         ]);

    
}

       public function ActualizarEstadoDeudaInsumos($ventaId)
       {
         /////////////////   ACTUALIZAR ESTADO DE PAGO   /////////////////////


              $this->data_cash = SalesInsumos::select('sales_insumos.cash','sales_insumos.created_at as fecha_factura')
              ->where('sales_insumos.id', $ventaId)
              ->get();

              $this->data_total = SalesInsumos::select('sales_insumos.total','sales_insumos.status','sales_insumos.recargo','sales_insumos.descuento')
              ->where('sales_insumos.id', $ventaId)
              ->first();

              $this->pagos2 = pagos_facturas::join('metodo_pagos as mp','mp.id','pagos_facturas.metodo_pago')
              ->select('mp.nombre as metodo_pago','pagos_facturas.id','pagos_facturas.iva_recargo','pagos_facturas.iva_pago','pagos_facturas.recargo','pagos_facturas.monto','pagos_facturas.created_at as fecha_pago')
              ->where('pagos_facturas.id_venta_insumos', $ventaId)
              ->where('pagos_facturas.eliminado',0)
              ->get();

              // dd($this->data_total);

              //dd($this->pagos2);

              // Pagos
              $this->suma_monto = $this->pagos2->sum('monto');

              // Recargos
              $this->rec = $this->pagos2->sum('recargo');

              // iva pago
              $iva_pago = $this->pagos2->sum('iva_pago');

              // IVA Recargo
              $iva_recargo = $this->pagos2->sum('iva_recargo');

              // total de la factura
              $this->tot = $this->data_total->total;

             // dd($this->tot,$this->suma_monto,$this->rec);

              if($this->data_total->status != "Cancelado") {
              $deuda = $this->tot - $this->suma_monto - $this->rec - $iva_pago - $iva_recargo;
              } else {
                  $deuda = 0;
              }



             $this->deuda_vieja = SalesInsumos::find($ventaId);

            //dd($this->deuda_vieja);

              $this->deuda_vieja->update([
                'deuda' => $deuda
                ]);


              ///////////////////////////////////////////////////////////////////
     }
     
}

