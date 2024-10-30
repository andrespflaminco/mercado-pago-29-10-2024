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

	public function render()
	{
	    /*
	    $m = historico_stock::where('tipo_movimiento',15)->get();
	    
	    foreach($m as $ma){
	       $hm = historico_stock::find($ma->id);
	       $hm->cantidad_movimiento = $ma->cantidad_movimiento * -1;
	       $hm->save();
	    }
	    */
	    
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

            $this->nombre_sucursal_elegida = User::find($this->sucursal_id)->name;

        if($this->product_id) {
		
		$this->stock_inicial = historico_stock::join('tipo_movimiento_stocks', 'tipo_movimiento_stocks.id', 'historico_stocks.tipo_movimiento')
            ->join('products', 'products.id', 'historico_stocks.producto_id')
            ->select('historico_stocks.*','tipo_movimiento_stocks.nombre as tipo_movimiento','products.name','products.barcode')
            ->where('historico_stocks.comercio_id', $this->sucursal_id)
            ->where('historico_stocks.producto_id', $this->product_id)
            ->where('historico_stocks.referencia_variacion', $this->referencia_variacion)
            ->whereBetween('historico_stocks.created_at', [$this->from, $this->to])
            ->orderBy('historico_stocks.id', 'asc')
            ->first();
        
        if($this->stock_inicial != null){
        $this->stock_inicial = $this->stock_inicial->stock - $this->stock_inicial->cantidad_movimiento;
        } else {$this->stock_inicial = 0;}
        
        // Obtener los movimientos de esos dias
		$movimientos = historico_stock::join('tipo_movimiento_stocks','tipo_movimiento_stocks.id','historico_stocks.tipo_movimiento')
		->join('products','products.id','historico_stocks.producto_id')
		->select('historico_stocks.*',
		'tipo_movimiento_stocks.nombre as tipo_movimiento',
		'products.name',
		'products.barcode'
		)
		->where('historico_stocks.comercio_id', $this->sucursal_id)
		->where('historico_stocks.cantidad_movimiento','<>',0)
		->where('historico_stocks.producto_id',$this->product_id)
            ->where('historico_stocks.referencia_variacion', $this->referencia_variacion)
		->whereBetween('historico_stocks.created_at', [$this->from, $this->to]);
		$movimientos = $movimientos->orderBy('historico_stocks.id','asc')
		->get();
		
		// Obtener los movimientos de esos dias
		$egresos = historico_stock::join('tipo_movimiento_stocks','tipo_movimiento_stocks.id','historico_stocks.tipo_movimiento')
		->join('products','products.id','historico_stocks.producto_id')
		->select('historico_stocks.*',
		'tipo_movimiento_stocks.nombre as tipo_movimiento',
		'products.name',
		'products.barcode'
		)
		->where('historico_stocks.comercio_id', $this->sucursal_id)
		->where('historico_stocks.cantidad_movimiento','<',0)
		->where('historico_stocks.producto_id',$this->product_id)
            ->where('historico_stocks.referencia_variacion', $this->referencia_variacion)
		->whereBetween('historico_stocks.created_at', [$this->from, $this->to])
		->orderBy('historico_stocks.id','asc')
		->get();
		
		// Obtener los movimientos de esos dias
		$ingresos = historico_stock::join('tipo_movimiento_stocks','tipo_movimiento_stocks.id','historico_stocks.tipo_movimiento')
		->join('products','products.id','historico_stocks.producto_id')
		->select('historico_stocks.*',
		'tipo_movimiento_stocks.nombre as tipo_movimiento',
		'products.name',
		'products.barcode'
		)
		->where('historico_stocks.comercio_id', $this->sucursal_id)
		->where('historico_stocks.cantidad_movimiento','>',0)
		->where('historico_stocks.producto_id',$this->product_id)
        ->where('historico_stocks.referencia_variacion', $this->referencia_variacion)
		->whereBetween('historico_stocks.created_at', [$this->from, $this->to])
		->orderBy('historico_stocks.id','asc')
		->get();
		
		//dd($movimientos);
        
        $this->egresos = $egresos->sum('cantidad_movimiento');
        $this->ingresos = $ingresos->sum('cantidad_movimiento');
        
        $this->movimientos = $movimientos;

        
        $this->stock_final = historico_stock::join('tipo_movimiento_stocks', 'tipo_movimiento_stocks.id', 'historico_stocks.tipo_movimiento')
            ->join('products', 'products.id', 'historico_stocks.producto_id')
            ->select('historico_stocks.*','tipo_movimiento_stocks.nombre as tipo_movimiento','products.name','products.barcode')
            ->where('historico_stocks.comercio_id', $this->sucursal_id)
            ->where('historico_stocks.producto_id', $this->product_id)
            ->where('historico_stocks.referencia_variacion', $this->referencia_variacion)
            ->whereBetween('historico_stocks.created_at', [$this->from, $this->to])
            ->orderBy('historico_stocks.id', 'desc')
            ->first();
            
        if($this->stock_final != null){    
        $this->stock_final = $this->stock_final->stock;
        } else {$this->stock_final = 0;}
        
        
        } else {
            
            
            $historico = historico_stock::join('tipo_movimiento_stocks','tipo_movimiento_stocks.id','historico_stocks.tipo_movimiento')
			->join('products','products.id','historico_stocks.producto_id')
			->select('historico_stocks.*','tipo_movimiento_stocks.nombre as tipo_movimiento','products.name','products.barcode')
			->where('historico_stocks.cantidad_movimiento','<>',0)
			->whereBetween('historico_stocks.created_at', [$this->from, $this->to])
			->where('historico_stocks.comercio_id', $this->sucursal_id)
			->orderBy('historico_stocks.id','asc')
			->get();
            
            $this->movimientos = $historico;

                
           $stock_inicial = historico_stock::whereBetween('historico_stocks.created_at', [$this->from, $this->to])
            ->where('historico_stocks.comercio_id', $this->sucursal_id)
            ->orderBy('historico_stocks.created_at', 'asc') // Ordena por created_at de forma descendente
            ->get();

        //dd($stock_inicial);
        
        $acumuladoPorProducto = [];
        $this->stock_inicial = 0;
        
        foreach ($stock_inicial as $historico) {
            $productoId = $historico['producto_id'];
            $productoStock = $historico['stock'] - $historico['cantidad_movimiento'];
        
            // Si el producto_id aún no está en el array acumulado, inicialízalo.
            if (!isset($acumuladoPorProducto[$productoId])) {
                $acumuladoPorProducto[$productoId] = [
                    'producto_id' => $productoId,
                    'stock' => $productoStock,
                ];
                
            // Acumula la cantidad correspondiente al stock.
            $this->stock_inicial += $productoStock;
        
            }
            
        }
        
        if($this->stock_inicial < 0) {$this->stock_inicial = 0;}
                
            $ingresos = historico_stock::select(DB::raw('SUM(historico_stocks.cantidad_movimiento) as total_cantidad') )
                ->where('historico_stocks.cantidad_movimiento', '>', 0)
                ->whereBetween('historico_stocks.created_at', [$this->from, $this->to])
                ->where('historico_stocks.comercio_id', $this->sucursal_id)
                ->first()->total_cantidad;
                
                            
            $egresos = historico_stock::select(DB::raw('SUM(historico_stocks.cantidad_movimiento) as total_cantidad') )
                ->where('historico_stocks.cantidad_movimiento', '<', 0)
                ->whereBetween('historico_stocks.created_at', [$this->from, $this->to])
                ->where('historico_stocks.comercio_id', $this->sucursal_id)
                ->first()->total_cantidad;


            $this->egresos = $egresos;
            $this->ingresos = $ingresos;
            
            $this->stock_final = $this->stock_inicial + $ingresos + $egresos;
    
        }

        //dd($this->movimientos);
        
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
	public function Store()
	{
		$rules  =[
			'name' => 'required|min:3',
			'barcode' => 'required|min:3',
			'cost' => 'required',
			'price' => 'required',
			'stock' => 'required',
			'almacen' => 'not_in:Elegir',
			'proveedor' => 'required',
			'alerts' => 'required',
			'categoryid' => 'required|not_in:Elegir',
			'stock_descubierto' => 'required|not_in:Elegir'
		];

		$messages = [
			'name.required' => 'Nombre del producto requerido',
			'name.min' => 'El nombre debe tener al menos 3 caracteres',
			'name.required' => 'El codigo del producto requerido',
			'barcode.required' => 'El codigo del producto requerido',
			'barcode.min' => 'El codigo debe tener al menos 3 caracteres',
			'cost.required' => 'El costo es requerido',
			'price.required' => 'Precio de venta requerido',
			'proveedor.required' => 'El proveedor es un campo requerido',
			'stock.required' => 'Ingresa las existencias',
			'almacen.not_in' => 'Ingresa el almacen',
			'alerts.required' => 'Falta el valor para las alertas',
			'categoryid.not_in' => 'Elige una categoría válida',
			'stock_descubierto.not_in' => 'Elige si maneja stock o no.'
		];

		$this->validate($rules, $messages);

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$product = Product::create([
			'name' => $this->name,
			'cost' => $this->cost,
			'price' => $this->price,
			'barcode' => $this->barcode,
			'stock' => $this->stock,
			'alerts' => $this->alerts,
			'inv_ideal' => $this->inv_ideal,
			'stock_descubierto' => $this->stock_descubierto,
			'seccionalmacen_id' => $this->almacen,
			'category_id' => $this->categoryid,
			'proveedor_id' => $this->proveedor,
			'comercio_id' => $comercio_id,
			'cod_proveedor' => $this->cod_proveedor
		]);

		if($product) {

			$usuario_id = Auth::user()->id;

			$historico = historico_stock::create([

				'tipo_movimiento' => 6,
				'producto_id' => $product->id,
				'cantidad_movimiento' => $this->stock,
				'stock' => $this->stock,
				'usuario_id' => $usuario_id,
				'comercio_id'  => $comercio_id
			]);
		}

		if($this->image)
		{
			$customFileName = uniqid() . '_.' . $this->image->extension();
			$this->image->storeAs('public/products', $customFileName);
			$product->image = $customFileName;
			$product->save();
		}

		$this->resetUI();
		$this->emit('product-added', 'Producto Registrado');


	}


	////////////////////// DETALLE DEL PEDIDO /////////////////////////



	public function getDetails($saleId)
	{



		$this->details = SaleDetail::join('products as p','p.id','sale_details.product_id')
		->select('sale_details.id','sale_details.price','sale_details.quantity','p.name as product')
		->where('sale_details.sale_id',$saleId)
		->get();

	// funcion anonima mejor conocida en laravel como Closure  (en javascript callback)
	$suma = $this->details->sum(function($item) {
		return $item->price * $item->quantity;
	});

	$this->sumDetails = $suma;
	$this->countDetails = $this->details->sum('quantity');
	$this->saleId = $saleId;

		$this->emit('show-modal','details loaded');
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
    
    
}
