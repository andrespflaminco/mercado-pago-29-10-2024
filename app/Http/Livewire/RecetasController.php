<?php

namespace App\Http\Livewire;


use App\Http\Livewire\Scaner;
use App\Models\Category;
use Illuminate\Validation\Rule;
use App\Models\proveedores;
use App\Models\Product;
use App\Models\receta;
use App\Models\historico_stock;
use Livewire\Component;
use App\Models\productos_variaciones_datos;
use App\Models\productos_variaciones;
use App\Models\seccionalmacen;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Traits\CartTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RecetasController extends Scaner //Component
{

	use WithPagination;
	use WithFileUploads;
	use CartTrait;


	public $name,$barcode,$cost,$price,$stock, $tipo_producto, $alerts,$categoria_id, $categoryid,$search,$image,$selected_id,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $inv_ideal, $receta, $proveedor,$rinde ,$sum_receta, $cantidad_receta, $cod_proveedor, $proveedor_elegido, $ecommerce_canal, $mostrador_canal, $name_almacen, $descripcion, $image_categoria, $name_categoria;
	public $id_almacen;
	public $id_categoria;
	public $id_proveedor;
	private $pagination = 10;

	public $SelectedProducts = [];
	public $selectedAll = FALSE;

	public $sortColumn = "name";
    public $sortDirection = "asc";
    public $appUrl;
    
    public $filter = 'all'; // Valor por defecto



	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function mount()
	{
		$this->metodo_pago = session('MetodoPago');
		$this->pageTitle = 'Listado';
		$this->proveedor = '1';
		$this->componentName = 'Recetas';
		$this->categoryid = 'Elegir';
		$this->almacen = 'Elegir';
		$this->stock_descubierto = 'Elegir';
		$this->OrderNombre = "ASC";
		$this->OrderBarcode = "ASC";
		$this->receta = [];


	}

	protected $listeners =[
		'deleteRow' => 'Destroy',
	];

	public function sort($column)
	{
			$this->sortColumn = $column;
			$this->sortDirection = $this->sortDirection == 'asc' ? 'desc' : 'asc';
	}

	public function render()
	{
	    $this->appUrl = config('app.url');
	     set_time_limit(300);

			if(Auth::user()->comercio_id != 1)
			$comercio_id = Auth::user()->comercio_id;
			else
			$comercio_id = Auth::user()->id;

            $casa_central_id = Auth::user()->casa_central_user_id;
            
            // listamos los productos //
            
            
			$products = Product::select('products.name','products.barcode','products.producto_tipo','products.id')
			->where('products.comercio_id', 'like', $casa_central_id)
			->where('products.eliminado', 'like', 0)
			->where('products.tipo_producto','>', 1);


			if(strlen($this->search) > 0) {

			$products = $products->where( function($query) {
					 $query->where('products.name', 'like', '%' . $this->search . '%')
						->orWhere('products.barcode', 'like',$this->search . '%');
					});

			}
			
			if(strlen($this->categoria_id) > 0) {

			$products = $products->where('products.category_id', $this->categoria_id);

			}
			
			if($this->filter != 'all') {

            if($this->filter == 1) {
            $products = $products->where('products.cost', '>' , 0);    
            }
            
            if($this->filter == 0) {
            $products = $products->where('products.cost', 0);    
            }
			

			}

			$products = $products
			->orderBy('products.name', 'ASC')
			->paginate($this->pagination);
			
			
			// obtenemos los id de los productos listados
			
			
			$item_ids = $products->items();
			
			$items_id = [];
			
	    	foreach ($item_ids as $i_id) {
		    $id_id = $i_id->id;
		    array_push($items_id, $id_id);
		    
		    } 
		    
		    // Buscamos las variaciones de los productos listados
		    
		    $this->productos_variaciones_datos = productos_variaciones_datos::where('comercio_id',$casa_central_id)->where('eliminado',0)->whereIn('product_id',$items_id)->get();
			
			$this->variaciones = productos_variaciones::join('atributos','atributos.id','productos_variaciones.atributo_id')
			->join('variaciones','variaciones.id','productos_variaciones.variacion_id')
			->select('productos_variaciones.referencia_id','variaciones.nombre as nombre_variacion')
			->where('variaciones.comercio_id', $casa_central_id)
			->get();
			
			
			///////////////////////////////////////////////////////
			
			
			$recetas = Product::leftjoin('recetas as r','r.product_id','products.id')
			->leftjoin('insumos','insumos.id','r.insumo_id')
			->select('products.name','r.rinde','products.producto_tipo','products.id','r.product_id','r.referencia_variacion',receta::raw(' (CASE WHEN r.eliminado = 0 THEN ( SUM(r.cantidad*r.costo_unitario*r.relacion_medida)) ELSE 0 END) AS cost'))
			->where('products.comercio_id', 'like', $casa_central_id)
			->where('products.eliminado', 'like', 0)
			->whereIn('product_id',$items_id)
			->where('products.tipo_producto', '>', 1);


			if(strlen($this->search) > 0) {

			$recetas = $recetas->where( function($query) {
					 $query->where('products.name', 'like', '%' . $this->search . '%')
						->orWhere('products.barcode', 'like',$this->search . '%');
					});

			}

			$recetas = $recetas->groupBy('r.eliminado','r.rinde','products.producto_tipo','r.referencia_variacion','products.name','products.id','r.product_id')
			->orderBy('products.name', 'ASC')
			->get();
			
			//  dd($recetas);
			

			return view('livewire.recetas.component', [
				'data' => $products,
				'recetas' => $recetas,
				'categories' => Category::orderBy('name','asc')->where('comercio_id', 'like', $comercio_id)->get(),
				'almacenes' => seccionalmacen::orderBy('nombre','asc')->where('comercio_id', 'like', $comercio_id)->get(),
				'prov' => proveedores::orderBy('nombre','asc')->where('comercio_id', 'like', $comercio_id)->get()
			])
			->extends('layouts.theme-pos.app')
			->section('content');

}

	public function Store()
	{

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$rules  =[
			'name' => 'required|min:3',
			'barcode' => ['required',Rule::unique('products')->where('comercio_id',$comercio_id)],
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
			'barcode.unique' => 'El codigo del producto ya esta en uso',
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
			'cod_proveedor' => $this->cod_proveedor,
			'mostrador_canal' => $this->mostrador_canal,
			'ecommerce_canal' => $this->ecommerce_canal,
			'descripcion' => $this->descripcion
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

		$this->emit('product-added', 'Producto Registrado');


	}


	public function Edit($id_receta)
	{

		$array = explode("&", $id_receta);

		$this->receta_id = $array[0];
		$this->referencia_variacion = $array[1];

		$this->receta = receta::join('insumos','insumos.id','recetas.insumo_id')
		->join('unidad_medidas','unidad_medidas.id','recetas.unidad_medida')
		->where('recetas.product_id', $this->receta_id)
		->where('recetas.referencia_variacion', $this->referencia_variacion)
		->select('insumos.name as nombre_insumo','recetas.rinde','recetas.cantidad','recetas.relacion_medida','recetas.costo_unitario','insumos.cost','insumos.cantidad as cantidad_insumo','unidad_medidas.nombre as unidad_medida')
		->orderBy('recetas.insumo_id')->get();

		$this->sum_receta = $this->receta->sum(function($item){
				return $item->costo_unitario * $item->cantidad * $item->relacion_medida  ;
		});

		$this->cantidad_receta = $this->receta->count(function($item){
				return $item->cantidad  ;
		});
		
		$this->rinde = $this->receta->average(function($item){
				return $item->rinde  ;
		});


		$this->emit('modal-show','Show modal');
	}


		public function ExportarRecetas() {

		    return redirect('recetas/excel/'. Carbon::now()->format('d_m_Y_H_i_s'));
		    }



public function Destroy($id) {

$array = explode("&" , $id);

$product_id = $array[0];
$referencia_variacion = $array[1];

$recetas = receta::where('product_id', $id)->where('referencia_variacion', $referencia_variacion )->where('eliminado', 0)->get();

foreach ($recetas as $r) {
    
    $recet = receta::find($r->id);
    
    $recet->eliminado = 1;
    $recet->save();

    
}

}


public function ActualizarCostosProductosSimples(){
$casa_central_id = Auth::user()->casa_central_user_id;
            
// listamos los productos //
$products = Product::select('products.*')
->where('products.comercio_id', $casa_central_id)
->where('products.eliminado', 0)
->where('products.tipo_producto','>', 1)
->get();

foreach($products as $p){
$this->ActualizarCostos($p->id,0);       
}
 
}


public function ActualizarCostos($producto_id,$referencia_variacion){

        $cost = receta::where('product_id',$producto_id)
		->where('referencia_variacion',$referencia_variacion)
		->select('recetas.product_id','recetas.referencia_variacion','recetas.rinde',receta::raw(' (CASE WHEN recetas.eliminado = 0 THEN ( SUM(recetas.cantidad*recetas.costo_unitario*recetas.relacion_medida)) ELSE 0 END) AS cost'))
		->groupBy('recetas.product_id','recetas.referencia_variacion','recetas.rinde','recetas.eliminado')
		->first();
		
		if($cost != null){
		if($referencia_variacion != 0) {
		$update_product = productos_variaciones_datos::where('product_id',$producto_id)->where('referencia_variacion',$referencia_variacion)->orderBy('id','desc')->first();
		$update_product->cost = $cost->cost/$cost->rinde;
		$update_product->save();
		} else {
		 $update_product = Product::find($producto_id);
		 $update_product->cost = $cost->cost/$cost->rinde;
		 $update_product->save();
		}		    
		}


}

    
    
}
