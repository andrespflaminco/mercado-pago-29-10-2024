<?php

namespace App\Http\Livewire;


use App\Models\etiquetas;
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


class EtiquetasMarcadoresController extends Component
{

	use WithFileUploads;
	use WithPagination;
	

	public $origen, $nombre, $search, $image, $agregar,$id_check,$selected_id, $pageTitle, $componentName, $wc_category_id;
	private $pagination = 25;
	private $wc_category;

	public function mount()
	{
	    $this->origen ="Elegir";
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
        
        
		if(strlen($this->search) > 0)
			$data = etiquetas::where('nombre', 'like', '%' . $this->search . '%')
			->where('comercio_id', 'like', $this->casa_central_id)
			->where('eliminado', $this->estado_filtro)
			->paginate($this->pagination);
		else
			$data = etiquetas::where('comercio_id', 'like', $this->casa_central_id)
			->where('eliminado',$this->estado_filtro)
			->orderBy('id','desc')
			->paginate($this->pagination);

		return view('livewire.etiquetas-marcadores.component', [
		    'etiquetas' => $data
		    ])
		->extends('layouts.theme-pos.app')
		->section('content');
	}



	public function Edit($id)
	{
	    
	    $this->agregar = 1;
		$record = etiquetas::find($id, ['id','nombre','origen']);
		$this->nombre = $record->nombre;
		$this->origen = $record->origen;
		$this->selected_id = $record->id;

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
			'nombre' => ['required',Rule::unique('etiquetas')->where('comercio_id',$comercio_id)->where('eliminado',0)],
			'origen' => 'not_in:Elegir'
		];

		$messages = [
			'nombre.required' => 'Nombre de la categoría es requerido',
			'nombre.min' => 'El nombre de la categoría debe tener al menos 3 caracteres',
			'nombre.unique' => 'El nombre de la categoría ya existe. Elija otro.',
			'origen.not_in' => 'Elegi un origen valido'
		];

		$this->validate($rules, $messages);

		$etiqueta = etiquetas::create([
			'nombre' => $this->nombre,
			'origen' => $this->origen,
			'comercio_id' => $comercio_id
		]);
        
      
		$this->resetUI();
		$this->emit('category-added','Etiqueta Registrada');

	}


	public function Update()
	{
	    
	    if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		
		$rules =[
			'nombre' => ['required',Rule::unique('etiquetas')->ignore($this->selected_id)->where('comercio_id',$comercio_id)->where('eliminado',0)],
			'origen' => 'not_in:Elegir'

		];

		$messages =[
			'nombre.required' => 'Nombre de etiqueta requerido',
			'nombre.min' => 'El nombre de la etiqueta debe tener al menos 3 caracteres',
			'origen.not_in' => 'Elegi un origen valido'
		];


		$this->validate($rules, $messages);

        	////////////////////////////////////////////////
        
		$etiquetas = etiquetas::find($this->selected_id);
		
		$etiquetas->update([
			'nombre' => $this->nombre,
			'origen' => $this->origen
		]);


		$this->resetUI();
		$this->emit('category-updated', 'Etiqueta Actualizada');



	}


	public function resetUI()
	{
		$this->nombre ='';
		$this->image = null;
		$this->search ='';
		$this->selected_id =0;
		$this->agregar = 0;
		$this->origen = "Elegir";
	}

	protected $listeners =[
		'deleteRow' => 'Destroy',
		'RestaurarCategoria' => 'RestaurarCategoria',
        'accion-lote' => 'AccionEnLote'
	];


	public function Destroy(etiquetas $etiqueta)
	{

		$etiqueta->eliminado = 1;
		$etiqueta->save();
		
	//	$productos = Product::where('category_id',$category->id)->get();
		
	//	foreach($productos as $p) {
		
	//	$products = Product::find($p->id);
	//	$products->category_id = 1;
		    
	//	} 

	
		$this->resetUI();
		$this->emit('category-updated', 'Etiqueta Eliminada');

	}


	public function Filtro($estado)
	{
	   $this->estado_filtro = $estado;
	   
	}
	
	    // Eliminar en lote 
    
		
	public function RestaurarCategoria(etiquetas $categoria)
	{
		$categoria->update([
			'eliminado' => 0
		]);

		$this->resetUI();
		$this->emit('category-updated', 'Categoria Restaurada');
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
    
    $etiquetas_checked = etiquetas::select('etiquetas.id','etiquetas.comercio_id')->whereIn('etiquetas.id',$ids)->get();

    $this->id_check = [];
    
    if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;
    
    
    foreach($etiquetas_checked as $pc) {
    
    $pc->eliminado = $estado;
    $pc->save();
    
    }
    
    
    $this->id_check = [];
    $this->accion_lote = 'Elegir';
    
     $this->emit('global-msg',"ETIQUETA ".$msg);
    
    }


}
