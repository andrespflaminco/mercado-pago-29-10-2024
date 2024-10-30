<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\hoja_ruta;
use App\Models\wocommerce;
use App\Models\metodo_pago;
use Automattic\WooCommerce\Client;
use App\Models\pagos_facturas;
use App\Models\Sale;
use App\Models\ClientesMostrador;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\SaleDetail;
use Carbon\Carbon;
use DB;


class ReportsEcommerceController extends Component
{

    public $componentName, $data, $details, $sumDetails, $countDetails, $sum, $totales_ver, $cantidad_tickets, $ticket_promedio,
    $reportType, $userId, $dateFrom, $dateTo, $saleId, $comercio_id, $search, $clienteId, $selected_id, $suma_totales, $suma_cantidades, $id_pedido, $estado_estado, $NroVenta, $ventaId, $suma_monto, $suma_cash, $tot, $hojar, $hoja_ruta, $monto, $estado, $estado2, $nombre_hr, $tipo, $fecha_hr, $turno, $observaciones_hr, $dateHojaRuta, $estado_pago, $metodo_pago_sale_detail, $recargo_mp, $nombre_mp, $recargo, $count;


  	public $query_id;
  	public $products_s;
    public $mail = [];
    public $listado_hojas_ruta = [];
    public $pagos1 = [];
    public $pagos2 = [];
    public $total_total = [];
    public $usuario = [];
    public $fecha = [];
    public $detalle_cliente = [];
    public $detalle_venta = [];
    public $query_product;
    public $clientesSelectedId;
    public $UsuarioSelectedName;
    public $EstadoSelectedName;
    public $MetodoPagoSelectedName;
    public $MetodoPagoSeleccionado;

    public $Usuario_SelectedValues;
    public $Estado_SelectedValues;

    public $usuarioSeleccionado;
    public $ClienteSeleccionado;
    public $EstadoSeleccionado;
    public $clientesSelectedName = [];

    public array $locationUsers = [];
    public array $usuario_seleccionado = [];
    public array $estado_seleccionado = [];
    public array $metodo_pago_seleccionado = [];



    protected $listeners = ['deletePago' => 'DeletePago','deleteRow' => 'EliminarProductoPedido','locationUsersSelected','UsuarioSelected','EstadoSelected','Usuario_Selected','MetodoPagoSelected'];


    public function UsuarioSelected($UsuarioSelectedValues)
    {
      $this->usuario_seleccionado = $UsuarioSelectedValues;


    }

    public function MetodoPagoSelected($MetodoPagoSelectedValues)
    {
      $this->metodo_pago_seleccionado = $MetodoPagoSelectedValues;



    }




    public function EstadoSelected($EstadoSelectedValues)
    {
      $this->estado_seleccionado = $EstadoSelectedValues;

    }


    public function locationUsersSelected($locationUsersValues)
    {
      $this->locationUsers = $locationUsersValues;
    }

    public function mount()
    {
        $this->componentName ='Reportes de Ventas';
        $this->data =[];
        $this->details =[];
        $this->sumDetails =0;
        $this->countDetails =0;
        $this->reportType =0;
        $this->userId =0;
        $this->saleId =0;
        $this->estado_pago = '';
        $this->estado_estado = [];
        $this->usuarioSeleccionado = 0;
        $this->ClienteSeleccionado = 0;
        $this->clienteId =0;
        $this->clientesSelectedName = [];
        $this->dateFrom = Carbon::parse('2000-01-01 00:00:00')->format('d-m-Y');
        $this->dateTo = Carbon::now()->format('d-m-Y');
        $this->dateHojaRuta = Carbon::now()->format('d-m-Y');



    }

    public function render()
    {
      $this->SalesByDate();

      $usuario_id = Auth::user()->id;


      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;
        return view('livewire.reports-ecommerce.component', [
          'users' => User::orderBy('name','asc')
          ->where('users.comercio_id', $comercio_id)
          ->orWhere('users.id', $usuario_id)
          ->get(),
          'estados' => Sale::select('sales.status')
          ->where('sales.comercio_id', $comercio_id)
          ->groupBy('sales.status')
          ->get(),
          'metodo_pago' => metodo_pago::select('metodo_pagos.*')
          ->where('metodo_pagos.comercio_id', $comercio_id)
          ->get(),
          'clientes' => ClientesMostrador::where('comercio_id', $comercio_id)
          ->orderBy('nombre','asc')
          ->get()
        ])
        ->extends('layouts.theme.app')
    ->section('content');



    }


    public function SalesByDate()
    {

      if($this->dateFrom !== '' || $this->dateTo !== '')
      {
        $from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
        $to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';

      }

      ////////// WooCommerce ////////////

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

      $wc = wocommerce::where('comercio_id', $comercio_id)->first();

      if($wc != null){

      $woocommerce = new Client(
        $wc->url,
        $wc->ck,
        $wc->cs,

          [
              'version' => 'wc/v3',
          ]
      );
    }

    ///////////////////////////////////////////////

     $this->data = $woocommerce->get('orders');


    if($this->estado_seleccionado)
    {

    $this->data = collect($this->data)->whereIn('status', $this->estado_seleccionado)->all();

    }




  $this->estado = "display: block;";



  if(($this->usuario_seleccionado == []) && ($this->locationUsers == []) && ($this->metodo_pago_seleccionado == []) && ($this->estado_pago == '') && ($this->estado_seleccionado == []))
   {

     ////////// WooCommerce ////////////

     if(Auth::user()->comercio_id != 1)
     $comercio_id = Auth::user()->comercio_id;
     else
     $comercio_id = Auth::user()->id;

     $wc = wocommerce::where('comercio_id', $comercio_id)->first();

     if($wc != null){

     $woocommerce = new Client(
       $wc->url,
       $wc->ck,
       $wc->cs,

         [
             'version' => 'wc/v3',
         ]
     );


    $this->data = $woocommerce->get('orders');

 }




     //////////////////////////////////////////////////////





}
}

public function ActualizarWc() {
  ////////// WooCommerce ////////////

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $wc = wocommerce::where('comercio_id', $comercio_id)->first();



  $woocommerce = new Client(
    $wc->url,
    $wc->ck,
    $wc->cs,

      [
          'version' => 'wc/v3',
      ]
  );


 $data = $woocommerce->get('orders');

$orden = $wc->last_order_id;

foreach ($data as  $item) {

  if($item->status = 'processing') {
    $item->status = 'En proceso';
  }
  if($item->status = 'completed') {
    $item->status = 'Entregado';
  }
  if($item->status = 'refunded') {
    $item->status = 'Cancelado';
  }


if($item->id > $orden){


  $sale = Sale::create([
    'id_wocommerce' => $item->id,
    'total' => $item->total,
    'cash' => $item->total,
    'change' => 0,
    'metodo_pago'  => $item->payment_method_title,
    'comercio_id' => $comercio_id,
    'cliente_id' => $item->customer_id,
    'user_id' => Auth::user()->id,
    'canal_venta' => 'Ecommerce',
    'estado_pago' => 'Pendiente',
    'status' => $item->status
  ]);

} else {



$sale = Sale::where('id_wocommerce', $item->id);

$sale = Sale::update([
  'id_wocommerce' => $item->id,
  'total' => $item->total,
  'cash' => $item->total,
  'change' => 0,
  'metodo_pago'  => $item->payment_method_title,
  'comercio_id' => $comercio_id,
  'cliente_id' => $item->customer_id,
  'user_id' => Auth::user()->id,
  'canal_venta' => 'Ecommerce',
  'estado_pago' => 'Pendiente',
  'status' => $item->status
]);


}

$max_wc = Sale::max('id_wocommerce');

$wc->update([
  'last_order_id' => $max_wc,
]);


}

$this->emit('pago-actualizado', 'El pago fue actualizado.');

}



////////////////////// DETALLE DEL PEDIDO /////////////////////////



    public function getDetails($saleId)
    {
        $this->details = SaleDetail::join('products as p','p.id','sale_details.product_id')
        ->join('sales','sales.id','sale_details.sale_id')
        ->select('sale_details.id','sale_details.id','sale_details.price','sale_details.quantity','p.name as product','p.barcode','p.stock','p.stock_descubierto','sales.status')
        ->where('sale_details.sale_id', $saleId)
        ->where('sale_details.eliminado',0)
        ->get();

        $this->metodo_pago_sale_detail = Sale::join('metodo_pagos','metodo_pagos.id','sales.metodo_pago')
        ->select('metodo_pagos.nombre','metodo_pagos.recargo')
        ->where('sales.id', $saleId)
        ->first();

        $this->nombre_mp = $this->metodo_pago_sale_detail->nombre;
        $this->recargo_mp = $this->metodo_pago_sale_detail->recargo;


        $this->estado_estado = Sale::select('sales.status')
        ->where('sales.id', $saleId)
        ->get();


        //
        $suma = $this->details->sum(function($item){
            return $item->price * $item->quantity;
        });

        $this->sumDetails = $suma;
        $this->countDetails = $this->details->sum('quantity');
        $this->saleId = $saleId;

        $this->emit('show-modal','details loaded');

    }



    public function EliminarProductoPedido($id_pedido_prod)
    {

      $this->items = SaleDetail::find($id_pedido_prod);


      $this->items->update([
        'eliminado' => 1
        ]);


      $this->qty_item = $this->items->quantity;
      $this->price_item = $this->items->price;

      $this->total = $this->qty_item*$this->price_item;


      $this->venta = Sale::find($this->items->sale_id);

      $this->total_nuevo = $this->venta->total - $this->total;
      $this->items_nuevo = $this->venta->items - $this->qty_item;

      $this->venta->update([
        'total' => $this->total_nuevo,
        'items' => $this->items_nuevo
        ]);


        $this->producto = Product::find($this->items->product_id);

        $this->stock_nvo_eliminar = $this->producto->stock + $this->qty_item;

        $this->producto->update([
          'stock' => $this->stock_nvo_eliminar
          ]);

          $this->ActualizarEstadoDeuda($this->items->sale_id);

        $this->getDetails($this->items->sale_id);
    }

    public function updateQtyPedido($id_pedido_prod, $cant = 1)
    {


      $this->items_viejo = SaleDetail::find($id_pedido_prod);

      $product = Product::find($this->items_viejo->product_id);



      $this->qty_item_viejo = $this->items_viejo->quantity;
      $this->price_item_viejo = $this->items_viejo->price;


      $this->items_viejo->update([
        'quantity' => $cant
        ]);


        $this->items_nuevo = SaleDetail::find($id_pedido_prod);

        $this->qty_item_nuevo = $this->items_nuevo->quantity;
        $this->price_item_nuevo = $this->items_nuevo->price;

      $this->diferencia_items = ($this->qty_item_viejo-$this->qty_item_nuevo);
      $this->diferencia_total = $this->diferencia_items*$this->price_item_nuevo;

      $this->venta = Sale::find($this->items_nuevo->sale_id);

      $this->total_venta_nuevo = $this->venta->total - $this->diferencia_total;
      $this->items_venta_nuevo = $this->venta->items - $this->diferencia_items;

      $this->venta->update([
        'total' => $this->total_venta_nuevo,
        'items' => $this->items_venta_nuevo
        ]);

        //update stock
        $product->stock = $product->stock + $this->diferencia_items;
        $product->save();

        $this->ActualizarEstadoDeuda($this->items_nuevo->sale_id);

        $this->getDetails($this->items_nuevo->sale_id);


    }


    public function updatePricePedido($id_pedido_prod, $cant = 1)
    {


      $this->items_viejo = SaleDetail::find($id_pedido_prod);

      $product = Product::find($this->items_viejo->product_id);



      $this->qty_item_viejo = $this->items_viejo->quantity;
      $this->price_item_viejo = $this->items_viejo->price;


      $this->items_viejo->update([
        'price' => $cant
        ]);


        $this->items_nuevo = SaleDetail::find($id_pedido_prod);

        $this->qty_item_nuevo = $this->items_nuevo->quantity;
        $this->price_item_nuevo = $this->items_nuevo->price;

      $this->diferencia_items = ($this->qty_item_viejo-$this->qty_item_nuevo);
      $this->diferencia_total = $this->diferencia_items*$this->price_item_nuevo;

      $this->venta = Sale::find($this->items_nuevo->sale_id);

      $this->total_venta_nuevo = $this->venta->total - $this->diferencia_total;
      $this->items_venta_nuevo = $this->venta->items - $this->diferencia_items;

      $this->venta->update([
        'total' => $this->total_venta_nuevo
        ]);

          $this->ActualizarEstadoDeuda($this->items_nuevo->sale_id);


        $this->getDetails($this->items_nuevo->sale_id);


    }



    public function getDetails2($saleId)
    {

      $this->id_pedido = $saleId;

        $this->emit('show-modal2','details loaded');

    }

    public function Update($estado_id)
    {

      $data = [
      'status' => $estado_id
      ];

      $woocommerce->put('orders/'.$this->id_pedido, $data);

      $this->emit('hide-modal2','details loaded');


    }



    public function updatedQueryProduct()
    {
      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;



        $this->products_s = 	Product::where('comercio_id', 'like', $comercio_id)->where( function($query) {
              $query->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
          })
            ->limit(5)
            ->get()
            ->toArray();

            $this->getDetails($this->saleId);



    }
    public function resetProduct()
   {
     $this->products_s = [];
      $this->query_product = '';
       $this->getDetails($this->saleId);
   }


    public function selectProduct($item)
    {

      $producto_venta = SaleDetail::where('sale_details.product_id', $item)->where('sale_details.sale_id', $this->saleId)->where('sale_details.eliminado', 0)->first();



      $product = Product::find($item);
      $venta = Sale::find($this->saleId);


      if($producto_venta == [] || $producto_venta == null || empty($producto_venta))
      {

      if($product->stock < 1 && $product->stock_descubierto == "si" ) {

        $this->emit('no-stock','Stock insuficiente');
        $this->getDetails($this->saleId);


      } else {

        $this->recargo = $this->recargo = 1+($this->recargo_mp/100);

        $this->recargo = $product->price*$this->recargo;

      SaleDetail::create([
        'price' => $this->recargo,
        'quantity' => 1,
        'product_id' => $product->id,
        'metodo_pago'  => $venta->metodo_pago,
        'seccionalmacen_id' => $product->seccionalmacen_id,
        'comercio_id' => $product->comercio_id,
        'sale_id' => $this->saleId,
        'canal_venta' => $venta->canal_venta,
        'cliente_id' => $venta->cliente_id
      ]);

      $tot = $venta->total + $this->recargo;

      $venta->update([
        'total' => $tot,
        'items' => $venta->items + 1
      ]);

      //update stock
      $product = Product::find($item);
      $product->stock = $product->stock - 1;
      $product->save();

      $this->ActualizarEstadoDeuda($this->saleId);

    }
      } else {

        if($product->stock < 1 && $product->stock_descubierto == "si" ) {

          $this->emit('no-stock','Stock insuficiente');
          $this->getDetails($this->saleId);


        } else {

      $producto_venta->update([
        'quantity' => $producto_venta->quantity + 1
      ]);


      $tot = $venta->total + $product->price;

      $venta->update([
        'total' => $tot,
        'items' => $venta->items + 1
      ]);

      //update stock
      $product = Product::find($item);
      $product->stock = $product->stock - 1;
      $product->save();

        $this->ActualizarEstadoDeuda($this->saleId);
    }

  }


        $this->resetProduct();

          $this->getDetails($this->saleId);
    }

    public function RedireccionarFactura($ventaId)
    {

      return \Redirect::to("factura/$ventaId");

    }



//////////////////////////////////////////////////////////////////////


////////////// FACTURA ///////////////



public function RenderFactura($ventaId)
   {

     $this->NroVenta = $ventaId;


     if(Auth::user()->comercio_id != 1)
     $comercio_id = Auth::user()->comercio_id;
     else
     $comercio_id = Auth::user()->id;

     $this->data_monto = Sale::leftjoin('pagos_facturas as p','p.id_factura','sales.id')
     ->select('sales.cash','sales.created_at as fecha_factura','p.monto as monto','p.created_at as fecha_pago')
     ->where('sales.id', $ventaId)
     ->where('p.eliminado',0)
     ->get();


     $this->data_cash = Sale::select('sales.cash','sales.created_at as fecha_factura')
     ->where('sales.id', $ventaId)
     ->get();

     $this->data_total = Sale::select('sales.total')
     ->where('sales.id', $ventaId)
     ->get();

       $this->ventaId = $ventaId;
       $this->hojar = hoja_ruta::join('sales','sales.hoja_ruta','hoja_rutas.id')->select('hoja_rutas.id')->where('sales.id', $ventaId)->first();
       $this->suma_monto = $this->data_monto->sum('monto');
       $this->suma_cash= $this->data_cash->sum('cash');
       $this->tot = $this->data_total->sum('total');
       $this->detalle_venta = SaleDetail::join('products as p','p.id','sale_details.product_id')
       ->select('sale_details.id','sale_details.comentario','sale_details.price','sale_details.quantity','p.name as product', SaleDetail::raw('sale_details.price*sale_details.quantity as total'))
       ->where('sale_details.sale_id', $ventaId)
       ->where('sale_details.eliminado', 0)
       ->get();
       $this->total_total = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
       ->select('sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago')
       ->where('sales.id', $ventaId)
       ->get();

       $this->usuario = User::select('users.image','users.name')
       ->where('users.id', $comercio_id)
       ->get();

       $this->fecha = Sale::select('sales.created_at')
       ->where('sales.id', $ventaId)
       ->get();
       $this->detalle_cliente = Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
       ->select('c.id','c.nombre','c.telefono','c.email','c.dni','c.direccion','c.barrio','c.localidad','c.provincia','sales.observaciones','sales.metodo_pago','sales.fecha_entrega')
       ->where('sales.id', $ventaId)
       ->get();
       $this->mail = Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
        ->select('c.email', 'sales.cash','sales.status')
        ->where('sales.id', $ventaId)
        ->get();
        $this->pagos1 = Sale::select('sales.cash','sales.created_at as fecha_factura')
        ->where('sales.id', $ventaId)
        ->get();
        $this->pagos2 = Sale::join('pagos_facturas as p','p.id_factura','sales.id')
        ->select('sales.cash','sales.created_at as fecha_factura','p.id','p.monto','p.created_at as fecha_pago')
        ->where('sales.id', $ventaId)
        ->where('p.eliminado',0)
        ->get();

        $this->listado_hojas_ruta = hoja_ruta::where('hoja_rutas.comercio_id', $comercio_id)
        ->where('hoja_rutas.fecha', '>', Carbon::now())
        ->orderBy('hoja_rutas.fecha','ASC')
        ->limit(3)
        ->get();

        $this->hoja_ruta = hoja_ruta::join('sales','sales.hoja_ruta','hoja_rutas.id')
        ->select('hoja_rutas.*')
        ->where('sales.id', $ventaId)->get();


$this->estado = "display: none;";
$this->estado2 = "display: none;";

	$this->emit('modal-show','Show modal');
              //
   }


   public function ActualizarEstadoDeuda($ventaId)
   {
     /////////////////   ACTUALIZAR ESTADO DE PAGO   /////////////////////

          $this->data_monto = Sale::leftjoin('pagos_facturas as p','p.id_factura','sales.id')
          ->select('sales.cash','sales.created_at as fecha_factura','p.monto as monto','p.created_at as fecha_pago')
          ->where('sales.id', $ventaId)
          ->where('p.eliminado',0)
          ->get();


          $this->data_cash = Sale::select('sales.cash','sales.created_at as fecha_factura')
          ->where('sales.id', $ventaId)
          ->get();

          $this->data_total = Sale::select('sales.total')
          ->where('sales.id', $ventaId)
          ->get();

          $this->suma_monto = $this->data_monto->sum('monto');
          $this->suma_cash= $this->data_cash->sum('cash');
          $this->tot = $this->data_total->sum('total');




          $deuda = $this->tot - ($this->suma_monto+$this->suma_cash);

         $this->deuda_vieja = Sale::find($ventaId);

          $this->deuda_vieja->update([
            'deuda' => $deuda
            ]);


          ///////////////////////////////////////////////////////////////////
   }


   public function UpdatePago($id_pago, $cant = 1)
   {


           $this->pago_viejo = pagos_facturas::find($id_pago);

          $ventaId = $this->pago_viejo->id_factura;


           $this->pago_viejo->update([
             'monto' => $cant
             ]);

             $this->emit('pago-actualizado', 'El pago fue actualizado.');

              $this->ActualizarEstadoDeuda($ventaId);

             $this->RenderFactura($ventaId);



             $this->estado = "display: block;";

   }


   public function CreatePago($ventaId)
   {


     pagos_facturas::create([
       'monto' => $this->monto,
       'id_factura' => $ventaId,
       'eliminado' => 0
     ]);

     $this->monto = '';

      $this->emit('pago-creado', 'El pago fue guardado.');

      $this->ActualizarEstadoDeuda($ventaId);


     $this->RenderFactura($ventaId);



     $this->estado = "display: block;";

   }

   public function DeletePago($id)
   {


           $this->pago_viejo = pagos_facturas::find($id);

          $ventaId = $this->pago_viejo->id_factura;


           $this->pago_viejo->update([
             'eliminado' => 1
             ]);

             $this->emit('pago-eliminado', 'El pago fue eliminado.');

              $this->ActualizarEstadoDeuda($ventaId);

             $this->RenderFactura($ventaId);



             $this->estado = "display: block;";

   }


   public function AsignarHojaRuta($HojaRutaElegida, $ventaId)
   {

       $Hruta = Sale::find($ventaId);

       $Hruta->update([
         'hoja_ruta' => $HojaRutaElegida
       ]);

       $this->RenderFactura($ventaId);

       $this->emit('hr-asignada', 'El pedido fue agregado a la Hoja de Ruta.');


     }

     public function SinAsignarHojaRuta($ventaId)
     {

         $Hruta = Sale::find($ventaId);

         $Hruta->update([
           'hoja_ruta' => null
         ]);

         $this->RenderFactura($ventaId);


       }


       public function GuardarHojaDeRuta($ventaId)
       {

         $rules  =[
           'fecha' => 'required',
           'tipo' => 'not_in:Elegir'

         ];

         $messages = [
           'fecha.required' => 'La fecha es requerida',
           'tipo.not_in' => 'Elija el tipo de transporte'

         ];

         $this->validate($rules, $messages);

         if(Auth::user()->comercio_id != 1)
         $comercio_id = Auth::user()->comercio_id;
         else
         $comercio_id = Auth::user()->id;

         $ultimo = hoja_ruta::where('hoja_rutas.comercio_id', 'like', $comercio_id)->select('hoja_rutas.nro_hoja','hoja_rutas.id')->latest('nro_hoja')->first();

         $hoja = $ultimo->nro_hoja + 1;
         $hoja_ulti = $ultimo->id + 1;

         $product = hoja_ruta::create([
           'nro_hoja' => $hoja,
           'fecha' => Carbon::parse($this->fecha_hr)->format('Y-m-d'),
           'nombre' => $this->nombre_hr,
           'tipo' => $this->tipo,
           'observaciones' => $this->observaciones_hr,
           'turno' => $this->turno,
           'comercio_id' => $comercio_id
         ]);




           if(Auth::user()->comercio_id != 1)
           $comercio_id = Auth::user()->comercio_id;
           else
           $comercio_id = Auth::user()->id;



         $Hruta = Sale::find($ventaId);

         $Hruta->update([
           'hoja_ruta' => $hoja_ulti
         ]);

         $this->turno = 'Elegir';
          $this->selected_id = '';
          $this->fecha = Carbon::now()->format('d-m-Y');

          $this->RenderFactura($ventaId);


          $this->emit('hr-added', 'Hoja de ruta registrada y agregado el pedido.');

          $this->emit('modal-hr-hide', '');




       }


           public function getDetails3($saleId)
           {
             $this->id_pedido = $saleId;

               $this->emit('show-modal3','details loaded');

               $this->RenderFactura($saleId);
           }


           public function Update2($estado_id)
           {

             $pedido = Sale::find($this->id_pedido);

             $pedido->update([
               'status' => $estado_id
             ]);



             if($estado_id == 4)
             {
               $items = SaleDetail::where('sale_details.sale_id',$this->id_pedido)->get();

                 foreach ($items as  $item) {
                   //update stock
                   $product = Product::find($item->product_id);
                   $product->stock = $product->stock + $item->quantity;
                   $product->save();
                 }


             }
              $this->RenderFactura($this->id_pedido);

             $this->emit('hide-modal3','details loaded');


           }

           public function CerrarFactura() {
              $this->emit('cerrar-facruta','details loaded');
            }



}
