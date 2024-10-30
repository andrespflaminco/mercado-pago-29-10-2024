<?php

namespace App\Http\Livewire;

use App\Services\CartInsumos;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Session;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\proveedores;
use App\Models\Category;
use Carbon\Carbon;
use App\Models\metodo_pago;
use App\Models\cajas;
use App\Models\historico_stock;
use App\Models\historico_stock_insumo;
use App\Models\pagos_facturas;
use App\Models\insumo;
use App\Models\Product;
use App\Models\bancos;
use App\Models\compras_insumos;
use App\Models\detalle_compra_insumos;
use DB;
use App\Traits\ProduccionTrait;
use App\Traits\BancosTrait;



class ComprasInsumosController extends Component
{
  use WithPagination;
  use WithFileUploads;
  use BancosTrait;
  use ProduccionTrait;
  
  public $name,$barcode,$cost,$price,$pago,$ultimas_cajas, $proveedor_id,$fecha_ap, $caja, $stock,$alerts,$categoryid,$monto_inicial, $caja_abierta, $codigo, $monto_total, $search, $image, $selected_id, $pageTitle,$componentName,$comercio_id,$data_Cart, $observaciones, $query_product, $products_s , $Cart, $deuda,$metodo_pago_elegido, $product,$total, $iva, $iva_general, $itemsQuantity, $cantidad, $carrito, $qty, $tipo_factura, $numero_factura;
  private $pagination = 25;

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
    $this->comercio_id = $comercio_id;
    $casa_central_id = Auth::user()->casa_central_user_id;
    
    $cart = new CartInsumos;
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

    $metodo_pagos = $this->GetBancosTrait($comercio_id);

      $proveedores = proveedores::where('proveedores.comercio_id', 'like', $casa_central_id)->where('eliminado',0)->get();
    
    // CAJAS 
    $this->ultimas_cajas = cajas::where('comercio_id', $comercio_id)->where('eliminado',0)->orderby('created_at','desc')->limit(5)->get();
 
    $this->caja_abierta = cajas::where('estado',0)->where('comercio_id',$comercio_id)->max('id');
    
    return view('livewire.compras_insumos.component',[
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
}




  public function Agregar() {

    $this->iva_general = session('IvaGral');

    if($this->iva_general != "Elegir") {
      $this->iva_agregar = $this->iva_general;
    } else {
      $this->iva_agregar = 0;
    }


    $cart = new CartInsumos;
    $items = $cart->getContent();


 if ($items->contains('id', $this->selected_id)) {

   $cart = new CartInsumos;
   $items = $cart->getContent();

   $product = Product::find($this->selected_id);

   foreach ($items as $i)
{
       if($i['id'] === $product['id']) {

         $cart->removeProduct($i['id']);

         $product = array(
             "id" => $i['id'],
            "barcode" => $i['barcode'],
             "name" => $i['name'],
             "iva" => $i['iva'],
             "cost" => $i['cost'],
             "qty" => $i['qty']+1,
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

      $cart = new CartInsumos;

      $product = array(
          "id" => $this->selected_id,
          "barcode" => $this->barcode,
          "name" => $this->name,
          "price" => $this->price,
          "iva" => $this->iva_agregar,
          "cost" => $this->cost,
          "qty" => $this->cantidad,
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

    public function removeProductFromCart(insumo $product) {
        $cart = new CartInsumos;
        $cart->removeProduct($product->id);
        session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
        return back();
    }


  public function Incrementar(insumo $product) {
      $cart = new CartInsumos;
      $items = $cart->getContent();


      foreach ($items as $i)
   {
          if($i['id'] === $product['id']) {

            $cart->removeProduct($product['id']);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "iva" => $i['iva'],
                "cost" => $i['cost'],
                "qty" => $i['qty']+1,
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


  public function UpdateIva(insumo $product, $iva) {
      $cart = new CartInsumos;
      $items = $cart->getContent();


      foreach ($items as $i)
   {
          if($i['id'] === $product['id']) {

            $cart->removeProduct($product['id']);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "iva" => $iva,
                "cost" => $i['cost'],
                "qty" => $i['qty'],
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


  public function UpdatePrice(insumo $product, $price) {
      $cart = new CartInsumos;
      $items = $cart->getContent();

      foreach ($items as $i)
   {
          if($i['id'] === $product['id']) {

            $cart->removeProduct($product['id']);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "iva" => $i['iva'],
                "cost" => $price,
                "qty" => $i['qty'],
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





public function UpdateIvaGral(insumo $product) {

  if($this->iva_general != "Elegir") {

  session(['IvaGral' => $this->iva_general]);

     $cart = new CartInsumos;
     $items = $cart->getContent();


    foreach ($items as $i)
 {
          $cart->removeProduct($i['id']);

          $product = array(
              "id" => $i['id'],
              "name" => $i['name'],
              "barcode" => $i['barcode'],
              "iva" => $this->iva_general,
              "cost" => $i['cost'],
              "qty" => $i['qty'],
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

  public function updateQty(insumo $product, $qty) {
      $cart = new CartInsumos;
      $items = $cart->getContent();

      foreach ($items as $i)
   {
          if($i['id'] === $product['id']) {

            $cart->removeProduct($product['id']);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "iva" => $i['iva'],
                "cost" => $i['cost'],
                "qty" => $qty,
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



  public function Decrecer(insumo $product) {
      $cart = new CartInsumos;
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
                "iva" => $i['iva'],
                "qty" => $i['qty']-1,
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
    $casa_central_id = Auth::user()->casa_central_user_id;
    
    $record = insumo::where('barcode',$barcode)->where('comercio_id', $casa_central_id)->first();

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
    $this->selected_id = 0;
    $this->caja = null;

  }

  // guardar venta
  public function saveSale()
  {

    $cart = new CartInsumos;

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

    if($this->tipo_factura == "Elegir"){
    $this->tipo_factura = 'CF';    
    }
    

    if($this->iva_general == "Elegir") {$this->iva_general = 0; }

    $cart = new CartInsumos;

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    $casa_central_id = Auth::user()->casa_central_user_id;

    $nro_compra = $this->SetNroCompra();
    
    DB::beginTransaction();

    try {

      $this->monto_total = $cart->totalAmount();
      $this->total = $cart->subtotalAmount();
      $this->iva_total = $cart->totalIva();
      $this->deuda = $this->monto_total - $this->pago;

            $sale = compras_insumos::create([
              'nro_compra' => $nro_compra,
              'subtotal' => $cart->subtotalAmount(),
              'alicuota_iva' => $this->iva_general,
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

            if($this->metodo_pago_elegido == 1) {
                $mp = 1;
            } else {$mp = 0;}
            

            $estado_pago = $this->GetPlazoAcreditacionPago($this->metodo_pago_elegido);

            $pagos = pagos_facturas::create([
              'estado_pago' => $estado_pago,
              'monto_compra' => $this->pago,
              'id_compra_insumos' => $sale->id,
              'comercio_id' => $comercio_id,
              'caja' => $this->caja,
              'banco_id'  => $this->metodo_pago_elegido,
              'metodo_pago'  => $mp,
              'proveedor_id' => $this->proveedor_id,
              'tipo_pago' => 1,
              'eliminado' => 0
            ]);


      if($sale)
      {
          $items = $cart->getContent();

        foreach ($items as  $item) {
          detalle_compra_insumos::create([
            'producto_id' => $item['id'],
            'precio' => $item['cost'],
            'nombre' => $item['name'],
            'barcode' => $item['barcode'],
            'cantidad' => $item['qty'],
            'iva' => $item['iva']*$item['cost'],
            'alicuota_iva' => $item['iva'],
            'compra_id' => $sale->id,
            'comercio_id' => $comercio_id
          ]);

          //update stock  --> Actualizar stocks
          
          $product = $this->GetStockInsumoEnSucursalById($item['id'],$comercio_id,$casa_central_id);
          
        //  $product = insumo::find($item['id']);
          $product->stock = $product->stock + $item['qty'];
          $product->save();


          $historico_stock = historico_stock_insumo::create([
            'tipo_movimiento' => 9,
            'insumo_id' => $item['id'],
            'cantidad_movimiento' => $item['qty'],
            'cantidad_contenido' => $product->cantidad,
            'unidad_medida_insumo' => $product->unidad_medida,
            'relacion_unidad_medida' => $product->relacion_unidad_medida,
            'stock' => $product->stock,
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
 $cart = new CartInsumos;
  $cart->clear();
  
  $this->deuda = "";
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
    $casa_central_id = Auth::user()->casa_central_user_id;

    $this->products_s = insumo::where('comercio_id', 'like', $casa_central_id)->where('eliminado',0)->where( function($query) {
            $query->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
        })
          ->limit(5)
          ->get()
          ->toArray();



  }

  public function SetNroCompra(){
      
      $compra = compras_insumos::where('comercio_id',$this->comercio_id)->orderBy('id','desc')->first();
      
      if($compra != null) {
      if($compra->nro_compra != null) {
      $nro_compra = $compra->nro_compra + 1;    
      } else {
      $nro_compra = 1;    
      }
          
      } else {$nro_compra = 1;}
      
      return $nro_compra;
      
    }
    
/// CAJAS

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


  public function SinCaja()
  {

  $this->caja = null;
  $this->caja_seleccionada =  null;
  $this->emit('msg','Sin caja seleccionada');
  
  //dd($this->caja);

  }
  
  
public function GetPlazoAcreditacionPago($id){
    return $id == 1 ? 1 : 0;
}
    
    
}
