<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Exports\ProductsExport;
use App\Exports\ProductsEjemploExport;
use App\Exports\ListaPreciosExport;
use App\Exports\StockSucursalExport;
use App\Exports\HojaRutaExport;
use App\Exports\CategoryExport;
use App\Exports\RecetasExport;
use App\Exports\CRMExport;
use App\Exports\CtaCteClientesMovimientosExport;
use App\Exports\CtaCteClientesMovimientosExportPorProducto;

use App\Exports\CtaCteProveedoresExport;
use App\Exports\CtaCteClientesExport;

// 18-4-2024
use App\Exports\PagosExport;
use DB;

use Illuminate\Support\Facades\Log;

use App\Models\configuracion_impresion; // 7-8-2024

use App\Exports\ClientesExport;
use App\Exports\ProveedoresExport;
use App\Exports\GastosExport;
use App\Exports\ComprasExport;
use App\Exports\InsumosExport;
use App\Exports\FacturasExport;
use App\Exports\FacturasComprasExport;
use App\Exports\SalesDetailExport;
use App\Exports\EtiquetasExport;
use App\Exports\CajaExport;
use App\Models\Estados;

use App\Models\configuracion_ctas_ctes;
use App\Models\pagos_facturas;
use App\Models\saldos_iniciales;

use App\Models\hoja_ruta;
use App\Models\cobro_rapido;
use App\Models\cobro_rapidos_detalle;
use App\Exports\AsistenteExport;
use App\Exports\ProduccionExport;

use App\Models\facturacion;
use App\Models\detalle_facturacion;

use App\Models\sucursales;
use App\Models\Sale;
use App\Models\ecommerce_envio;
use App\Models\detalle_compra_insumos;
use App\Models\compras_insumos;
use App\Models\proveedores;
use App\Models\detalle_compra_proveedores;
use App\Models\compras_proveedores;
use App\Models\presupuestos;
use App\Models\lista_precios;
use App\Models\ecommerce;
use App\Models\presupuestos_detalle;
use App\Models\produccion_detalle;
use App\Models\ClientesMostrador;
use App\Models\datos_facturacion;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\User;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Mail;
use Afip;

use App\Models\movimiento_stocks; // Movimiento stock
use App\Models\movimiento_stocks_detalles; // Movimiento stock

use App\Traits\CajasTrait; // 26-6-2024


class ExportController extends Controller
{

  use CajasTrait; // 26-6-2024


  public $userId;
  public $clienteId;
  public $detalle_facturacion;
  public $usuario;



// Movimiento stock
public function RemitoMovimientoStockPDF($movimientoId)
{
    $detalle_venta = [];
    $usuario = [];
    $fecha = [];
    $movimientoIdFactura = $movimientoId;

    $usuario_id = Auth::user()->id;

    if (Auth::user()->comercio_id != 1) {
        $comercio_id = Auth::user()->comercio_id;
    } else {
        $comercio_id = Auth::user()->id;
    }

    $movimiento = movimiento_stocks::find($movimientoId);
    
    $detalle_facturacion = User::find($movimiento->sucursal_origen);
    
    $detalle_venta = movimiento_stocks_detalles::where('movimiento_stocks_detalles.movimiento_id', $movimientoId)->get();
    
    $detalle_origen = User::find($movimiento->sucursal_origen);      
    $detalle_destino = User::find($movimiento->sucursal_destino);      
    
    $fecha = movimiento_stocks::find($movimientoId)->created_at;
    
    $usuario = User::join('movimiento_stocks as s', 's.casa_central_id', 'users.id')
        ->select('users.image', 'users.name')
        ->where('s.id', $movimientoId)
        ->get();
        
        
    $usuario_realizador = User::join('movimiento_stocks as s', 's.user_id', 'users.id')
        ->select('users.image', 'users.name')
        ->where('s.id', $movimientoId)
        ->first();

    $movimientoId = $movimiento->nro_movimiento;

    $pdf_factura = PDF::loadView('pdf.remito-movimiento-stock', compact('detalle_venta', 'detalle_origen','detalle_destino', 'detalle_facturacion', 'movimiento', 'fecha', 'usuario','usuario_realizador', 'movimientoId'));

    return $pdf_factura->stream('Movimiento stock.pdf'); // visualizar
}

// 26-6-2024 
public function PDFCaja($caja_id, $uid)
{
    $this->GetResumenCaja($caja_id);
    // Generales 
    $detalle_nro_caja = $this->detalle_nro_caja;
    $total_ventas_totales = $this->total_ventas_totales;
    $total_ventas_efectivo = $this->total_ventas_efectivo;
    $total_ventas_plataformas = $this->total_ventas_plataformas;
    $total_ventas_bancos = $this->total_ventas_bancos;
    
    // Efectivo
    $details_efectivo = $this->details_efectivo ;
    $ingresos_efectivo = $this->ingresos_efectivo;
    $retiros_efectivo = $this->retiros_efectivo;
    $caja = $this->caja;
    $total_ingresos_efectivo = $this->total_ingresos_efectivo;
    $total_retiros_efectivo = $this->total_retiros_efectivo;
    $total_efectivo_inicial = $this->total_efectivo_inicial;
    $total_efectivo_final = $this->total_efectivo_final ;
    $count_efectivo = $this->count_efectivo;
    $total_compras_efectivo = $this->total_compras_efectivo ;
    $total_gastos_efectivo = $this->total_gastos_efectivo ;
    $total_ventas_efectivo = $this->total_ventas_efectivo ;
    $total_efectivo = $this->total_efectivo ;
    
    // Bancos
   $listado_bancos = $this->listado_bancos;
   $details_bancos = $this->details_bancos;
   $compras_bancos = $this->compras_bancos;
   $gastos_bancos = $this->gastos_bancos;
   $ingresos_bancos = $this->ingresos_bancos;
   $retiros_bancos = $this->retiros_bancos;
   $totales_bancos = $this->totales_bancos;
   $count_bancos = $this->count_bancos;
   $total_bancos = $this->total_bancos;
   $total_ventas_bancos = $this->total_ventas_bancos;
   
   // Plafatormas
    $listado_plataformas =     $this->listado_plataformas;
    $details_plataformas =     $this->details_plataformas;
    $compras_plataformas =     $this->compras_plataformas;
    $gastos_plataformas =     $this->gastos_plataformas;
    $ingresos_plataformas =     $this->ingresos_plataformas;
    $retiros_plataformas =     $this->retiros_plataformas;
    $totales_plataformas =     $this->totales_plataformas;
    $count_plataformas =     $this->count_plataformas;
    $total_plataformas =     $this->total_plataformas;
    $total_ventas_plataformas =     $this->total_ventas_plataformas;
    
    
  $pdf = PDF::loadView('pdf.resumen-caja', compact('detalle_nro_caja', 'total_ventas_totales', 'total_ventas_efectivo', 'total_ventas_plataformas', 'total_ventas_bancos',
    'details_efectivo', 'ingresos_efectivo', 'retiros_efectivo', 'caja', 'total_ingresos_efectivo', 'total_retiros_efectivo',
    'total_efectivo_inicial', 'total_efectivo_final', 'count_efectivo', 'total_compras_efectivo', 'total_gastos_efectivo',
    'total_efectivo',
    'listado_bancos', 'details_bancos', 'compras_bancos', 'gastos_bancos', 'ingresos_bancos', 'retiros_bancos', 'totales_bancos',
    'count_bancos', 'total_bancos', 'total_ventas_bancos',
    'listado_plataformas', 'details_plataformas', 'compras_plataformas', 'gastos_plataformas', 'ingresos_plataformas',
    'retiros_plataformas', 'totales_plataformas', 'count_plataformas', 'total_plataformas', 'total_ventas_plataformas'
));

  foreach($detalle_nro_caja as $dnc){
  $nombre = 'Caja nro'.$dnc->nro_caja.'.pdf';    
  }
    
  return $pdf->stream($nombre); // visualizar
}


  public function reportPDFRemito($Id)
  {
      $detalle_venta = [];
      $usuario = [];
      $detalle_cliente = [];
      $total_total = [];
      $fecha = [];
      $ventaId = $Id;
      $ventaIdFactura = $Id;

      $usuario_id = Auth::user()->id;

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;


      $detalle_facturacion = $this->GetDatosUsuario($comercio_id);
      
      $detalle_venta = SaleDetail::join('products as p','p.id','sale_details.product_id')
      ->select('sale_details.id','sale_details.price','sale_details.quantity','p.name as product', 'sale_details.iva','sale_details.product_name as product','sale_details.product_barcode','p.stock','p.stock_descubierto', SaleDetail::raw('sale_details.price*sale_details.quantity as total'))
      ->where('sale_details.sale_id', $Id)
      ->where('sale_details.eliminado', 0)
      ->get();


      $detalle_cliente = Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
      ->select('c.id','c.nombre','c.telefono','c.email','c.dni','c.direccion','c.barrio','c.localidad','c.provincia','sales.observaciones','sales.metodo_pago','sales.fecha_entrega')
      ->where('sales.id', $Id)
      ->get();

      $total_total = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
         ->select('sales.recargo','sales.tipo_comprobante','sales.created_at','sales.descuento','sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago','sales.cae','sales.tipo_comprobante','sales.vto_cae','sales.nro_factura','sales.iva')
         ->where('sales.id', $Id)
         ->get();

        $fecha = Sale::select('sales.created_at')
         ->where('sales.id', $Id)
         ->get();


         $usuario = User::join('sales as s','s.comercio_id','users.id')
          ->select('users.image','users.name')
         ->where('s.id', $Id)
         ->get();

            $ecommerce_envio_form = ecommerce_envio::leftjoin('provincias','provincias.id','ecommerce_envios.provincia')
            ->select('ecommerce_envios.*','provincias.provincia  as nombre_provincia')
            ->where('sale_id',$Id)
            ->first();


            /////////////// CODIGO DE BARRAS AFIP ///////////////////

  $this->total_total2 = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
  ->select('sales.nro_venta','sales.recargo','sales.tipo_comprobante','sales.created_at','sales.descuento','sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago','sales.cae','sales.vto_cae','sales.nro_factura')
  ->where('sales.id', $Id)
  ->first();

  $ventaId = $this->total_total2->nro_venta;
  
  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;


  $this->detalle_facturacion2 = datos_facturacion::where('datos_facturacions.comercio_id', $comercio_id)->first();

if($this->detalle_facturacion2 != null) {

  /**
   * CUIT de la persona/empresa emitio la factura (11 caracteres)
   **/

  /**
   * Tipo de comprobante (2 caracteres, completado con 0's)
   **/
  if ($this->total_total2->tipo_comprobante == "A") {
   $tipo_de_comprobante = '01';
  }
  if ($this->total_total2->tipo_comprobante == "B") {
   $tipo_de_comprobante = '06';
  }
  if ($this->total_total2->tipo_comprobante == "C") {
   $tipo_de_comprobante = '011';
  }
  if ($this->total_total2->tipo_comprobante == "CF") {
   $tipo_de_comprobante = '099';
  }


  $cuit = $this->detalle_facturacion2->cuit;

    if($this->total_total2->nro_factura != null){

  /**
   * Punto de venta (4 caracteres, completado con 0's)
   **/
   $porciones = explode("-", $this->total_total2->nro_factura);
   $tipo_factura = $porciones[0]; // porción1
   $pto_venta = $porciones[1]; // porción2
   $nro_factura_ = $porciones[2]; // porción2
   $this->pto_venta = str_pad($pto_venta, 4, "0", STR_PAD_LEFT);


  $punto_de_venta = $this->pto_venta;

  /**
   * CAE (14 caracteres)
   **/
  $cae = $this->total_total2->cae;

  /**
   * Fecha de expiracion del CAE (8 caracteres, formato aaaammdd)
   **/
  $this->vto_cae = Carbon::parse($this->total_total2->vto_cae)->format('Ymd');

  $vencimiento_cae = $this->vto_cae;


  $barcode = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;

  $code = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;

  //Step one
  $number_odd = 0;
  for ($i=0; $i < strlen($code); $i+=2) {
    $number_odd += $code[$i];
  }

  //Step two
  $number_odd *= 3;

  //Step three
  $number_even = 0;
  for ($i=1; $i < strlen($code); $i+=2) {
    $number_even += $code[$i];
  }

  //Step four
  $sum = $number_odd+$number_even;

  //Step five
  $checksum_char = 10 - ($sum % 10);

  $this->barcode_ultimo = $checksum_char == 10 ? 0 : $checksum_char;

  $barcode .= $this->barcode_ultimo;

  /**
   * Mostramos por pantalla el numero del codigo de barras de 40 caracteres
   **/
  $codigo_barra_afip = $barcode;

    } else {
         $codigo_barra_afip = "";
        $cae = "";
    }

} else {
  $codigo_barra_afip = "";
  $cae = "";
}

if($this->detalle_facturacion2 != null) {


	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

	$this->datos_facturacion = datos_facturacion::where('comercio_id', $comercio_id)->first();


	if($this->datos_facturacion->cuit != null || $this->datos_facturacion->cuit != '') {


	$cuit =$this->datos_facturacion->cuit;

	$afip = new Afip(array('CUIT' =>$cuit , 'production' => true));

	/**
	* Numero del punto de venta
	**/
	$punto_de_venta = $this->datos_facturacion->pto_venta;

	$this->factura = Sale::join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','sales.nro_factura','sales.tipo_comprobante')->find($ventaIdFactura);


	if($this->factura->tipo_comprobante == 'C' || $this->factura->tipo_comprobante == 'CF') {

	  $tipo_de_comprobante = 11;


	}

	if($this->factura->tipo_comprobante == 'B') {
	  $tipo_de_comprobante = 6;
	}



	if($this->factura->tipo_comprobante == 'A') {
	$tipo_de_comprobante = 1;
	}

	if($this->factura->nro_factura != null) {
	    	$porciones = explode("-", $this->factura->nro_factura);
	$tipo_factura = $porciones[0]; // porción1
	$pto_venta = $porciones[1]; // porción2
	$nro_factura = $porciones[2]; // porción2
	$n = $porciones[2]; // porción2

	                                    /**
	* Numero de factura
	**/
	$numero_de_factura = $nro_factura;

	                    /**
	                     * Numero del punto de venta
	                     **/
	                    $punto_de_venta = $punto_de_venta;

	                    /**
	                     * Tipo de comprobante
	                     **/
	                    $tipo_de_comprobante = $tipo_de_comprobante; // 6 = Factura B

	                    /**
	                     * Informacion de la factura
	                     **/

	                    $informacion = $afip->ElectronicBilling->GetVoucherInfo($numero_de_factura, $punto_de_venta, $tipo_de_comprobante);

	                    if($informacion === NULL){
	                        echo 'La factura no existe';
	                    }
	                    else{
	                      /**
	                       * Mostramos por pantalla la información de la factura
	                       **

	                       dd($informacion);

	                       */

	                       $cuit = intval($cuit);
                           $numero_de_factura = intval($numero_de_factura);
                           $cae = intval($informacion->CodAutorizacion);
                           $dia = substr($informacion->CbteFch, -2);
                           $mes = substr($informacion->CbteFch, -4, 2);
                           $año = substr($informacion->CbteFch, -8, 4);

                           $fecha = $año."-".$mes."-".$dia;


	                                               // genero los datos para AFIP
	                        $url = 'https://www.afip.gob.ar/fe/qr/'; // URL que pide AFIP que se ponga en el QR.
	                        $datos_cmp_base_64 = json_encode([
	                            "ver" => 1,                         // Numérico 1 digito -  OBLIGATORIO – versión del formato de los datos del comprobante	1
	                            "fecha" => $fecha,            // full-date (RFC3339) - OBLIGATORIO – Fecha de emisión del comprobante
	                            "cuit" => $cuit,        // Numérico 11 dígitos -  OBLIGATORIO – Cuit del Emisor del comprobante
	                            "ptoVta" => $informacion->PtoVta,               // Numérico hasta 5 digitos - OBLIGATORIO – Punto de venta utilizado para emitir el comprobante
	                            "tipoCmp" => $informacion->CbteTipo,               // Numérico hasta 3 dígitos - OBLIGATORIO – tipo de comprobante (según Tablas del sistema. Ver abajo )
	                            "nroCmp" => $numero_de_factura,               // Numérico hasta 8 dígitos - OBLIGATORIO – Número del comprobante
	                            "importe" => $informacion->ImpTotal,         // Decimal hasta 13 enteros y 2 decimales - OBLIGATORIO – Importe Total del comprobante (en la moneda en la que fue emitido)
	                            "moneda" => "PES",                  // 3 caracteres - OBLIGATORIO – Moneda del comprobante (según Tablas del sistema. Ver Abajo )
	                            "ctz" => 1,                 // Decimal hasta 13 enteros y 6 decimales - OBLIGATORIO – Cotización en pesos argentinos de la moneda utilizada (1 cuando la moneda sea pesos)
	                            "tipoCodAut" => "E",                // string - OBLIGATORIO – “A” para comprobante autorizado por CAEA, “E” para comprobante autorizado por CAE
	                            "codAut" =>  $cae    // Numérico 14 dígitos -  OBLIGATORIO – Código de autorización otorgado por AFIP para el comprobante
	                        ]);


	                        $datos_cmp_base_64 = base64_encode($datos_cmp_base_64);
	                        $codigo_qr = $url.'?p='.$datos_cmp_base_64;

                            $codigoQR = QrCode::size(90)->generate($codigo_qr);
	                    }

	} else {
        $codigo_qr = 0;
	    $codigoQR = 0;
	}


	} else {
	   $codigo_qr = 0;
	    $codigoQR = 0; 
	}


} else {
    $codigo_qr = 0;
    $codigoQR = 0;
}


  $pdf_factura = PDF::loadView('pdf.reporte-remito', compact('detalle_venta','Id','detalle_cliente','detalle_facturacion','total_total','fecha','usuario','ventaId','codigo_barra_afip','cae','codigoQR','ecommerce_envio_form'));


      return $pdf_factura->stream('Venta.pdf'); // visualizar

  }


  public function reportPDFRecetaImprimir($Id)
  {
    $data = [];
 
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    
    $usuario = User::find($comercio_id);
    
    $produccion_detalles_insumos = produccion_detalle::find($Id);

    // dd($produccion_detalles_insumos);
    
    $nombre = Product::leftjoin('productos_variaciones_datos','productos_variaciones_datos.product_id','products.id')
    ->where('products.id',$produccion_detalles_insumos->producto_id )
    ->where('productos_variaciones_datos.referencia_variacion', $produccion_detalles_insumos->referencia_variacion)
    ->where('products.eliminado', 'like', 0)
    ->where('productos_variaciones_datos.eliminado', 'like', 0)
    ->first();
    
    
    $data = Product::leftjoin('recetas as r','r.product_id','products.id')
    ->leftjoin('insumos','insumos.id','r.insumo_id')
    ->join('unidad_medidas','unidad_medidas.id','r.unidad_medida')
    ->select('insumos.id','r.rinde','unidad_medidas.nombre as nombre_unidad_medida','insumos.name','insumos.barcode','insumos.cost','r.unidad_medida','r.cantidad','r.costo_unitario','r.cantidad','r.relacion_medida','insumos.relacion_unidad_medida','r.product_id')
    ->where('products.id', 'like', $produccion_detalles_insumos->producto_id)
    ->where('r.referencia_variacion', 'like', $produccion_detalles_insumos->referencia_variacion)
    ->where('products.eliminado', 'like', 0)
    ->where('r.eliminado', 'like', 0)
    ->get();
    
   // dd($products);
   
    $suma = $data->sum(function($item){
              return $item->cost*$item->relacion_medida*$item->cantidad;
    });
    
    $rinde = $data->sum(function($item){
              return $item->rinde;
    });
    
    // Obtener longitud
    $cantidadDeElementos = count($data);
    // Dividir, y listo
    $promedio = $rinde / $cantidadDeElementos;
    
    $rinde = $promedio;
    
    $pdf_factura = PDF::loadView('pdf.reporte-receta-detalle', compact('Id', 'data', 'rinde' , 'produccion_detalles_insumos', 'suma', 'nombre', 'usuario'));

    return $pdf_factura->stream('Venta.pdf'); // visualizar

  }


public function Ticket($saleId)
{


Log::info('Tickt'. Auth::user()->id .' Venta: '. $saleId);

$items = [];
$sale= [];

// 7-8-2024

$configuracion_impresion = configuracion_impresion::where('user_id',Auth::user()->id)->first();
//dd($configuracion_impresion);
if($configuracion_impresion == null){
$size = 58;
$muestra_cta_cte = 0;
} else {
$size = $configuracion_impresion->size;
$muestra_cta_cte = $configuracion_impresion->muestra_cta_cte;
}

//
$items = SaleDetail::join('products','products.id','sale_details.product_id')
->select('sale_details.product_name','sale_details.precio_original','sale_details.quantity','sale_details.descuento','sale_details.recargo','sale_details.price','sale_details.iva','sale_details.nombre_promo','sale_details.cantidad_promo','sale_details.descuento_promo')
->where('sale_details.eliminado',0)
->where('sale_id',$saleId)->get();

$sale = Sale::select('sales.*',Sale::raw('(sales.subtotal) as subtotal'))->find($saleId);

$cliente = ClientesMostrador::select('clientes_mostradors.*')->join('sales','sales.cliente_id','clientes_mostradors.id')->where('sales.id',$saleId)->first();

$deuda = $this->GetCtaClienteClienteById($cliente->id);
$maximo_cta_cte = $cliente->monto_maximo_cuenta_corriente;

$user = Sale::join('users','users.id','sales.comercio_id')
->select('users.*')
->where('sales.id',$saleId)->first();

$ventaId = $saleId;
$ventaIdFactura = $saleId;

$datos_facturacion = datos_facturacion::where('comercio_id', $sale->comercio_id)->where('eliminado',0)->first();

if($sale->cae != null) {

  /////////////// CODIGO DE BARRAS AFIP ///////////////////

  $this->total_total2 = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
  ->select('sales.recargo','sales.tipo_comprobante','sales.created_at','sales.descuento','sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago','sales.cae','sales.vto_cae','sales.nro_factura')
  ->where('sales.id', $ventaId)
  ->first();

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $this->detalle_facturacion2 = datos_facturacion::where('datos_facturacions.comercio_id', $comercio_id)->first();

  /**
   * CUIT de la persona/empresa emitio la factura (11 caracteres)
   **/

  /**
   * Tipo de comprobante (2 caracteres, completado con 0's)
   **/
  if ($this->total_total2->tipo_comprobante == "A") {
   $tipo_de_comprobante = '01';
  }
  if ($this->total_total2->tipo_comprobante == "B") {
   $tipo_de_comprobante = '06';
  }
  if ($this->total_total2->tipo_comprobante == "C") {
   $tipo_de_comprobante = '011';
  }

  if ($this->total_total2->tipo_comprobante == "CF") {
   $tipo_de_comprobante = '099';
  }



  $cuit = $this->detalle_facturacion2->cuit;

  /**
   * Punto de venta (4 caracteres, completado con 0's)
   **/
   $porciones = explode("-", $this->total_total2->nro_factura);
   $tipo_factura = $porciones[0]; // porción1
   $pto_venta = $porciones[1]; // porción2
   $nro_factura_ = $porciones[2]; // porción2
   $this->pto_venta = str_pad($pto_venta, 4, "0", STR_PAD_LEFT);


  $punto_de_venta = $this->pto_venta;

  /**
   * CAE (14 caracteres)
   **/
  $cae = $this->total_total2->cae;

  /**
   * Fecha de expiracion del CAE (8 caracteres, formato aaaammdd)
   **/
  $this->vto_cae = Carbon::parse($this->total_total2->vto_cae)->format('Ymd');

  $vencimiento_cae = $this->vto_cae;
 

  $barcode = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;

  $code = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;

  //Step one
  $number_odd = 0;
  for ($i=0; $i < strlen($code); $i+=2) {
    $number_odd += $code[$i];
  }

  //Step two
  $number_odd *= 3;

  //Step three
  $number_even = 0;
  for ($i=1; $i < strlen($code); $i+=2) {
    $number_even += $code[$i];
  }

  //Step four
  $sum = $number_odd+$number_even;

  //Step five
  $checksum_char = 10 - ($sum % 10);

  $this->barcode_ultimo = $checksum_char == 10 ? 0 : $checksum_char;

  $barcode .= $this->barcode_ultimo;

  /**
   * Mostramos por pantalla el numero del codigo de barras de 40 caracteres
   **/
  $codigo_barra_afip = $barcode;


} else {
    $codigo_barra_afip = 0;
}

if($sale->cae != null) {


	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

	$this->datos_facturacion = datos_facturacion::where('comercio_id', $comercio_id)->first();


	if($this->datos_facturacion->cuit != null || $this->datos_facturacion->cuit != '') {


	$cuit =$this->datos_facturacion->cuit;

	$afip = new Afip(array('CUIT' =>$cuit , 'production' => true));

	/**
	* Numero del punto de venta
	**/
	$punto_de_venta = $this->datos_facturacion->pto_venta;

	$this->factura = Sale::join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','sales.nro_factura','sales.tipo_comprobante')->find($ventaIdFactura);


	if($this->factura->tipo_comprobante == 'C' || $this->factura->tipo_comprobante == 'CF') {

	  $tipo_de_comprobante = 11;


	}

	if($this->factura->tipo_comprobante == 'B') {
	  $tipo_de_comprobante = 6;
	}



	if($this->factura->tipo_comprobante == 'A') {
	$tipo_de_comprobante = 1;
	}


	$porciones = explode("-", $this->factura->nro_factura);
	$tipo_factura = $porciones[0]; // porción1
	$pto_venta = $porciones[1]; // porción2
	$nro_factura = $porciones[2]; // porción2
	$n = $porciones[2]; // porción2

	                                    /**
	* Numero de factura
	**/
	$numero_de_factura = $nro_factura;

	                    /**
	                     * Numero del punto de venta
	                     **/
	                    $punto_de_venta = $punto_de_venta;

	                    /**
	                     * Tipo de comprobante
	                     **/
	                    $tipo_de_comprobante = $tipo_de_comprobante; // 6 = Factura B

	                    /**
	                     * Informacion de la factura
	                     **/

	                    $informacion = $afip->ElectronicBilling->GetVoucherInfo($numero_de_factura, $punto_de_venta, $tipo_de_comprobante);

	                    if($informacion === NULL){
	                        echo 'La factura no existe';
	                    }
	                    else{
	                      /**
	                       * Mostramos por pantalla la información de la factura
	                       **

	                       dd($informacion);

	                       */

	                       $cuit = intval($cuit);
                           $numero_de_factura = intval($numero_de_factura);
                           $cae = intval($informacion->CodAutorizacion);
                           $dia = substr($informacion->CbteFch, -2);
                           $mes = substr($informacion->CbteFch, -4, 2);
                           $año = substr($informacion->CbteFch, -8, 4);

                           $fecha = $año."-".$mes."-".$dia;


	                                               // genero los datos para AFIP
	                        $url = 'https://www.afip.gob.ar/fe/qr/'; // URL que pide AFIP que se ponga en el QR.
	                        $datos_cmp_base_64 = json_encode([
	                            "ver" => 1,                         // Numérico 1 digito -  OBLIGATORIO – versión del formato de los datos del comprobante	1
	                            "fecha" => $fecha,            // full-date (RFC3339) - OBLIGATORIO – Fecha de emisión del comprobante
	                            "cuit" => $cuit,        // Numérico 11 dígitos -  OBLIGATORIO – Cuit del Emisor del comprobante
	                            "ptoVta" => $informacion->PtoVta,               // Numérico hasta 5 digitos - OBLIGATORIO – Punto de venta utilizado para emitir el comprobante
	                            "tipoCmp" => $informacion->CbteTipo,               // Numérico hasta 3 dígitos - OBLIGATORIO – tipo de comprobante (según Tablas del sistema. Ver abajo )
	                            "nroCmp" => $numero_de_factura,               // Numérico hasta 8 dígitos - OBLIGATORIO – Número del comprobante
	                            "importe" => $informacion->ImpTotal,         // Decimal hasta 13 enteros y 2 decimales - OBLIGATORIO – Importe Total del comprobante (en la moneda en la que fue emitido)
	                            "moneda" => "PES",                  // 3 caracteres - OBLIGATORIO – Moneda del comprobante (según Tablas del sistema. Ver Abajo )
	                            "ctz" => 1,                 // Decimal hasta 13 enteros y 6 decimales - OBLIGATORIO – Cotización en pesos argentinos de la moneda utilizada (1 cuando la moneda sea pesos)
	                            "tipoCodAut" => "E",                // string - OBLIGATORIO – “A” para comprobante autorizado por CAEA, “E” para comprobante autorizado por CAE
	                            "codAut" =>  $cae    // Numérico 14 dígitos -  OBLIGATORIO – Código de autorización otorgado por AFIP para el comprobante
	                        ]);


	                        $datos_cmp_base_64 = base64_encode($datos_cmp_base_64);
	                        $codigo_qr = $url.'?p='.$datos_cmp_base_64;

                            $codigoQR = QrCode::size(90)->generate($codigo_qr);
	                    }



	}


} else {
    $codigo_qr = 0;
    $codigoQR = 0;
}


$pdf_factura = PDF::loadView('pdf.ticket', compact('items','sale','user','codigo_barra_afip','datos_facturacion','cliente','codigoQR','size','deuda','maximo_cta_cte','muestra_cta_cte'));

$nombre_ticket = 'Ticket_'.$saleId.'.pdf';

return $pdf_factura->stream($nombre_ticket); // visualizar

}


public function TicketFactura($factura_id)
{
$items = [];
$sale= [];
$Id = $factura_id;

if(Auth::user()->comercio_id != 1)
$comercio_id = Auth::user()->comercio_id;
else
$comercio_id = Auth::user()->id;

// 7-8-2024

$configuracion_impresion = configuracion_impresion::where('user_id',Auth::user()->id)->first();

if($configuracion_impresion == null){
$size = 58;
$muestra_cta_cte = 0;
} else {
$size = $configuracion_impresion->size;
$muestra_cta_cte = $configuracion_impresion->muestra_cta_cte;
}

//

$data_factura = $this->GetDatosFactura($Id,$comercio_id);
      
$datos_facturacion = $data_factura[0];
$items = $data_factura[1];
$cliente = $data_factura[2];
$sale = $data_factura[7];
$fecha = $data_factura[4];
$user = $data_factura[5];
$venta = $data_factura[6];

//dd($sale);

$cliente_buscar = $cliente->first();
$cliente_base = ClientesMostrador::find($cliente_buscar->id);

$deuda = $this->GetCtaClienteClienteById($cliente_base->id);
$maximo_cta_cte = $cliente_base->monto_maximo_cuenta_corriente;

//$deuda = 0;
//$maximo_cta_cte = 0;

$data_return = $this->GetCodigoBarraAfip($Id,$comercio_id);
    
$codigo_barra_afip = $data_return[0];
$cae = $data_return[1];
    
$dataQR = $this->GetCodigoQRAfip($Id,$comercio_id);
    
$codigo_qr = $dataQR[0];
$codigoQR = $dataQR[1];

//dd($items,$sale,$user,$codigo_barra_afip,$datos_facturacion,$cliente,$codigoQR);

$pdf_factura = PDF::loadView('pdf.ticket-factura', compact('items','sale','user','codigo_barra_afip','datos_facturacion','cliente','codigoQR','venta','size','deuda','maximo_cta_cte','muestra_cta_cte'));

$nombre_ticket = 'Ticket_'.$factura_id.'.pdf';

return $pdf_factura->stream($nombre_ticket); // visualizar


    
}


public function TicketRapido($saleId)
{
$items = [];
$sale= [];

$items = cobro_rapidos_detalle::select(cobro_rapidos_detalle::raw('1 as quantity'),'cobro_rapidos_detalles.concepto as product_name','cobro_rapidos_detalles.monto as price','cobro_rapidos_detalles.iva')
->where('cobro_rapido_id',$saleId)->get();

$sale = cobro_rapido::select('cobro_rapidos.cae','cobro_rapidos.tipo_comprobante','cobro_rapidos.vto_cae','cobro_rapidos.nro_factura','cobro_rapidos.total','cobro_rapidos.items','cobro_rapidos.user_id','cobro_rapidos.cliente_id','cobro_rapidos.comercio_id','cobro_rapidos.iva',cobro_rapido::raw('(cobro_rapidos.total - cobro_rapidos.iva) as subtotal'))->find($saleId);

$cliente = ClientesMostrador::select('clientes_mostradors.*')->join('cobro_rapidos','cobro_rapidos.cliente_id','clientes_mostradors.id')->where('cobro_rapidos.id',$saleId)->first();

$user = cobro_rapido::join('users','users.id','cobro_rapidos.comercio_id')
->select('users.*')
->where('cobro_rapidos.id',$saleId)->first();


$ventaId = $saleId;
$ventaIdFactura = $saleId;


$datos_facturacion = datos_facturacion::where('comercio_id', $user->id)->first();


if($sale->cae != null) {

  /////////////// CODIGO DE BARRAS AFIP ///////////////////

  $this->total_total2 = cobro_rapido::join('metodo_pagos as m','m.id','cobro_rapidos.metodo_pago')
  ->select('cobro_rapidos.recargo','cobro_rapidos.tipo_comprobante','cobro_rapidos.created_at','cobro_rapidos.total','cobro_rapidos.created_at as fecha','m.nombre as metodo_pago','cobro_rapidos.cae','cobro_rapidos.vto_cae','cobro_rapidos.nro_factura')
  ->where('cobro_rapidos.id', $ventaId)
  ->first();

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $this->detalle_facturacion2 = datos_facturacion::where('datos_facturacions.comercio_id', $comercio_id)->first();

  /**
   * CUIT de la persona/empresa emitio la factura (11 caracteres)
   **/

  /**
   * Tipo de comprobante (2 caracteres, completado con 0's)
   **/
  if ($this->total_total2->tipo_comprobante == "A") {
   $tipo_de_comprobante = '01';
  }
  if ($this->total_total2->tipo_comprobante == "B") {
   $tipo_de_comprobante = '06';
  }
  if ($this->total_total2->tipo_comprobante == "C") {
   $tipo_de_comprobante = '011';
  }

  if ($this->total_total2->tipo_comprobante == "CF") {
   $tipo_de_comprobante = '099';
  }



  $cuit = $this->detalle_facturacion2->cuit;

  /**
   * Punto de venta (4 caracteres, completado con 0's)
   **/
   $porciones = explode("-", $this->total_total2->nro_factura);
   $tipo_factura = $porciones[0]; // porción1
   $pto_venta = $porciones[1]; // porción2
   $nro_factura_ = $porciones[2]; // porción2
   $this->pto_venta = str_pad($pto_venta, 4, "0", STR_PAD_LEFT);


  $punto_de_venta = $this->pto_venta;

  /**
   * CAE (14 caracteres)
   **/
  $cae = $this->total_total2->cae;

  /**
   * Fecha de expiracion del CAE (8 caracteres, formato aaaammdd)
   **/
  $this->vto_cae = Carbon::parse($this->total_total2->vto_cae)->format('Ymd');

  $vencimiento_cae = $this->vto_cae;


  $barcode = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;

  $code = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;

  //Step one
  $number_odd = 0;
  for ($i=0; $i < strlen($code); $i+=2) {
    $number_odd += $code[$i];
  }

  //Step two
  $number_odd *= 3;

  //Step three
  $number_even = 0;
  for ($i=1; $i < strlen($code); $i+=2) {
    $number_even += $code[$i];
  }

  //Step four
  $sum = $number_odd+$number_even;

  //Step five
  $checksum_char = 10 - ($sum % 10);

  $this->barcode_ultimo = $checksum_char == 10 ? 0 : $checksum_char;

  $barcode .= $this->barcode_ultimo;

  /**
   * Mostramos por pantalla el numero del codigo de barras de 40 caracteres
   **/
  $codigo_barra_afip = $barcode;


} else {
    $codigo_barra_afip = 0;
}


if($sale->cae != null) {


	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

	$this->datos_facturacion = datos_facturacion::where('comercio_id', $comercio_id)->first();


	if($this->datos_facturacion->cuit != null || $this->datos_facturacion->cuit != '') {


	$cuit =$this->datos_facturacion->cuit;

	$afip = new Afip(array('CUIT' =>$cuit , 'production' => true));

	/**
	* Numero del punto de venta
	**/
	$punto_de_venta = $this->datos_facturacion->pto_venta;

	$this->factura = cobro_rapido::join('clientes_mostradors','clientes_mostradors.id','cobro_rapidos.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','cobro_rapidos.nro_factura','cobro_rapidos.tipo_comprobante')->find($ventaIdFactura);


	if($this->factura->tipo_comprobante == 'C' || $this->factura->tipo_comprobante == 'CF') {

	  $tipo_de_comprobante = 11;


	}

	if($this->factura->tipo_comprobante == 'B') {
	  $tipo_de_comprobante = 6;
	}



	if($this->factura->tipo_comprobante == 'A') {
	$tipo_de_comprobante = 1;
	}


	$porciones = explode("-", $this->factura->nro_factura);
	$tipo_factura = $porciones[0]; // porción1
	$pto_venta = $porciones[1]; // porción2
	$nro_factura = $porciones[2]; // porción2
	$n = $porciones[2]; // porción2

	                                    /**
	* Numero de factura
	**/
	$numero_de_factura = $nro_factura;

	                    /**
	                     * Numero del punto de venta
	                     **/
	                    $punto_de_venta = $punto_de_venta;

	                    /**
	                     * Tipo de comprobante
	                     **/
	                    $tipo_de_comprobante = $tipo_de_comprobante; // 6 = Factura B

	                    /**
	                     * Informacion de la factura
	                     **/

	                    $informacion = $afip->ElectronicBilling->GetVoucherInfo($numero_de_factura, $punto_de_venta, $tipo_de_comprobante);

	                    if($informacion === NULL){
	                        echo 'La factura no existe';
	                    }
	                    else{
	                      /**
	                       * Mostramos por pantalla la información de la factura
	                       **

	                       dd($informacion);

	                       */

	                       $cuit = intval($cuit);
                           $numero_de_factura = intval($numero_de_factura);
                           $cae = intval($informacion->CodAutorizacion);
                           $dia = substr($informacion->CbteFch, -2);
                           $mes = substr($informacion->CbteFch, -4, 2);
                           $año = substr($informacion->CbteFch, -8, 4);

                           $fecha = $año."-".$mes."-".$dia;


	                                               // genero los datos para AFIP
	                        $url = 'https://www.afip.gob.ar/fe/qr/'; // URL que pide AFIP que se ponga en el QR.
	                        $datos_cmp_base_64 = json_encode([
	                            "ver" => 1,                         // Numérico 1 digito -  OBLIGATORIO – versión del formato de los datos del comprobante	1
	                            "fecha" => $fecha,            // full-date (RFC3339) - OBLIGATORIO – Fecha de emisión del comprobante
	                            "cuit" => $cuit,        // Numérico 11 dígitos -  OBLIGATORIO – Cuit del Emisor del comprobante
	                            "ptoVta" => $informacion->PtoVta,               // Numérico hasta 5 digitos - OBLIGATORIO – Punto de venta utilizado para emitir el comprobante
	                            "tipoCmp" => $informacion->CbteTipo,               // Numérico hasta 3 dígitos - OBLIGATORIO – tipo de comprobante (según Tablas del sistema. Ver abajo )
	                            "nroCmp" => $numero_de_factura,               // Numérico hasta 8 dígitos - OBLIGATORIO – Número del comprobante
	                            "importe" => $informacion->ImpTotal,         // Decimal hasta 13 enteros y 2 decimales - OBLIGATORIO – Importe Total del comprobante (en la moneda en la que fue emitido)
	                            "moneda" => "PES",                  // 3 caracteres - OBLIGATORIO – Moneda del comprobante (según Tablas del sistema. Ver Abajo )
	                            "ctz" => 1,                 // Decimal hasta 13 enteros y 6 decimales - OBLIGATORIO – Cotización en pesos argentinos de la moneda utilizada (1 cuando la moneda sea pesos)
	                            "tipoCodAut" => "E",                // string - OBLIGATORIO – “A” para comprobante autorizado por CAEA, “E” para comprobante autorizado por CAE
	                            "codAut" =>  $cae    // Numérico 14 dígitos -  OBLIGATORIO – Código de autorización otorgado por AFIP para el comprobante
	                        ]);


	                        $datos_cmp_base_64 = base64_encode($datos_cmp_base_64);
	                        $codigo_qr = $url.'?p='.$datos_cmp_base_64;

                            $codigoQR = QrCode::size(115)->generate($codigo_qr);
	                    }



	}


} else {
    $codigo_qr = 0;
    $codigoQR = 0;
}

//dd($codigo_qr,$codigoQR);

$pdf_factura = PDF::loadView('pdf.ticket_rapido', compact('items','sale','user','codigo_barra_afip','datos_facturacion','cliente','codigo_qr','codigoQR'));

$nombre_ticket = 'Ticket_'.$saleId.'.pdf';

return $pdf_factura->stream($nombre_ticket); // visualizar

}


public function PDFZeta($Uid, $dateFrom, $dateTo)
{
$items = [];
$sale= [];


if(Auth::user()->comercio_id != 1)
 $comercio_id = Auth::user()->comercio_id;
 else
 $comercio_id = Auth::user()->id;

 if($dateFrom !== '' || $dateTo !== '')
 {
 $from = Carbon::parse($dateFrom)->format('Y-m-d').' 00:00:00';
 $to = Carbon::parse($dateTo)->format('Y-m-d').' 23:59:59';

}

$cobro_rapido = cobro_rapido::where('comercio_id', $comercio_id)
->where('cobro_rapidos.cae','<>',null)
->whereBetween('cobro_rapidos.created_at', [$from, $to])
->select(cobro_rapido::raw('IFNULL(SUM(cobro_rapidos.subtotal),0) as subtotal'),cobro_rapido::raw('IFNULL(SUM(cobro_rapidos.total),0) as total'), cobro_rapido::raw('IFNULL(SUM(cobro_rapidos.iva),0) as iva')  )
->first();


$sale = Sale::where('comercio_id', $comercio_id)
->where('sales.cae','<>',null)
->whereBetween('sales.created_at', [$from, $to])
->select(Sale::raw('IFNULL(SUM(sales.total),0) as subtotal'),Sale::raw('IFNULL(SUM(sales.total + sales.iva),0) as total'), Sale::raw('IFNULL(SUM(sales.iva),0) as iva')  )
->first();


$datos_facturacion = datos_facturacion::where('comercio_id', $comercio_id)->first();

$ultima_factura_A = cobro_rapido::where('cobro_rapidos.tipo_comprobante','A')->where('comercio_id', $comercio_id)->orderBy('id','desc')->first();


$ultima_factura_B = cobro_rapido::where('cobro_rapidos.tipo_comprobante','B')->where('comercio_id', $comercio_id)->orderBy('id','desc')->first();

if($ultima_factura_A != null) {
    $ultima_factura_A = $ultima_factura_A;
} else {
    $ultima_factura_A = "";
}

if($ultima_factura_B != null) {
    $ultima_factura_B = $ultima_factura_B;
} else {
    $ultima_factura_B = "";
}



/**
$afip = new Afip(array('CUIT' => $datos_facturacion->cuit));


$punto_de_venta = $datos_facturacion->pto_venta;


$tipo_de_comprobante_A = 1; // 1 = Factura A

$ultima_factura_A = $afip->ElectronicBilling->GetLastVoucher($punto_de_venta, $tipo_de_comprobante_A);
 **/



$pdf_factura = PDF::loadView('pdf.informe_zeta', compact('sale','cobro_rapido','datos_facturacion','ultima_factura_A','ultima_factura_B','dateTo','dateFrom'));

$nombre_ticket = 'Zeta'.$Uid.'.pdf';

return $pdf_factura->stream($nombre_ticket); // visualizar

}



    public function Etiquetas($nombre_producto, $precio, $codigo, $codigo_barra, $fecha_impresion, $size, $producto_elegido)
{
    
    ini_set('memory_limit', '1024M');
    set_time_limit(3000000);

    $products = [];

    $nombre_producto = 1;

    $usuario_id = Auth::user()->id;

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $products = Product::where('comercio_id',$comercio_id)->get();



$pdf_factura = PDF::loadView('pdf.etiquetas', compact('products','nombre_producto','precio','codigo','codigo_barra','fecha_impresion'));

    return $pdf_factura->download('Etiquetas.pdf'); // descargar

//    return $pdf_factura->stream('Etiquetas.pdf'); // visualizar

}




   public function reportPDFCompra($Id)
  {
      $detalle_venta = [];
      $usuario = [];
      $detalle_cliente = [];
      $total_total = [];
      $fecha = [];
      $ventaId = $Id;
      $ventaIdFactura = $Id;

    $usuario_id = Auth::user()->id;

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $detalle_facturacion = proveedores::join('compras_proveedores','compras_proveedores.proveedor_id','proveedores.id')
    ->select('proveedores.*')
    ->where('compras_proveedores.id', $ventaId)
    ->get();

    $detalle_venta = detalle_compra_proveedores::where('detalle_compra_proveedores.compra_id', $ventaId)
    ->where('detalle_compra_proveedores.eliminado', 0)->get();


    $total_total = compras_proveedores::where('compras_proveedores.id', $ventaId)->get();

    $fecha = compras_proveedores::where('compras_proveedores.id', $ventaId)->select('created_at')->get();

  $pdf_factura = PDF::loadView('pdf.reporte-compra', compact('detalle_venta','Id','detalle_facturacion','total_total','fecha','ventaId'));

  return $pdf_factura->stream('Compra.pdf'); // visualizar

  }


   public function reportPDFCompraInsumos($Id)
  {
      $detalle_venta = [];
      $usuario = [];
      $detalle_cliente = [];
      $total_total = [];
      $fecha = [];
      $ventaId = $Id;
      $ventaIdFactura = $Id;

    $usuario_id = Auth::user()->id;

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $detalle_facturacion = proveedores::join('compras_insumos','compras_insumos.proveedor_id','proveedores.id')
    ->select('proveedores.*')
    ->where('compras_insumos.id', $ventaId)
    ->get();

    $detalle_venta = detalle_compra_insumos::where('detalle_compra_insumos.compra_id', $ventaId)
    ->where('detalle_compra_insumos.eliminado', 0)->get();


    $total_total = compras_insumos::where('compras_insumos.id', $ventaId)->get();

    $fecha = compras_insumos::where('compras_insumos.id', $ventaId)->select('created_at')->get();

  $pdf_factura = PDF::loadView('pdf.reporte-compra', compact('detalle_venta','Id','detalle_facturacion','total_total','fecha','ventaId'));

  return $pdf_factura->stream('Compra.pdf'); // visualizar

  }

  public function reportPDFPresupuesto($Id)
 {
     $detalle_venta = [];
     $usuario = [];
     $detalle_cliente = [];
     $total_total = [];
     $fecha = [];
     $ventaId = $Id;
     $ventaIdFactura = $Id;

     $usuario_id = Auth::user()->id;

     if(Auth::user()->comercio_id != 1)
     $comercio_id = Auth::user()->comercio_id;
     else
     $comercio_id = Auth::user()->id;


    $detalle_facturacion = datos_facturacion::join('users','users.id','datos_facturacions.comercio_id')->leftjoin('provincias','provincias.id','datos_facturacions.provincia')->select('datos_facturacions.*','users.email','users.phone','provincias.provincia')->where('datos_facturacions.comercio_id', $comercio_id)->get();

     $detalle_venta = presupuestos_detalle::join('products as p','p.id','presupuestos_detalles.producto_id')
     ->select('presupuestos_detalles.id','presupuestos_detalles.precio','presupuestos_detalles.recargo','presupuestos_detalles.descuento','presupuestos_detalles.alicuota_iva','presupuestos_detalles.cantidad','presupuestos_detalles.nombre as product', 'presupuestos_detalles.iva','presupuestos_detalles.barcode', presupuestos_detalle::raw('presupuestos_detalles.precio*presupuestos_detalles.cantidad as total'))
     ->where('presupuestos_detalles.presupuesto_id', $Id)
     ->get();


     $detalle_cliente = presupuestos::join('clientes_mostradors as c','c.id','presupuestos.cliente_id')
     ->select('c.id','c.nombre','c.telefono','c.email','c.dni','c.direccion','c.barrio','c.localidad','c.provincia','presupuestos.observaciones','presupuestos.metodo_pago')
     ->where('presupuestos.id', $Id)
     ->get();

     $total_total = presupuestos::join('metodo_pagos as m','m.id','presupuestos.metodo_pago')
        ->select('presupuestos.recargo','presupuestos.vigencia','presupuestos.tipo_comprobante','presupuestos.created_at','presupuestos.descuento','presupuestos.total','presupuestos.subtotal','presupuestos.created_at as fecha','presupuestos.observaciones', 'm.nombre as metodo_pago','presupuestos.iva')
        ->where('presupuestos.id', $Id)
        ->get();

       $fecha = presupuestos::select('presupuestos.created_at','presupuestos.vigencia')
        ->where('presupuestos.id', $Id)
        ->get();


        $usuario = User::join('presupuestos as s','s.comercio_id','users.id')
         ->select('users.image','users.name')
        ->where('s.id', $Id)
        ->get();


         if(Auth::user()->comercio_id != 1)
         $comercio_id = Auth::user()->comercio_id;
         else
         $comercio_id = Auth::user()->id;


         $this->detalle_facturacion2 = datos_facturacion::where('datos_facturacions.comercio_id', $comercio_id)->first();


 $pdf_factura = PDF::loadView('pdf.reporte-presupuesto', compact('detalle_venta','Id','detalle_cliente','detalle_facturacion','total_total','fecha','usuario','ventaId'));


     return $pdf_factura->stream('Presupuesto.pdf'); // visualizar

 }


    public function reportPDF($usuarioSeleccionado, $clienteId, $dateFrom = null, $dateTo = null)
    {
        $data = [];

        $this->cId = $clienteId;
        $this->uId = $usuarioSeleccionado;
        $this->userId = explode(",", $usuarioSeleccionado);
        $this->clienteId = explode(",", $clienteId);


       $from = Carbon::parse($dateFrom)->format('Y-m-d') . ' 00:00:00';
       $to = Carbon::parse($dateTo)->format('Y-m-d')     . ' 23:59:59';

       if($this->cId == 0)
       {

       if($this->uId == 0)
       {
         if(Auth::user()->comercio_id != 1)
         $comercio_id = Auth::user()->comercio_id;
         else
         $comercio_id = Auth::user()->id;

        $data = Sale::join('users as u','u.id','sales.user_id')
        ->select('sales.*','u.name as user')
        ->whereBetween('sales.created_at', [$from, $to])
        ->where('sales.comercio_id', $comercio_id)
        ->get();
        } else {
        $data = Sale::join('users as u','u.id','sales.user_id')
        ->select('sales.*','u.name as user')
        ->whereBetween('sales.created_at', [$from, $to])
        ->whereIn('sales.user_id', $this->userId)
        ->get();
        }

        } else {


        if($this->uId == 0)
        {
         $data = Sale::join('users as u','u.id','sales.user_id')
         ->select('sales.*','u.name as user')
         ->whereBetween('sales.created_at', [$from, $to])
         ->whereIn('sales.cliente_id', $this->clienteId)
         ->get();
        } else {
         $data = Sale::join('users as u','u.id','sales.user_id')
         ->select('sales.*','u.name as user')
         ->whereBetween('sales.created_at', [$from, $to])
         ->whereIn('sales.cliente_id', $this->clienteId)
         ->whereIn('sales.user_id', $this->userId)
         ->get();
          }

      }

    $pdf = PDF::loadView('pdf.reporte', compact('data','dateFrom','dateTo'));

Mail::send('emails/templates/send-invoice', $messageData, function ($mail) use ($pdf) {
    $mail->from('andrespasquetta@gmail.com', 'Flaminco');
    $mail->to('andrespasquetta@gmail.com');
    $mail->attachData($pdf->output(), 'test.pdf');
});
/*
    $pdf = new DOMPDF();
    $pdf->setBasePath(realpath(APPLICATION_PATH . '/css/'));
    $pdf->loadHtml($html);
    $pdf->render();
    */
    /*
    $pdf->set_protocol(WWW_ROOT);
    $pdf->set_base_path('/');
*/

        return $pdf->stream('salesReport.pdf'); // visualizar
        //$customReportName = 'salesReport_'.Carbon::now()->format('Y-m-d').'.pdf';
        //return $pdf->download($customReportName); //descargar

    }

   public function PDFFactura($Id)
  {
    //  dd($Id);
      $detalle_venta = [];
      $usuario = [];
      $detalle_cliente = [];
      $total_total = [];
      $fecha = [];
      $ventaId = $Id;
      $ventaIdFactura = $Id;

      $usuario_id = Auth::user()->id;

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

      $data_factura = $this->GetDatosFactura($Id,$comercio_id);
      
      $detalle_facturacion = $data_factura[0];
      $detalle_venta = $data_factura[1];
      $detalle_cliente = $data_factura[2];
      $total_total = $data_factura[3];
      $fecha = $data_factura[4];
      $usuario = $data_factura[5];
      $venta = $data_factura[6];

      //dd($detalle_facturacion,$detalle_venta,$detalle_cliente,$total_total,$fecha,$usuario);
    
      /////////////// CODIGO DE BARRAS AFIP ///////////////////
    
      $data_return = $this->GetCodigoBarraAfip($Id,$comercio_id);
    
      $codigo_barra_afip = $data_return[0];
      $cae = $data_return[1];
    
      $dataQR = $this->GetCodigoQRAfip($Id,$comercio_id);
    
      $codigo_qr = $dataQR[0];
      $codigoQR = $dataQR[1];
    
      $pdf_factura = PDF::loadView('pdf.factura', compact('detalle_venta','Id','detalle_cliente','detalle_facturacion','total_total','fecha','usuario','ventaId','codigo_barra_afip','cae','codigoQR','venta'));
    
    
      return $pdf_factura->stream('Venta.pdf'); // visualizar

  }
 
   public function emailPDFFactura($Id, $email)
  {
      $detalle_venta = [];
      $usuario = [];
      $detalle_cliente = [];
      $total_total = [];
      $fecha = [];
      $ventaId = $Id;
      $ventaIdFactura = $Id;

      $usuario_id = Auth::user()->id;

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

      $data_factura = $this->GetDatosFactura($Id,$comercio_id);
      
      $detalle_facturacion = $data_factura[0];
      $detalle_venta = $data_factura[1];
      $detalle_cliente = $data_factura[2];
      $total_total = $data_factura[3];
      $fecha = $data_factura[4];
      $usuario = $data_factura[5];
      $venta = $data_factura[6];

      //dd($detalle_facturacion,$detalle_venta,$detalle_cliente,$total_total,$fecha,$usuario);
    
      /////////////// CODIGO DE BARRAS AFIP ///////////////////
    
      $data_return = $this->GetCodigoBarraAfip($Id,$comercio_id);
    
      $codigo_barra_afip = $data_return[0];
      $cae = $data_return[1];
    
      $dataQR = $this->GetCodigoQRAfip($Id,$comercio_id);
    
      $codigo_qr = $dataQR[0];
      $codigoQR = $dataQR[1];

      $fecha = Sale::select('sales.created_at')
      ->where('sales.id', $Id)
      ->get();

      $pdf = PDF::loadView('pdf.factura', compact('detalle_venta','Id','detalle_cliente','total_total','fecha','usuario','ventaId','codigo_barra_afip','cae','detalle_facturacion','codigoQR','venta'));

  
        $data["email"] = $email;
        $data["title"] = "Factura";
        $data["body"] = "A continuacion se adjunta la factura.";
        
        Mail::send('mail', $data, function ($message) use ($data, $pdf) {
            $message->to($data["email"], $data["email"])
                ->subject($data["title"])
                ->attachData($pdf->output(), "Factura .pdf");
        });
        
        return redirect()->back()->with('status', 'Mail enviado correctamente.'); 

  }
  
    public function GetDatosFactura($Id,$comercio_id) {
      
      $facturacion = facturacion::find($Id);
    //  dd($facturacion);
      
      $detalle_facturacion = datos_facturacion::join('users','users.id','datos_facturacions.comercio_id')
      ->leftjoin('provincias','provincias.id','datos_facturacions.provincia')
      ->select('datos_facturacions.*','users.email','users.phone','provincias.provincia')
      ->where('datos_facturacions.id', $facturacion->datos_facturacion_id)->get();

      $detalle_venta = detalle_facturacion::join('products as p','p.id','detalle_facturacions.product_id')
      ->select('detalle_facturacions.*')
      ->where('detalle_facturacions.factura_id', $Id)
      ->get();

      $detalle_cliente = facturacion::join('clientes_mostradors as c','c.id','facturacions.cliente_id')
      ->select('c.id','c.nombre','c.telefono','c.email','c.dni','c.direccion','c.barrio','c.localidad','c.provincia')
      ->where('facturacions.id', $Id)
      ->get();

      $total_total = facturacion::select('facturacions.created_at','facturacions.total','facturacions.subtotal','facturacions.created_at as fecha','facturacions.cae','facturacions.tipo_comprobante','facturacions.vto_cae','facturacions.nro_factura','facturacions.iva','facturacions.sale_id')
         ->where('facturacions.id', $Id)
         ->get();

      $fecha = facturacion::select('facturacions.created_at')
         ->where('facturacions.id', $Id)
         ->get();


      $usuario = User::join('facturacions','facturacions.comercio_id','users.id')
         ->select('users.image','users.name')
         ->where('facturacions.id', $Id)
         ->get();
         
       foreach($total_total as $t) {
       $sale_id = $t->sale_id;    
       }   
       
      $venta = Sale::join('metodo_pagos','metodo_pagos.id','sales.metodo_pago')->select('sales.*','metodo_pagos.nombre as metodo_pago')->find($sale_id);
    //  dd($venta);
      
      $factura = facturacion::select('facturacions.created_at','facturacions.total','facturacions.subtotal','facturacions.created_at as fecha','facturacions.cae','facturacions.tipo_comprobante','facturacions.vto_cae','facturacions.nro_factura','facturacions.iva','facturacions.sale_id')
         ->where('facturacions.id', $Id)
         ->first();
         
      return [$detalle_facturacion,$detalle_venta,$detalle_cliente,$total_total,$fecha,$usuario,$venta,$factura];
  }
  
  public function GetDatosFacturaOld($Id,$comercio_id) {
      
      $detalle_facturacion = datos_facturacion::join('users','users.id','datos_facturacions.comercio_id')->leftjoin('provincias','provincias.id','datos_facturacions.provincia')->select('datos_facturacions.*','users.email','users.phone','provincias.provincia')->where('datos_facturacions.comercio_id', $comercio_id)->get();

      $detalle_venta = detalle_facturacion::join('products as p','p.id','detalle_facturacions.product_id')
      ->select('detalle_facturacions.*')
      ->where('detalle_facturacions.factura_id', $Id)
      ->get();

      $detalle_cliente = facturacion::join('clientes_mostradors as c','c.id','facturacions.cliente_id')
      ->select('c.id','c.nombre','c.telefono','c.email','c.dni','c.direccion','c.barrio','c.localidad','c.provincia')
      ->where('facturacions.id', $Id)
      ->get();

      $total_total = facturacion::select('facturacions.created_at','facturacions.total','facturacions.subtotal','facturacions.created_at as fecha','facturacions.cae','facturacions.tipo_comprobante','facturacions.vto_cae','facturacions.nro_factura','facturacions.iva','facturacions.sale_id')
         ->where('facturacions.id', $Id)
         ->get();

      $fecha = facturacion::select('facturacions.created_at')
         ->where('facturacions.id', $Id)
         ->get();


      $usuario = User::join('facturacions','facturacions.comercio_id','users.id')
         ->select('users.image','users.name')
         ->where('facturacions.id', $Id)
         ->get();
         
       foreach($total_total as $t) {
       $sale_id = $t->sale_id;    
       }   
       
      $venta = Sale::join('metodo_pagos','metodo_pagos.id','sales.metodo_pago')->select('sales.*','metodo_pagos.nombre as metodo_pago')->find($sale_id);

      $factura = facturacion::select('facturacions.created_at','facturacions.total','facturacions.subtotal','facturacions.created_at as fecha','facturacions.cae','facturacions.tipo_comprobante','facturacions.vto_cae','facturacions.nro_factura','facturacions.iva','facturacions.sale_id')
         ->where('facturacions.id', $Id)
         ->first();
         
      return [$detalle_facturacion,$detalle_venta,$detalle_cliente,$total_total,$fecha,$usuario,$venta,$factura];
  }

  public function GetCodigoBarraAfip($factura_id,$comercio_id){
    
     $factura = facturacion::select('*')
     ->where('facturacions.id', $factura_id)
     ->first();
  
    $detalle_facturacion = datos_facturacion::where('datos_facturacions.comercio_id', $comercio_id)->first();

    if($detalle_facturacion != null) {
    
      /**
       * CUIT de la persona/empresa emitio la factura (11 caracteres)
       **/
        $cuit = $detalle_facturacion->cuit;
    
        if($factura->nro_factura != null){
    
      /**
       * Punto de venta (4 caracteres, completado con 0's)
       **/
       $porciones = explode("-", $factura->nro_factura);
       $tipo_factura = $porciones[0]; // porción1
       $pto_venta = $porciones[1]; // porción2
       $nro_factura_ = $porciones[2]; // porción2
       $this->pto_venta = str_pad($pto_venta, 4, "0", STR_PAD_LEFT);
    
    
       $punto_de_venta = $this->pto_venta;
    
      $tipo_de_comprobante = $this->GetTipoComprobante($tipo_factura);
      /**
       * Tipo de comprobante (2 caracteres, completado con 0's)
       **/

    
      /**
       * CAE (14 caracteres)
       **/
      $cae = $factura->cae;
    
      /**
       * Fecha de expiracion del CAE (8 caracteres, formato aaaammdd)
       **/
      $this->vto_cae = Carbon::parse($factura->vto_cae)->format('Ymd');
    
      $vencimiento_cae = $this->vto_cae;
    
    
      $barcode = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;
    
      $code = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;
    
      //Step one
      $number_odd = 0;
      for ($i=0; $i < strlen($code); $i+=2) {
        $number_odd += $code[$i];
      }
    
      //Step two
      $number_odd *= 3;
    
      //Step three
      $number_even = 0;
      for ($i=1; $i < strlen($code); $i+=2) {
        $number_even += $code[$i];
      }
    
      //Step four
      $sum = $number_odd+$number_even;
    
      //Step five
      $checksum_char = 10 - ($sum % 10);
    
      $this->barcode_ultimo = $checksum_char == 10 ? 0 : $checksum_char;
    
      $barcode .= $this->barcode_ultimo;
    
      /**
       * Mostramos por pantalla el numero del codigo de barras de 40 caracteres
       **/
      $codigo_barra_afip = $barcode;
    
        } else {
             $codigo_barra_afip = "";
            $cae = "";
        }
    
    } else {
      $codigo_barra_afip = "";
      $cae = "";
    }     
    
    
    return [$codigo_barra_afip,$cae];
  }


  public function GetCodigoBarraAfipOld($factura_id,$comercio_id){
    
    
     $factura = facturacion::select('*')
     ->where('facturacions.id', $factura_id)
     ->first();
  
    $detalle_facturacion = datos_facturacion::where('datos_facturacions.comercio_id', $comercio_id)->first();

    if($detalle_facturacion != null) {
    
      /**
       * CUIT de la persona/empresa emitio la factura (11 caracteres)
       **/
        $cuit = $detalle_facturacion->cuit;
    
        if($factura->nro_factura != null){
    
      /**
       * Punto de venta (4 caracteres, completado con 0's)
       **/
       $porciones = explode("-", $factura->nro_factura);
       $tipo_factura = $porciones[0]; // porción1
       $pto_venta = $porciones[1]; // porción2
       $nro_factura_ = $porciones[2]; // porción2
       $this->pto_venta = str_pad($pto_venta, 4, "0", STR_PAD_LEFT);
    
    
       $punto_de_venta = $this->pto_venta;
    
      $tipo_de_comprobante = $this->GetTipoComprobante($tipo_factura);
      /**
       * Tipo de comprobante (2 caracteres, completado con 0's)
       **/

    
      /**
       * CAE (14 caracteres)
       **/
      $cae = $factura->cae;
    
      /**
       * Fecha de expiracion del CAE (8 caracteres, formato aaaammdd)
       **/
      $this->vto_cae = Carbon::parse($factura->vto_cae)->format('Ymd');
    
      $vencimiento_cae = $this->vto_cae;
    
    
      $barcode = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;
    
      $code = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;
    
      //Step one
      $number_odd = 0;
      for ($i=0; $i < strlen($code); $i+=2) {
        $number_odd += $code[$i];
      }
    
      //Step two
      $number_odd *= 3;
    
      //Step three
      $number_even = 0;
      for ($i=1; $i < strlen($code); $i+=2) {
        $number_even += $code[$i];
      }
    
      //Step four
      $sum = $number_odd+$number_even;
    
      //Step five
      $checksum_char = 10 - ($sum % 10);
    
      $this->barcode_ultimo = $checksum_char == 10 ? 0 : $checksum_char;
    
      $barcode .= $this->barcode_ultimo;
    
      /**
       * Mostramos por pantalla el numero del codigo de barras de 40 caracteres
       **/
      $codigo_barra_afip = $barcode;
    
        } else {
             $codigo_barra_afip = "";
            $cae = "";
        }
    
    } else {
      $codigo_barra_afip = "";
      $cae = "";
    }     
    
    
    return [$codigo_barra_afip,$cae];
  }
  
  public function GetDatosUsuario($comercio_id){
          $datos = datos_facturacion::join('users','users.id','datos_facturacions.comercio_id')->leftjoin('provincias','provincias.id','datos_facturacions.provincia')->select('datos_facturacions.*','users.name as razon_social','users.email','users.phone','provincias.provincia')->where('datos_facturacions.comercio_id', $comercio_id)->where('datos_facturacions.eliminado',0)->limit(1)->get();
          if($datos->count() < 1){
          $datos = User::select('users.name as razon_social','users.email','users.phone')->where('users.id', $comercio_id)->limit(1)->get();
          }
          return $datos;
  }
  
  
   public function reportPDFFactura($Id)
  {
      $detalle_venta = [];
      $usuario = [];
      $detalle_cliente = [];
      $total_total = [];
      $fecha = [];
      $ventaIdFactura = $Id;

      $usuario_id = Auth::user()->id;

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

      $detalle_facturacion = $this->GetDatosUsuario($comercio_id);

      $detalle_venta = SaleDetail::join('products as p','p.id','sale_details.product_id')
      ->select(
      'sale_details.id','sale_details.price','sale_details.descuento','sale_details.recargo','sale_details.quantity',
      'sale_details.product_name as product', 'sale_details.iva','sale_details.product_barcode as barcode',
      'p.stock','p.stock_descubierto', SaleDetail::raw('sale_details.price*sale_details.quantity as total'),
      'sale_details.nombre_promo','sale_details.cantidad_promo','sale_details.descuento_promo'
      )
      ->where('sale_details.sale_id', $Id)
      ->where('sale_details.eliminado', 0)
      ->get();


      $detalle_cliente = Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
      ->select('c.id','c.nombre','c.telefono','c.email','c.dni','c.direccion','c.barrio','c.localidad','c.provincia','sales.observaciones','sales.metodo_pago','sales.fecha_entrega')
      ->where('sales.id', $Id)
      ->get();

      $total_total = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
         ->select('sales.recargo','sales.tipo_comprobante','sales.created_at','sales.descuento','sales.descuento_promo','sales.total','sales.subtotal','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago','sales.cae','sales.tipo_comprobante','sales.vto_cae','sales.nro_factura','sales.iva')
         ->where('sales.id', $Id)
         ->get();

        $fecha = Sale::select('sales.created_at')
         ->where('sales.id', $Id)
         ->get();


         $usuario = User::join('sales as s','s.comercio_id','users.id')
          ->select('users.image','users.name')
         ->where('s.id', $Id)
         ->get();



            /////////////// CODIGO DE BARRAS AFIP ///////////////////

  $this->total_total2 = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
  ->select('sales.nro_venta','sales.recargo','sales.tipo_comprobante','sales.created_at','sales.descuento','sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago','sales.cae','sales.vto_cae','sales.nro_factura')
  ->where('sales.id', $Id)
  ->first();
  
  $ventaId =  $this->total_total2->nro_venta; 
      
  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;


  $this->detalle_facturacion2 = datos_facturacion::where('datos_facturacions.comercio_id', $comercio_id)->first();

if($this->detalle_facturacion2 != null) {

  /**
   * CUIT de la persona/empresa emitio la factura (11 caracteres)
   **/

  /**
   * Tipo de comprobante (2 caracteres, completado con 0's)
   **/
  if ($this->total_total2->tipo_comprobante == "A") {
   $tipo_de_comprobante = '01';
  }
  if ($this->total_total2->tipo_comprobante == "B") {
   $tipo_de_comprobante = '06';
  }
  if ($this->total_total2->tipo_comprobante == "C") {
   $tipo_de_comprobante = '011';
  }
  if ($this->total_total2->tipo_comprobante == "CF") {
   $tipo_de_comprobante = '099';
  }


  $cuit = $this->detalle_facturacion2->cuit;

    if($this->total_total2->nro_factura != null){

  /**
   * Punto de venta (4 caracteres, completado con 0's)
   **/
   $porciones = explode("-", $this->total_total2->nro_factura);
   $tipo_factura = $porciones[0]; // porción1
   $pto_venta = $porciones[1]; // porción2
   $nro_factura_ = $porciones[2]; // porción2
   $this->pto_venta = str_pad($pto_venta, 4, "0", STR_PAD_LEFT);


  $punto_de_venta = $this->pto_venta;

  /**
   * CAE (14 caracteres)
   **/
  $cae = $this->total_total2->cae;

  /**
   * Fecha de expiracion del CAE (8 caracteres, formato aaaammdd)
   **/
  $this->vto_cae = Carbon::parse($this->total_total2->vto_cae)->format('Ymd');

  $vencimiento_cae = $this->vto_cae;


  $barcode = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;

  $code = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;

  //Step one
  $number_odd = 0;
  for ($i=0; $i < strlen($code); $i+=2) {
    $number_odd += $code[$i];
  }

  //Step two
  $number_odd *= 3;

  //Step three
  $number_even = 0;
  for ($i=1; $i < strlen($code); $i+=2) {
    $number_even += $code[$i];
  }

  //Step four
  $sum = $number_odd+$number_even;

  //Step five
  $checksum_char = 10 - ($sum % 10);

  $this->barcode_ultimo = $checksum_char == 10 ? 0 : $checksum_char;

  $barcode .= $this->barcode_ultimo;

  /**
   * Mostramos por pantalla el numero del codigo de barras de 40 caracteres
   **/
  $codigo_barra_afip = $barcode;

    } else {
         $codigo_barra_afip = "";
        $cae = "";
    }

} else {
  $codigo_barra_afip = "";
  $cae = "";
}

if($this->detalle_facturacion2 != null) {


	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

	$this->datos_facturacion = datos_facturacion::where('comercio_id', $comercio_id)->first();


	if($this->datos_facturacion->cuit != null || $this->datos_facturacion->cuit != '') {


	$cuit =$this->datos_facturacion->cuit;

	$afip = new Afip(array('CUIT' =>$cuit , 'production' => true));

	/**
	* Numero del punto de venta
	**/
	$punto_de_venta = $this->datos_facturacion->pto_venta;

	$this->factura = Sale::join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','sales.nro_factura','sales.tipo_comprobante')->find($ventaIdFactura);


	if($this->factura->tipo_comprobante == 'C' || $this->factura->tipo_comprobante == 'CF') {

	  $tipo_de_comprobante = 11;


	}

	if($this->factura->tipo_comprobante == 'B') {
	  $tipo_de_comprobante = 6;
	}



	if($this->factura->tipo_comprobante == 'A') {
	$tipo_de_comprobante = 1;
	}

	if($this->factura->nro_factura != null) {
	    	$porciones = explode("-", $this->factura->nro_factura);
	$tipo_factura = $porciones[0]; // porción1
	$pto_venta = $porciones[1]; // porción2
	$nro_factura = $porciones[2]; // porción2
	$n = $porciones[2]; // porción2

	                                    /**
	* Numero de factura
	**/
	$numero_de_factura = $nro_factura;

	                    /**
	                     * Numero del punto de venta
	                     **/
	                    $punto_de_venta = $punto_de_venta;

	                    /**
	                     * Tipo de comprobante
	                     **/
	                    $tipo_de_comprobante = $tipo_de_comprobante; // 6 = Factura B

	                    /**
	                     * Informacion de la factura
	                     **/

	                    $informacion = $afip->ElectronicBilling->GetVoucherInfo($numero_de_factura, $punto_de_venta, $tipo_de_comprobante);

	                    if($informacion === NULL){
	                        echo 'La factura no existe';
	                    }
	                    else{
	                      /**
	                       * Mostramos por pantalla la información de la factura
	                       **

	                       dd($informacion);

	                       */

	                       $cuit = intval($cuit);
                           $numero_de_factura = intval($numero_de_factura);
                           $cae = intval($informacion->CodAutorizacion);
                           $dia = substr($informacion->CbteFch, -2);
                           $mes = substr($informacion->CbteFch, -4, 2);
                           $año = substr($informacion->CbteFch, -8, 4);

                           $fecha = $año."-".$mes."-".$dia;


	                                               // genero los datos para AFIP
	                        $url = 'https://www.afip.gob.ar/fe/qr/'; // URL que pide AFIP que se ponga en el QR.
	                        $datos_cmp_base_64 = json_encode([
	                            "ver" => 1,                         // Numérico 1 digito -  OBLIGATORIO – versión del formato de los datos del comprobante	1
	                            "fecha" => $fecha,            // full-date (RFC3339) - OBLIGATORIO – Fecha de emisión del comprobante
	                            "cuit" => $cuit,        // Numérico 11 dígitos -  OBLIGATORIO – Cuit del Emisor del comprobante
	                            "ptoVta" => $informacion->PtoVta,               // Numérico hasta 5 digitos - OBLIGATORIO – Punto de venta utilizado para emitir el comprobante
	                            "tipoCmp" => $informacion->CbteTipo,               // Numérico hasta 3 dígitos - OBLIGATORIO – tipo de comprobante (según Tablas del sistema. Ver abajo )
	                            "nroCmp" => $numero_de_factura,               // Numérico hasta 8 dígitos - OBLIGATORIO – Número del comprobante
	                            "importe" => $informacion->ImpTotal,         // Decimal hasta 13 enteros y 2 decimales - OBLIGATORIO – Importe Total del comprobante (en la moneda en la que fue emitido)
	                            "moneda" => "PES",                  // 3 caracteres - OBLIGATORIO – Moneda del comprobante (según Tablas del sistema. Ver Abajo )
	                            "ctz" => 1,                 // Decimal hasta 13 enteros y 6 decimales - OBLIGATORIO – Cotización en pesos argentinos de la moneda utilizada (1 cuando la moneda sea pesos)
	                            "tipoCodAut" => "E",                // string - OBLIGATORIO – “A” para comprobante autorizado por CAEA, “E” para comprobante autorizado por CAE
	                            "codAut" =>  $cae    // Numérico 14 dígitos -  OBLIGATORIO – Código de autorización otorgado por AFIP para el comprobante
	                        ]);


	                        $datos_cmp_base_64 = base64_encode($datos_cmp_base_64);
	                        $codigo_qr = $url.'?p='.$datos_cmp_base_64;

                            $codigoQR = QrCode::size(90)->generate($codigo_qr);
	                    }

	} else {
        $codigo_qr = 0;
	    $codigoQR = 0;
	}


	} else {
	$codigo_qr = 0;
    $codigoQR = 0;
	}


} else {
    $codigo_qr = 0;
    $codigoQR = 0;
}


  $pdf_factura = PDF::loadView('pdf.reporte-factura', compact('detalle_venta','Id','detalle_cliente','detalle_facturacion','total_total','fecha','usuario','ventaId','codigo_barra_afip','cae','codigoQR'));


   return $pdf_factura->stream('Venta.pdf'); // visualizar

  }

   public function emailPDFVenta($Id, $email)
  {
      
      /// AKA //
      $detalle_venta = [];
      $detalle_cliente = [];
      $total_total = [];
      $fecha = [];
      $usuario = [];

      $ventaId = $Id;
      $ventaIdFactura = $Id;

      $usuario_id = Auth::user()->id;

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;


    //$detalle_facturacion = datos_facturacion::join('users','users.id','datos_facturacions.comercio_id')->leftjoin('provincias','provincias.id','datos_facturacions.provincia')->select('datos_facturacions.*','users.email','users.phone','provincias.provincia')->where('datos_facturacions.comercio_id', $comercio_id)->get();
    
    $detalle_facturacion = $this->GetDatosUsuario($comercio_id);
    
    //  $detalle_venta = SaleDetail::join('products as p','p.id','sale_details.product_id')
    //  ->select('sale_details.id','sale_details.price','sale_details.quantity','sale_details.product_name as product', 'sale_details.iva','sale_details.product_barcode as barcode','p.stock','p.stock_descubierto', SaleDetail::raw('sale_details.price*sale_details.quantity as total'))
    //  ->where('sale_details.sale_id', $Id)
    //  ->where('sale_details.eliminado', 0)
    //  ->get();
      
      
            $detalle_venta = SaleDetail::join('products as p','p.id','sale_details.product_id')
      ->select('sale_details.nombre_promo','sale_details.cantidad_promo','sale_details.descuento_promo','sale_details.id','sale_details.price','sale_details.descuento','sale_details.recargo','sale_details.quantity','sale_details.product_name as product', 'sale_details.iva','sale_details.product_barcode as barcode','p.stock','p.stock_descubierto', SaleDetail::raw('sale_details.price*sale_details.quantity as total'))
      ->where('sale_details.sale_id', $Id)
      ->where('sale_details.eliminado', 0)
      ->get();


      $detalle_cliente = Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
      ->select('c.id','c.nombre','c.telefono','c.email','c.dni','c.direccion','c.barrio','c.localidad','c.provincia','sales.observaciones','sales.metodo_pago','sales.fecha_entrega')
      ->where('sales.id', $Id)
      ->get();

      $total_total = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
         ->select('sales.recargo','sales.tipo_comprobante','sales.created_at','sales.descuento','sales.descuento_promo','sales.subtotal','sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago','sales.cae','sales.tipo_comprobante','sales.vto_cae','sales.nro_factura','sales.iva')
         ->where('sales.id', $Id)
         ->get();

        $fecha = Sale::select('sales.created_at')
         ->where('sales.id', $Id)
         ->get();


         $usuario = User::join('sales as s','s.comercio_id','users.id')
          ->select('users.image','users.name')
         ->where('s.id', $Id)
         ->get();


                     /////////////// CODIGO DE BARRAS AFIP ///////////////////

  $this->total_total2 = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
  ->select('sales.nro_venta','sales.recargo','sales.tipo_comprobante','sales.created_at','sales.descuento','sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago','sales.cae','sales.vto_cae','sales.nro_factura')
  ->where('sales.id', $Id)
  ->first();

  $ventaId = $this->total_total2->nro_venta;
  
  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $this->detalle_facturacion2 = datos_facturacion::where('datos_facturacions.comercio_id', $comercio_id)->first();

  /**
   * CUIT de la persona/empresa emitio la factura (11 caracteres)
   **/

  /**
   * Tipo de comprobante (2 caracteres, completado con 0's)
   **/
  if ($this->total_total2->tipo_comprobante == "A") {
   $tipo_de_comprobante = '01';
  }
  if ($this->total_total2->tipo_comprobante == "B") {
   $tipo_de_comprobante = '06';
  }
  if ($this->total_total2->tipo_comprobante == "C") {
   $tipo_de_comprobante = '011';
  }

  $this->detalle_facturacion2 = datos_facturacion::where('datos_facturacions.comercio_id', $comercio_id)->first();

if($this->detalle_facturacion2 != null) {

  /**
   * CUIT de la persona/empresa emitio la factura (11 caracteres)
   **/

  /**
   * Tipo de comprobante (2 caracteres, completado con 0's)
   **/
  if ($this->total_total2->tipo_comprobante == "A") {
   $tipo_de_comprobante = '01';
  }
  if ($this->total_total2->tipo_comprobante == "B") {
   $tipo_de_comprobante = '06';
  }
  if ($this->total_total2->tipo_comprobante == "C") {
   $tipo_de_comprobante = '011';
  }
    if ($this->total_total2->tipo_comprobante == "CF") {
   $tipo_de_comprobante = '099';
  }


  $cuit = $this->detalle_facturacion2->cuit;

    if($this->total_total2->nro_factura != null){
  /**
   * Punto de venta (4 caracteres, completado con 0's)
   **/
   $porciones = explode("-", $this->total_total2->nro_factura);
   $tipo_factura = $porciones[0]; // porción1
   $pto_venta = $porciones[1]; // porción2
   $nro_factura_ = $porciones[2]; // porción2
   $this->pto_venta = str_pad($pto_venta, 4, "0", STR_PAD_LEFT);


  $punto_de_venta = $this->pto_venta;

  /**
   * CAE (14 caracteres)
   **/
  $cae = $this->total_total2->cae;

  /**
   * Fecha de expiracion del CAE (8 caracteres, formato aaaammdd)
   **/
  $this->vto_cae = Carbon::parse($this->total_total2->vto_cae)->format('Ymd');

  $vencimiento_cae = $this->vto_cae;


  $barcode = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;

  $code = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;

  //Step one
  $number_odd = 0;
  for ($i=0; $i < strlen($code); $i+=2) {
    $number_odd += $code[$i];
  }

  //Step two
  $number_odd *= 3;

  //Step three
  $number_even = 0;
  for ($i=1; $i < strlen($code); $i+=2) {
    $number_even += $code[$i];
  }

  //Step four
  $sum = $number_odd+$number_even;

  //Step five
  $checksum_char = 10 - ($sum % 10);

  $this->barcode_ultimo = $checksum_char == 10 ? 0 : $checksum_char;

  $barcode .= $this->barcode_ultimo;

  /**
   * Mostramos por pantalla el numero del codigo de barras de 40 caracteres
   **/


  $codigo_barra_afip = $barcode;

    } else {
  $codigo_barra_afip = "";
  $cae = "";
    }

} else {
  $codigo_barra_afip = "";
  $cae = "";
}

if($this->detalle_facturacion2 != null) {


	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

	$this->datos_facturacion = datos_facturacion::where('comercio_id', $comercio_id)->first();


	if($this->datos_facturacion->cuit != null || $this->datos_facturacion->cuit != '') {


	$cuit =$this->datos_facturacion->cuit;

	$afip = new Afip(array('CUIT' =>$cuit , 'production' => true));

	/**
	* Numero del punto de venta
	**/
	$punto_de_venta = $this->datos_facturacion->pto_venta;

	$this->factura = Sale::join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','sales.nro_factura','sales.tipo_comprobante')->find($ventaIdFactura);


	if($this->factura->tipo_comprobante == 'C' || $this->factura->tipo_comprobante == 'CF') {

	  $tipo_de_comprobante = 11;


	}

	if($this->factura->tipo_comprobante == 'B') {
	  $tipo_de_comprobante = 6;
	}



	if($this->factura->tipo_comprobante == 'A') {
	$tipo_de_comprobante = 1;
	}

	if($this->factura->nro_factura != null) {

	$porciones = explode("-", $this->factura->nro_factura);
	$tipo_factura = $porciones[0]; // porción1
	$pto_venta = $porciones[1]; // porción2
	$nro_factura = $porciones[2]; // porción2
	$n = $porciones[2]; // porción2

	                                    /**
	* Numero de factura
	**/
	$numero_de_factura = $nro_factura;

	                    /**
	                     * Numero del punto de venta
	                     **/
	                    $punto_de_venta = $punto_de_venta;

	                    /**
	                     * Tipo de comprobante
	                     **/
	                    $tipo_de_comprobante = $tipo_de_comprobante; // 6 = Factura B

	                    /**
	                     * Informacion de la factura
	                     **/

	                    $informacion = $afip->ElectronicBilling->GetVoucherInfo($numero_de_factura, $punto_de_venta, $tipo_de_comprobante);

	                    if($informacion === NULL){
	                        echo 'La factura no existe';
	                    }
	                    else{
	                      /**
	                       * Mostramos por pantalla la información de la factura
	                       **

	                       dd($informacion);

	                       */

	                       $cuit = intval($cuit);
                           $numero_de_factura = intval($numero_de_factura);
                           $cae = intval($informacion->CodAutorizacion);
                           $dia = substr($informacion->CbteFch, -2);
                           $mes = substr($informacion->CbteFch, -4, 2);
                           $año = substr($informacion->CbteFch, -8, 4);

                           $fecha = $año."-".$mes."-".$dia;


	                                               // genero los datos para AFIP
	                        $url = 'https://www.afip.gob.ar/fe/qr/'; // URL que pide AFIP que se ponga en el QR.
	                        $datos_cmp_base_64 = json_encode([
	                            "ver" => 1,                         // Numérico 1 digito -  OBLIGATORIO – versión del formato de los datos del comprobante	1
	                            "fecha" => $fecha,            // full-date (RFC3339) - OBLIGATORIO – Fecha de emisión del comprobante
	                            "cuit" => $cuit,        // Numérico 11 dígitos -  OBLIGATORIO – Cuit del Emisor del comprobante
	                            "ptoVta" => $informacion->PtoVta,               // Numérico hasta 5 digitos - OBLIGATORIO – Punto de venta utilizado para emitir el comprobante
	                            "tipoCmp" => $informacion->CbteTipo,               // Numérico hasta 3 dígitos - OBLIGATORIO – tipo de comprobante (según Tablas del sistema. Ver abajo )
	                            "nroCmp" => $numero_de_factura,               // Numérico hasta 8 dígitos - OBLIGATORIO – Número del comprobante
	                            "importe" => $informacion->ImpTotal,         // Decimal hasta 13 enteros y 2 decimales - OBLIGATORIO – Importe Total del comprobante (en la moneda en la que fue emitido)
	                            "moneda" => "PES",                  // 3 caracteres - OBLIGATORIO – Moneda del comprobante (según Tablas del sistema. Ver Abajo )
	                            "ctz" => 1,                 // Decimal hasta 13 enteros y 6 decimales - OBLIGATORIO – Cotización en pesos argentinos de la moneda utilizada (1 cuando la moneda sea pesos)
	                            "tipoCodAut" => "E",                // string - OBLIGATORIO – “A” para comprobante autorizado por CAEA, “E” para comprobante autorizado por CAE
	                            "codAut" =>  $cae    // Numérico 14 dígitos -  OBLIGATORIO – Código de autorización otorgado por AFIP para el comprobante
	                        ]);


	                        $datos_cmp_base_64 = base64_encode($datos_cmp_base_64);
	                        $codigo_qr = $url.'?p='.$datos_cmp_base_64;

                            $codigoQR = QrCode::size(90)->generate($codigo_qr);
	                    }

} else {
    $codigo_qr = 0;
    $codigoQR = 0;
}

	}


} else {
    $codigo_qr = 0;
    $codigoQR = 0;
}

        $data["email"] = $email;
        $data["title"] = "Detalle de venta";
        $data["body"] = "A continuacion se adjunta el detalle de venta.";


        $fecha = Sale::select('sales.created_at')
         ->where('sales.id', $Id)
         ->get();

  $pdf = PDF::loadView('pdf.reporte-factura', compact('detalle_venta','Id','detalle_cliente','total_total','fecha','usuario','ventaId','codigo_barra_afip','cae','detalle_facturacion','codigoQR'));


   Mail::send('mail', $data, function ($message) use ($data, $pdf) {
            $message->to($data["email"], $data["email"])
                ->subject($data["title"])
                ->attachData($pdf->output(), "Venta.pdf");
        });
        
        return redirect()->back()->with('status', 'Mail enviado correctamente.'); 

     //   return redirect('pos')->with('status', 'Mail enviado correctamente.');



  }

   public function emailPDFEstado($Id, $email, $estado)
  {
      $detalle_venta = [];
      $detalle_cliente = [];
      $total_total = [];
      $fecha = [];
      $usuario = [];

           $ventaId = $Id;

      $usuario_id = Auth::user()->id;

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;


$detalle_facturacion = datos_facturacion::join('users','users.id','datos_facturacions.comercio_id')->leftjoin('provincias','provincias.id','datos_facturacions.provincia')->select('datos_facturacions.*','users.email','users.phone','provincias.provincia')->where('datos_facturacions.comercio_id', $comercio_id)->get();

      $detalle_venta = SaleDetail::join('products as p','p.id','sale_details.product_id')
      ->select('sale_details.id','sale_details.price','sale_details.quantity','sale_details.product_name as product', 'sale_details.iva','sale_details.product_barcode as barcode','p.stock','p.stock_descubierto', SaleDetail::raw('sale_details.price*sale_details.quantity as total'))
      ->where('sale_details.sale_id', $Id)
      ->where('sale_details.eliminado', 0)
      ->get();


      $detalle_cliente = Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
      ->select('c.id','c.nombre','c.telefono','c.email','c.dni','c.direccion','c.barrio','c.localidad','c.provincia','sales.observaciones','sales.metodo_pago','sales.fecha_entrega')
      ->where('sales.id', $Id)
      ->get();

     $total_total = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
         ->select('sales.recargo','sales.tipo_comprobante','sales.created_at','sales.descuento','sales.subtotal','sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago','sales.cae','sales.tipo_comprobante','sales.vto_cae','sales.nro_factura','sales.iva')
         ->where('sales.id', $Id)
         ->get();

        $fecha = Sale::select('sales.created_at')
         ->where('sales.id', $Id)
         ->get();

          $estado_actual = Estados::find($estado);


         $usuario = User::join('sales as s','s.comercio_id','users.id')
          ->select('users.image','users.name')
         ->where('s.id', $Id)
         ->get();


                     /////////////// CODIGO DE BARRAS AFIP ///////////////////

  $this->total_total2 = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
  ->select('sales.recargo','sales.tipo_comprobante','sales.created_at','sales.descuento','sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago','sales.cae','sales.vto_cae','sales.nro_factura')
  ->where('sales.id', $ventaId)
  ->first();

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $this->detalle_facturacion2 = datos_facturacion::where('datos_facturacions.comercio_id', $comercio_id)->first();

  /**
   * CUIT de la persona/empresa emitio la factura (11 caracteres)
   **/

  /**
   * Tipo de comprobante (2 caracteres, completado con 0's)
   **/
  if ($this->total_total2->tipo_comprobante == "A") {
   $tipo_de_comprobante = '01';
  }
  if ($this->total_total2->tipo_comprobante == "B") {
   $tipo_de_comprobante = '06';
  }
  if ($this->total_total2->tipo_comprobante == "C") {
   $tipo_de_comprobante = '011';
  }

  $this->detalle_facturacion2 = datos_facturacion::where('datos_facturacions.comercio_id', $comercio_id)->first();

if($this->detalle_facturacion2 != null) {

  /**
   * CUIT de la persona/empresa emitio la factura (11 caracteres)
   **/

  /**
   * Tipo de comprobante (2 caracteres, completado con 0's)
   **/
  if ($this->total_total2->tipo_comprobante == "A") {
   $tipo_de_comprobante = '01';
  }
  if ($this->total_total2->tipo_comprobante == "B") {
   $tipo_de_comprobante = '06';
  }
  if ($this->total_total2->tipo_comprobante == "C") {
   $tipo_de_comprobante = '011';
  }


  $cuit = $this->detalle_facturacion2->cuit;

    if($this->total_total2->nro_factura != null){
  /**
   * Punto de venta (4 caracteres, completado con 0's)
   **/
   $porciones = explode("-", $this->total_total2->nro_factura);
   $tipo_factura = $porciones[0]; // porción1
   $pto_venta = $porciones[1]; // porción2
   $nro_factura_ = $porciones[2]; // porción2
   $this->pto_venta = str_pad($pto_venta, 4, "0", STR_PAD_LEFT);


  $punto_de_venta = $this->pto_venta;

  /**
   * CAE (14 caracteres)
   **/
  $cae = $this->total_total2->cae;

  /**
   * Fecha de expiracion del CAE (8 caracteres, formato aaaammdd)
   **/
  $this->vto_cae = Carbon::parse($this->total_total2->vto_cae)->format('Ymd');

  $vencimiento_cae = $this->vto_cae;


  $barcode = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;

  $code = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;

  //Step one
  $number_odd = 0;
  for ($i=0; $i < strlen($code); $i+=2) {
    $number_odd += $code[$i];
  }

  //Step two
  $number_odd *= 3;

  //Step three
  $number_even = 0;
  for ($i=1; $i < strlen($code); $i+=2) {
    $number_even += $code[$i];
  }

  //Step four
  $sum = $number_odd+$number_even;

  //Step five
  $checksum_char = 10 - ($sum % 10);

  $this->barcode_ultimo = $checksum_char == 10 ? 0 : $checksum_char;

  $barcode .= $this->barcode_ultimo;

  /**
   * Mostramos por pantalla el numero del codigo de barras de 40 caracteres
   **/


  $codigo_barra_afip = $barcode;

    } else {
  $codigo_barra_afip = "";
  $cae = "";
    }

} else {
  $codigo_barra_afip = "";
  $cae = "";
}

        $data["email"] = $email;
        $data["title"] = "Ha cambiado el estado de su pedido # ".$ventaId." a ".$estado_actual->nombre;
        $data["body"] = "A continuacion se adjunta el detalle de venta en cuestion.";


        $fecha = Sale::select('sales.created_at')
         ->where('sales.id', $Id)
         ->get();

  $pdf = PDF::loadView('pdf.reporte-factura', compact('detalle_venta','Id','detalle_cliente','total_total','fecha','usuario','ventaId','codigo_barra_afip','cae','detalle_facturacion'));


   Mail::send('mail', $data, function ($message) use ($data, $pdf) {
            $message->to($data["email"], $data["email"])
                ->subject($data["title"])
                ->attachData($pdf->output(), "Venta.pdf");
        });

        return redirect('reports')->with('status', 'Mail enviado correctamente.');



  }


    public function reportCRM($uid)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(3000000);

        $reportName = 'Reporte de CRM_' . $uid . '.xlsx';

        $exportData = new CRMExport();

        return Excel::download($exportData, $reportName);
    }
    
    public function reporteExcel($sucursal_id,$usuarioSeleccionado, $clienteId,$estado_pago,$estado, $metodo_pago,$estado_facturacion, $dateFrom =null, $dateTo =null, $uid )
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(3000000);

        $reportName = 'Reporte de Ventas_' . $uid . '.xlsx';

        $exportData = new SalesExport($sucursal_id,$usuarioSeleccionado,$clienteId, $estado_pago,$estado,$metodo_pago,$estado_facturacion, $dateFrom, $dateTo);

        return Excel::download($exportData, $reportName);
    }
    
    // 18-4-2024
    public function reporteExcelPagos($tipo_movimiento_filtro,$estado_pago,$operacion_filtro,$banco_filtro,$metodo_pago_filtro,$sucursal_id,$uid)
    {

        ini_set('memory_limit', '1024M');
        set_time_limit(3000000);

        $reportName = 'Reporte de pagos' . $uid . '.xlsx';

        $exportData = new PagosExport($tipo_movimiento_filtro,$estado_pago,$operacion_filtro,$banco_filtro,$metodo_pago_filtro,$sucursal_id);

        return Excel::download($exportData, $reportName);
    }
    
    
    public function FacturacionExcel($sucursal_id,$tipo_comprobante_buscar,$facturas_repetidas, $clienteId,$estado_pago, $dateFrom =null, $dateTo =null, $uid )
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(3000000);

        $reportName = 'Reporte de Facturas de venta ' . $uid . '.xlsx';

        $exportData = new FacturasExport($sucursal_id,$tipo_comprobante_buscar,$facturas_repetidas, $clienteId,$estado_pago, $dateFrom, $dateTo);

        return Excel::download($exportData, $reportName);
    }
    
        public function FacturacionComprasExcel($sucursal_id,$tipo_comprobante_buscar, $proveedor_id, $dateFrom =null, $dateTo =null, $uid )
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(3000000);

        $reportName = 'Reporte de Facturas de compra ' . $uid . '.xlsx';

        $exportData = new FacturasComprasExport($sucursal_id,$tipo_comprobante_buscar,$proveedor_id,$dateFrom, $dateTo);

        return Excel::download($exportData, $reportName);
    }
    
    public function reporteExcelGastos($search, $categoria_filtro, $etiquetas_filtro ,$metodo_pago_filtro,$forma_pago_filtro, $dateFrom =null, $dateTo =null, $uid )
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(3000000);

        $reportName = 'Reporte de Gastos' . $uid . '.xlsx';

        $exportData = new GastosExport($search, $categoria_filtro, $etiquetas_filtro ,$metodo_pago_filtro,$forma_pago_filtro, $dateFrom, $dateTo);

        return Excel::download($exportData, $reportName);
    }
    
    
    

    public function reporteExcelEtiquetas($id, $uid )
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(3000000);

        $reportName = 'Etiquetas_' . $uid . '.xlsx';

        $exportData = new EtiquetasExport($id,$uid);

        return Excel::download($exportData, $reportName);
    }
    
    
    
        public function reporteExcelCompras($id_compra, $proveedor_id,$estado_pago, $dateFrom =null, $dateTo =null, $uid )
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(3000000);

        $reportName = 'Reporte de Compras' . $uid . '.xlsx';

        $exportData = new ComprasExport($id_compra, $proveedor_id,$estado_pago, $dateFrom, $dateTo);

        return Excel::download($exportData, $reportName);
    }

    public function reporteExcelClientes($sucursal_id,$uid)
    {

        ini_set('memory_limit', '1024M');
        set_time_limit(3000000);

        $reportName = 'Reporte de clientes' . $uid . '.xlsx';

        $exportData = new ClientesExport($sucursal_id);

        return Excel::download($exportData, $reportName);
    }
    
        public function reporteExcelProveedores($uid)
    {

        ini_set('memory_limit', '1024M');
        set_time_limit(3000000);

        $reportName = 'Reporte de proveedores_' . $uid . '.xlsx';

        $exportData = new ProveedoresExport();

        return Excel::download($exportData, $reportName);
    }

    public function reporteExcelDetalle($usuarioSeleccionado, $ClienteSeleccionado,$metodopagoSeleccionado, $productoSeleccionado, $categoriaSeleccionado, $almacenSeleccionado, $dateFrom=null, $dateTo=null, $sucursal_id, $uid )
    {

        ini_set('memory_limit', '1024M');
        set_time_limit(3000000);

        $reportName = 'Reporte de Ventas por Producto_' . $uid . '.xlsx';

        $exportData = new SalesDetailExport($usuarioSeleccionado, $ClienteSeleccionado,$metodopagoSeleccionado, $productoSeleccionado, $categoriaSeleccionado, $almacenSeleccionado, $dateFrom, $dateTo, $sucursal_id);

        return Excel::download($exportData, $reportName);

    }

    public function reporteExcelProduccion($estadoSeleccionado, $ClienteSeleccionado,$metodopagoSeleccionado, $productoSeleccionado, $categoriaSeleccionado, $almacenSeleccionado, $dateFrom=null, $dateTo=null)
    {

        ini_set('memory_limit', '1024M');
        set_time_limit(3000000);

        $reportName = 'Reporte de Produccion_' . uniqid() . '.xlsx';
        return Excel::download(new ProduccionExport($estadoSeleccionado, $ClienteSeleccionado,$metodopagoSeleccionado, $productoSeleccionado, $categoriaSeleccionado, $almacenSeleccionado, $dateFrom, $dateTo), $reportName);

    }


    public function reporteExcelAsistente($id_proveedor, $reportType, $buscar)
    {

        ini_set('memory_limit', '1024M');
        set_time_limit(3000000);

        $reportName = 'Compras_' . uniqid() . '.xlsx';
        return Excel::download(new AsistenteExport($id_proveedor, $reportType, $buscar), $reportName);

    }



    public function reporteExcelHojaRuta($id_hoja_ruta, $uid)
{

    ini_set('memory_limit', '1024M');
        set_time_limit(3000000);

    $reportName = 'Hoja de ruta' . $uid . '.xlsx';
    $exportData = new HojaRutaExport($id_hoja_ruta);
    return Excel::download($exportData, $reportName);


}



public function reportePDFHojaRuta($id_hoja_ruta, $uid)
{
    if(Auth::user()->comercio_id != 1)
        $comercio_id = Auth::user()->comercio_id;
    else
        $comercio_id = Auth::user()->id;
        
    $datos_comercio = User::find($comercio_id);
    $datos_hoja_ruta = hoja_ruta::find($id_hoja_ruta);
    
    // Obtener los clientes y sus ventas asociadas
    $clientes_ventas = Sale::join('clientes_mostradors as cm', 'cm.id', 'sales.cliente_id')
        ->where('sales.hoja_ruta', $id_hoja_ruta) 
        ->select('cm.*', 'sales.id as sale_id', 'sales.nro_venta', 'sales.cliente_id')
        ->where('sales.eliminado', 0)
        ->where('sales.status', '<>', 'Cancelado')
        ->orderBy('cm.nombre', 'asc')
        ->get()
        ->groupBy('cliente_id');
    
    $consolidado = [];
    foreach ($clientes_ventas as $cliente_id => $ventas) {
        $cliente = $ventas->first();
        $cliente_data = [
            'cliente' => $cliente,
            'ventas' => []
        ];
        
        foreach ($ventas as $venta) {
            $detalles_venta = SaleDetail::join('products', 'products.id', 'sale_details.product_id')
                ->where('sale_details.sale_id', $venta->sale_id)
                ->where('sale_details.eliminado', 0)
                ->orderBy('sale_details.product_barcode', 'desc')
                ->get();
                
            $cliente_data['ventas'][] = [
                'venta' => $venta,
                'detalles' => $detalles_venta
            ];
        }
        
        $consolidado[] = $cliente_data;
    }
    
    
    $pdf = PDF::loadView('pdf.hoja-ruta', compact('consolidado', 'datos_comercio', 'datos_hoja_ruta'));
    
    $nombre = 'Consolidado Hoja de Ruta nro' . $datos_hoja_ruta->nro_hoja . '.pdf';
    
    return $pdf->stream($nombre); // visualizar
}


public function reportePDFHojaRutaConsolidado($id_hoja_ruta, $uid)
{
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
            
    $datos_comercio = User::find($comercio_id);
    
    $datos_hoja_ruta = hoja_ruta::find($id_hoja_ruta);
        
    $consolidado = SaleDetail::join('products','products.id','sale_details.product_id')
    ->join('sales', 'sales.id', 'sale_details.sale_id')
    ->join('users as u', 'u.id', 'sales.user_id')
    ->join('clientes_mostradors as cm','cm.id','sales.cliente_id')
    ->leftjoin('hoja_rutas as hr','hr.id','sales.hoja_ruta')
    ->select('sale_details.product_barcode','sale_details.product_name',DB::raw('SUM(quantity) AS cantidad'))  
      ->whereIn('sale_id', function($query) {
      $query->select(DB::raw('id'))
      ->from('sales')
      ->whereRaw('sale_details.sale_id = sales.id');

  })
  ->where('sale_details.eliminado',0)
  ->where('sales.comercio_id', $comercio_id)
  ->where('sales.hoja_ruta', $id_hoja_ruta)
  ->groupBy('sale_details.product_barcode','sale_details.product_name')  
  ->orderBy('sale_details.product_barcode','desc')
  ->get();

  $pdf = PDF::loadView('pdf.hoja-ruta-consolidado', compact('consolidado','datos_comercio','datos_hoja_ruta'));

  $nombre = 'Consolidado Hoja de Ruta nro'.$datos_hoja_ruta->nro_hoja.'.pdf';
    
  return $pdf->stream($nombre); // visualizar
}

public function reporteExcelCaja($cajaid, $uid)
{

    ini_set('memory_limit', '1024M');
        set_time_limit(3000000);

$reportName = 'Caja' . $uid . '.xlsx';
$exportData = new CajaExport($cajaid);
return Excel::download($exportData, $reportName);


}


public function reporteExcelListaPrecios($uid, $listaId, $id_categoria,$id_almacen,$proveedor_elegido)
{

ini_set('memory_limit', '1024M');
set_time_limit(3000000);


  if($listaId != 1) {
  $this->lista_precio = lista_precios::find($listaId);
  $this->nombre_lista = $this->lista_precio->nombre;
} else {
  $this->nombre_lista = "Base";
}

$reportName = 'Lista de precios_' . $this->nombre_lista .'_'. $uid . '.xlsx';
$exportData = new ListaPreciosExport($listaId,$id_categoria,$id_almacen,$proveedor_elegido);
return Excel::download($exportData, $reportName);

}

public function reporteExcelStock($uid, $sucursalId, $id_categoria,$id_almacen,$proveedor_elegido)
{

ini_set('memory_limit', '1024M');
set_time_limit(3000000);

  if($sucursalId != 1) {
  $this->sucursal = sucursales::join('users','users.id','sucursales.sucursal_id')->where('sucursales.sucursal_id',$sucursalId)->first();
  $this->nombre_sucursal = $this->sucursal->nombre;
} else {
  $this->nombre_sucursal = "Casa central";
}

$reportName = 'Stock_' . $this->nombre_sucursal .'_'. $uid . '.xlsx';
$exportData = new StockSucursalExport($sucursalId, $id_categoria,$id_almacen,$proveedor_elegido);
return Excel::download($exportData, $reportName);

}

public function reporteExcelInsumos($uid)
{
ini_set('memory_limit', '1024M');
set_time_limit(3000000);

$reportName = 'Insumos_'. $uid . '.xlsx';
$exportData = new InsumosExport($uid);
return Excel::download($exportData, $reportName);

}

public function reporteExcelRecetas($uid)
{
ini_set('memory_limit', '1024M');
set_time_limit(3000000);

$reportName = 'Recetas_'. $uid . '.xlsx';
$exportData = new RecetasExport($uid);
return Excel::download($exportData, $reportName);

}

public function reporteExcelCtaCteClientes($search,$uid)
{
ini_set('memory_limit', '1024M');
set_time_limit(3000000);

$reportName = 'Cuenta corriente Clientes '. $uid . '.xlsx';
$exportData = new CtaCteClientesExport($search);
return Excel::download($exportData, $reportName);

}


public function reporteExcelCtaCteProveedores($search,$uid)
{
ini_set('memory_limit', '1024M');
set_time_limit(3000000);

$reportName = 'Cuenta corriente Clientes '. $uid . '.xlsx';
$exportData = new CtaCteProveedoresExport($search);
return Excel::download($exportData, $reportName);

}

    public function reporteExcelProducto($uid, $id_reporte, $comercio_id, $reportName){

    ini_set('memory_limit', '-1');
    set_time_limit(0);

     Excel::store(new ProductsExport($comercio_id, $id_reporte), $reportName);
    
    
    return redirect('products');
   
   //  return Excel::download(new ProductsExport, $reportName);
 }

    public function reporteExcelProductoEjemplo($comercio_id,$nombre_reporte){
 
       
    return Excel::download(new ProductsEjemploExport($comercio_id), $nombre_reporte);
 }




 public function reporteExcelCategorias(){
  return Excel::download(new CategoryExport, 'Categorias.xlsx');
}

public function emailPDFGracias($Id, $email, $slug)
   {
       $detalle_venta = [];
       $detalle_cliente = [];
       $total_total = [];
       $fecha = [];
       $usuario = [];

        $ventaId = $Id;

        $this->ecommerce = ecommerce::where('slug',$slug)->first();

       $usuario_id = Auth::user()->id;


       $comercio_id =   $this->ecommerce->comercio_id;


 $detalle_facturacion = datos_facturacion::join('users','users.id','datos_facturacions.comercio_id')
 ->leftjoin('provincias','provincias.id','datos_facturacions.provincia')
 ->select('datos_facturacions.*','users.email','users.phone','provincias.provincia')->where('datos_facturacions.comercio_id', $comercio_id)->get();

       $detalle_venta = SaleDetail::join('products as p','p.id','sale_details.product_id')
       ->select('sale_details.id','sale_details.comentario','sale_details.price','sale_details.quantity','p.name as product', SaleDetail::raw('sale_details.price*sale_details.quantity as total'))
       ->where('sale_details.sale_id', $Id)
       ->where('sale_details.eliminado', 0)
       ->get();


       $detalle_cliente = Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
       ->select('c.id','c.nombre','c.telefono','c.email','c.dni','c.direccion','c.barrio','c.localidad','c.provincia','sales.observaciones','sales.metodo_pago','sales.fecha_entrega')
       ->where('sales.id', $Id)
       ->get();

 $total_total = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
         ->select('sales.recargo','sales.tipo_comprobante','sales.created_at','sales.descuento','sales.subtotal','sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago','sales.cae','sales.tipo_comprobante','sales.vto_cae','sales.nro_factura','sales.iva')
         ->where('sales.id', $Id)
         ->get();

           $usuario = User::join('sales as s','s.comercio_id','users.id')
           ->select('users.image','users.name','users.email')
          ->where('s.id', $Id)
          ->get();


                      /////////////// CODIGO DE BARRAS AFIP ///////////////////

   $this->total_total2 = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
   ->select('sales.recargo','sales.tipo_comprobante','sales.created_at','sales.descuento','sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago','sales.cae','sales.vto_cae','sales.nro_factura')
   ->where('sales.id', $ventaId)
   ->first();

   if(Auth::user()->comercio_id != 1)
   $comercio_id = Auth::user()->comercio_id;
   else
   $comercio_id = Auth::user()->id;

   $this->detalle_facturacion2 = datos_facturacion::where('datos_facturacions.comercio_id', $comercio_id)->first();

   /**
    * CUIT de la persona/empresa emitio la factura (11 caracteres)
    **/

   /**
    * Tipo de comprobante (2 caracteres, completado con 0's)
    **/
   if ($this->total_total2->tipo_comprobante == "A") {
    $tipo_de_comprobante = '01';
   }
   if ($this->total_total2->tipo_comprobante == "B") {
    $tipo_de_comprobante = '06';
   }
   if ($this->total_total2->tipo_comprobante == "C") {
    $tipo_de_comprobante = '011';
   }


   $codigo_barra_afip = "";
   $cae = "";

         $data["email"] = $email;
         $data["title"] = "Factura";
         $data["body"] = "A continuacion se adjunta el detalle de venta. A la brevedad el comercio confirmara su compra. ";


         $fecha = Sale::select('sales.created_at')
          ->where('sales.id', $Id)
          ->get();

   $pdf = PDF::loadView('pdf.reporte-factura', compact('detalle_venta','Id','detalle_cliente','total_total','fecha','usuario','ventaId','codigo_barra_afip','cae','detalle_facturacion'));


    Mail::send('mail', $data, function ($message) use ($data, $pdf) {
             $message->to($data["email"], $data["email"])
                 ->subject($data["title"])
                 ->attachData($pdf->output(), "Venta.pdf");
         });

         return redirect('ecommerce-gracias/'.$slug)->with('status', 'Mail enviado correctamente.');



   }


        public function emailPDFFacturaRapido($Id, $email)
{

   $saleId = $Id;

   $items = [];
$sale= [];

$items = cobro_rapidos_detalle::select(cobro_rapidos_detalle::raw('1 as quantity'),'cobro_rapidos_detalles.concepto as product_name','cobro_rapidos_detalles.monto as price','cobro_rapidos_detalles.iva')
->where('cobro_rapido_id',$saleId)->get();

$sale = cobro_rapido::select('cobro_rapidos.cae','cobro_rapidos.tipo_comprobante','cobro_rapidos.vto_cae','cobro_rapidos.nro_factura','cobro_rapidos.total','cobro_rapidos.items','cobro_rapidos.user_id','cobro_rapidos.cliente_id','cobro_rapidos.comercio_id','cobro_rapidos.iva',cobro_rapido::raw('(cobro_rapidos.total - cobro_rapidos.iva) as subtotal'))->where('cobro_rapidos.id',$saleId)->get();

$sale2 = cobro_rapido::select('cobro_rapidos.cae','cobro_rapidos.tipo_comprobante','cobro_rapidos.vto_cae','cobro_rapidos.nro_factura','cobro_rapidos.total','cobro_rapidos.items','cobro_rapidos.user_id','cobro_rapidos.cliente_id','cobro_rapidos.comercio_id','cobro_rapidos.cae','cobro_rapidos.iva',cobro_rapido::raw('(cobro_rapidos.total - cobro_rapidos.iva) as subtotal'))->find($saleId);


$cliente = ClientesMostrador::select('clientes_mostradors.*')->join('cobro_rapidos','cobro_rapidos.cliente_id','clientes_mostradors.id')->where('cobro_rapidos.id',$saleId)->first();

$user = cobro_rapido::join('users','users.id','cobro_rapidos.comercio_id')
->select('users.*')
->where('cobro_rapidos.id',$saleId)->first();


$ventaId = $saleId;
$ventaIdFactura = $saleId;

if(Auth::user()->comercio_id != 1)
$comercio_id = Auth::user()->comercio_id;
else
$comercio_id = Auth::user()->id;

$datos_facturacion = datos_facturacion::join('users','users.id','datos_facturacions.comercio_id')
 ->leftjoin('provincias','provincias.id','datos_facturacions.provincia')
 ->select('datos_facturacions.*','users.email','users.phone','provincias.provincia')->where('datos_facturacions.comercio_id', $comercio_id)->first();



if($sale2->cae != null) {

/////////////// CODIGO DE BARRAS AFIP ///////////////////

$this->total_total2 = cobro_rapido::join('metodo_pagos as m','m.id','cobro_rapidos.metodo_pago')
->select('cobro_rapidos.recargo','cobro_rapidos.tipo_comprobante','cobro_rapidos.created_at','cobro_rapidos.total','cobro_rapidos.created_at as fecha','m.nombre as metodo_pago','cobro_rapidos.cae','cobro_rapidos.vto_cae','cobro_rapidos.nro_factura')
->where('cobro_rapidos.id', $ventaId)
->first();

if(Auth::user()->comercio_id != 1)
$comercio_id = Auth::user()->comercio_id;
else
$comercio_id = Auth::user()->id;

$this->detalle_facturacion2 = datos_facturacion::where('datos_facturacions.comercio_id', $comercio_id)->first();

/**
* CUIT de la persona/empresa emitio la factura (11 caracteres)
**/

/**
* Tipo de comprobante (2 caracteres, completado con 0's)
**/
if ($this->total_total2->tipo_comprobante == "A") {
$tipo_de_comprobante = '01';
}
if ($this->total_total2->tipo_comprobante == "B") {
$tipo_de_comprobante = '06';
}
if ($this->total_total2->tipo_comprobante == "C") {
$tipo_de_comprobante = '011';
}

if ($this->total_total2->tipo_comprobante == "CF") {
$tipo_de_comprobante = '099';
}



$cuit = $this->detalle_facturacion2->cuit;

/**
* Punto de venta (4 caracteres, completado con 0's)
**/
$porciones = explode("-", $this->total_total2->nro_factura);
$tipo_factura = $porciones[0]; // porción1
$pto_venta = $porciones[1]; // porción2
$nro_factura_ = $porciones[2]; // porción2
$this->pto_venta = str_pad($pto_venta, 4, "0", STR_PAD_LEFT);


$punto_de_venta = $this->pto_venta;

/**
* CAE (14 caracteres)
**/
$cae = $this->total_total2->cae;

/**
* Fecha de expiracion del CAE (8 caracteres, formato aaaammdd)
**/
$this->vto_cae = Carbon::parse($this->total_total2->vto_cae)->format('Ymd');

$vencimiento_cae = $this->vto_cae;


$barcode = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;

$code = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;

//Step one
$number_odd = 0;
for ($i=0; $i < strlen($code); $i+=2) {
 $number_odd += $code[$i];
}

//Step two
$number_odd *= 3;

//Step three
$number_even = 0;
for ($i=1; $i < strlen($code); $i+=2) {
 $number_even += $code[$i];
}

//Step four
$sum = $number_odd+$number_even;

//Step five
$checksum_char = 10 - ($sum % 10);

$this->barcode_ultimo = $checksum_char == 10 ? 0 : $checksum_char;

$barcode .= $this->barcode_ultimo;

/**
* Mostramos por pantalla el numero del codigo de barras de 40 caracteres
**/
$codigo_barra_afip = $barcode;


} else {
 $codigo_barra_afip = 0;
}


if($sale2->cae != null) {


if(Auth::user()->comercio_id != 1)
$comercio_id = Auth::user()->comercio_id;
else
$comercio_id = Auth::user()->id;

$this->datos_facturacion = datos_facturacion::where('comercio_id', $comercio_id)->first();


if($this->datos_facturacion->cuit != null || $this->datos_facturacion->cuit != '') {


$cuit =$this->datos_facturacion->cuit;

$afip = new Afip(array('CUIT' =>$cuit , 'production' => true));

/**
* Numero del punto de venta
**/
$punto_de_venta = $this->datos_facturacion->pto_venta;

$this->factura = cobro_rapido::join('clientes_mostradors','clientes_mostradors.id','cobro_rapidos.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','cobro_rapidos.nro_factura','cobro_rapidos.tipo_comprobante','cobro_rapidos.cae')->find($ventaIdFactura);


if($this->factura->tipo_comprobante == 'C' || $this->factura->tipo_comprobante == 'CF') {

 $tipo_de_comprobante = 11;


}

if($this->factura->tipo_comprobante == 'B') {
 $tipo_de_comprobante = 6;
}



if($this->factura->tipo_comprobante == 'A') {
$tipo_de_comprobante = 1;
}

if($this->factura->cae != null) {
$porciones = explode("-", $this->factura->nro_factura);
$tipo_factura = $porciones[0]; // porción1
$pto_venta = $porciones[1]; // porción2
$nro_factura = $porciones[2]; // porción2
$n = $porciones[2]; // porción2

                                   /**
* Numero de factura
**/
$numero_de_factura = $nro_factura;

                   /**
                    * Numero del punto de venta
                    **/
                   $punto_de_venta = $punto_de_venta;

                   /**
                    * Tipo de comprobante
                    **/
                   $tipo_de_comprobante = $tipo_de_comprobante; // 6 = Factura B

                   /**
                    * Informacion de la factura
                    **/

                   $informacion = $afip->ElectronicBilling->GetVoucherInfo($numero_de_factura, $punto_de_venta, $tipo_de_comprobante);

                   if($informacion === NULL){

                   }
                   else{
                     /**
                      * Mostramos por pantalla la información de la factura
                      **

                      dd($informacion);

                      */

                      $cuit = intval($cuit);
                        $numero_de_factura = intval($numero_de_factura);
                        $cae = intval($informacion->CodAutorizacion);
                        $dia = substr($informacion->CbteFch, -2);
                        $mes = substr($informacion->CbteFch, -4, 2);
                        $año = substr($informacion->CbteFch, -8, 4);

                        $fecha = $año."-".$mes."-".$dia;


                                              // genero los datos para AFIP
                       $url = 'https://www.afip.gob.ar/fe/qr/'; // URL que pide AFIP que se ponga en el QR.
                       $datos_cmp_base_64 = json_encode([
                           "ver" => 1,                         // Numérico 1 digito -  OBLIGATORIO – versión del formato de los datos del comprobante	1
                           "fecha" => $fecha,            // full-date (RFC3339) - OBLIGATORIO – Fecha de emisión del comprobante
                           "cuit" => $cuit,        // Numérico 11 dígitos -  OBLIGATORIO – Cuit del Emisor del comprobante
                           "ptoVta" => $informacion->PtoVta,               // Numérico hasta 5 digitos - OBLIGATORIO – Punto de venta utilizado para emitir el comprobante
                           "tipoCmp" => $informacion->CbteTipo,               // Numérico hasta 3 dígitos - OBLIGATORIO – tipo de comprobante (según Tablas del sistema. Ver abajo )
                           "nroCmp" => $numero_de_factura,               // Numérico hasta 8 dígitos - OBLIGATORIO – Número del comprobante
                           "importe" => $informacion->ImpTotal,         // Decimal hasta 13 enteros y 2 decimales - OBLIGATORIO – Importe Total del comprobante (en la moneda en la que fue emitido)
                           "moneda" => "PES",                  // 3 caracteres - OBLIGATORIO – Moneda del comprobante (según Tablas del sistema. Ver Abajo )
                           "ctz" => 1,                 // Decimal hasta 13 enteros y 6 decimales - OBLIGATORIO – Cotización en pesos argentinos de la moneda utilizada (1 cuando la moneda sea pesos)
                           "tipoCodAut" => "E",                // string - OBLIGATORIO – “A” para comprobante autorizado por CAEA, “E” para comprobante autorizado por CAE
                           "codAut" =>  $cae    // Numérico 14 dígitos -  OBLIGATORIO – Código de autorización otorgado por AFIP para el comprobante
                       ]);


                       $datos_cmp_base_64 = base64_encode($datos_cmp_base_64);
                       $codigo_qr = $url.'?p='.$datos_cmp_base_64;

                         $codigoQR = QrCode::size(90)->generate($codigo_qr);
                   }




} else {

   $codigo_qr = 0;
   $codigoQR = 0;
}

}

} else {
 $codigo_qr = 0;
 $codigoQR = 0;
}



     $data["email"] = $email;
     $data["title"] = "Factura";
     $data["body"] = "A continuacion se adjunta el detalle de venta.";




$pdf_factura = PDF::loadView('pdf.reporte-factura-rapido', compact('items','sale','user','codigo_barra_afip','datos_facturacion','cliente','codigo_qr','codigoQR'));

Mail::send('mail', $data, function ($message) use ($data, $pdf_factura) {
         $message->to($data["email"], $data["email"])
             ->subject($data["title"])
             ->attachData($pdf_factura->output(), "Venta.pdf");
     });

    return redirect()->back()->with('status', 'Mail enviado correctamente.');

   //  return redirect('venta-rapida')->with('status', 'Mail enviado correctamente.');



}


      public function PDFFacturaRapido($Id)
{

   $saleId = $Id;

   $items = [];
$sale= [];

$items = cobro_rapidos_detalle::select(cobro_rapidos_detalle::raw('1 as quantity'),'cobro_rapidos_detalles.concepto as product_name','cobro_rapidos_detalles.monto as price','cobro_rapidos_detalles.iva')
->where('cobro_rapido_id',$saleId)->get();


$sale = cobro_rapido::join('metodo_pagos','cobro_rapidos.metodo_pago','metodo_pagos.id')
->select('metodo_pagos.nombre as metodo_pago','cobro_rapidos.cae','cobro_rapidos.tipo_comprobante','cobro_rapidos.vto_cae','cobro_rapidos.nro_factura','cobro_rapidos.total','cobro_rapidos.items','cobro_rapidos.user_id','cobro_rapidos.cliente_id','cobro_rapidos.comercio_id','cobro_rapidos.iva',cobro_rapido::raw('(cobro_rapidos.total - cobro_rapidos.iva) as subtotal'))->where('cobro_rapidos.id',$saleId)->get();


$sale2 = cobro_rapido::select('cobro_rapidos.cae','cobro_rapidos.tipo_comprobante','cobro_rapidos.vto_cae','cobro_rapidos.nro_factura','cobro_rapidos.total','cobro_rapidos.items','cobro_rapidos.user_id','cobro_rapidos.cliente_id','cobro_rapidos.comercio_id','cobro_rapidos.iva',cobro_rapido::raw('(cobro_rapidos.total - cobro_rapidos.iva) as subtotal'))->find($saleId);


$cliente = ClientesMostrador::select('clientes_mostradors.*')->join('cobro_rapidos','cobro_rapidos.cliente_id','clientes_mostradors.id')->where('cobro_rapidos.id',$saleId)->first();

$user = cobro_rapido::join('users','users.id','cobro_rapidos.comercio_id')
->select('users.*')
->where('cobro_rapidos.id',$saleId)->first();


$ventaId = $saleId;
$ventaIdFactura = $saleId;

if(Auth::user()->comercio_id != 1)
$comercio_id = Auth::user()->comercio_id;
else
$comercio_id = Auth::user()->id;

$datos_facturacion = datos_facturacion::join('users','users.id','datos_facturacions.comercio_id')
 ->leftjoin('provincias','provincias.id','datos_facturacions.provincia')
 ->select('datos_facturacions.*','users.email','users.phone','provincias.provincia')->where('datos_facturacions.comercio_id', $comercio_id)->first();


if($sale2->cae != null) {

/////////////// CODIGO DE BARRAS AFIP ///////////////////

$this->total_total2 = cobro_rapido::join('metodo_pagos as m','m.id','cobro_rapidos.metodo_pago')
->select('cobro_rapidos.recargo','cobro_rapidos.tipo_comprobante','cobro_rapidos.created_at','cobro_rapidos.total','cobro_rapidos.created_at as fecha','m.nombre as metodo_pago','cobro_rapidos.cae','cobro_rapidos.vto_cae','cobro_rapidos.nro_factura')
->where('cobro_rapidos.id', $ventaId)
->first();

if(Auth::user()->comercio_id != 1)
$comercio_id = Auth::user()->comercio_id;
else
$comercio_id = Auth::user()->id;

$this->detalle_facturacion2 = datos_facturacion::where('datos_facturacions.comercio_id', $comercio_id)->first();

/**
* CUIT de la persona/empresa emitio la factura (11 caracteres)
**/

/**
* Tipo de comprobante (2 caracteres, completado con 0's)
**/
if ($this->total_total2->tipo_comprobante == "A") {
$tipo_de_comprobante = '01';
}
if ($this->total_total2->tipo_comprobante == "B") {
$tipo_de_comprobante = '06';
}
if ($this->total_total2->tipo_comprobante == "C") {
$tipo_de_comprobante = '011';
}

if ($this->total_total2->tipo_comprobante == "CF") {
$tipo_de_comprobante = '099';
}



$cuit = $this->detalle_facturacion2->cuit;

/**
* Punto de venta (4 caracteres, completado con 0's)
**/
$porciones = explode("-", $this->total_total2->nro_factura);
$tipo_factura = $porciones[0]; // porción1
$pto_venta = $porciones[1]; // porción2
$nro_factura_ = $porciones[2]; // porción2
$this->pto_venta = str_pad($pto_venta, 4, "0", STR_PAD_LEFT);


$punto_de_venta = $this->pto_venta;

/**
* CAE (14 caracteres)
**/
$cae = $this->total_total2->cae;

/**
* Fecha de expiracion del CAE (8 caracteres, formato aaaammdd)
**/
$this->vto_cae = Carbon::parse($this->total_total2->vto_cae)->format('Ymd');

$vencimiento_cae = $this->vto_cae;


$barcode = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;

$code = $cuit.$tipo_de_comprobante.$punto_de_venta.$cae.$vencimiento_cae;

//Step one
$number_odd = 0;
for ($i=0; $i < strlen($code); $i+=2) {
 $number_odd += $code[$i];
}

//Step two
$number_odd *= 3;

//Step three
$number_even = 0;
for ($i=1; $i < strlen($code); $i+=2) {
 $number_even += $code[$i];
}

//Step four
$sum = $number_odd+$number_even;

//Step five
$checksum_char = 10 - ($sum % 10);

$this->barcode_ultimo = $checksum_char == 10 ? 0 : $checksum_char;

$barcode .= $this->barcode_ultimo;

/**
* Mostramos por pantalla el numero del codigo de barras de 40 caracteres
**/
$codigo_barra_afip = $barcode;


} else {
 $codigo_barra_afip = 0;
}


if($sale2->cae != null) {


if(Auth::user()->comercio_id != 1)
$comercio_id = Auth::user()->comercio_id;
else
$comercio_id = Auth::user()->id;

$this->datos_facturacion = datos_facturacion::where('comercio_id', $comercio_id)->first();


if($this->datos_facturacion->cuit != null || $this->datos_facturacion->cuit != '') {


$cuit =$this->datos_facturacion->cuit;

$afip = new Afip(array('CUIT' =>$cuit , 'production' => true));

/**
* Numero del punto de venta
**/
$punto_de_venta = $this->datos_facturacion->pto_venta;

$this->factura = cobro_rapido::join('clientes_mostradors','clientes_mostradors.id','cobro_rapidos.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','cobro_rapidos.nro_factura','cobro_rapidos.tipo_comprobante','cobro_rapidos.cae')->find($ventaIdFactura);


if($this->factura->tipo_comprobante == 'C' || $this->factura->tipo_comprobante == 'CF') {

 $tipo_de_comprobante = 11;


}

if($this->factura->tipo_comprobante == 'B') {
 $tipo_de_comprobante = 6;
}



if($this->factura->tipo_comprobante == 'A') {
$tipo_de_comprobante = 1;
}


if($this->factura->cae != null) {

$porciones = explode("-", $this->factura->nro_factura);
$tipo_factura = $porciones[0]; // porción1
$pto_venta = $porciones[1]; // porción2
$nro_factura = $porciones[2]; // porción2
$n = $porciones[2]; // porción2

                                   /**
* Numero de factura
**/
$numero_de_factura = $nro_factura;

                   /**
                    * Numero del punto de venta
                    **/
                   $punto_de_venta = $punto_de_venta;

                   /**
                    * Tipo de comprobante
                    **/
                   $tipo_de_comprobante = $tipo_de_comprobante; // 6 = Factura B

                   /**
                    * Informacion de la factura
                    **/

                   $informacion = $afip->ElectronicBilling->GetVoucherInfo($numero_de_factura, $punto_de_venta, $tipo_de_comprobante);

                   if($informacion === NULL){

                   }
                   else{
                     /**
                      * Mostramos por pantalla la información de la factura
                      **

                      dd($informacion);

                      */

                      $cuit = intval($cuit);
                        $numero_de_factura = intval($numero_de_factura);
                        $cae = intval($informacion->CodAutorizacion);
                        $dia = substr($informacion->CbteFch, -2);
                        $mes = substr($informacion->CbteFch, -4, 2);
                        $año = substr($informacion->CbteFch, -8, 4);

                        $fecha = $año."-".$mes."-".$dia;


                                              // genero los datos para AFIP
                       $url = 'https://www.afip.gob.ar/fe/qr/'; // URL que pide AFIP que se ponga en el QR.
                       $datos_cmp_base_64 = json_encode([
                           "ver" => 1,                         // Numérico 1 digito -  OBLIGATORIO – versión del formato de los datos del comprobante	1
                           "fecha" => $fecha,            // full-date (RFC3339) - OBLIGATORIO – Fecha de emisión del comprobante
                           "cuit" => $cuit,        // Numérico 11 dígitos -  OBLIGATORIO – Cuit del Emisor del comprobante
                           "ptoVta" => $informacion->PtoVta,               // Numérico hasta 5 digitos - OBLIGATORIO – Punto de venta utilizado para emitir el comprobante
                           "tipoCmp" => $informacion->CbteTipo,               // Numérico hasta 3 dígitos - OBLIGATORIO – tipo de comprobante (según Tablas del sistema. Ver abajo )
                           "nroCmp" => $numero_de_factura,               // Numérico hasta 8 dígitos - OBLIGATORIO – Número del comprobante
                           "importe" => $informacion->ImpTotal,         // Decimal hasta 13 enteros y 2 decimales - OBLIGATORIO – Importe Total del comprobante (en la moneda en la que fue emitido)
                           "moneda" => "PES",                  // 3 caracteres - OBLIGATORIO – Moneda del comprobante (según Tablas del sistema. Ver Abajo )
                           "ctz" => 1,                 // Decimal hasta 13 enteros y 6 decimales - OBLIGATORIO – Cotización en pesos argentinos de la moneda utilizada (1 cuando la moneda sea pesos)
                           "tipoCodAut" => "E",                // string - OBLIGATORIO – “A” para comprobante autorizado por CAEA, “E” para comprobante autorizado por CAE
                           "codAut" =>  $cae    // Numérico 14 dígitos -  OBLIGATORIO – Código de autorización otorgado por AFIP para el comprobante
                       ]);


                       $datos_cmp_base_64 = base64_encode($datos_cmp_base_64);
                       $codigo_qr = $url.'?p='.$datos_cmp_base_64;

                         $codigoQR = QrCode::size(90)->generate($codigo_qr);
                   }




} else {

   $codigo_qr = 0;
   $codigoQR = 0;
}

}

} else {
 $codigo_qr = 0;
 $codigoQR = 0;
}



$pdf_factura = PDF::loadView('pdf.reporte-factura-rapido', compact('items','sale','user','codigo_barra_afip','datos_facturacion','cliente','codigo_qr','codigoQR'));


$nombre_ticket = 'Ticket_'.$saleId.'.pdf';

return $pdf_factura->stream($nombre_ticket); // visualizar



}

public function GetTipoComprobante($tipo_factura){
     
      if ($tipo_factura == "FA" || $tipo_factura == "A") {
       $tipo_de_comprobante = '01';
      }
      if ($tipo_factura == "FB" || $tipo_factura == "B") {
       $tipo_de_comprobante = '06';
      }
      if ($tipo_factura == "FC" || $tipo_factura == "C") {
       $tipo_de_comprobante = '011';
      }
      if ($tipo_factura == "CF") {
       $tipo_de_comprobante = '099';
      }
    
      return $tipo_de_comprobante;
}


public function GetCodigoQRAfip($factura_id,$comercio_id){

//  $datos_facturacion = datos_facturacion::where('datos_facturacions.comercio_id', $comercio_id)->first();
	$factura = facturacion::join('clientes_mostradors','clientes_mostradors.id','facturacions.cliente_id')
	->select('facturacions.*','clientes_mostradors.id as cliente_id','facturacions.cuit_comprador as dni')
	->find($factura_id);

	$codigo_qr = 0;
    $codigoQR = 0;
    
    //dd($datos_facturacion);
    if($factura != null) {
	if($factura->cuit_vendedor != null || $factura->cuit_vendedor != '') {

	$cuit =$factura->cuit_vendedor;

	$afip = new Afip(array('CUIT' =>$cuit , 'production' => true));

	/**
	* Numero del punto de venta
	**/

	if($factura->nro_factura != null) {
	$porciones = explode("-", $factura->nro_factura);
	$tipo_factura = $porciones[0]; // porción1
	$pto_venta = $porciones[1]; // porción2
	$nro_factura = $porciones[2]; // porción2
	$n = $porciones[2]; // porción2

    $tipo_de_comprobante = $this->GetTipoComprobante($tipo_factura);
	                                    /**
	* Numero de factura
	**/
	$numero_de_factura = $nro_factura;

	/**
	* Numero del punto de venta
	**/
	$punto_de_venta = $pto_venta;

	/**
	* Informacion de la factura
	**/

	$informacion = $afip->ElectronicBilling->GetVoucherInfo($numero_de_factura, $punto_de_venta, $tipo_de_comprobante);
    //dd($informacion);
	if($informacion === NULL){
	  echo 'La factura no existe';
	  }
	 else{
	/**
	* Mostramos por pantalla la información de la factura
	**

	dd($informacion);

	*/
    $cuit = intval($cuit);
    $numero_de_factura = intval($numero_de_factura);
    $cae = intval($informacion->CodAutorizacion);
    $dia = substr($informacion->CbteFch, -2);
    $mes = substr($informacion->CbteFch, -4, 2);
    $año = substr($informacion->CbteFch, -8, 4);

    $fecha = $año."-".$mes."-".$dia;


	// genero los datos para AFIP
	$url = 'https://www.afip.gob.ar/fe/qr/'; // URL que pide AFIP que se ponga en el QR.
	$datos_cmp_base_64 = json_encode([
	   "ver" => 1,                         // Numérico 1 digito -  OBLIGATORIO – versión del formato de los datos del comprobante	1
	   "fecha" => $fecha,            // full-date (RFC3339) - OBLIGATORIO – Fecha de emisión del comprobante
	   "cuit" => $cuit,        // Numérico 11 dígitos -  OBLIGATORIO – Cuit del Emisor del comprobante
	   "ptoVta" => $informacion->PtoVta,               // Numérico hasta 5 digitos - OBLIGATORIO – Punto de venta utilizado para emitir el comprobante
	   "tipoCmp" => $informacion->CbteTipo,               // Numérico hasta 3 dígitos - OBLIGATORIO – tipo de comprobante (según Tablas del sistema. Ver abajo )
	   "nroCmp" => $numero_de_factura,               // Numérico hasta 8 dígitos - OBLIGATORIO – Número del comprobante
	   "importe" => $informacion->ImpTotal,         // Decimal hasta 13 enteros y 2 decimales - OBLIGATORIO – Importe Total del comprobante (en la moneda en la que fue emitido)
	   "moneda" => "PES",                  // 3 caracteres - OBLIGATORIO – Moneda del comprobante (según Tablas del sistema. Ver Abajo )
	   "ctz" => 1,                 // Decimal hasta 13 enteros y 6 decimales - OBLIGATORIO – Cotización en pesos argentinos de la moneda utilizada (1 cuando la moneda sea pesos)
	   "tipoCodAut" => "E",                // string - OBLIGATORIO – “A” para comprobante autorizado por CAEA, “E” para comprobante autorizado por CAE
	   "codAut" =>  $cae    // Numérico 14 dígitos -  OBLIGATORIO – Código de autorización otorgado por AFIP para el comprobante
	   ]);


	   $datos_cmp_base_64 = base64_encode($datos_cmp_base_64);
	   $codigo_qr = $url.'?p='.$datos_cmp_base_64;

       $codigoQR = QrCode::size(90)->generate($codigo_qr);
	   
	  // dd($codigo_qr,$codigoQR);
	     
	 }

	} 
	} 
}

//dd($codigo_qr,$codigoQR);
return [$codigo_qr,$codigoQR];
    
}


public function GetCodigoQRAfipOld($factura_id,$comercio_id){

$datos_facturacion = datos_facturacion::where('datos_facturacions.comercio_id', $comercio_id)->first();

	$codigo_qr = 0;
    $codigoQR = 0;
    
    //dd($datos_facturacion);
    if($datos_facturacion != null) {
	if($datos_facturacion->cuit != null || $datos_facturacion->cuit != '') {

	$cuit =$datos_facturacion->cuit;

	$afip = new Afip(array('CUIT' =>$cuit , 'production' => true));

	/**
	* Numero del punto de venta
	**/
	$punto_de_venta = $datos_facturacion->pto_venta;

	$factura = facturacion::join('clientes_mostradors','clientes_mostradors.id','facturacions.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','facturacions.nro_factura','facturacions.tipo_comprobante')->find($factura_id);
    
	if($factura->nro_factura != null) {
	$porciones = explode("-", $factura->nro_factura);
	$tipo_factura = $porciones[0]; // porción1
	$pto_venta = $porciones[1]; // porción2
	$nro_factura = $porciones[2]; // porción2
	$n = $porciones[2]; // porción2

    $tipo_de_comprobante = $this->GetTipoComprobante($tipo_factura);
	                                    /**
	* Numero de factura
	**/
	$numero_de_factura = $nro_factura;

	/**
	* Numero del punto de venta
	**/
	$punto_de_venta = $punto_de_venta;

	/**
	* Informacion de la factura
	**/

	$informacion = $afip->ElectronicBilling->GetVoucherInfo($numero_de_factura, $punto_de_venta, $tipo_de_comprobante);
    //dd($informacion);
	if($informacion === NULL){
	  echo 'La factura no existe';
	  }
	 else{
	/**
	* Mostramos por pantalla la información de la factura
	**

	dd($informacion);

	*/
    $cuit = intval($cuit);
    $numero_de_factura = intval($numero_de_factura);
    $cae = intval($informacion->CodAutorizacion);
    $dia = substr($informacion->CbteFch, -2);
    $mes = substr($informacion->CbteFch, -4, 2);
    $año = substr($informacion->CbteFch, -8, 4);

    $fecha = $año."-".$mes."-".$dia;


	// genero los datos para AFIP
	$url = 'https://www.afip.gob.ar/fe/qr/'; // URL que pide AFIP que se ponga en el QR.
	$datos_cmp_base_64 = json_encode([
	   "ver" => 1,                         // Numérico 1 digito -  OBLIGATORIO – versión del formato de los datos del comprobante	1
	   "fecha" => $fecha,            // full-date (RFC3339) - OBLIGATORIO – Fecha de emisión del comprobante
	   "cuit" => $cuit,        // Numérico 11 dígitos -  OBLIGATORIO – Cuit del Emisor del comprobante
	   "ptoVta" => $informacion->PtoVta,               // Numérico hasta 5 digitos - OBLIGATORIO – Punto de venta utilizado para emitir el comprobante
	   "tipoCmp" => $informacion->CbteTipo,               // Numérico hasta 3 dígitos - OBLIGATORIO – tipo de comprobante (según Tablas del sistema. Ver abajo )
	   "nroCmp" => $numero_de_factura,               // Numérico hasta 8 dígitos - OBLIGATORIO – Número del comprobante
	   "importe" => $informacion->ImpTotal,         // Decimal hasta 13 enteros y 2 decimales - OBLIGATORIO – Importe Total del comprobante (en la moneda en la que fue emitido)
	   "moneda" => "PES",                  // 3 caracteres - OBLIGATORIO – Moneda del comprobante (según Tablas del sistema. Ver Abajo )
	   "ctz" => 1,                 // Decimal hasta 13 enteros y 6 decimales - OBLIGATORIO – Cotización en pesos argentinos de la moneda utilizada (1 cuando la moneda sea pesos)
	   "tipoCodAut" => "E",                // string - OBLIGATORIO – “A” para comprobante autorizado por CAEA, “E” para comprobante autorizado por CAE
	   "codAut" =>  $cae    // Numérico 14 dígitos -  OBLIGATORIO – Código de autorización otorgado por AFIP para el comprobante
	   ]);


	   $datos_cmp_base_64 = base64_encode($datos_cmp_base_64);
	   $codigo_qr = $url.'?p='.$datos_cmp_base_64;

       $codigoQR = QrCode::size(90)->generate($codigo_qr);
	   
	  // dd($codigo_qr,$codigoQR);
	     
	 }

	} 
	} 
}

//dd($codigo_qr,$codigoQR);
return [$codigo_qr,$codigoQR];
    
}


// 1-8-2024
public function reporteExcelCtaCteClientesMovimiento($cliente_id,$from,$to,$uid)
{
ini_set('memory_limit', '1024M');
set_time_limit(3000000);

$reportName = 'Movimientos Cuenta corriente Clientes '. $uid . '.xlsx';
$exportData = new CtaCteClientesMovimientosExport($cliente_id,$from,$to);
return Excel::download($exportData, $reportName);

}

public function reporteExcelCtaCteClientesMovimientoPorProducto($cliente_id,$from,$to,$uid)
{
ini_set('memory_limit', '1024M');
set_time_limit(3000000);

$reportName = 'Movimientos Cuenta corriente Clientes '. $uid . '.xlsx';
$exportData = new CtaCteClientesMovimientosExportPorProducto($cliente_id,$from,$to);
return Excel::download($exportData, $reportName);

}



public function PDFCtaCteClientesMovimiento($cliente_id,$from,$to,$uid)
{

    $datos_cliente = ClientesMostrador::find($cliente_id);
    
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
        
    $configuracion_ctas_ctes = configuracion_ctas_ctes::where('comercio_id',$comercio_id)->first();
    //dd($configuracion_ctas_ctes);
    
    $sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
    ->select('users.name','sucursales.sucursal_id')
    ->where('sucursales.casa_central_id', auth()->user()->casa_central_user_id)
    ->where('eliminado',0)
    ->get();
    
    if($configuracion_ctas_ctes == null){
    $selectedSucursales = [$comercio_id]; // Inicializar con el ID del usuario actual  
    } else {
        
    if($configuracion_ctas_ctes->valor == "por_sucursal"){
    $selectedSucursales = [$comercio_id]; // Inicializar con el ID del usuario actual      
    } else {
    
//    $selectedSucursales = [auth()->user()->id]; // Inicializar con el ID del usuario actual      
    
    $selectedSucursales = [auth()->user()->casa_central_user_id]; // Inicializar con el ID del usuario actual
    
    foreach ($sucursales as $sucursal) {
        $selectedSucursales[] = $sucursal->sucursal_id; // Agregar cada sucursal_id al array
    }
    }
    }
    
    //dd($selectedSucursales);
    
    $compras_clientes = [];
        
    // Consultas ajustadas para incluir el nombre de la sucursal
    $pagos = pagos_facturas::join('bancos', 'bancos.id', 'pagos_facturas.banco_id')
        ->leftJoin('sales', 'sales.id', 'pagos_facturas.id_factura')
        ->leftJoin('sucursales', 'pagos_facturas.comercio_id', 'sucursales.sucursal_id')
        ->leftjoin('users','users.id','sucursales.sucursal_id')
        ->whereBetween('pagos_facturas.created_at', [$from, $to])
        ->whereIn('pagos_facturas.comercio_id', $selectedSucursales)
        ->where('pagos_facturas.tipo_pago', 1)
        ->where('pagos_facturas.eliminado', 0)
        ->where('pagos_facturas.cliente_id', $cliente_id)
        ->select('pagos_facturas.comercio_id','bancos.nombre as nombre_banco', 'bancos.id as id_banco', 'pagos_facturas.url_comprobante as url_pago', Sale::raw('0 as id_saldo'), Sale::raw('0 as monto_saldo'), 'sales.nro_venta', 'pagos_facturas.id as id_pago', pagos_facturas::raw('0 as id_venta'), 'pagos_facturas.created_at', pagos_facturas::raw('0 as monto'), pagos_facturas::raw(' (pagos_facturas.monto + pagos_facturas.recargo + pagos_facturas.iva_pago + pagos_facturas.iva_recargo)  as monto_pago'), 'users.name as nombre_sucursal');

    $ventas = Sale::leftJoin('sucursales', 'sales.comercio_id', 'sucursales.sucursal_id')
        ->leftjoin('users','users.id','sucursales.sucursal_id')
        ->whereIn('sales.comercio_id', $selectedSucursales)
        ->whereBetween('sales.created_at', [$from, $to])
        ->where('sales.cliente_id', $cliente_id)
        ->where('sales.eliminado', 0)
        ->where('sales.status', '<>', 'Cancelado')
        ->select('sales.comercio_id',Sale::raw('"-" as nombre_banco'), Sale::raw('0 as id_banco'), Sale::raw('0 as url_pago'), Sale::raw('0 as id_saldo'), Sale::raw('0 as monto_saldo'), 'sales.nro_venta', Sale::raw('0 as id_pago'), 'sales.id as id_venta', 'sales.created_at', 'sales.total as monto', compras_proveedores::raw('0 as monto_pago'), 'users.name as nombre_sucursal');

    $saldos_iniciales = saldos_iniciales::join('bancos', 'bancos.id', 'saldos_iniciales.metodo_pago')
        ->leftJoin('sucursales', 'saldos_iniciales.comercio_id', 'sucursales.sucursal_id')
        ->leftjoin('users','users.id','sucursales.sucursal_id')
        ->whereIn('saldos_iniciales.sucursal_id', $selectedSucursales)
        ->whereBetween('saldos_iniciales.created_at', [$from, $to])
        ->where('saldos_iniciales.referencia_id', $cliente_id)
        ->where('saldos_iniciales.tipo', 'cliente')
        ->where('saldos_iniciales.eliminado', 0)
        ->select('saldos_iniciales.comercio_id','bancos.nombre as nombre_banco', 'bancos.id as id_banco', saldos_iniciales::raw('0 as url_pago'), 'saldos_iniciales.id as id_saldo', 'saldos_iniciales.monto as monto_saldo', saldos_iniciales::raw('0 as nro_venta'), saldos_iniciales::raw('0 as id_pago'), saldos_iniciales::raw('0 as id_venta'), 'saldos_iniciales.created_at', Sale::raw('0 as monto'), Sale::raw('0 as monto_pago'), 'users.name as nombre_sucursal');

    // Unión de las subconsultas
    $union = $pagos->union($ventas)->union($saldos_iniciales);
    
    // Obtener el resultado ordenado
    $compras_clientes = $union->orderBy('created_at', 'desc')->get();


$pdf_factura = PDF::loadView('pdf.cta-cte-clientes-movimientos', compact('compras_clientes','datos_cliente','from','to'));

$nombre_ticket = 'Movimientos cuenta corriente cliente_'.$cliente_id.'.pdf';

return $pdf_factura->stream($nombre_ticket); // visualizar



}

public function GetCtaClienteClienteById($cliente_id){
   
   $deuda_inicial = ClientesMostrador::join('saldos_iniciales','saldos_iniciales.referencia_id','clientes_mostradors.id')
   ->select(Sale::raw('SUM(IFNULL(saldos_iniciales.monto,0)) AS saldo'))
   ->where('clientes_mostradors.id',$cliente_id)
   ->where('saldos_iniciales.eliminado',0)
   ->first()->saldo; 
   
      
   $deuda = ClientesMostrador::join('sales','sales.cliente_id','clientes_mostradors.id')
   ->select(Sale::raw('SUM(IFNULL(sales.deuda,0)) AS saldo'))
   ->where('clientes_mostradors.id',$cliente_id)
   ->where('sales.eliminado',0)
   ->first()->saldo; 
   
   if($deuda == null){$deuda = 0;}
   if($deuda_inicial == null){$deuda_inicial = 0;}
   
return $deuda + $deuda_inicial;
}

}
