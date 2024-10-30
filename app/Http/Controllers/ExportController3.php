<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Exports\ProductsExport;
use App\Exports\ListaPreciosExport;
use App\Exports\StockSucursalExport;
use App\Exports\HojaRutaExport;
use App\Exports\CategoryExport;
use App\Exports\RecetasExport;
use App\Exports\ClientesExport;
use App\Exports\InsumosExport;
use App\Exports\SalesDetailExport;
use App\Exports\CajaExport;
use App\Models\Estados;
use App\Models\cobro_rapido;
use App\Models\cobro_rapidos_detalle;
use App\Exports\AsistenteExport;
use App\Exports\ProduccionExport;
use App\Models\sucursales;
use App\Models\Sale;
use App\Models\presupuestos;
use App\Models\lista_precios;
use App\Models\ecommerce;
use App\Models\presupuestos_detalle;
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


class ExportController extends Controller
{

  public $userId;
  public $clienteId;
  public $detalle_facturacion;
  public $usuario;



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


$detalle_facturacion = datos_facturacion::join('users','users.id','datos_facturacions.comercio_id')->leftjoin('provincias','provincias.id','datos_facturacions.provincia')->select('datos_facturacions.*','users.email','users.phone','provincias.provincia')->where('datos_facturacions.comercio_id', $comercio_id)->get();

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


  $pdf_factura = PDF::loadView('pdf.reporte-remito', compact('detalle_venta','Id','detalle_cliente','detalle_facturacion','total_total','fecha','usuario','ventaId','codigo_barra_afip','cae','codigoQR'));


      return $pdf_factura->stream('Factura.pdf'); // visualizar

  }



public function Ticket($saleId)
{
$items = [];
$sale= [];

$items = SaleDetail::join('products','products.id','sale_details.product_id')
->select('sale_details.product_name','sale_details.quantity','sale_details.descuento','sale_details.recargo','sale_details.price','sale_details.iva')
->where('sale_details.eliminado',0)
->where('sale_id',$saleId)->get();

$sale = Sale::select('sales.*',Sale::raw('(sales.subtotal) as subtotal'))->find($saleId);

$cliente = ClientesMostrador::select('clientes_mostradors.*')->join('sales','sales.cliente_id','clientes_mostradors.id')->where('sales.id',$saleId)->first();

$user = Sale::join('users','users.id','sales.comercio_id')
->select('users.*')
->where('sales.id',$saleId)->first();

$ventaId = $saleId;
$ventaIdFactura = $saleId;

$datos_facturacion = datos_facturacion::where('comercio_id', $user->id)->first();


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


$pdf_factura = PDF::loadView('pdf.ticket', compact('items','sale','user','codigo_barra_afip','datos_facturacion','cliente','codigoQR'));

$nombre_ticket = 'Ticket_'.$saleId.'.pdf';

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



   public function reportPDFFactura($Id)
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

      $detalle_venta = SaleDetail::join('products as p','p.id','sale_details.product_id')
      ->select('sale_details.id','sale_details.price','sale_details.descuento','sale_details.recargo','sale_details.quantity','sale_details.product_name as product', 'sale_details.iva','sale_details.product_barcode as barcode','p.stock','p.stock_descubierto', SaleDetail::raw('sale_details.price*sale_details.quantity as total'))
      ->where('sale_details.sale_id', $Id)
      ->where('sale_details.eliminado', 0)
      ->get();


      $detalle_cliente = Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
      ->select('c.id','c.nombre','c.telefono','c.email','c.dni','c.direccion','c.barrio','c.localidad','c.provincia','sales.observaciones','sales.metodo_pago','sales.fecha_entrega')
      ->where('sales.id', $Id)
      ->get();

      $total_total = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
         ->select('sales.recargo','sales.tipo_comprobante','sales.created_at','sales.descuento','sales.total','sales.subtotal','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago','sales.cae','sales.tipo_comprobante','sales.vto_cae','sales.nro_factura','sales.iva')
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
  ->select('sales.recargo','sales.tipo_comprobante','sales.created_at','sales.descuento','sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago','sales.cae','sales.vto_cae','sales.nro_factura')
  ->where('sales.id', $ventaId)
  ->first();

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


      return $pdf_factura->stream('Factura.pdf'); // visualizar

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


   public function emailPDFFactura($Id, $email)
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
        $data["title"] = "Factura";
        $data["body"] = "A continuacion se adjunta el detalle de venta.";


        $fecha = Sale::select('sales.created_at')
         ->where('sales.id', $Id)
         ->get();

  $pdf = PDF::loadView('pdf.reporte-factura', compact('detalle_venta','Id','detalle_cliente','total_total','fecha','usuario','ventaId','codigo_barra_afip','cae','detalle_facturacion','codigoQR'));


   Mail::send('mail', $data, function ($message) use ($data, $pdf) {
            $message->to($data["email"], $data["email"])
                ->subject($data["title"])
                ->attachData($pdf->output(), "Factura.pdf");
        });

        return redirect('pos')->with('status', 'Mail enviado correctamente.');



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
         ->select('sales.recargo','sales.tipo_comprobante','sales.created_at','sales.descuento','sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago','sales.cae','sales.tipo_comprobante','sales.vto_cae','sales.nro_factura','sales.iva')
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
                ->attachData($pdf->output(), "Factura.pdf");
        });

        return redirect('reports')->with('status', 'Mail enviado correctamente.');



  }


    public function reporteExcel($usuarioSeleccionado, $clienteId,$estado_pago,$estado, $metodo_pago, $dateFrom =null, $dateTo =null, $uid )
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(3000000);

        $reportName = 'Reporte de Ventas_' . $uid . '.xlsx';

        $exportData = new SalesExport($usuarioSeleccionado,$clienteId, $estado_pago,$estado,$metodo_pago, $dateFrom, $dateTo);

        return Excel::download($exportData, $reportName);
    }

    public function reporteExcelClientes($uid)
    {

        ini_set('memory_limit', '1024M');
        set_time_limit(3000000);

        $reportName = 'Reporte de clientes' . $uid . '.xlsx';

        $exportData = new ClientesExport();

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

    public function reporteExcelProducto($uid, $id_reporte, $comercio_id, $reportName){

    ini_set('memory_limit', '-1');
    set_time_limit(0);

     Excel::store(new ProductsExport($comercio_id, $id_reporte), $reportName);
    
    
    return redirect('products');
   
   //  return Excel::download(new ProductsExport, $reportName);
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
          ->select('sales.total','sales.descuento','sales.created_at as fecha','sales.observaciones', 'm.nombre as metodo_pago','sales.cae','sales.vto_cae','sales.tipo_comprobante','sales.nro_factura')
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
             ->attachData($pdf_factura->output(), "Factura.pdf");
     });

     return redirect('venta-rapida')->with('status', 'Mail enviado correctamente.');



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


}
