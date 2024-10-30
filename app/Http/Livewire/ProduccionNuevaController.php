<?php

namespace App\Http\Livewire;

use App\Services\CartProduccion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Session;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\proveedores;
use App\Models\produccion_detalles_insumos;
use App\Models\productos_stock_sucursales;
use App\Models\receta;
use App\Models\insumo;
use App\Models\User;
use App\Models\historico_stock_insumo;
use App\Models\produccion;
use App\Models\produccion_detalle;
use App\Models\Category;
use App\Models\metodo_pago;
use App\Models\cajas;
use App\Models\historico_stock;
use App\Models\pagos_facturas;
use App\Models\unidad_medida;
use App\Models\tipo_unidad_medida;
use App\Models\unidad_medida_relacion;
use App\Models\Product;
use App\Models\bancos;
use App\Models\compras_proveedores;
use App\Models\productos_variaciones_datos;
use App\Models\productos_variaciones;
use App\Models\variaciones;
use Notification;
use App\Notifications\NotificarCambios;
use App\Models\detalle_compra_proveedores;
use DB;
use Carbon\Carbon;

// Trait

use App\Traits\ProduccionTrait;

class ProduccionNuevaController extends Component
{
  use WithPagination;
  use WithFileUploads;
  use ProduccionTrait;

  public $name,$barcode,$fecha_produccion,$cost,$price,$pago, $orderby_id, $productos_variacion_datos, $proveedor_id, $caja, $stock,$alerts,$categoryid, $codigo, $monto_total, $search, $image, $selected_id, $pageTitle,$componentName,$comercio_id,$data_Cart, $observaciones, $query_product, $products_s , $Cart, $deuda,$metodo_pago_elegido, $product,$total, $estado, $iva, $iva_general, $itemsQuantity, $cantidad, $carrito, $qty, $tipo_factura, $numero_factura;
  private $pagination = 25;

    public $productos_variaciones_datos = [];

  public function mount()
  {
    $this->pageTitle = 'Listado';
    $this->cantidad = 1;
    $this->pago = 0;
    $this->iva_general = 0;
    $this->componentName = 'Productos';
    $this->estado = 'Elegir';
    $this->categoryid = 'Elegir';
    $this->tipo_factura = 'Elegir';
    $this->referencia_variacion = 1;
    $this->fecha_produccion = Carbon::now();
  }

  public function render()
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $cart = new CartProduccion;
    $this->monto_total = $cart->totalAmount();
    $this->subtotal = $cart->subtotalAmount();


      $products = Product::join('categories as c','c.id','products.category_id')
			->join('seccionalmacens as a','a.id','products.seccionalmacen_id')
			->join('proveedores as pr','pr.id','products.proveedor_id')
			->select('products.*','c.name as category','a.nombre as almacen','pr.nombre as nombre_proveedor')
			->where('products.comercio_id', 'like', $comercio_id)
			->where('products.eliminado', 'like', 0)
			->orderBy('products.name','asc')
			->paginate($this->pagination);

      $metodo_pagos = bancos::where('bancos.comercio_id', 'like', $comercio_id)->get();

      $proveedores = proveedores::where('proveedores.comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();

    return view('livewire.produccion-nueva.component',[
      'data' => $products,
      'proveedores' => $proveedores,
      'metodo_pago' => $metodo_pagos,
      'categorias_fabrica' => Category::orderBy('name','asc')->get()
    ])
    ->extends('layouts.theme-pos.app')
    ->section('content');
  }



  public function Agregar() {
    
    //Aumentar id order by
    $this->orderby_id =  $this->orderby_id + 1;
    
    $this->iva_general = 0;


    $cart = new CartProduccion;
    $items = $cart->getContent();

            ///////////   SI ESTA AGREGADO ////////////////////
 if (($items->contains('product_id', $this->selected_id)) && ($items->contains('referencia_variacion', $this->referencia_variacion))) {


   $cart = new CartProduccion;
   $items = $cart->getContent();

   $product = Product::find($this->selected_id);


   foreach ($items as $i)
{
       if($i['product_id'] === $this->selected_id) {
           
        if($i['referencia_variacion'] === $this->referencia_variacion) {
        
        
        
         $cart->removeProduct($i['id']);

         $product = array(
             "id" => $i['id'],
             "product_id" => $i['product_id'],
            "barcode" => $i['barcode'],
             "name" => $i['name'],
             "referencia_variacion" => $i['referencia_variacion'],
             "cost" => $i['cost'],
             "qty" => $i['qty']+$this->cantidad,
             "orderby_id"=> $i['orderby_id'],
         );

         $cart->addProduct($product);

     }
     
     }
}

    $this->resetUI();

    $this->emit('product-added','Producto agregado');
    
    $this->fecha_produccion = Carbon::now();
    $this->monto_total = $cart->totalAmount();
    $this->subtotal = $cart->subtotalAmount();
    $this->iva_total = 0;


   return back();

} else {

 ///////////   SI NO ESTA AGREGADO ////////////////////
 
      $cart = new CartProduccion;
      
if(($this->referencia_variacion != 0) && ($this->referencia_variacion != 1) ) {

$productos_variaciones_datos = productos_variaciones::join('variaciones','variaciones.id','productos_variaciones.variacion_id')
   ->select('variaciones.nombre')
   ->where('productos_variaciones.referencia_id',$this->referencia_variacion)
   ->get();
                
    $pvd = [];
                
    foreach ($productos_variaciones_datos as $pv) {
                        
          	array_push($pvd, $pv->nombre);
                        
      }
      
      
      $var = implode(" ",$pvd);
      
      $this->name = $this->name." - ".$var;
      $ref = $this->referencia_variacion;
      
      // dd($ref);
      
      //$ref = substr($ref, -4, 4);
      
      $this->id = $this->selected_id.'-'.$ref;
      
      // dd($this->id);
      
      
      
      
} else {
    $this->id = $this->selected_id."-".$this->referencia_variacion;
} 

    if($this->cost == '') {
    $cost = 0;    
    } else {
    $cost = $this->cost;    
    }
    
    

      $product = array(
          "id" => $this->id,
          "product_id" => $this->selected_id,
          "barcode" => $this->barcode,
          "name" => $this->name,
          "referencia_variacion" => $this->referencia_variacion,
          "price" => $this->price,
          "cost" => $cost,
          "qty" => $this->cantidad,
          "orderby_id"=> $this->orderby_id
      );
      
     // dd($product);

      $cart->addProduct($product);

      $this->monto_total = $cart->totalAmount();
      $this->subtotal = $cart->subtotalAmount();
      $this->iva_total = 0;

      $this->resetUI();

        

      $this->emit('product-added','Producto agregado');

  }

}


  public function AgregarVariacion($product_id,$referencia_variacion)
  {
    
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    
    $this->product =  productos_variaciones_datos::where('referencia_variacion',$referencia_variacion)->where('product_id',$product_id)->where('eliminado',0)->first();
    
    $record = Product::where('id',$this->product->product_id)->where('tipo_producto','>',1)->where('eliminado',0)->where('comercio_id', $comercio_id)->first();

    // dd($record);
    
    if($record == null || empty($record))
    {

    $this->emit('mensaje','El producto no está registrado como producto de produccion');

    $this->codigo = '';

    }  else {
    
   $this->receta = Product::leftjoin('recetas as r','r.product_id','products.id')
   ->leftjoin('insumos','insumos.id','r.insumo_id')
   ->join('unidad_medidas','unidad_medidas.id','r.unidad_medida')
   ->select('r.rinde', receta::raw(' SUM(r.cantidad*r.costo_unitario*r.relacion_medida) AS cost'))
   ->where('products.id', $record->id)
   ->where('r.referencia_variacion', $referencia_variacion)
   ->where('products.eliminado', 'like', 0)
   ->groupBy('r.rinde')
   ->first();
   
   
   
   if($this->receta == null) {
       $this->emit('no-stock','La receta no esta registrada');
       $this->cost = 0;
   } else {
    $this->cost = floatval($this->receta->cost/$this->receta->rinde);    
   }
   
    
  //  dd($this->cost);
    $this->selected_id = $record->id;
    $this->name = $record->name;
    $this->barcode = $record->barcode;
    $this->price = $record->price;
    $this->stock = $record->stock;
    $this->categoryid = $record->categorias_fabrica_id;
    $this->image = null;
    $this->referencia_variacion = $referencia_variacion;

    $this->emit('show-modal','Show modal!');
     $this->emit('variaciones-hide','');

    $this->codigo = '';
  }

      
      
  }

    public function removeProductFromCart($product) {
        $cart = new CartProduccion;
        $cart->removeProduct($product);
        session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
        return back();
    }


  public function Incrementar(Product $product) {
      $cart = new CartProduccion;
      $items = $cart->getContent();


      foreach ($items as $i)
   {
          if($i['id'] === $product['id']) {

            $cart->removeProduct($product['id']);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "cost" => $i['cost'],
                "qty" => $i['qty']+1,
                "orderby_id"=> $i['orderby_id']
            );

            $cart->addProduct($product);

        }
   }


         $this->monto_total = $cart->totalAmount();
         $this->subtotal = $cart->subtotalAmount();
         $this->iva_total = 0;

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }



  public function UpdatePrice($product, $price) {
      
      // dd($product);
      
      $cart = new CartProduccion;
      $items = $cart->getContent();

      foreach ($items as $i)
   {
          if($i['id'] === $product) {

            $cart->removeProduct($product);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "cost" => $price,
                "qty" => $i['qty'],
                "orderby_id"=> $i['orderby_id'],
            );

            $cart->addProduct($product);

        }


   }

   $this->monto_total = $cart->totalAmount();
   $this->subtotal = $cart->subtotalAmount();
   $this->iva_total = 0;

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }



  public function updateQty($product, $qty) {
      
      // dd($product);
      
      $cart = new CartProduccion;
      $items = $cart->getContent();

      foreach ($items as $i)
   {
          if($i['id'] === $product) {

            $cart->removeProduct($product);

            $product = array(
                "id" => $i['id'],
                "product_id" => $i['product_id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "referencia_variacion" => $i['referencia_variacion'],
                "cost" => $i['cost'],
                "qty" => $qty,
                "orderby_id"=> $i['orderby_id'],
            );

            $cart->addProduct($product);

        }


   }

   $this->monto_total = $cart->totalAmount();
   $this->subtotal = $cart->subtotalAmount();
   $this->iva_total = 0;

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }



  public function Decrecer(Product $product) {
      $cart = new CartProduccion;
      $items = $cart->getContent();


      foreach ($items as $i)
   {
          if($i['id'] === $product['id']) {

            $cart->removeProduct($product['id']);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "cost" => $i['cost'],
                "qty" => $i['qty']-1,
                "orderby_id"=> $i['orderby_id'],
            );

            $cart->addProduct($product);

        }
   }

   $this->monto_total = $cart->totalAmount();
   $this->subtotal = $cart->subtotalAmount();
   $this->iva_total = 0;

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
    
    $casa_central_id = Auth::user()->casa_central_user_id;
    
    ////// LOS CODIGOS DE LAS VARIACIONES TIENEN EL CODIGO DEL PRODUCTO SEGUIDO DE "/" Y EL CODIGO DE LA VARIACION ////
        
        
    if (strpos($barcode, '/') !== false) {
            
        $this->product = explode('/',$barcode);
        $product_id = 	$this->product[0];
        $ref_variacion = 	$this->product[1];
        
        // 
        $pvd = productos_variaciones_datos::join('products as P','P.id','productos_variaciones_datos.product_id')
        ->where('P.barcode',$product_id)
        ->where('P.tipo_producto','>',1)
        ->where('productos_variaciones_datos.codigo_variacion', $ref_variacion)
        ->where('productos_variaciones_datos.eliminado',0)
        ->OrderBy('productos_variaciones_datos.created_at','desc')
        ->first();
        //
        
        $barcode = $pvd->referencia_variacion;
        
        $cant = 1;
        
        $this->AgregarVariacion($barcode, $cant);
        
        return;
    }

    /////////////////////////////////////////////////////////////////////////////////////
    
    
    $record = Product::where('barcode',$barcode)->where('tipo_producto','>',1)->where('comercio_id', $casa_central_id)->where('eliminado',0)->first();
    
    // dd($record);
    
    

    if($record == null || empty($record))
    {

    $this->emit('mensaje','El producto no está registrado como producto de produccion');
    $this->codigo = '';
    return;
    }  else {
        
        if($record->producto_tipo == "v" && $this->referencia_variacion == "1") {
       
       $this->referencia_variacion = 1;
       
        $this->productos_variaciones_datos =  productos_variaciones_datos::where('product_id',$record->id)
        ->where('comercio_id', $comercio_id)
        ->where('eliminado',0)
        ->get();
        
        $this->atributos = productos_variaciones::join('variaciones','variaciones.id','productos_variaciones.variacion_id')
        ->select('variaciones.nombre','variaciones.id as atributo_id', 'productos_variaciones.referencia_id')
        ->where('productos_variaciones.producto_id', $record->id)
        ->get();
        
        $this->product_id = $record->id;
        $this->selected_id = $this->product_id;
        
        $this->variaciones = variaciones::where('variaciones.comercio_id', $casa_central_id)->get();

         $this->emit('variaciones', $record->id);
        
        return $this->barcode;
    }
        
        if($this->referencia_variacion == "1") {
            $this->referencia_variacion = 0;
        } else {
            $this->referencia_variacion = $this->referencia_variacion;
        }

   $receta = receta::join('products','products.id', 'recetas.product_id')
   ->select('recetas.rinde',receta::raw(' SUM(recetas.cantidad*recetas.costo_unitario*recetas.relacion_medida) AS cost'))
   ->where('recetas.product_id', $record->id)
   ->where('recetas.referencia_variacion', $this->referencia_variacion)
   ->where('products.eliminado', 0)
   ->where('recetas.eliminado',0)
   ->groupBy('recetas.rinde')
   ->first();


    if($receta == null || empty($receta))
    {
    $this->emit('mensaje','El producto no tiene registrada la receta');
    $this->codigo = '';
    return;
    }
    
    $this->cost = floatval($receta->cost/$receta->rinde);
    
    // dd($this->cost);
    
    $this->selected_id = $record->id;
    $this->name = $record->name;
    $this->barcode = $record->barcode;
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
		$record = Product::find($id);

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
    $this->referencia_variacion = 1;
    $this->categoryid = 'Elegir';
    $this->image = null;
    $this->selected_id = 0;

  }
    


  public function resetProduct()
 {
   $this->products_s = [];
 }

 public function clearCart() {
  $cart = new CartProduccion;
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

    $casa_central_user_id = Auth::user()->casa_central_user_id;

      $this->products_s = 	Product::where('comercio_id', 'like', $casa_central_user_id)->where('eliminado',0)->where('tipo_producto','>',1)->where( function($query) {
            $query->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
        })
          ->limit(5)
          ->get()
          ->toArray();



  }


public function saveProduccion(){

    if($this->estado == "Elegir")
    {
      $this->emit('sale-error','DEBE ELEGIR EL ESTADO DE LA PRODUCCION');
      return;
    }
    
    $cart = new CartProduccion;
    $items = $cart->getContent();
    $total = $cart->totalAmount();
    $cantidad_items = $cart->totalCantidad();
    $observaciones = $this->observaciones;
    
    // SaveProduccionTrait($origen,$cart,$total,$cantidad_items,$observaciones,$estado,$fecha_produccion)    
    $this->SaveProduccionTrait(1,$items,$total,$cantidad_items,$observaciones,$this->estado,$this->fecha_produccion,0);
  
    $this->observaciones = '';
    $this->monto = 0;

    $cart->clear();
    $this->emit('sale-ok','Produccion registrada con éxito');

}

}
