<?php
namespace App\Traits;

use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

use App\Models\pagos_facturas;
use App\Models\User;
use App\Models\sucursales;
use App\Models\bancos;
use App\Models\compras_proveedores;
use App\Models\Sale;
use App\Models\proveedores;

use App\Models\SalesInsumos;

// 14-8-2024
use App\Models\gastos;

use App\Models\saldos_iniciales;
use App\Models\ClientesMostrador;

use Carbon\Carbon;

// Trait
use App\Traits\VentasTrait;
use App\Traits\ComprasTrait;

trait PagosTrait {

use VentasTrait;
use ComprasTrait;

public $signo = '+';

public function ActualizarPagoVentaInsumos($id_pago,$monto,$recargo,$recargo_total,$caja,$fecha_ap,$banco_id,$metodo_pago,$nro_comprobante,$url_comprobante) {

      $pagos = pagos_facturas::find($id_pago);
      
      $ventaId = $pagos->id_venta_insumos;
      $alicuota_iva = $this->GetAlicuotaIVAInsumos($ventaId);    
      $relacion_precio_iva  = $this->GetRelacionPrecioIVAInsumos($ventaId);
      // seteamos los montos
      
      
      $monto_original = $monto;
      $monto_real = $this->setMontoReal($monto,$alicuota_iva,$relacion_precio_iva);
    
      $recargo = $this->setRecargoPagos($monto_real,$recargo);
      $iva_pago = $this->sumarIVAPago($monto_real,$alicuota_iva);
    
      $iva_recargo = $this->sumarIVARecargo($recargo,$alicuota_iva,$relacion_precio_iva);
      $monto = $this->setMontoPagos($monto_original,$monto_real,$recargo_total,$iva_pago,$iva_recargo,$relacion_precio_iva);
    
  $array = [
       'monto' => $monto_real,
       'iva_pago' => $iva_pago,
       'iva_recargo' => $iva_recargo,
       'recargo' => $recargo,
       'caja' => $caja,
       'nro_comprobante' => $nro_comprobante,
    //   'created_at' => $fecha_ap,
       'metodo_pago' => $metodo_pago,
       'banco_id' => $banco_id
       ];
  
   $pagos->update($array);
  
    if($url_comprobante != $pagos->url_comprobante)
	{
		$customFileName = uniqid() . '_.' . $url_comprobante->extension();
		$url_comprobante->storeAs('public/comprobantes', $customFileName);
		$pagos->url_comprobante = $customFileName;
		$pagos->save();
	}
	
	
      $ventas_vieja = SalesInsumos::find($ventaId);

      $rec = $this->CalcularRecargoInsumos($ventaId);

      $ventas_vieja->update([
        'recargo' => $rec,
        'metodo_pago' => $metodo_pago
       ]);
      
      
      // Cambiar esto ---
      $this->ActualizarTotalesVentaInsumos($ventaId);  // --- aca esta el error

      $this->ActualizarEstadoDeudaInsumos($ventaId);
      
      $cliente_id = SalesInsumos::find($ventaId)->cliente_id;
      $cliente = ClientesMostrador::find($cliente_id);
      
      if($cliente->sucursal_id != 0) {
       
          $this->ActualizarPagoCompraInsumos($pagos->id);    
      
    //  $this->ActualizarPagoCompra($pagos->id,$monto_real,$metodo_pago,$fecha_ap,$caja);
      
      }  
      
    //  $this->guardarDeducciones($pagos); // 30-6-2024

    }


public function ActualizarPagoVenta($id_pago,$monto,$recargo,$recargo_total,$caja,$fecha_ap,$banco_id,$metodo_pago,$nro_comprobante,$url_comprobante) {

      $pagos = pagos_facturas::find($id_pago);

      $ventaId = $pagos->id_factura;
      $alicuota_iva = $this->GetAlicuotaIVA($ventaId);    
      $relacion_precio_iva  = $this->GetRelacionPrecioIVA($ventaId);
      // seteamos los montos
      
      $monto_original = $monto;
      $monto_real = $this->setMontoReal($monto,$alicuota_iva,$relacion_precio_iva);
    
      $recargo = $this->setRecargoPagos($monto_real,$recargo);
      $iva_pago = $this->sumarIVAPago($monto_real,$alicuota_iva);
    
      $iva_recargo = $this->sumarIVARecargo($recargo,$alicuota_iva,$relacion_precio_iva);
      $monto = $this->setMontoPagos($monto_original,$monto_real,$recargo_total,$iva_pago,$iva_recargo,$relacion_precio_iva);
    
  $array = [
       'monto' => $monto_real,
       'iva_pago' => $iva_pago,
       'iva_recargo' => $iva_recargo,
       'recargo' => $recargo,
       'caja' => $caja,
       'nro_comprobante' => $nro_comprobante,
    //   'created_at' => $fecha_ap,
       'metodo_pago' => $metodo_pago,
       'banco_id' => $banco_id
       ];
  
   $pagos->update($array);
  
    if($url_comprobante != $pagos->url_comprobante)
	{
		$customFileName = uniqid() . '_.' . $url_comprobante->extension();
		$url_comprobante->storeAs('public/comprobantes', $customFileName);
		$pagos->url_comprobante = $customFileName;
		$pagos->save();
	}
	
	
      $ventas_vieja = Sale::find($ventaId);

      $rec = $this->CalcularRecargo($ventaId);

      $ventas_vieja->update([
        'recargo' => $rec,
        'metodo_pago' => $metodo_pago
       ]);
      
      
      $this->ActualizarTotalesVenta($ventaId);
      
      $this->ActualizarEstadoDeuda($ventaId);
      
      $cliente_id = Sale::find($ventaId)->cliente_id;
      $cliente = ClientesMostrador::find($cliente_id);
      
      if($cliente->sucursal_id != 0) {
       
      $this->ActualizarPagoCompra($pagos->id);    
      
    //  $this->ActualizarPagoCompra($pagos->id,$monto_real,$metodo_pago,$fecha_ap,$caja);
      
      }  
      
      $this->guardarDeducciones($pagos); // 30-6-2024

    }


public function ActualizarPagoCompra($id_pago_casa_central) {

  $pago_casa_central = pagos_facturas::find($id_pago_casa_central);

  $id = $id_pago_casa_central;

  $pagos_sucursal = pagos_facturas::where('pago_sucursal_id',$id)->where('eliminado',0)->first();

  $monto_total = $pago_casa_central->monto + $pago_casa_central->recargo + $pago_casa_central->iva_recargo + $pago_casa_central->iva_pago;

  $banco_id = intval($this->tipo_pago_sucursal);

  $pagos_sucursal->update([
    'banco_id' => $banco_id,
    'monto_compra' => $monto_total,
    'created_at' => $this->fecha_ap,
    'nro_comprobante' => $this->nro_comprobante,
    'metodo_pago' => $this->metodo_pago_agregar_pago,
  ]);

  $ventaId = $pago_casa_central->id_factura;


    if($this->comprobante != $pagos_sucursal->url_comprobante)
	{
		$customFileName = uniqid() . '_.' . $this->comprobante->extension();
		$this->comprobante->storeAs('public/comprobantes', $customFileName);
		$pagos_sucursal->url_comprobante = $customFileName;
		$pagos_sucursal->save();
	}


  $compra = compras_proveedores::where('sale_casa_central',$ventaId)->where('eliminado',0)->first();
      
  $this->ActualizarTotalCompra($compra->id);
  $this->ActualizarEstadoDeudaCompra($compra->id);

  $this->tipo_pago_sucursal = 1;
}


public function ActualizarPagoCompraTrait($id_pago_casa_central) {

  $pago_casa_central = pagos_facturas::find($id_pago_casa_central);

  //$monto_total = $pago_casa_central->monto + $pago_casa_central->recargo + $pago_casa_central->iva_recargo + $pago_casa_central->iva_pago;

  $pago_casa_central->update([
    'banco_id' => $this->tipo_pago,
    'monto_compra' => $this->monto_ap,
    'created_at' => $this->fecha_ap,
    'caja' => $this->caja,
    'nro_comprobante' => $this->nro_comprobante,
    'metodo_pago' => $this->metodo_pago_agregar_pago,
  ]);
  
  
  $ventaId = $pago_casa_central->id_factura;

  if($this->comprobante != $pago_casa_central->url_comprobante)
  {
		$customFileName = uniqid() . '_.' . $this->comprobante->extension();
		$this->comprobante->storeAs('public/comprobantes', $customFileName);
		$pago_casa_central->url_comprobante = $customFileName;
		$pago_casa_central->save();
	}

//  $this->ActualizarTotalCompraTrait($pago_casa_central->id_compra);
  $this->ActualizarEstadoDeudaCompraTrait($pago_casa_central->id_compra);
}


public function ActualizarEstadoDeudaCompraTrait($ventaId)
{
  /////////////////   ACTUALIZAR ESTADO DE PAGO   /////////////////////


       $data_total = compras_proveedores::where('compras_proveedores.id', $ventaId)->first();
       
       //dd($this->data_total);

       $pagos2 = pagos_facturas::join('bancos','bancos.id','pagos_facturas.banco_id')
       ->select('bancos.nombre as metodo_pago','pagos_facturas.id_compra','pagos_facturas.monto_compra','pagos_facturas.actualizacion','pagos_facturas.created_at as fecha_pago')
       ->where('pagos_facturas.id_compra', $ventaId)
       ->where('pagos_facturas.eliminado',0)
       ->get();

        $suma_monto = 0;
        
        // pagos        
        foreach ($pagos2 as $pago) {
            $montoCompra = $pago->monto_compra;
            $actualizacion = $pago->actualizacion;
        
            // Realizar el cálculo y agregar al total
            $suma_monto += $montoCompra * (1 + $actualizacion);
        }
        
        
       $total = $data_total->total;
       
      // dd($this->tot,$suma_monto);
       
       $deuda = $total - $suma_monto;

      //  dd($deuda);

      $deuda_vieja = compras_proveedores::find($ventaId);

       $deuda_vieja->update([
         'deuda' => $deuda
         ]);


       ///////////////////////////////////////////////////////////////////
}

// 14-8-2024
public function ActualizarPagoGasto($id_pago,$monto_ap,$metodo_pago,$fecha_ap,$caja){
            
    $pagos_facturas = pagos_facturas::find($id_pago);

    if($metodo_pago == 1) {
    $mp = 1;
    } else {$mp = 0;}

  if($pagos_facturas->banco_id != $metodo_pago){
  $estado_pago = $this->GetPlazoAcreditacionPago($metodo_pago); 
  } else {
  $estado_pago = $pagos_facturas->estado_pago;   
  }
  
    
    
    $pagos_facturas->update([
      'monto_gasto' => $monto_ap,
      'caja' => $this->caja,
      'metodo_pago'  => $mp,
      'banco_id'  => $metodo_pago,
      'estado_pago' => $estado_pago
    ]);
    
    $gasto = gastos::find($pagos_facturas->id_gasto);
    
    $total = $gasto->monto;
    $pagos = pagos_facturas::where('id_gasto',$gasto->id)->where('eliminado',0)->get();
    
    $monto_pago = $pagos->sum('monto_gasto');
    $deuda = $total - $monto_pago;
    
    // Filtrar los métodos de pago donde 'eliminado' es igual a 0 y luego acumular los valores de 'metodo_pago_ap_div'
    $cuentas = collect($pagos)
        ->filter(function ($metodo) {
            return $metodo->eliminado == 0;
        })
        ->pluck('banco_id')
        ->implode(',');

    $gasto->cuenta = $cuentas;
    $gasto->deuda = $deuda;
    $gasto->save();


}



//18-5-2024
public function GetPlazoAcreditacionPago($id){
    return $id == 1 ? 1 : 0;
}

function CrearPagoSaldoInicial($tipo,$selected_id,$comercio_id,$monto_ap,$metodo_pago,$caja_id) {

	$estado_pago = $this->GetPlazoAcreditacionPago($metodo_pago);
	
	if($tipo == 'proveedor'){$concepto = 'Pago';}
	if($tipo == 'cliente'){$concepto = 'Cobro';}
	
 	saldos_iniciales::create([
	    'tipo' => $tipo,
        'concepto' => $concepto,
        'referencia_id' => $selected_id,
        'comercio_id' => $comercio_id,
        'sucursal_id' => $comercio_id, // 2-8-2024
        'monto' => -$monto_ap,
        'eliminado' => 0,
        'metodo_pago' => $metodo_pago,
        'fecha' => Carbon::now(),
        'caja_id' => $caja_id,
        'estado_pago' => $estado_pago
	    ]);

	$si = saldos_iniciales::where("referencia_id",$selected_id)->where("tipo",$tipo)->where("eliminado",0)->get();
	
	$sum_si = $si->sum('monto');

	$this->saldos_iniciales = saldos_iniciales::where("tipo",$tipo)->where("referencia_id",$selected_id)->get();
	
	return $sum_si;
}

function ActualizarPagoSaldoInicial($pago_id,$tipo,$selected_id,$comercio_id,$monto_ap,$metodo_pago,$caja_id) {
		
	$saldos_iniciales = saldos_iniciales::where('id',$pago_id)->where('tipo',$tipo)->first();
	
	if($saldos_iniciales->concepto == 'Saldo inicial'){$monto = $monto_ap;} else {$monto = -$monto_ap;}
	
	if($saldos_iniciales->metodo_pago != $metodo_pago){
	$estado_pago = $this->GetPlazoAcreditacionPago($metodo_pago);	    
	} else {
	$estado_pago = $saldos_iniciales->estado_pago;    
	}

	$saldos_iniciales->update([
        'monto' => $monto,
        'metodo_pago' => $metodo_pago,
        'fecha' => Carbon::now(),
         'caja_id' => $caja_id,
        'estado_pago' => $estado_pago
	    ]);
	 
	$si = saldos_iniciales::where("referencia_id",$selected_id)->where("tipo",$tipo)->where("eliminado",0)->get();
	
	$sum_si = $si->sum('monto');
    
	//$saldos_iniciales_mostrar = saldos_iniciales::where("tipo",$tipo)->where("referencia_id",$selected_id)->get();
	
	//dd($saldos_iniciales);
	
	if($saldos_iniciales->concepto == 'Saldo inicial'){
	    
	   if($tipo == "cliente"){
	       
	   } else {
	   $p = proveedores::find($saldos_iniciales->referencia_id);
	   $p->saldo_inicial_cuenta_corriente = $monto;
	   $p->save();	       
	   }
	   

	}
	
	return $sum_si;
}

public function DeletePagoSaldoInicial($pago_id,$tipo,$selected_id){

	$saldos_iniciales = saldos_iniciales::where('id',$pago_id)->where('tipo',$tipo)->first();

	$saldos_iniciales->update([
        'eliminado' => 1
	    ]);
	 
	$si = saldos_iniciales::where("referencia_id",$selected_id)->where("tipo",$tipo)->where("eliminado",0)->get();
	
	$sum_si = $si->sum('monto');

	$this->saldos_iniciales = saldos_iniciales::where("tipo",$tipo)->where("referencia_id",$selected_id)->get();

}


// Insumos

public function ActualizarPagoCompraInsumos($id_pago_casa_central) {

  $pago_casa_central = pagos_facturas::find($id_pago_casa_central);

  $id = $id_pago_casa_central;

  $pagos_sucursal = pagos_facturas::where('pago_sucursal_id',$id)->where('eliminado',0)->first();

  $monto_total = $pago_casa_central->monto + $pago_casa_central->recargo + $pago_casa_central->iva_recargo + $pago_casa_central->iva_pago;

  $banco_id = intval($this->tipo_pago_sucursal);

  $pagos_sucursal->update([
    'banco_id' => $banco_id,
    'monto_compra' => $monto_total,
    'created_at' => $this->fecha_ap,
    'nro_comprobante' => $this->nro_comprobante,
    'metodo_pago' => $this->metodo_pago_agregar_pago,
  ]);

  $ventaId = $pago_casa_central->id_venta_insumos;


    if($this->comprobante != $pagos_sucursal->url_comprobante)
	{
		$customFileName = uniqid() . '_.' . $this->comprobante->extension();
		$this->comprobante->storeAs('public/comprobantes', $customFileName);
		$pagos_sucursal->url_comprobante = $customFileName;
		$pagos_sucursal->save();
	}


  $this->ActualizarTotalCompraInsumos($ventaId);
  $this->ActualizarEstadoDeudaCompraInsumos($ventaId);

  $this->tipo_pago_sucursal = 1;
}

public function SetPagosClienteSucursal($cliente_id,$pago_id){
    
    $this->datos_cliente = ClientesMostrador::find($cliente_id);

    if($this->datos_cliente != null){
    if($this->datos_cliente->sucursal_id != 0){
    $sucursal_id_compra = sucursales::find($this->datos_cliente->sucursal_id)->sucursal_id;

    // aca pasamos la sucursal y el id de pago
    $this->SetSucursalPagos($sucursal_id_compra,$pago_id);
    }
    }
    //
       
}
public function SetSucursalPagos($sucursal_id,$pago_id){

      // Setea los tipos de pago
       $this->tipos_pago_sucursal = bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
      ->select('bancos.*')
      ->where('bancos_muestra_sucursales.sucursal_id', $sucursal_id)
       ->where('bancos_muestra_sucursales.muestra', 1)
      ->orderBy('bancos.nombre','asc')->get();

      //  si $pago_id != 0 tiene que traer el metodo de pago de la sucursal

      if($pago_id != 0){
      $tipo_pago_sucursal = pagos_facturas::find($pago_id)->banco_id;
      $this->tipo_pago_sucursal = $tipo_pago_sucursal;
      }


    }

}

