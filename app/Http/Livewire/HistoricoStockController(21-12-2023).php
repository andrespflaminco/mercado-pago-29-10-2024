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
use App\Models\productos_stock_sucursales;
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

class HistoricoStockController extends Scaner //Component
{

	use WithPagination;
	use WithFileUploads;
	use CartTrait;


	public $name,$barcode,$countDetails, $sumDetails,$cost,$sucursal_id, $price,$variaciones, $stock,$details,$productos_variaciones, $alerts,$NroVenta, $detalle_cliente, $categoryid,$search,$tipo_movimiento_id, $image,$selected_id,$pageTitle,$ventaId, $style, $estado2,$estado,$estado_estado, $saleId, $detalle_facturacion, $suma_monto, $rec, $tot, $componentName,$comercio_id, $almacen, $stock_descubierto, $inv_ideal, $proveedor, $cod_proveedor, $proveedor_elegido;
	public $id_almacen;
	public $stock_inicial, $stock_final;
	public $id_categoria;
	public $pagos2 = [];
	public $arraySinDuplicados;
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
	    $this->nombre_producto = null;
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
    
    $producto = Product::find($value);
    
    if($producto->producto_tipo == "s") {
    $this->nombre_producto = $producto->name;
    $this->cod_producto = $producto->barcode;
    $this->search = $value;
    $this->referencia_variacion = 0;
    } else {
    $variaciones = productos_variaciones_datos::where('product_id', $value)->get();    
    $this->emit();    
    }
    
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

  }
  
}

	public function render()
	{
	    
	    $this->SetFechas();

			if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;
				
				$this->comercio_id = $comercio_id;
				
			if($this->sucursal_id != null) {
              $this->sucursal_id = $this->sucursal_id;
            } else {
              $this->sucursal_id = $comercio_id;
            }


            $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')->where('casa_central_id', $comercio_id)->get();



        if($this->search) {

        $this->stock_inicial = historico_stock::join('tipo_movimiento_stocks', 'tipo_movimiento_stocks.id', 'historico_stocks.tipo_movimiento')
            ->join('products', 'products.id', 'historico_stocks.producto_id')
            ->select('historico_stocks.*','tipo_movimiento_stocks.nombre as tipo_movimiento','products.name','products.barcode')
            ->where('historico_stocks.comercio_id', $this->sucursal_id)
            ->where('historico_stocks.producto_id', $this->search)
            ->whereBetween('historico_stocks.created_at', [$this->from, $this->to])
            ->orderBy('historico_stocks.id', 'asc')
            ->first();

        // Obtener los movimientos de esos dias
		$movimientos = historico_stock::join('tipo_movimiento_stocks','tipo_movimiento_stocks.id','historico_stocks.tipo_movimiento')
		->join('products','products.id','historico_stocks.producto_id')
		->select('historico_stocks.*',
		'tipo_movimiento_stocks.nombre as tipo_movimiento',
		'products.name',
		'products.barcode',
		DB::raw('"movimientos" as referencia') // Agregar columna referencia con el valor "Stock inicial"
		)
		->where('historico_stocks.comercio_id', $this->sucursal_id)
		->where('historico_stocks.cantidad_movimiento','<>',0)
		->where('historico_stocks.producto_id',$this->search)
		->whereBetween('historico_stocks.created_at', [$this->from, $this->to]);
		$movimientos = $movimientos->orderBy('historico_stocks.id','asc')
		->get();

        // Obtener el stock final
        $final = historico_stock::join('tipo_movimiento_stocks', 'tipo_movimiento_stocks.id', 'historico_stocks.tipo_movimiento')
            ->join('products', 'products.id', 'historico_stocks.producto_id')
            ->select(
                //DB::raw('DATE(historico_stocks.created_at) as created_date'), // Obtener solo la fecha
                'historico_stocks.created_at',
                'tipo_movimiento_stocks.nombre as tipo_movimiento',
                'products.name',
                'products.barcode'
            )
            ->where('historico_stocks.comercio_id', $this->sucursal_id)
            ->where('historico_stocks.cantidad_movimiento', '<>', 0)
            ->where('historico_stocks.producto_id', $this->search)
            ->whereBetween('historico_stocks.created_at', [$this->from, $this->to])
            ->orderBy('historico_stocks.id', 'desc')
            ->groupBy(DB::raw('DATE(historico_stocks.created_at)'))
            ->select(DB::raw('MAX(historico_stocks.id) as id'))
            ->get();
        
        // Añadir la columna 'referencia' con el valor 'Stock inicial' y ajustar la hora a '00:00:00'
        $this->stock_final = historico_stock::find($final)->map(function ($item) {
        $item['created_at'] = Carbon::parse($item['created_at'])->format('Y-m-d 23:59:59');
            $item['referencia'] = 'Stock final';
            return $item;
        });


        $resultado = $this->stock_final->concat($movimientos);

        // Ordenar $resultado por created_at de más antiguo a más actual
        $resultadoOrdenado = $resultado->sortBy('created_at');

        $this->movimientos = $resultadoOrdenado;
        

        } else {
            $this->movimientos = [];
        }

        

		return view('livewire.historico-stock.component', [
			'usuario' => User::where('comercio_id', $comercio_id)->select('*')->get(),
				'tipo_movimiento' => tipo_movimiento_stock::select('*')->get()
		])
		->extends('layouts.theme-pos.app')
		->section('content');



}

	      public function updatedQueryProduct()
	      {
					if(Auth::user()->comercio_id != 1)
					$comercio_id = Auth::user()->comercio_id;
					else
					$comercio_id = Auth::user()->id;

					$this->tipo_usuario = User::find($comercio_id);



					if($this->tipo_usuario->sucursal != 1) {
						$this->casa_central_id = $comercio_id;
					} else {

						$this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
						$this->casa_central_id = $this->casa_central->casa_central_id;
					}



	          $this->products_s = 	Product::where('comercio_id', 'like', $this->casa_central_id)->where( function($query) {
							    $query->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
							})
							->where('eliminado',0)
				->limit(25)
	              ->get()
	              ->toArray();


	      }


  public function ElegirSucursal($sucursal_id) {

  	$this->sucursal_id = $sucursal_id;

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

    public function SincronizarHoy(){
       
         // Obtener el inicio del día de hoy (00:00:00)
        $inicioDia = Carbon::today()->startOfDay();
        
        // Obtener el final del día de hoy (23:59:59)
        $finDia = Carbon::today()->endOfDay();

        
        // Obtener los movimientos del día
        $movimientos_hoy = historico_stock::join('tipo_movimiento_stocks', 'tipo_movimiento_stocks.id', 'historico_stocks.tipo_movimiento')
            ->join('products', 'products.id', 'historico_stocks.producto_id')
            ->select('historico_stocks.producto_id', 'historico_stocks.referencia_variacion','historico_stocks.comercio_id', 'products.name')
            ->where('historico_stocks.comercio_id', $this->sucursal_id)
            ->where('historico_stocks.cantidad_movimiento', '<>', 0)
            ->whereBetween('historico_stocks.created_at', [$inicioDia, $finDia]) // Filtrar por rango de fechas
            ->groupBy('historico_stocks.producto_id', 'historico_stocks.referencia_variacion','historico_stocks.comercio_id', 'products.name')
            ->orderBy('historico_stocks.id', 'desc')
            ->get();
  
       // dd($movimientos_hoy);
        
        foreach($movimientos_hoy as $m){
            
            $u = User::find($m->comercio_id);
    
            consolidado_stock::create([
                'product_id' => $m->product_id,
                'referencia_variacion' => $m->referencia_variacion,
                'comercio_id' => $m->comercio_id,
                'casa_central_id'=> $u->casa_central_user_id,
                'stock_real' => $m->stock,
                ]);
        
        }
    }
    
   
public function ConciliarStock(){
    $hs = historico_stock::where('historico_stocks.comercio_id', $this->sucursal_id)
    ->select('historico_stocks.producto_id','historico_stocks.stock')
    ->groupBy('historico_stocks.producto_id','historico_stocks.stock')
    ->latest('historico_stocks.created_at')
    ->get();
    
    $ss = productos_stock_sucursales::join('products','products.id','productos_stock_sucursales.product_id')->where('productos_stock_sucursales.sucursal_id', $this->sucursal_id)
    ->select('products.barcode','productos_stock_sucursales.product_id as producto_id','productos_stock_sucursales.stock_real')
    ->get();
    

// Combinar las colecciones por 'producto_id'
$resultado = $hs->concat($ss)->mapToGroups(function ($item, $key) {
    return [$item->producto_id => $item];
});

// Ahora $resultado contiene una colección con ambos conjuntos de datos combinados por 'producto_id'

$diferencias = [];

foreach ($resultado as $item) {
    $stock = $item->stock ?? 0;
    $stockReal = $item->stock_real ?? 0;

    if ($stock - $stockReal != 0) {
        $diferencias[] = [
            'producto_id' => $item->producto_id,
            'stock' => $stock,
            'stock_real' => $stockReal,
            'diferencia' => $stock - $stockReal,
            // Agrega otras propiedades según tus necesidades
        ];
    }
}

// Ahora $diferencias contiene los elementos con diferencias en stock y stock_real
//dd($diferencias);

$this->arraySinDuplicados = $diferencias;
  
}
 
}
