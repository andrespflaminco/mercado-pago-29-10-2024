<?php

namespace App\Http\Livewire;

use App\Services\Cart;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Session;
use Livewire\WithFileUploads;
use App\Models\ColumnConfiguration;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\proveedores;

use App\Models\compras_insumos;
use App\Models\detalle_compra_insumos;
use App\Models\insumo;
use App\Models\historico_stock_insumo;


use App\Models\actualizaciones;
use App\Models\etiquetas_relacion;
use App\Models\sucursales;
use App\Models\productos_stock_sucursales;
use App\Models\productos_variaciones_datos;
use App\Models\productos_variaciones;
use App\Models\variaciones;
use App\Models\atributos;
use App\Models\Category;
use App\Models\metodo_pago;
use App\Models\seccionalmacen;
use App\Models\cajas;
use App\Models\historico_stock;
use App\Models\pagos_facturas;
use App\Models\Product;


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



use App\Traits\WocommerceTrait;
use App\Traits\ProduccionTrait;

// Trait
use App\Traits\BancosTrait;

class ComprasResumenInsumosController extends Component
{
  use WithPagination;
  use WithFileUploads;
  use WocommerceTrait;
  use BancosTrait;
  use ProduccionTrait;

  public $name,$barcode,$cost,$price,$agregar,$accion,$observacion,$comprasProveedores,$Nro_Compra,$suma_compras_pagas,$proveedor_id,$suma_compras_totales,$suma_compras_cantidades,$suma_compras_deuda,$pago,$iva_compra,$caja_seleccionada, $total_total, $estado_pago, $caja, $sucursal_id, $proveedor_elegido,  $stock,$alerts,$categoryid, $codigo, $monto_total, $search, $image, $selected_id, $pageTitle,$componentName,$comercio_id,$data_Cart, $dateFrom, $dateTo, $Cart, $deuda,$metodo_pago_elegido, $product,$total, $itemsQuantity, $cantidad, $carrito, $qty, $detalle_cliente, $detalle_facturacion, $ventaId, $style, $style2, $pagos2, $estado2, $estado, $listado_hojas_ruta, $suma_monto, $suma_cash, $suma_deuda, $rec, $tot, $usuario,$tipo_pago,$monto_ap, $recargo_total,$total_pago,$NroVenta, $id_pago, $tipos_pago, $detalle_venta, $detalle_compra, $dci, $details, $saleId, $countDetails, $sumDetails, $formato_modal, $metodo_pago_agregar_pago, $fecha_ap, $fecha_editar, $detalle_proveedor;
  private $pagination = 25;
  public $productos_variaciones_datos = [];
  // En tu componente Livewire
public $etiquetaSeleccionada = [];

public $query_product;

public $comprobante;
  
    public $mostrarFiltros = false;

    public function MostrarFiltro()
    {
        $this->mostrarFiltros = !$this->mostrarFiltros;
    }
    
  	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}
	
	    public function loadColumns()
    {
        $columns = ColumnConfiguration::where(['user_id' => Auth::id(), 'table_name' => 'compras_resumen_insumos'])
            ->pluck('is_visible', 'column_name')
            ->toArray();

        // Todas las columnas disponibles
        $allColumns = [
            'nro_compra' => true,
            'nombre_proveedor' => true,
            'created_at' => true,
            'status' => true,
            'subtotal' => true,
            'descuento' => true,
            'iva' => true,
            'total' => true, 
            'etiquetas' => true,
            'pago' => true,
            'deuda' => true,
            'estado_pago' => true,
            'entrega_parcial' => true,
            'actualizacion' => true
        ];

        // Fusionar columnas personalizadas con todas las columnas disponibles
        $this->columns = array_merge($allColumns, $columns);
    }

    public function toggleColumnVisibility($columnName)
    {
        //dd($this->columns[$columnName]);
        $isVisible = ($this->columns[$columnName] ?? false);
        ColumnConfiguration::updateOrCreate(
            ['user_id' => Auth::id(), 'table_name' => 'compras_resumen', 'column_name' => $columnName],
            ['is_visible' => $isVisible]
        );

        $this->columns[$columnName] = $isVisible;
    }
	
  public function mount()
  {
    $this->accion = 0;
    $this->ver_opciones_pantalla = 0;
    $this->lista_cajas_dia = [];
    $this->caja = cajas::select('*')->where('estado',0)->where('user_id',Auth::user()->id)->max('id');
    $fecha_editar = Carbon::now()->format('d-m-Y');
    $this->fecha_ap = Carbon::now()->format('d-m-Y');
    $this->tipos_pago = [];
    $this->detalle_compra = [];
    $this->pagos2 = [];
    $this->detalle_proveedor = [];
    $this->dci = [];
    $this->style = 'none';
    $this->style2 = 'block';
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
    
    $this->loadColumns();
  }

    public function VerOpcionesPantalla($value) {
    
    if($value == 1) {$this->ver_opciones_pantalla = 0;}
    if($value == 0) {$this->ver_opciones_pantalla = 1;}
    }

  
  
  public function AgregarModal() {
      $this->accion = 1;
  }
  
    
  public function CerrarModal() {
      $this->accion = 0;
  }
  
  public function render()
  {


    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    $this->comercio_id = $comercio_id;

    
    if(Auth::user()->sucursal != 1) {
	$this->casa_central_id = $comercio_id;
	} else {

	$this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
	$this->casa_central_id = $this->casa_central->casa_central_id;
	}


    $this->caja_seleccionada = cajas::find($this->caja);
    
    $this->ultimas_cajas = cajas::where('comercio_id', $comercio_id)->where('eliminado',0)->orderby('created_at','desc')->limit(5)->get();
    
    $this->tipos_pago = bancos::where('bancos.comercio_id', 'like', $comercio_id)
    ->orderBy('bancos.nombre','asc')->get();

    if($this->dateFrom !== '' || $this->dateTo !== '')
    {
      $from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
      $to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';

    }

    if($this->estado_pago !== '' )
    {
      if($this->estado_pago == 'Pendiente' )
      {

        $this->estado_pago_buscar = ' compras_insumos.deuda > 0 ';
      }

      if($this->estado_pago == 'Pago' ) {
        $this->estado_pago_buscar = ' compras_insumos.deuda <= 0';
      }


    }



      $compras_proveedores = compras_insumos::select('compras_insumos.*','proveedores.nombre as nombre_proveedor')
      ->join('proveedores','proveedores.id','compras_insumos.proveedor_id')
      ->where('compras_insumos.eliminado',0)
	  ->where('compras_insumos.comercio_id', 'like', $comercio_id)
      ->whereBetween('compras_insumos.created_at', [$from, $to]);

      if($this->proveedor_elegido) {
        $compras_proveedores = $compras_proveedores->where('proveedores.id',$this->proveedor_elegido);
      }

      if($this->search) {
        $compras_proveedores = $compras_proveedores->where('compras_insumos.id', 'like', '%' . $this->search . '%');
      }

      if($this->estado_pago) {
        $compras_proveedores = $compras_proveedores->whereRaw($this->estado_pago_buscar);

      }

      $compras_proveedores = $compras_proveedores->orderBy('compras_insumos.id','desc')->paginate($this->pagination);


      $etiquetas = etiquetas_relacion::where('origen','compras')->where('estado',1)->get();
      
          // Crear un diccionario asociativo usando el campo 'relacion_id'
    $etiquetasPorRelacionId = [];
    foreach ($etiquetas as $etiqueta) {
        $etiquetasPorRelacionId[$etiqueta->relacion_id] = $etiqueta->nombre_etiqueta;
    }
    
    // Iterar sobre los resultados de la primera consulta y agregar 'nombre_etiqueta'
    foreach ($compras_proveedores as $compra_proveedor) {
        $relacionId = $compra_proveedor->id;
    
        // Verificar si existe un valor para 'nombre_etiqueta' en el diccionario
        $nombreEtiqueta = isset($etiquetasPorRelacionId[$relacionId]) ? $etiquetasPorRelacionId[$relacionId] : null;
    
        // Agregar 'nombre_etiqueta' al objeto de la primera consulta
        $compra_proveedor->nombre_etiqueta = $nombreEtiqueta;
    }
    
    // Ahora, $compras_proveedores contendrá la columna 'nombre_etiqueta' agregada
    
    //dd($compras_proveedores);
      
      $compras_proveedores_totales = compras_insumos::select( compras_insumos::raw('SUM(compras_insumos.total) as total'),compras_insumos::raw('COUNT(compras_insumos.id) as cantidad'), compras_insumos::raw('SUM(compras_insumos.deuda) as deuda')    )
      ->join('proveedores','proveedores.id','compras_insumos.proveedor_id')
      ->where('compras_insumos.eliminado',0)
      ->where('compras_insumos.comercio_id', $comercio_id)
      ->whereBetween('compras_insumos.created_at', [$from, $to]);

      if($this->proveedor_elegido) {
        $compras_proveedores_totales = $compras_proveedores_totales->where('proveedores.id',$this->proveedor_elegido);
      }

      if($this->search) {
        $compras_proveedores_totales = $compras_proveedores_totales->where('compras_insumos.id', 'like', '%' . $this->search . '%');
      }


      $compras_proveedores_totales = $compras_proveedores_totales->first();

      //dd($compras_proveedores_totales);



    //  $metodo_pagos = bancos::where('bancos.comercio_id', 'like', $this->casa_central_id)->get();

    $metodo_pagos = $this->GetBancosTrait($comercio_id); 
    
    //dd($compras_proveedores_totales->total,$compras_proveedores_totales->deuda);
    
    $this->suma_compras_totales = $compras_proveedores_totales->total;
    $this->suma_compras_cantidades = $compras_proveedores_totales->cantidad;
    $this->suma_compras_deuda = $compras_proveedores_totales->deuda;
    $this->suma_compras_pagas = floatval($compras_proveedores_totales->total) - floatval($compras_proveedores_totales->deuda);
    
    //dd($this->suma_compras_totales,$this->suma_compras_cantidades,$this->suma_compras_deuda);

    $this->etiqueta = $this->GetEtiquetas($comercio_id,"compras_insumos");
    
    $this->etiqueta_json = $this->GetEtiquetasJson($comercio_id,"compras_insumos");
    
    return view('livewire.compras-resumen-insumos.component',[
      'data' => $compras_proveedores,
      'detalle_compra' => $this->detalle_compra,
      'metodo_pago' => $metodo_pagos,
      'categories' => Category::orderBy('name','asc')->where('comercio_id', 'like', $comercio_id)->get(),
      'almacenes' => seccionalmacen::orderBy('nombre','asc')->where('comercio_id', 'like', $comercio_id)->get(),
      'prov' => proveedores::orderBy('nombre','asc')->where('comercio_id', 'like', $comercio_id)->orWhere('comercio_id', 'like', $this->casa_central_id)->get()
    ])
    ->extends('layouts.theme-pos.app')
    ->section('content');
  }




  public function showCart() {
      return view('livewire.ventas-fabrica.cart')
      ->extends('layouts.theme.app')
      ->section('content');
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
    'EliminarProducto' => 'Delete',
    'EliminarProductoLast' => 'DeleteLast',
    'EliminarCompra' => 'EliminarCompra',
    'AgregarPago' => 'AgregarPago',
    'etiquetaSeleccionada' => 'actualizarEtiqueta',
    'FechaElegida' => 'FechaElegida'
  ];
  
      
    public function FechaElegida($startDate, $endDate)
    {
      // Manejar las fechas seleccionadas aquí
      $this->dateFrom  = $startDate;
      $this->dateTo = $endDate;;
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



//////////    PAGOS    /////////////////


  public function MontoPagoEditarPago($value)
  {
  $metodo_pago = metodo_pago::find($this->metodo_pago_agregar_pago);

  $this->MontoPagoEditarPago = $value;

  $this->recargo = $metodo_pago->recargo/100;

  $this->recargo_total = $this->MontoPagoEditarPago * $this->recargo;

  $this->total_pago = $this->recargo_total + $this->MontoPagoEditarPago;

  }


  public function CreatePago($ventaId)
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $this->compras_proveedores = compras_insumos::find($ventaId);
    
    if($this->metodo_pago_agregar_pago == 1) {
    $mp = 1;
    } else {$mp = 0;}
    
    $estado_pago = $this->GetPlazoAcreditacionPago($this->metodo_pago_agregar_pago); //18-5-2024
    
    $pago_factura =   pagos_facturas::create([
      'estado_pago' => $estado_pago,
      'monto_compra' => $this->monto_ap,
      'caja' => $this->caja,
      'banco_id' => $this->metodo_pago_agregar_pago,
      'metodo_pago' => $mp,
      'proveedor_id' => $this->compras_proveedores->proveedor_id,
      'comercio_id' => $comercio_id,
      'id_compra_insumos' => $ventaId,
      'eliminado' => 0
    ]);
    
    //dd($pago_factura);

    $this->monto_ap = '';
    $this->metodo_pago_ap = 'Elegir';
    $this->caja = cajas::where('estado',0)->where('user_id',Auth::user()->id)->max('id');

     $this->emit('pago-agregado', 'El pago fue guardado.');

     $this->emit('agregar-pago-hide', 'hide');

     $this->ActualizarEstadoDeuda($ventaId);

     $this->ResetPago();

     $this->RenderFactura($ventaId);




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
       
       $this->RenderFactura($id_pago);

     }

function EditPago($id_pago) {


       $this->emit('agregar-pago','details loaded');

       $this->formato_modal = 1;


       $this->id_pago = $id_pago;


       $pagos = pagos_facturas::find($id_pago);

       $this->caja = $pagos->caja;

       $this->metodo_pago_agregar_pago = $pagos->banco_id;

      $this->tipo_pago = $this->metodo_pago_agregar_pago;

      $this->monto_ap = $pagos->monto_compra;

       $this->fecha_ap = Carbon::parse($pagos->created_at)->format('d-m-Y');


       $this->total_pago = $this->monto_ap;
        
      // dd($pagos);
        
       $this->RenderFactura($pagos->id_compra_insumos);
  

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

function CerrarAgregarPago($ventaId) {

  $this->emit('agregar-pago-hide','details loaded');

  $this->ResetPago();

  $this->RenderFactura($ventaId);


}


public function ActualizarPago($id_pago) {

  $pagos = pagos_facturas::find($id_pago);

  $ventaId = $pagos->id_compra_insumos;

  $this->recargo_viejo_actualizar_pago = $pagos->recargo;

  if($this->metodo_pago_agregar_pago == 1) {
  $mp = 1;
  } else {$mp = 0;}
  
  
  if($pagos->banco_id != $this->metodo_pago_agregar_pago){
  $estado_pago = $this->GetPlazoAcreditacionPago($this->metodo_pago_agregar_pago); //18-5-2024
  } else {
  $estado_pago = $pagos->estado_pago;   
  }
  
  $pagos->update([
    'estado_pago' => $estado_pago,
    'monto_compra' => $this->monto_ap,
    'caja' => $this->caja,
    'banco_id' => $this->metodo_pago_agregar_pago,
    'metodo_pago' => $mp

  ]);


  $this->emit('agregar-pago-hide', 'hide');

  $this->emit('pago-actualizado', 'El pago fue actualizado.');

  $this->ActualizarEstadoDeuda($ventaId);

  $this->RenderFactura($ventaId);

  $this->ResetPago();



  $this->estado = "display: block;";

}
//// ELEGIR UNA CAJA POR FECHA ////


public function CambioCaja() {


  $this->tipo_click = 1;

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  if($this->sucursal_id != null) {
    $this->sucursal_id = $this->sucursal_id;
  } else {
    $this->sucursal_id = $comercio_id;
  }



  $this->fecha_pedido_desde = $this->fecha_ap.' 00:00:00';

  $this->fecha_pedido_hasta = $this->fecha_ap.' 23:59:50';

  $this->emit('modal-estado','details loaded');

  $this->lista_cajas_dia = cajas::where('comercio_id', $this->sucursal_id)->where('eliminado',0)->whereBetween('fecha_inicio',[$this->fecha_pedido_desde, $this->fecha_pedido_hasta])->get();


}

public function CerrarModalEstado()
{

  $this->emit('modal-estado-hide','close');

    $this->tipo_click = 0;
 
}

//// ELIMINAR UN PAGO ///

public function DeletePago($id)
{
    
    $this->pago_viejo = pagos_facturas::find($id);

    $ventaId = $this->pago_viejo->id_compra_insumos;

    $this->pago_viejo->eliminado = 1;
    $this->pago_viejo->save();


    $this->emit('pago-eliminado', 'El pago fue eliminado.');

    $this->ActualizarEstadoDeuda($ventaId);

    $this->RenderFactura($ventaId);

    $this->estado = "display: block;";


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
  



public function ActualizarEstadoDeuda($ventaId)
{
  /////////////////   ACTUALIZAR ESTADO DE PAGO   /////////////////////


       $this->data_total = compras_insumos::where('compras_insumos.id', $ventaId)->first();
       
       //dd($this->data_total);

       $this->pagos2 = pagos_facturas::join('bancos','bancos.id','pagos_facturas.banco_id')
       ->select('bancos.nombre as metodo_pago','pagos_facturas.id_compra_insumos as id_compra','pagos_facturas.monto_compra','pagos_facturas.actualizacion','pagos_facturas.created_at as fecha_pago')
       ->where('pagos_facturas.id_compra_insumos', $ventaId)
       ->where('pagos_facturas.eliminado',0)
       ->get();

        $suma_monto = 0;
        
        // pagos        
        foreach ($this->pagos2 as $pago) {
            $montoCompra = $pago->monto_compra;
        
            // Realizar el cálculo y agregar al total
            $suma_monto += $montoCompra;
        }
        
        
       $this->tot = $this->data_total->total;
       $this->suma_monto = $suma_monto;
       
      // dd($this->tot,$suma_monto);
       
       $deuda = $this->tot - $this->suma_monto;

      //  dd($deuda);

      $this->deuda_vieja = compras_insumos::find($ventaId);

       $this->deuda_vieja->update([
         'deuda' => $deuda
         ]);


       ///////////////////////////////////////////////////////////////////
}



  public function RenderFactura($ventaId)
     {

        $this->GetEtiquetasEdit($ventaId,"compras_insumos",$this->comercio_id);
        
        $this->accion = 1;
        $this->NroVenta = $ventaId;

        
       //////////////// PAGOS //////////////
             $this->pagos2 = pagos_facturas::join('bancos as mp','mp.id','pagos_facturas.banco_id')
             ->leftjoin('cajas','cajas.id','pagos_facturas.caja')
             ->select('mp.nombre as metodo_pago','pagos_facturas.id','cajas.nro_caja','pagos_facturas.monto_compra','pagos_facturas.actualizacion','pagos_facturas.actualizacion','pagos_facturas.created_at as fecha_pago')
             ->where('pagos_facturas.id_compra_insumos', $ventaId)
             ->where('pagos_facturas.eliminado',0)
             ->get();

             $this->detalle_proveedor = proveedores::join('compras_insumos','compras_insumos.proveedor_id','proveedores.id')
             ->select('proveedores.*')
             ->where('compras_insumos.id', $ventaId)
             ->get();
    
            $suma_monto = 0;
            
            // pagos        
            foreach ($this->pagos2 as $pago) {
                $montoCompra = $pago->monto_compra;
                $actualizacion = $pago->actualizacion;
            
                // Realizar el cálculo y agregar al total
                $suma_monto += $montoCompra * (1 + $actualizacion);
            }

             $this->suma_monto = $suma_monto;

             $this->ventaId = $ventaId;

             $this->estado = "display: none;";
             $this->estado2 = "display: none;";


  /////////////// DETALLE DE VENTA /////////////////////7
         $this->dci = detalle_compra_insumos::where('detalle_compra_insumos.compra_id', $ventaId)->where('detalle_compra_insumos.eliminado', 0)->get();

         $this->total = compras_insumos::where('compras_insumos.id', $ventaId)->get();

         foreach($this->total as $t) {
         $proveedor_id = $t->proveedor_id;
         $iva_compra = $t->alicuota_iva;
         $fecha_compra = $t->created_at;
         $nro_compra = $t->nro_compra;
         $actualizacion = $t->actualizacion;
         $descuento = $t->descuento;
         $observacion = $t->observaciones;
         $porcentaje_descuento = $t->porcentaje_descuento;
         }        
         
         $fechaFormateada = Carbon::parse($fecha_compra)->format('Y-m-d');
     
         $this->actualizacion = $actualizacion;
         $this->proveedor_id = $proveedor_id;
         $this->iva_compra = $iva_compra;
         $this->fecha_compra = $fechaFormateada;
         $this->Nro_Compra = $nro_compra;
         $this->descuento = $descuento;
         $this->observacion = $observacion;
         $this->porcentaje_descuento = $porcentaje_descuento*100;
            
         //dd($this->iva_compra);
            
          $this->emit('modal-show','Show modal');

        //
     }


/////////// EDITAR LOS PRODUCTOS DE LA COMPRA ////////////////


								   
public function EditarPedido($style) {

    if ($style === "none") {

      $this->style = "block";

      $this->style2 = "none";

      $this->RenderFactura($this->NroVenta);

    } else {
      $this->style = "none";

      $this->style2 = "block";

      $this->RenderFactura($this->NroVenta);

    }



}


///////////// AGREGAR UN NUEVO PRODUCTO A LA COMPRA  ///////////

    public function updatedQueryProduct()
    {


            if(Auth::user()->comercio_id != 1)
            $comercio_id = Auth::user()->comercio_id;
            else
            $comercio_id = Auth::user()->id;


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


        $this->products_s = 	insumo::where('comercio_id', 'like', $this->casa_central_id)->where('eliminado', 0)
        ->where( function($query) {
              $query->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
          })
            ->limit(5)
            ->get()
            ->toArray();

            $this->RenderFactura($this->NroVenta);



    }
    
    
    

    public function selectProduct($item)
    {

     $producto_venta = detalle_compra_insumos::where('detalle_compra_insumos.producto_id', $item)->where('detalle_compra_insumos.compra_id', $this->NroVenta)->where('detalle_compra_insumos.eliminado', 0)->first();

     $product = insumo::find($item);
     $venta = compras_insumos::find($this->NroVenta);

    
    if($producto_venta == [] || $producto_venta == null || empty($producto_venta))
      {

       $descuento_total = $product->cost * $venta->porcentaje_descuento;
       $iva = $product->cost - $descuento_total * $venta->alicuota_iva;
       
      detalle_compra_insumos::create([
        'precio' => $product->cost,
        'cantidad' => 1,
        'nombre' => $product->name,
        'producto_id' => $product->id,
        'barcode' => $product->barcode,
        'metodo_pago'  => $venta->metodo_pago,
        'comercio_id' => $product->comercio_id,
        'compra_id' => $this->NroVenta,
        'alicuota_iva' => $venta->alicuota_iva,
        'iva' => $iva,
        'porcentaje_descuento' => $venta->porcentaje_descuento,
        'descuento' => $descuento_total,
        'eliminado' => 0
      ]);

    //  Actualizar el total de la compra
    
      $this->ActualizarTotalCompra($this->NroVenta);


    //Actualizar el stock stock
    
    $cantidad = 1;
    $referencia_variacion = 0;
    
    $this->ActualizarStockProducto($item, $cantidad);
    
    // Actualizar la deuda
    
    $this->ActualizarEstadoDeuda($this->NroVenta);

    
      } else {

      $producto_venta->update([
        'cantidad' => $producto_venta->cantidad + 1
      ]);

    //  Actualizar el total de la compra
    
      $this->ActualizarTotalCompra($this->NroVenta);

    //Actualizar el stock stock
    
    $cantidad = 1;
    $referencia_variacion = 0;
    
    $this->ActualizarStockProducto($item, $cantidad);
    
    //Actualizar la deuda
    
    $this->ActualizarEstadoDeuda($this->NroVenta);
    
          
    }



        $this->resetProduct();

          $this->RenderFactura($this->NroVenta);
          
          $this->emit('msg','Producto agregado');
    }


    public function resetProduct()
   {
     $this->products_s = [];
      $this->query_product = '';
       $this->RenderFactura($this->NroVenta);
   }

//////////// ACTUALIZAR LA CANTIDAD DE UN PRODUCTO DE LA COMPRA ////////

public function updateQty($id_pedido_prod, $cant = 1)
{

    if(0 < $cant){
    
      $this->items_viejo = detalle_compra_insumos::find($id_pedido_prod);
      
      $this->qty_item_viejo = $this->items_viejo->cantidad;


      $this->items_viejo->update([
        'cantidad' => $cant
        ]);

      $this->items_nuevo = detalle_compra_insumos::find($id_pedido_prod);

      $this->qty_item_nuevo = $this->items_nuevo->cantidad;

      $this->diferencia_items = ($this->qty_item_viejo-$this->qty_item_nuevo);

      
    //  Actualizar el total de la compra
    
    $this->ActualizarTotalCompra($this->items_viejo->compra_id);

    //Actualizar el stock stock
    
    $cantidad = -1*$this->diferencia_items;
    $referencia_variacion = 0;
    
    $this->ActualizarStockProducto($this->items_viejo->producto_id, $cantidad);
    
    //Actualizar la deuda
 
    $this->ActualizarEstadoDeuda($this->items_nuevo->compra_id);

    // Renderizar nuevamente la compra 
    
    $this->RenderFactura($this->items_nuevo->compra_id);
    $this->emit('msg','Cantidad actualizada');
    } else {
      $this->Delete($id_pedido_prod);  
    }
    
}

////////// ELIMINAR UN PRODUCTO DE LA COMPRA /////////

public function Delete($id_pedido_prod)
{

      $this->items = detalle_compra_insumos::find($id_pedido_prod);


      $this->items->update([
        'eliminado' => 1
        ]);


      $this->qty_item = $this->items->cantidad;
      $this->price_item = $this->items->precio;

    //  Actualizar el total de la compra
    
      $this->ActualizarTotalCompra($this->items->compra_id);

      //Actualizar el stock stock
    
        $product_id = $this->items->producto_id;
        $referencia_id = $this->items->referencia_variacion;
        $cantidad = -1*$this->qty_item;
        
        //  Actualizar stock
        
        $this->ActualizarStockProducto($product_id , $cantidad);
        
        // ACtualizar estado de deuda
        
        $this->ActualizarEstadoDeuda($this->items->compra_id);

        //  Renderizar factura
        
        $result = $this->UpdateProductStockIndividualWocommerce($this->items->producto_id,$this->items->referencia_variacion,$this->items->comercio_id);
        
        $this->RenderFactura($this->items->compra_id);
        $this->emit('msg','Producto eliminado');
    }
    
public function DeleteLast($id_pedido_prod)
{

      $this->items = detalle_compra_insumos::find($id_pedido_prod);


      $this->items->update([
        'eliminado' => 1
        ]);


      $this->qty_item = $this->items->cantidad;
      $this->price_item = $this->items->precio;

    //  Actualizar el total de la compra
    
    
      $this->ActualizarTotalCompra($this->items->compra_id);

      //Actualizar el stock stock
    
        $product_id = $this->items->producto_id;
        $referencia_id = $this->items->referencia_variacion;
        $cantidad = -1*$this->qty_item;
        
        //  Actualizar stock
        
        $this->ActualizarStockProducto($product_id , $cantidad);
        
        // ACtualizar estado de deuda
        
        $this->ActualizarEstadoDeuda($this->items->compra_id);

        //  Renderizar factura
        $this->EliminarCompra($this->items->compra_id);
        $this->agregar = 0;
        $this->emit('msg','Compra eliminada');
    }
    
public function updatePrice($id_pedido_prod, $cant = 1) {

      $this->items_viejo = detalle_compra_insumos::find($id_pedido_prod);

      $this->precio_viejo = $this->items_viejo->precio;
      
      $this->iva_total_nuevo = ($cant * (1+$this->items_viejo->alicuota_iva) ) * $this->items_viejo->cantidad;

      $this->items_viejo->update([
        'precio' => $cant,
        'iva' => $this->iva_total_nuevo
        ]);

    //  Actualizar el total de la compra
    
        $this->ActualizarTotalCompra($this->items_viejo->compra_id);

    //  Actualizar el estado de la deuda
    
        $this->ActualizarEstadoDeuda($this->items_viejo->compra_id);

    //  Renderiza la compra
    
        $this->RenderFactura($this->items_viejo->compra_id);

    
}

/////// ACTUALIZAR EL IVA DE UN PRODUCTO ////////
    
public function UpdateIva($id_pedido_prod, $cant = 1)
{

      $this->items_viejo = detalle_compra_insumos::find($id_pedido_prod);

      $this->iva_viejo = $this->items_viejo->alicuota_iva;
      
      $this->iva_total_nuevo = $cant * $this->items_viejo->precio * $this->items_viejo->cantidad;


      $this->items_viejo->update([
        'alicuota_iva' => $cant,
        'iva' => $this->iva_total_nuevo
        ]);


        $this->items_nuevo = detalle_compra_insumos::find($id_pedido_prod);

        $this->iva_nuevo = $this->items_nuevo->alicuota_iva;

        $this->diferencia_iva = ($this->iva_viejo - $this->items_nuevo->alicuota_iva) * $this->items_nuevo->precio * $this->items_nuevo->cantidad;

      $this->venta = compras_insumos::find($this->items_nuevo->compra_id);

      $this->total_iva_nuevo = $this->venta->iva - $this->diferencia_iva;
      
    //  Actualizar el total de la compra
    
        $this->ActualizarTotalCompra($this->items_nuevo->compra_id);

    //  Actualizar el estado de la deuda
    
        $this->ActualizarEstadoDeuda($this->items_nuevo->compra_id);

    //  Renderiza la compra
    
        $this->RenderFactura($this->items_nuevo->compra_id);


    }


public function UpdateIvaGral() {
    
    

      $detalle_compra_vieja = detalle_compra_insumos::where('compra_id',$this->NroVenta)->where('eliminado',0)->get();
      $venta_vieja = compras_insumos::find($this->NroVenta);
      
      $iva_viejo = $venta_vieja->alicuota_iva;
      
      $iva_nuevo = $this->iva_compra;
      
      foreach($detalle_compra_vieja as $dc) {
      
      $detalle_compra_vieja = detalle_compra_insumos::find($dc->id);
      
      $iva_total_nuevo = $detalle_compra_vieja->cantidad * $detalle_compra_vieja->precio * $iva_nuevo;

      $detalle_compra_vieja->update([
        'alicuota_iva' => $iva_nuevo,
        'iva' => $iva_total_nuevo
        ]);
        
      $venta_vieja->update([
       'alicuota_iva' => $iva_nuevo,
       ]);
        
      }
      

    //  Actualizar el total de la compra
    
        $this->ActualizarTotalCompra($this->NroVenta);

    //  Actualizar el estado de la deuda
    
        $this->ActualizarEstadoDeuda($this->NroVenta);

    //  Renderiza la compra
        
        $this->emit("msg","IVA actualizado");
        
        $this->RenderFactura($this->NroVenta);

    
}


/// ------------- FUNCIONES COMUNES A TODAS LAS EDICIONES ------------------------ /////



//////////// ESTA FUNCION ACTUALIZA EL MONTO TOTAL DE LA COMPRA LUEGO DE EDITARLA /////////


public function ActualizarTotalCompra($compra_id) {
    
      $this->details = detalle_compra_insumos::where('compra_id',$compra_id)->where('eliminado',0)->get();
      //
      $suma = $this->details->sum(function($item){
          return $item->precio * $item->cantidad;
      });

      $descuentos = $this->details->sum(function($item){
          return $item->precio * $item->cantidad * $item->porcentaje_descuento;
      });
      
      $iva = $this->details->sum(function($item){
          return $item->alicuota_iva * ( ($item->cantidad *$item->precio) - ($item->cantidad *$item->precio * $item->porcentaje_descuento ) );
      });

      
      $actualizacion = $this->details->sum(function($item){
          return $item->actualizacion * ( ($item->cantidad *$item->precio) - ($item->cantidad *$item->precio * $item->porcentaje_descuento ) );
      });

      $this->actualizacion = $actualizacion;
      $this->subtotal_venta_nuevo = $suma;
      $this->descuento_nuevo = $descuentos;
      $this->total_venta_nuevo = $suma - $descuentos + $iva + $actualizacion;
      $this->iva_venta_nuevo = $iva;
      $this->items_venta_nuevo = $this->details->sum('cantidad');

      $this->sale = compras_insumos::find($compra_id);
      

      $this->sale->update([
        'subtotal' => $this->subtotal_venta_nuevo,
        'total' => $this->total_venta_nuevo,
        'items' => $this->items_venta_nuevo,
        'iva' => $this->iva_venta_nuevo,
        ]);



}



//////////// ESTA FUNCION ACTUALIZA EL STOCK DEL PRODUCTO LUEGO DE EDITAR LA COMPRA /////////

public function ActualizarStockProducto($product_id, $cantidad) {
      
      $product = $this->GetStockInsumoEnSucursalById($product_id,$this->comercio_id,$this->casa_central_id);
      //update stock
      
      $product->stock = $product->stock + $cantidad;
      $product->save();


      $historico_stock = historico_stock_insumo::create([
        'tipo_movimiento' => 9,
        'compra_id' => $this->sale->id,
        'insumo_id' => $product->id,
        'cantidad_movimiento' => 1,
        'stock' => $product->stock,
        'usuario_id' => Auth::user()->id,
        'comercio_id'  => $this->comercio_id
      ]);


}

/// -------------------------------------------------------------------------------- /////

    
    public function ExportarReporte($url) {

    return redirect('report-compras/excel/'. $url .'/'. Carbon::now()->format('d_m_Y_H_i_s'));

    }
    
    public function ExportarCompra($nro_compra){
    return redirect('compra/excel/'. $nro_compra .'/'. Carbon::now()->format('d_m_Y_H_i_s'));    
    }
    
    public function EliminarCompra($compra_id) {
        
        // dd($compra_id);
        
        // colocar compra como eliminada
        $compra = compras_insumos::find($compra_id);
        $compra->eliminado = 1;
        $compra->save();
        
        // devolver el stock de productos
        
        $detalle_compra_insumos = detalle_compra_insumos::where('compra_id',$compra_id)->where('eliminado',0)->get();
        
        foreach($detalle_compra_insumos as $dcp) {

        $stock = $this->GetStockInsumoEnSucursalById($dcp->producto_id,$dcp->comercio_id,$this->casa_central_id);

        $stock_product = $stock->stock;
        $stock->stock = $stock_product - $dcp->cantidad;
        $stock->save();
        
        // Eliminar los pagos 
        
        $pagos = pagos_facturas::where('id_compra_insumos',$compra_id)->where('eliminado',0)->get();
        
        foreach($pagos as $p) {
         $p->delete();
        }
        
        $this->ActualizarEstadoDeuda($compra_id);
        
        // historico_stock
        
        $historico_stock = historico_stock::create([
            'tipo_movimiento' => 9,
            'producto_id' => $dcp->producto_id,
            'referencia_variacion' => $dcp->referencia_variacion,
            'cantidad_movimiento' => -$dcp->cantidad,
            'stock' => $stock->stock,
            'comercio_id'  => $compra->comercio_id,
            'usuario_id'  => Auth::user()->id
          ]);
        
        
        $this->emit('product-added','Compra eliminada');
            
        }
    
    }
   
   public function CambiarProveedor(){
       //dd($this->proveedor_id);
       
       $proveedor = compras_insumos::find($this->NroVenta);
       
       $proveedor->update([
           'proveedor_id' => $this->proveedor_id
           ]);
           
           $this->emit('msg','Proveedor actualizado');
           $this->RenderFactura($this->NroVenta);
   }
   
      public function CambiarFecha(){
       //dd($this->proveedor_id);
       
       $compra = compras_insumos::find($this->NroVenta);
       
       $compra->update([
           'created_at' => $this->fecha_compra
           ]);
           
        //dd($compra);
        
           $this->emit('msg','Fecha actualizada');
           $this->RenderFactura($this->NroVenta);
   }
   
   
   
   
   public function Filtrar(){
       $this->render();
   }
   
    // actualizar el precio del item en carrito
	public function updateDescuentoGral($descuento)
	{
	  //dd($descuento);
	    
	  if(empty($descuento)) { $descuento = 0; }
      $descuento = str_replace(",",".",$descuento);
      
      $this->porcentaje_descuento = $descuento;
      $descuento_alicuota = $descuento/100;
      
	  $detalle_compra = detalle_compra_insumos::where('compra_id',$this->NroVenta)->where('eliminado',0)->get();
      $compra_vieja = compras_insumos::find($this->NroVenta);
      
      foreach($detalle_compra as $dc) {
      
      $detalle_compra_vieja = detalle_compra_insumos::find($dc->id);
      
      $descuento_total_nuevo = $detalle_compra_vieja->cantidad * $detalle_compra_vieja->precio * $descuento_alicuota;

      $detalle_compra_vieja->update([
        'porcentaje_descuento' => $descuento_alicuota,
        'descuento' => $descuento_total_nuevo
        ]);

      }
      
      $compra_vieja->porcentaje_descuento = $descuento_alicuota;
      $compra_vieja->save();

    //  Actualizar el total de la compra
    
        $this->ActualizarTotalCompra($this->NroVenta);

    //  Actualizar el estado de la deuda
    
        $this->ActualizarEstadoDeuda($this->NroVenta);

    //  Renderiza la compra
    
        $this->RenderFactura($this->NroVenta);

        $this->emit('msg','Descuento actualizado');

      
	}
   
    public function LimpiarFiltros(){
    $this->proveedor_elegido = 0;
    $this->estado_pago = '';
    $this->search = '';
    $this->dateFrom = Carbon::parse('2000-01-01 00:00:00')->format('d-m-Y');
    $this->dateTo = Carbon::now()->format('d-m-Y');
    }

// En tu componente Livewire
public function actualizarEtiqueta($data)
{
        // Obtener el valor seleccionado y el ID del select2
    $value = $data['value'];
    $compraId = $data['id'];
    
    //dd($this->comercio_id);
    
    $this->SetEtiquetasSeleccionadas($this->comercio_id,$value,"compras_insumos");
    // aca crea
    
    $this->StoreUpdateEtiquetas($compraId,2,"compras_insumos",$this->comercio_id);
    // hay que ver como hacer para que actualice
    
    $this->GetEtiquetasEdit($compraId,"compras_insumos",$this->comercio_id);
    
    $this->emit("msg","Etiqueta actualizada");
    
}

    
    public function CambiarObservacion(){
        $cp = compras_insumos::find($this->NroVenta);
        $cp->update([
            'observaciones' => $this->observacion
            ]);
        
               
           $this->emit('msg','Oservacion actualizada');
           $this->RenderFactura($this->NroVenta);
        
        
        
    }
    
    public function CambiarReferenciaProveedor($compraId)
{
        $cp = compras_insumos::find($compraId);
        $cp->update([
            'referencia_proveedor' => $this->observacion
            ]);
        
        $this->emit('msg','Referencia modificada');
}

public function GetEtiquetasEdit($relacion_id,$origen,$comercio_id){
        
        //dd($relacion_id,$origen,$comercio_id);
        $this->GetEtiquetasJson($comercio_id, $origen);
        
        $etiquetasSeleccionadas = etiquetas_relacion::where('relacion_id', $relacion_id)
        ->where('estado', 1)
        ->where('origen',$origen)
        ->first();
        
        if($etiquetasSeleccionadas != null) {
        $a_pasar = $etiquetasSeleccionadas['nombre_etiqueta'];
        } else {$a_pasar = null;}
        
        $this->nombre_etiqueta_seleccionada = $a_pasar;
        
}    


// DEUDA

public function EditarDeuda($compra_id){
     
     $this->detalle_compra_insumos = detalle_compra_insumos::where('compra_id',$compra_id)->where('eliminado',0)->get();
     $compra = compras_insumos::find($compra_id);
     //$pagos = pagos_facturas::where('id_compra',$compra_id)->where('eliminado',0)->get();
     //dd($pagos);
     
     $monto_total = $compra->total;
     $saldo = $compra->deuda;
     
     $suma_actualizacion = 0;
     $fila = 1;
     $suma_total = 0;
     $suma_porcentaje_actualizacion = 0;
     $suma_alicuota_actualizacion = 0;
     $suma_alicuota_nueva = 0;
     $this->nuevosDetalles = [];
     $this->nuevosPagos = [];
      
     foreach($this->detalles_actualizacion as $detalle){
    
     //dd($detalle);
     $product_id = $detalle->producto_id;
     $referencia_variacion = $detalle->referencia_variacion;
     
     $product = Product::find($product_id);
     
     //costos
     if($product->producto_tipo == "s"){
     $costo = $product->cost; 
     }
     if($product->producto_tipo == "v"){
     $pvd = producto_variaciones_datos::where('product_id',$product_id)->where('referencia_variacion',$referencia_variacion)->where('eliminado',0)->first();
     $costo =  $pvd->cost;    
     }
    
     $datos_actualizacion = actualizaciones::where('product_id',$product_id)->where('referencia_variacion',$referencia_variacion)->where('relacion_id',$compra_id)->orderBy('id','desc')->first();
     if($datos_actualizacion != null){
     $valor_viejo = $datos_actualizacion->valor_nuevo;  
     $valor_viejo_real = $datos_actualizacion->valor_nuevo_real;  
     } else {
     $valor_viejo = $detalle->precio;
     $valor_viejo_real = $detalle->precio;  
     }
     $precio_historico = $detalle->precio;
     // calcula cuanto aumento % el precio del producto     
     
     if($this->tipo_actualizacion == 1){
     //dd($costo,$valor_viejo);
     $actualizacion = ($costo/$valor_viejo);
     $actualizacion = $actualizacion - 1;
     }
     if($this->tipo_actualizacion == 2){
     $actualizacion = $this->actualizacion_elegida;
     }
     if($this->tipo_actualizacion == 3){
     $actualizacion = 0;
     }
     
     //dd($detalle->actualizacion);
     // Agrega la columna actualizacion al detalle
     $detalle->actualizacion_nueva = $actualizacion;
     $detalle->actualizacion_acumulada = ((1 +  $detalle->actualizacion) * (1 +  $actualizacion)) - 1;
     $detalle->costo_actual = $detalle->precio * (1 +  $detalle->actualizacion_acumulada);
     $detalle->actualizacion_producto = $detalle->precio * $detalle->actualizacion_acumulada * $detalle->cantidad;
     $detalle->total_actual = ($detalle->precio*$detalle->cantidad)*(1+$detalle->alicuota_iva)*(1+$detalle->actualizacion_acumulada);
     //dd($detalle->total_actual);
     $cantidad_filas = $fila++;
     
    $this->nuevosDetalles[] = [
        "fila" => $cantidad_filas,
        "id" => $detalle->id,
        "compra_id" => $detalle->compra_id,
        "precio" => $detalle->precio,
        "actualizacion" => $detalle->actualizacion,
        "actualizacion_real" => $detalle->actualizacion_real,
        "precio_final" => $detalle->precio_final,
        "precio_final_real" => $detalle->precio_final_real,
        "cantidad" => $detalle->cantidad,
        "producto_id" => $detalle->producto_id,
        "referencia_variacion" => $detalle->referencia_variacion,
        "porcentaje_descuento" => $detalle->porcentaje_descuento,
        "descuento" => $detalle->descuento,
        "alicuota_iva" => $detalle->alicuota_iva,
        "iva" => $detalle->iva,
        "barcode" => $detalle->barcode,
        "nombre" => $detalle->nombre,
        "comercio_id" => $detalle->comercio_id,
        "eliminado" => $detalle->eliminado,
        'actualizacion_nueva' => $detalle->actualizacion_nueva,
        'costo_actual' => $detalle->costo_actual,
        'actualizacion_producto' => $detalle->actualizacion_producto,
        'total_actual' => $detalle->total_actual
    ];


     $suma_porcentaje_actualizacion += $actualizacion;
     $suma_total += $detalle->total_actual;
     $suma_actualizacion += $detalle->actualizacion_producto;
     $suma_alicuota_actualizacion += $detalle->actualizacion_acumulada;
     $suma_alicuota_nueva += $detalle->actualizacion_nueva;
     
     }
     
     //dd($this->detalles_actualizacion);
     
    $porcentaje_actualizacion_promedio = $suma_alicuota_nueva/$cantidad_filas;
    //dd($porcentaje_actualizacion_promedio);
    
     //////////////// PAGOS //////////////
     $this->pagos2 = pagos_facturas::join('bancos as mp','mp.id','pagos_facturas.banco_id')
     ->leftjoin('cajas','cajas.id','pagos_facturas.caja')
     ->select('mp.nombre as metodo_pago','pagos_facturas.id','cajas.nro_caja','pagos_facturas.monto_compra','pagos_facturas.actualizacion','pagos_facturas.actualizacion','pagos_facturas.created_at as fecha_pago')
     ->where('pagos_facturas.id_compra_insumos', $compra_id)
     ->where('pagos_facturas.eliminado',0)
     ->get();
     
     //dd($this->pagos2);
    
     $this->detalle_proveedor = proveedores::join('compras_proveedores','compras_proveedores.proveedor_id','proveedores.id')
     ->select('proveedores.*')
     ->where('compras_proveedores.id', $compra_id)
     ->get();
                
       // pagos       
       $suma_monto = 0;
       foreach ($this->pagos2 as $pago) {
             $montoCompra = $pago->monto_compra;
             $actualizacion = $porcentaje_actualizacion_promedio;
            // $pago->actualizacion = $pago->actualizacion + $actualizacion;
             $pago->actualizacion = ((1 + $pago->actualizacion) * (1 + $actualizacion)) - 1;
             // Realizar el cálculo y agregar al total
             $suma_monto += $montoCompra * (1 + $pago->actualizacion);
             
             $this->nuevosPagos[] = [
                "id" => $pago->id,
                "actualizacion" => $pago->actualizacion,
            ];
        }
        
         $this->suma_monto = $suma_monto;

         $this->ventaId = $compra_id;

         $this->total = compras_insumos::where('compras_proveedores.id', $compra_id)->get();

         foreach($this->total as $t) {
         $this->subtotal = $t->subtotal;
         $this->recargos = $t->recargos;
         $total = $suma_total;
         $this->proveedor_id = $t->proveedor_id;
         $this->alicuota_iva = $t->alicuota_iva;
         $this->iva = $t->iva;
         $fecha_compra = $t->created_at;
         $this->Nro_Compra = $t->nro_compra;
         $this->actualizacion = $suma_actualizacion;
         $this->descuento = $t->descuento;
         $this->observacion = $t->observaciones;
         $this->porcentaje_descuento = $t->porcentaje_descuento*100;
         $deuda = $t->deuda;
         }        
         
         $this->total_compra = $total;
         $fechaFormateada = Carbon::parse($fecha_compra)->format('Y-m-d');
         $this->fecha_compra = $fechaFormateada;
         
         //dd($this->total_compra,$this->suma_monto);
         $this->deuda = $this->total_compra - $this->suma_monto;

         $this->accion = 2;
         //dd($this->detalles_actualizacion);
}

public function VerElegirTipoActualizacion(){
$this->accion = 3;    
}

public function ElegirTipoActualizacion($value){
    $this->tipo_actualizacion = $value;
    $this->EditarDeuda($this->NroVenta);
}

public function GetValorIndiceSaldo($compra){
  return $compra->deuda/$compra->total;  
}

public function GetValorAnteriorProducto($detalle,$compra_id){

// % por el cual se actualizo el producto
$datos_actualizacion = actualizaciones::where('product_id',$detalle['producto_id'])->where('referencia_variacion',$detalle['referencia_variacion'])->where('relacion_id',$compra_id)->orderBy('id','desc')->first();
 if($datos_actualizacion != null){
 $valor_viejo = $datos_actualizacion->valor_nuevo;  
 $valor_viejo_real = $datos_actualizacion->valor_nuevo_real;  
} else {
 $valor_viejo = $detalle['precio'];
 $valor_viejo_real = $detalle['precio'];  
}    

return [$valor_viejo,$valor_viejo_real];    
}

public function CalcularActualizacionReal($detalle,$valor_indice_saldo) {
$valor_indice_producto = $detalle['actualizacion_nueva'];
$valor = $valor_indice_saldo * $valor_indice_producto;
return $valor;
}
public function CalcularCostoRealNuevo($valor_anterior_real,$indice_actualizacion_real){
return  $valor_anterior_real * (1 + $indice_actualizacion_real);
}


public function GuardarActualizacion($compra_id){

$compra = compras_insumos::find($compra_id);
$pagos = pagos_facturas::where('id_compra_insumos',$compra_id)->where('eliminado',0)->get();
 
// comparar con el saldo y sacar el nuevo %
$monto_total = $compra->total;
$saldo = $compra->total - $compra->deuda;
$indice_actualizacion_saldo = $this->GetValorIndiceSaldo($compra);

$suma_alicuota_actualizacion = 0;

foreach($this->nuevosDetalles as $detalle){

// Obtener los valores anteriores
$valores_anterior = $this->GetValorAnteriorProducto($detalle,$compra_id);

// Valores anteriores
$valor_anterior = $valores_anterior[0];
$valor_anterior_real = $valores_anterior[1];

// Indices 
$valor_indice_producto = $detalle['actualizacion_nueva'];
$indice_actualizacion_real = $this->CalcularActualizacionReal($detalle,$indice_actualizacion_saldo); // anteriormente $indice_actualizacion_real

// Valores nuevos
$valor_nuevo = $detalle['costo_actual'];
$valor_nuevo_real = $this->CalcularCostoRealNuevo($valor_anterior_real,$indice_actualizacion_real); // cambiar $precio_nuevo por $valor_nuevo_real

//dd($valor_anterior,$valor_anterior_real,$valor_nuevo,$valor_nuevo_real,$valor_indice_producto,$indice_actualizacion_saldo,$indice_actualizacion_real);

if( $valor_anterior < $valor_nuevo ) {
     
$array = [
     'product_id' => $detalle['producto_id'], //
     'referencia_variacion' => $detalle['referencia_variacion'], //
     'comercio_id' => $this->comercio_id, //
     'relacion_id' => $compra->id, //
     'relacion_detail_id' => $detalle['id'],
     'valor_viejo' => $valor_anterior, //
     'valor_nuevo' => $valor_nuevo, //
     'valor_viejo_real' => $valor_anterior_real, //
     'valor_nuevo_real' => $valor_nuevo_real, 
     'porcentaje_saldo' => $indice_actualizacion_saldo, //
     'porcentaje_producto' => $valor_indice_producto, //
     'porcentaje_actualizacion' => $indice_actualizacion_real, //
     'origen' => "compras_insumos",
     'monto_total' => $monto_total, //
     'saldo' => $saldo //
     ];

 //dd($array);

    actualizaciones::create($array);
    
    $indices_acumulados = $this->GetIndicesAcumulados($detalle);
    
    $valor_indice_producto_acumulado = $indices_acumulados[0];
    $valor_indice_actualizacion_acumulado = $indices_acumulados[1];
    
 $detalle_actualizar = detalle_compra_insumos::find($detalle['id']);
 
 $array_detalle = [
 'actualizacion' => $valor_indice_producto_acumulado,
 'actualizacion_real' => $valor_indice_actualizacion_acumulado,
 'precio_final' => $valor_nuevo,
 'precio_final_real' => $valor_nuevo_real     
  ];

 $detalle_actualizar->update($array_detalle);       

}

$cantidad_filas = count($this->nuevosDetalles);
$porcentaje_actualizacion_promedio = $suma_alicuota_actualizacion/$cantidad_filas;

foreach($this->nuevosPagos as $p){
   $pagos_actualizacion = pagos_facturas::find($p['id']);
   $pagos_actualizacion->update(['actualizacion' =>  $p['actualizacion'] ]);
}
 
 $this->ActualizarTotalCompra($compra_id);
 $this->ActualizarEstadoDeuda($compra_id);
 $this->RenderFactura($compra_id);
 
}


}


public function GetIndicesAcumulados($detalle){

    $actualizaciones = actualizaciones::where('relacion_detail_id',$detalle['id'])->get();
 
     // Inicializa variables para el cálculo acumulativo y multiplicativo
    $sum_actualizacion_producto = 1;
    $sum_actualizacion_real = 1;
    
    foreach ($actualizaciones as $valor) {
        
        // Cálculo multiplicativo
        $sum_actualizacion_producto *= (1 + $valor->porcentaje_producto);
        $sum_actualizacion_real *= (1 + $valor->porcentaje_actualizacion);
    }
    
    $sum_actualizacion_producto = $sum_actualizacion_producto - 1;
    $sum_actualizacion_real = $sum_actualizacion_real - 1;
    
    return [$sum_actualizacion_producto,$sum_actualizacion_real];
}

//18-5-2024
public function GetPlazoAcreditacionPago($id){
    return $id == 1 ? 1 : 0;
}

}
