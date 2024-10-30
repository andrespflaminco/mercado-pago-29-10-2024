<?php

namespace App\Http\Livewire;


use App\Http\Livewire\Scaner;
use App\Models\Category;
use App\Models\tipo_movimiento_stock;
use App\Models\productos_variaciones;
use App\Models\variaciones;
use App\Models\productos_stock_sucursales;
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
use Livewire\Component;
use App\Models\seccionalmacen;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Traits\CartTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ControladorStockController extends Scaner //Component
{

	use WithPagination;
	use WithFileUploads;
	use CartTrait;


	public $name,$barcode,$countDetails, $product_id,$sumDetails,$cost,$stock_anterior,$sucursal_id, $price,$variaciones, $stock,$details,$productos_variaciones, $alerts,$NroVenta, $detalle_cliente, $categoryid,$search,$tipo_movimiento_id, $image,$selected_id,$pageTitle,$ventaId, $style, $estado2,$estado,$estado_estado, $saleId, $detalle_facturacion, $suma_monto, $rec, $tot, $componentName,$comercio_id, $almacen, $stock_descubierto, $inv_ideal, $proveedor, $cod_proveedor, $proveedor_elegido;
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
	
	public $sucursal_stock;



	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function mount()
	{
	    $this->cambio_hecho = false;
	    $this->cod_producto = null;
	    $this->nombre_producto = null;
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

public function Buscar(){
    //dd($value);
    
    $this->producto = Product::where('barcode',$this->query_product)->where('comercio_id',$this->casa_central_id)->first();
    
    if( $this->producto == null){
        $this->emit("msg-error","El codigo ingresado no existe");
        return;
    }
    
    
    if($this->producto->producto_tipo == "s") {
    $this->nombre_producto = $this->producto->name;
    $this->cod_producto = $this->producto->barcode;
    $this->product_id = $this->producto->id;
    $this->referencia_variacion = 0;
    } else {
    productos_variaciones_datos::where('product_id', $this->producto->id)->get();    
    $this->emit();    
    }
    
}



	public function render()
	{

	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;
				
	$this->comercio_id = $comercio_id;
	$this->casa_central_id  = Auth::user()->casa_central_user_id;	
	
	if($this->comercio_id == Auth::user()->casa_central_user_id){
	    $this->sucursal_stock = 0;
	} else {
	    $this->sucursal_stock = $this->comercio_id;
	}
	
    $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')->where('casa_central_id', $comercio_id)->get();

    if($this->product_id) {
	
    $this->stock_anterior = productos_stock_sucursales::where('product_id',$this->product_id)
    ->where('referencia_variacion',$this->referencia_variacion)
    ->where('sucursal_id',$this->sucursal_stock)
    ->where('eliminado',0)
    ->first()
    ->stock_real;
    
    //dd($this->stock_anterior);
    } else {
    $this->stock_anterior = null;
    }

    //dd($this->stock_anterior);
    
		return view('livewire.controlador-stock.component', [
			'usuario' => User::where('comercio_id', $comercio_id)->select('*')->get(),
				'tipo_movimiento' => tipo_movimiento_stock::select('*')->get()
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

    
    public function LimpiarBusqueda() {
    $this->resetUI();
    }
    
    public function Guardar(){
    
    $stock_nuevo = $this->stock;
    
    $product_stock = productos_stock_sucursales::where('sucursal_id',$this->sucursal_stock)
    ->where('product_id',$this->product_id)
    ->where('referencia_variacion',$this->referencia_variacion)
    ->where('eliminado',0)->first();
    
    //dd($product_stock);
    
    $diferencia = $product_stock->stock_real - $product_stock->stock;
   
    $stock_disponible_nuevo = $this->stock - $diferencia;
    //dd($stock_disponible_nuevo); 
    $cantidad_movimiento = $this->stock - $product_stock->stock_real;
    
    $array = ['stock_real' => intval($this->stock),'stock' => $stock_disponible_nuevo];
    //dd($array);
    //dd($product_stock);
    $product_stock->update($array);
    
    //dd($product_stock);
    
	$historico_stock = historico_stock::create([
		'tipo_movimiento' => 20,
		'producto_id' => $this->product_id,
		'cantidad_movimiento' => $cantidad_movimiento,
		'stock' => $this->stock,
		'usuario_id' => Auth::user()->id,
		'comercio_id'  => $this->comercio_id
	]);
	
	
    
    $this->cambio_hecho = true;
    
    if($this->cambio_hecho == true) {
    $this->stock_nuevo = productos_stock_sucursales::where('product_id',$this->product_id)
    ->where('referencia_variacion',$this->referencia_variacion)
    ->where('sucursal_id',$this->sucursal_stock)
    ->where('eliminado',0)->first()->stock_real;
    }

    if($this->stock != $this->stock_nuevo){
        $this->msg="text-danger";
    } else {
        $this->msg="text-success";
    }

    }
    
    public function Volver(){
        return redirect('controlador-stock');
    }
    
    public function resetUI() {
    $this->stock_anterior = null;
    $this->stock = null;
    $this->cod_producto = null;
    $this->nombre_producto = null;
    $this->product_id = null;
    $this->query_product = null;
    $this->referencia_variacion = null;
    }
}
