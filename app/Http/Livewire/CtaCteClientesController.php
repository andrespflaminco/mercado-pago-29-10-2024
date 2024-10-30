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

use App\Models\sucursales;

////////////////////////
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\hoja_ruta;
use App\Models\User;

//////////////////////
use Carbon\Carbon;
use App\Models\bancos;
use App\Models\configuracion_ctas_ctes;

use App\Models\ClientesMostrador;
use App\Models\compras_proveedores;
use App\Models\detalle_compra_proveedores;
use DB;


use App\Traits\PagosTrait;

class CtaCteClientesController extends Component
{
  use WithPagination;
  use WithFileUploads;
  use PagosTrait; // Se actualiza pagos trait tambien

  public $saldos_iniciales,$name,$barcode,$cost,$price,$pago, $total_total, $estado_pago, $caja_seleccionada, $caja, $proveedor_elegido, $stock,$alerts,$categoryid, $codigo, $monto_total, $search, $image, $selected_id, $pageTitle,$componentName,$comercio_id,$data_Cart, $dateFrom, $dateTo, $Cart, $deuda,$metodo_pago_elegido, $product,$total, $itemsQuantity, $cantidad, $carrito, $qty, $detalle_cliente, $detalle_facturacion, $ventaId, $style, $style2, $pagos2, $estado2, $estado, $listado_hojas_ruta, $suma_monto, $suma_cash, $suma_deuda, $rec, $tot, $usuario,$tipo_pago,$monto_ap, $recargo_total,$total_pago,$NroVenta, $id_pago, $tipos_pago, $detalle_venta, $detalle_compra, $dci, $details, $saleId, $countDetails, $sumDetails, $formato_modal, $metodo_pago_agregar_pago, $fecha_ap, $fecha_editar, $detalle_proveedor;
  public $sum_si;
  public $ver_configuracion;
  
  private $pagination = 25;

  public $sucursales_elegidas;
  public $selectedSucursales = [];
  public $selectedSucursalesStock = [];
  public $selectedSucursalesCheckbox = [];
  public $estado_filtro;
  public $sucursales_agregan_pago;
  public $valor;
  public $MostrarOcultar;
  public $configuracion_valor, $configuracion_sucursales_agregan_pago;
  public $tipo_saldo;
  public $monto_minimo = null; // Inicialmente null para indicar sin filtro
  public $monto_maximo = null; // Inicialmente null para indicar sin filtro

    public function Filtros($mostrar)
    {   
        $this->MostrarOcultar = $this->MostrarOcultar == 'block' ? 'none' : 'block';
    
    }

  
  public function GetConfiguracionCtaCte(){
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    
    $configuracion_ctas_ctes = configuracion_ctas_ctes::where('comercio_id',$comercio_id)->first();
    
    if($configuracion_ctas_ctes == null){
    $this->valor = "por_sucursal";
    $this->sucursales_agregan_pago = 0;   
    $this->configuracion_valor = "por_sucursal";
    $this->configuracion_sucursales_agregan_pago = 0;   
    } else {
    $this->valor = $configuracion_ctas_ctes->valor;
    $this->sucursales_agregan_pago = $configuracion_ctas_ctes->sucursales_agregan_pago;  
    $this->configuracion_valor = $configuracion_ctas_ctes->valor;
    $this->configuracion_sucursales_agregan_pago = $configuracion_ctas_ctes->sucursales_agregan_pago;  
    }

  }
  
  public function UpdateConfiguracion(){
  $configuracion_ctas_ctes = configuracion_ctas_ctes::updateOrCreate(
      [
      'comercio_id' => $this->comercio_id
      ],
      [
      'valor' => $this->configuracion_valor ,
      'sucursales_agregan_pago' => $this->configuracion_sucursales_agregan_pago 
      ]);    
      
      $this->CerrarModalConfiguracion();
      $this->mount();
      $this->render();
  }
  
  
  public function CerrarModalConfiguracion(){
  $this->ver_configuracion = 0;    
  $this-> GetConfiguracionCtaCte();
  }
  


  public function mount()
  {
    $this->tipo_saldo = "all";
    $this->MostrarOcultar = "none";
    $this->ver_configuracion = 0;  
    $this->estado_filtro = 0;
    $this->lista_cajas_dia = [];
    $this->saldos_iniciales = [];  
    $this->caja = cajas::select('*')->where('estado',0)->where('user_id',Auth::user()->id)->max('id');
    $fecha_editar = Carbon::now()->format('d-m-Y');
    $this->fecha_ap = Carbon::now()->format('d-m-Y');
    $this->tipos_pago = [];
    $this->detalle_compra = [];
    $this->pagos2 = [];
    $this->detalle_proveedor = [];
    $this->dci = [];
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
   
    //31-5-2024
    $this->columnaOrden = 'id_cliente';
    $this->direccionOrden = 'asc';
    
    $sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
    ->select('users.name','sucursales.sucursal_id')
    ->where('sucursales.casa_central_id', auth()->user()->casa_central_user_id)
    ->where('eliminado',0)
    ->get();


    $this->GetConfiguracionCtaCte();
    
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    
    if($this->valor == "por_sucursal"){
    $this->selectedSucursales = [$comercio_id]; // Inicializar con el ID del usuario actual
    }

    if($this->valor == "compartido"){
    $this->selectedSucursales = [$comercio_id]; // Inicializar con el ID del usuario actual
    
    foreach ($sucursales as $sucursal) {
        $this->selectedSucursales[] = $sucursal->sucursal_id; // Agregar cada sucursal_id al array
    }
    }
    
    
    $this->sucursales_elegidas = auth()->user()->id;    
    $this->selectedSucursalesCheckbox[auth()->user()->id] = true;
    if(auth()->user()->id == auth()->user()->casa_central_user_id){$this->selectedSucursalesStock["0"] = true;} else {$this->selectedSucursalesStock[auth()->user()->id] = true;}

  }


//31-5-2024
  public function OrdenarColumna($columna)
    {
    if ($this->columnaOrden == $columna) {
        // Cambiar la dirección de orden si la columna es la misma
        $this->columnaOrden = $columna;
        $this->direccionOrden = $this->direccionOrden == 'asc' ? 'desc' : 'asc';
    } else {
        // Si es una columna diferente, cambiar a la nueva columna y establecer la dirección predeterminada
        $this->columnaOrden = $columna;
        $this->direccionOrden = 'asc';
        
       // dd($this->columnaOrden,$this->direccionOrden);
    }
    
    $this->render();
}

public function Filtro($value){
    $this->estado_filtro = $value;
}

public function AbrirModalConfiguracion(){
    $this->ver_configuracion = 1;  
}

public function ConfiguracionClientes($comercio_id){
  $info_sucursal = sucursales::where('sucursal_id',$comercio_id)->first();
      if($info_sucursal == null){return 0;} else {
          $solo_ver_clientes_propios = $info_sucursal->solo_ver_clientes_propios;
          return $solo_ver_clientes_propios;
  }
}
          
public function GetClientes($casa_central_id,$comercio_id){

$solo_ver_clientes_propios = $this->ConfiguracionClientes($comercio_id);


// Consulta para obtener todos los clientes con sus datos
$clientes_query = ClientesMostrador::select(
    'clientes_mostradors.id_cliente',
    'clientes_mostradors.id',
    'clientes_mostradors.nombre as nombre_cliente'
);

if($solo_ver_clientes_propios == 0){
$clientes_query = $clientes_query->where('clientes_mostradors.comercio_id', $casa_central_id);    
} else {
$clientes_query = $clientes_query->where('clientes_mostradors.creador_id', $comercio_id);        
} 

$clientes_query = $clientes_query->where('clientes_mostradors.id', '<>', 1)
->where('clientes_mostradors.eliminado', $this->estado_filtro);

if(auth()->user()->sucursal == 1){
    $clientes_query = $clientes_query->where('sucursal_id','0');
}


// Aplica el filtro de búsqueda si existe
if ($this->search) {
    $clientes_query = $clientes_query->where('clientes_mostradors.nombre', 'like', '%' . $this->search . '%');
}

$clientes = $clientes_query->get();
return $clientes;
}

public function GetDatosCtaCte($comercio_id, $valor)
{
    $casa_central_id = Auth::user()->casa_central_user_id;

    // Obtener clientes
    $clientes = $this->GetClientes($casa_central_id, $comercio_id);

    // Obtener deudas agrupadas por cliente
    $deudas_query = Sale::select(
        'sales.cliente_id',
        Sale::raw('SUM(CASE WHEN sales.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN sales.deuda ELSE 0 END) as deuda_30_dias'),
        Sale::raw('SUM(CASE WHEN sales.created_at >= DATE_SUB(NOW(), INTERVAL 61 DAY) AND sales.created_at < DATE_SUB(NOW(), INTERVAL 31 DAY) THEN sales.deuda ELSE 0 END) as deuda_60_dias'),
        Sale::raw('SUM(CASE WHEN sales.created_at < DATE_SUB(NOW(), INTERVAL 61 DAY) THEN sales.deuda ELSE 0 END) as deuda_mas_60_dias'),
        Sale::raw('SUM(sales.deuda) as total')
    )
    ->where('sales.status', '<>', 'Cancelado')
    ->where('sales.eliminado', 0)
    ->whereIn('sales.comercio_id', $this->selectedSucursales)
    ->groupBy('sales.cliente_id');

    $deudas = $deudas_query->get()->keyBy('cliente_id');

    // Obtener saldos iniciales agrupados por cliente
    $saldos_iniciales = saldos_iniciales::select(
        Sale::raw('SUM(saldos_iniciales.monto) as saldo_inicial_cuenta_corriente'),
        'referencia_id as cliente_id'
    );

    if ($this->valor == "por_sucursal") {
        $saldos_iniciales = $saldos_iniciales->whereIn('saldos_iniciales.sucursal_id', $this->selectedSucursales);
    } else {
        $saldos_iniciales = $saldos_iniciales->whereIn('saldos_iniciales.comercio_id', $this->selectedSucursales);
    }

    $saldos_iniciales = $saldos_iniciales->where('saldos_iniciales.tipo', 'cliente')
        ->where('saldos_iniciales.eliminado', 0)
        ->groupBy('referencia_id')
        ->get()
        ->keyBy('cliente_id');

    // Combina los resultados
    foreach ($clientes as $cliente) {
        $cliente_id = $cliente->id;

        if (isset($deudas[$cliente_id])) {
            $cliente->deuda_30_dias = $deudas[$cliente_id]->deuda_30_dias;
            $cliente->deuda_60_dias = $deudas[$cliente_id]->deuda_60_dias;
            $cliente->deuda_mas_60_dias = $deudas[$cliente_id]->deuda_mas_60_dias;
            $cliente->total = $deudas[$cliente_id]->total;
        } else {
            $cliente->deuda_30_dias = 0;
            $cliente->deuda_60_dias = 0;
            $cliente->deuda_mas_60_dias = 0;
            $cliente->total = 0;
        }

        if (isset($saldos_iniciales[$cliente_id])) {
            $cliente->saldo_inicial_cuenta_corriente = $saldos_iniciales[$cliente_id]->saldo_inicial_cuenta_corriente;
            $cliente->total = $cliente->total + $saldos_iniciales[$cliente_id]->saldo_inicial_cuenta_corriente;
        } else {
            $cliente->saldo_inicial_cuenta_corriente = 0;
        }
    }

    // Filtro por tipo de saldo
    if ($this->tipo_saldo != 'all') {
        if ($this->tipo_saldo == 0) {
            $clientes = $clientes->filter(fn($cliente) => $cliente->total == 0);
        } elseif ($this->tipo_saldo == 1) {
            $clientes = $clientes->filter(fn($cliente) => $cliente->total > 0);
        } elseif ($this->tipo_saldo == 2) {
            $clientes = $clientes->filter(fn($cliente) => $cliente->total < 0);
        }
    }

    // Filtrar por rango de monto mínimo y máximo
    if (is_numeric($this->monto_minimo) && is_numeric($this->monto_maximo)) {
        $clientes = $clientes->filter(fn($cliente) => $cliente->total >= $this->monto_minimo && $cliente->total <= $this->monto_maximo);
    } elseif (is_numeric($this->monto_minimo)) {
        $clientes = $clientes->filter(fn($cliente) => $cliente->total >= $this->monto_minimo);
    } elseif (is_numeric($this->monto_maximo)) {
        $clientes = $clientes->filter(fn($cliente) => $cliente->total <= $this->monto_maximo);
    }

    // Ordenar los clientes según la columna y dirección especificadas
    $clientes = $clientes->sortBy(function($cliente) {
        return $cliente->{$this->columnaOrden};
    }, SORT_REGULAR, $this->direccionOrden === 'desc');

    return $clientes;
}

public function GetDatosCtaCteOld($comercio_id,$valor){

$casa_central_id = Auth::user()->casa_central_user_id;

$clientes = $this->GetClientes($casa_central_id,$comercio_id);

//dd($this->selectedSucursales);

// Consulta para obtener las deudas agrupadas por cliente
$deudas_query = Sale::select(
    'sales.cliente_id',
    Sale::raw('SUM(CASE WHEN sales.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN sales.deuda ELSE 0 END) as deuda_30_dias'),
    Sale::raw('SUM(CASE WHEN sales.created_at >= DATE_SUB(NOW(), INTERVAL 61 DAY) AND sales.created_at < DATE_SUB(NOW(), INTERVAL 31 DAY) THEN sales.deuda ELSE 0 END) as deuda_60_dias'),
    Sale::raw('SUM(CASE WHEN sales.created_at < DATE_SUB(NOW(), INTERVAL 61 DAY) THEN sales.deuda ELSE 0 END) as deuda_mas_60_dias'),
    Sale::raw('SUM(sales.deuda) as total')
)
->where('sales.status', '<>', 'Cancelado')
->where('sales.eliminado', 0)
->whereIn('sales.comercio_id', $this->selectedSucursales)
//->where('sales.comercio_id',$comercio_id)
->groupBy('sales.cliente_id');

$deudas = $deudas_query->get()->keyBy('cliente_id');

// Consulta para obtener los saldos iniciales agrupados por cliente
$saldos_iniciales = saldos_iniciales::select(
    Sale::raw('SUM(saldos_iniciales.monto) as saldo_inicial_cuenta_corriente'),
    'referencia_id as cliente_id'
);
//dd($this->valor,$this->selectedSucursales);
if($this->valor == "por_sucursal"){
$saldos_iniciales = $saldos_iniciales->whereIn('saldos_iniciales.sucursal_id', $this->selectedSucursales);
} else {
$saldos_iniciales = $saldos_iniciales->whereIn('saldos_iniciales.comercio_id', $this->selectedSucursales);    
}

$saldos_iniciales = $saldos_iniciales->where('saldos_iniciales.tipo', 'cliente')
->where('saldos_iniciales.eliminado', 0)
->groupBy('referencia_id')
->get()
->keyBy('cliente_id');


// Combina los resultados
foreach ($clientes as $cliente) {
    $cliente_id = $cliente->id;

    // Agrega las deudas al cliente si existen
    if (isset($deudas[$cliente_id])) {
        $cliente->deuda_30_dias = $deudas[$cliente_id]->deuda_30_dias;
        $cliente->deuda_60_dias = $deudas[$cliente_id]->deuda_60_dias;
        $cliente->deuda_mas_60_dias = $deudas[$cliente_id]->deuda_mas_60_dias;
        $cliente->total = $deudas[$cliente_id]->total;
    } else {
        $cliente->deuda_30_dias = 0;
        $cliente->deuda_60_dias = 0;
        $cliente->deuda_mas_60_dias = 0;
        $cliente->total = 0;
    }

    // Agrega el saldo inicial al cliente si existe
    if (isset($saldos_iniciales[$cliente_id])) {
        $cliente->saldo_inicial_cuenta_corriente = $saldos_iniciales[$cliente_id]->saldo_inicial_cuenta_corriente;
        $cliente->total = $cliente->total + $saldos_iniciales[$cliente_id]->saldo_inicial_cuenta_corriente;
    } else {
        $cliente->saldo_inicial_cuenta_corriente = 0;
        $cliente->total = $cliente->total; 
    }
    
}


    // Ordenar los clientes según la columna y dirección especificadas
    $clientes = $clientes->sortBy(function($cliente) {
        return $cliente->{$this->columnaOrden};
    }, SORT_REGULAR, $this->direccionOrden === 'desc');


return $clientes;
    
}

/*
public function GetDatosCtaCteOld2($comercio_id){
          $datos_cta_cte = ClientesMostrador::leftjoin('sales','sales.cliente_id','clientes_mostradors.id')
        ->select(
          Sale::raw('SUM(CASE WHEN sales.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN sales.deuda ELSE 0 END) as deuda_30_dias'),
          Sale::raw('SUM(CASE WHEN sales.created_at >= DATE_SUB(NOW(), INTERVAL 61 DAY) AND sales.created_at < DATE_SUB(NOW(), INTERVAL 31 DAY) THEN sales.deuda ELSE 0 END) as deuda_60_dias'),
          Sale::raw('SUM(CASE WHEN sales.created_at < DATE_SUB(NOW(), INTERVAL 61 DAY) THEN sales.deuda ELSE 0 END) as deuda_mas_60_dias'),
          Sale::raw('SUM(sales.deuda) as total'),
          'clientes_mostradors.id_cliente',
          'clientes_mostradors.id',
          'clientes_mostradors.nombre as nombre_cliente')
		  ->where('clientes_mostradors.comercio_id', $comercio_id)
//		  ->where('sales.status','<>', 'Cancelado')
//		  ->where('sales.eliminado', 0)
		  ->where('clientes_mostradors.id','<>',1);

      if($this->search) {
        $datos_cta_cte = $datos_cta_cte->where('clientes_mostradors.nombre', 'like', '%' . $this->search . '%');
      }

      $datos_cta_cte = $datos_cta_cte
      ->groupBy('clientes_mostradors.id_cliente','clientes_mostradors.id','clientes_mostradors.nombre')
      ->orderBy('clientes_mostradors.id','desc')->get();

      $saldos_iniciales = saldos_iniciales::select(
          Sale::raw('SUM(saldos_iniciales.monto) as saldo_inicial_cuenta_corriente'),'referencia_id as cliente_id')
          ->where('saldos_iniciales.tipo','cliente')
          ->where('saldos_iniciales.eliminado',0)
          ->where('saldos_iniciales.comercio_id',$comercio_id)
          ->groupBy('referencia_id')
          ->orderBy('referencia_id','desc')
          ->get();
      
      foreach ($datos_cta_cte as $dato_cta_cte) {
        foreach ($saldos_iniciales as $saldoInicial) {
            // Verificar si el ID del cliente mostrador coincide con el cliente_id del saldo inicial
            if ($dato_cta_cte->id == $saldoInicial->cliente_id) {
                // Agregar el valor de saldo_inicial_cuenta_corriente al objeto ClientesMostrador
                $dato_cta_cte->saldo_inicial_cuenta_corriente = $saldoInicial->saldo_inicial_cuenta_corriente;
                // Romper el bucle interior ya que hemos encontrado el saldo inicial para este cliente mostrador
                break;
            }
        }
        }
        

}
*/

public function AplicarElegirSucursal(){
    // Obtener las claves donde el valor es true
    $clavesTrue = array_keys($this->selectedSucursalesCheckbox, true);
    
    foreach($this->selectedSucursalesCheckbox as $key => $value){
      $this->selectedSucursalesCheckbox[$key] = $value;  
    }
      
    // Reindexar el array
    $this->selectedSucursales = array_values($clavesTrue);
    $selectedSucursalesStock = array_values($clavesTrue);
    
    // Reemplazar 295 con 0 si está presente en el array
    foreach ($selectedSucursalesStock as $key => $value) {
        if ($value == $this->casa_central_id) {
            $selectedSucursalesStock[$key] = 0;
        }
    }
    
    $this->selectedSucursalesStock = $selectedSucursalesStock;
    $this->sucursales_elegidas = implode(",",$this->selectedSucursales);
}


  public function render()
  {
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    
    $this->comercio_id = $comercio_id;
    $this->casa_central_id = Auth::user()->casa_central_user_id;
    
    $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')->where('casa_central_id', $this->comercio_id)->where('eliminado',0)->get();

    $this->caja_seleccionada = cajas::find($this->caja);
    
    $this->ultimas_cajas = cajas::where('comercio_id', $comercio_id)->where('eliminado',0)->orderby('created_at','desc')->limit(5)->get();
    
    /*
    $this->tipos_pago = bancos::where('bancos.comercio_id', $this->casa_central_id)
    ->orderBy('bancos.nombre','asc')->get();
    */
    $this->tipo_pago = $this->getBancos($this->casa_central_id);
    
    if($this->dateFrom !== '' || $this->dateTo !== '')
    {
      $from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
      $to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';
    }

    $datos_cta_cte = $this->GetDatosCtaCte($comercio_id,$this->valor);

    $metodo_pagos = $this->getBancos($comercio_id);

    return view('livewire.ctacte-clientes.component',[
      'data' => $datos_cta_cte,
      'detalle_compra' => $this->detalle_compra,
      'metodo_pago' => $metodo_pagos,
      'categories' => Category::orderBy('name','asc')->where('comercio_id', 'like', $comercio_id)->get(),
      'almacenes' => seccionalmacen::orderBy('nombre','asc')->where('comercio_id', 'like', $comercio_id)->get(),
      'prov' => proveedores::orderBy('nombre','asc')->where('comercio_id', 'like', $comercio_id)->get()
    ])
    ->extends('layouts.theme-pos.app')
    ->section('content');
  }


  public function MontoPagoEditarPago($value)
  {
  $metodo_pago = metodo_pago::find($this->metodo_pago_agregar_pago);

  $this->MontoPagoEditarPago = $value;

  $this->recargo = $metodo_pago->recargo/100;

  $this->recargo_total = $this->MontoPagoEditarPago * $this->recargo;

  $this->total_pago = $this->recargo_total + $this->MontoPagoEditarPago;

  }

public function MostrarPagos() {
  $this->estado = "display: block;";
  $this->estado2 = "display: none;";
}


     function AgregarPago($id_pago) {

       $this->emit('cerrar-factura','details loaded');

       $this->emit('agregar-pago','details loaded');

       $this->caja_pago = cajas::select('*')->where('id', $this->caja)->get();

       $this->id_pago = $id_pago;

       $this->formato_modal = 0;

     }



    public function CerrarFactura() {
   $this->emit('cerrar-factura','details loaded');
   }



     function EditPago($id_pago) {


       $this->CerrarFactura();

       $this->emit('agregar-pago','details loaded');

       $this->formato_modal = 1;


       $this->id_pago = $id_pago;

       $pagos = pagos_facturas::find($id_pago);

       $this->caja = $pagos->caja;

       $this->metodo_pago_agregar_pago = $pagos->metodo_pago;

       $metodo_pago = metodo_pago::find($this->metodo_pago_agregar_pago);

     $this->tipo_pago = $metodo_pago->cuenta;

      $this->monto_ap = $pagos->monto_compra;

       $this->fecha_ap = Carbon::parse($pagos->created_at)->format('d-m-Y');


       $this->total_pago = $this->monto_ap;



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

function CerrarAgregarPago($proveedor_id) {

  $this->emit('hide-modal-saldos-iniciales','details loaded');
  $this->ResetPago();

  $this->RenderSaldoInicial($proveedor_id);

}


public function RenderSaldoInicial($value){
$this->selected_id = $value;

$this->saldos_iniciales = saldos_iniciales::join('bancos','bancos.id','saldos_iniciales.metodo_pago')
->join('users','users.id','saldos_iniciales.sucursal_id')
->select('saldos_iniciales.*','bancos.nombre as nombre_banco','users.name as nombre_sucursal')
->where("saldos_iniciales.tipo","cliente");

if($this->valor == "por_sucursal"){
$this->saldos_iniciales = $this->saldos_iniciales->whereIn('saldos_iniciales.sucursal_id', $this->selectedSucursales);
} else {
$this->saldos_iniciales = $this->saldos_iniciales->whereIn('saldos_iniciales.comercio_id', $this->selectedSucursales);    
}

$this->saldos_iniciales = $this->saldos_iniciales->where("saldos_iniciales.referencia_id",$value)
->where('saldos_iniciales.eliminado',0)
->get();

$this->sum_si = $this->saldos_iniciales->sum('monto');

$this->emit("show-modal-saldos-iniciales",$value);    
}

public function CerrarSaldoInicial(){
$this->emit("hide-modal-saldos-iniciales","");        
}



public function ModalAgregarEditarPago($valor,$cliente_id){

//dd($valor);
$this->formato_modal = $valor;
$this->id_pago = $valor;
$this->selected_id = $cliente_id;

if(0 < $valor){
$value = saldos_iniciales::where("tipo","cliente")->where("id",$valor)->first();  
$this->caja = $value->caja_id;
$this->caja_seleccionada = cajas::find($this->caja);

if($value->concepto == "Cobro"){
$this->monto_ap = abs($value->monto);     
} else {
$this->monto_ap = $value->monto;     
}

$this->metodo_pago_agregar_pago = $value->metodo_pago;
} else {
$this->monto_ap = 0; 
$this->caja = null;
$this->metodo_pago_agregar_pago = 1;
}

$this->emit("show-agregar-editar","");        
}

function CreatePago(){
    $this->CrearPagoSaldoInicial('cliente',$this->selected_id,$this->comercio_id,$this->monto_ap,$this->metodo_pago_agregar_pago,$this->caja);
    $this->RenderSaldoInicial($this->selected_id);
	$this->emit("hide-agregar-editar","");
}

public function ActualizarPago($pago_id){
    $this->ActualizarPagoSaldoInicial($pago_id,'cliente',$this->selected_id,$this->comercio_id,$this->monto_ap,$this->metodo_pago_agregar_pago,$this->caja);
    $this->RenderSaldoInicial($this->selected_id);
	$this->emit("hide-agregar-editar","");
}

public function DeletePagoSaldo($pago_id) {
    $this->DeletePagoSaldoInicial($pago_id,'cliente',$this->selected_id);	
	$this->CerrarSaldoInicial();
    $this->RenderSaldoInicial($this->selected_id);
}


public function ExportarReporte() {
    
    $search = $this->search ?? 0;
    $url = 'report-cta-cte-clientes/excel/'. $search .'/'. Carbon::now()->format('d_m_Y_H_i_s');
    return redirect($url);

}

//18-5-2024
public function GetPlazoAcreditacionPago($id){
    return $id == 1 ? 1 : 0;
}

public function ElegirCaja($caja_id)
{
$this->caja = $caja_id;
$this->emit('listado-cajas-hide','close');
}

public function CambioCaja() {

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $this->fecha_pedido_desde = $this->fecha_ap.' 00:00:00';

  $this->fecha_pedido_hasta = $this->fecha_ap.' 23:59:50';

  $this->emit('listado-cajas-show','');

  $this->lista_cajas_dia = cajas::where('comercio_id', $comercio_id)->where('eliminado',0)->whereBetween('fecha_inicio',[$this->fecha_pedido_desde, $this->fecha_pedido_hasta])->get();

}   
   
public function ModalAbrirCaja() {
//$this->emit('agregar-pago-hide','');
$this->emit('abrir-caja-show','');
}



  public function AbrirCajaGuardar() {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $ultimo = cajas::where('cajas.comercio_id', 'like', $this->sucursal_id)->where('eliminado',0)->select('cajas.nro_caja')->latest('nro_caja')->first();

    if($ultimo != null)
    $nro = $ultimo->nro_caja + 1;
    else
    $nro = 1;

    $cajas = cajas::create([
      'user_id' => Auth::user()->id,
      'comercio_id' => $this->sucursal_id,
      'nro_caja' => $nro,
      'monto_inicial' => $this->monto_inicial,
      'estado' => '0',
      'fecha_inicio' => Carbon::now()

    ]);

    $this->caja = $cajas->id;

    $this->caja_seleccionada = $cajas->id;

  }    
  
  
   public function getBancos($comercio_id){
    return bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
    ->where('bancos_muestra_sucursales.muestra', 1)
    ->where('bancos_muestra_sucursales.sucursal_id', $comercio_id)
    ->select('bancos.*')
    ->orderBy('bancos.nombre','asc')
    ->get();
    
   
  }
    
}
