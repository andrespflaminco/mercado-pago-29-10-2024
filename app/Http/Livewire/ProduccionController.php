<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Sale;
use App\Models\produccion;
use App\Models\historico_stock;
use App\Models\receta;
use App\Models\insumo;
use App\Models\productos_stock_sucursales;
use App\Models\unidad_medida_relacion;
use App\Models\unidad_medida;
use App\Models\historico_stock_insumo;
use App\Models\tipo_unidad_medida;
use App\Models\produccion_detalle;
use App\Models\produccion_detalles_insumos;
use App\Models\Product;
use App\Models\Category;
use App\Models\seccionalmacen;
use App\Models\metodo_pago;
use App\Models\ClientesMostrador;
use Illuminate\Support\Facades\Auth;
use App\Models\SaleDetail;
use Carbon\Carbon;
use App\Models\Estados;

use App\Traits\ProduccionTrait;

class ProduccionController extends Component
{

use ProduccionTrait;

  public $componentName, $data, $details, $sumDetails, $countDetails, $sum, $produccion_detalle_id, $totales_ver, $cantidad_tickets, $ticket_promedio,
  $reportType, $userId, $dateFrom, $dateTo,$id_produccion_detalle, $saleId, $comercio_id, $clienteId, $selected_id, $suma_totales, $id_producto, $suma_cantidades, $pendiente, $entregado, $cancelado, $fabricacion, $terminado;



  public $clientesSelectedId;
  public $UsuarioSelectedName;
  public $metodopagoSelectedName;
  public $productoSelectedName;
  public $categoriaSelectedName;
  public $almacenSelectedName;
  public $estadoSelectedName;



  public $Usuario_SelectedValues;
  public $usuarioSeleccionado;
  public $ClienteSeleccionado;
  public $metodopagoSeleccionado;
  public $productoSeleccionado;
  public $categoriaSeleccionado;
  public $almacenSeleccionado;
  public $estadoSeleccionado;

  public $clientesSelectedName = [];

  public array $locationUsers = [];
  public array $usuario_seleccionado = [];
  public array $metodopago_seleccionado = [];
  public array $producto_seleccionado = [];
  public array $categoria_seleccionado = [];
  public array $almacen_seleccionado = [];
  public array $estado_seleccionado = [];



  protected $listeners = ['locationUsersSelected','UsuarioSelected','Usuario_Selected','metodopagoSelected','productoSelected','categoriaSelected','estadoSelected','almacenSelected'];


  public function UsuarioSelected($UsuarioSelectedValues)
  {
    $this->usuario_seleccionado = $UsuarioSelectedValues;


  }

  public function estadoSelected($estadoSelectedValues)
  {
    $this->estado_seleccionado = $estadoSelectedValues;

  }

  public function locationUsersSelected($locationUsersValues)
  {
    $this->locationUsers = $locationUsersValues;
  }


  public function metodopagoSelected($metodopagoValues)
  {
    $this->metodopago_seleccionado = $metodopagoValues;
  }


  public function productoSelected($productoValues)
  {
    $this->producto_seleccionado = $productoValues;
  }


  public function categoriaSelected($categoriaValues)
  {
    $this->categoria_seleccionado = $categoriaValues;
  }


  public function almacenSelected($almacenValues)
  {
    $this->almacen_seleccionado = $almacenValues;
  }

  public function mount()
  {
      $this->cambios_stock_producto = 0;
      $this->cambios_stock_insumos = 0;
      
      $this->componentName ='Producción';
      $this->data =[];
      $this->details =[];
      $this->sumDetails =0;
      $this->countDetails =0;
      $this->reportType =0;
      $this->userId =0;
      $this->saleId =0;

      $this->usuarioSeleccionado = 0;
      $this->ClienteSeleccionado = 0;
      $this->metodopagoSeleccionado = 0;
      $this->productoSeleccionado = 0;
      $this->categoriaSeleccionado = 0;
      $this->almacenSeleccionado = 0;

      $this->clienteId =0;
      $this->clientesSelectedName = [];
      $this->dateFrom = '01-01-2000';
      $this->dateTo = Carbon::now()->format('d-m-Y');



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
        
    $this->GetListadoProduccion();
    $this->SetContador();
    
    $usuario_id = Auth::user()->id;

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

      return view('livewire.produccion-detalle.component', [
        'users' => User::orderBy('name','asc')
        ->where('users.comercio_id', $comercio_id)
        ->orWhere('users.id', $usuario_id)
        ->get(),
        'clientes' => ClientesMostrador::where('comercio_id', $comercio_id)
        ->orderBy('nombre','desc')
        ->get(),
        'seccion_almacen' => seccionalmacen::where('seccionalmacens.comercio_id', 'like', $comercio_id)
        ->orderBy('nombre','desc')->get(),
        'products' => Product::where('products.comercio_id', 'like', $comercio_id)
        ->orderBy('name','desc')->get(),
        'metodo_pago' => metodo_pago::where('metodo_pagos.comercio_id', 'like', $comercio_id)
        ->orderBy('nombre','desc')->get(),
        'categoria' => Category::where('categories.comercio_id', 'like', $comercio_id)
        ->orderBy('name','desc')->get(),
        'estados' => Estados::orderBy('nombre','desc')->get()
      ])
      ->extends('layouts.theme-pos.app')
  ->section('content');
  }


  public function GetListadoProduccion()
  {

  if($this->dateFrom !== '' || $this->dateTo !== '')
  {
    $from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
    $to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';

  }

        if(Auth::user()->comercio_id != 1)
            $comercio_id = Auth::user()->comercio_id;
            else
            $comercio_id = Auth::user()->id;
        
        $this->data = produccion_detalle::join('produccions','produccions.id','produccion_detalles.produccion_id')
        ->join('estados','estados.id','produccions.estado')
        ->join('products as p','p.id','produccion_detalles.producto_id')
        ->join('users','users.id','produccions.user_id')
        ->join('categories','categories.id','p.category_id')
        ->select('produccions.inicio_produccion','produccion_detalles.*','categories.name as nombre_categoria','users.name as nombre_usuario','p.barcode','p.name as nombre_producto','estados.id as id_estado','estados.nombre as nombre_estado');
        
        if($this->estado_seleccionado)
        {
        
        $this->data= $this->data->whereIn('estados.id',$this->estado_seleccionado);
        
        }
        
        if($this->producto_seleccionado)
        {
        $this->data= $this->data->whereIn('produccion_detalles.producto_id',$this->producto_seleccionado);
        }
        if($this->categoria_seleccionado)
        {
        
          $this->data= $this->data->whereIn('p.category_id',$this->categoria_seleccionado);
        
        }
        
        $this->data= $this->data->where('produccions.comercio_id', $comercio_id)
        ->whereBetween('produccion_detalles.created_at', [$from, $to])
        ->orderBy('produccion_detalles.produccion_id','desc')
        ->get();

        }

    public function SetContador(){
    $this->suma_cantidades = $this->data->sum('cantidad');
    $this->pendiente = $this->data->where('id_estado','1')->sum('cantidad');
    $this->fabricacion = $this->data->where('id_estado','2')->sum('cantidad');
    $this->terminado = $this->data->where('id_estado','3')->sum('cantidad');
    $this->cancelado_s = $this->data->where('id_estado','5')->sum('cantidad');
    $this->cancelado_c = $this->data->where('id_estado','6')->sum('cantidad');        
    }

    
    public function UpdateEstadoProduccion($estado_id,$origen){
        $this->UpdateEstadoProduccionTrait($estado_id,$origen,$this->id_producto);
    } 

    public function getDetails($saleId)
    {

        $this->id_producto = $saleId;


        $this->emit('show-modal','details loaded');

    }
    
       

    
}
