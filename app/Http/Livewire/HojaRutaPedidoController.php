<?php

namespace App\Http\Livewire;

use App\Models\hoja_ruta;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\SaleDetail;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use DB;

class HojaRutaPedidoController extends Component
{
  use WithPagination;
  use WithFileUploads;


  public $nombre,$recargo,$recargoDetails,$descripcion,$price,$details , $stock,$alerts,$categoryid,$search,$hojas_de_ruta, $image,$selected_id,$id_pedido_hr,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $metodos, $metodo, $saleId, $turno, $fecha, $HojaRutaElegida, $nro_hoja, $id_hoja_ruta, $metodos_nro_hoja, $ultimo,$listado_hojas_ruta, $hoja_ruta_nueva,$countDetails,$sumDetails, $sum;
  private $pagination = 25;


  public function paginationView()
  {
    return 'vendor.livewire.bootstrap';
  }


  public function mount()
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;


    $this->pageTitle = 'Listado';
    $this->details =[];
    $this->listado_hojas_ruta =[];
    $this->sumDetails =0;
    $this->countDetails =0;
    $this->componentName = 'Hoja de ruta';
    $this->turno = 'Elegir';
    $this->fecha = Carbon::now()->format('d-m-Y');
    $this->hoja_ruta_nueva = hoja_ruta::where('hoja_rutas.comercio_id', 'like', $comercio_id)->select('hoja_rutas.nro_hoja')->latest('nro_hoja')->first();

  }





  public function render()
  {
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $metodos = hoja_ruta::where('hoja_rutas.comercio_id', 'like', $comercio_id)
    ->orderBy('hoja_rutas.nro_hoja','desc')
    ->paginate($this->pagination);

    $this->hoja_ruta_nueva = hoja_ruta::where('hoja_rutas.comercio_id', 'like', $comercio_id)->select('hoja_rutas.nro_hoja')->latest('nro_hoja')->first();

    $ultimo = hoja_ruta::where('hoja_rutas.comercio_id', 'like', $comercio_id)->select('hoja_rutas.nro_hoja')->latest('nro_hoja')->first();

    $hojas_de_ruta = hoja_ruta::where('hoja_rutas.comercio_id', 'like', $comercio_id)->orderBy('hoja_rutas.nro_hoja','desc')->get();


    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;



    if($this->id_hoja_ruta) {

      if($this->id_hoja_ruta === 'no') {

      $data = Sale::join('users as u', 'u.id', 'sales.user_id')
        ->leftjoin('hoja_rutas as hr','hr.id','sales.hoja_ruta')
        ->join('metodo_pagos as m','m.id','sales.metodo_pago')
        ->join('clientes_mostradors as cm','cm.id','sales.cliente_id')
        ->leftjoin('pagos_facturas','pagos_facturas.id_factura','sales.id')
        ->select('sales.*', 'hr.fecha','hr.turno','u.name as user','m.nombre as nombre_metodo_pago','cm.nombre as nombre_cliente','cm.direccion','cm.localidad','cm.provincia','cm.dni','cm.email',Sale::raw('SUM(pagos_facturas.monto) AS monto'))
        ->where('sales.comercio_id', $comercio_id)
        ->where('sales.hoja_ruta', null)
        ->groupBy('sales.eliminado','sales.recordatorio','sales.wc_order_id','sales.subtotal','sales.tipo_comprobante','sales.nro_factura','sales.cae','sales.caja','sales.recargo','sales.descuento','sales.deuda','sales.id','sales.total','sales.items','sales.cash','sales.change','sales.status','sales.metodo_pago','sales.user_id','sales.comercio_id','sales.cliente_id','sales.observaciones','sales.fecha_entrega','sales.hoja_ruta','sales.canal_venta','sales.estado_pago','sales.created_at','sales.updated_at','hr.fecha','hr.turno','u.name','m.nombre','cm.nombre','cm.direccion','cm.localidad','cm.provincia','cm.dni','cm.email','sales.nota_interna','sales.vto_cae','sales.iva')

        ->orderBy('sales.created_at','desc')
        ->get();







        return view('livewire.hoja-ruta-pedido.component', [
          'data' => $metodos,
          'ultima_hoja' => $ultimo,
          'pedidos' => $data,
          'hr' => $hojas_de_ruta
        ])
        ->extends('layouts.theme.app')
        ->section('content');
      } else {
        $data = Sale::join('users as u', 'u.id', 'sales.user_id')
        ->leftjoin('hoja_rutas as hr','hr.id','sales.hoja_ruta')
          ->join('metodo_pagos as m','m.id','sales.metodo_pago')
          ->join('clientes_mostradors as cm','cm.id','sales.cliente_id')
          ->leftjoin('pagos_facturas','pagos_facturas.id_factura','sales.id')
          ->select('sales.*', 'hr.fecha','hr.turno','u.name as user','m.nombre as nombre_metodo_pago','cm.nombre as nombre_cliente','cm.direccion','cm.localidad','cm.provincia','cm.dni','cm.email',Sale::raw('SUM(pagos_facturas.monto) AS monto'))
          ->where('sales.comercio_id', $comercio_id)
          ->where('sales.hoja_ruta', $this->id_hoja_ruta)

          ->groupBy('sales.eliminado','sales.wc_order_id','sales.subtotal','sales.tipo_comprobante','sales.nro_factura','sales.cae','sales.caja','sales.recargo','sales.descuento','sales.deuda','sales.id','sales.total','sales.items','sales.cash','sales.change','sales.status','sales.metodo_pago','sales.user_id','sales.comercio_id','sales.cliente_id','sales.observaciones','sales.fecha_entrega','sales.hoja_ruta','sales.canal_venta','sales.estado_pago','sales.created_at','sales.updated_at','hr.fecha','hr.turno','u.name','m.nombre','cm.nombre','cm.direccion','cm.localidad','cm.provincia','cm.dni','cm.email','sales.nota_interna','sales.vto_cae','sales.iva')
          ->orderBy('sales.created_at','desc')
          ->get();


          return view('livewire.hoja-ruta-pedido.component', [
            'data' => $metodos,
            'ultima_hoja' => $ultimo,
            'pedidos' => $data,
            'hr' => $hojas_de_ruta
          ])
          ->extends('layouts.theme.app')
          ->section('content');
      }

    } else {
      $data = Sale::join('users as u', 'u.id', 'sales.user_id')
        ->leftjoin('hoja_rutas as hr','hr.id','sales.hoja_ruta')
        ->join('metodo_pagos as m','m.id','sales.metodo_pago')
        ->join('clientes_mostradors as cm','cm.id','sales.cliente_id')
        ->leftjoin('pagos_facturas','pagos_facturas.id_factura','sales.id')
        ->select('sales.*', 'hr.fecha','hr.turno','u.name as user','m.nombre as nombre_metodo_pago','cm.nombre as nombre_cliente','cm.direccion','cm.localidad','cm.provincia','cm.dni','cm.email',Sale::raw('SUM(pagos_facturas.monto) AS monto'))
        ->where('sales.comercio_id', $comercio_id)
        ->groupBy('sales.eliminado','sales.recordatorio','sales.tipo_comprobante','sales.wc_order_id','sales.nro_factura','sales.subtotal','sales.cae','sales.caja','sales.recargo','sales.descuento','sales.deuda','sales.id','sales.total','sales.items','sales.cash','sales.change','sales.status','sales.metodo_pago','sales.user_id','sales.comercio_id','sales.cliente_id','sales.observaciones','sales.fecha_entrega','sales.hoja_ruta','sales.canal_venta','sales.estado_pago','sales.created_at','sales.updated_at','hr.fecha','hr.turno','u.name','m.nombre','cm.nombre','cm.direccion','cm.localidad','cm.provincia','cm.dni','cm.email','sales.nota_interna','sales.vto_cae','sales.iva')
        ->orderBy('sales.created_at','desc')
        ->get();


          $data2 = SaleDetail::join('products','products.id','sale_details.product_id')
          ->select('sale_details.sale_id',DB::raw('GROUP_CONCAT(" ",sale_details.quantity," ",products.name," ",sale_details.comentario," ") as prod'))->whereIn('sale_id', function($query) {
            $query->select(DB::raw('id'))
                  ->from('sales')
                  ->whereRaw('sale_details.sale_id = sales.id');

          })
          ->where('sale_details.eliminado',0)
          ->groupBy('sale_details.sale_id')
          ->get();

        return view('livewire.hoja-ruta-pedido.component', [
          'data' => $metodos,
          'ultima_hoja' => $ultimo,
          'pedidos' => $data,
          'hr' => $hojas_de_ruta
        ])
        ->extends('layouts.theme.app')
        ->section('content');
    }




  }


  public function Store()
  {
    $rules  =[
      'turno' => 'required',
      'fecha' => 'required'

    ];

    $messages = [
      'turno.required' => 'El turno es requerido',
      'fecha.required' => 'La fecha es requerida',

    ];

    $this->validate($rules, $messages);

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $ultimo = hoja_ruta::where('hoja_rutas.comercio_id', 'like', $comercio_id)->select('hoja_rutas.nro_hoja')->latest('nro_hoja')->first();

    $hoja = $ultimo->nro_hoja + 1;


    $product = hoja_ruta::create([
      'nro_hoja' => $hoja,
      'fecha' => Carbon::parse($this->fecha)->format('Y-m-d'),
      'turno' => $this->turno,
      'comercio_id' => $comercio_id
    ]);


    $this->resetUI();
    $this->emit('product-added', 'Producto Registrado');


  }


  public function Edit(hoja_ruta $metodo)
  {
    $this->selected_id = $metodo->id;
    $this->nro_hoja = $metodo->id;
    $this->fecha = Carbon::parse($metodo->fecha)->format('d-m-Y');
    $this->turno = $metodo->turno;

    $this->emit('modal-show','Show modal');
  }


  public function Update()
  {
    $rules  =[
      'fecha' => 'required',
      'turno' => 'required',

    ];

    $messages = [
      'fecha.required' => 'La fecha es requerida',
      'turno.required' => 'El turno es requerido',

    ];

    $this->validate($rules, $messages);

    $metodo = hoja_ruta::find($this->selected_id);

    $metodo->update([
      'fecha' => $this->fecha = Carbon::parse($this->fecha)->format('Y-m-d'),
      'turno' => $this->turno
    ]);


    $this->resetUI();
    $this->emit('product-updated', 'Hoja de ruta Actualizada');


  }


  public function HojaRutaElegida($HojaRutaElegida)
  {

    if($HojaRutaElegida != 0) {
      $Hruta = Sale::find($this->id_pedido_hr);

      $Hruta->update([
        'hoja_ruta' => $HojaRutaElegida
      ]);

      $this->resetUI();
      $this->emit('hide-modal2','details loaded');
      $this->emit('product-updated', 'Hoja de ruta Actualizada');

    } else {
      $Hruta = Sale::find($this->id_pedido_hr);

      $Hruta->update([
        'hoja_ruta' => null
      ]);

      $this->resetUI();
      $this->emit('hide-modal2','details loaded');
      $this->emit('product-updated', 'Hoja de ruta Actualizada');


    }


  }



  public function resetUI()
  {

    $this->selected_id = '';
    $this->id_pedido_hr = '';


  }

  protected $listeners =[
    'deleteRow' => 'Destroy'
  ];

  public function ScanCode($code)
  {
    $this->ScanearCode($code);
    $this->emit('global-msg',"SE AGREGÓ EL PRODUCTO AL CARRITO");
  }


  public function Destroy(hoja_ruta $metodo)
  {
    $imageTemp = $metodo->image;
    $metodo->delete();

    if($imageTemp !=null) {
      if(file_exists('storage/products/' . $imageTemp )) {
        unlink('storage/products/' . $imageTemp);
      }
    }

    $this->resetUI();
    $this->emit('product-deleted', 'Producto Eliminado');
  }

  public function getDetails($saleId)
  {

      $this->details = SaleDetail::join('products as p','p.id','sale_details.product_id')
      ->select('sale_details.id','sale_details.price','sale_details.quantity','p.name as product')
      ->where('sale_details.sale_id', $saleId)
      ->get();

      $this->venta = Sale::find($saleId);

      $this->recargoDetails = $this->venta->recargo;


      //
      $suma = $this->details->sum(function($item){
          return $item->price * $item->quantity;
      });

      $this->sumDetails = $suma;
      $this->countDetails = $this->details->sum('quantity');
      $this->saleId = $saleId;

      $this->emit('show-modal','details loaded');

  }

  public function getDetails2($saleId)
  {

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

      $this->listado_hojas_ruta = hoja_ruta::where('hoja_rutas.comercio_id', $comercio_id)->where('hoja_rutas.fecha', '>', Carbon::now())->orderBy('hoja_rutas.fecha','desc')->get();;
      $this->id_pedido_hr = $saleId;

      $this->emit('show-modal2','details loaded');

  }
}
