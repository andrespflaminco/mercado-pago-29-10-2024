<?php

namespace App\Http\Livewire;

// Trait

use App\Traits\WocommerceTrait;

use App\Models\marcas;
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


class MarcasController extends Component
{

	use WithFileUploads;
	use WithPagination;
	
	use WocommerceTrait;



	public $name, $search, $image, $agregar,$id_check,$selected_id, $pageTitle, $componentName, $wc_category_id;
	private $pagination = 25;
	private $wc_category;

	public function mount()
	{
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
        		    
        if($this->tipo_usuario->sucursal != 1) {
        
        $this->casa_central_id = $this->comercio_id;
        	
        } else {
        	  
        $this->casa_central = sucursales::where('sucursal_id', $this->comercio_id)->first();
        $this->casa_central_id = $this->casa_central->casa_central_id;
        
        }

		if(strlen($this->search) > 0)
			$data = marcas::where('name', 'like', '%' . $this->search . '%')
			->where('comercio_id', 'like', $this->casa_central_id)
			->where('eliminado', $this->estado_filtro)
			->paginate($this->pagination);
		else
			$data = marcas::where('comercio_id', 'like', $this->casa_central_id)
			->where('eliminado',$this->estado_filtro)
			->orderBy('id','desc')
			->paginate($this->pagination);

		return view('livewire.marcas.component', [
		    'marcas' => $data
		    ])
		->extends('layouts.theme-pos.app')
		->section('content');
	}



	public function Edit($id)
	{
	    
	    $this->agregar = 1;
		$record = marcas::find($id, ['id','name','image']);
		$this->name = $record->name;
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
		
		$rules = [
			'name' => ['required',Rule::unique('marcas')->where('comercio_id',$comercio_id)->where('eliminado',0)],
		];

		$messages = [
			'name.required' => 'Nombre de la marca es requerido',
			'name.min' => 'El nombre de la marca debe tener al menos 3 caracteres',
			'name.unique' => 'El nombre de la marca ya existe. Elija otro.'
		];

		$this->validate($rules, $messages);

		$category = marcas::create([
			'name' => $this->name,
			'comercio_id' => $comercio_id
		]);
        
		$customFileName;
		if($this->image)
		{
			$customFileName = uniqid() . '_.' . $this->image->extension();
			$this->image->storeAs('public/marcas', $customFileName);
			$category->image = $customFileName;
			$category->save();
		}

		$this->resetUI();
		$this->emit('category-added','Marca Registrada');

	}


	public function Update()
	{
	    
	    if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		
		$rules =[
			'name' => ['required',Rule::unique('marcas')->ignore($this->selected_id)->where('comercio_id',$comercio_id)->where('eliminado',0)],
		];

		$messages =[
			'name.required' => 'Nombre de categoría requerido',
			'name.min' => 'El nombre de la categoría debe tener al menos 3 caracteres'
		];

		$this->validate($rules, $messages);

        	////////////////////////////////////////////////
        
		$category = marcas::find($this->selected_id);
		
		$category->update([
			'name' => $this->name
		]);

		if($this->image)
		{
			$customFileName = uniqid() . '_.' . $this->image->extension();
			$this->image->storeAs('public/marcas', $customFileName);
			$imageName = $category->image;

			$category->image = $customFileName;
			$category->save();

			if($imageName !=null)
			{
				if(file_exists('storage/marcas' . $imageName))
				{
					unlink('storage/marcas' . $imageName);
				}
			}

		}

		$this->resetUI();
		$this->emit('category-updated', 'Marca Actualizada');



	}


	public function resetUI()
	{
		$this->name ='';
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


	public function Destroy(marcas $marcas)
	{

		$marcas->eliminado = 1;
		$marcas->save();

		$this->resetUI();
		$this->emit('category-updated', 'Marca Eliminada');

	}


	public function Filtro($estado)
	{
	   $this->estado_filtro = $estado;
	   
	}
	
	    // Eliminar en lote 
    
		
	public function RestaurarCategoria(marcas $marca)
	{
		$marca->update([
			'eliminado' => 0
		]);

		$this->resetUI();
		$this->emit('category-updated', 'Marca Restaurada');
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
    
    $marcas_checked = marcas::select('marcas.id','marcas.comercio_id')->whereIn('marcas.id',$ids)->get();

    $this->id_check = [];

    foreach($marcas_checked as $pc) {
    
    $pc->eliminado = $estado;
    $pc->save();

    }
    
    
    $this->id_check = [];
    $this->accion_lote = 'Elegir';
    
     $this->emit('global-msg',"MARCA ".$msg);
    
    }


}
