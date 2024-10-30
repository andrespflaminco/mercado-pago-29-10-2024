<?php

namespace App\Http\Livewire;


use App\Models\Category;
use App\Models\Product;
use App\Models\receta;
use App\Models\unidad_medida_relacion;
use App\Models\unidad_medida;
use App\Models\tipo_unidad_medida;
use App\Models\historico_stock_insumo;
use App\Models\produccion_detalle;
use App\Models\insumo;
use App\Models\productos_stock_sucursales;
use App\Models\SaleDetail;
use App\Models\produccion;
use App\Models\asistente_produccions;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use App\Models\wocommerce;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;


class AsistenteProduccionController extends Component
{

	use WithFileUploads;
	use WithPagination;

	public $name, $search, $image, $selected_id, $pageTitle, $componentName, $wc_category_id;
	private $pagination = 25;
	private $wc_category;

	public function mount()
	{
		$this->pageTitle = 'Asistente de Produccion';
		$this->componentName = '';


	}

	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function render()
	{
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

			$data = asistente_produccions::join('sales','sales.id','asistente_produccions.sale_id')
			->leftjoin('productos_variaciones_datos','productos_variaciones_datos.referencia_variacion','asistente_produccions.referencia_variacion')
			->select('asistente_produccions.*','productos_variaciones_datos.variaciones')
			->where('sales.comercio_id', $comercio_id)
			->where('asistente_produccions.estado', 0)
			->get();

		return view('livewire.asistente-produccion.component', [
			'data' => $data
		])
		->extends('layouts.theme.app')
		->section('content');
	}



	public function Edit($id)
	{
		$record = Category::find($id, ['id','name','image']);
		$this->name = $record->name;
		$this->selected_id = $record->id;
		$this->image = null;

		$this->emit('show-modal', 'show modal!');
	}



	public function Store()
	{
		$rules = [
			'name' => 'required|min:3|unique:categories,name,{$this->selected_id}'
		];

		$messages = [
			'name.required' => 'Nombre de la categoría es requerido',
			'name.min' => 'El nombre de la categoría debe tener al menos 3 caracteres',
			'name.unique' => 'El nombre de la categoría ya existe. Elija otro.'
		];

		$this->validate($rules, $messages);

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

			////////// WooCommerce ////////////

			$wc = wocommerce::where('comercio_id', $comercio_id)->first();

			if($wc != null){

			$woocommerce = new Client(
				$wc->url,
				$wc->ck,
				$wc->cs,

					[
							'version' => 'wc/v3',
					]
			);

			$data = [
			    'name' => $this->name,
			    'image' => [
			        'src' => ''
			    ]
			];

			$this->wc_category = $woocommerce->post('products/categories', $data);

		}

        if($wc != null){
            $this->wc_category_id = $this->wc_category->id;
        } else {
            $this->wc_category_id = 0;
        }
		////////////////////////////////////////////////

		$category = Category::create([
			'name' => $this->name,
			'comercio_id' => $comercio_id,
			'wc_category_id' => $this->wc_category_id
		]);


		$customFileName;
		if($this->image)
		{
			$customFileName = uniqid() . '_.' . $this->image->extension();
			$this->image->storeAs('public/categories', $customFileName);
			$category->image = $customFileName;
			$category->save();
		}

		$this->resetUI();
		$this->emit('category-added','Categoría Registrada');

	}


	public function Update()
	{
		$rules =[
			'name' => "required|min:3|unique:categories,name,{$this->selected_id}"
		];

		$messages =[
			'name.required' => 'Nombre de categoría requerido',
			'name.min' => 'El nombre de la categoría debe tener al menos 3 caracteres'
		];

		$this->validate($rules, $messages);


		$category = Category::find($this->selected_id);

		////////// WooCommerce ////////////
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$wc = wocommerce::where('comercio_id', $comercio_id)->first();

		if($wc != null){

		$woocommerce = new Client(
			$wc->url,
			$wc->ck,
			$wc->cs,

				[
						'version' => 'wc/v3',
				]
		);

		$data = [
				'name' => $this->name,
				'image' => [
						'src' => ''
				]
		];

		$woocommerce->put('products/categories/'.$category->wc_category_id , $data);

	}

	////////////////////////////////////////////////

		$category->update([
			'name' => $this->name
		]);

		if($this->image)
		{
			$customFileName = uniqid() . '_.' . $this->image->extension();
			$this->image->storeAs('public/categories', $customFileName);
			$imageName = $category->image;

			$category->image = $customFileName;
			$category->save();

			if($imageName !=null)
			{
				if(file_exists('storage/categories' . $imageName))
				{
					unlink('storage/categories' . $imageName);
				}
			}

		}

		$this->resetUI();
		$this->emit('category-updated', 'Categoría Actualizada');



	}


	public function resetUI()
	{
		$this->name ='';
		$this->image = null;
		$this->search ='';
		$this->selected_id =0;
	}

	protected $listeners =[
		'deleteRow' => 'Destroy'
	];


	public function Destroy(Category $category)
	{

		$imageName = $category->image;
		$category->delete();

		////////// WooCommerce ////////////
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$wc = wocommerce::where('comercio_id', $comercio_id)->first();

		if($wc != null){

		$woocommerce = new Client(
			$wc->url,
			$wc->ck,
			$wc->cs,

				[
						'version' => 'wc/v3',
				]
		);

		$data = [
				'name' => $this->name,
				'image' => [
						'src' => ''
				]
		];

		$woocommerce->delete('products/categories/'.$category->wc_category_id , ['force' => true]);
	}

	////////////////////////////////////////////////

		if($imageName !=null) {
			unlink('storage/categories/' . $imageName);
		}

		$this->resetUI();
		$this->emit('category-deleted', 'Categoría Eliminada');

	}

public function IniciarProduccion($id, $product_id, $referencia_variacion, $cantidad) {

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

	DB::beginTransaction();

	try {

		   $this->receta = Product::leftjoin('recetas as r','r.product_id','products.id')
		   ->leftjoin('insumos','insumos.id','r.insumo_id')
		   ->join('unidad_medidas','unidad_medidas.id','r.unidad_medida')
		   ->select(receta::raw(' SUM(r.cantidad*r.costo_unitario*r.relacion_medida) AS cost'))
		   ->where('products.id', $product_id)
		   ->where('r.referencia_variacion', $referencia_variacion)
		   ->where('products.eliminado', 'like', 0)
		   ->first();

			 if($this->receta->cost == null){
				emit($msg, "Debe crear la receta");
			 }

			 $this->products = Product::where('products.id', $product_id)
			 ->where('products.eliminado', 'like', 0)
			 ->first();


					$sale = produccion::create([
						'total' => $this->receta->cost,
						'items' => -$cantidad,
						'observaciones' => '',
						'estado' => 2,
						'comercio_id' => $comercio_id,
						'user_id' => Auth::user()->id
					]);

		if($sale)
		{

				$this->produccion_detalle_id = produccion_detalle::create([
					'producto_id' => $product_id,
					'costo' => $this->receta->cost,
					'nombre' => $this->products->name,
					'barcode' => $this->products->barcode,
					'referencia_variacion' => $referencia_variacion,
					'cantidad' => -$cantidad,
					'estado' => 2,
					'produccion_id' => $sale->id,
					'comercio_id' => $comercio_id
				]);

            //////////////////////////////////////////////////////////////////////////////////////////

            //////////// CUANDO EL ESTADO DE LA PRODUCCION ES EN PROCESO  /////////////////////
			
				$this->estado = 2;

				if($this->estado == 2) {

					$receta = receta::where('product_id', $product_id)
					->where('referencia_variacion', $referencia_variacion)
					->where('eliminado',0)
					->get();

					foreach ($receta as $r) {


						$insumos = insumo::find($r->insumo_id);


						// RELACION DE UNIDADES DE MEDIDA...

						$this->relacion_receta = unidad_medida_relacion::where('unidad_medida',$r->unidad_medida)->first();

						$this->relacion_insumo = unidad_medida_relacion::where('unidad_medida', $insumos->unidad_medida)->first();


						$this->relacion_medidas = $this->relacion_receta->relacion/$this->relacion_insumo->relacion;

						$this->relacion_cantidades = $r->cantidad/$insumos->cantidad;

						$this->relacion = $this->relacion_cantidades * $this->relacion_medidas;

						// ..................................

						$this->cantidad_insumos = -1*($cantidad*$this->relacion);

						$this->stock_nuevo_insumos = $insumos->stock-$this->cantidad_insumos;



						$historico_stock = historico_stock_insumo::create([
							'tipo_movimiento' => 11,
							'insumo_id' => $r->insumo_id,
							'produccion_detalle_id' => $this->produccion_detalle_id->id,
							'cantidad_receta' => $r->cantidad,
							'unidad_medida_receta' => $r->unidad_medida,
							'cantidad_movimiento' => -$this->cantidad_insumos,
							'cantidad_contenido' => $insumos->cantidad,
							'unidad_medida_insumo' => $insumos->unidad_medida,
							'relacion_unidad_medida' => $this->relacion,
							'stock' => $this->stock_nuevo_insumos,
							'comercio_id'  => $comercio_id,
							'usuario_id'  => Auth::user()->id
						]);

						$insumos->stock = $this->stock_nuevo_insumos;
						$insumos->save();
						

					}

				}

				$a_p = asistente_produccions::find($id);

				$a_p->update([
					'estado' => 1
				]);
////////////////////////////////////////////////////////////////////////////////////////////////

			}


		DB::commit();

		$this->observaciones = '';
		$this->monto = 0;

		$this->emit('msg','Produccion registrada con éxito');

		return auth::user();



	} catch (Exception $e) {
		DB::rollback();
		$this->emit('sale-error', $e->getMessage());
	}



}

}
