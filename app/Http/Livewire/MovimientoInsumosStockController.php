<?php

namespace App\Http\Livewire;

use App\Services\CartMovimientoInsumos;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Session;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\proveedores;
use App\Models\insumo;
use App\Models\ClientesMostrador;
use App\Models\Category;
use App\Models\metodo_pago;
use App\Models\movimiento_insumos_stocks;
use App\Models\movimiento_insumos_stocks_detalles;
use App\Models\productos_stock_sucursales;
use App\Models\cajas;
use App\Models\User;
use App\Models\sucursales;
use App\Models\presupuestos;
use App\Models\presupuestos_detalle;
use App\Models\productos_variaciones_datos;
use App\Models\productos_variaciones;
use App\Models\variaciones;
use App\Models\atributos;
use App\Models\historico_stock;
use App\Models\pagos_facturas;
use App\Models\Product;
use App\Models\bancos;
use App\Models\compras_proveedores;
use App\Models\detalle_compra_proveedores;
use DB;
use Notification;
use App\Notifications\NotificarCambios;
use App\Traits\ProduccionTrait;

class MovimientoInsumosStockController extends Component
{
  use WithPagination;
  use WithFileUploads;
  use ProduccionTrait;

  public $name,$barcode,$cost,$price,$pago, $metodos, $proveedor_id, $caja, $referencia_variacion, $stock,$alerts,$categoryid, $codigo, $monto_total, $search, $image, $product_id, $pageTitle,$componentName,$comercio_id,$data_Cart, $observaciones, $nombre_sucursal_origen, $query_product, $products_s , $Cart, $deuda,$metodo_pago_elegido, $product,$total, $iva, $iva_general, $itemsQuantity, $cantidad, $carrito, $qty, $sucursal_origen, $tipo_factura, $numero_factura, $query, $tipo_pago, $nombre_sucursal_destino, $query_id, $vigencia;
  private $pagination = 25;

  public $productos_variaciones_datos = [];

  public function mount()
  {
    $this->pageTitle = 'Listado';
    $this->cantidad = 1;
    $this->pago = 0;
    $this->metodos = [];
    $this->tipo_pago = "Elegir";
    $this->iva_general = 0;
    $this->sucursal_origen = 0;
    $this->componentName = 'Productos';
    $this->categoryid = 'Elegir';
    $this->tipo_factura = 'Elegir';
    $this->sucursal_origen = session('SucursalOrigen');
    $this->sucursal_destino = session('SucursalDestino');
    $this->metodo_pago_elegido = session('MetodoPagoPresupuesto');
    $this->tipo_pago_elegido = session('TipoPagoPresupuesto');


    if($this->sucursal_origen != null){

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

      //   SI LA SUCURSAL NO ES LA CASA CENTRAL //

      if($this->sucursal_origen != 1) {


        $this->sucursal_origen_nueva = User::where('users.id', $this->sucursal_origen)->first();

        session(['SucursalOrigen' => $this->sucursal_origen]);

        $this->nombre_sucursal_origen = $this->sucursal_origen_nueva->name;

      } else {

        // SI LA SUCURSAL ES LA CASA CENTRAL //


        $this->tipo_usuario = User::find(Auth::user()->id);

        if($this->tipo_usuario->sucursal != 1) {

          //------- SI EL USUARIO ES LA CASA CENTRAL ------ //

          $this->sucursal_origen = $comercio_id;

          $this->sucursal_origen_nueva = User::where('users.id', $this->sucursal_origen)->first();

          session(['SucursalOrigen' => $this->sucursal_origen]);

          $this->nombre_sucursal_origen = $this->sucursal_origen_nueva->name;

        } else {

          //------- SI EL USUARIO NO ES LA CASA CENTRAL ------ //

          $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
          $this->sucursal_origen = $this->casa_central->casa_central_id;

          $this->sucursal_origen_nueva = User::where('users.id', $this->sucursal_origen)->first();

          session(['SucursalOrigen' => $this->sucursal_origen]);

          $this->nombre_sucursal_origen = $this->sucursal_origen_nueva->name;
        }

      }



    }

    if($this->sucursal_destino != null){
      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

      //   SI LA SUCURSAL NO ES LA CASA CENTRAL //

      if($this->sucursal_destino != 1) {


        $this->sucursal_destino_nueva = User::where('users.id', $this->sucursal_destino)->first();

        session(['SucursalDestino' => $this->sucursal_destino]);

        $this->nombre_sucursal_destino = $this->sucursal_destino_nueva->name;

      } else {

        // SI LA SUCURSAL ES LA CASA CENTRAL //


        $this->tipo_usuario = User::find(Auth::user()->id);

        if($this->tipo_usuario->sucursal != 1) {

          //------- SI EL USUARIO ES LA CASA CENTRAL ------ //

          $this->sucursal_destino = $comercio_id;

          $this->sucursal_destino_nueva = User::where('users.id', $this->sucursal_destino)->first();

          session(['SucursalDestino' => $this->sucursal_destino]);

          $this->nombre_sucursal_destino = $this->sucursal_destino_nueva->name;

        } else {

          //------- SI EL USUARIO NO ES LA CASA CENTRAL ------ //

          $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
          $this->sucursal_destino = $this->casa_central->casa_central_id;

          $this->sucursal_destino_nueva = User::where('users.id', $this->sucursal_destino)->first();

          session(['SucursalDestino' => $this->sucursal_destino]);

          $this->nombre_sucursal_destino = $this->sucursal_destino_nueva->name;
        }

      }
    }

    if($this->metodo_pago_elegido != null){
      $this->metodo_pago_elegido = $this->metodo_pago_elegido;

    } else {
      $this->metodo_pago_elegido = 'Elegir';
    }


  }

  public function render()
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $cart = new CartMovimientoInsumos;

    $this->tipo_usuario = User::find(Auth::user()->id);
    $this->casa_central_id = Auth::user()->casa_central_user_id;

    if($this->tipo_usuario->sucursal != 1) {


    $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
    ->select('users.name','sucursales.sucursal_id')
    ->where('casa_central_id', $comercio_id)
    ->get();


    } else {
    $this->sucursal_id = $comercio_id;
    $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
    $this->casa_central_id = $this->casa_central->casa_central_id;

    $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
    ->select('users.name','sucursales.sucursal_id')
    ->where('casa_central_id', $this->casa_central->casa_central_id)
    ->get();


    }

      $metodo_pagos = bancos::where('bancos.comercio_id', 'like', $comercio_id)->get();

      $proveedores = proveedores::where('proveedores.comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();


		$this->metodos =  metodo_pago::join('bancos','bancos.id','metodo_pagos.cuenta')->select('metodo_pagos.*','bancos.nombre as nombre_banco')->where('metodo_pagos.comercio_id', 'like', $comercio_id);

      if($this->tipo_pago != 1 &&  $this->tipo_pago != 2 && $this->tipo_pago != null) {
        $this->metodos = $this->metodos->where('metodo_pagos.cuenta', 'like', $this->tipo_pago);
      }

      $this->metodos = $this->metodos->orderBy('metodo_pagos.nombre','asc')->get();

    return view('livewire.movimiento-insumos-stock.component',[
      'proveedores' => $proveedores,
      'metodo_pago' => $metodo_pagos,
      'metodos' => $this->metodos,
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

    $cart = new CartMovimientoInsumos;
    $items = $cart->getContent();


 if ($items->contains('id', $this->id_cart)) {

   $cart = new CartMovimientoInsumos;
   $items = $cart->getContent();


   foreach ($items as $i)
{
       if($i['id'] === $this->id_cart) {

         $cart->removeProduct($this->id_cart);

         $product = array(
             "id" => $i['id'],
             "barcode" => $i['barcode'],
             "product_id" => $i['product_id'],
             "referencia_variacion" => $i['referencia_variacion'],
             "name" => $i['name'],
             "stock" => $i['stock'],
             "costo" => $i['costo'],
             "qty" => $i['qty']+1,
         );

         $cart->addProduct($product);

     }
}

    $this->resetUI();

    $this->emit('product-added','Producto agregado');

   return back();

} else {

    if($this->stock < $this->cantidad ) {
      $this->emit('sale-error','STOCK INSUFICIENTE. STOCK: '.$this->stock);
      return;
    }

      $cart = new CartMovimientoInsumos;

      $product = array(
          "id" => $this->id_cart,
          "barcode" => $this->barcode,
          "product_id" => $this->product_id,
          "referencia_variacion" => $this->referencia_variacion,
          "name" => $this->name,
          "costo" => $this->cost,
          "qty" => $this->cantidad,
          "stock" => $this->stock,
      );

      $cart->addProduct($product);

      $this->resetUI();



      $this->emit('product-added','Producto agregado');

  }

}

    public function removeProductFromCart($product) {
        $cart = new CartMovimientoInsumos;
        $cart->removeProduct($product);
        session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
        return back();
    }


  public function UpdatePrice(Product $product, $price) {
      $cart = new CartMovimientoInsumos;
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
                "recargo" => $i['recargo'],
                "descuento" => $i['descuento'],
                "price" => $price,
                "qty" => $i['qty'],
            );

            $cart->addProduct($product);

        }


   }

   $this->monto_total = $cart->totalAmount();
   $this->subtotal = $cart->subtotalAmount();
   $this->iva_total = $cart->totalIva();
   $this->descuento_total = $cart->totalDescuento();


    $this->emit('product-added','Precio modificado');
      return back();
  }


  public function updateDescuento(Product $product, $descuento) {


      $cart = new CartMovimientoInsumos;
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
                "recargo" => $i['recargo'],
                "descuento" => $descuento,
                "price" => $i['price'],
                "qty" => $i['qty'],
            );

            $cart->addProduct($product);

        }


   }

   $this->monto_total = $cart->totalAmount();
   $this->subtotal = $cart->subtotalAmount();
   $this->iva_total = $cart->totalIva();
   $this->descuento_total = $cart->totalDescuento();


      $this->emit('product-added','Descuento modificado');
      return back();
  }


public function UpdateIvaGral(Product $product) {

  if($this->iva_general != "Elegir") {

  session(['IvaGralPresupuesto' => $this->iva_general]);

    $cart = new CartMovimientoInsumos;
    $items = $cart->getContent();


    foreach ($items as $i)
 {
          $cart->removeProduct($i['id']);

          $product = array(
              "id" => $i['id'],
              "name" => $i['name'],
              "barcode" => $i['barcode'],
              "recargo" => $i['recargo'],
              "descuento" => $i['descuento'],
              "iva" => $this->iva_general,
              "price" => $i['price'],
              "qty" => $i['qty'],
          );

          $cart->addProduct($product);

 }

 $this->monto_total = $cart->totalAmount();
 $this->subtotal = $cart->subtotalAmount();
 $this->iva_total = $cart->totalIva();
 $this->recargo_total = $cart->totalRecargo();
 $this->descuento_total = $cart->totalDescuento();


    $this->emit('product-added','Iva modificado');
    return back();


  }


}

  public function updateQty($product, $qty) {
      $cart = new CartMovimientoInsumos;
      $items = $cart->getContent();

      foreach ($items as $i)
   {
          if($i['id'] === $product) {

            if($i['stock'] < $qty) {

              $this->emit('no-stock','Stock insuficiente, disponibles: '.$i['stock']);
              $this->emit('volver-stock', $product , $i['stock']);
              return;

          } else {


            $cart->removeProduct($product);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "product_id" => $i['product_id'],
                "referencia_variacion" => $i['referencia_variacion'],
                "barcode" => $i['barcode'],
                "stock" => $i['stock'],
                "costo" => $i['costo'],
                "qty" => $qty,
            );

            $cart->addProduct($product);
          }

        }


   }


    $this->emit('product-added','Cantidad modificada');
      return back();
  }



  public function Decrecer(Product $product) {
      $cart = new CartMovimientoInsumos;
      $items = $cart->getContent();


      foreach ($items as $i)
   {
          if($i['id'] === $product['id']) {

            $cart->removeProduct($product['id']);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "price" => $i['price'],
                "recargo" => $i['recargo'],
                "recargo" => $i['descuento'],
                "iva" => $i['iva'],
                "qty" => $i['qty']-1,
            );

            $cart->addProduct($product);

        }
   }

   $this->monto_total = $cart->totalAmount();
   $this->subtotal = $cart->subtotalAmount();
   $this->iva_total = $cart->totalIva();
   $this->recargo_total = $cart->totalRecargo();
   $this->descuento_total = $cart->totalDescuento();


    $this->emit('product-added','Cantidad modificada');
      return back();
  }

  // escuchar eventos
  protected $listeners = [
    'scan-code'  =>  'BuscarCode',
    	'clearCart'  => 'clearCart',
  ];


  public function SetSucursalStock($sucursal_id,$casa_central_id){
      if($sucursal_id == $casa_central_id){
      return 0;    
      } else {
      return $sucursal_id;   
      }
      
  }
  
  public function BuscarCode($barcode)
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $casa_central_id = Auth::user()->casa_central_user_id;

    $record = $this->GetInsumo($barcode,$casa_central_id);
    $stock = $this->GetStockInsumoEnSucursal($barcode,$comercio_id,$casa_central_id);

    if($record == null || empty($record))
    {

    $this->emit('scan-notfound','El producto no está registrado');

    $this->codigo = '';

    }  else {

    $this->product_id = $record->id;
    $this->referencia_variacion = 0;
    $this->id_cart = $record->id."-0";
    $this->name = $record->name;
    $this->barcode = $record->barcode;
    $this->cost = $record->cost;
    $this->stock = $stock->stock ?? 0;
    $this->image = null;

    $this->emit('show-modal','Show modal!');

    $this->codigo = '';
  }

}

  public function AbrirModal($id)
	{
		$record = insumo::find($id);

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

  }


public function SetMovimientoDB($total, $cantidad_total, $nro_movimiento)
{
    $movimiento_stocks = movimiento_insumos_stocks::create([
        'total' => $total,
        'items' => $cantidad_total,
        'sucursal_origen' => $this->sucursal_origen,
        'sucursal_destino' => $this->sucursal_destino,
        'user_id' => Auth::user()->id,
        'observacion' => $this->observaciones,
        'nro_movimiento' => $nro_movimiento,
    ]);

    return $movimiento_stocks;
}

public function SetStockSucursal($sucursal_id, $casa_central_id, $item, $accion)
{
    $product_stock = $this->GetStockInsumoEnSucursalById($item['product_id'],$sucursal_id,$casa_central_id);
      
    if ($accion == 1) {
        $stock = $product_stock->stock - $item['qty'];
    } elseif ($accion == 2) {
        $stock = $product_stock->stock + $item['qty'];
    }

    $product_stock->update([
        'stock' => $stock,
    ]);

    // Descomentar la siguiente línea si necesitas registrar los movimientos de stock
     $this->SetMovimientosStockDB($stock, $item, $sucursal_id, $sucursal_id,$accion);

    return $stock;
}

public function SetMovimientosStockDB($stock, $item, $comercio_id, $sucursal_id,$accion)
{
    if($accion == 1){
        $cant = -$item['qty'];
    }
    if($accion == 2){
        $cant = $item['qty'];
    }
    
  //  $this->SetMovimientosInsumosStockDB(12,$item['product_id'],$cant,$stock,$sucursal_id,$comercio_id);

}

public function SetMovimientosDetalleDB($item, $movimiento_stocks)
{
    $movimiento_stocks_detalles = movimiento_insumos_stocks_detalles::create([
        'product_barcode' => $item['barcode'],
        'product_id' => $item['product_id'],
        'product_name' => $item['name'],
        'referencia_variacion' => $item['referencia_variacion'],
        'costo' => $item['costo'],
        'cantidad' => $item['qty'],
        'total' => $item['costo'] * $item['qty'],
        'movimiento_id' => $movimiento_stocks->id,
    ]);

    return $movimiento_stocks_detalles;
}

public function saveSale()
{
    if ($this->sucursal_destino == 0) {
        $this->emit('sale-error', 'DEBE ELEGIR LA SUCURSAL DE DESTINO DEL MOVIMIENTO');
        return;
    }

    if ($this->sucursal_destino == $this->sucursal_origen) {
        $this->emit('sale-error', 'LA SUCURSAL DE DESTINO Y ORIGEN DEBEN SER DISTINTAS');
        return;
    }

    $cart = new CartMovimientoInsumos();

    $comercio_id = (Auth::user()->comercio_id != 1) ? Auth::user()->comercio_id : Auth::user()->id;

    DB::beginTransaction();

    try {
        // GUARDAR LOS DATOS DE MOVIMIENTOS
        $items = $cart->getContent();
        $total = $cart->totalAmount();
        $cantidad_total = $cart->totalCantidad();
        $nro_movimiento = $this->SetNroMovimiento($comercio_id);

        $movimiento_stocks = $this->SetMovimientoDB($total, $cantidad_total, $nro_movimiento);

        if ($movimiento_stocks) {
            foreach ($items as $item) {
                // AGREGAR EL DETALLE DEL MOVIMIENTO
                $movimiento_stocks_detalles = $this->SetMovimientosDetalleDB($item, $movimiento_stocks);

                // DESCUENTO EN LA SUCURSAL DE ORIGEN
                $this->SetStockSucursal($this->sucursal_origen, $this->casa_central_id, $item, 1);

                // AUMENTO EN LA SUCURSAL DE DESTINO
                $this->SetStockSucursal($this->sucursal_destino, $this->casa_central_id, $item, 2);
            }
        }

        DB::commit();

        // Restablecer campos
        $this->resetFields();
        $cart->clear();
        $this->emit('sale-ok', 'Movimiento registrado con éxito');
    } catch (Exception $e) {
        DB::rollback();
        dd($e->getMessage());
    }
}

private function resetFields()
{
    $this->pago = 0;
    $this->deuda = 0;
    $this->observaciones = '';
    $this->numero_factura = '';
    $this->tipo_factura = 'Elegir';
    session(['SucursalDestino' => null]);
    session(['SucursalOrigen' => null]);
    $this->sucursal_origen = 0;
    $this->sucursal_destino = 0;
    $this->nombre_sucursal_destino = 0;
    $this->nombre_sucursal_origen = 0;
    $this->monto = 0;
    $this->metodo_pago_elegido = 'Elegir';
    $this->proveedor_id = 'Elegir';
}


  public function resetProduct()
 {
   $this->products_s = [];
 }

 public function clearCart() {
  $cart = new CartMovimientoInsumos;
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


      $this->products_s = 	insumo::where('comercio_id', 'like', $comercio_id)->where('eliminado',0)->where( function($query) {
            $query->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
        })
          ->limit(5)
          ->get()
          ->toArray();


        } else {

          if($this->sucursal_origen != 0) {

            $this->products_s = 	insumo::where('comercio_id', 'like', $this->sucursal_origen)->where('eliminado',0)->where( function($query) {
                  $query->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
              })
                ->limit(5)
                ->get()
                ->toArray();


          } else {
              dd("Debe elegir la sucursal de destino");
          }



        }



  }

  public function selectContact(ClientesMostrador $cliente)
  {

      $this->query = $cliente->nombre;
      $this->query_id = $cliente->id;

      session(['NombreCliente' => $this->query]);
      session(['IdCliente' => $this->query_id]);

      $this->cliente = ClientesMostrador::find($this->query_id);

      if($this->cliente->lista_precio != 0) {


        $this->emit('update-cliente-modal', $this->query_id);

      }

      $this->resetCliente();
  }

  public function resetCliente()
  {
    $this->contacts = [];
  }


  public function updatedQuery()
  {
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

      $this->contacts = ClientesMostrador::where('nombre', 'like', '%' . $this->query . '%')
          ->where('comercio_id', 'like', $comercio_id)
          ->orWhere('comercio_id', 'like', 1)
          ->limit(8)
          ->get()
          ->toArray();
  }

  public function TipoPago($value)
  {

    session(['TipoPagoPresupuesto' => $value]);

    if($value ==  1) {
      $this->metodo_pago_elegido = 1;
      $this->MetodoPago($value);
    } else {
      $this->metodo_pago_elegido = "Elegir";
    }

  }


  public function MetodoPago($value)
  {

  	if($value == 'OTRO') {
  	$this->emit('metodo-pago-nuevo-show','Sales');

  	return;
  	}

  	$metodo_pago = metodo_pago::find($value);

  	$this->recargo = $metodo_pago->recargo/100;

  	session(['MetodoPagoPresupuesto' => $value]);

  	$this->metodo_pago_nuevo = $metodo_pago->id;

    $cart = new CartMovimientoInsumos;
    $items = $cart->getContent();


    foreach ($items as $i)
 {

          $this->recargo = $metodo_pago->recargo/100;

          $cart->removeProduct($i['id']);

          $product = array(
              "id" => $i['id'],
              "name" => $i['name'],
              "barcode" => $i['barcode'],
              "iva" => $i['iva'],
              "descuento" => $i['descuento'],
              "recargo" => $this->recargo,
              "price" => $i['price'],
              "qty" => $i['qty'],
          );

          $cart->addProduct($product);

 }

 $this->monto_total = $cart->totalAmount();
 $this->subtotal = $cart->subtotalAmount();
 $this->recargo_total = $cart->totalRecargo();
 $this->iva_total = $cart->totalIva();


  }



  public function ElegirSucursal($sucursal) {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;


    //   SI LA SUCURSAL NO ES LA CASA CENTRAL //

    if($sucursal != 1) {

      $this->sucursal_origen = $sucursal;

      $this->sucursal_origen_nueva = User::where('users.id', $sucursal)->first();

      session(['SucursalOrigen' => $sucursal]);

      $this->nombre_sucursal_origen = $this->sucursal_origen_nueva->name;

    } else {

      // SI LA SUCURSAL ES LA CASA CENTRAL //


      $this->tipo_usuario = User::find(Auth::user()->id);

      if($this->tipo_usuario->sucursal != 1) {

        //------- SI EL USUARIO ES LA CASA CENTRAL ------ //

        $this->sucursal_origen = $comercio_id;

        $this->sucursal_origen_nueva = User::where('users.id', $this->sucursal_origen)->first();

        session(['SucursalOrigen' => $sucursal]);

        $this->nombre_sucursal_origen = $this->sucursal_origen_nueva->name;

      } else {

        //------- SI EL USUARIO NO ES LA CASA CENTRAL ------ //

        $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
        $this->sucursal_origen = $this->casa_central->casa_central_id;

        $this->sucursal_origen_nueva = User::where('users.id', $this->sucursal_origen)->first();

        session(['SucursalOrigen' => $sucursal]);

        $this->nombre_sucursal_origen = $this->sucursal_origen_nueva->name;
      }

    }


    $this->emit('agregar-origen-hide','');
  }

public function ElegirSucursalDest($sucursal) {

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  //   SI LA SUCURSAL NO ES LA CASA CENTRAL //

  if($sucursal != 1) {

    $this->sucursal_destino = $sucursal;

    $this->sucursal_destino_nueva = User::where('users.id', $sucursal)->first();

    session(['SucursalDestino' => $sucursal]);

    $this->nombre_sucursal_destino = $this->sucursal_destino_nueva->name;

  } else {

    // SI LA SUCURSAL ES LA CASA CENTRAL //


    $this->tipo_usuario = User::find(Auth::user()->id);

    if($this->tipo_usuario->sucursal != 1) {

      //------- SI EL USUARIO ES LA CASA CENTRAL ------ //

      $this->sucursal_destino = $comercio_id;

      $this->sucursal_destino_nueva = User::where('users.id', $this->sucursal_destino)->first();

      session(['SucursalDestino' => $sucursal]);

      $this->nombre_sucursal_destino = $this->sucursal_destino_nueva->name;

    } else {

      //------- SI EL USUARIO NO ES LA CASA CENTRAL ------ //

      $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
      $this->sucursal_destino = $this->casa_central->casa_central_id;

      $this->sucursal_destino_nueva = User::where('users.id', $this->sucursal_destino)->first();

      session(['SucursalDestino' => $sucursal]);

      $this->nombre_sucursal_destino = $this->sucursal_destino_nueva->name;
    }

  }


  $this->emit('agregar-destino-hide','');
}



public function ElegirSucursalOrigen() {

$this->emit('agregar-origen','');
}

public function ElegirSucursalDestino() {

$this->emit('agregar-destino','');
}



  public function SetNroMovimiento($comercio_id){
      $movimiento = movimiento_insumos_stocks::where('user_id',$comercio_id)->orderBy('id','desc')->first();

      if($movimiento != null) {
      if($movimiento->nro_movimiento != null) {
      $nro_movimiento = $movimiento->nro_movimiento + 1;    
      } else {
      $nro_movimiento = 1;    
      }
          
      } else {$nro_movimiento = 1;}
      
      return $nro_movimiento;
      
    }
    
}
