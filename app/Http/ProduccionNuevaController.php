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
use App\Models\receta;
use App\Models\insumo;
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
use App\Models\detalle_compra_proveedores;
use DB;

class ProduccionNuevaController extends Component
{
  use WithPagination;
  use WithFileUploads;

  public $name,$barcode,$cost,$price,$pago, $proveedor_id, $caja, $stock,$alerts,$categoryid, $codigo, $monto_total, $search, $image, $selected_id, $pageTitle,$componentName,$comercio_id,$data_Cart, $observaciones, $query_product, $products_s , $Cart, $deuda,$metodo_pago_elegido, $product,$total, $estado, $iva, $iva_general, $itemsQuantity, $cantidad, $carrito, $qty, $tipo_factura, $numero_factura;
  private $pagination = 25;

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
    $this->iva_general = session('IvaGral');
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
    $this->iva_total = $cart->totalIva();


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
    ->extends('layouts.theme.app')
    ->section('content');
  }



  public function Agregar() {

    $this->iva_general = session('IvaGral');

    if($this->iva_general != "Elegir") {
      $this->iva_agregar = $this->iva_general;
    } else {
      $this->iva_agregar = 0;
    }


    $cart = new CartProduccion;
    $items = $cart->getContent();


 if ($items->contains('id', $this->selected_id)) {

   $cart = new CartProduccion;
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

   return back();

} else {

      $cart = new CartProduccion;

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

      $this->resetUI();



      $this->emit('product-added','Producto agregado');

  }

}

    public function removeProductFromCart(Product $product) {
        $cart = new CartProduccion;
        $cart->removeProduct($product->id);
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


  public function UpdateIva(Product $product, $iva) {
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

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }


  public function UpdatePrice(Product $product, $price) {
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

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }





public function UpdateIvaGral(Product $product) {

  if($this->iva_general != "Elegir") {

  session(['IvaGral' => $this->iva_general]);

    $cart = new CartProduccion;
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

    session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
    return back();


  }


}

  public function updateQty(Product $product, $qty) {
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
                "iva" => $i['iva'],
                "qty" => $i['qty']-1,
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

    $record = Product::where('barcode',$barcode)->where('comercio_id', $comercio_id)->first();

    if($record == null || empty($record))
    {

    $this->emit('scan-notfound','El producto no está registrado');

    $this->codigo = '';

    }  else {

      $this->receta = Product::leftjoin('recetas as r','r.product_id','products.id')
      ->leftjoin('insumos','insumos.id','r.insumo_id')
      ->join('unidad_medidas','unidad_medidas.id','r.unidad_medida')
      ->select(receta::raw(' SUM(r.cantidad*r.costo_unitario*r.relacion_medida) AS cost'))
      ->where('products.id', $record->id)
      ->where('products.eliminado', 'like', 0)
      ->first();


    $this->cost = number_format($this->receta->cost,2);
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
    $this->categoryid = 'Elegir';
    $this->image = null;
    $this->selected_id = 0;

  }

  // guardar venta
  public function saveSale()
  {

    if($this->estado == "Elegir")
    {
      $this->emit('sale-error','DEBE ELEGIR EL ESTADO DE LA PRODUCCION');
      return;
    }

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $cart = new CartProduccion;

    DB::beginTransaction();

    try {

      $this->monto_total = $cart->totalAmount();

            $sale = produccion::create([
              'total' => $cart->totalAmount(),
              'items' => $cart->totalCantidad(),
              'observaciones' => $this->observaciones,
              'proveedor_id' => $this->proveedor_id,
              'estado' => $this->estado,
              'comercio_id' => $comercio_id,
              'user_id' => Auth::user()->id
            ]);

      if($sale)
      {
          $items = $cart->getContent();

        foreach ($items as  $item) {

          $this->produccion_detalle_id = produccion_detalle::create([
            'producto_id' => $item['id'],
            'costo' => $item['cost'],
            'nombre' => $item['name'],
            'barcode' => $item['barcode'],
            'cantidad' => $item['qty'],
            'estado' => $this->estado,
            'produccion_id' => $sale->id,
            'comercio_id' => $comercio_id
          ]);

//////////////////////////////////////////////////////////////////////////////////////////



          // CUANDO EL ESTADO DE LA PRODUCCION NUEVA ES TERMINADA //

          if($this->estado == 3) {


            //update stock
            $product = Product::find($item['id']);
            $product->stock = $product->stock + $item['qty'];
            $product->save();

            $historico_stock = historico_stock::create([
              'tipo_movimiento' => 11,
              'producto_id' => $item['id'],
              'cantidad_movimiento' => $item['qty'],
              'stock' => $product->stock,
              'comercio_id'  => $comercio_id,
              'usuario_id'  => Auth::user()->id
            ]);

            $receta = receta::where('product_id', $item['id'])->get();

            foreach ($receta as $r) {

              $insumos = insumo::find($r->insumo_id);

              // RELACION DE UNIDADES DE MEDIDA...

              $this->relacion_receta = unidad_medida_relacion::where('unidad_medida',$r->unidad_medida)->first();

              $this->relacion_insumo = unidad_medida_relacion::where('unidad_medida', $insumos->unidad_medida)->first();


              $this->relacion_medidas = $this->relacion_receta->relacion/$this->relacion_insumo->relacion;

              $this->relacion_cantidades = $r->cantidad/$insumos->cantidad;

              $this->relacion = $this->relacion_cantidades * $this->relacion_medidas;

              // ..................................

              $this->cantidad_insumos = -1*($item['qty']*$this->relacion);

              $this->stock_nuevo_insumos = $insumos->stock+$this->cantidad_insumos;


              $historico_stock = historico_stock_insumo::create([
                'tipo_movimiento' => 11,
                'insumo_id' => $r->insumo_id,
                'produccion_detalle_id' => $this->produccion_detalle_id->id,
                'cantidad_receta' => $r->cantidad,
                'unidad_medida_receta' => $r->unidad_medida,
                'cantidad_movimiento' => -$this->cantidad_insumos,
                'cantidad_contenido' => $insumos->cantidad,
                'unidad_medida_insumo' => $insumos->unidad_medida,
                'relacion_unidad_medida' => $this->relacion,
                'stock' => $this->stock_nuevo_insumos,
                'comercio_id'  => $comercio_id,
                'usuario_id'  => Auth::user()->id
              ]);


              $insumos->stock = $this->stock_nuevo_insumos;
              $insumos->save();

            }

          }

///////////////////////////////////////////////////////////////////////////////////////


//////////// CUANDO EL ESTADO DE LA PRODUCCION ES EN PROCESO  /////////////////////


          if($this->estado == 2) {



            $receta = receta::where('product_id', $item['id'])->get();

            foreach ($receta as $r) {


              $insumos = insumo::find($r->insumo_id);


              // RELACION DE UNIDADES DE MEDIDA...

              $this->relacion_receta = unidad_medida_relacion::where('unidad_medida',$r->unidad_medida)->first();

              $this->relacion_insumo = unidad_medida_relacion::where('unidad_medida', $insumos->unidad_medida)->first();


              $this->relacion_medidas = $this->relacion_receta->relacion/$this->relacion_insumo->relacion;

              $this->relacion_cantidades = $r->cantidad/$insumos->cantidad;

              $this->relacion = $this->relacion_cantidades * $this->relacion_medidas;

              // ..................................

              $this->cantidad_insumos = -1*($item['qty']*$this->relacion);

              $this->stock_nuevo_insumos = $insumos->stock+$this->cantidad_insumos;



              $historico_stock = historico_stock_insumo::create([
                'tipo_movimiento' => 11,
                'insumo_id' => $r->insumo_id,
                'produccion_detalle_id' => $this->produccion_detalle_id->id,
                'cantidad_receta' => $r->cantidad,
                'unidad_medida_receta' => $r->unidad_medida,
                'cantidad_movimiento' => -$this->cantidad_insumos,
                'cantidad_contenido' => $insumos->cantidad,
                'unidad_medida_insumo' => $insumos->unidad_medida,
                'relacion_unidad_medida' => $this->relacion,
                'stock' => $this->stock_nuevo_insumos,
                'comercio_id'  => $comercio_id,
                'usuario_id'  => Auth::user()->id
              ]);

              $insumos->stock = $this->stock_nuevo_insumos;
              $insumos->save();

            }

          }



          ////////////////////////////////////////////////////////////////////////////////////////////////


          //////////// CUANDO EL ESTADO DE LA PRODUCCION ES EN PROCESO  /////////////////////


                    if($this->estado == 1) {



                      $receta = receta::where('product_id', $item['id'])->get();

                      foreach ($receta as $r) {


                        $insumos = insumo::find($r->insumo_id);


                        // RELACION DE UNIDADES DE MEDIDA...

                        $this->relacion_receta = unidad_medida_relacion::where('unidad_medida',$r->unidad_medida)->first();

                        $this->relacion_insumo = unidad_medida_relacion::where('unidad_medida', $insumos->unidad_medida)->first();


                        $this->relacion_medidas = $this->relacion_receta->relacion/$this->relacion_insumo->relacion;

                        $this->relacion_cantidades = $r->cantidad/$insumos->cantidad;

                        $this->relacion = $this->relacion_cantidades * $this->relacion_medidas;

                        // ..................................

                        $this->cantidad_insumos = -1*($item['qty']*$this->relacion);

                        $this->stock_nuevo_insumos = $insumos->stock+$this->cantidad_insumos;



                        $historico_stock = historico_stock_insumo::create([
                          'tipo_movimiento' => 11,
                          'insumo_id' => $r->insumo_id,
                          'produccion_detalle_id' => $this->produccion_detalle_id->id,
                          'cantidad_receta' => $r->cantidad,
                          'unidad_medida_receta' => $r->unidad_medida,
                          'cantidad_movimiento' => -$this->cantidad_insumos,
                          'cantidad_contenido' => $insumos->cantidad,
                          'unidad_medida_insumo' => $insumos->unidad_medida,
                          'relacion_unidad_medida' => $this->relacion,
                          'stock' => $this->stock_nuevo_insumos,
                          'comercio_id'  => $comercio_id,
                          'usuario_id'  => Auth::user()->id
                        ]);

                        $insumos->stock = $this->stock_nuevo_insumos;
                        $insumos->save();

                      }

                    }



                    ////////////////////////////////////////////////////////////////////////////////////////////////






        }

      }


      DB::commit();

      $this->observaciones = '';
      $this->monto = 0;

      $cart->clear();
      $this->emit('sale-ok','Produccion registrada con éxito');



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



      $this->products_s = 	Product::where('comercio_id', 'like', $comercio_id)->where('eliminado',0)->where( function($query) {
            $query->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
        })
          ->limit(5)
          ->get()
          ->toArray();



  }




}
