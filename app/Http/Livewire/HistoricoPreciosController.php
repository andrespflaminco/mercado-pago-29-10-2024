<?php

namespace App\Http\Livewire;


use App\Http\Livewire\Scaner;
use App\Models\Category;
use App\Models\tipo_movimiento_stock;
use App\Models\productos_variaciones;
use App\Models\variaciones;
use App\Models\productos_variaciones_datos;
use App\Models\proveedores;
use App\Models\consolidado_stock;
use App\Models\pagos_facturas;
use App\Models\Product;
use App\Models\hoja_ruta;
use App\Models\sucursales;
use App\Models\User;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\DB;

use App\Models\historico_stock;
use App\Models\actualizacion_precios;

use Livewire\Component;
use App\Models\seccionalmacen;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Traits\CartTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HistoricoPreciosController extends Scaner //Component
{

	use WithPagination;
	use WithFileUploads;
	use CartTrait;


	public $ingresos,$egresos,$name,$barcode,$countDetails,$product_id,$referencia_variacion, $sumDetails,$cost,$sucursal_id,$nombre_sucursal_elegida,$from_formateado,$to_formateado,$price,$variaciones, $stock,$details,$productos_variaciones, $alerts,$NroVenta, $detalle_cliente, $categoryid,$search,$tipo_movimiento_id, $image,$selected_id,$pageTitle,$ventaId, $style, $estado2,$estado,$estado_estado, $saleId, $detalle_facturacion, $suma_monto, $rec, $tot, $componentName,$comercio_id, $almacen, $stock_descubierto, $inv_ideal, $proveedor, $cod_proveedor, $proveedor_elegido;
	public $id_almacen;
	public $stock_inicial, $stock_final;
	public $id_categoria;
	public $pagos2 = [];
	public $id_proveedor;
	private $pagination = 25;

	public $query_product;
	public $products_s;
	public $SelectedProducts = [];
	public $selectedAll = FALSE;



	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function mount()
	{
	    $this->productos_variaciones_datos = [];
	    $this->nombre_producto =  null;
        $this->cod_producto = null;
		$this->details = [];
		$this->sumDetails =0;
		$this->countDetails =0;
		$this->reportType = 0;
		$this->userId = 0;
		$this->saleId = 0;
		$this->pageTitle = 'Listado';
		$this->componentName = 'Movimientos de Stock';
		$this->usuario_id = 'Elegir';
		$this->productos_variaciones = [];
        $this->variaciones = [];
              
      // Obtener el primer día de este año
      $this->dateFrom  = Carbon::now()->firstOfYear();
      // hasta
      $this->dateTo = Carbon::now()->format('d-m-Y');
      


	}

	// escuchar eventos
	protected $listeners = [
		'buscar' => 'Buscar',
		'FechaElegida' => 'FechaElegida'
	];

public function Buscar($value){
    //dd($value);
    $this->producto = Product::find($value);
    
    if($this->producto->producto_tipo == "s") {
    $this->nombre_producto = $this->producto->name;
    $this->cod_producto = $this->producto->barcode;
    $this->product_id = $this->producto->id;
    $this->referencia_variacion = 0;
    } else {
    $this->nombre_producto = $this->producto->name;
    $this->cod_producto = $this->producto->barcode;
    $this->productos_variaciones_datos = productos_variaciones_datos::where('product_id', $this->producto->id)->where('eliminado',0)->get();  
    $this->product_id = $this->producto->id;
    $this->emit("abrir-modal-variaciones","");    
    }
    
}

public function ScanearVariacion($value){
    $valor = explode("|-|",$value);
    $this->product_id = $this->producto->id;
    $this->referencia_variacion = $valor[1];
    $pvd = productos_variaciones_datos::where('product_id', $this->producto->id)->where('referencia_variacion',$this->referencia_variacion)->where('eliminado',0)->first();
    //dd($pvd);
    $nombre_variaciones = $pvd->variaciones;
    $this->nombre_producto = $this->nombre_producto." - ".$nombre_variaciones;
    $this->emit("cerrar-modal-variaciones","");  
}



    public function FechaElegida($startDate, $endDate)
    {
      // Manejar las fechas seleccionadas aquí
      $this->dateFrom  = $startDate;
      $this->dateTo = $endDate;
      
      
    }

  public function SetFechas() {
  if($this->dateFrom !== '' || $this->dateTo !== '')
  {
    $this->from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
    $this->to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';


    $this->from_formateado = Carbon::parse($this->dateFrom)->format('d/m/Y');
    $this->to_formateado = Carbon::parse($this->dateTo)->format('d/m/Y');

  }
  
}
    public function LimpiarFiltros(){
        $this->product_id = null;
        $this->referencia_variacion = null;
        $this->dateFrom  = Carbon::now()->firstOfYear();
        $this->dateTo = Carbon::now()->format('d-m-Y');
        $this->SetFechas();
    }
    
	public function render()
	{

	    $this->SetFechas();

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		$casa_central_id = Auth::user()->casa_central_user_id;
			
		$this->comercio_id = $comercio_id;
		
        $this->movimientos = actualizacion_precios::select('actualizacion_precios.*','products.name as nombre_producto','products.barcode')
        ->join('products','products.id','actualizacion_precios.product_id')->where('actualizacion_precios.comercio_id',$casa_central_id);
        
        if($this->product_id) {
        $this->movimientos = $this->movimientos->where('actualizacion_precios.product_id', $this->product_id)
        ->where('actualizacion_precios.referencia_variacion', $this->referencia_variacion);
        }
        $this->movimientos = $this->movimientos->whereBetween('actualizacion_precios.created_at', [$this->from, $this->to])
        ->get();

		return view('livewire.historico-precios.component', [
			'usuario' => User::where('comercio_id', $comercio_id)->select('*')->get(),
			'movimientos' => $this->movimientos
		])
		->extends('layouts.theme-pos.app')
		->section('content');



}

		public function resetProduct()
		{
		 $this->products_s = [];
		}
			 
		public function selectProduct()
	    
	    {
	    $this->query_product = '';

	    $this->resetProduct();
	      }
public function updatedQueryProduct()
{
if(Auth::user()->comercio_id != 1)
$comercio_id = Auth::user()->comercio_id;
else
$comercio_id = Auth::user()->id;

$this->casa_central_id = Auth::user()->casa_central_user_id;

 $this->products_s = 	Product::where('comercio_id', 'like', $this->casa_central_id)->where( function($query) {
	    $query->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
		})
		->where('eliminado',0)
    	->limit(25)
	    ->get()
	    ->toArray();

    
}

    
}
