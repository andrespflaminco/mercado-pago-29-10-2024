<?php

namespace App\Http\Livewire;

use App\Services\CartCobros;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Session;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\proveedores;
use App\Models\Category;
use App\Models\datos_facturacion;
use App\Models\metodo_pago;
use App\Models\cobro_rapido;
use App\Models\cobro_rapidos_detalle;
use App\Models\cajas;
use App\Models\historico_stock;
use App\Models\historico_stock_insumo;
use App\Models\pagos_facturas;
use App\Models\insumo;
use App\Models\concepto_rapido;
use App\Models\Product;
use App\Models\bancos;
use App\Models\ClientesMostrador;
use App\Models\compras_insumos;
use App\Models\detalle_compra_insumos;
use DB;
use Carbon\Carbon;
use Afip;

class VentaRapidaController extends Component
{
  use WithPagination;
  use WithFileUploads;

  public $name,$barcode, $nombre_concepto,$caja, $caja_abierta, $cost,$price,$pago, $proveedor_id,  $monto_concepto, $monto_inicial, $stock,$alerts,$categoryid, $codigo, $monto_total, $mail_ingresado, $search, $image, $selected_id, $pageTitle,$componentName,$comercio_id,$data_Cart, $observaciones, $query_product, $products_s , $Cart, $deuda,$metodo_pago_elegido, $product,$total, $iva, $iva_general, $tipo_pago, $mensaje, $apellido_cliente, $concepto, $itemsQuantity, $cliente_cuit, $metodo_pagos, $cantidad, $carrito, $qty, $tipo_factura, $numero_factura, $query_concepto, $tipo_iva, $relacion_precio_iva, $precio_sin_iva, $nombre_cliente, $tipo_comprobante, $tipo_clave, $cuit, $tipo_persona, $provincia, $direccion, $localidad, $facturar, $ventaId, $mail;
  public $cuit_agregar,$nombre_cliente_agregar,$apellido_cliente_agregar, $genero_dni;
  
  private $pagination = 25;
  public $concepto_rapido = [];

  public function mount()
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    $this->mail = [];

    $this->pageTitle = 'Listado';
    $this->cantidad = 1;
    $this->pago = 0;
    $this->cuit = 0;
    $this->concepto = "VARIOS";
    $this->tipo_pago = 1;
    $this->tipo_iva = 1;
    $this->tipos_pago = [];
    $this->metodo_pago_ap = 'Elegir';
    $this->componentName = 'Productos';
    $this->metodo_pago_elegido = 'Elegir';
    $this->categoryid = 'Elegir';
    
    // Aca seteo facturacion

    $datos_facturacion = datos_facturacion::where('comercio_id',$comercio_id)->where('predeterminado',1)->where('eliminado',0)->first();
    if($datos_facturacion != null){
    $this->facturar = true;
    $this->iva_general = $datos_facturacion->iva_defecto;
    $this->relacion_precio_iva = $datos_facturacion->relacion_precio_iva;    
    if(0 < $this->iva_general){
    $this->tipo_comprobante = 'B';    
    } else {
    if($datos_facturacion->condicion_iva == "Monotributo"){
    $this->tipo_comprobante = 'C';       
    } else {
    $this->tipo_comprobante = 'CF';       
    }   
    }
    } else {
    $this->facturar = false;
    $this->tipo_comprobante = 'CF';
    $this->iva_general = 0;
    $this->relacion_precio_iva = 1;    
    }
    
    if($this->relacion_precio_iva == 1){
        $this->relacion_precio_iva = 2;
    } else {
        $this->relacion_precio_iva = 1;
    }
     
    $this->tipo_factura = 'Elegir';
    $this->metodo_pago_elegido = 1;

    $this->iva_general = session('IvaGral');

    
    if($this->iva_general != null) {
        
      $this->iva_general = session('IvaGral');
      
    } else {

      $this->iva_defecto = datos_facturacion::where('datos_facturacions.comercio_id',$comercio_id)->where('predeterminado',1)->first();

      if($this->iva_defecto != null) {
      $this->iva_general = $this->iva_defecto->iva_defecto;
      session(['IvaGral' => $this->iva_general]);
    } else {
      $this->iva_general = 0;
      session(['IvaGral' => 0]);
    }

    }
    
    




  }

  public function AbrirModalConcepto(){
  $this->emit("agregar-concepto","");
  }

  public function render()
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $cart = new CartCobros;
    $this->monto_total = $cart->totalAmount();
    $this->subtotal = $cart->subtotalAmount();
    $this->iva_total = $cart->totalIva();
    
    


      $products = Product::join('categories as c','c.id','products.category_id')
			->join('seccionalmacens as a','a.id','products.seccionalmacen_id')
			->join('proveedores as pr','pr.id','products.proveedor_id')
			->select('products.*','c.name as category','a.nombre as almacen','pr.nombre as nombre_proveedor')
			->where('products.comercio_id', 'like', $comercio_id)
			->where('products.eliminado', 'like', 0)
			->orderBy('products.name','asc')
			->paginate($this->pagination);


      $this->concepto_rapido = concepto_rapido::where('concepto_rapidos.comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();

      $tipo_pago = bancos::where('bancos.comercio_id', 'like', $comercio_id)->get();

      $proveedores = proveedores::where('proveedores.comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();

      $datos_facturacion = datos_facturacion::where('datos_facturacions.comercio_id',$comercio_id)->first();


  		$caja = cajas::where('estado',0)->where('comercio_id',$comercio_id)->max('id');

  		$this->tipos_pago = bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
        ->where('bancos_muestra_sucursales.muestra', 1)
        ->where('bancos_muestra_sucursales.sucursal_id', $comercio_id)
        ->select('bancos.id','bancos.cbu','bancos.cuit','bancos.tipo','bancos.nombre','bancos.muestra_sucursales','bancos.comercio_id')
        ->groupBy('bancos.id','bancos.cbu','bancos.cuit','bancos.tipo','bancos.nombre','bancos.muestra_sucursales','bancos.comercio_id')
        ->orderBy('bancos.nombre','asc')->get();

  		$this->metodos =  metodo_pago::join('bancos','bancos.id','metodo_pagos.cuenta')
    ->join('metodo_pagos_muestra_sucursales','metodo_pagos_muestra_sucursales.metodo_id','metodo_pagos.id')
    ->where('metodo_pagos_muestra_sucursales.muestra', 1)
    ->where('metodo_pagos_muestra_sucursales.sucursal_id', $comercio_id)
    ->where('metodo_pagos.eliminado',0)
    ->select('metodo_pagos.*','bancos.nombre as nombre_banco');

  		$this->bancos_metodo_pago = bancos::where('bancos.comercio_id', 'like', $comercio_id)
  		->where('bancos.tipo', 'like', 2)
  		->orderBy('bancos.nombre','asc')
  		->get();

  		$this->plataformas_metodo_pago = bancos::where('bancos.comercio_id', 'like', $comercio_id)
  		->where('bancos.tipo', 'like', 3)
  		->orderBy('bancos.nombre','asc')
  		->get();


      		if($this->tipo_pago != 1 &&  $this->tipo_pago != 2 && $this->tipo_pago != null) {
      			$this->metodos = $this->metodos->where('metodo_pagos.cuenta', 'like', $this->tipo_pago);
      		}

      		$this->metodos = $this->metodos->orderBy('metodo_pagos.nombre','asc')->get();

	$this->caja = cajas::where('estado',0)->where('comercio_id',$comercio_id)->max('id');
	
		$this->caja_abierta = cajas::where('estado',0)->where('comercio_id',$comercio_id)->max('id');

    return view('livewire.venta-rapida.component',[
      'data' => $products,
      'metodos' => $this->metodos,
      'tipos' => $this->tipos_pago,
      'proveedores' => $proveedores,
      'tipo_pago' => $tipo_pago,
      'caja' => $this->caja,
      'iva_general' => $this->iva_general,
      'categorias_fabrica' => Category::orderBy('name','asc')->get(),
      'concepto_rapido' => $this->concepto_rapido,
			'bancos_metodo_pago' => $this->bancos_metodo_pago,
			'plataformas_metodo_pago' => $this->plataformas_metodo_pago,
    ])
    ->extends('layouts.theme-pos.app')
    ->section('content');
  }





  public function showCart() {
      return view('livewire.ventas-fabrica.cart')
      ->extends('layouts.theme.app')
      ->section('content');
  }


public function TipoPago($value)
{

	if($value == 'OTRO') {
	$this->emit('tipo-pago-nuevo-show','Sales');
	}


if($value == '2') {
$this->emit('pago-dividido','Sales');
}

if($value == '1' || $value == '2') {

$this->metodo_pago = $value;

$this->metodo_pago = $value;
$this->metodo_pago_nuevo = $value;
session(['MetodoPago' => $value]);


if($value == '1') {
    $this->recargo = 0;
    $this->recargo_total = 0;
    $this->metodo_pago = 1;
    $this->metodo_pago_nuevo = 1;
    session(['MetodoPago' => 1]);
}



} else {
	$this->metodo_pago = 'Elegir';
}

}


  public function Agregar() {

    if($this->monto_concepto == "" || empty($this->monto_concepto)){
        $this->emit("msg-error","Debe agregar el monto");
        return;
    }
    
    $this->iva_general = session('IvaGral');
  
    if($this->iva_general != "Elegir") {
      $this->iva_agregar = $this->iva_general;
    } else {
      $this->iva_agregar = 0;
    }

    if($this->relacion_precio_iva == 1) {

      $this->precio_sin_iva = $this->monto_concepto/(1+$this->iva_agregar) ;

      $this->iva_precio = $this->monto_concepto - $this->precio_sin_iva;

    }

    if($this->relacion_precio_iva == 2) {

      $this->precio_sin_iva = $this->monto_concepto;

      $this->iva_precio = $this->monto_concepto - $this->precio_sin_iva;

    }

    $this->concepto = $this->query_concepto;
    
    if($this->concepto == ""){
        $this->concepto = "VARIOS";
        
    } else {
         $this->concepto = $this->concepto;
    }

    $cart = new CartCobros;
    $items = $cart->getContent();
    $this->cantidad_items = $cart->totalCantidad();

    $this->id = $this->cantidad_items + 1;

      $product = array(
          "id" => $this->id,
          "name" => $this->concepto,
          "cost" => $this->precio_sin_iva,
          "iva" => $this->iva_agregar,
          "qty" => 1,
          "relacion_precio_iva" => $this->relacion_precio_iva
      );

      $cart->addProduct($product);

      $this->monto_total = $cart->totalAmount();
      $this->subtotal = $cart->subtotalAmount();
      $this->iva_total = $cart->totalIva();

      $this->resetUI();

      $this->concepto = "";
      $this->monto_concepto = "";

      $this->emit('product-added','Concepto agregado');


}


    public function removeProductFromCart($id) {
        $cart = new CartCobros;
        $cart->removeProduct($id);
        session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
        return back();
    }

  public function RelacionPrecioIva($value){

    $this->iva_general = session('IvaGral');
    $this->relacion_precio_iva = $value;
    
    if($this->iva_general != "Elegir") {
      $iva = $this->iva_general;
    } else {
      $iva = 0;
    }
     
     
  $cart = new CartCobros;
  $items = $cart->getContent();
  foreach ($items as $i)
   {
    if($this->relacion_precio_iva == 1) {
      $precio_sin_iva = $i['cost']/(1+$iva) ;
      $iva_precio = $i['cost'] - $precio_sin_iva;
    }

    if($this->relacion_precio_iva == 2) {
      $precio_sin_iva = $i['cost'] * (1+$i['iva']);
      $iva_precio = $i['cost'] * ($iva/100);
    }
    
    
    $cart->removeProduct($i['id']);
    $product = array(
          "id" => $i['id'],
          "name" => $i['name'],
          "cost" => $precio_sin_iva,
          "iva" => $iva,
          "qty" => 1,
          "relacion_precio_iva" => $this->relacion_precio_iva
      );

      $cart->addProduct($product);
  }      
  

      $this->monto_total = $cart->totalAmount();
      $this->subtotal = $cart->subtotalAmount();
      $this->iva_total = $cart->totalIva();

      $this->resetUI();

      $this->concepto = "";
      $this->monto_concepto = "";

      $this->emit('product-added','Relacion precio iva modificada');
      
  }
  
  
  public function UpdateIva($id, $iva) {
      $cart = new CartCobros;
      $items = $cart->getContent();


      foreach ($items as $i)
   {
          if($i['id'] === $id) {

            $cart->removeProduct($i['id']);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "iva" => $iva,
                "cost" => $i['cost'],
                "qty" => 1,
            );

            $cart->addProduct($product);

        }


   }

   $this->monto_total = $cart->totalAmount();
   $this->subtotal = $cart->subtotalAmount();
   $this->iva_total = $cart->totalIva();

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }


  public function UpdatePrice(insumo $product, $price) {
      $cart = new CartCobros;
      $items = $cart->getContent();


      if($this->relacion_precio_iva == 1) {

        $this->price = $this->monto_concepto/(1+$this->iva_agregar) ;

        $this->iva_precio = $this->monto_concepto - $this->precio_sin_iva;

      }

      if($this->relacion_precio_iva == 2) {

        $this->price = $this->monto_concepto;

        $this->iva_precio = $this->monto_concepto - $this->precio_sin_iva;

      }



      foreach ($items as $i)
   {
          if($i['id'] === $product['id']) {

            $cart->removeProduct($product['id']);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "iva" => $i['iva'],
                "cost" => $this->price,
                "qty" => $i['qty'],
            );

            $cart->addProduct($product);

        }


   }

   $this->monto_total = $cart->totalAmount();
   $this->subtotal = $cart->subtotalAmount();
   $this->iva_total = $cart->totalIva();

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }





public function UpdateIvaGral() {

  if($this->iva_general != "Elegir") {


    session(['IvaGral' => $this->iva_general]);


    $cart = new CartCobros;
    $items = $cart->getContent();


    foreach ($items as $i)
 {
          $this->precio_con_iva = $i['cost']*(1+$i['iva']);


          if($this->relacion_precio_iva == 1) {

            $this->precio_sin_iva = floatval($this->precio_con_iva)/(1+ floatval($this->iva_general) ) ;

            $this->iva_precio = floatval($this->monto_concepto) - floatval($this->precio_sin_iva) ;

          }

          if($this->relacion_precio_iva == 2) {

            $this->precio_sin_iva = $this->precio_con_iva;

            $this->iva_precio = $this->monto_concepto-$this->precio_sin_iva;

          }

          $cart->removeProduct($i['id']);

          $product = array(
              "id" => $i['id'],
              "name" => $i['name'],
              "iva" => $this->iva_general,
              "cost" => $this->precio_sin_iva,
              "qty" => 1,
          );

          $cart->addProduct($product);

 }

 $this->monto_total = $cart->totalAmount();
 $this->subtotal = $cart->subtotalAmount();
 $this->iva_total = $cart->totalIva();

    session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
    return back();


  }


}


  // escuchar eventos
  protected $listeners = [
    'scan-code'  =>  'BuscarCode',
    	'clearCart'  => 'clearCart',
      	'TeclaRapida'  => 'TeclaRapida',
  ];



public function TeclaRapida($i)
{
dd($i);
}

  public function BuscarCode($barcode)
  {



    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $record = insumo::where('barcode',$barcode)->where('comercio_id', $comercio_id)->first();

    if($record == null || empty($record))
    {

    $this->emit('scan-notfound','El insumo no está registrado');

    $this->codigo = '';

    }  else {

    $this->selected_id = $record->id;
    $this->name = $record->name;
    $this->barcode = $record->barcode;
    $this->cost = $record->cost;
    $this->price = $record->price;
    $this->stock = $record->stock;
    $this->categoryid = $record->categorias_fabrica_id;
    $this->image = null;

    $this->emit('show-modal','Show modal!');

    $this->codigo = '';
  }

}

  public function AbrirModal($id)
	{
		$record = insumo::find($id);

		$this->selected_id = $record->id;
		$this->name = $record->name;
		$this->barcode = $record->barcode;
		$this->cost = $record->cost;
		$this->price = $record->price;
		$this->stock = $record->stock;
		$this->categoryid = $record->categorias_fabrica_id;
		$this->image = null;

		$this->emit('show-modal','Show modal!');
	}

  public function AgregarNroFactura()
  {
      $this->emit('show-modal2','Show modal!');
  }

  public function Edit2($monto_total)
	{


    $this->monto_total = $monto_total;

		$this->emit('show-modal2','Show modal!');
	}

  public function MontoPago()
  {

    $this->deuda = $this->monto_total - $this->pago;

  }

  public function orders()
  {
      $orders = auth()->user()->processedOrders();
      $suma = 0;
      return view('products.orders', compact('orders', 'suma'));
  }
  public function resetUIMount(){
      
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    $this->mail = [];

    $this->pageTitle = 'Listado';
    $this->cantidad = 1;
    $this->pago = 0;
    $this->cuit = 0;
    $this->concepto = "VARIOS";
    $this->tipo_pago = 1;
    $this->tipo_iva = 1;
    $this->tipos_pago = [];
    $this->metodo_pago_ap = 'Elegir';
    $this->componentName = 'Productos';
    $this->metodo_pago_elegido = 'Elegir';
    $this->categoryid = 'Elegir';
    
    // Aca seteo facturacion

    $datos_facturacion = datos_facturacion::where('comercio_id',$comercio_id)->where('predeterminado',1)->where('eliminado',0)->first();
    if($datos_facturacion != null){
    $this->facturar = true;
    $this->iva_general = $datos_facturacion->iva_defecto;
    $this->relacion_precio_iva = $datos_facturacion->relacion_precio_iva;    
    if(0 < $this->iva_general){
    $this->tipo_comprobante = 'B';    
    } else {
    if($datos_facturacion->condicion_iva == "Monotributo"){
    $this->tipo_comprobante = 'C';       
    } else {
    $this->tipo_comprobante = 'CF';       
    }   
    }
    } else {
    $this->facturar = false;
    $this->tipo_comprobante = 'CF';
    $this->iva_general = 0;
    $this->relacion_precio_iva = 1;    
    }
    
    if($this->relacion_precio_iva == 1){
        $this->relacion_precio_iva = 2;
    } else {
        $this->relacion_precio_iva = 1;
    }
     
    $this->tipo_factura = 'Elegir';
    $this->metodo_pago_elegido = 1;

    $this->iva_general = session('IvaGral');

    
    if($this->iva_general != null) {
        
      $this->iva_general = session('IvaGral');
      
    } else {

      $this->iva_defecto = datos_facturacion::where('datos_facturacions.comercio_id',$comercio_id)->where('predeterminado',1)->first();

      if($this->iva_defecto != null) {
      $this->iva_general = $this->iva_defecto->iva_defecto;
      session(['IvaGral' => $this->iva_general]);
    } else {
      $this->iva_general = 0;
      session(['IvaGral' => 0]);
    }

    }
    
    

  }
  // reset values inputs
  public function resetUI()
  {
    
    $this->factura = null;
    $this->metodo_pago_elegido = 1;
    $this->name ='';
    $this->cantidad =1;
    $this->barcode ='';
    $this->cost ='';
    $this->price ='';
    $this->stock ='';
    $this->alerts ='';
    $this->search ='';
    $this->categoryid = 'Elegir';
    $this->image = null;
    $this->selected_id = 0;

  }

public function resetUIDNI(){
    $this->emit("dni-hide","");
    $this->ResetCuit();
}

public function getCuilCuit() {
    
    if($this->genero_dni == "Elegir" || $this->genero_dni == null){
    $this->emit("msg-error","Debe elegir un genero");
    return;
    }
    
    $document_number = $this->cliente_cuit;
    $gender = $this->genero_dni;

    // Define constants for gender
    $HOMBRE = ["HOMBRE", "M", "MALE"];
    $MUJER = ["MUJER", "F", "FEMALE"];
    $SOCIEDAD = ["SOCIEDAD", "S", "SOCIETY"];

    // Check if the document number has exactly 8 digits
    if (strlen($document_number) != 8 || !is_numeric($document_number)) {
        if (strlen($document_number) == 7 && is_numeric($document_number)) {
            $document_number = "0" . $document_number;
        } else {
            return "El número de documento ingresado no es correcto.";
        }
    }

    // Convert gender to uppercase
    $gender = strtoupper($gender);

    // Define the prefix value
    if (in_array($gender, $HOMBRE)) {
        $AB = "20";
    } elseif (in_array($gender, $MUJER)) {
        $AB = "27";
    } else {
        $AB = "30";
    }

    // Array of multipliers
    $multiplicadores = [3, 2, 7, 6, 5, 4, 3, 2];

    // Perform the initial calculations
    $calculo = intval($AB[0]) * 5 + intval($AB[1]) * 4;

    // Loop through the document number and perform the multiplications
    for ($i = 0; $i < 8; $i++) {
        $calculo += intval($document_number[$i]) * $multiplicadores[$i];
    }

    // Calculate the remainder
    $resto = $calculo % 11;

    // Determine the value of C and adjust the prefix value if necessary
    if (!in_array($gender, $SOCIEDAD) && $resto == 1) {
        if (in_array($gender, $HOMBRE)) {
            $C = "9";
        } else {
            $C = "4";
        }
        $AB = "23";
    } elseif ($resto === 0) {
        $C = "0";
    } else {
        $C = 11 - $resto;
    }

    // Generate the full CUIT
    $cuil_cuit = $AB . $document_number . $C;

    $this->cliente_cuit = $cuil_cuit;
    $this->emit("dni-hide","");
    
    $this->BuscarClienteAFIP();
}
 
  public function BuscarCuitDNI(){

  $this->ResetCuit();
  
  $cuit = $this->cliente_cuit;
  
  // Validar que el CUIT tenga al menos 10 dígitos
  if (strlen($cuit) < 10) {
        $this->emit("dni-show", "");
        return;
  }      
  
      
  }
  
  
  public function BuscarClienteAFIP() {

  $this->ResetCuit();
  
  $cuit = $this->cliente_cuit;

  // Validar que el CUIT tenga al menos 10 dígitos
  if (strlen($cuit) < 10) {
        $this->emit("dni-show", "");
        return;
  }  

  /**
  * Obtenemos los datos del contribuyente
  **/

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $this->datos_facturacion = datos_facturacion::where('comercio_id', $comercio_id)->where('predeterminado',1)->where('eliminado',0)->first();
  
  if($this->datos_facturacion != null){
  if($this->datos_facturacion->cuit != null || $this->datos_facturacion->cuit != '') {


  $afip = new Afip(array('CUIT' => '20358072101', 'production' => true));


  /**
   * Obtenemos los datos del contribuyente
   **/
  $datos = $afip->RegisterScopeFive->GetTaxpayerDetails($cuit);

  if($datos === NULL){
  	$this->mensaje = 'El contribuyente no existe.';
  }
  else{
  	/**

  	 * Mostramos por pantalla los datos del contribuyente
  	 **/
  	 
if($datos->datosGenerales->tipoPersona == "JURIDICA") {
    
     $this->cuit = $datos->datosGenerales->idPersona;
       $this->nombre_cliente = $datos->datosGenerales->razonSocial;
       $this->tipo_clave = $datos->datosGenerales->tipoClave;
       $this->tipo_persona = $datos->datosGenerales->tipoPersona;
       $this->provincia = $datos->datosGenerales->domicilioFiscal->descripcionProvincia;
       $this->direccion = $datos->datosGenerales->domicilioFiscal->direccion;
 
       
       if($this->provincia != "CIUDAD AUTONOMA BUENOS AIRES") {
           $this->localidad = $datos->datosGenerales->domicilioFiscal->localidad;
       } else {
           $this->localidad = "";
       }
       
    
    
    
} else {
    
     $this->cuit = $datos->datosGenerales->idPersona;
       $this->nombre_cliente = $datos->datosGenerales->nombre;
       $this->apellido_cliente = $datos->datosGenerales->apellido;
       $this->tipo_clave = $datos->datosGenerales->tipoClave;
       $this->tipo_persona = $datos->datosGenerales->tipoPersona;


       $this->provincia = $datos->datosGenerales->domicilioFiscal->descripcionProvincia;
       $this->direccion = $datos->datosGenerales->domicilioFiscal->direccion;
       
       if($this->provincia != "CIUDAD AUTONOMA BUENOS AIRES") {
           $this->localidad = $datos->datosGenerales->domicilioFiscal->localidad;
       } else {
           $this->localidad = "";
       }
       
}


    $this->emit('datos-cliente','');

  }


  } else {
      $this->emit("msg-error","Tenes que agregar los datos de tu punto de venta para buscar un cliente");
  }
  } else {
      $this->emit("msg-error","Tenes que agregar los datos de tu punto de venta y configurarlo como predeterminado para buscar un cliente");
  }
  }
  // reset values inputs
  public function resetCliente()
  {
    $this->cliente_cuit ='';

  }

  // reset values inputs
  public function AgregarCliente()
  {
    $this->cuit = $this->cliente_cuit;


    		if(Auth::user()->comercio_id != 1)
    		$comercio_id = Auth::user()->comercio_id;
    		else
    		$comercio_id = Auth::user()->id;

        $cliente = ClientesMostrador::where('dni',$this->cuit)->where('comercio_id',$comercio_id)->first();

        if($cliente == null) {

    		$cliente = ClientesMostrador::create([
    			'nombre' => $this->nombre_cliente." ".$this->apellido_cliente,
    			'direccion' => $this->direccion,
    			'localidad' => $this->localidad,
    			'provincia' => $this->provincia,
    			'status' => 'Activo',
    			'dni' => $this->cuit,
    			'comercio_id' => $comercio_id,
    			'creador_id' => $comercio_id
    			]);

          }
    
    $this->cuit_agregar = $this->cuit;
    $this->nombre_cliente_agregar = $this->nombre_cliente." ".$this->apellido_cliente;
    
    $this->emit('datos-cliente-hide','');

  }
  
  public function ResetCuit(){
  $this->nombre_cliente = null;
  $this->apellido_cliente = null;
  $this->direccion = null;
  $this->localidad = null;
  $this->provincia = null;
  $this->cuit = null;
  $this->cuit_agregar = null;
  $this->nombre_agregar = null;
  $this->genero_dni = null;
  }
  
  public function ResetearCuit(){
      $this->cliente_cuit = null;
      $this->ResetCuit();
  }
  
  
  // guardar venta
  public function saveSale()
  {
    $cart = new CartCobros;

    $this->iva_total = $cart->totalIva();

    if($this->tipo_comprobante == "A" && $this->cuit == 0)
    {
      $this->emit('msg-error','DEBE INGRESAR EL CUIT DEL USUARIO');
      return;
    }

    if($this->metodo_pago_elegido == "Elegir")
    {
      $this->emit('msg-error','DEBE ELEGIR LA FORMA DE PAGO');
      return;
    }
    if(0 < $this->iva_total && $this->tipo_comprobante == "C")
    {
      $this->emit('msg-error','NO SE PUEDE EMITIR COMPROBANTE TIPO C CON IVA. ');
      return;
    }

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $this->caja = cajas::where('estado',0)->where('comercio_id',$comercio_id)->max('id');

    $cart = new CartCobros;

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $this->cliente_id = ClientesMostrador::where('dni',$this->cuit)->where('comercio_id',$comercio_id)->first();
    
    if($this->cliente_id == null) {
        $this->cliente_elegido = 1;
        
    } else {
        $this->cliente_elegido = $this->cliente_id->id;
    }
    
    
    DB::beginTransaction();

    try {

      $this->total = $cart->totalAmount();
      $this->sub_total = $cart->subtotalAmount();
      $this->iva_total = $cart->totalIva();
      $this->deuda = $this->monto_total - $this->pago;
      

            $sale = cobro_rapido::create([
              'subtotal' => $this->subtotal,
              'total' => $this->total,
              'items' => $cart->totalCantidad(),
              'comercio_id' => $comercio_id,
              'user_id' => Auth::user()->id,
              'cliente_id' => $this->cliente_elegido,
              'metodo_pago' => $this->metodo_pago_elegido,
              'caja' => $this->caja,
              'tipo_comprobante' => $this->tipo_comprobante,
              'iva' => $cart->totalIva(),

            ]);

            $pagos = pagos_facturas::create([
              'monto_cobro_rapido' => $this->total,
              'id_cobro_rapido' => $sale->id,
              'comercio_id' => $comercio_id,
              'caja' => $this->caja,
              'metodo_pago'  => $this->metodo_pago_elegido,
              'cliente_id' => $this->cliente_elegido,
              'tipo_pago' => 1,
              'eliminado' => 0
            ]);

      if($sale)
      {
          $items = $cart->getContent();

        foreach ($items as  $item) {
          cobro_rapidos_detalle::create([
            'cobro_rapido_id' => $sale->id,
            'monto' => $item['cost'],
            'concepto' => $item['name'],
            'iva' => $item['iva']*$item['cost'],
            'alicuota_iva' => $item['iva'],
            'compra_id' => $sale->id,
            'comercio_id' => $comercio_id
          ]);


        }

      }


      if($this->facturar == true) {

        $this->FacturarAfip($sale->id);

      }
      $ventaId = $sale->id;
			$this->ventaId = $sale->id;

			$this->mail = cobro_rapido::join('clientes_mostradors as c','c.id','cobro_rapidos.cliente_id')
			->select('c.email')
			->where('cobro_rapidos.id', $ventaId)
			->get();


      $this->emit('modal-imprimir', $sale->id);

      DB::commit();

      $this->pago = 0;
      $this->deuda = 0;
      $this->observaciones = '';
      $this->numero_factura = '';
      $this->tipo_factura = 'Elegir';
      
      $this->monto = 0;
      $this->cuit = "";
      $this->metodo_pago_elegido = 'Elegir';
      $this->proveedor_id = 'Elegir';

      $cart->clear();
      $this->ResetCuit();
      
      $this->resetUIMount();
      
      $this->emit('sale-ok','Cobro registrado con éxito');



    } catch (Exception $e) {
      DB::rollback();
      $this->emit('sale-error', $e->getMessage());
    }

  }


  public function resetProduct()
 {
   $this->products_s = [];
 }

 public function clearCart() {
$cart = new CartCobros;
  $cart->clear();
 }

public function AgregarConceptoRapido($concepto) {

  $this->concepto = concepto_rapido::find($concepto);

  $this->concepto = $this->concepto->nombre;

  $this->emit('show-modal','Show modal!');
}


  public function selectConcepto()
  {
    $this->concepto = $this->query_concepto;

    $this->emit('show-modal','Show modal!');
  }


  public function updatedQueryProduct()
  {
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;



      $this->products_s = 	insumo::where('comercio_id', 'like', $comercio_id)->where('eliminado',0)->where( function($query) {
            $query->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
        })
          ->limit(5)
          ->get()
          ->toArray();



  }





    //////////////////////   CONCEPTOS RAPIDOS   /////////////////////////////////

      public function CreateConcepto()
      {
        if(Auth::user()->comercio_id != 1)
        $comercio_id = Auth::user()->comercio_id;
        else
        $comercio_id = Auth::user()->id;

        $etiqueta = concepto_rapido::create([
          'nombre' => $this->nombre_concepto,
          'comercio_id' => $comercio_id
        ]);

        $this->concepto_rapido = concepto_rapido::where('concepto_rapidos.comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();



        $this->GetEtiqueta();

      }


  public function GetEtiqueta()
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $this->concepto_rapido = concepto_rapido::where('concepto_rapidos.comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();

    $this->emit('tabs-show','Show modal');
  }




  public function MetodoPago($value)
  {

  	$this->metodo_pago_elegido = $value;

  }





  							public function FacturarAfip($ventaIdFactura)
  							{

  							if(Auth::user()->comercio_id != 1)
  							$comercio_id = Auth::user()->comercio_id;
  							else
  							$comercio_id = Auth::user()->id;

  							$this->datos_facturacion = datos_facturacion::where('comercio_id', $comercio_id)->first();


                            if($this->datos_facturacion->cuit != null || $this->datos_facturacion->cuit != '') {


  							$afip = new Afip(array('CUIT' => $this->datos_facturacion->cuit, 'production' => true));

  							/**
  							* Numero del punto de venta
  							**/
  							$punto_de_venta = $this->datos_facturacion->pto_venta;



  							$this->factura = cobro_rapido::join('clientes_mostradors','clientes_mostradors.id','cobro_rapidos.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','cobro_rapidos.*')->find($ventaIdFactura);


  							if($this->factura->tipo_comprobante == 'C' || $this->factura->tipo_comprobante == 'CF') {
  							/**
  							 * Tipo de factura
  							 **/
  							  $tipo_de_comprobante = 11; // 11 = Factura C

  							  if($tipo_de_comprobante == 11) {
  							  $this->tipo_factura = 'C';
  							  }

  							  /**
  							 * Número de la ultima Factura C
  							 **/
  							$last_voucher = $afip->ElectronicBilling->GetLastVoucher($punto_de_venta, $tipo_de_comprobante);

  							/**
  							 * Concepto de la factura
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
                 * Numero de comprobante
                 **/
                $numero_de_factura = $last_voucher+1;

                $this->numero_factura = $numero_de_factura;
                /**
                 * Fecha de la factura en formato aaaa-mm-dd (hasta 10 dias antes y 10 dias despues)
                 **/
                $fecha = date('Y-m-d');

                /**
                 * Importe de la Factura
                 **/


                $importe_total = $this->factura->total;

                /**
                 * Los siguientes campos solo son obligatorios para los conceptos 2 y 3
                 **/
                	$fecha_servicio_desde = null;
                	$fecha_servicio_hasta = null;
                	$fecha_vencimiento_pago = null;


                $data = array(
                	'CantReg' 	=> 1, // Cantidad de facturas a registrar
                	'PtoVta' 	=> $punto_de_venta,
                	'CbteTipo' 	=> $tipo_de_comprobante,
                	'Concepto' 	=> $concepto,
                	'DocTipo' 	=> $tipo_de_documento,
                	'DocNro' 	=> $numero_de_documento,
                	'CbteDesde' => $numero_de_factura,
                	'CbteHasta' => $numero_de_factura,
                	'CbteFch' 	=> intval(str_replace('-', '', $fecha)),
                	'FchServDesde'  => $fecha_servicio_desde,
                	'FchServHasta'  => $fecha_servicio_hasta,
                	'FchVtoPago'    => $fecha_vencimiento_pago,
                	'ImpTotal' 	=> $importe_total,
                	'ImpTotConc'=> 0, // Importe neto no gravado
                	'ImpNeto' 	=> $importe_total, // Importe neto
                	'ImpOpEx' 	=> 0, // Importe exento al IVA
                	'ImpIVA' 	=> 0, // Importe de IVA
                	'ImpTrib' 	=> 0, //Importe total de tributos
                	'MonId' 	=> 'PES', //Tipo de moneda usada en la factura ('PES' = pesos argentinos)
                	'MonCotiz' 	=> 1, // Cotización de la moneda usada (1 para pesos argentinos)
                );

                /**
                 * Creamos la Factura
                 **/
                $res = $afip->ElectronicBilling->CreateVoucher($data);

                /**
                 * Mostramos por pantalla los datos de la nueva Factura
                 **/


                  $this->factura->update([
                    'cae' => $res['CAE'], //CAE asignado a la Factura
                    'vto_cae' => $res['CAEFchVto'], //Fecha de vencimiento del CAE
                    'nro_factura' => $this->tipo_factura.'-'.$punto_de_venta.'-'.$this->numero_factura
                    ]);

                    $this->emit('pago-actualizado', 'FACTURA GENERADA CORRECTAMENTE');


                  }

  								                if($this->factura->tipo_comprobante == 'B') {

  								                  $this->tipo_factura = 'FB';

  								                                    /**
  								                   * Numero del punto de venta
  								                   **/

  								                  /**
  								                   * Tipo de factura
  								                   **/
  								                  $tipo_de_factura = 6; // 6 = Factura B


  								                  $this->factura = cobro_rapido::join('clientes_mostradors','clientes_mostradors.id','cobro_rapidos.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','cobro_rapidos.*')->find($ventaIdFactura);
  								                  /**
  								                   * Número de la ultima Factura B
  								                   **/
  								                  $last_voucher = $afip->ElectronicBilling->GetLastVoucher($punto_de_venta, $tipo_de_factura);

  								                  /**
  								                   * Concepto de la factura
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

  								                  if ($this->factura->dni == 0) {
  								                  $tipo_de_documento = 99;
  								                  } else {
  								                  $tipo_de_documento = 80;    
  								                  }

  								                  $numero_de_documento = $this->factura->dni;


  								                  /**
  								                   * Numero de documento del comprador (0 para consumidor final)
  								                   **/

  								                  /**
  								                   * Numero de factura
  								                   **/
  								                  $numero_de_factura = $last_voucher+1;

  								                  $this->numero_factura = $numero_de_factura;

  								                  /**
  								                   * Fecha de la factura en formato aaaa-mm-dd (hasta 10 dias antes y 10 dias despues)
  								                   **/
  								                  $fecha = date('Y-m-d');

  								                  /**
  								                   * Importe sujeto al IVA (sin icluir IVA)
  								                   **/
  								                  $importe_gravado = floatval($this->factura->subtotal);
  								    
  								                  /**
  								                   * Importe de IVA
  								                   **/
  								                  $importe_iva = floatval(($importe_gravado*0.21));
  								                  
  								                  $importe_iva = number_format((float)$importe_iva, 2, '.', '');
                                                  
                                                  $importe_iva = floatval($importe_iva);
                                                          
  								                  /**
  								                   * Importe exento al IVA
  								                   **/
  								                  $importe_exento_iva = 0;
  								                  
                                                  $importe_total = $importe_gravado +  $importe_iva + $importe_exento_iva;
                                                  
                                                  $importe_total = number_format((float)$importe_total, 2, '.', '');
                                                  
                                                  $importe_total = floatval($importe_total);
                                                  
                                                  
  								                  /**
  								                   * Los siguientes campos solo son obligatorios para los conceptos 2 y 3
  								                   **/


  								                  	$fecha_servicio_desde = null;
  								                  	$fecha_servicio_hasta = null;
  								                  	$fecha_vencimiento_pago = null;


  								                  $data = array(
  								                  	'CantReg' 	=> 1, // Cantidad de facturas a registrar
  								                  	'PtoVta' 	=> $punto_de_venta,
  								                  	'CbteTipo' 	=> $tipo_de_factura,
  								                  	'Concepto' 	=> $concepto,
  								                  	'DocTipo' 	=> $tipo_de_documento,
  								                  	'DocNro' 	=> $numero_de_documento,
  								                  	'CbteDesde' => $numero_de_factura,
  								                  	'CbteHasta' => $numero_de_factura,
  								                  	'CbteFch' 	=> intval(str_replace('-', '', $fecha)),
  								                  	'FchServDesde'  => $fecha_servicio_desde,
  								                  	'FchServHasta'  => $fecha_servicio_hasta,
  								                  	'FchVtoPago'    => $fecha_vencimiento_pago,
  								                  	'ImpTotal' 	=> $importe_total,
  								                  	'ImpTotConc'=> 0, // Importe neto no gravado
  								                  	'ImpNeto' 	=> $importe_gravado,
  								                  	'ImpOpEx' 	=> $importe_exento_iva,
  								                  	'ImpIVA' 	=> $importe_iva,
  								                  	'ImpTrib' 	=> 0, //Importe total de tributos
  								                  	'MonId' 	=> 'PES', //Tipo de moneda usada en la factura ('PES' = pesos argentinos)
  								                  	'MonCotiz' 	=> 1, // Cotización de la moneda usada (1 para pesos argentinos)
  								                  	'Iva' 		=> array(// Alícuotas asociadas al factura
  								                  		array(
  								                  			'Id' 		=> 5, // Id del tipo de IVA (5 = 21%)
  								                  			'BaseImp' 	=> $importe_gravado,
  								                  			'Importe' 	=> $importe_iva
  								                  		)
  								                  	),
  								                  );
  								                  

  								                  /**
  								                   * Creamos la Factura
  								                   **/
  								                  $res = $afip->ElectronicBilling->CreateVoucher($data);

  								                  /**
  								                   * Mostramos por pantalla los datos de la nueva Factura
  								                   **/
  								                   $this->factura->update([
  								                     'cae' => $res['CAE'], //CAE asignado a la Factura
  								                     'vto_cae' => $res['CAEFchVto'], //Fecha de vencimiento del CAE
  								                     'nro_factura' => $this->tipo_factura.'-'.$punto_de_venta.'-'.$this->numero_factura
  								                     ]);

  								                     $this->emit('pago-actualizado', 'FACTURA GENERADA CORRECTAMENTE');


  								                }



                  if($this->factura->tipo_comprobante == 'A') {



                      $this->factura = cobro_rapido::join('clientes_mostradors','clientes_mostradors.id','cobro_rapidos.cliente_id')->select('clientes_mostradors.id as cliente_id','clientes_mostradors.dni','cobro_rapidos.*')->find($ventaIdFactura);
  								                      /**
                          * Tipo de factura
                          **/
                          $tipo_de_factura = 1; // 1 = Factura A

                          $this->tipo_factura = 'FA';

                          /**
                          * Número de la ultima Factura A
                          **/
                          $last_voucher = $afip->ElectronicBilling->GetLastVoucher($punto_de_venta, $tipo_de_factura);

                          /**
                          * Concepto de la factura
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
                          $tipo_de_documento = 80;

                          /**
                          * Numero de documento del comprador (0 para consumidor final)
                          **/
                          $numero_de_documento = $this->factura->dni;

                          /**
                          * Numero de factura
                          **/
                          $numero_de_factura = $last_voucher+1;

                          $this->numero_factura = $numero_de_factura;

                          /**
                          * Fecha de la factura en formato aaaa-mm-dd (hasta 10 dias antes y 10 dias despues)
                          **/
                          $fecha = date('Y-m-d');


                          /**
  								                    /**
  								                   * Importe sujeto al IVA (sin icluir IVA)
  								                   **/
  								                  $importe_gravado = floatval($this->factura->subtotal);
  								    
  								                  /**
  								                   * Importe de IVA
  								                   **/
  								                  $importe_iva = floatval(($importe_gravado*0.21));
  								                  
  								                  $importe_iva = number_format((float)$importe_iva, 2, '.', '');
                                                  
                                                  $importe_iva = floatval($importe_iva);
                                                          
  								                  /**
  								                   * Importe exento al IVA
  								                   **/
  								                  $importe_exento_iva = 0;
  								                  
                                                  $importe_total = $importe_gravado +  $importe_iva + $importe_exento_iva;
                                                  
                                                  $importe_total = number_format((float)$importe_total, 2, '.', '');
                                                  
                                                  $importe_total = floatval($importe_total);
                                             


                          $fecha_servicio_desde = null;
                          $fecha_servicio_hasta = null;
                          $fecha_vencimiento_pago = null;

                          $data = array(
                          'CantReg' 	=> 1, // Cantidad de facturas a registrar
                          'PtoVta' 	=> $punto_de_venta,
                          'CbteTipo' 	=> $tipo_de_factura,
                          'Concepto' 	=> $concepto,
                          'DocTipo' 	=> $tipo_de_documento,
                          'DocNro' 	=> $numero_de_documento,
                          'CbteDesde' => $numero_de_factura,
                          'CbteHasta' => $numero_de_factura,
                          'CbteFch' 	=> intval(str_replace('-', '', $fecha)),
                          'FchServDesde'  => $fecha_servicio_desde,
                          'FchServHasta'  => $fecha_servicio_hasta,
                          'FchVtoPago'    => $fecha_vencimiento_pago,
                          'ImpTotal' 	=> $importe_total,
                          'ImpTotConc'=> 0, // Importe neto no gravado
                          'ImpNeto' 	=> $importe_gravado,
                          'ImpOpEx' 	=> $importe_exento_iva,
                          'ImpIVA' 	=> $importe_iva,
                          'ImpTrib' 	=> 0, //Importe total de tributos
                          'MonId' 	=> 'PES', //Tipo de moneda usada en la factura ('PES' = pesos argentinos)
                          'MonCotiz' 	=> 1, // Cotización de la moneda usada (1 para pesos argentinos)
                          'Iva' 		=> array(// Alícuotas asociadas al factura
                            array(
                              'Id' 		=> 5, // Id del tipo de IVA (5 = 21%)
                              'BaseImp' 	=> $importe_gravado,
                              'Importe' 	=> $importe_iva
                            )
                          ),
                          );

                          /**
                          * Creamos la Factura
                          **/
                          $res = $afip->ElectronicBilling->CreateVoucher($data);

                          /**
                          * Mostramos por pantalla los datos de la nueva Factura
                          **/
                          $this->factura->update([
                            'cae' => $res['CAE'], //CAE asignado a la Factura
                            'vto_cae' => $res['CAEFchVto'], //Fecha de vencimiento del CAE
                            'nro_factura' => $this->tipo_factura.'-'.$punto_de_venta.'-'.$this->numero_factura
                            ]);

                            $this->emit('pago-actualizado', 'FACTURA A GENERADA CORRECTAMENTE');

              }


                              } else {

                              $this->emit('no-factura', '');

                              return;

                              }


  }







  public function CodigoBarrasAfip($ventaId) {

    /////////////// CODIGO DE BARRAS AFIP ///////////////////

    $this->total_total2 = cobro_rapido::join('metodo_pagos as m','m.id','sales.metodo_pago')
    ->select('cobro_rapidos.recargo','cobro_rapidos.tipo_comprobante','cobro_rapidos.created_at','cobro_rapidos.total','cobro_rapidos.created_at as fecha', 'm.nombre as metodo_pago','cobro_rapidos.cae','cobro_rapidos.vto_cae','cobro_rapidos.nro_factura')
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
    $this->codigo_barra_afip = $barcode;


  }


public function MailModal($ventaId) {
    $this->ventaId = $ventaId;
     $this->emit('mail-modal', '');
     
}




public function EnviarMail() {
    
     
      return redirect('report-email-rapido/pdf' . '/' . $this->ventaId  . '/' . $this->mail_ingresado);
      
}


									 public function AbrirCaja() {


								     if(Auth::user()->comercio_id != 1)
								     $comercio_id = Auth::user()->comercio_id;
								     else
								     $comercio_id = Auth::user()->id;

								     $ultimo = cajas::where('cajas.comercio_id', 'like', $comercio_id)->select('cajas.nro_caja')->latest('nro_caja')->first();

								     if($ultimo != null)
								     $nro = $ultimo->nro_caja + 1;
								     else
								     $nro = 1;



								     $cajas = cajas::create([
								       'user_id' => Auth::user()->id,
								       'comercio_id' => $comercio_id,
								       'nro_caja' => $nro,
								       'monto_inicial' => $this->monto_inicial,
								       'estado' => '0',
								       'fecha_inicio' => Carbon::now()

								     ]);


								   }

}
