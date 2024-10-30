<?php

namespace App\Http\Livewire;

// Trait

use App\Traits\WocommerceTrait;


use App\Models\Subcategoria;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\sucursales;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use App\Models\wocommerce;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;


class SubcategoriasController extends Component
{

	use WithFileUploads;
	use WithPagination;
	
	use WocommerceTrait;



	public $name, $search, $image, $agregar,$id_check,$selected_id, $pageTitle, $componentName, $wc_category_id;
	private $pagination = 25;
	private $wc_category;
	public $categoria_id;

	public function mount()
	{
	    $this->categoria_id = 'Elegir';
	    $this->estado_filtro = 0;
	    $this->accion_lote = 'Elegir';
		$this->pageTitle = 'Listado';
		$this->componentName = 'Categorías';


	}

	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function render()
	{
        if(Auth::user()->comercio_id != 1)
        $this->comercio_id = Auth::user()->comercio_id;
        else
        $this->comercio_id = Auth::user()->id;
        
        $this->tipo_usuario = User::find($this->comercio_id);
        $this->sucursal_id = $this->comercio_id;
        		    
        $this->casa_central_id = Auth::user()->casa_central_user_id;

		$categorias = Category::where('comercio_id', $this->casa_central_id)->where('eliminado',0)->get();
	    
		$data = Subcategoria::join('categories','categories.id','subcategorias.categoria_id')
		->select('subcategorias.*','categories.name as categoria')
		->where('subcategorias.comercio_id', $this->casa_central_id);
	        if(strlen($this->search) > 0){
	        $data  = $data->where('subcategorias.nombre', 'like', '%' . $this->search . '%');
	    }
		
		$data  = $data->where('subcategorias.eliminado',$this->estado_filtro)
		->orderBy('categories.name','desc')
		->paginate($this->pagination);

        $wc = wocommerce::where('comercio_id', $this->casa_central_id)->first();
        
		return view('livewire.subcategorias.component', [
		    'subcategorias' => $data,
		    'categorias' => $categorias,
		    'wc' => $wc
		    ])
		->extends('layouts.theme-pos.app')
		->section('content');
	}



	public function Edit($id)
	{
	    
	    $this->agregar = 1;
		$record = Subcategoria::find($id);
		$this->name = $record->nombre;
		$this->categoria_id = $record->categoria_id;
		$this->selected_id = $record->id;
		$this->image = null;

		$this->emit('show-modal', 'show modal!');
	}


    public function Agregar() {
        $this->agregar = 1;
    }
    
	public function Store()
	{
	    if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
	
		if($this->categoria_id == "Elegir"){
		    $this->emit("msg-error","Debe elegir la categoria");
		    return;
		}
			
		$rules = [
			'name' => [
			    'required',
			    'categoria_id' => 'not_in:Elegir'
			  //  Rule::unique('subcategorias')->where('comercio_id',$comercio_id)->where('eliminado',0)
			    ],
		];

		$messages = [
			'name.required' => 'Nombre de la categoría es requerido',
			'name.min' => 'El nombre de la categoría debe tener al menos 3 caracteres',
			'categoria_id.not_in' => 'Debe elegir una categoria'
		//	'name.unique' => 'El nombre de la categoría ya existe. Elija otro.'
		];

		$this->validate($rules, $messages);

		$category = Subcategoria::create([
			'nombre' => $this->name,
			'categoria_id' => $this->categoria_id,
			'comercio_id' => $comercio_id
		]);
        
        // Si usa wocommerce
        /*
        $wc = wocommerce::where('comercio_id', $comercio_id)->first();
        
		if($wc != null){
	    $this->FindOrCreateCategoryByName($category->id);
		}
		*/
		//

		$this->resetUI();
		$this->emit('category-added','Subcategoria Registrada');

	}


	public function Update()
	{
	    
	    if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		
		if($this->categoria_id == "Elegir"){
		    $this->emit("msg-error","Debe elegir la categoria");
		    return;
		}
		
		$rules =[
			'name' => ['required',
			//Rule::unique('categories')->ignore($this->selected_id)->where('comercio_id',$comercio_id)->where('eliminado',0)
			],
			'categoria_id' => 'not_in:Elegir'
		];

		$messages =[
			'name.required' => 'Nombre de categoría requerido',
			'name.min' => 'El nombre de la categoría debe tener al menos 3 caracteres',
			'categoria_id.not_in' => 'Debe elegir una categoria'
		];

		$this->validate($rules, $messages);

        	////////////////////////////////////////////////
        
		$subcategoria = Subcategoria::find($this->selected_id);
		
		$subcategoria->update([
			'nombre' => $this->name,
			'categoria_id' => $this->categoria_id,
		]);


        
		////////// WooCommerce ////////////
        /*	
		$wc = wocommerce::where('comercio_id', $comercio_id)->first();

		if($wc != null){
		
		if($category->wc_category_id != null) {
		$return = $this->UpdateCategoriaWC($category->id);    
		
		} else {
		$this->FindOrCreateCategoryByName($category->id);        
		}
        
    	}

        */

		$this->resetUI();
		$this->emit('category-updated', 'Subcategoria Actualizada');



	}


	public function resetUI()
	{
		$this->name ='';
		$this->categoria_id = 'Elegir';
		$this->image = null;
		$this->search ='';
		$this->selected_id =0;
		$this->agregar = 0;
	}

	protected $listeners =[
		'deleteRow' => 'Destroy',
		'RestaurarCategoria' => 'RestaurarCategoria',
        'accion-lote' => 'AccionEnLote'
	];


	public function Destroy(Subcategoria $subcategoria)
	{

		$subcategoria->eliminado = 1;
		$subcategoria->save();
		
		/*
		$productos = Product::where('category_id',$subcategoria->id)->get();
		
		foreach($productos as $p) {
		
		$products = Product::find($p->id);
		$products->category_id = 1;
		    
		} 
		*/

		////////// WooCommerce ////////////
		
		//// falta actualizar los productos a sin categoria cuando se elimina la categoria ///
		
		
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
        
        /*
		$wc = wocommerce::where('comercio_id', $comercio_id)->first();

		if($wc != null){
        $this->DeleteCategoriaWC($category->id);
	    }
        */
        
		$this->resetUI();
		$this->emit('category-updated', 'Subcategoria Eliminada');

	}


	public function Filtro($estado)
	{
	   $this->estado_filtro = $estado;
	   
	}
	
	    // Eliminar en lote 
    
		
	public function RestaurarCategoria(Subcategoria $categoria)
	{
		$categoria->update([
			'eliminado' => 0
		]);

		$this->resetUI();
		$this->emit('category-updated', 'Subcategoria Restaurada');
	}
	
	
	public function AccionEnLote($ids, $id_accion)
    {
    
    if($id_accion == 1) {
        $estado = 0;
        $msg = 'RESTAURADOS';
    } else {
        $estado = 1;
        $msg = 'ELIMINADOS';
    }
    
    $gastos_checked = Subcategoria::select('subcategorias.id','subcategorias.comercio_id')->whereIn('subcategorias.id',$ids)->get();

    $this->id_check = [];
    
    if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;
    
    $wc = wocommerce::where('comercio_id', $comercio_id)->first();
    
    if($wc != null) {
    $return = $this->checkCredentials($wc->url, $wc->ck, $wc->cs);
    } else {
    $return = 0;
    }
    
    foreach($gastos_checked as $pc) {
    
    $pc->eliminado = $estado;
    $pc->save();
    if( ($estado == 1) && ($return != 0) ) {
    $this->DeleteCategoriaWC($pc->id);    
    }
    
    
    }
    
    
    $this->id_check = [];
    $this->accion_lote = 'Elegir';
    
     $this->emit('global-msg',"CATEGORIA ".$msg);
    
    }
    
    public function Sincronizar($categoria_id) {
        
        $this->FindOrCreateCategoryByName($categoria_id);
    }



}

