<?php

namespace App\Http\Livewire;


use App\Models\Category;
use App\Models\ayuda;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use App\Models\wocommerce;
use Livewire\WithFileUploads;
use Livewire\WithPagination;


use Illuminate\Http\Request;

class AyudaController extends Component
{

	use WithFileUploads;
	use WithPagination;



	public $name, $search, $image,$categoria, $selected_id, $pageTitle, $componentName, $wc_category_id;
	private $pagination = 5;
	private $wc_category;
	public $IdView;

	public function mount(Request $request)
	{
		$this->pageTitle = 'Listado';
		$this->componentName = 'Categorías';
        $Id = $request->input('ayuda_id');
        $this->IdView = $Id;

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

		if(strlen($this->search) > 0) {
			$data = ayuda::where('titulo', 'like', '%' . $this->search . '%');
			
			if($this->categoria) {
			    $data = $data->where('categoria', $this->categoria);
			}
			$data = $data->orderBy('categoria','desc')
			->get();
		} else {
			$data = ayuda::orderBy('categoria','desc');
			
			if($this->categoria) {
			    $data = $data->where('categoria', $this->categoria);
			}
			$data = $data->get();
		}

        if($this->IdView == null){
        $viewName = 'livewire.ayuda.component';            
        } else {
        $viewName = 'livewire.ayuda.ayuda' . $this->IdView;        
        }
        
        //dd($viewName);
        
        // Verifica si la vista existe, si no, devuelve un error 404
        if (!view()->exists($viewName)) {
            abort(404);
        }
            
		return view($viewName, ['ayuda' => $data])
		->extends('layouts.theme-pos.app')
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
