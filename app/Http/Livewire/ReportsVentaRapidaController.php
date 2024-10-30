<?php

namespace App\Http\Livewire;


use App\Http\Livewire\Scaner;
use App\Models\Category;
use Illuminate\Validation\Rule;
use App\Models\proveedores;
use App\Models\Product;
use App\Models\User;
use App\Models\datos_facturacion;
use App\Models\ClientesMostrador;
use App\Models\metodo_pago;
use App\Models\cobro_rapido;
use App\Models\cobro_rapidos_detalle;
use App\Models\receta;
use App\Models\historico_stock;
use Livewire\Component;
use App\Models\seccionalmacen;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Models\sucursales;
use App\Traits\CartTrait;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Afip;

class ReportsVentaRapidaController extends Scaner //Component
{

	use WithPagination;
	use WithFileUploads;
	use CartTrait;


	public $name,$barcode,$cost,$price,$tipo_producto, $stock,$alerts,$categoryid,$search,$mail_ingresado, $image,$selected_id,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $inv_ideal, $proveedor, $cod_proveedor, $proveedor_elegido, $id_sucursal, $ecommerce_canal, $mostrador_canal, $name_almacen, $descripcion, $image_categoria, $name_categoria, $product_added, $cobros, $dateFrom, $dateTo, $sucursal_id;
	public $id_almacen;
	public $id_categoria;
	public $id_proveedor;
	private $pagination = 25;
	private $codigoQR;

	public $sucursal = [];
	public $SelectedProducts = [];
	public $selectedAll = FALSE;

	public $sortColumn = "created_at";
    public $sortDirection = "desc";


	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function mount()
	{
		$this->dateFrom = Carbon::parse('2000-01-01 00:00:00')->format('d-m-Y');
		$this->dateTo = Carbon::now()->format('d-m-Y');
		$this->metodo_pago = session('MetodoPago');
		$this->pageTitle = 'Listado';
		$this->proveedor = '1';
		$this->componentName = 'Productos';
		$this->categoryid = 'Elegir';
		$this->almacen = 'Elegir';
		$this->stock_descubierto = 'Elegir';
		$this->OrderNombre = "ASC";
		$this->OrderBarcode = "ASC";
		$this->tipo_producto = "Elegir";


	}


	public function sort($column)
	{
			$this->sortColumn = $column;
			$this->sortDirection = $this->sortDirection == 'asc' ? 'desc' : 'asc';
	}


	public function ModalCategoria($value)
	{
		if($value == 'AGREGAR') {

		$this->emit('modal-categoria-show', '');

		}

	}

	public function ModalAlmacen($value)
	{
		if($value == 'AGREGAR') {

		$this->emit('modal-almacen-show', '');

		}

	}



	public function render()
	{


			if(Auth::user()->comercio_id != 1)
			$comercio_id = Auth::user()->comercio_id;
			else
			$comercio_id = Auth::user()->id;

			if($this->sucursal_id != null) {
				$this->sucursal_id = $this->sucursal_id;
			} else {
				$this->sucursal_id = $comercio_id;
			}





			$this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')->where('casa_central_id', $comercio_id)->get();

			if($this->dateFrom !== '' || $this->dateTo !== '')
			{
			$from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
			$to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';

			}

			$cobros = cobro_rapido::where('cobro_rapidos.comercio_id', 'like', $this->sucursal_id);

			$cobros = $cobros->whereBetween('cobro_rapidos.created_at', [$from, $to]);
			
			if(0 < strlen($this->search)){
			$cobros = $cobros->where('nro_factura','%' . $this->search . '%');    
			}
			$cobros = $cobros->orderBy($this->sortColumn, $this->sortDirection);

			$cobros = $cobros->paginate($this->pagination);

			$this->comercio_id = $comercio_id;

			return view('livewire.reports-venta-rapida.component', [
				'data' => $cobros,
				'sucursales' => $this->sucursales,
				'comercio_id' => $this->comercio_id
			])
			->extends('layouts.theme-pos.app')
			->section('content');

}


	protected $listeners =[
		'deleteRow' => 'Destroy',
		'ConfirmCheck' => 'DeleteSelected'
	];


public function MailModal($ventaId) {
    $this->ventaId = $ventaId;
     $this->emit('mail-modal', '');

}


public function EnviarMail() {

      return redirect('report-email-rapido/pdf' . '/' . $this->ventaId  . '/' . $this->mail_ingresado);

}



public function ImprimirTicket($saleId)
{
$this->items = [];
$this->sale= [];

$this->items = cobro_rapidos_detalle::select(cobro_rapidos_detalle::raw('(cobro_rapidos_detalles.monto+cobro_rapidos_detalles.iva) as price_iva'),cobro_rapidos_detalle::raw('1 as quantity'),'cobro_rapidos_detalles.concepto as product_name','cobro_rapidos_detalles.monto as price','cobro_rapidos_detalles.iva')
->where('cobro_rapido_id',$saleId)->get();

$this->sale = cobro_rapido::select('cobro_rapidos.cae','cobro_rapidos.tipo_comprobante','cobro_rapidos.vto_cae','cobro_rapidos.nro_factura','cobro_rapidos.total','cobro_rapidos.items','cobro_rapidos.user_id','cobro_rapidos.cliente_id','cobro_rapidos.comercio_id','cobro_rapidos.iva',cobro_rapido::raw('(cobro_rapidos.total - cobro_rapidos.iva) as subtotal'))->find($saleId);

$this->cliente = ClientesMostrador::select('clientes_mostradors.*')->join('cobro_rapidos','cobro_rapidos.cliente_id','clientes_mostradors.id')->where('cobro_rapidos.id',$saleId)->first();

$this->user = cobro_rapido::join('users','users.id','cobro_rapidos.comercio_id')
->select('users.*')
->where('cobro_rapidos.id',$saleId)->first();


$ventaId = $saleId;
$ventaIdFactura = $saleId;


$datos_facturacion = datos_facturacion::join('provincias','provincias.id','datos_facturacions.provincia')
->select('datos_facturacions.*','provincias.provincia as nombre_provincia')
->where('datos_facturacions.comercio_id', $this->user->id)->first();


if($this->sale->cae != null) {

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


if($this->sale->cae != null) {


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
	                        $this->codigo_qr = $url.'?p='.$datos_cmp_base_64;

                            $this->codigoQR = QrCode::size(90)->generate($this->codigo_qr);

	                    }



	}


} else {
    $this->codigo_qr = 0;
    $this->codigoQR = 0;
}

if($this->sale->cae != null){
  $this->cae = $this->sale->cae;
} else {
    $this->cae = 0;
}

if($this->sale->nro_factura != null){
  $this->nro_factura = $this->sale->nro_factura;
} else {
    $this->nro_factura = 0;
}

$this->datos_facturacion = datos_facturacion::join('provincias','provincias.id','datos_facturacions.provincia')
->select('datos_facturacions.*','provincias.provincia as nombre_provincia')
->where('datos_facturacions.comercio_id', $this->user->id)->first();

$this->razon_social =  $datos_facturacion->razon_social;
$this->cuit =  $datos_facturacion->cuit;
$this->condicion_iva = $datos_facturacion->condicion_iva;

$this->inicio_actividades = Carbon::parse($datos_facturacion->fecha_inicio_actividades)->format('d/m/Y');
$this->iibb = $datos_facturacion->iibb;
$this->pto_venta = $datos_facturacion->pto_venta;
$this->tipo_comprobante = $this->sale->tipo_comprobante;
$this->total = $this->sale->total;
$this->subtotal = $this->sale->subtotal;
$this->iva = $this->sale->iva;
$this->nro_factura = $this->sale->nro_factura;
$this->usuario = $this->user->name;
$this->cuit_cliente = $this->cliente->dni;
$this->nombre_cliente = $this->cliente->name;


$this->direccion =  $datos_facturacion->domicilio_fiscal." ".$datos_facturacion->localidad."-".$datos_facturacion->nombre_provincia;
$this->fecha = Carbon::now()->format('d/m/Y H:i');

$this->items = json_encode($this->items);


$this->emit('imprimir-ticket', $this->items);


}




  public function ElegirSucursal($sucursal_id) {

  	$this->sucursal_id = $sucursal_id;

  }


								   
				public function AnularFactura($factura_id){
    
                
                if(Auth::user()->comercio_id != 1)
                $comercio_id = Auth::user()->comercio_id;
                else
                $comercio_id = Auth::user()->id;
                
                $ventaIdFactura = $factura_id;
                
                $this->factura = cobro_rapido::join('clientes_mostradors','clientes_mostradors.id','cobro_rapidos.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','cobro_rapidos.*')->find($ventaIdFactura);
                
            //  $this->factura = Sale::join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','sales.*')->find($ventaId);
                
                $this->datos_facturacion = datos_facturacion::where('comercio_id', $comercio_id)->first();
                
                $cuit_vendedor = $this->datos_facturacion->cuit;
            //  $this->datos_facturacion = datos_facturacion::where('comercio_id', $comercio_id)->first();
            
                $venta = $this->factura;
                $ventaId = $this->factura->sale_id;
                
                $array = explode("-",$venta->nro_factura);
                
                $tipo_factura = $array[0];
                
                /* Vemos que tipo de factura es */
                if($tipo_factura == "FB") {
                 $tipo_comprobante_factura = 6;
                 $tipo_comprobante_nota_credito = 8;   
                }
                if($tipo_factura == "B") {
                $tipo_comprobante_factura = 6;
                $tipo_comprobante_nota_credito = 8;   
                }
                if($tipo_factura == "FA") {
                $tipo_comprobante_factura = 1;
                $tipo_comprobante_nota_credito = 3;   
                }
                if($tipo_factura == "A") {
                $tipo_comprobante_factura = 1;
                 $tipo_comprobante_nota_credito = 3;   
                }
                if($tipo_factura == "FC") {
                $tipo_comprobante_factura = 11;
                $tipo_comprobante_nota_credito = 13;   
                }
                if($tipo_factura == "C") {
                $tipo_comprobante_factura = 11;
                $tipo_comprobante_nota_credito = 13;   
                }
                
                
                /* Obtenemos el punto de venta usado en esa factura */
                $pto_venta = $array[1];
                /* Obtenemos el numero de factura */
                $nro_factura = $array[2];
                
                //dd($cuit_vendedor);
                if($cuit_vendedor != null || $cuit_vendedor != '') {
                
                
                $afip = new Afip(array('CUIT' => $cuit_vendedor, 'production' => true));
                
                /**
                 * Numero del punto de venta
                 **/
                $punto_de_venta = $pto_venta;
                
                /**
                 * Tipo de Nota de Crédito
                 **/
                $tipo_de_nota = $tipo_comprobante_nota_credito; // 8 = Nota de Crédito B
                
                /**
                 * Número de la ultima Nota de Crédito B
                 **/
                $last_voucher = $afip->ElectronicBilling->GetLastVoucher($punto_de_venta, $tipo_de_nota);
                
                /**
                 * Numero del punto de venta de la Factura 
                 * asociada a la Nota de Crédito
                 **/
                $punto_factura_asociada = $pto_venta;
                
                /**
                 * Tipo de Factura asociada a la Nota de Crédito
                 **/
                $tipo_factura_asociada = $tipo_comprobante_factura; // 6 = Factura B
                
                /**
                 * Numero de Factura asociada a la Nota de Crédito
                 **/
                $numero_factura_asociada = $nro_factura;
                
                /**
                 * Concepto de la Nota de Crédito
                 *
                 * Opciones:
                 *
                 * 1 = Productos 
                 * 2 = Servicios 
                 * 3 = Productos y Servicios
                 **/
                $concepto = 1;
                
                /**
                 * Tipo de documento del comprador
                 *
                 * Opciones:
                 *
                 * 80 = CUIT 
                 * 86 = CUIL 
                 * 96 = DNI
                 * 99 = Consumidor Final 
                 **/
                 
                 
                /**
                 * Numero de documento del comprador (0 para consumidor final)
                 **/
                 
                if ($this->factura->cliente_id == 1) {
                
                 $tipo_de_documento = 99;
                
                $numero_de_documento = 0;
                
                } else {
                
                $tipo_de_documento = 80;
                
                $numero_de_documento = $this->factura->dni;
                
                }
                
                /**
                 * Numero de Nota de Crédito
                 **/
                $numero_de_nota = $last_voucher+1;
                
                /**
                 * Fecha de la Nota de Crédito en formato aaaa-mm-dd (hasta 10 dias antes y 10 dias despues)
                 **/
                $fecha = date('Y-m-d');
                
                /**
                 * Informacion de la factura
                 **/
                $informacion = $afip->ElectronicBilling->GetVoucherInfo($nro_factura, $pto_venta, $tipo_comprobante_factura); 
                
                if($informacion === NULL){
                    dd('La factura no existe');
                }
                else{
                    
                $importe_gravado = $informacion->ImpNeto;
                $importe_iva = $informacion->ImpIVA;
                
                /**
                 * Importe exento al IVA
                 **/
                $importe_exento_iva = 0;
                
                /**
                 * Importe de IVA
                 **/
                
                $fecha_servicio_desde = null;
                $fecha_servicio_hasta = null;
                $fecha_vencimiento_pago = null;
                
                $data = array(
                'CantReg'       => 1,
                'PtoVta'        => $punto_de_venta,
                'CbteTipo'      => $tipo_de_nota, 
                'Concepto'      => $concepto,
                'DocTipo'       => $tipo_de_documento,
                'DocNro'        => $numero_de_documento,
                'CbteDesde'     => $numero_de_nota,
                'CbteHasta'     => $numero_de_nota,
                'CbteFch'       => intval(str_replace('-', '', $fecha)),
                'FchServDesde'  => $fecha_servicio_desde,
                'FchServHasta'  => $fecha_servicio_hasta,
                'FchVtoPago'    => $fecha_vencimiento_pago,
                'ImpTotal'      => $importe_gravado + $importe_iva + $importe_exento_iva,
                'ImpTotConc'    => 0,
                'ImpNeto'       => $importe_gravado,
                'ImpOpEx'       => $importe_exento_iva,
                'ImpIVA'        => $importe_iva,
                'ImpTrib'       => 0,
                'MonId'         => 'PES',
                'MonCotiz'      => 1,
                'CbtesAsoc'     => array(
                    array(
                        'Tipo'      => $tipo_factura_asociada,
                        'PtoVta'    => $punto_factura_asociada,
                        'Nro'       => $numero_factura_asociada,
                    )
                ),
            );
            
                // Verificar si $tipo_factura es igual a "FC" antes de incluir la sección 'Iva'
                if ($tipo_factura != "FC" && $tipo_factura != "C") {
                    $alicuota_iva = number_format($this->factura->iva/$this->factura->subtotal,2);
                    $tipo_iva = $this->SetTipoIva($alicuota_iva);
                    
                    $data['Iva'] = array(
                        array(
                            'Id'        => $tipo_iva,
                            'BaseImp'   => $importe_gravado,
                            'Importe'   => $importe_iva 
                        )
                    );
                }

                
                /** 
                 * Creamos la Nota de Crédito 
                 **/
                $res = $afip->ElectronicBilling->CreateVoucher($data);
                
                /**
                 * Mostramos por pantalla los datos de la nueva Nota de Crédito 
                 **/
                
                //dd($res);
                
                  $facturacion = cobro_rapido::find($this->factura->id);
                  $facturacion->nota_credito = "NC-".$tipo_factura."-".$numero_de_nota;
                  $facturacion->save();
                
                 $this->emit('pago-actualizado', 'NOTA DE CREDITO GENERADA CORRECTAMENTE');
                
                }
                }
    
}

public function SetTipoIva($alicuota_iva) {

if($alicuota_iva == 0.105) {
return 4;
}

if($alicuota_iva == 0.21) {
return 5;    
}

if($alicuota_iva == 0.27) {
return 6;   
}   

}

}
