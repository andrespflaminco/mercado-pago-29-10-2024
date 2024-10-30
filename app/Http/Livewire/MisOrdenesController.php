<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;


use App\Models\ecommerce_mp_pago;

use Illuminate\Support\Facades\Http;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\ClientesMostrador;
use App\Models\provincias;
use App\Models\Category;
use App\Models\User;
use App\Models\SaleDetail;
use App\Models\Sale;
use Illuminate\Http\Request;
use App\Models\ecommerce_envio;
use App\Models\ecommerce;
use App\Models\datos_facturacion;
use App\Services\CartEcommerce;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\Session;
use App\Models\metodo_pago;
use App\Models\cajas;
use App\Models\historico_stock;
use App\Models\bancos;
use DB;


class MisOrdenesController extends Component
{

    use WithPagination;

    public $products, $comercio_id, $search, $name, $barcode, $price, $stock, $image, $categories_menu, $selected_id, $cost, $cantidad, $slug, $nombre_destinatario, $dni, $ciudad, $pais, $provincia, $direccion, $depto, $codigo_postal, $metodo_entrega, $ecommerce, $metodo_pago_nuevo, $departamento, $observaciones, $tk, $metodo_pago, $caja, $descuento, $data_ecommerce, $item, $cart, $preference;

  	private $pagination = 25;

    public function paginationView()
    {
    return 'vendor.livewire.bootstrap';
    }

    public function mount($slug) {
      $this->cantidad = 1;
      $this->slug = $slug;
      $this->data_ecommerce = [];
      $this->ecommerce = ecommerce::where('slug',$slug)->first();

      $this->descuento = session('descuento');

    }

    public function resetUI() {
      $this->cantidad = 1;
      $this->selected_id = "";
      $this->name = "";
      $this->barcode = "";
      $this->stock = "";
      $this->price = "";
      $this->cost = "";
      $this->image = "";
    }

    protected $listeners =[
      'SaveSale' => 'SaveSale'
    ];

    public function render()
    {



      $this->data = User::find($this->ecommerce->comercio_id);

      $this->datos_retiro = User::leftjoin('datos_facturacions','datos_facturacions.comercio_id','users.id')->where('users.id', $this->ecommerce->comercio_id)->first();

      $this->orders = Sale::join('metodo_pagos', 'metodo_pagos.id', 'sales.metodo_pago')
      ->select('sales.*','metodo_pagos.nombre as metodo_pago')
      ->where('sales.cliente_id',Auth::user()->cliente_id)
      ->where('sales.canal_venta','Ecommerce')->get();
      
     $this->categories_menu = Category::orderBy('name','asc')
    ->where('comercio_id', 'like', $this->ecommerce->comercio_id)
    ->get();

      return view('livewire.mis-ordenes.component', [
        'orders' => $this->orders,
        'imagen' => $this->data->image,
        'data_e' => $this->data,
        'categories_menu' => $this->categories_menu,
        'datos_retiro' => $this->datos_retiro,
        'categories' => Category::orderBy('name','asc')->where('comercio_id', 'like', $this->ecommerce->comercio_id)->get(),
        'ecommerce' => ecommerce::join('bancos','bancos.id','ecommerces.banco_id')->select('ecommerces.*','bancos.id','bancos.nombre as nombre_banco','bancos.CBU','bancos.cuit')->where('ecommerces.comercio_id', $this->ecommerce->comercio_id )->first(),
        'banco' => ecommerce::join('bancos','bancos.id','ecommerces.banco_id')->select('ecommerces.*','bancos.id','bancos.nombre as nombre_banco','bancos.CBU','bancos.cuit')->where('ecommerces.comercio_id', $this->ecommerce->comercio_id )->get()
      ])
      ->extends('layouts.theme-ecommerce.app')
      ->section('content');

    }

    public function MetodoPago()
    {
    $this->metodo_pago_nuevo = $this->metodo_pago;
    }

    // guardar venta
    public function SaveSale()
    {

      if($this->metodo_pago == "Elegir")
      {
        $this->emit('sale-error','DEBE ELEGIR LA FORMA DE PAGO');
        return;
      }

      if($this->metodo_entrega == "Elegir")
      {
        $this->emit('sale-error','DEBE ELEGIR LA FORMA DE ENVIO');
        return;
      }

      $this->caja = cajas::where('estado',0)->where('comercio_id',$this->ecommerce->comercio_id)->max('id');

      $this->cliente = ClientesMostrador::find(Auth::user()->cliente_id);

      $cart = new CartEcommerce;

      DB::beginTransaction();

      try {

        $this->descuento_ratio = session('descuento');
        $this->total = $cart->totalAmount();
        $this->itemsQuantity = $cart->totalCantidad();
        $this->descuento = $this->descuento_ratio * $this->total;


        $sale = DB::table('sales')->insertGetId([
          'total' => $this->total,
          'recargo' => 0,
          'descuento' =>   $this->descuento,
          'items' => $this->itemsQuantity,
          'tipo_comprobante'  => 'CF',
          'cash' => 0,
          'change' => 0,
          'iva' => 0,
          'metodo_pago'  => $this->metodo_pago,
          'comercio_id' =>  $this->ecommerce->comercio_id,
          'cliente_id' => Auth::user()->cliente_id,
          'user_id' =>  $this->ecommerce->comercio_id,
          'observaciones' => $this->observaciones,
          'canal_venta' => 'Ecommerce',
          'estado_pago' => 'Pendiente',
          'caja' => $this->caja,
          'deuda' => $this->total,
          'status' => 'Pendiente',
          'nota_interna' => ''
        ]);

        if($sale)
        {

            $items = $cart->getContent();

            if($this->metodo_entrega == 2)
            {
              ecommerce_envio::create([
                'nombre_destinatario' => $this->nombre_destinatario,
                'dni' => $this->dni,
                'sale_id' => $sale,
                'comercio_id' => $this->ecommerce->comercio_id,
                'ciudad' => $this->ciudad,
                'direccion' => $this->direccion,
                'depto' => $this->departamento,
                'provincia' => $this->provincia,
                'pais' => $this->pais,
                'codigo_postal' => $this->codigo_postal
              ]);

            }


          foreach ($items as  $item) {
            SaleDetail::create([
              'product_id' => $item['id'],
              'price' => $item['price'],
              'product_name' => $item['name'],
              'product_barcode' => $item['barcode'],
              'quantity' => $item['qty'],
              'seccionalmacen_id' => $item['seccion_almacen'],
              'sale_id' => $sale,
              'cliente_id' => Auth::user()->cliente_id,
              'comercio_id' => $this->ecommerce->comercio_id,
              'metodo_pago'  => $this->metodo_pago,
              'seccionalmacen_id' => $item['seccion_almacen'],
              'caja' => $this->caja,
              'canal_venta' => 'Ecommerce'
            ]);

            //update stock
            $product = Product::find($item['id']);
            $product->stock = $product->stock - $item['qty'];
            $product->save();

            $historico_stock = historico_stock::create([
              'tipo_movimiento' => 10,
              'producto_id' => $item['id'],
              'cantidad_movimiento' => $item['qty'],
              'stock' => $product->stock,
              'comercio_id'  =>  $this->ecommerce->comercio_id,
              'usuario_id'  =>  $this->ecommerce->comercio_id
            ]);


          }

        }


        DB::commit();

        $this->pago = 0;
        $this->observaciones = '';
        $this->monto = 0;
        $this->metodo_pago_elegido = 'Elegir';
        session(['descuento' => 0]);

        $cart->clear();
        return \Redirect::to("ecommerce-email/pdf/" . $sale . "/"  .$this->cliente->email ."/".  $this->ecommerce->slug  );



      } catch (Exception $e) {
        DB::rollback();
        $this->emit('sale-error', $e->getMessage());
      }

    }




    // guardar venta
    public function pay(Request $request, $slug)
    {

      $cart = new CartEcommerce;

      $items = $cart->getContent();



      $this->ecommerce = ecommerce::where('slug',$slug)->first();

      $tk = $this->ecommerce->mp_token;

      $payment_id = $request->get('payment_id');

      $response = Http::get("https://api.mercadopago.com/v1/payments/$payment_id" . "?access_token=$tk");

      $response = json_decode($response);

      $status = $response->status;


      ////// SI LA RESPUESTA ESTA APROBADA //////////


      if($status == "approved") {

        $this->caja = cajas::where('estado',0)->where('comercio_id',$this->ecommerce->comercio_id)->max('id');

        $this->cliente = ClientesMostrador::find(Auth::user()->cliente_id);

        $cart = new CartEcommerce;

        DB::beginTransaction();

        try {

          $this->descuento_ratio = session('descuento');
          $this->total = $cart->totalAmount();
          $this->itemsQuantity = $cart->totalCantidad();
          $this->descuento = $this->descuento_ratio * $this->total;
          $this->total_sin_descuento = $this->total - $this->descuento;


          $sale = DB::table('sales')->insertGetId([
            'total' => $this->total,
            'recargo' => 0,
            'descuento' =>   $this->descuento,
            'items' => $this->itemsQuantity,
            'tipo_comprobante'  => 'CF',
            'cash' => 0,
            'change' => 0,
            'iva' => 0,
            'metodo_pago'  => 3,
            'comercio_id' =>  $this->ecommerce->comercio_id,
            'cliente_id' => Auth::user()->cliente_id,
            'user_id' =>  $this->ecommerce->comercio_id,
            'observaciones' => $this->observaciones,
            'canal_venta' => 'Ecommerce',
            'estado_pago' => 'Pendiente',
            'caja' => $this->caja,
            'deuda' => 0,
            'status' => 'Pendiente',
            'nota_interna' => ''
          ]);

          if($sale)
          {

            $mp = ecommerce_mp_pago::create([
              'sale_id' => $sale,
              'comercio_id' =>  $this->ecommerce->comercio_id,
              'cliente_id' => Auth::user()->cliente_id,
              'status' => $response->status,
              'mp_id' => $response->id,
              'payment_method_id' => $response->payment_method_id,
              'payer_id' => $response->payer->id
            ]);


            $pagos = DB::table('pagos_facturas')->insert([
              'monto' => $this->total_sin_descuento,
              'cambio' => 0,
              'recargo' => 0,
              'id_factura' => $sale,
              'comercio_id' => $this->ecommerce->comercio_id,
              'caja' => $this->caja,
              'metodo_pago'  => 3,
              'eliminado' => 0
            ]);


              if($this->metodo_entrega == 2)
              {
                ecommerce_envio::create([
                  'nombre_destinatario' => $this->nombre_destinatario,
                  'dni' => $this->dni,
                  'sale_id' => $sale,
                  'comercio_id' => $this->ecommerce->comercio_id,
                  'ciudad' => $this->ciudad,
                  'direccion' => $this->direccion,
                  'depto' => $this->departamento,
                  'provincia' => $this->provincia,
                  'pais' => $this->pais,
                  'codigo_postal' => $this->codigo_postal
                ]);

              }


            //////// GUARDA EL DETALLE DEL PEDIDO /////////


            $cart = new CartEcommerce;
            $items = $cart->getContent();

            foreach ($items as  $item) {
              SaleDetail::create([
                'product_id' => $item['id'],
                'price' => $item['price'],
                'product_name' => $item['name'],
                'product_barcode' => $item['barcode'],
                'quantity' => $item['qty'],
                'sale_id' => $sale,
                'cliente_id' => Auth::user()->cliente_id,
                'comercio_id' => $this->ecommerce->comercio_id,
    						'metodo_pago'  => $this->metodo_pago,
    						'seccionalmacen_id' => $item['seccion_almacen'],
    						'caja' => $this->caja,
    						'canal_venta' => 'Ecommerce'
              ]);

              //update stock
              $product = Product::find($item['id']);
              $product->stock = $product->stock - $item['qty'];
              $product->save();

              $historico_stock = historico_stock::create([
                'tipo_movimiento' => 10,
                'producto_id' => $item['id'],
                'cantidad_movimiento' => $item['qty'],
                'stock' => $product->stock,
                'comercio_id'  =>  $this->ecommerce->comercio_id,
                'usuario_id'  =>  $this->ecommerce->comercio_id
              ]);


            }

          }


          DB::commit();

          $this->pago = 0;
          $this->observaciones = '';
          $this->monto = 0;
          $this->metodo_pago_elegido = 'Elegir';
          session(['descuento' => 0]);

          $cart->clear();
          return \Redirect::to("ecommerce-email/pdf/" . $sale . "/"  .$this->cliente->email ."/".  $this->ecommerce->slug  );



        } catch (Exception $e) {
          DB::rollback();
          $this->emit('sale-error', $e->getMessage());
        }


        }


    }



public function RenderFactura($id) {
    dd($id);
}

}
