<?php

namespace App\Http\Livewire;

use App\Services\Cart;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Session;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\proveedores;
use App\Models\Category;
use App\Models\saldos_iniciales;
use App\Models\metodo_pago;
use App\Models\seccionalmacen;
use App\Models\cajas;
use App\Models\historico_stock;
use App\Models\pagos_facturas;
use App\Models\Product;

// 14-8-2024
use App\Models\gastos;

////////////////////////
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\hoja_ruta;
use App\Models\User;

//////////////////////
use Carbon\Carbon;
use App\Models\bancos;
use App\Models\compras_proveedores;
use App\Models\detalle_compra_proveedores;
use DB;

// Trait
use App\Traits\BancosTrait;

class CtaCteProveedoresMovimientosController extends Component
{
  use WithPagination;
  use WithFileUploads;
  use BancosTrait;

  public $origen_accion,$name,$barcode,$cost,$price,$pago, $total_total,$caja_seleccionada,$caja_ver,$compra_id, $estado_pago, $caja, $proveedor_elegido, $stock,$alerts,$categoryid, $codigo, $monto_total, $search, $image, $selected_id, $pageTitle,$componentName,$comercio_id,$data_Cart, $dateFrom, $dateTo, $Cart, $deuda,$metodo_pago_elegido, $product,$total, $itemsQuantity, $cantidad, $carrito, $qty, $detalle_cliente, $detalle_facturacion, $ventaId, $style, $style2, $pagos2, $estado2, $estado, $listado_hojas_ruta, $suma_monto, $suma_cash, $suma_deuda, $rec, $tot, $usuario,$tipo_pago,$monto_ap, $recargo_total,$total_pago,$NroVenta, $id_pago, $tipos_pago, $detalle_venta, $detalle_compra, $dci, $details, $saleId, $countDetails, $proveedor_id, $sumDetails, $formato_modal, $id_compra_modal, $id_pago_modal , $metodo_pago_agregar_pago, $fecha_ap, $fecha_editar, $detalle_proveedor, $sum_pago;
  private $pagination = 25;
  
  public $Nro_Compra, $filtro_operacion,$nombre_etiqueta_seleccionada,$porcentaje_descuento, $id_compra;
  public $comprobante, $nro_comprobante,$datos_proveedor;
  public $pagar_deuda = [];
  
  public $monto_saldo,$metodo_pago_saldo,$modo_ver_saldo,$selected_pago_saldo_id;
  
  public $monto_ver,$nombre_banco_ver,$nro_comprobante_ver,$comprobante_ver;


public $mostrarInputFile = false;

    public function toggleInputFile()
    {
        $this->mostrarInputFile = !$this->mostrarInputFile;
    }

  public function mount($id)
  {
    $this->filtro_operacion = 0;
    $this->id_compra = 0;
    $this->id_pago = 0;
    $this->caja = cajas::select('*')->where('estado',0)->where('user_id',Auth::user()->id)->max('id');
    
    $fecha_editar = Carbon::now()->format('d-m-Y');
    $this->fecha_ap = Carbon::now();
    $this->tipos_pago = [];
    $this->detalle_compra = [];
    $this->pagos2 = [];
    $this->detalle_proveedor = [];
    $this->dci = [];
    $this->pagar_deuda = [];
    $this->total = [];
    $this->details =[];
    $this->pageTitle = 'Listado';
    $this->cantidad = 1;
    $this->monto_ap = 0;
    $this->tipo_pago = 1;
    $this->metodo_pago_agregar_pago = 1;
    $this->componentName = 'Productos';
    $this->metodo_pago_elegido = 'Elegir';
    $this->categoryid = 'Elegir';
    $this->dateFrom = Carbon::parse('2000-01-01 00:00:00')->format('d-m-Y');
    $this->dateTo = Carbon::now()->format('d-m-Y');

    $this->proveedor_id = $id;
  }

    protected function redirectLogin()
    {
        return \Redirect::to("login");
    }

    
    
	public function render()
	{
	   
        if (!Auth::check()) {
            // Redirigir al inicio de sesión y retornar una vista vacía
            $this->redirectLogin();
            return view('auth.login');
        }

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    
    $this->comercio_id = $comercio_id;
    //$this->OrdenarNroPagos($comercio_id);
    
    $this->SetFechas();
    
    $this->caja_seleccionada = cajas::find($this->caja);
    
    $this->ultimas_cajas = cajas::where('comercio_id', $comercio_id)->where('eliminado',0)->orderby('created_at','desc')->limit(5)->get();
    
    /*
    $this->tipos_pago = bancos::where('bancos.comercio_id', 'like', $comercio_id)
    ->orderBy('bancos.nombre','asc')->get();
    */
    $this->tipos_pago = $this->getBancos($comercio_id);
    
    $this->datos_proveedor = proveedores::find($this->proveedor_id);
    
    $compras_proveedores = $this->GetData($comercio_id);
    
    /*
    $pagos = pagos_facturas::join('bancos','bancos.id','pagos_facturas.banco_id')
        ->leftjoin('compras_proveedores', 'compras_proveedores.id', 'pagos_facturas.id_compra')
        ->whereBetween('pagos_facturas.created_at', [$this->from, $this->to])
        ->where('pagos_facturas.comercio_id', $comercio_id)
        ->where('pagos_facturas.tipo_pago', 1)
        ->where('pagos_facturas.eliminado',0)
        ->where('pagos_facturas.proveedor_id', $this->proveedor_id)
        ->select('bancos.nombre as nombre_banco','bancos.id as id_banco','pagos_facturas.url_comprobante as url_pago',Sale::raw('0 as id_saldo'), Sale::raw('0 as monto_saldo'),'compras_proveedores.nro_compra', 'pagos_facturas.id as id_pago', pagos_facturas::raw('0 as id_compra'), 'pagos_facturas.created_at', pagos_facturas::raw('0 as monto_compra'), 'pagos_facturas.monto_compra as monto_pago');
    
    $compras = compras_proveedores::where('compras_proveedores.comercio_id', 'like', $comercio_id)
        ->whereBetween('compras_proveedores.created_at', [$this->from, $this->to])
        ->where('compras_proveedores.proveedor_id', $this->proveedor_id)
        ->where('compras_proveedores.eliminado',0)
        ->select(compras_proveedores::raw('"-" as nombre_banco'),compras_proveedores::raw('0 as id_banco'),compras_proveedores::raw('0 as url_pago'),compras_proveedores::raw('0 as id_saldo'), compras_proveedores::raw('0 as monto_saldo'),'compras_proveedores.nro_compra', compras_proveedores::raw('0 as id_pago'),'compras_proveedores.id as id_compra', 'compras_proveedores.created_at', 'compras_proveedores.total as monto_compra', compras_proveedores::raw('0 as monto_pago'));
    
    $saldos_iniciales = saldos_iniciales::join('bancos','bancos.id','saldos_iniciales.metodo_pago')
        ->where('saldos_iniciales.comercio_id', 'like', $comercio_id)
        ->whereBetween('saldos_iniciales.created_at', [$this->from, $this->to])
        ->where('saldos_iniciales.referencia_id', $this->proveedor_id)
        ->where('saldos_iniciales.tipo', 'proveedor')
        ->where('saldos_iniciales.eliminado',0)
        ->select('bancos.nombre as nombre_banco','bancos.id as id_banco',saldos_iniciales::raw('0 as url_pago'),'saldos_iniciales.id as id_saldo','saldos_iniciales.monto as monto_saldo',saldos_iniciales::raw('0 as nro_compra'),saldos_iniciales::raw('0 as id_pago'), saldos_iniciales::raw('0 as id_compra'), 'saldos_iniciales.created_at', compras_proveedores::raw('0 as monto_compra'), compras_proveedores::raw('0 as monto_pago'));
    
    // Unión de las subconsultas
    $union = $pagos->union($compras)->union($saldos_iniciales);
    
    // Obtener el resultado ordenado
    $compras_proveedores = $union->orderBy('created_at', 'desc')->get();
    
    */
    
    // Filtrar los resultados después de obtenerlos
    if ($this->filtro_operacion == "1") {
        $compras_proveedores = $compras_proveedores->filter(function ($item) {
            return $item->id_compra > 0;
        });
    } elseif ($this->filtro_operacion == "2") {
        $compras_proveedores = $compras_proveedores->filter(function ($item) {
            return $item->id_pago > 0;
        });
    }

    //dd($compras_proveedores);
    
    $compras_proveedores_totales = compras_proveedores::select( compras_proveedores::raw('SUM(compras_proveedores.total) as total'),compras_proveedores::raw('COUNT(compras_proveedores.id) as count_proveedores'), Sale::raw('SUM(compras_proveedores.deuda) as deuda')    )
      ->join('proveedores','proveedores.id','compras_proveedores.proveedor_id')
      ->where('compras_proveedores.comercio_id', 'like', $comercio_id)
      ->where('compras_proveedores.proveedor_id', $this->proveedor_id)
      ->whereBetween('compras_proveedores.created_at', [$this->from, $this->to]);

      if($this->proveedor_elegido) {
        $compras_proveedores_totales = $compras_proveedores_totales->where('proveedores.id',$this->proveedor_elegido);
      }

      if($this->search) {
        $compras_proveedores_totales = $compras_proveedores_totales->where('compras_proveedores.id', 'like', '%' . $this->search . '%');
      }


      $compras_proveedores_totales = $compras_proveedores_totales->first();


       $this->suma_totales = $compras_proveedores_totales->total;
       $this->suma_proveedores = $compras_proveedores_totales->count_proveedores;
       $this->suma_deuda = $compras_proveedores_totales->deuda;

      $metodo_pagos = $this->GetBancosTrait($comercio_id);
      
      //$metodo_pagos = bancos::where('bancos.comercio_id', 'like', $comercio_id)->get();

    return view('livewire.ctacte-proveedores-movimientos.component',[
      'data' => $compras_proveedores,
      'detalle_compra' => $this->detalle_compra,
      'metodo_pago' => $metodo_pagos,
      'categories' => Category::orderBy('name','asc')->where('comercio_id', 'like', $comercio_id)->get(),
      'almacenes' => seccionalmacen::orderBy('nombre','asc')->where('comercio_id', 'like', $comercio_id)->get(),
      'prov' => proveedores::orderBy('nombre','asc')->where('comercio_id', 'like', $comercio_id)->get()
    ])
    ->extends('layouts.theme-pos.app')
    ->section('content');
  }




  public function AgregarPagoModal($id_proveedor) {

      $this->emit('agregar-pago', $id_proveedor);
  }


  public function ver($id_compra , $id_pago)
  {

    $this->id_pago_modal = $id_pago;
    $this->id_compra_modal = $id_compra;

    if($this->id_compra_modal > 0) {

    $this->compras_modal = compras_proveedores::find($this->id_compra_modal);

    $this->monto_modal = $this->compras_modal->total;
    $this->iva_modal = $this->compras_modal->iva;
    $this->id_modal = $this->compras_modal->id;
    $this->recargo_modal = $this->compras_modal->recargo;
    $this->created_at_modal = $this->compras_modal->created_at;

    }

    if($this->id_pago_modal > 0) {

    $this->pagos_modal = pagos_facturas::join('metodo_pagos','metodo_pagos.id','pagos_facturas.metodo_pago')
    ->join('bancos','bancos.id','pagos_facturas.tipo_pago')
    ->where('pagos_facturas.id', $this->id_pago_modal)
    ->select('pagos_facturas.*','metodo_pagos.nombre as metodo_pago','bancos.nombre as banco')
    ->first();



        $this->monto_modal = $this->pagos_modal->monto_compra;
        $this->id_modal = $this->pagos_modal->id;
        $this->metodo_pago_modal = $this->pagos_modal->metodo_pago;
        $this->banco_modal = $this->pagos_modal->banco;
        $this->created_at_modal = $this->pagos_modal->created_at;

    }


  $this->emit('show-modal', '');

  }


  public function Agregar() {

    $cart = new Cart;
    $items = $cart->getContent();


 if ($items->contains('id', $this->selected_id)) {

   $cart = new Cart;
   $items = $cart->getContent();

   $product = Product::find($this->selected_id);

   foreach ($items as $i)
{
       if($i['id'] === $product['id']) {

         $cart->removeProduct($i['id']);

         $product = array(
             "id" => $i['id'],
             "name" => $i['name'],
             "price" => $i['price'],
             "cost" => $i['cost'],
             "qty" => $i['qty']+1,
         );

         $cart->addProduct($product);

     }
}

    $this->resetUI();

    $this->emit('product-added','Producto agregado');

   return back();

} else {

      $cart = new Cart;

      $product = array(
          "id" => $this->selected_id,
          "barcode" => $this->barcode,
          "name" => $this->name,
          "price" => $this->price,
          "cost" => $this->cost,
          "qty" => $this->cantidad,
      );

      $cart->addProduct($product);

      $this->resetUI();

      $this->emit('product-added','Producto agregado');

  }

}

    public function removeProductFromCart(Product $product) {
        $cart = new Cart;
        $cart->removeProduct($product->id);
        session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
        return back();
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
                "price" => $i['price'],
                "cost" => $i['cost'],
                "qty" => $i['qty']+1,
            );

            $cart->addProduct($product);

        }
   }

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
                "name" => $i['name'],
                "price" => $i['price'],
                "cost" => $i['cost'],
                "qty" => $i['qty']-1,
            );

            $cart->addProduct($product);

        }
   }

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }

  // escuchar eventos
  protected $listeners = [
    'scan-code'  =>  'BuscarCode',
    'deletePago' => 'DeletePago',
    'deletePagoSaldo' => 'DeletePagoSaldo',
    'FechaElegida' => 'FechaElegida'
  ];

    public function FechaElegida($startDate, $endDate)
    {
      // Manejar las fechas seleccionadas aquí
      $this->dateFrom  = $startDate;
      $this->dateTo = $endDate;;
    }

  public function SetFechas() {
  if($this->dateFrom !== '' || $this->dateTo !== '')
  {
    $this->from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
    $this->to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';

  }
  
}

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

  public function Edit2($monto_total)
	{


    $this->monto_total = $monto_total;

		$this->emit('show-modal2','Show modal!');
	}

  public function MontoPago()
  {


    $this->deuda = $this->monto_total - $this->pago;

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
    $this->selected_id = 0;

  }


  public function CrearPagoACuenta()
  {
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $this->pendientes = compras_proveedores::whereRaw('compras_proveedores.deuda > 0')
    ->where('proveedor_id',$this->proveedor_id)
    ->where('comercio_id',$comercio_id)->orderBy('compras_proveedores.created_at','asc')->get();


    $sum_pago = 0;
    $i = 0;

    foreach ($this->pendientes as $p) {

        $i = $i++;

        if($i == 1) {

          /* SI ES EL PRIMER PAGO */
        $this->saldo_primero_pago = $this->monto_ap - $p->deuda;

        if($this->saldo_primero_pago < 0) {

        $pago_factura =   pagos_facturas::create([
             'monto_compra' => $this->monto_ap,
             'caja' => $this->caja,
             'metodo_pago' => $this->metodo_pago_agregar_pago,
             'recargo' => $this->recargo_total,
             'created_at' => $this->fecha_ap,
             'comercio_id' => $p->comercio_id,
             'proveedor_id' => $this->proveedor_id,
             'id_compra' => $p->id,
             'tipo_pago' => 2,
             'eliminado' => 0
           ]);

         } else {

           $pago_factura =   pagos_facturas::create([
                'monto_compra' => $p->deuda,
                'caja' => $this->caja,
                'metodo_pago' => $this->metodo_pago_agregar_pago,
                'recargo' => $this->recargo_total,
                'created_at' => $this->fecha_ap,
                'proveedor_id' => $this->proveedor_id,
                'comercio_id' => $p->comercio_id,
                'id_compra' => $p->id,
                'tipo_pago' => 2,
                'eliminado' => 0
              ]);

            }


            } else {
              /* NO ES EL PRIMER PAGO */

              $this->saldo_sum_pago = $this->monto_ap - $sum_pago;

            if($this->saldo_sum_pago != 0 ) {

            if( $p->deuda > $this->saldo_sum_pago ) {

                $pago_factura =   pagos_facturas::create([
                     'monto_compra' => $this->saldo_sum_pago,
                     'caja' => $this->caja,
                     'metodo_pago' => $this->metodo_pago_agregar_pago,
                     'recargo' => $this->recargo_total,
                     'created_at' => $this->fecha_ap,
                     'comercio_id' => $p->comercio_id,
                     'proveedor_id' => $this->proveedor_id,
                     'id_compra' => $p->id,
                     'tipo_pago' => 2,
                     'eliminado' => 0
                   ]);

              } else {

                $pago_factura =   pagos_facturas::create([
                     'monto_compra' => $p->deuda,
                     'caja' => $this->caja,
                     'metodo_pago' => $this->metodo_pago_agregar_pago,
                     'recargo' => $this->recargo_total,
                     'created_at' => $this->fecha_ap,
                     'proveedor_id' => $this->proveedor_id,
                     'comercio_id' => $p->comercio_id,
                     'id_compra' => $p->id,
                     'tipo_pago' => 2,
                     'eliminado' => 0
                   ]);

              }

              }

            }

           $sum_pago += $pago_factura->monto_compra;

           $this->ActualizarEstadoDeuda($p->id);

      }

      $pago_factura =   pagos_facturas::create([
           'monto_compra' => $this->monto_ap,
           'caja' => $this->caja,
           'metodo_pago' => $this->metodo_pago_agregar_pago,
           'recargo' => $this->recargo_total,
           'created_at' => $this->fecha_ap,
           'proveedor_id' => $this->proveedor_id,
           'comercio_id' => $p->comercio_id,
           'id_compra' => 0,
           'tipo_pago' => 1,
           'eliminado' => 0
         ]);

    $this->monto_ap = 0;
    $this->emit('agregar-pago-hide', 'PAGO REGISTRADO.');

  }





  ////////////// FACTURA ///////////////


public function MostrarPagos() {
  $this->estado = "display: block;";
  $this->estado2 = "display: none;";
}


     function AgregarPago($id_compra,$origen) {
       $this->origen_accion = $origen;
       $this->compra_id = $id_compra;
       $this->emit('agregar-pago','');
       $this->emit('hide-ver-pagos','');

     }

    public function CerrarAgregarPago(){
       $this->emit('agregar-pago-hide','');
       $this->emit('show-ver-pagos','');
    }

    public function CerrarVerPago(){
       $this->emit('ver-pago-hide','');
    }



public function EditPago($id_pago,$origen) {

       $this->origen_accion = $origen;
       
       $this->emit('hide-ver-pagos','details loaded');
       $this->emit('agregar-pago','details loaded');

       $this->formato_modal = 1;

       $this->id_pago = $id_pago;

       $pagos = pagos_facturas::find($id_pago);
        
       // Si es compra  
       if(0 < $pagos->id_compra || $pagos->id_compra != null){
       $this->monto_ap = $pagos->monto_compra;    
       }
     
       // Si es gasto
       if(0 < $pagos->id_gasto || $pagos->id_gasto != null){
       $this->monto_ap = $pagos->monto_gasto;   
       }
       $this->caja = $pagos->caja;

       $this->metodo_pago_agregar_pago = $pagos->banco_id;
       
       $this->tipo_pago = $this->metodo_pago_agregar_pago;

       
       $this->nro_comprobante = $pagos->nro_comprobante;
       $this->comprobante = $pagos->url_comprobante;
      
       $this->fecha_ap = Carbon::parse($pagos->created_at)->format('d-m-Y');

       $this->total_pago = $this->monto_ap;
       
       $this->render();

     }

public function ResetPago() {
  $this->metodo_pago_agregar_pago = 1;
  $this->monto_ap = 0;
  $this->formato_modal = 0;
  $this->recargo = 0;
  $this->tipo_pago = 1;
  $this->recargo_total = 0;
  $this->total_pago = 0;
  $this->recargo_mp = 0;
  $this->metodo_pago_ap = 1;
  $this->fecha_ap = Carbon::now()->format('d-m-Y');
}

public function ActualizarPago($id_pago) {

  $pagos = pagos_facturas::find($id_pago);

  $compra_id = $pagos->id_compra;
  $gasto_id = $pagos->id_gasto;

  $this->recargo_viejo_actualizar_pago = $pagos->recargo;

  if($this->metodo_pago_agregar_pago == 1) {
  $mp = 1;
  } else {
  $mp = 0;
  }
  
  // Si es compra
  if(0 < $pagos->id_compra || $pagos->id_compra != null){
      $pagos->update([
        'monto_compra' => $this->monto_ap,
        'caja' => $this->caja,
        'created_at' => $this->fecha_ap,
        'nro_comprobante' => $this->nro_comprobante,
        'banco_id' => $this->metodo_pago_agregar_pago,
        'metodo_pago' => $mp
    
      ]);      
  }
   
  // Si es gasto
  if(0 < $pagos->id_gasto || $pagos->id_gasto != null){
      $pagos->update([
        'monto_gasto' => $this->monto_ap,
        'caja' => $this->caja,
        'created_at' => $this->fecha_ap,
        'nro_comprobante' => $this->nro_comprobante,
        'banco_id' => $this->metodo_pago_agregar_pago,
        'metodo_pago' => $mp
      ]);      
  }


   if($this->comprobante != $pagos->url_comprobante)
	{
		$customFileName = uniqid() . '_.' . $this->comprobante->extension();
		$this->comprobante->storeAs('public/comprobantes', $customFileName);
		$pagos->url_comprobante = $customFileName;
		$pagos->save();
	}
	
    $this->emit('agregar-pago-hide', 'hide');

  $this->emit('pago-actualizado', 'El pago fue actualizado.');

  // Si es compra
  if(0 < $pagos->id_compra || $pagos->id_compra != null){
  $this->ActualizarEstadoDeuda($compra_id); 
  }
   
  // Si es gasto
  if(0 < $pagos->id_gasto || $pagos->id_gasto != null){
  $this->ActualizarEstadoDeudaGasto($gasto_id); 
  }
  
  if($this->origen_accion == 2){
    $this->VerPagos($compra_id);    
  }
  

}

//// ELIMINAR UN PAGO ///

public function DeletePago($datos)
{
    
    $id = $datos[0];
    $origen = $datos[1];
    
    $this->pago_viejo = pagos_facturas::find($id);

    $compra_id = $this->pago_viejo->id_compra;
    $gasto_id = $this->pago_viejo->id_gasto;

    $this->pago_viejo->eliminado = 1;
    $this->pago_viejo->save();


    $this->emit('pago-eliminado', 'El pago fue eliminado.');
    
    $pagos = pagos_facturas::find($id);
    
    // Si es compra
    if(0 < $pagos->id_compra || $pagos->id_compra != null){
      $this->ActualizarEstadoDeuda($compra_id); 
    }
       
    // Si es gasto
    if(0 < $pagos->id_gasto || $pagos->id_gasto != null){
      $this->ActualizarEstadoDeudaGasto($gasto_id); 
    }

    if($origen == 2){
    $this->VerPagos($compra_id);
    } 
}


public function RenderCompra($CompraId)
{

        $this->id_compra = $CompraId;

       //////////////// PAGOS //////////////
             $this->pagos2 = pagos_facturas::join('metodo_pagos as mp','mp.id','pagos_facturas.metodo_pago')
             ->leftjoin('cajas','cajas.id','pagos_facturas.caja')
             ->select('mp.nombre as metodo_pago','pagos_facturas.id','cajas.nro_caja','pagos_facturas.monto_compra','pagos_facturas.created_at as fecha_pago')
             ->where('pagos_facturas.id_compra', $CompraId)
             ->where('pagos_facturas.eliminado',0)
             ->get();
             $this->suma_monto = $this->pagos2->sum('monto_compra');
             
             //dd($this->pagos2);
             
             $this->detalle_proveedor = proveedores::join('compras_proveedores','compras_proveedores.proveedor_id','proveedores.id')
             ->select('proveedores.*')
             ->where('compras_proveedores.id', $CompraId)
             ->get();

             $this->estado = "display: none;";
             $this->estado2 = "display: none;";

  /////////////// DETALLE DE VENTA /////////////////////7
         $this->dci = detalle_compra_proveedores::where('detalle_compra_proveedores.compra_id', $CompraId)->get();

         $this->total = compras_proveedores::where('compras_proveedores.id', $CompraId)->get();

        foreach($this->total as $t){
            $this->Nro_Compra = $t->nro_compra;
            $this->fecha_compra = Carbon::parse($t->created_at)->format('Y-m-d');
        }


          $this->emit('modal-show','Show modal');
     }

     public function CambioCaja() {


       $this->tipo_click = 1;

       if(Auth::user()->comercio_id != 1)
       $comercio_id = Auth::user()->comercio_id;
       else
       $comercio_id = Auth::user()->id;


       $this->fecha_pedido_desde = $this->fecha_ap.' 00:00:00';

       $this->fecha_pedido_hasta = $this->fecha_ap.' 23:59:50';

       $this->emit('modal-estado','details loaded');

       $this->lista_cajas_dia = cajas::where('comercio_id', $comercio_id)->whereBetween('fecha_inicio',[$this->fecha_pedido_desde, $this->fecha_pedido_hasta])->get();


     }


     public function ActualizarEstadoDeudaProveedores($ventaId)
     {
       /////////////////   ACTUALIZAR ESTADO DE PAGO   /////////////////////


            $this->data_total = compras_proveedores::select('compras_proveedores.total','compras_proveedores.deuda')
            ->where('compras_proveedores.id', $ventaId)
            ->get();

            $this->pagos2 = pagos_facturas::join('metodo_pagos as mp','mp.id','pagos_facturas.metodo_pago')
            ->select('mp.nombre as metodo_pago','pagos_facturas.id_compra','pagos_facturas.monto_compra','pagos_facturas.created_at as fecha_pago')
            ->where('pagos_facturas.id_compra', $ventaId)
            ->where('pagos_facturas.eliminado',0)
            ->get();


            $this->suma_monto = $this->pagos2->sum('monto_compra');

            $this->tot = $this->data_total->sum('total');


            $deuda = $this->tot - $this->suma_monto;



           $this->deuda_vieja = compras_proveedores::find($ventaId);

            $this->deuda_vieja->update([
              'deuda' => $deuda
              ]);


            ///////////////////////////////////////////////////////////////////
     }



    public function RenderPago($id){
    $pago = pagos_facturas::join('bancos','bancos.id','pagos_facturas.banco_id')->select('pagos_facturas.*','bancos.nombre as nombre_banco')->find($id);    
    
    $this->nombre_banco_ver = $pago->nombre_banco;
    $this->nro_comprobante_ver = $pago->nro_comprobante;
    $this->comprobante_ver = $pago->url_comprobante;
    $caja_ver = cajas::find($pago->caja);
    if($caja_ver != null){
    $this->caja_ver = $caja_ver->nro_caja;
    } else {
    $this->caja_ver = 0;    
    }

    // Si es gasto
    if(0 < $pago->id_gasto || $pago->id_gasto != null){
    $this->monto_ver = $pago->monto_gasto;  
    }
          
    // Si es compra
    if(0 < $pago->id_compra || $pago->id_compra != null){
    $this->monto_ver = $pago->monto_compra;  
    }
       
    $this->emit("ver-pago","");
    }
    
    public function RenderSaldo($id){
    $pago = saldos_iniciales::join('bancos','bancos.id','saldos_iniciales.metodo_pago')->select('saldos_iniciales.*','bancos.nombre as nombre_banco')->find($id);    
    $this->monto_saldo = abs($pago->monto);
    $this->metodo_pago_saldo = $pago->metodo_pago;
    $this->fecha_saldo = Carbon::parse($pago->created_at)->format("Y-m-d");
    $this->emit("ver-pago-saldo-inicial","");
    $this->modo_ver_saldo = 1;
    }
    
    public function AgregarPagoSaldo(){
    $this->selected_pago_saldo_id = 0;
    $this->monto_saldo = 0;
    $this->metodo_pago_saldo = 1;
    $this->fecha_saldo = Carbon::now()->format("Y-m-d");
    $this->emit("ver-pago-saldo-inicial","");
    $this->modo_ver_saldo = 0;    
    }
    
    public function EditPagoSaldo($id_pago){
    $this->selected_pago_saldo_id = $id_pago;
    $this->RenderSaldo($id_pago);
    $this->modo_ver_saldo = 0;
    }
 
    
    public function CerrarAgregarPagoSaldo(){
    $this->emit("ver-pago-saldo-inicial-hide","");    
    }

    public function ActualizarPagoSaldo($id_pago){
    $pago = saldos_iniciales::find($id_pago);  
    if($pago->concepto == "Pago"){$monto = -1*$this->monto_saldo;} else {$monto = $this->monto_saldo;}
    
    $pago->update([
        'monto' => $monto,
        'metodo_pago' => $this->metodo_pago_saldo
        ]);
        
    $si = saldos_iniciales::where("referencia_id",$pago->referencia_id)->where("tipo","proveedor")->where("eliminado",0)->get();
	
	$sum_si = $si->sum('monto');
	
	$proveedores = proveedores::find($pago->referencia_id);
	$proveedores->saldo_inicial_cuenta_corriente = $sum_si;
	$proveedores->save();
	
	$this->emit("ver-pago-saldo-inicial-hide","");
	
    }
    
    public function DeletePagoSaldo($id_pago){
    $pago = saldos_iniciales::find($id_pago);  
    
    $pago->update([
        'eliminado' => 1
        ]);
        
    $si = saldos_iniciales::where("referencia_id",$pago->referencia_id)->where("tipo","proveedor")->where("eliminado",0)->get();
	
	$sum_si = $si->sum('monto');
	
	$proveedores = proveedores::find($pago->referencia_id);
	$proveedores->saldo_inicial_cuenta_corriente = $sum_si;
	$proveedores->save();
   }
 
    public function CreatePagoSaldo(){

    $monto = -1*$this->monto_saldo;
    
    $array = [
        'monto' => $monto,
        'metodo_pago' => $this->metodo_pago_saldo,
        'tipo' => 'proveedor',
        'concepto'  => 'Pago',
        'referencia_id' => $this->datos_proveedor->id,
        'comercio_id' => $this->comercio_id
        ];
    
    $pago = saldos_iniciales::create($array);
        
    $si = saldos_iniciales::where("referencia_id",$pago->referencia_id)->where("tipo","proveedor")->where("eliminado",0)->get();
	
	$sum_si = $si->sum('monto');
	
	$proveedores = proveedores::find($pago->referencia_id);
	$proveedores->saldo_inicial_cuenta_corriente = $sum_si;
	$proveedores->save();
	
	$this->emit("ver-pago-saldo-inicial-hide","");
        
    }


public function CerrarVerPagos(){
 $this->emit("hide-ver-pagos","");
 }
 
 
public function VerPagos($CompraId){

    $this->compra_id = $CompraId;

   //////////////// PAGOS //////////////
    $this->pagos2 = pagos_facturas::join('bancos as mp','mp.id','pagos_facturas.banco_id')
    ->leftjoin('cajas','cajas.id','pagos_facturas.caja')
    ->select('mp.nombre as metodo_pago','pagos_facturas.url_comprobante','pagos_facturas.nro_comprobante','pagos_facturas.id','cajas.nro_caja','pagos_facturas.monto_compra','pagos_facturas.actualizacion','pagos_facturas.actualizacion','pagos_facturas.created_at as fecha_pago')
    ->where('pagos_facturas.id_compra', $CompraId)
    ->where('pagos_facturas.eliminado',0)
    ->get();
     
     //dd($CompraId);
    
    $this->suma_monto = $this->pagos2->sum('monto_compra');

    $this->emit("show-ver-pagos","");
    
    $this->render();
}    

public function CerrarModal(){
    $this->id_compra = 0;
}



  public function CreatePago($compra_id)
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    
    $compra_id = $this->compra_id;
    
    $this->compras_proveedores = compras_proveedores::find($compra_id);
    
    if($this->metodo_pago_agregar_pago == 1) {
    $mp = 1;
    } else {$mp = 0;}
            
    $nro_pago = $this->GetNumeroPago($comercio_id);
    
    $pago_factura =   pagos_facturas::create([
      'nro_pago' => $nro_pago,
      'monto_compra' => $this->monto_ap,
      'caja' => $this->caja,
      'nro_comprobante' => $this->nro_comprobante,
      'banco_id' => $this->metodo_pago_agregar_pago,
      'metodo_pago' => $mp,
      'created_at' => $this->fecha_ap,
      'proveedor_id' => $this->compras_proveedores->proveedor_id,
      'comercio_id' => $comercio_id,
      'id_compra' => $compra_id,
      'eliminado' => 0
    ]);
    
    if($this->comprobante)
	{
		$customFileName = uniqid() . '_.' . $this->comprobante->extension();
		$this->comprobante->storeAs('public/comprobantes', $customFileName);
		$pago_factura->url_comprobante = $customFileName;
		$pago_factura->save();
	}
		
    $this->monto_ap = '';
    $this->metodo_pago_ap = 'Elegir';
    $this->caja = cajas::where('estado',0)->where('user_id',Auth::user()->id)->max('id');

     $this->emit('pago-agregado', 'El pago fue guardado.');

     
     $this->emit('agregar-pago-hide', 'hide');
     
     $this->ActualizarEstadoDeuda($compra_id); 
     
     if($this->origen_accion == 2){
      $this->VerPagos($compra_id);     
     }
    
    
  }

public function ActualizarEstadoDeudaGasto($gasto_id)
{
    
    $gasto = gastos::find($gasto_id);
    $monto_total = $gasto->monto;

    $pagos = pagos_facturas::where('id_gasto',$gasto_id)->where('eliminado',0)->get();
    $pago_total = $pagos->sum("monto_gasto");
    
    // Filtrar los métodos de pago donde 'eliminado' es igual a 0 y luego acumular los valores de 'metodo_pago_ap_div'
    $cuentas = collect($pagos)
        ->filter(function ($metodo) {
            return $metodo->eliminado == 0;
        })
        ->pluck('banco_id')
        ->implode(',');
        
    $deuda = $monto_total - $pago_total;
    
    $gasto->deuda = $deuda;
    $gasto->cuenta = $cuentas;
    $gasto->save();
    
}

public function ActualizarEstadoDeuda($ventaId)
{
  /////////////////   ACTUALIZAR ESTADO DE PAGO   /////////////////////


       $this->data_total = compras_proveedores::where('compras_proveedores.id', $ventaId)->first();
       
       //dd($this->data_total);

       $this->pagos2 = pagos_facturas::join('bancos','bancos.id','pagos_facturas.banco_id')
       ->select('bancos.nombre as metodo_pago','pagos_facturas.id_compra','pagos_facturas.monto_compra','pagos_facturas.actualizacion','pagos_facturas.created_at as fecha_pago')
       ->where('pagos_facturas.id_compra', $ventaId)
       ->where('pagos_facturas.eliminado',0)
       ->get();

        $suma_monto = 0;
        
        // pagos        
        foreach ($this->pagos2 as $pago) {
            $montoCompra = $pago->monto_compra;
            $actualizacion = $pago->actualizacion;
        
            // Realizar el cálculo y agregar al total
            $suma_monto += $montoCompra * (1 + $actualizacion);
        }
        
        
       $this->tot = $this->data_total->total;
       $this->suma_monto = $suma_monto;
       
      // dd($this->tot,$suma_monto);
       
       $deuda = $this->tot - $this->suma_monto;

      //  dd($deuda);

      $this->deuda_vieja = compras_proveedores::find($ventaId);

       $this->deuda_vieja->update([
         'deuda' => $deuda
         ]);


       ///////////////////////////////////////////////////////////////////
}

//// ELEGIR UNA CAJA AL MOMENTO DE AGREGAR O EDITAR UN PAGO ////

public function ElegirCaja($caja_id)
{

$this->caja = $caja_id;


$this->emit('modal-estado-hide','close');

}

  public function SinCaja() {

  	$this->caja = null;
  	$this->caja_elegida = null;

  }
  
  
  public function ExportarMovimientos(){
      
  }
  
  
    public function OrdenarNroPagos($comercio_id){
    
    /*
    $pagos_ventas = pagos_facturas::where('comercio_id',$comercio_id)->where('id_factura' > 0)->get();
    $pagos_ingresos_retiros = pagos_facturas::where('comercio_id',$comercio_id)->where('id_factura' > 0)->get();
    $pagos_cobro_rapido = pagos_facturas::where('comercio_id',$comercio_id)->where('id_factura' > 0)->get();
    $pagos_compra_insumos = pagos_facturas::where('comercio_id',$comercio_id)->where('id_factura' > 0)->get();
    $pagos_gastos = pagos_facturas::where('comercio_id',$comercio_id)->where('id_factura' > 0)->get();
    */
    
    $pagos_compra = pagos_facturas::where('comercio_id',$comercio_id)->where('id_compra', '>', 0)->get();
    $i = 0;
    foreach($pagos_compra as $pc){
    $i++;
    $pc->nro_pago = $i;
    $pc->save();
    }
    //  'id_factura','id_ingresos_retiros','id_cobro_rapido','id_sale_casa_central','id_compra_insumos','id_gasto','id_compra'
    }   

    public function GetNumeroPago($comercio_id){  
    $pago_compra = pagos_facturas::where('comercio_id',$comercio_id)->where('id_compra', '>', 0)->orderBy('id','desc')->first();
    if($pago_compra != null){
        $nro_pago =  $pago_compra->nro_pago + 1;
    } else {
        $nro_pago =  1;    
    }
    
    return $nro_pago;
    }
    
    
    
    // 14-8-2024
    public function GetData($comercio_id)
{
    $pagos_compras = pagos_facturas::join('bancos', 'bancos.id', 'pagos_facturas.banco_id')
        ->leftjoin('compras_proveedores', 'compras_proveedores.id', 'pagos_facturas.id_compra')
        ->whereBetween('pagos_facturas.created_at', [$this->from, $this->to])
        ->where('pagos_facturas.comercio_id', $comercio_id)
        ->where('pagos_facturas.tipo_pago', 1)
        ->where('pagos_facturas.eliminado', 0)
        ->where('pagos_facturas.proveedor_id', $this->proveedor_id)
        ->select(
            'bancos.nombre as nombre_banco',
            'bancos.id as id_banco',
            'pagos_facturas.url_comprobante as url_pago',
            pagos_facturas::raw('0 as id_saldo'),
            pagos_facturas::raw('0 as monto_saldo'),
            'compras_proveedores.nro_compra',
            'pagos_facturas.id as id_pago',
            'pagos_facturas.id_compra',
            'pagos_facturas.created_at',
            'pagos_facturas.monto_compra as monto_compra',
            pagos_facturas::raw('0 as monto_pago'),
            compras_proveedores::raw('0 as id_gasto'),
            compras_proveedores::raw('0 as monto_gasto'),
            compras_proveedores::raw('0 as nro_gasto')
        );

    $pagos_gastos = pagos_facturas::leftjoin('bancos', 'bancos.id', 'pagos_facturas.banco_id')
        ->leftjoin('gastos', 'gastos.id', 'pagos_facturas.id_gasto')
        ->whereBetween('pagos_facturas.created_at', [$this->from, $this->to])
        ->where('pagos_facturas.comercio_id', $comercio_id)
    //    ->where('pagos_facturas.tipo_pago', 1)
        ->where('pagos_facturas.eliminado', 0)
        ->where('pagos_facturas.proveedor_id', $this->proveedor_id)
        ->select(
            'bancos.nombre as nombre_banco',
            'bancos.id as id_banco',
            'pagos_facturas.url_comprobante as url_pago',
            pagos_facturas::raw('0 as id_saldo'),
            pagos_facturas::raw('0 as monto_saldo'),
            gastos::raw('0 as nro_compra'),
            'pagos_facturas.id as id_pago',
            pagos_facturas::raw('0 as id_compra'),
            'pagos_facturas.created_at',
            pagos_facturas::raw('0 as monto_compra'),
            'pagos_facturas.monto_gasto as monto_pago',
            'gastos.id as id_gasto',
             pagos_facturas::raw('0 as monto_gasto'),
            'gastos.id as nro_gasto'
        );

    $compras = compras_proveedores::where('compras_proveedores.comercio_id', $comercio_id)
        ->whereBetween('compras_proveedores.created_at', [$this->from, $this->to])
        ->where('compras_proveedores.proveedor_id', $this->proveedor_id)
        ->where('compras_proveedores.eliminado', 0)
        ->select(
            compras_proveedores::raw('"-" as nombre_banco'),
            compras_proveedores::raw('0 as id_banco'),
            compras_proveedores::raw('0 as url_pago'),
            compras_proveedores::raw('0 as id_saldo'),
            compras_proveedores::raw('0 as monto_saldo'),
            'compras_proveedores.nro_compra',
            compras_proveedores::raw('0 as id_pago'),
            'compras_proveedores.id as id_compra',
            'compras_proveedores.created_at',
            'compras_proveedores.total as monto_compra',
            compras_proveedores::raw('0 as monto_pago'),
            compras_proveedores::raw('0 as id_gasto'),
            compras_proveedores::raw('0 as monto_gasto'),
            compras_proveedores::raw('0 as nro_gasto')
        );

    $gastos = gastos::where('gastos.comercio_id', $comercio_id)
        ->whereBetween('gastos.created_at', [$this->from, $this->to])
        ->where('gastos.proveedor_id', $this->proveedor_id)
        ->where('gastos.eliminado', 0)
        ->select(
            gastos::raw('"-" as nombre_banco'),
            gastos::raw('0 as id_banco'),
            gastos::raw('0 as url_pago'),
            gastos::raw('0 as id_saldo'),
            gastos::raw('0 as monto_saldo'),
            gastos::raw('0 as nro_compra'),
            gastos::raw('0 as id_pago'),
            gastos::raw('0 as id_compra'),
            'gastos.created_at',
            gastos::raw('0 as monto_compra'),
            gastos::raw('0 as monto_pago'),
            'gastos.id as id_gasto',
            'gastos.monto as monto_gasto',
            'gastos.id as nro_gasto'
        );

    $saldos_iniciales = saldos_iniciales::join('bancos', 'bancos.id', 'saldos_iniciales.metodo_pago')
        ->where('saldos_iniciales.comercio_id', $comercio_id)
        ->whereBetween('saldos_iniciales.created_at', [$this->from, $this->to])
        ->where('saldos_iniciales.referencia_id', $this->proveedor_id)
        ->where('saldos_iniciales.tipo', 'proveedor')
        ->where('saldos_iniciales.eliminado', 0)
        ->select(
            'bancos.nombre as nombre_banco',
            'bancos.id as id_banco',
            saldos_iniciales::raw('0 as url_pago'),
            'saldos_iniciales.id as id_saldo',
            'saldos_iniciales.monto as monto_saldo',
            saldos_iniciales::raw('0 as nro_compra'),
            saldos_iniciales::raw('0 as id_pago'),
            saldos_iniciales::raw('0 as id_compra'),
            'saldos_iniciales.created_at',
            saldos_iniciales::raw('0 as monto_compra'),
            saldos_iniciales::raw('0 as monto_pago'),
            saldos_iniciales::raw('0 as id_gasto'),
            saldos_iniciales::raw('0 as monto_gasto'),
            saldos_iniciales::raw('0 as nro_gasto')
        );

    // Unión de las subconsultas
    $union = $pagos_compras->union($pagos_gastos)
        ->union($compras)
        ->union($gastos)
        ->union($saldos_iniciales);

    // Obtener el resultado ordenado
    $compras_proveedores = $union->orderBy('created_at', 'desc')->get();

   // dd($compras_proveedores);

    return $compras_proveedores;
}

/////////////////////////////////////////////////////////////////////////////

public function getBancos($comercio_id){
    return bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
    ->where('bancos_muestra_sucursales.muestra', 1)
    ->where('bancos_muestra_sucursales.sucursal_id', $comercio_id)
    ->select('bancos.*')
    ->orderBy('bancos.nombre','asc')
    ->get();
    
   
}
  
}
