<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Sale;
use App\Models\Product;
use App\Models\sucursales;
use App\Models\Category;
use App\Models\ColumnConfiguration;
use App\Models\seccionalmacen;
use Livewire\WithPagination;
use App\Models\metodo_pago;
use App\Models\ClientesMostrador;
use Illuminate\Support\Facades\Auth;
use App\Models\SaleDetail;
use Carbon\Carbon;


class ReportsdetalleController extends Component
{


    use WithPagination;

    public $componentName, $details, $sumDetails, $countDetails,$estado_entrega_search, $sum, $totales_ver, $cantidad_tickets, $ticket_promedio,
    $reportType, $userId, $dateFrom, $dateTo, $saleId, $sucursal_id, $comercio_id, $clienteId, $selected_id, $suma_totales, $suma_cantidades;



    public $clientesSelectedId;
    public $UsuarioSelectedName;
    public $metodopagoSelectedName;
    public $productoSelectedName;
    public $categoriaSelectedName;
    public $almacenSelectedName;



    public $Usuario_SelectedValues;
    public $usuarioSeleccionado;
    public $ClienteSeleccionado;
    public $metodopagoSeleccionado;
    public $productoSeleccionado;
    public $categoriaSeleccionado;
    public $almacenSeleccionado;

    public $clientesSelectedName = [];

    public array $locationUsers = [];
    public array $usuario_seleccionado = [];
    public array $metodopago_seleccionado = [];
    public array $producto_seleccionado = [];
    public array $categoria_seleccionado = [];
    public array $almacen_seleccionado = [];



    private $pagination = 25;

    public function paginationView()
    {
      return 'vendor.livewire.bootstrap';
    }

    protected $listeners = ['locationUsersSelected','UsuarioSelected','Usuario_Selected','metodopagoSelected','productoSelected','categoriaSelected','almacenSelected'];


    public function UsuarioSelected($UsuarioSelectedValues)
    {
      $this->usuario_seleccionado = $UsuarioSelectedValues;


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
    public function VerOpcionesPantalla($value) {
    
    if($value == 1) {$this->ver_opciones_pantalla = 0;}
    if($value == 0) {$this->ver_opciones_pantalla = 1;}
    }

    public function mount()
    {
        
        $this->estado_entrega_search = "all";
        $this->ver_opciones_pantalla = 0;
        $this->componentName ='Reportes de Ventas';
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

        $this->loadColumns();

    }


    public function loadColumns()
    {
        $columns = ColumnConfiguration::where(['user_id' => Auth::id(), 'table_name' => 'reports_detalle'])
            ->pluck('is_visible', 'column_name')
            ->toArray();

        // Todas las columnas disponibles
        $allColumns = [
        
        'created_at' => true,
        'nro_venta' => true,
        'entrega' => true,
        'barcode' => true,
        'product' => true,
        'nombre_categoria' => true,
        'nombre_cliente' => true,
        'nombre_usuario' => true,
        'price' => true,
        'quantity' => true,
        'iva' => true,
        'recargo' => true,
        'descuento' => true,
        'descuento_promo' => true,
        'costo' => true,
        'total' => true,
        'almacen' => true,
        'nombre_banco' => true,
        'nombre_metodo_pago' => true,
        'cantidad_unidad_medida' => true,
        'tipo_unidad_medida' => true
        ];


        // Fusionar columnas personalizadas con todas las columnas disponibles
        $this->columns = array_merge($allColumns, $columns);
    }
    
        public function toggleColumnVisibility($columnName)
    {
        //dd($this->columns[$columnName]);
        $isVisible = ($this->columns[$columnName] ?? false);
        ColumnConfiguration::updateOrCreate(
            ['user_id' => Auth::id(), 'table_name' => 'reports_detalle', 'column_name' => $columnName],
            ['is_visible' => $isVisible]
        );

        $this->columns[$columnName] = $isVisible;
    }
    
    public function render()
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

$this->comercio_id = $comercio_id;
  $this->tipo_usuario = User::find(Auth::user()->id);

  if($this->tipo_usuario->sucursal != 1) {
  $this->casa_central_id = $comercio_id;
  } else {

  $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
  $this->casa_central_id = $this->casa_central->casa_central_id;
  }

    if($this->sucursal_id != null) {
      $this->sucursal_id = $this->sucursal_id;
    } else {
      $this->sucursal_id = $comercio_id;
    }
    
    
    $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')->where('sucursales.eliminado',0)->where('casa_central_id', $comercio_id)->get();


  $reportes = SaleDetail::join('products as p','p.id','sale_details.product_id')
  ->leftjoin('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
  ->leftjoin('sales as sa','sa.id','sale_details.sale_id')
  ->leftjoin('metodo_pagos as m','m.id','sa.metodo_pago')
  ->leftjoin('bancos','bancos.id','m.cuenta')
  ->leftjoin('users','users.id','sa.user_id')
  ->leftjoin('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
  ->leftjoin('categories','categories.id','p.category_id')
  ->leftjoin('sales','sales.id','sale_details.sale_id')
  ->select('bancos.nombre as nombre_banco','sa.nro_venta','sale_details.tipo_unidad_medida','sale_details.cost','sale_details.iva','sale_details.product_barcode as barcode','categories.name as nombre_categoria','cl.nombre as nombre_cliente','users.name as nombre_usuario','sale_details.id','sale_details.descuento','sale_details.descuento_promo','sale_details.cantidad_promo','m.nombre as nombre_metodo_pago','sale_details.price','sale_details.quantity',SaleDetail::raw('IFNULL(sale_details.recargo,0) as recargo'),'sale_details.product_name as product','s.nombre as almacen','sale_details.created_at',
  SaleDetail::raw('(CASE WHEN sale_details.estado = 1 THEN "Entregado" ELSE "Pendiente de entrega" END) as estado'))
  ->where('sale_details.comercio_id',$this->sucursal_id);

                
  if($this->categoria_seleccionado) {
  $reportes = $reportes->whereIn('categories.id',$this->categoria_seleccionado);
  }

  if($this->almacen_seleccionado) {
  $reportes = $reportes->whereIn('sale_details.seccionalmacen_id',$this->almacen_seleccionado);
  }

  if($this->usuario_seleccionado){
    $reportes = $reportes->whereIn('sa.user_id',$this->usuario_seleccionado);

  }

  if($this->locationUsers) {
  $reportes = $reportes->whereIn('sale_details.cliente_id',$this->locationUsers);

  }

  if($this->metodopago_seleccionado){
  $reportes = $reportes->whereIn('sale_details.metodo_pago',$this->metodopago_seleccionado);

  }

  if($this->producto_seleccionado)
  {
  $reportes = $reportes->whereIn('sale_details.product_id',$this->producto_seleccionado);

  }
  if($this->estado_entrega_search != "all")
  {
  $reportes = $reportes->where('sale_details.estado',$this->estado_entrega_search);

  }

  $reportes = $reportes->where('sale_details.eliminado',0)
  ->where('sales.eliminado',0)
  ->where('sales.status','<>',4)
  ->whereBetween('sale_details.created_at', [$from, $to])
  ->orderBy('sale_details.sale_id','desc')
  ->paginate($this->pagination);

   //dd($reportes);
    
  $reportes_total = SaleDetail::join('sales','sales.id','sale_details.sale_id')
  ->join('products','products.id','sale_details.product_id')
  ->select(SaleDetail::raw('SUM((sale_details.price*sale_details.quantity)   ) as total'),SaleDetail::raw('SUM(sale_details.quantity) as cantidad'),SaleDetail::raw('SUM( ( (sale_details.price*sale_details.quantity) - sale_details.descuento + sale_details.recargo )*(sale_details.iva)) as iva'),SaleDetail::raw('SUM(sale_details.recargo) as recargo'),SaleDetail::raw('SUM(sale_details.descuento) as descuento'),SaleDetail::raw('SUM(sale_details.descuento_promo * sale_details.cantidad_promo) as descuento_promo'))
  ->where('sale_details.comercio_id',$this->sucursal_id);

  if($this->categoria_seleccionado) {
  $reportes_total = $reportes_total->whereIn('products.category_id',$this->categoria_seleccionado);
  }


  if($this->almacen_seleccionado) {
  $reportes_total = $reportes_total->whereIn('sale_details.seccionalmacen_id',$this->almacen_seleccionado);
  }

  if($this->usuario_seleccionado){
    $reportes_total = $reportes_total->whereIn('sales.user_id',$this->usuario_seleccionado);

  }

  if($this->locationUsers) {
  $reportes_total = $reportes_total->whereIn('sale_details.cliente_id',$this->locationUsers);

  }

  if($this->metodopago_seleccionado){
  $reportes_total = $reportes_total->whereIn('sale_details.metodo_pago',$this->metodopago_seleccionado);

  }

  if($this->producto_seleccionado)
  {
  $reportes_total = $reportes_total->whereIn('sale_details.product_id',$this->producto_seleccionado);

  }

  $reportes_total = $reportes_total->where('sale_details.eliminado',0)
  ->where('sales.status','<>',4)
  ->where('sales.eliminado',0)
  ->whereBetween('sale_details.created_at', [$from, $to])
  ->first();

  $this->suma_cantidades = $reportes_total->cantidad;

  $this->suma_totales = $reportes_total->total;

  $this->suma_iva = $reportes_total->iva;

  $this->suma_recargo = $reportes_total->recargo;

  $this->suma_descuento = $reportes_total->descuento;

  $this->suma_descuento_promo = $reportes_total->descuento_promo;

      $usuario_id = Auth::user()->id;

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

        return view('livewire.reportsdetalle.component', [
          'users' => User::orderBy('name','asc')
          ->where('users.comercio_id', $this->sucursal_id)
          ->orWhere('users.id', $usuario_id)
          ->get(),
          'data' => $reportes,
          'clientes' => ClientesMostrador::where('comercio_id', $this->sucursal_id)
          ->orderBy('nombre','asc')
          ->get(),
          'seccion_almacen' => seccionalmacen::where('seccionalmacens.comercio_id', 'like', $comercio_id)
          ->orWhere('seccionalmacens.comercio_id', $this->casa_central_id)
          ->orderBy('nombre','asc')->get(),
          'products' => Product::where('products.eliminado',0)
          ->where('products.comercio_id', $this->casa_central_id)
          ->orderBy('name','asc')->get(),
          'metodo_pago' => metodo_pago::join('bancos','bancos.id','metodo_pagos.cuenta')
          ->join('metodo_pagos_muestra_sucursales','metodo_pagos_muestra_sucursales.metodo_id','metodo_pagos.id')
          ->select('metodo_pagos.*','bancos.nombre as nombre_banco')
          ->where('metodo_pagos_muestra_sucursales.sucursal_id', $this->sucursal_id)
          ->orderBy('metodo_pagos.nombre','asc')->get(),
          'categoria' => Category::where('categories.comercio_id', 'like', $this->sucursal_id)
          ->orWhere('categories.comercio_id', $this->casa_central_id)
          ->orderBy('name','asc')->get()
        ])
        ->extends('layouts.theme-pos.app')
    ->section('content');
    }


    public function ExportarReporte($url) {

    return redirect('report-detalle/excel/'. $url .'/'. Carbon::now()->format('d_m_Y_H_i_s'));

}


  public function ElegirSucursal($sucursal_id) {

  	$this->sucursal_id = $sucursal_id;

  }

}
