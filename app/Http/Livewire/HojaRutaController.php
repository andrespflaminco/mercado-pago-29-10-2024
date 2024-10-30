<?php

namespace App\Http\Livewire;

use App\Models\hoja_ruta;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\SaleDetail;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HojaRutaController extends Component
{
  use WithPagination;
  use WithFileUploads;


  public $ver,$nombre,$hoja_selected,$recargo,$descripcion,$price,$details , $stock,$alerts,$categoryid,$search,$image,$selected_id,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $metodos, $metodo, $saleId, $turno, $tipo, $observaciones_hr, $fecha, $nro_hoja, $metodos_nro_hoja, $ultimo, $hoja_ruta_nueva,$countDetails,$sumDetails, $sum;
  private $pagination = 25;
  public $id_check = [];

  public function paginationView()
  {
    return 'vendor.livewire.bootstrap';
  }


  public function mount(Request $request)
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $this->pageTitle = 'Listado';
    $this->details =[];
    $this->sumDetails =0;

    $this->ventas_hoja_ruta = [];
    $this->ventas_todas = [];
    $this->tipo = '';
    $this->nombre = '';
    $this->turno = '';


    $this->observaciones_hr = '';
    $this->countDetails =0;
    $this->componentName = 'Hoja de ruta';

    $this->fecha = Carbon::now()->format('d-m-Y');
    $this->hoja_ruta_nueva = hoja_ruta::where('hoja_rutas.comercio_id', 'like', $comercio_id)->select('hoja_rutas.nro_hoja')->latest('nro_hoja')->first();


    $hoja_id = $request->input('hoja_id');
    if($hoja_id == null){
    $this->ver = 0;    
    } else {
      $this->ver = 1;
      $this->hoja_selected = $hoja_id;
      
      $this->ventas_hoja_ruta = Sale::select('sales.*','clientes_mostradors.nombre as nombre_cliente')->join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')->where('sales.hoja_ruta',$hoja_id)->where('sales.eliminado',0)->get();
      
      $hoja = hoja_ruta::find($hoja_id);
      $this->nombre = $hoja->nombre;
      $this->tipo = $hoja->tipo;
      $this->nro_hoja = $hoja->id;
      $this->fecha = Carbon::parse($hoja->fecha)->format('Y-m-d');
      $this->turno = $hoja->turno;
      $this->observaciones_hr = $hoja->observaciones;          
    }
  }





  public function render()
  {
      
    $this->app_url = config('app.url');
                
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    $this->comercio_id = $comercio_id;
    
    $hoja_ruta = hoja_ruta::select('hoja_rutas.*')
    ->where('hoja_rutas.comercio_id', 'like', $comercio_id)
    ->orderBy('hoja_rutas.nro_hoja','desc')
    ->paginate($this->pagination);

    $this->hoja_ruta_nueva = hoja_ruta::where('hoja_rutas.comercio_id', 'like', $comercio_id)->select('hoja_rutas.nro_hoja')->latest('nro_hoja')->first();

    $ultimo = hoja_ruta::where('hoja_rutas.comercio_id', 'like', $comercio_id)->select('hoja_rutas.nro_hoja')->latest('nro_hoja')->first();

    if($ultimo != null)
    $hoja = $ultimo->nro_hoja + 1;
    else
    $hoja = 1;

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;


    $data = Sale::join('users as u', 'u.id', 'sales.user_id')
      ->join('metodo_pagos as m','m.id','sales.metodo_pago')
      ->join('clientes_mostradors as cm','cm.id','sales.cliente_id')
      ->select('sales.*', 'u.name as user','m.nombre as nombre_metodo_pago','cm.nombre as nombre_cliente','cm.email')
      ->where('sales.comercio_id', $comercio_id)
      ->orderBy('sales.created_at','desc')
      ->paginate(25);


    return view('livewire.hoja-ruta.component', [
      'data' => $hoja_ruta,
      'ultima_hoja' => $ultimo,
      'pedidos' => $data
    ])
    ->extends('layouts.theme-pos.app')
    ->section('content');

  }


  public function AbrirModal(){
      $this->emit("modal-show","");
  }

  public function Store()
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $ultimo = hoja_ruta::where('hoja_rutas.comercio_id', 'like', $comercio_id)->select('hoja_rutas.nro_hoja')->latest('nro_hoja')->first();

    if($ultimo != null)
    $hoja = $ultimo->nro_hoja + 1;
    else
    $hoja = 1;

    $product = hoja_ruta::create([
      'nro_hoja' => $hoja,
      'fecha' => Carbon::parse($this->fecha)->format('Y-m-d'),
      'nombre' => $this->nombre,
      'tipo' => $this->tipo,
      'observaciones' => $this->observaciones_hr,
      'turno' => $this->turno,
      'comercio_id' => $comercio_id
    ]);


    $this->resetUI();
    $this->emit('noty', 'Hoja de ruta agregada');


  }


  public function Edit(hoja_ruta $hoja)
  {
    $this->selected_id = $hoja->id;
    $this->nombre = $hoja->nombre;
    $this->tipo = $hoja->tipo;
    $this->nro_hoja = $hoja->id;
    $this->fecha = Carbon::parse($hoja->fecha)->format('Y-m-d');
    $this->turno = $hoja->turno;
    $this->observaciones_hr = $hoja->observaciones;

    $this->emit('modal-show','Show modal');
  }

  public function Update()
  {
    $rules  =[
      'fecha' => 'required'

    ];

    $messages = [
      'fecha.required' => 'La fecha es requerida'

    ];

    $this->validate($rules, $messages);

    $metodo = hoja_ruta::find($this->selected_id);

    $metodo->update([
      'fecha' => $this->fecha = Carbon::parse($this->fecha)->format('Y-m-d'),
      'turno' => $this->turno,
      'nombre' => $this->nombre,
      'observaciones' => $this->observaciones_hr,
      'tipo' => $this->tipo
      
    ]);


    $this->resetUI();
    $this->emit('noty', 'Hoja de ruta Actualizada');


  }

  
  public function Destroy(hoja_ruta $hoja)
  {
    $hoja->eliminado = 1;
    $hoja->save();

    $this->resetUI();
    $this->emit('noty', 'Hoja de ruta Eliminada');
  }

  public function resetUI()
  {
    $this->emit("modal-hide","");
    $this->nombre = '';
    $this->tipo = '';
    $this->turno = '';
    $this->selected_id = '';
    $this->fecha = Carbon::now()->format('d-m-Y');
    $this->observaciones_hr = '';


  }

  protected $listeners =[
    'deleteRow' => 'Destroy',
    'agregar-lote' => 'AsociarVentas'
  ];



public function AsociarVentas($ids)
{
// Comparamos las que tenemos que asociar con las que ya estan asociadas
$ventas_ya_asociadas = Sale::where('hoja_ruta',$this->hoja_selected)->get();
$ventas_a_asociar = $ids;

// Obtener los IDs de las ventas ya asociadas
$ventas_ya_asociadas_ids = Sale::where('hoja_ruta', $this->hoja_selected)->pluck('id')->toArray();

// IDs de las ventas que quieres asociar
$ventas_a_asociar_ids = $ids;

// Encontrar IDs que están en $ventas_a_asociar_ids pero no en $ventas_ya_asociadas_ids
$ventas_para_agregar = array_diff($ventas_a_asociar_ids, $ventas_ya_asociadas_ids);

// Encontrar IDs que ya están en $ventas_ya_asociadas_ids pero no en $ventas_a_asociar_ids
$ventas_para_eliminar = array_diff($ventas_ya_asociadas_ids, $ventas_a_asociar_ids);

// Encontrar IDs que ya están en ambas listas
$ventas_ya_existentes = array_intersect($ventas_ya_asociadas_ids, $ventas_a_asociar_ids);

// seteamos las nuevas
foreach($ventas_para_eliminar as $id){
$sale = Sale::find($id);
$sale->hoja_ruta = null;
$sale->save();    
}

// seteamos las nuevas
foreach($ventas_para_agregar as $id){
$sale = Sale::find($id);
$sale->hoja_ruta = $this->hoja_selected;
$sale->save();    
}

$this->id_check = [];
$this->ventas_hoja_ruta = Sale::select('sales.*','clientes_mostradors.nombre as nombre_cliente')->join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')->where('sales.hoja_ruta',$this->hoja_selected)->where('sales.eliminado',0)->get();


$this->emit("seleccionar-ventas-hoja-ruta-hide","");    
$this->emit("noty","Ventas asociadas a la Hoja de ruta");
}

public function CerrarVentasAsociadas(){
$this->emit("seleccionar-ventas-hoja-ruta-hide","");    
}

  public function Ver($id){
      $this->ver = 1;
      $this->hoja_selected = $id;
      
      $this->ventas_hoja_ruta = Sale::select('sales.*','clientes_mostradors.nombre as nombre_cliente')->join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')->where('sales.hoja_ruta',$id)->where('sales.eliminado',0)->get();
      
      $hoja = hoja_ruta::find($id);
      $this->nombre = $hoja->nombre;
      $this->tipo = $hoja->tipo;
      $this->nro_hoja = $hoja->id;
      $this->fecha = Carbon::parse($hoja->fecha)->format('Y-m-d');
      $this->turno = $hoja->turno;
      $this->observaciones_hr = $hoja->observaciones;     
      
  }
  
  public function BuscarVenta(){
     
     $this->ventas_todas = Sale::select('sales.*','clientes_mostradors.nombre as nombre_cliente')
     ->join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')
     ->where('sales.comercio_id',$this->comercio_id)
     ->where('sales.eliminado',0)
     ->orderBy('sales.nro_venta','desc')
     ->get();
     
     
    // Emite un evento para seleccionar los checkboxes
    $this->emit("seleccionar-ventas-hoja-ruta", $this->ventas_hoja_ruta->pluck('id')->toArray());
  }
  
  
  
  
}
