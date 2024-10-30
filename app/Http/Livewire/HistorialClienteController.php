<?php

namespace App\Http\Livewire;


use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\productos_stock_sucursales;
use App\Models\SaleDetail;
use App\Models\produccion;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use App\Models\wocommerce;
use Livewire\WithFileUploads;
use Livewire\WithPagination;


class HistorialClienteController extends Component
{

	use WithFileUploads;
	use WithPagination;

	public $name, $search, $image, $selected_id, $pageTitle, $componentName, $wc_category_id;
	private $pagination = 25;
	private $wc_category;

	public function mount($id_cliente)
	{
		$this->pageTitle = 'Asistente de Produccion';
		$this->componentName = '';
		
		$this->id_cliente = $id_cliente;


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
		

			$data = Sale::join('metodo_pagos','metodo_pagos.id','sales.metodo_pago')
			->select('sales.*','metodo_pagos.nombre as metodo_pago')
			->where('sales.comercio_id', $comercio_id)
			->where('sales.cliente_id', $this->id_cliente)
			->get();

		return view('livewire.historial-cliente.component', [
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



}
