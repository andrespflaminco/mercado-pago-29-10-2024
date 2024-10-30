<?php

namespace App\Http\Livewire;

use App\Services\CartCompraCentral;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Session;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\proveedores;
use App\Models\Category;
use App\Models\User;
use App\Models\datos_facturacion;
use App\Models\ClientesMostrador;
use App\Models\sucursales;
use App\Models\metodo_pago;
use App\Models\cajas;
use App\Models\productos_stock_sucursales;
use App\Models\productos_lista_precios;
use App\Models\historico_stock;
use App\Models\pagos_facturas;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Carbon\Carbon;
use App\Models\bancos;
use App\Models\productos_variaciones_datos;
use App\Models\atrobutos;
use App\Models\variaciones;
use App\Models\compras_proveedores;
use App\Models\productos_variaciones;
use App\Models\detalle_compra_proveedores;
use DB;

class ComprasCasaCentralController extends Component
{
  use WithPagination;
  use WithFileUploads;

  public $name,$barcode,$cost,$price,$pago,$id_cart, $categoria_search, $proveedor_id, $caja, $stock,$alerts,$categoryid,$monto_inicial, $caja_abierta, $codigo, $monto_total, $search, $image, $product_id, $pageTitle,$componentName,$comercio_id,$data_Cart, $observaciones, $query_product, $products_s , $Cart, $deuda,$metodo_pago_elegido, $product,$total, $iva, $iva_general, $itemsQuantity, $cantidad,  $carrito, $qty, $tipo_factura, $numero_factura, $orderby_id;
  private $pagination = 24;

  public $lista_id;
  public $productos_variaciones_datos = [];

  public function mount()
  {
    $this->pageTitle = 'Listado';
    $this->cantidad = 1;
    $this->pago = 0;
    $this->iva_general = 0;
    $this->componentName = 'Productos';
    $this->metodo_pago_elegido = 1;
    $this->categoryid = 'Elegir';
    $this->tipo_factura = 'Elegir';
    $this->iva_general = session('IvaGral');
    $this->muestra_carrito = 0;
    
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    
    $this->tipo_usuario = User::find(Auth::user()->id);

    if($this->tipo_usuario->sucursal != 1) {
    $this->casa_central_id = $comercio_id;
    $this->sucursal_id = 0;
    } else {
    $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
    $this->casa_central_id = $this->casa_central->casa_central_id;
    $this->sucursal_id = Auth::user()->id;
    
    // Si toma los precios internos para hacer la compra, el proveedor es el id 2, que es casa central
    if($this->casa_central->precio_interno == 1) {
    $this->proveedor_id = 2;       
    }
    
   }
   
   
   	$this->datos_facturacion = datos_facturacion::where('comercio_id', $this->casa_central_id)->where('predeterminado',1)->where('eliminado',0)->first();
    

    
    $tipo_comprobante = $this->SetearTipoFactura();
    //dd($tipo_comprobante);
  //  dd($this->casa_central_id,$this->sucursal_id);


  }

  public function GetProductosListaPrecioInterno(){
    $products = Product::join('categories as c','c.id','products.category_id')
			->join('seccionalmacens as a','a.id','products.seccionalmacen_id')
			->join('proveedores as pr','pr.id','products.proveedor_id')
			->select('products.*','c.name as category','a.nombre as almacen','pr.nombre as nombre_proveedor')
			->where('products.comercio_id', 'like', $this->casa_central_id)
			->where('products.eliminado', 'like', 0);
			
		//	dd($this->categoria_search);
			if($this->categoria_search != 0) {
				$products = $products->where('c.id',$this->categoria_search);    
			}
			
			if(strlen($this->search) > 0) {

			$products = $products->where( function($query) {
					 $query->where('products.name', 'like', '%' . $this->search . '%')
						->orWhere('products.barcode', 'like',$this->search . '%');
					});

			}
			
			$products = $products->orderBy('products.name','asc')
			->paginate($this->pagination);
			
			return $products;
      
  }
  
  public function GetProductosListaPrecioDiferente($lista_id){
    $products = Product::join('productos_lista_precios','productos_lista_precios.product_id','products.id')
            ->join('categories as c','c.id','products.category_id')
			->join('seccionalmacens as a','a.id','products.seccionalmacen_id')
			->join('proveedores as pr','pr.id','products.proveedor_id')
			->select('products.name','products.id','products.barcode','products.producto_tipo',
			'products.producto_tipo','c.name as category','a.nombre as almacen','pr.nombre as nombre_proveedor','productos_lista_precios.precio_lista as precio_interno')
			->where('products.comercio_id', 'like', $this->casa_central_id)
			->where('productos_lista_precios.lista_id', $lista_id)
			->where('products.eliminado', 'like', 0);
			
		//	dd($this->categoria_search);
			if($this->categoria_search != 0) {
				$products = $products->where('c.id',$this->categoria_search);    
			}
			
			if(strlen($this->search) > 0) {

			$products = $products->where( function($query) {
					 $query->where('products.name', 'like', '%' . $this->search . '%')
						->orWhere('products.barcode', 'like',$this->search . '%');
					});

			}
			
			$products = $products->orderBy('products.name','asc')
			->paginate($this->pagination);
			
			return $products;      
  }
  
  public function render()
  {
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    
    $cart = new CartCompraCentral;
    $this->monto_total = $cart->totalAmount();
    $this->subtotal = $cart->subtotalAmount();
    $this->iva_total = $cart->totalIva();

  
    $this->caja_abierta = cajas::where('estado',0)->where('comercio_id',$comercio_id)->max('id');

    //dd($this->search);
    
    $sucursal_id_compra = sucursales::where('sucursal_id',$comercio_id)->first();
    $cliente = ClientesMostrador::where('sucursal_id',$sucursal_id_compra->id)->first();
  	//dd($cliente);
  	$lista_id = $cliente->lista_precio;
  	$this->lista_id = $lista_id;
  	
  	if($lista_id == 1){
  	 $products = $this->GetProductosListaPrecioInterno();
  	} else {
  	 $products = $this->GetProductosListaPrecioDiferente($lista_id);
  	}
			
	

      $metodo_pagos = bancos::where('bancos.comercio_id', 'like', $this->casa_central_id)->get();

      $proveedores = proveedores::where('proveedores.comercio_id', 'like', $this->casa_central_id)->where('eliminado',0)->get();
      
      
    //  $prod = Product::where('products.comercio_id', 'like', $this->casa_central_id)->get();

    return view('livewire.compras_central.component',[
      'prod' => $products,
      'proveedores' => $proveedores,
      'metodo_pago' => $metodo_pagos,
      'categorias' => Category::where('comercio_id',$this->casa_central_id)->where('eliminado',0)->orderBy('name','asc')->get()
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

	  //Aumentar id order by
     $this->orderby_id =  $this->orderby_id + 1;

    // si es mayor al stock no dejar pedir
    if($this->stock < $this->cantidad) {
     $this->emit('sale-error', 'El stock insuficiente');
     return;
    }
  
    // Aca tenemos que ver el IVA con que vende los productos y la relacion precio - IVA.

    
    $costo = floatval($this->cost);

    if($this->relacion_precio_iva == 2) {
     $costo = $costo/(1+$this->iva_agregar);       
    }
    
    $cart = new CartCompraCentral;
    $items = $cart->getContent();

 if ($items->contains('id', $this->id_cart)) {

   $cart = new CartCompraCentral;
   $items = $cart->getContent();
   
   $product = Product::find($this->product_id);

   foreach ($items as $i)
{
  
       if($i['id'] === $this->id_cart) {

         $cart->removeProduct($i['id']);

         $product = array(
             "id" => $i['id'],
            "barcode" => $i['barcode'],
             "name" => $i['name'],
             "referencia_variacion" => $i['referencia_variacion'],
             "product_id" => $i['product_id'],
             "iva" => $i['iva'],
             "cost" => $i['cost'],
             "qty" => $this->cantidad,
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

      $cart = new CartCompraCentral;

   
    
      $product = array(
          "id" => $this->id_cart,
          "barcode" => $this->barcode,
          "name" => $this->name,
          "product_id" => $this->product_id,
          "referencia_variacion" => $this->referencia_variacion,
          "price" => $this->price,
          "iva" => floatval($this->iva_agregar),
          "cost" => $costo,
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
        $cart = new CartCompraCentral;
        $cart->removeProduct($product);
        session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
        return back();
    }


  public function Incrementar(Product $product) {
      $cart = new CartCompraCentral;
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
      $cart = new CartCompraCentral;
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

      $cart = new CartCompraCentral;
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

    $cart = new CartCompraCentral;
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

      $cart = new CartCompraCentral;
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
      $cart = new CartCompraCentral;
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



    $record = Product::where('barcode',$barcode)->where('comercio_id', $this->casa_central_id)->where('eliminado',0)->first();

    if($record->producto_tipo == "s") { $variacion = 0; } else {$variacion = 0; }
    // Verificar que el producto no exista en el carrito 
    
    $cart = new CartCompraCentral;
    $items = $cart->getContent();
    
    $targetId = $record->id."-".$variacion;

    //dd($items);
    // Buscar el elemento con el ID deseado
    foreach ($items as $item) {
        if ($item['id'] === $targetId) {
            // Extraer la cantidad (qty)
            $this->cantidad = $item['qty'];
            break; // Se puede detener el bucle una vez que se encuentra el elemento deseado
        }
    }
    
    
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
  
    $get_stock = $this->GetSTock($record->id,0,$this->casa_central_id);

    $this->product_id = $record->id;
    $this->id_cart = $record->id."-0";
    $this->name = $record->name;
    $this->barcode = $record->barcode;
    $this->referencia_variacion = 0;
    
    $this->cost = $this->GetPrecio($record->id,$variacion,$this->casa_central_id,$this->lista_id,$record);
    
    //$this->cost = $record->precio_interno;    
    $this->price = $record->price;
    $this->stock = $get_stock;
    $this->categoryid = $record->categorias_fabrica_id;
    $this->image = null;

    $this->emit('show-modal','Show modal!');

    $this->codigo = '';
  }

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

  $cost = productos_variaciones_datos::join('products','products.id','productos_variaciones_datos.product_id')
  ->select('productos_variaciones_datos.cost','productos_variaciones_datos.precio_interno')
  ->where('productos_variaciones_datos.product_id',$record->id)
  ->where('productos_variaciones_datos.referencia_variacion',$variacion)
  ->where('productos_variaciones_datos.comercio_id', $this->casa_central_id)
  ->where('products.eliminado',0)
  ->first();
  
  $get_stock = $this->GetSTock($record->id,$variacion,$this->casa_central_id);

  $this->product_id = $record->id;
  $this->referencia_variacion = $variacion;
  $this->name = $record->name;
  $this->barcode = $record->barcode;
  $this->cost = $cost->precio_interno;    
  $this->price = $record->price;
  $this->stock = $get_stock;
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
		$this->cost = $record->precio_interno;
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
    
    $this->emit('hide-modal','');
   

  }

  // guardar venta
  public function saveSale()
  {
    
    // Aca hay que ver si la casa central tiene stock

    $cart = new CartCompraCentral;
    
 //   dd($cart);
    
    $this->iva_total = $cart->totalIva();

    $this->metodo_pago_elegido = 1;


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

    $this->nro_compra = $this->SetNroCompra($comercio_id);
    
    $this->caja = cajas::where('estado',0)->where('comercio_id',$comercio_id)->max('id');

    $cart = new CartCompraCentral;

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $this->SetearTipoFactura();

    DB::beginTransaction();

    try {

      $this->monto_total = $cart->totalAmount();
      $this->total = $cart->subtotalAmount();
      $this->iva_total = $cart->totalIva();
      $this->deuda = $this->monto_total - $this->pago;


            $compra = compras_proveedores::create([
              'nro_compra' => $this->nro_compra,
              'subtotal' => $cart->subtotalAmount(),
              'iva' => $cart->totalIva(),
              'total' => $cart->totalAmount(),
              'items' => $cart->totalCantidad(),
              'deuda' => $this->deuda,
              'observaciones' => $this->observaciones,
              'tipo_factura' => $this->tipo_comprobante,
              'numero_factura' => $this->numero_factura,
              'proveedor_id' => 2,
              'comercio_id' => $comercio_id,
              'status' => 1
            ]);
            
            // dd($compra);

            // Pagos que hace la sucursal 
            
            $pagos = pagos_facturas::create([
              'monto_compra' => $this->pago,
              'id_compra' => $compra->id,
              'comercio_id' => $comercio_id,
              'caja' => $this->caja,
              'metodo_pago'  => $this->metodo_pago_elegido,
              'proveedor_id' => 2,
              'tipo_pago' => 1,
              'eliminado' => 0
            ]);
            
            /* Pago que recibe la casa central
            
            $pagos = pagos_facturas::create([
              'monto_compra' => $this->pago,
              'id_compra' => $sale->id,
              'comercio_id' => $comercio_id,
              'caja' => $this->caja,
              'metodo_pago'  => $this->metodo_pago_elegido,
              'proveedor_id' => 0,
              'tipo_pago' => 1,
              'eliminado' => 0
            ]);

            */
            
          // SI TRABAJA CON PRECIOS INTERNOS 
          // Aca tiene que generar una nueva venta para el cliente este 
          $cliente = User::find(Auth::user()->id);
            
            //dd($cliente);
           
           $this->nro_venta = $this->SetNroVenta();
            
           $sale =  Sale::create([
              'nro_venta' => $this->nro_venta ,
              'subtotal' => floatval($cart->subtotalAmount()),
              'total' => floatval($cart->totalAmount()),
              'recargo' => 0,
              'descuento' => 0,
              'items' => $cart->totalCantidad(),
              'tipo_comprobante'  => $this->tipo_comprobante,
              'cash' => 0,
              'change' => 0,
              'iva' =>  $this->iva_total,
              'alicuota_iva' => 0,
              'relacion_precio_iva' => null,
              'metodo_pago'  => $this->metodo_pago_elegido,
              'comercio_id' => $this->casa_central_id,
              'cliente_id' => $cliente->cliente_id,
              'user_id' => $comercio_id,
              'observaciones' => $this->observaciones,
              'canal_venta' => 'Venta a sucursales',
              'estado_pago' => 'Pendiente',
              'caja' => $this->caja,
              'deuda' => $this->deuda,
              'recordatorio' => '',
              'status' => 'Pendiente',
              'nota_interna' => ''
            ]);      
          
       
      // dd($compra);
       
          ////////////
      if($compra)
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
            'cantidad' => $item['qty'],
            'iva' => $item['iva']*$item['cost'],
            'alicuota_iva' => $item['iva'],
            'compra_id' => $compra->id,
            'comercio_id' => $comercio_id
          ]);
          
           SaleDetail::create([
              'precio_original' => $item['cost'],
              'price' => $item['cost'],
              'recargo' => 0,
              'descuento' => 0,
              'quantity' => $item['qty'],
              'metodo_pago'  => $this->metodo_pago_elegido,
              'product_id' => $product_id,
              'referencia_variacion' => $referencia_variacion,
              'product_name' => $item['name'],
              'iva' => $item['iva'],
              'iva_total' => $item['iva']*$item['cost'],
              'cost' => 0,
              'product_barcode' => $item['barcode'],
              'seccionalmacen_id' => null,
              'comercio_id' => $this->casa_central_id,
              'comentario' => '',
              'estado' => 0,
              'sale_id' => $sale->id,
              'stock_de_sucursal_id' => $this->casa_central_id,
              'caja' => $this->caja,
              'canal_venta' => 'Venta a sucursales',
              'cliente_id' => $cliente->cliente_id
            ]);

          //update stock

            $product_stock = productos_stock_sucursales::where('productos_stock_sucursales.product_id',$product_id)
            ->where('productos_stock_sucursales.referencia_variacion',$referencia_variacion)
            ->where('productos_stock_sucursales.sucursal_id',0)
            ->where('productos_stock_sucursales.comercio_id',$this->casa_central_id)
            ->first();
            
            $stock_c = $product_stock->stock;
            $stock_r = $product_stock->stock_real;
            
            $product_stock->stock = $stock_c - $item['qty'];
            $product_stock->stock_real = $stock_r;
            $product_stock->save();

        //    $historico_stock = historico_stock::create([
        //       'tipo_movimiento' => 1,
        //       'sale_id' => $sale->id,
        //       'producto_id' => $product_id,
        //       'referencia_variacion' => $referencia_variacion,
        //       'cantidad_movimiento' => $item['qty'],
        //       'stock' => $product_stock->stock,
        //       'usuario_id' => $this->casa_central_id,
        //       'comercio_id'  => $this->casa_central_id
        //       ]);
            
            
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


        }

      }
        
    // Actualizar el id de la venta de la casa central en la compra 
    
    $compra->update([
        'sale_casa_central' => $sale->id
        ]);
        
    /// AGREGAR LOS QUE SON COMPRAS A LA CASA CENTRAL QUE RESTE EL STOCK EN CASA CENTRAL Y LO SUME EN LA SUCURSAL

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

  $cart = new CartCompraCentral;
  $cart->clear();
  $this->deuda = 0;
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


public function MuestraCarrito() {
     $this->muestra_carrito = 1;
}

public function OcultaCarrito() {
     $this->muestra_carrito = 0;
}


public function SetearTipoFactura() {

//  dd($this->casa_central_id,$this->sucursal_id);  

$datos_facturacion_central =  datos_facturacion::where('comercio_id', $this->casa_central_id)->where('predeterminado',1)->where('eliminado',0)->first();
$this->SetPrecioIva($datos_facturacion_central);

$datos_facturacion_sucursal = datos_facturacion::where('comercio_id', $this->sucursal_id)->where('predeterminado',1)->where('eliminado',0)->first();

if($datos_facturacion_central != null){
// Si la casa central es responsable inscripto
if($datos_facturacion_central->iva_defecto != 0) {
    
    if($datos_facturacion_sucursal != null) {
    if($datos_facturacion_sucursal->iva_defecto == 0) {
    $this->tipo_comprobante = "B";       
    } else {
    $this->tipo_comprobante = "A";       
   }    
    } else {
     $this->tipo_comprobante = "B";      
    }

} else {
    $this->tipo_comprobante = "C";   
}
} else {
    $this->tipo_comprobante = "CF";
}
return $this->tipo_comprobante;
}

public function SetPrecioIva($datos_facturacion_central) {
    
    $this->datos_facturacion = $datos_facturacion_central;
    
    if($this->datos_facturacion != null) {
	    
	$this->relacion_precio_iva = $this->datos_facturacion->relacion_precio_iva;
	$this->iva_agregar = $this->datos_facturacion->iva_defecto;

	} else {
	$this->relacion_precio_iva = 1;
	$this->iva_agregar = 0;
	}
}

public function GetSTock($product_id,$referencia_variacion,$casa_central_id) {
    
    $product_stock = productos_stock_sucursales::where('productos_stock_sucursales.product_id',$product_id)
    ->where('productos_stock_sucursales.referencia_variacion',$referencia_variacion)
    ->where('productos_stock_sucursales.sucursal_id',0)
    ->where('productos_stock_sucursales.comercio_id',$casa_central_id)
    ->first()->stock;
            
    return $product_stock;
}

public function GetPrecio($product_id,$referencia_variacion,$casa_central_id,$lista_id,$product) {
    
    if($lista_id == 1){
    return $product->precio_interno;    
    } else {
    return productos_lista_precios::where('productos_lista_precios.product_id',$product_id)
    ->where('productos_lista_precios.referencia_variacion',$referencia_variacion)
    ->where('productos_lista_precios.lista_id',$lista_id)
    ->where('productos_lista_precios.comercio_id',$casa_central_id)
    ->first()->precio_lista;        
    }

}


  public function SetNroCompra($comercio_id){
      
      $compra = compras_proveedores::where('comercio_id',$comercio_id)->orderBy('id','desc')->first();
      
    //  dd($compra);
      
      if($compra != null) {
      if($compra->nro_compra != null) {
      $nro_compra = $compra->nro_compra + 1;    
      } else {
      $nro_compra = 1;    
      }
          
      } else {$nro_compra = 1;}
      
      return $nro_compra;
      
    }
    
      public function SetNroVenta(){
          
      $sale = Sale::where('comercio_id',Auth::user()->casa_central_user_id)->orderBy('id','desc')->first();
      
      if($sale != null) {
      if($sale->nro_venta != null) {
      $nro_venta = $sale->nro_venta + 1;    
      } else {
      $nro_venta = 1;    
      }
          
      } else {$nro_venta = 1;}
      
      return $nro_venta;
      
    }


}
