<?php

namespace App\Http\Livewire;

use App\Services\CartMovimiento;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Session;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\proveedores;
use App\Models\ClientesMostrador;
use App\Models\Category;
use App\Models\metodo_pago;
use App\Models\movimiento_stocks;
use App\Models\movimiento_stocks_detalles;
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
use Carbon\Carbon;

use Barryvdh\DomPDF\Facade as PDF;

class MovimientoStockResumenController extends Component
{
  use WithPagination;
  use WithFileUploads;

  public $name,$barcode,$cost,$price,$pago, $ventaId,$style, $estado, $total_venta, $metodos, $proveedor_id, $caja, $referencia_variacion, $stock,$alerts,$categoryid, $codigo, $monto_total, $search, $image, $product_id, $pageTitle,$componentName,$comercio_id,$data_Cart, $observaciones, $nombre_sucursal_origen, $query_product, $products_s , $Cart, $deuda,$metodo_pago_elegido, $product,$total, $iva, $iva_general, $itemsQuantity, $cantidad, $carrito, $qty, $sucursal_origen, $tipo_factura, $numero_factura, $query, $tipo_pago, $nombre_sucursal_destino, $query_id, $vigencia, $dateFrom, $dateTo;
  private $pagination = 25;
  public $NroVenta;

  public $productos_variaciones_datos = [];
  
  public function paginationView()
  {
      return 'vendor.livewire.bootstrap';
  }
  
  public function mount()
  {
    $this->ver = 0;
    $this->pageTitle = 'Listado';
    $this->cantidad = 1;
    $this->pago = 0;
    $this->metodos = [];
    $this->detalle_venta = [];
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
    
    $this->dateFrom = Carbon::parse('2000-01-01 00:00:00')->format('d-m-Y');
    $this->dateTo = Carbon::now()->format('d-m-Y');


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
    
    $casa_central_id = Auth::user()->casa_central_user_id;
    
    $es_sucursal = Auth::user()->sucursal;
    
    $movimiento_stocks = movimiento_stocks::where('movimiento_stocks.items','>',0)->whereBetween('movimiento_stocks.created_at', [$from, $to]);
    
    if($es_sucursal == 1){
        $movimiento_stocks->where('user_id', $comercio_id);
    } else {
        $movimiento_stocks->where('casa_central_id', $casa_central_id);
    }
    

    if($this->search) {
    $movimiento_stocks = $movimiento_stocks->where('movimiento_stocks.id', 'like', '%' . $this->search . '%');
    }
    $movimiento_stocks = $movimiento_stocks->orderBy('movimiento_stocks.id','desc')->paginate($this->pagination);

    $sucursales = User::get();

    return view('livewire.movimiento-stock-resumen.component',[
      'data' => $movimiento_stocks,
      'sucursales' => $sucursales
    ])
    ->extends('layouts.theme-pos.app')
    ->section('content');
  }


    public function CerrarModal(){
     $this->ver = 0;
    }

    public function RenderFactura($ventaId)
       {
          $this->ver = 1;
          $this->NroVenta = $ventaId;

          $this->total_venta = movimiento_stocks::find($ventaId);
          $this->observaciones = $this->total_venta->observacion;
          $this->nro_movimiento = $this->total_venta->nro_movimiento;
          
          $this->detalle_venta = movimiento_stocks_detalles::where('movimiento_stocks_detalles.movimiento_id', $ventaId)
          ->get();
          
          $this->sucursal_origen = User::find($this->total_venta->sucursal_origen)->name;
          $this->sucursal_destino = User::find($this->total_venta->sucursal_destino)->name;
          
          $this->suma_monto = $this->detalle_venta->sum('monto_compra');

          $this->ventaId = $ventaId;

          $this->estado = "display: none;";
          $this->estado2 = "display: none;";


                  //
       }

/*      
public function ImprimirRemito($ventaId)
{
    $detalle_venta = [];
    $usuario = [];
    $detalle_cliente = [];
    $total_total = [];
    $fecha = [];
    $ventaIdFactura = $ventaId;

    $usuario_id = Auth::user()->id;

    if(Auth::user()->comercio_id != 1)
        $comercio_id = Auth::user()->comercio_id;
    else
        $comercio_id = Auth::user()->id;
    
    $total_total = movimiento_stocks::find($ventaId);
    
    $detalle_facturacion = User::find($total_total->sucursal_origen);
    $detalle_venta = movimiento_stocks_detalles::where('movimiento_stocks_detalles.movimiento_id', $ventaId)->get();
    $detalle_cliente = User::find($total_total->sucursal_destino);      
    $fecha = movimiento_stocks::find($ventaId)->created_at;

    $usuario = User::join('movimiento_stocks as s','s.user_id','users.id')
        ->select('users.image','users.name')
        ->where('s.id', $ventaId)
        ->get();

    $ventaId = $total_total->nro_movimiento;

    $pdf_factura = PDF::loadView('pdf.remito-movimiento-stock', compact('detalle_venta', 'detalle_cliente', 'detalle_facturacion', 'total_total', 'fecha', 'usuario', 'ventaId'));

    return $pdf_factura->stream('Movimiento stock.pdf'); // visualizar
}
*/    
       
       
public function SetCasaCentralMovimientoStock(){
    $movimiento_stocks = movimiento_stocks::get();
    foreach($movimiento_stocks as $ms){
        $u = User::find($ms->user_id);
        $movimiento = movimiento_stocks::find($ms->id);
        $movimiento->casa_central_id = $u->casa_central_user_id;
        $movimiento->save();
        
    }
}       

       
public function ImprimirRemito($movimientoId)
{
    return redirect('remito-movimiento-stock/pdf/'. $movimientoId);
}

}
