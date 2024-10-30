<?php

namespace App\Http\Livewire;

use App\Services\Cart;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Session;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\configuracion_compras;
use App\Models\proveedores;
use App\Models\Category;
use App\Models\User;
use App\Models\sucursales;
use App\Models\metodo_pago;
use App\Models\cajas;
use App\Models\productos_stock_sucursales;
use App\Models\productos_lista_precios;
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

use App\Models\lista_precios_reglas; // 29-8-2024 -- Actualizacion de precios

use DB;

// Trait
use App\Traits\BancosTrait;

use App\Traits\WocommerceTrait;
use App\Traits\ProveedoresTrait;

class ComprasController extends Component
{
  use WithPagination;
  use WithFileUploads;
  use WocommerceTrait;
  use ProveedoresTrait;
  use BancosTrait;

  public $name,$lista_cajas_dia,$barcode,$descuento_gral_mostrar,$sucursal_id,$cost,$price,$fecha_ap,$query,$ultimas_cajas,$pago,$id_cart,$actualizar_costo, $proveedor_id, $caja, $stock,$alerts,$categoryid,$monto_inicial, $caja_abierta, $codigo, $monto_total, $search, $image, $product_id, $pageTitle,$componentName,$comercio_id,$data_Cart, $observaciones, $query_product, $products_s , $Cart, $deuda,$metodo_pago_elegido, $product,$total, $iva, $iva_general, $itemsQuantity, $cantidad,  $carrito, $qty, $tipo_factura, $numero_factura, $orderby_id;
  private $pagination = 25;
  
 public $productos_variaciones_datos = [];


  // 29-8-2024 -- Actualizacion de precios
  public $lista_precios_reglas,$regla_precio_base, $porcentaje_precio_base,$regla_precio_interno,$porcentaje_precio_interno;
  
  public $descuento_costo , $costo_original;
    
    
  public function mount()
  {
    
    // 12-1-2024
    $this->actualizar_precio_interno = true;
    $this->actualizar_costo = true;
    //
    
    // 21-6-2024
    $this->actualizar_precio_base = true;
    
    $this->lista_cajas_dia = [];
    $this->pageTitle = 'Listado';
    $this->cantidad = 1;
    $this->pago = 0;
    $this->componentName = 'Productos';
    $this->metodo_pago_elegido = 'Elegir';
    $this->categoryid = 'Elegir';
    $this->tipo_factura = 'Elegir';
    $this->iva_general = session('IvaGral');
    $this->descuento_general = session('DescuentoGral');
    $this->descuento_gral_mostrar = $this->descuento_general*100;
    $this->proveedor_id = session('IdProveedor');
    
    $proveedor = proveedores::find($this->proveedor_id);
    if($proveedor != null) {
    $this->query = $proveedor->nombre;   
    $this->query_id = $proveedor->id;
    $this->proveedor_id = $proveedor->id;
    } else {
    $this->query = '';        
    $this->query_id = null;
    $this->proveedor_id = null;
    }
    
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
    
    // Si toma los precios internos para hacer la compra, el proveedor es el id 2, que es casa central
    if($this->casa_central->precio_interno == 1) {
    $this->proveedor_id = 2;       
    }
    
   }

    
    $this->sucursal_elegida = $comercio_id;
    
    $this->GetConfiguracionCompras($this->casa_central_id);
    
    // 29-8-2024 -- Actualizacion de precios
    $this->lista_precios_reglas = $this->GetReglaListaPrecios($this->casa_central_id);
    
  }

  public function render()
  {
   
   /* 
    $this->proveedor_id = session('IdProveedor');
    
    $proveedor = proveedores::find($this->proveedor_id);
    if($proveedor != null) {
    $this->query = $proveedor->nombre;   
    $this->query_id = $proveedor->id;
    $this->proveedor_id = $proveedor->id;
    } else {
    $this->query = '';        
    $this->query_id = null;
    $this->proveedor_id = null;
    }
    */
    
    
    $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
    ->where('casa_central_id',$this->casa_central_id)
    ->select('users.name as nombre','sucursales.*')->get();
    
    $this->iva_general = session('IvaGral');
    $this->descuento_general = session('DescuentoGral');
    $this->metodo_pago_elegido = $this->metodo_pago_elegido;
    $this->pago = $this->pago;

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    
    $this->comercio_id = $comercio_id;
    
    // CAJAS 
    $this->ultimas_cajas = cajas::where('comercio_id', $comercio_id)->where('eliminado',0)->orderby('created_at','desc')->limit(5)->get();
 
    $this->caja_abierta = cajas::where('estado',0)->where('comercio_id',$comercio_id)->max('id');
    
    $cart = new Cart;
    $this->monto_total = $cart->totalAmount();
    $this->subtotal = $cart->subtotalAmount();
    $this->iva_total = $cart->totalIva();

  
    


      $products = Product::join('categories as c','c.id','products.category_id')
			->join('seccionalmacens as a','a.id','products.seccionalmacen_id')
			->join('proveedores as pr','pr.id','products.proveedor_id')
			->select('products.*','c.name as category','a.nombre as almacen','pr.nombre as nombre_proveedor')
			->where('products.comercio_id', 'like', $this->casa_central_id)
			->where('products.eliminado', 'like', 0)
			->orderBy('products.name','asc')
			->paginate($this->pagination);

      //$metodo_pagos = bancos::where('bancos.comercio_id', 'like', $this->casa_central_id)->get();

      $metodo_pagos = $this->GetBancosTrait($comercio_id);
      
      $proveedores = proveedores::where('proveedores.comercio_id', 'like', $this->casa_central_id)->where('eliminado',0)->get();

    return view('livewire.compras_proveedores.component',[
      'data' => $products,
      'proveedores' => $proveedores,
      'metodo_pago' => $metodo_pagos,
      'categorias_fabrica' => Category::orderBy('name','asc')->get()
    ])
    ->extends('layouts.theme-pos.app')
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
	
	$this->cost = floatval($this->cost);
	
	// 12-1-2024
	$this->ActualizarCosto();
	
	$this->ActualizarPrecioInterno();
	
	$this->ActualizarPrecioBase();
	
	$this->ActualizarListasPorRegla($this->product_id,$this->referencia_variacion,$this->cost);

	// actualizar precio interno
	
	//Aumentar id order by
     $this->orderby_id =  $this->orderby_id + 1;


    $this->iva_general = session('IvaGral');
    $this->descuento_general = session('DescuentoGral');
    
    $this->descuento_general = $this->descuento_general ?? 0;
    
    if($this->iva_general != "Elegir") {
      $this->iva_agregar = $this->iva_general;
    } else {
      $this->iva_agregar = 0;
    }

    $cart = new Cart;
    $items = $cart->getContent();


 if ($items->contains('codigo_compuesto', $this->codigo_compuesto)) {

   $cart = new Cart;
   $items = $cart->getContent();
   
   $maxIndex = 0;

    foreach ($items as $i) {
        if ($i['codigo_compuesto'] === $this->codigo_compuesto) {
            $maxIndex = max($maxIndex, $i['index']);
            // Resto del código...
        }
    }
    
    $index = $maxIndex + 1;

      $product = array(
          "id" => $this->id_cart."-".$index,
          "index" => $index,
          "codigo_compuesto" => $this->codigo_compuesto,
          "barcode" => $this->barcode,
          "name" => $this->name,
          "product_id" => $this->product_id,
          "referencia_variacion" => $this->referencia_variacion,
          "price" => $this->price,
          "descuento" => $this->descuento_general,
          "iva" => $this->iva_agregar,
          "cost" => $this->cost,
          "qty" => $this->cantidad,
          "orderby_id"=> $this->orderby_id,
      );
    
    $cart->addProduct($product);

    $this->resetUIModal();

    $this->emit('product-added','Producto agregado');

    $this->monto_total = $cart->totalAmount();
    $this->subtotal = $cart->subtotalAmount();
    $this->iva_total = $cart->totalIva();
    $this->descuento_total = $cart->totalDescuento();
    
    
   $this->deuda = $this->monto_total - $this->pago;

   return back();

} else {

      $cart = new Cart;

    $this->cost = floatval($this->cost);
    
      $index = 1;
      
      $product = array(
          "id" => $this->id_cart."-".$index,
          "index" => $index,
          "codigo_compuesto" => $this->codigo_compuesto,
          "barcode" => $this->barcode,
          "name" => $this->name,
          "product_id" => $this->product_id,
          "referencia_variacion" => $this->referencia_variacion,
          "price" => $this->price,
          "descuento" => $this->descuento_general,
          "iva" => $this->iva_agregar,
          "cost" => $this->cost,
          "qty" => $this->cantidad,
          "orderby_id"=> $this->orderby_id,
      );
      

      $cart->addProduct($product);
        
      $this->descuento_total = $cart->totalDescuento();
      $this->monto_total = $cart->totalAmount();
      $this->subtotal = $cart->subtotalAmount();
      $this->iva_total = $cart->totalIva();
      
      
   $this->deuda = $this->monto_total - $this->pago;

      $this->resetUIModal();



      $this->emit('product-added','Producto agregado');

  }

}


  public function Incrementar(Product $product) {
      $cart = new Cart;
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
                "descuento" => $i['descuento'],
                "qty" => $i['qty']+1,
                "orderby_id"=> $i['orderby_id'],
                
            );

            $cart->addProduct($product);

        }
   }


         $this->monto_total = $cart->totalAmount();
         $this->subtotal = $cart->subtotalAmount();
         $this->iva_total = $cart->totalIva();
         $this->descuento_total = $cart->totalDescuento();

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }


  public function UpdateIva($product, $iva) {
      $cart = new Cart;
      $items = $cart->getContent();


      foreach ($items as $i)
   {
          if($i['id'] === $product) {

            $cart->removeProduct($product);

            $product = array(
                "id" => $i['id'],
                "codigo_compuesto" => $i['codigo_compuesto'],
                "index" => $i['index'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "referencia_variacion" => $i['referencia_variacion'],
                "product_id" => $i['product_id'],
                "iva" => $iva,
                "cost" => $i['cost'],
                "descuento" => $i['descuento'],
                "qty" => $i['qty'],
                "orderby_id"=> $i['orderby_id'],
            );

            $cart->addProduct($product);

        }


   }

   $this->monto_total = $cart->totalAmount();
   $this->subtotal = $cart->subtotalAmount();
   $this->iva_total = $cart->totalIva();
   $this->descuento_total = $cart->totalDescuento();

   $this->deuda = $this->monto_total - $this->pago;

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }


  public function UpdatePrice($product, $price) {

      $cart = new Cart;
      $items = $cart->getContent();

      foreach ($items as $i)
   {
          if($i['id'] === $product) {

            $cart->removeProduct($product);

            $product = array(
                "id" => $i['id'],
                "codigo_compuesto" => $i['codigo_compuesto'],
                "index" => $i['index'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "referencia_variacion" => $i['referencia_variacion'],
                "product_id" => $i['product_id'],
                "iva" => $i['iva'],
                "descuento" => $i['descuento'],
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
   $this->descuento_total = $cart->totalDescuento();

   $this->deuda = $this->monto_total - $this->pago;

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }





public function UpdateIvaGral(Product $product) {

  if($this->iva_general != "Elegir") {

  session(['IvaGral' => $this->iva_general]);

    $cart = new Cart;
    $items = $cart->getContent();


    foreach ($items as $i)
 {
          $cart->removeProduct($i['id']);

          $product = array(
              "id" => $i['id'],
              "name" => $i['name'],
              "codigo_compuesto" => $i['codigo_compuesto'],
              "index" => $i['index'],
              "barcode" => $i['barcode'],
              "product_id" => $i['product_id'],
              "referencia_variacion" => $i['referencia_variacion'],
              "iva" => $this->iva_general,
              "descuento" => $i['descuento'],
              "cost" => $i['cost'],
              "qty" => $i['qty'],
              "orderby_id"=> $i['orderby_id'],
          );

          $cart->addProduct($product);

 }

 $this->monto_total = $cart->totalAmount();
 $this->subtotal = $cart->subtotalAmount();
 $this->iva_total = $cart->totalIva();
 $this->descuento_total = $cart->totalDescuento();

 $this->deuda = $this->monto_total - $this->pago;

    session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
    return back();


  }


}

  public function updateQty($product, $qty) {

      $cart = new Cart;
      $items = $cart->getContent();

      foreach ($items as $i)
   {
          if($i['id'] === $product) {

            $cart->removeProduct($product);

            $product = array(
                "id" => $i['id'],
                "codigo_compuesto" => $i['codigo_compuesto'],
                "index" => $i['index'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "referencia_variacion" => $i['referencia_variacion'],
                "product_id" => $i['product_id'],
                "iva" => $i['iva'],
                "cost" => $i['cost'],
                "descuento" => $i['descuento'],
                "qty" => $qty,
                "orderby_id"=> $i['orderby_id'],
            );

            $cart->addProduct($product);

        }


   }

   $this->monto_total = $cart->totalAmount();
   $this->subtotal = $cart->subtotalAmount();
   $this->iva_total = $cart->totalIva();
   $this->descuento_total = $cart->totalDescuento();

   $this->deuda = $this->monto_total - $this->pago;

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }



  public function Decrecer(Product $product) {
      $cart = new Cart;
      $items = $cart->getContent();


      foreach ($items as $i)
   {
          if($i['id'] === $product['id']) {

            $cart->removeProduct($product['id']);

            $product = array(
                "id" => $i['id'],
                "codigo_compuesto" => $i['codigo_compuesto'],
                "index" => $i['index'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "referencia_variacion" => $i['referencia_variacion'],
                "product_id" => $i['product_id'],
                "cost" => $i['cost'],
                "descuento" => $i['descuento'],
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
   $this->descuento_total = $cart->totalDescuento();

   $this->deuda = $this->monto_total - $this->pago;

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }

  // escuchar eventos
  protected $listeners = [
    'scan-code'  =>  'BuscarCode',
    	'clearCart'  => 'clearCart',
  ];

  public function AgregarCodigoDesdeBuscador($barcode){
      $this->BuscarCode($barcode);
  }
  
  public function BuscarCode($barcode)
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

  $this->tipo_usuario = User::find(Auth::user()->id);

  if($this->tipo_usuario->sucursal != 1) {
    $this->casa_central_id = $comercio_id;
    $precio_interno = 0;
    
  } else {

    $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
    $this->casa_central_id = $this->casa_central->casa_central_id;
    
    if($this->casa_central->precio_interno == 0){
    $precio_interno = 0;
    } else {
    $precio_interno = 1;   
    }
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


    // 1-9-2024
    //$record = Product::where('barcode',$barcode)->where('comercio_id', $this->casa_central_id)->where('eliminado',0)->first();
    
    $record = Product::where('comercio_id', $this->casa_central_id)
            ->where('eliminado', 0)
            ->where(function ($query) use ($barcode) {
                $query->where('barcode', $barcode)
                      ->orWhere('cod_proveedor', $barcode);
            })
    ->first();

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
    $this->codigo_compuesto = $record->id.'-0';
    
    $this->name = $record->name;
    $this->barcode = $record->barcode;
    $this->referencia_variacion = 0;

    if($precio_interno == 0){
    $this->cost = $record->cost;
    } else {
    $this->cost = $record->precio_interno;    
    }
    
    $this->costo_original = $this->cost;
    $descuento_costo = $this->SetDescuentoCosto($record,0);
    $this->descuento_costo = $descuento_costo * 100;
    $this->cost = $this->costo_original * (1 - $descuento_costo);
    
    $precio_base = $this->GetPrecio($this->product_id,0); // 21-6-2024
    //
    $this->precio_base = $precio_base->precio_lista;
    
    // 29-8-2024 -- Actualizacion de precios
    $this->regla_precio_base = $precio_base->regla_precio;
    $this->porcentaje_precio_base = $precio_base->porcentaje_regla_precio;
    $this->regla_precio_interno = $record->regla_precio_interno;
    $this->porcentaje_precio_interno = $record->porcentaje_regla_precio_interno;
    //
    
    $this->precio_interno = $record->precio_interno;    
    $this->price = $record->price;
    
    
    $this->stock = $record->stock;
    $this->categoryid = $record->categorias_fabrica_id;
    $this->image = null;

    $this->emit('show-modal','Show modal!');

    $this->codigo = '';
  }

}

public function SetDescuentoCosto($product,$referencia_variacion){
    if($product->producto_tipo == "s"){
        $descuento_costo = $product->descuento_costo;
    } else {
        $pvd = $this->GetProductosVariacionesDatos($product->id, $referencia_variacion,$this->casa_central_id);
        $descuento_costo = $pvd ? $pvd->descuento_costo : 0;
    }
    return $descuento_costo;
}


public function BuscarCodeVariacion($barcode)
{

// dd($barcode);

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
    $precio_interno = 0;
  } else {

    $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
    $this->casa_central_id = $this->casa_central->casa_central_id;
    
    if($this->casa_central->precio_interno == 0){
    $precio_interno = 0;
    } else {
    $precio_interno = 1;   
    }
    
  }

  $record = Product::where('barcode',$barcode)->where('comercio_id', $this->casa_central_id)->where('eliminado',0)->first();

  // 16-9-2024 -- Actualizacion de precios
  $cost = $this->GetProductosVariacionesDatos($record->id,$variacion,$this->casa_central_id);

    
  $this->product_id = $record->id;
  $this->referencia_variacion = $variacion;
  $this->name = $record->name;
  $this->barcode = $record->barcode;
  

  if($precio_interno == 0){
  $this->cost = $cost->cost;
  } else {
  $this->cost = $cost->precio_interno;    
  }

  $this->costo_original = $this->cost;
  $descuento_costo = $this->SetDescuentoCosto($record,$this->referencia_variacion);
  $this->descuento_costo = $descuento_costo * 100;
  $this->cost = $this->cost * (1 - $descuento_costo);
    
  
  $this->precio_interno = $cost->precio_interno;   
  $precio_base = $this->GetPrecio($this->product_id,$this->referencia_variacion); // 21-6-2024
  $this->precio_base = $precio_base->precio_lista;
    
  // 29-8-2024 -- Actualizacion de precios
  $this->regla_precio_base = $precio_base->regla_precio;
  $this->porcentaje_precio_base = $precio_base->porcentaje_regla_precio;
 
  $this->regla_precio_interno = $cost->regla_precio_interno;
  $this->porcentaje_precio_interno = $cost->porcentaje_regla_precio_interno;
  //

  $this->price = $record->price;
  $this->stock = $record->stock;
  $this->categoryid = $record->categorias_fabrica_id;
  $this->image = null;

  $this->id_cart = $record->id.'-'.$variacion;
  
  $this->codigo_compuesto = $record->id.'-'.$variacion;


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
    //  dd("hola");
    
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
    
    $this->emit('show-modal2','Show modal!');
  }
  
   public function CerrarAgregarNroFactura(){   
    $this->emit('hide-modal2','Show modal!');
  }

  public function Edit2($monto_total)
	{


    $this->monto_total = $monto_total;

		$this->emit('show-modal2','Show modal!');
	}

  public function EliminarMoneda(){
  $this->pago = 0;
  $this->MontoPago();
  }
  public function MontoPago()
  {
    // Verifica si $this->pago es numérico y no está vacío
    if (!is_numeric($this->pago) && $this->pago == '') {
        $this->pago = 0; 
    }
    
    $this->deuda = $this->monto_total - $this->pago;
  }

  public function orders()
  {
      $orders = auth()->user()->processedOrders();
      $suma = 0;
      return view('products.orders', compact('orders', 'suma'));
  }

  public function resetUIModal(){
    $this->name ='';
    $this->cantidad =1;
    $this->barcode ='';
    $this->cost ='';
    $this->price ='';
    $this->stock ='';
    $this->alerts ='';
    $this->product_id = 0;
    $this->cantidad = 1;
    $this->iva_general = 0;

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
    $this->iva_general = 0;
    $this->componentName = 'Productos';
    $this->metodo_pago_elegido = 'Elegir';
    $this->categoryid = 'Elegir';

  }

  // guardar venta
  public function saveSale()
  {

    $cart = new Cart;

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

    if($this->proveedor_id == null || $this->proveedor_id == "Elegir")
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
	
	// Set nro compra 
	$this->nro_compra = $this->SetNroCompra();

   // $this->caja = cajas::where('estado',0)->where('comercio_id',$comercio_id)->max('id');

    
    $cart = new Cart;

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    if($this->iva_general == "Elegir") {
        $this->iva_general = 0;
    }
    
    DB::beginTransaction();

    try {

      $this->monto_total = $cart->totalAmount();
      $this->total = $cart->subtotalAmount();
      $this->iva_total = $cart->totalIva();
      $this->descuento_total = $cart->totalDescuento();
      $this->deuda = $this->monto_total - $this->pago;

        //dd($this->iva_general);

            $sale = compras_proveedores::create([
              'subtotal' => $cart->subtotalAmount(),
              'iva' => $cart->totalIva(),
              'alicuota_iva' => $this->iva_general,
              'total' => $cart->totalAmount(),
              'items' => $cart->totalCantidad(),
              'deuda' => $this->deuda,
              'porcentaje_descuento' => $this->descuento_general,
              'descuento' => $this->descuento_total,
              'observaciones' => $this->observaciones,
              'tipo_factura' => $this->tipo_factura,
              'numero_factura' => $this->numero_factura,
              'proveedor_id' => $this->proveedor_id,
              'comercio_id' => $comercio_id,
              'nro_compra' =>$this->nro_compra
            ]);
            
            
            if($this->metodo_pago_elegido == 1) {
                $mp = 1;
            } else {$mp = 0;}

            $estado_pago = $this->GetPlazoAcreditacionPago($this->metodo_pago_elegido);
            
            $pagos = pagos_facturas::create([
              'estado_pago' => $estado_pago,
              'monto_compra' => $this->pago,
              'id_compra' => $sale->id,
              'comercio_id' => $comercio_id,
              'caja' => $this->caja,
              'banco_id'  => $this->metodo_pago_elegido,
              'metodo_pago'  => $mp,
              'proveedor_id' => $this->proveedor_id,
              'tipo_pago' => 1,
              'eliminado' => 0
            ]);

          // SI TRABAJA CON PRECIOS INTERNOS 
          // Aca tiene que generar una nueva venta para el cliente este 
          
       /*   if($this->casa_central->precio_interno == 1) {
             
             Sale::create([
              'subtotal' => $cart->subtotalAmount(),
              'total' => $cart->totalAmount(),
              'recargo' => 0,
              'descuento' => 0,
              'items' => $cart->totalCantidad(),
              'tipo_comprobante'  => $this->tipo_comprobante,
              'cash' => 0,
              'change' => $this->change,
              'iva' => $this->sum_iva,
              'alicuota_iva' => $this->iva_elegido,
              'metodo_pago'  => $this->metodo_pago_nuevo,
              'comercio_id' => $this->comercio_id,
              'cliente_id' => $this->query_id,
              'user_id' => $this->usuario_activo,
              'observaciones' => $this->observaciones,
              'canal_venta' => $this->canal_venta,
              'estado_pago' => 'Pendiente',
              'caja' => $this->caja,
              'deuda' => $this->deuda,
              'created_at' => $this->created_at,
              'recordatorio' => $this->recordatorio,
              'status' => $this->estado_pedido,
              'nota_interna' => $this->nota_interna
            ]);      
          
              
          }
        */
          ////////////
      if($sale)
      {
          $items = $cart->getContent();

        foreach ($items as  $item) {

        
          $item_product = explode("-",$item['id']);

          $product_id = $item_product[0];
          $referencia_variacion = $item['referencia_variacion'];

          $this->tipo_usuario = User::find(Auth::user()->id);


          detalle_compra_proveedores::create([
            'producto_id' => $product_id,
            'referencia_variacion' => $referencia_variacion,
            'precio' => $item['cost'],
            'nombre' => $item['name'],
            'barcode' => $item['barcode'],
            'descuento' => $item['descuento']*$item['cost']*$item['qty'],
            'porcentaje_descuento' => $this->descuento_general,
            'cantidad' => $item['qty'],
            'iva' => $item['iva']*$item['cost']*$item['qty'],
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
            

          }
          
         if($this->casa_central_id == $comercio_id) { $this->sucursal_id = 0;} else {$this->sucursal_id = $comercio_id; }

          // Actualiza stock en el comercio en cuestion por la compra
          
          $stock_disponible = $product->stock + $item['qty'];
          $stock_real = $product->stock_real + $item['qty'];
          
          $product->update([
              'stock' => $stock_disponible,
              'stock_real' => $stock_real
              ]);
          
          // SI TRABAJA CON PRECIOS INTERNOS 
          // Aca tiene que actualizar el stock en la casa central vendedora y crear la venta 


          ////////////
          
          // Actualizacion de stock en wocommerce
        //  $pvd = productos_variaciones_datos::where('referencia_variacion',$referencia_variacion)->where('product_id',$product_id)->where('eliminado',0)->first();
        //  $pvd->wc_push = 1;
        //  $pvd->save();
          
                    
          // Actualizacion de stock en wocommerce
        //  $prod = Product::where('id',$product_id)->where('eliminado',0)->first();
        //  $prod->wc_push = 1;
        //  $prod->save();
          

          $historico_stock = historico_stock::create([
            'tipo_movimiento' => 9,
            'producto_id' => $product_id,
            'referencia_variacion' => $referencia_variacion,
            'cantidad_movimiento' => $item['qty'],
            'stock' => $stock_real,
            'comercio_id'  => $comercio_id,
            'usuario_id'  => Auth::user()->id
          ]);


        }

      }
        
     
      
      /// AGREGAR LOS QUE SON COMPRAS A LA CASA CENTRAL QUE RESTE EL STOCK EN CASA CENTRAL Y LO SUME EN LA SUCURSAL

      DB::commit();
        
      $this->wooCommerceUpdateStockGlobal($sale->id,2);
       
      $this->pago = 0;
      $this->deuda = 0;
      $this->observaciones = '';
      $this->numero_factura = '';
      $this->tipo_factura = 'Elegir';
      session(['IvaGral' => 'Elegir']);
      session(['DescuentoGral' => 0]);
      
      
      $this->descuento_general = 0;
      $this->descuento_gral_mostrar = 0;
      
      $this->monto = 0;

	 session(['NombreProveedor' => null]);
     session(['IdProveedor' => null]); 
     session(['IdProveedor' => null]); 

      $this->metodo_pago_elegido = 'Elegir';
      
      $this->query = '';        
      $this->query_id = null;
      $this->proveedor_id = null;
    
      //12-1-2024
      //$this->actualizar_costo = false;

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

  $cart = new Cart;
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

     $this->sucursal_id = $comercio_id;

    $ultimo = cajas::where('cajas.comercio_id', 'like', $this->sucursal_id)->where('estado',1)->where('eliminado', 0)->latest('nro_caja')->first();
    
   
    
    if($ultimo != null) {
    $nro = $ultimo->nro_caja + 1;
    }
    else {
    $nro = 1;
    }

    DB::beginTransaction();

    try {

      $cajas = DB::table('cajas')->insertGetId([
        'user_id' => Auth::user()->id,
        'comercio_id' => $this->sucursal_id,
        'nro_caja' => $nro,
        'monto_inicial' => $this->monto_inicial,
        'estado' => '0',
        'fecha_inicio' => Carbon::now(),
    
      ]);


      DB::commit();

    } catch (Exception $e) {
      DB::rollback();
      $this->emit('sale-error', $e->getMessage());

 
    }
    
    $this->caja = $cajas;
    $this->caja_seleccionada = cajas::find($this->caja);
    
    
    $this->emit('abrir-caja-hide','Show modal');

    $this->emit('msg','Caja Abierta');
    
  }




  public function SinCaja()
  {

  $this->caja = null;
  $this->caja_seleccionada =  null;
  $this->emit('msg','Sin caja seleccionada');

  }
  
  public function ElegirCaja($caja_id)
  {

  $this->caja = $caja_id;
  
  $this->caja_seleccionada =  cajas::find($caja_id);


  $this->emit('msg','Caja seleccionada');

  }
  
    public function CambioCaja() {

  	$this->tipo_click = 1;

  	if(Auth::user()->comercio_id != 1)
  	$comercio_id = Auth::user()->comercio_id;
  	else
  	$comercio_id = Auth::user()->id;
  	
  	$this->fecha_pedido = $this->fecha_ap;

  	$this->fecha_pedido_desde = $this->fecha_pedido.' 00:00:00';

  	$this->fecha_pedido_hasta = $this->fecha_pedido.' 23:59:50';

  	$this->emit('modal-estado','details loaded');

  	$this->lista_cajas_dia = cajas::where('comercio_id', $comercio_id)->whereBetween('fecha_inicio',[$this->fecha_pedido_desde, $this->fecha_pedido_hasta])->where('eliminado',0)->get();


  }

  public function ModalAbrirCaja(){
   $this->emit('abrir-caja', '');   
  }
  

// BUSQUEDA DE PROVEEDOR

	    public function resetProveedor()
	    {
	      $this->contacts = [];
	    }

	      public function selectProveedor(proveedores $proveedor)
	      {
	          
	        //  dd($cliente);

	                    $this->query = $proveedor->nombre;
						$this->query_id = $proveedor->id;
                        $this->proveedor_id = $proveedor->id;
                        
						session(['NombreProveedor' => $this->query]);
						session(['IdProveedor' => $this->query_id]);

	          $this->resetProveedor();
	          
	          $this->contacts = [];
	      }

	      public function updatedQuery()
	      {

	          $this->contacts = proveedores::where('eliminado',0)->where('nombre', 'like', '%' . $this->query . '%')
								->where('comercio_id', 'like', $this->casa_central_id)
	              ->get()
	              ->toArray();
	              
	         // dd($this->contacts);
	      }


  public function SetNroCompra(){
      
      $compra = compras_proveedores::where('comercio_id',$this->comercio_id)->orderBy('id','desc')->first();
      
      if($compra != null) {
      if($compra->nro_compra != null) {
      $nro_compra = $compra->nro_compra + 1;    
      } else {
      $nro_compra = 1;    
      }
          
      } else {$nro_compra = 1;}
      
      return $nro_compra;
      
    }
    
    	// actualizar el precio del item en carrito
	public function updateDescuentoGral($descuento)
	{
	  //dd($descuento);
	    
	  if(empty($descuento)) { $descuento = 0; }
      $descuento = str_replace(",",".",$descuento);
      
      $descuento_alicuota = $descuento/100;
      
	  $descuento_gral_mostrar = $descuento;
      $this->descuento_gral_mostrar = $descuento;
	  
	  session(['DescuentoGral' => $descuento_alicuota]);
	
      $cart = new Cart;
      $items = $cart->getContent();
      
      foreach($items as $i) {
      $this->updateDescuento($i['id'], $descuento_alicuota);   
      }
      
      
	}
	
	  public function updateDescuento($product, $descuento) {

      $cart = new Cart;
      $items = $cart->getContent();

      foreach ($items as $i)
   {
          if($i['id'] === $product) {
            
            $cart->removeProduct($product);

            $product = array(
                "id" => $i['id'],
                "codigo_compuesto" => $i['codigo_compuesto'],
                "index" => $i['index'],
                 "name" => $i['name'],
                "barcode" => $i['barcode'],
                "referencia_variacion" => $i['referencia_variacion'],
                "product_id" => $i['product_id'],
                "iva" => $i['iva'],
                "cost" => $i['cost'],
                "qty" => $i['qty'],
                "descuento" => $descuento,
                "orderby_id"=> $i['orderby_id'],
            );

            $cart->addProduct($product);

        }


   }

   $this->monto_total = $cart->totalAmount();
   $this->subtotal = $cart->subtotalAmount();
   $this->iva_total = $cart->totalIva();
   $this->descuento_total = $cart->totalDescuento();

  
   $this->deuda = $this->monto_total - $this->pago;

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }

// 10-12

public function ModalAgregarProveedor(){
    $this->emit("modal-agregar-proveedor","");
}

public function StoreProveedor(){
  $proveedor = $this->StoreProveedorTrait();
  $this->selectProveedor($proveedor);
  //dd($this->proveedor_id);
  $this->emit("modal-agregar-proveedor-hide","Proveedor guardado");
    
}



public function removeProductFromCart($product) {
    $cart = new Cart;
    $cart->removeProduct($product);
    session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
    return back();
}

// 12-1-2024
public function ActualizarCosto(){

	// actualizar costo
	if($this->actualizar_costo == true){
	    
	   $p = Product::find($this->product_id);
	   
	   if($p != null){
    	   if($p->producto_tipo == "s"){
    	   $p->cost = $this->costo_original;
    	   $p->save();
    	   } else {
    	   $pvd = productos_variaciones_datos::where('product_id',$this->product_id)
    	   ->where('referencia_variacion',$this->referencia_variacion)
    	   ->where('comercio_id',$this->comercio_id)
    	   ->where('eliminado',0)->first();
    	   $pvd->cost = $this->costo_original;
    	   $pvd->save();
    	   
    	   }	       
	   }


	}
	
}

// 21-6-2024

public function ActualizarPrecioInterno(){
    	// actualizar costo
	if($this->actualizar_precio_interno == true || ($this->actualizar_costo == true && $this->regla_precio_interno == 2) ){
	    
	   $p = Product::find($this->product_id);
	   
	   $margen_nuevo = ($this->precio_interno/$this->cost) - 1;    

	   if($p->producto_tipo == "s"){
	   $p->precio_interno = $this->precio_interno;
	   if($this->regla_precio_interno == 1){$p->porcentaje_regla_precio_interno = $margen_nuevo;}
	   $p->save();
	   } else {
	   $pvd = productos_variaciones_datos::where('product_id',$this->product_id)
	   ->where('referencia_variacion',$this->referencia_variacion)
	   ->where('comercio_id',$this->comercio_id)
	   ->where('eliminado',0)->first();
	   
	   
	   $pvd->precio_interno = $this->precio_interno;
	   if($this->regla_precio_interno == 1){$pvd->porcentaje_regla_precio_interno = $margen_nuevo;}
	   $pvd->save();
	   
	   }
	   
	}
}

public function ActualizarPrecioBase(){

	// actualizar precio
	if($this->actualizar_precio_base == true || ($this->actualizar_costo == true && $this->regla_precio_base == 2)){
	    
	    
	   $p = Product::find($this->product_id);
	   
	   if($p->producto_tipo == "s"){
	   $precio = $this->GetPrecio($this->product_id,0);
	   $precio->precio_lista = $this->precio_base;
	   $precio->save();
	   } else {
	   $precio = $this->GetPrecio($this->product_id,$this->referencia_variacion);
	   $precio->precio_lista = $this->precio_base;
	   $precio->save();

	   }
	   
	}
	
}

public function CambiarCosto() {

// 29-8-2024 -- Actualizacion de precios

$precio_base = $this->GetPrecio($this->product_id,$this->referencia_variacion); // 21-6-2024

$regla_precio_base = $precio_base->regla_precio;
$porcentaje_precio_base = $precio_base->porcentaje_regla_precio;

$data = $this->GetReglaPrecioInterno($this->product_id,$this->referencia_variacion);

$regla_precio_interno = $data['regla_precio_interno'];
$porcentaje_precio_interno = $data['porcentaje_precio_interno'];

$this->cost = $this->costo_original * (1 - $this->descuento_costo/100);
    
// Precio interno
if($regla_precio_interno == 2){
$this->precio_interno = $this->cost * (1 + $porcentaje_precio_interno);    
}    
// Precio base
if($regla_precio_base == 2){
$this->precio_base = $this->cost * (1 + $porcentaje_precio_base);    
}


    
}


public function toggleActualizarCosto(){
    
    $configuracion_compras = configuracion_compras::where('casa_central_id',$this->casa_central_id)->first();
    $configuracion_compras->actualizar_costo = $this->actualizar_costo;
    $configuracion_compras->save();
    
}

public function toggleActualizarPrecioInterno(){
    
    $configuracion_compras = configuracion_compras::where('casa_central_id',$this->casa_central_id)->first();
    $configuracion_compras->actualizar_precio_interno = $this->actualizar_precio_interno;
    $configuracion_compras->save();
}

// 21-6-2024
public function toggleActualizarPrecioBase(){
    
    $configuracion_compras = configuracion_compras::where('casa_central_id',$this->casa_central_id)->first();
    $configuracion_compras->actualizar_precio_base = $this->actualizar_precio_base;
    $configuracion_compras->save();
}

public function GetConfiguracionCompras($casa_central_id){

   //dd($casa_central_id);
   $configuracion_compras = configuracion_compras::where('casa_central_id',$casa_central_id)->first();
   $user = User::find($casa_central_id);
  
   if($configuracion_compras == null) {
       $configuracion_compras = configuracion_compras::create([
           'costo_igual_precio' => 0,
           'actualizar_costo' => 0,
           'actualizar_precio_interno' => 0,
           'actualizar_precio_base' => 0,
           'casa_central_id' => $casa_central_id
           ]);
    
       $this->costo_igual_precio = $user->costo_igual_precio;
       $this->actualizar_costo = $configuracion_compras->actualizar_costo;
       $this->actualizar_precio_interno = $configuracion_compras->actualizar_precio_interno;
       $this->actualizar_precio_base = $configuracion_compras->actualizar_precio_base; //21-6-2024
       
   } else {
       $this->costo_igual_precio = $user->costo_igual_precio;
       $this->actualizar_costo = $configuracion_compras->actualizar_costo;
       $this->actualizar_precio_interno = $configuracion_compras->actualizar_precio_interno;
       $this->actualizar_precio_base = $configuracion_compras->actualizar_precio_base; //21-6-2024
   }
   
   //dd($this->costo_igual_precio);

}
//



//18-5-2024
public function GetPlazoAcreditacionPago($id){
    return $id == 1 ? 1 : 0;
}


// 21-6-2024

public function GetPrecio($product_id,$referencia_variacion){
    $productos_lista_precios = productos_lista_precios::where('lista_id',0)
    ->where('product_id',$product_id)
    ->where('referencia_variacion',$referencia_variacion)
    ->where('eliminado',0)
    ->first();
    
    return $productos_lista_precios;
}


// 29-8-2024 -- Actualizacion de precios
public function GetReglaListaPrecios($casa_central_id){
return $this->lista_precios_reglas = lista_precios_reglas::where('comercio_id',$casa_central_id)->get();    
}

public function GetReglaListaPrecioByListaId($casa_central_id,$lista_id){
return lista_precios_reglas::where('comercio_id',$casa_central_id)->where('lista_id',$lista_id)->first();    
}
    
public function GetProductosVariacionesDatos($product_id,$variacion,$casa_central_id){
  
  return productos_variaciones_datos::join('products','products.id','productos_variaciones_datos.product_id')
  ->select('productos_variaciones_datos.cost','productos_variaciones_datos.precio_interno','productos_variaciones_datos.regla_precio_interno','productos_variaciones_datos.porcentaje_regla_precio_interno')
  ->where('productos_variaciones_datos.product_id',$product_id)
  ->where('productos_variaciones_datos.referencia_variacion',$variacion)
  ->where('productos_variaciones_datos.comercio_id', $casa_central_id)
  ->where('products.eliminado',0)
  ->first();   
  
}

public function GetReglaPrecioInterno($product_id,$referencia_variacion){

$record = Product::find($product_id);
if($record->producto_tipo == "s"){
$regla_precio_interno = $record->regla_precio_interno;
$porcentaje_precio_interno = $record->porcentaje_regla_precio_interno;    
}
if($record->producto_tipo == "v"){
$record = $this->GetProductosVariacionesDatos($product_id,$referencia_variacion,$this->casa_central_id);
$regla_precio_interno = $record->regla_precio_interno;
$porcentaje_precio_interno = $record->porcentaje_regla_precio_interno;    
}

return ['regla_precio_interno' => $regla_precio_interno, 'porcentaje_precio_interno' => $porcentaje_precio_interno];
}



    public function ActualizarListasPorRegla($product_id,$variacion,$costoNuevo){
        
        $costoNuevo = trim($costoNuevo);
        //$costoNuevo = $this->convertirFormatoMoneda($costoNuevo);
        
        $plp = productos_lista_precios::where('product_id',$product_id)->where('referencia_variacion',$variacion)->where('eliminado',0)->get();
       
        foreach($plp as $p){
        if($p->regla_precio == 2){
            $margen = $p->porcentaje_regla_precio;    
            $precioActual = $costoNuevo * (1 + $margen);
            $this->updatePrecioDB($p->id,$variacion, $precioActual);              
        }
        if($p->regla_precio == 1){
            $precio_lista = $p->precio_lista;    
            if($costoNuevo != 0){
            $margen_nuevo = ($precio_lista/$costoNuevo) - 1;    
            } else {
            $margen_nuevo = 0;    
            }
            $p->porcentaje_regla_precio = $margen_nuevo;    
            $p->save();
        }
        }
        
    }

    public function updatePrecioDB($id,$variacion,$precioActual)
    {
        // Buscar el producto y actualizar su precio
        $producto = productos_lista_precios::find($id);
        
        if ($producto) {
            $producto->precio_lista = $precioActual;
            $product = Product::find($producto->product_id);
            if($product->producto_tipo == "s"){
                $costo = $product->cost;
                $costo = $costo * (1 - $this->descuento_costo);
            } else {
                $pvd = productos_variaciones_datos::where('referencia_variacion',$variacion)->where('product_id',$product->id)->where('eliminado',0)->first();
                $costo = $pvd->cost;
                $costo = $costo * (1 - $this->descuento_costo);
            }
            $margen = ($precioActual / $costo) - 1;
            $producto->porcentaje_regla_precio = $margen;
            $producto->save();
            
        } else {
            // Mensaje de error si el producto no se encuentra
            $this->emit("msg-error","Se ha producido un error");
        }
    }




}
