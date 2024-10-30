<?php

namespace App\Http\Livewire;

use App\Services\CartPedidosSucursales;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Session;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\proveedores;
use App\Models\Category;
use App\Models\User;
use App\Models\sucursales;
use App\Models\metodo_pago;
use App\Models\cajas;
use App\Models\productos_stock_sucursales;
use App\Models\historico_stock;
use App\Models\pagos_facturas;
use App\Models\Product;
use Carbon\Carbon;
use App\Models\bancos;
use App\Models\productos_variaciones_datos;
use App\Models\atrobutos;
use App\Models\variaciones;
use App\Models\compras_proveedores;
use App\Models\productos_variaciones;
use App\Models\detalle_compra_proveedores;
use DB;

class PedidosSucursalController extends Component
{
  use WithPagination;
  use WithFileUploads;

  public $name,$barcode,$cost,$price,$pago,$id_cart,  $proveedor_id, $caja, $stock,$alerts,$categoryid,$monto_inicial, $caja_abierta, $codigo, $monto_total, $search, $image, $product_id, $pageTitle,$componentName,$comercio_id,$data_Cart, $observaciones, $query_product, $products_s , $Cart, $deuda,$metodo_pago_elegido, $product,$total, $iva, $iva_general, $itemsQuantity, $cantidad,  $carrito, $qty, $tipo_factura, $numero_factura, $orderby_id;
  private $pagination = 25;


  public $productos_variaciones_datos = [];

  public function mount()
  {
    $this->pageTitle = 'Listado';
    $this->cantidad = 1;
    $this->pago = 0;
    $this->iva_general = 0;
    $this->componentName = 'Productos';
    $this->metodo_pago_elegido = 'Elegir';
    $this->categoryid = 'Elegir';
    $this->tipo_factura = 'Elegir';
    $this->iva_general = session('IvaGral');
  }

  public function render()
  {



    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

  $this->tipo_usuario = User::find(Auth::user()->id);

  if($this->tipo_usuario->sucursal != 1) {
    $this->casa_central_id = $comercio_id;
  } else {

    $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
    $this->casa_central_id = $this->casa_central->casa_central_id;
  }


    $cart = new CartPedidosSucursales;
    $this->monto_total = $cart->totalAmount();
    $this->subtotal = $cart->subtotalAmount();
    $this->iva_total = $cart->totalIva();

  
    $this->caja_abierta = cajas::where('estado',0)->where('comercio_id',$comercio_id)->max('id');


      $products = Product::join('categories as c','c.id','products.category_id')
			->join('seccionalmacens as a','a.id','products.seccionalmacen_id')
			->join('proveedores as pr','pr.id','products.proveedor_id')
			->select('products.*','c.name as category','a.nombre as almacen','pr.nombre as nombre_proveedor')
			->where('products.comercio_id', 'like', $this->casa_central_id)
			->where('products.eliminado', 'like', 0)
			->orderBy('products.name','asc')
			->paginate($this->pagination);

      $metodo_pagos = bancos::where('bancos.comercio_id', 'like', $this->casa_central_id)->get();

      $proveedores = proveedores::where('proveedores.comercio_id', 'like', $this->casa_central_id)->where('eliminado',0)->get();

    return view('livewire.pedidos-sucursales.component',[
      'data' => $products,
      'proveedores' => $proveedores,
      'metodo_pago' => $metodo_pagos,
      'categorias_fabrica' => Category::orderBy('name','asc')->get()
    ])
    ->extends('layouts.theme.app')
    ->section('content');
  }




  public function showCart() {
      return view('livewire.ventas-fabrica.cart')
      ->extends('layouts.theme.app')
      ->section('content');
  }

public function ACash() {

  $this-> pago = $this->monto_total;
  $this->deuda = $this->monto_total - $this->pago;

    
}




  public function Agregar() {
	  //Aumentar id order by
     $this->orderby_id =  $this->orderby_id + 1;


    $this->iva_general = session('IvaGral');

    if($this->iva_general != "Elegir") {
      $this->iva_agregar = $this->iva_general;
    } else {
      $this->iva_agregar = 0;
    }


    $cart = new CartPedidosSucursales;
    $items = $cart->getContent();

   

 if ($items->contains('id', $this->id_cart)) {

   $cart = new CartPedidosSucursales;
   $items = $cart->getContent();
   
 
   $product = Product::find($this->product_id);

   foreach ($items as $i)
{
  
       if($i['id'] === $product['id']) {

         $cart->removeProduct($i['id']);

         $product = array(
             "id" => $i['id'],
            "barcode" => $i['barcode'],
             "name" => $i['name'],
             "referencia_variacion" => $i['referencia_variacion'],
             "product_id" => $i['product_id'],
             "iva" => $i['iva'],
             "cost" => $i['cost'],
             "qty" => $i['qty']+1,
             "orderby_id"=> $i['orderby_id'],
         );

         $cart->addProduct($product);

     }
}

    $this->resetUI();

    $this->emit('product-added','Producto agregado');

    $this->monto_total = $cart->totalAmount();
    $this->subtotal = $cart->subtotalAmount();
    $this->iva_total = $cart->totalIva();
    
    
   $this->deuda = $this->monto_total - $this->pago;

   return back();

} else {

      $cart = new CartPedidosSucursales;

    $this->cost = floatval($this->cost);
    
      $product = array(
          "id" => $this->id_cart,
          "barcode" => $this->barcode,
          "name" => $this->name,
          "product_id" => $this->product_id,
          "referencia_variacion" => $this->referencia_variacion,
          "price" => $this->price,
          "iva" => $this->iva_agregar,
          "cost" => $this->cost,
          "qty" => $this->cantidad,
          "orderby_id"=> $this->orderby_id,
      );
      

      $cart->addProduct($product);

      $this->monto_total = $cart->totalAmount();
      $this->subtotal = $cart->subtotalAmount();
      $this->iva_total = $cart->totalIva();
      
      
   $this->deuda = $this->monto_total - $this->pago;

      $this->resetUI();



      $this->emit('product-added','Producto agregado');

  }

}

    public function removeProductFromCart($product) {
        $cart = new CartPedidosSucursales;
        $cart->removeProduct($product);
        session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
        return back();
    }


  public function Incrementar(Product $product) {
      $cart = new CartPedidosSucursales;
      $items = $cart->getContent();
      


      foreach ($items as $i)
   {
          if($i['id'] === $product['id']) {

            $cart->removeProduct($product['id']);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "referencia_variacion" => $i['referencia_variacion'],
                "product_id" => $i['product_id'],
                "iva" => $i['iva'],
                "cost" => $i['cost'],
                "qty" => $i['qty']+1,
                "orderby_id"=> $i['orderby_id'],
                
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


  public function UpdateIva($product, $iva) {
      $cart = new CartPedidosSucursales;
      $items = $cart->getContent();


      foreach ($items as $i)
   {
          if($i['id'] === $product) {

            $cart->removeProduct($product);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "referencia_variacion" => $i['referencia_variacion'],
                "product_id" => $i['product_id'],
                "iva" => $iva,
                "cost" => $i['cost'],
                "qty" => $i['qty'],
                "orderby_id"=> $i['orderby_id'],
            );

            $cart->addProduct($product);

        }


   }

   $this->monto_total = $cart->totalAmount();
   $this->subtotal = $cart->subtotalAmount();
   $this->iva_total = $cart->totalIva();

   $this->deuda = $this->monto_total - $this->pago;

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }


  public function UpdatePrice($product, $price) {

      $cart = new CartPedidosSucursales;
      $items = $cart->getContent();

      foreach ($items as $i)
   {
          if($i['id'] === $product) {

            $cart->removeProduct($product);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "referencia_variacion" => $i['referencia_variacion'],
                "product_id" => $i['product_id'],
                "iva" => $i['iva'],
                "cost" => $price,
                "qty" => $i['qty'],
                "orderby_id"=> $i['orderby_id'],
            );

            $cart->addProduct($product);

        }


   }

   $this->monto_total = $cart->totalAmount();
   $this->subtotal = $cart->subtotalAmount();
   $this->iva_total = $cart->totalIva();

   $this->deuda = $this->monto_total - $this->pago;

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }





public function UpdateIvaGral(Product $product) {

  if($this->iva_general != "Elegir") {

  session(['IvaGral' => $this->iva_general]);

    $cart = new CartPedidosSucursales;
    $items = $cart->getContent();


    foreach ($items as $i)
 {
          $cart->removeProduct($i['id']);

          $product = array(
              "id" => $i['id'],
              "name" => $i['name'],
              "barcode" => $i['barcode'],
              "product_id" => $i['product_id'],
              "referencia_variacion" => $i['referencia_variacion'],
              "iva" => $this->iva_general,
              "cost" => $i['cost'],
              "qty" => $i['qty'],
              "orderby_id"=> $i['orderby_id'],
          );

          $cart->addProduct($product);

 }

 $this->monto_total = $cart->totalAmount();
 $this->subtotal = $cart->subtotalAmount();
 $this->iva_total = $cart->totalIva();

 $this->deuda = $this->monto_total - $this->pago;

    session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
    return back();


  }


}

  public function updateQty($product, $qty) {

      $cart = new CartPedidosSucursales;
      $items = $cart->getContent();

      foreach ($items as $i)
   {
          if($i['id'] === $product) {

            $cart->removeProduct($product);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "referencia_variacion" => $i['referencia_variacion'],
                "product_id" => $i['product_id'],
                "iva" => $i['iva'],
                "cost" => $i['cost'],
                "qty" => $qty,
                "orderby_id"=> $i['orderby_id'],
            );

            $cart->addProduct($product);

        }


   }

   $this->monto_total = $cart->totalAmount();
   $this->subtotal = $cart->subtotalAmount();
   $this->iva_total = $cart->totalIva();

   $this->deuda = $this->monto_total - $this->pago;

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }



  public function Decrecer(Product $product) {
      $cart = new CartPedidosSucursales;
      $items = $cart->getContent();


      foreach ($items as $i)
   {
          if($i['id'] === $product['id']) {

            $cart->removeProduct($product['id']);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "referencia_variacion" => $i['referencia_variacion'],
                "product_id" => $i['product_id'],
                "cost" => $i['cost'],
                "iva" => $i['iva'],
                "qty" => $i['qty']-1,
                "orderby_id"=> $i['orderby_id'],
            );

            $cart->addProduct($product);

        }
   }

   $this->monto_total = $cart->totalAmount();
   $this->subtotal = $cart->subtotalAmount();
   $this->iva_total = $cart->totalIva();

   $this->deuda = $this->monto_total - $this->pago;

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }

  // escuchar eventos
  protected $listeners = [
    'scan-code'  =>  'BuscarCode',
    	'clearCart'  => 'clearCart',
  ];


  public function BuscarCode($barcode)
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;



  $this->tipo_usuario = User::find(Auth::user()->id);

  if($this->tipo_usuario->sucursal != 1) {
    $this->casa_central_id = $comercio_id;
  } else {

    $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
    $this->casa_central_id = $this->casa_central->casa_central_id;
  }
  
   ////// LOS CODIGOS DE LAS VARIACIONES TIENEN EL CODIGO DEL PRODUCTO SEGUIDO DE "/" Y EL CODIGO DE LA VARIACION ////
        
        
    if (strpos($barcode, '/') !== false) {
            
        $this->product = explode('/',$barcode);
        $product_id = 	$this->product[0];
        $ref_variacion = 	$this->product[1];
        
        // 
        $pvd = productos_variaciones_datos::join('products as P','P.id','productos_variaciones_datos.product_id')
        ->where('P.barcode',$product_id)
        ->where('productos_variaciones_datos.codigo_variacion', $ref_variacion)
        ->where('productos_variaciones_datos.eliminado',0)
        ->OrderBy('productos_variaciones_datos.created_at','desc')
        ->first();
        //
        
        $cant = 1;
        
        $barcode = $product_id."|-|".$pvd->referencia_variacion;
        
       // dd($barcode);
        
        $this->BuscarCodeVariacion($barcode);
        
        return;
    }



    $record = Product::where('barcode',$barcode)->where('comercio_id', $this->casa_central_id)->where('eliminado',0)->first();

    if($record == null || empty($record))
    {

    $this->emit('scan-notfound','El producto no está registrado');

    $this->codigo = '';

    }  else {


  ////////// SI ES VARIACION //////////////////////


  if($record->producto_tipo == "v") {

  $this->productos_variaciones_datos =  productos_variaciones_datos::where('product_id',$record->id)->where('comercio_id', $this->casa_central_id)->where('eliminado',0)->get();

  $this->atributos = productos_variaciones::join('variaciones','variaciones.id','productos_variaciones.variacion_id')
  ->select('variaciones.nombre','variaciones.id as atributo_id', 'productos_variaciones.referencia_id')
  ->where('productos_variaciones.producto_id', $record->id)
  ->get();

  $this->product_id = $record->id;
  $this->barcode = $record->barcode;

  $this->variaciones = variaciones::where('variaciones.comercio_id', $this->casa_central_id)->get();

  $this->emit('variacion-elegir', $record->id);

  return $this->barcode;
  }

    $this->product_id = $record->id;
    $this->id_cart = $record->id."-0";
    $this->name = $record->name;
    $this->barcode = $record->barcode;
    $this->referencia_variacion = 0;
    $this->cost = $record->cost;
    $this->price = $record->price;
    $this->stock = $record->stock;
    $this->categoryid = $record->categorias_fabrica_id;
    $this->image = null;

    $this->emit('show-modal','Show modal!');

    $this->codigo = '';
  }

}


public function BuscarCodeVariacion($barcode)
{

$this->product = explode('|-|',$barcode);

$barcode = 	$this->product[0];
$variacion = 	$this->product[1];

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $this->tipo_usuario = User::find(Auth::user()->id);

  if($this->tipo_usuario->sucursal != 1) {
    $this->casa_central_id = $comercio_id;
  } else {

    $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
    $this->casa_central_id = $this->casa_central->casa_central_id;
  }

  $record = Product::where('barcode',$barcode)->where('comercio_id', $this->casa_central_id)->where('eliminado',0)->first();

  $cost = productos_variaciones_datos::join('products','products.id','productos_variaciones_datos.product_id')
  ->select('productos_variaciones_datos.cost')
  ->where('productos_variaciones_datos.product_id',$record->id)
  ->where('productos_variaciones_datos.referencia_variacion',$variacion)
  ->where('productos_variaciones_datos.comercio_id', $this->casa_central_id)
  ->where('products.eliminado',0)
  ->first();

  $this->product_id = $record->id;
  $this->referencia_variacion = $variacion;
  $this->name = $record->name;
  $this->barcode = $record->barcode;
  $this->cost = $cost->cost;
  $this->price = $record->price;
  $this->stock = $record->stock;
  $this->categoryid = $record->categorias_fabrica_id;
  $this->image = null;

  $this->id_cart = $record->id.'-'.$variacion;


  $productos_variaciones_datos = productos_variaciones::join('variaciones','variaciones.id','productos_variaciones.variacion_id')
  ->select('variaciones.nombre')
  ->where('productos_variaciones.referencia_id',$variacion)
  ->get();

  $pvd = [];

  foreach ($productos_variaciones_datos as $pv) {

        array_push($pvd, $pv->nombre);

          }

  $var = implode(" ",$pvd);

  $this->name = $record->name." - ".$var;

  $this->emit('variacion-elegir-hide','Show modal!');
  $this->emit('show-modal','Show modal!');

  $this->codigo = '';
}



  public function AbrirModal($id)
	{
		$record = Product::find($id);

		$this->product_id = $record->id;
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

  // reset values inputs
  public function resetUI()
  {
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
    $this->product_id = 0;
    
    $this->proveedor_id = null;

    $this->pageTitle = 'Listado';
    $this->cantidad = 1;
    $this->pago = 0;
    $this->iva_general = 0;
    $this->componentName = 'Productos';
    $this->metodo_pago_elegido = 'Elegir';
    $this->categoryid = 'Elegir';
    
   

  }

  // guardar venta
  public function saveSale()
  {

    $cart = new CartPedidosSucursales;

    $this->iva_total = $cart->totalIva();

    if($this->iva_total) {

      if($this->tipo_factura == "Elegir")
      {
        $this->emit('sale-error','DEBE ELEGIR EL TIPO DE FACTURA');
        return;
      }

      if($this->numero_factura == "")
      {
        $this->emit('sale-error','DEBE INCLUIR EL NUMERO DE FACTURA');
        return;
      }

    }

    if($this->metodo_pago_elegido == "Elegir")
    {
      $this->emit('sale-error','DEBE ELEGIR LA FORMA DE PAGO');
      return;
    }

    if($this->proveedor_id == null)
    {
      $this->emit('sale-error','DEBE ELEGIR EL PROVEEDOR');
      return;
    }

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $this->tipo_usuario = User::find($comercio_id);
	    
	if($this->tipo_usuario->sucursal != 1) {
	$this->casa_central_id = $comercio_id;
	} else {
	$this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
	$this->casa_central_id = $this->casa_central->casa_central_id;
	}

    $this->caja = cajas::where('estado',0)->where('comercio_id',$comercio_id)->max('id');

    $cart = new CartPedidosSucursales;

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;


    DB::beginTransaction();

    try {

      $this->monto_total = $cart->totalAmount();
      $this->total = $cart->subtotalAmount();
      $this->iva_total = $cart->totalIva();
      $this->deuda = $this->monto_total - $this->pago;


            $sale = compras_proveedores::create([
              'subtotal' => $cart->subtotalAmount(),
              'iva' => $cart->totalIva(),
              'total' => $cart->totalAmount(),
              'items' => $cart->totalCantidad(),
              'deuda' => $this->deuda,
              'observaciones' => $this->observaciones,
              'tipo_factura' => $this->tipo_factura,
              'numero_factura' => $this->numero_factura,
              'proveedor_id' => $this->proveedor_id,
              'comercio_id' => $comercio_id,
            ]);

            $pagos = pagos_facturas::create([
              'monto_compra' => $this->pago,
              'id_compra' => $sale->id,
              'comercio_id' => $comercio_id,
              'caja' => $this->caja,
              'metodo_pago'  => $this->metodo_pago_elegido,
              'proveedor_id' => $this->proveedor_id,
              'tipo_pago' => 1,
              'eliminado' => 0
            ]);

      if($sale)
      {
          $items = $cart->getContent();

        foreach ($items as  $item) {

          $item_product = explode("-",$item['id']);

          $product_id = $item_product[0];
          $referencia_variacion = $item_product[1];

          $this->tipo_usuario = User::find(Auth::user()->id);

          if($referencia_variacion != 0) {

            if($this->tipo_usuario->sucursal != 1) {
              $referencia_variacion = $referencia_variacion."-".$comercio_id;
            } else {

              $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
              $referencia_variacion = $referencia_variacion."-".$this->casa_central->casa_central_id;

            }

        } else {
            $referencia_variacion = $referencia_variacion;
        }

          detalle_compra_proveedores::create([
            'producto_id' => $product_id,
            'referencia_variacion' => $referencia_variacion,
            'precio' => $item['cost'],
            'nombre' => $item['name'],
            'barcode' => $item['barcode'],
            'cantidad' => $item['qty'],
            'iva' => $item['iva']*$item['cost'],
            'alicuota_iva' => $item['iva'],
            'compra_id' => $sale->id,
            'comercio_id' => $comercio_id
          ]);

          //update stock

          if($this->tipo_usuario->sucursal != 1) {

            // ES CASA CENTRAL

            $product = productos_stock_sucursales::join('products','products.id','productos_stock_sucursales.product_id')
            ->select('productos_stock_sucursales.*')
            ->where('productos_stock_sucursales.product_id', $product_id)
            ->where('productos_stock_sucursales.referencia_variacion', $referencia_variacion)
            ->where('productos_stock_sucursales.comercio_id', $comercio_id)
            ->where('products.eliminado', 0)
            ->first();

          } else {

            // ES SUCURSAL

            $product = productos_stock_sucursales::join('products','products.id','productos_stock_sucursales.product_id')
            ->select('productos_stock_sucursales.*')
            ->where('productos_stock_sucursales.product_id', $product_id)
            ->where('productos_stock_sucursales.referencia_variacion', $referencia_variacion)
            ->where('productos_stock_sucursales.sucursal_id', $comercio_id)
            ->where('products.eliminado', 0)
            ->first();
            
 

            /////////////
          }
          
         if($this->casa_central_id == $comercio_id) { $this->sucursal_id = 0;} else {$this->sucursal_id = $comercio_id; }
         
         if($product === null) {
        
        $stock = $item['qty'];
        
         productos_stock_sucursales::create([
         'stock' => $item['qty'],
         'sucursal_id' => $this->sucursal_id,
         'referencia_variacion' => $referencia_variacion,
         'comercio_id' => $this->casa_central_id,
         'product_id' => $product_id,
         ]);
         
         
	
        //  dd($product_id , $referencia_variacion, $comercio_id);
        } else {
        
          $stock = $product->stock + $item['qty'];
          $product->stock = $product->stock + $item['qty'];
          $product->save();
          
        }

          $historico_stock = historico_stock::create([
            'tipo_movimiento' => 9,
            'producto_id' => $product_id,
            'referencia_variacion' => $referencia_variacion,
            'cantidad_movimiento' => $item['qty'],
            'stock' => $stock,
            'comercio_id'  => $comercio_id,
            'usuario_id'  => Auth::user()->id
          ]);


        }

      }


      DB::commit();

      $this->pago = 0;
      $this->deuda = 0;
      $this->observaciones = '';
      $this->numero_factura = '';
      $this->tipo_factura = 'Elegir';
      session(['IvaGral' => 'Elegir']);
      $this->monto = 0;
      $this->metodo_pago_elegido = 'Elegir';
      $this->proveedor_id = 'Elegir';

      $cart->clear();
      $this->emit('sale-ok','Compra registrada con éxito');



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
   //Reseteo id order by
  $this->orderby_id =  0;

  $cart = new CartPedidosSucursales;
  $cart->clear();
 }

  public function selectProduct()
  {
      $this->query_product = '';

      $this->resetProduct();

  }


  public function updatedQueryProduct()
  {
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;


      $this->tipo_usuario = User::find(Auth::user()->id);

      if($this->tipo_usuario->sucursal != 1) {
        $this->casa_central_id = $comercio_id;
      } else {

        $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
        $this->casa_central_id = $this->casa_central->casa_central_id;
      }


      $this->products_s = 	Product::where('comercio_id', 'like', $this->casa_central_id)->where('eliminado',0)->where( function($query) {
            $query->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
        })
          ->limit(5)
          ->get()
          ->toArray();



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
