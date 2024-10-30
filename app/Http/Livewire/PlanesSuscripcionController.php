<?php

namespace App\Http\Livewire;

// Trait

use App\Traits\WocommerceTrait;

use App\Models\planes_suscripcion;
use App\Models\planes_suscripcion_landings;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use App\Models\wocommerce;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;


class PlanesSuscripcionController extends Component
{

	use WithFileUploads;
	use WithPagination;

	public $name, $search, $image, $agregar,$seccion,$id_check,$selected_id, $pageTitle, $componentName, $wc_category_id;
	private $pagination = 25;
	public $nombre_origen,$url_origen;
	private $wc_category;
	
	public $preapproval_plan_id, $nombre, $monto, $origen, $plan_id;

	public function mount()
	{
	    $this->seccion = 1;
	    $this->estado_filtro = 0;
	    $this->accion_lote = 'Elegir';
		$this->pageTitle = 'Listado';
		$this->componentName = 'Categorías';
	}

	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}

    protected function redirectLogin()
    {
        return \Redirect::to("login");
    }
    
    public function ElegirSeccion($value){
    $this->seccion = $value;    
    }
    
	public function render()
	{
	    

        if (!Auth::check()) {
            // Redirigir al inicio de sesión y retornar una vista vacía
            $this->redirectLogin();
            return view('auth.login');
        }
        
		if(strlen($this->search) > 0)
			$data = planes_suscripcion::where('nombre', 'like', '%' . $this->search . '%')
			->where('eliminado', $this->estado_filtro)
			->paginate($this->pagination);
		else
			$data = planes_suscripcion::where('eliminado',$this->estado_filtro)
			->orderBy('id','desc')
			->paginate($this->pagination);


        $datos_landing = planes_suscripcion_landings::where('eliminado',$this->estado_filtro)->get();
        
		return view('livewire.planes_suscripcion.component', [
		    'datos' => $data,
		    'datos_landing' => $datos_landing
		    ])
		->extends('layouts.theme-pos.app')
		->section('content');
	}



	public function Edit($id)
	{
	    $this->agregar = 1;
		
	    if($this->seccion == 1){
	    $record = planes_suscripcion::find($id);
		$this->nombre = $record->nombre;
		$this->origen = $record->origen;
		$this->preapproval_plan_id = $record->preapproval_plan_id;
		$this->plan_id = $record->plan_id;
		$this->monto = $record->monto;
		$this->selected_id = $record->id;
	    } 
	    if($this->seccion == 2){
	    $record = planes_suscripcion_landings::find($id);
	    $this->selected_id = $record->id;    
	    $this->url_origen = $record->url_registro;    
	    $this->nombre_origen = $record->nombre;
	    }
	    if($this->seccion == 3){
	    $record = planes_suscripcion_landings::find($id);
	    $this->selected_id = $record->id;    
	    $this->url_origen = $record->url;    
	    $this->nombre_origen = $record->nombre;
	    }
	    

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
		
		if($this->seccion == 1){
		$rules = [
			'preapproval_plan_id' => ['required',Rule::unique('planes_suscripcions')->where('eliminado',0)],
		];

		$messages = [
			'preapproval_plan_id.required' => 'preapproval_plan_id es requerido',
			'preapproval_plan_id.min' => 'El preapproval_plan_id debe tener al menos 3 caracteres',
			'preapproval_plan_id.unique' => 'El preapproval_plan_id ya existe. Elija otro.'
		];

		$this->validate($rules, $messages);

		$category = planes_suscripcion::create([
		'nombre' => $this->nombre,
		'origen' => $this->origen,
		'plan_id' => $this->plan_id,
		'eliminado' => 0,
		'preapproval_plan_id' => $this->preapproval_plan_id,
		'monto' => $this->monto
		]);
		    
		}

		if($this->seccion == 2){
		$rules = [
			'nombre_origen' => ['required'],
			'url_origen' => ['required'],
		];

		$messages = [
			'nombre_origen.required' => 'nombre es requerido',
			'url_origen.required' => 'url es requerido',
		];

		$this->validate($rules, $messages);

		$category = planes_suscripcion_landings::create([
		'nombre' => $this->nombre_origen,
		'url_registro' => $this->url_origen,
		'eliminado' => 0,
		]);
		    
		}
		
		if($this->seccion == 3){
		$rules = [
			'nombre_origen' => ['required'],
			'url_origen' => ['required'],
		];

		$messages = [
			'nombre_origen.required' => 'nombre es requerido',
			'url_origen.required' => 'url es requerido',
		];

		$this->validate($rules, $messages);

		$category = planes_suscripcion_landings::create([
		'nombre' => $this->nombre_origen,
		'url' => $this->url_origen,
		'eliminado' => 0,
		]);
		    
		}
        
		$this->resetUI();
		$this->emit('category-added','Categoría Registrada');

	}


	public function Update()
	{
	    
	    if($this->seccion == 1){
	        
	    $rules =[
			'preapproval_plan_id' => ['required',Rule::unique('planes_suscripcions')->ignore($this->selected_id)->where('eliminado',0)],
		];

		$messages =[
			'preapproval_plan_id.required' => 'preapproval_plan_id requerido',
			'preapproval_plan_id.min' => 'El preapproval_plan_id debe tener al menos 3 caracteres'
		];

		$this->validate($rules, $messages);

        	////////////////////////////////////////////////
        
		$planes_suscripcions = planes_suscripcion::find($this->selected_id);
		
		$planes_suscripcions->update([
		'nombre' => $this->nombre,
		'origen' => $this->origen,
		'plan_id' => $this->plan_id,
		'preapproval_plan_id' => $this->preapproval_plan_id,
		'monto' => $this->monto
		]);
        
	    }
	    
	    if($this->seccion == 2){
	        
		$rules = [
			'nombre_origen' => ['required'],
			'url_origen' => ['required'],
		];

		$messages = [
			'nombre_origen.required' => 'nombre es requerido',
			'url_origen.required' => 'url es requerido',
		];

		$this->validate($rules, $messages);

        	////////////////////////////////////////////////
        
		$planes_suscripcions = planes_suscripcion_landings::find($this->selected_id);
		
		$planes_suscripcions->update([
		'nombre' => $this->nombre_origen,
		'url_registro' => $this->url_origen
		]);
        

	    
	    }
	    
	    if($this->seccion == 3){
	        
		$rules = [
			'nombre_origen' => ['required'],
			'url_origen' => ['required'],
		];

		$messages = [
			'nombre_origen.required' => 'nombre es requerido',
			'url_origen.required' => 'url es requerido',
		];

		$this->validate($rules, $messages);

        	////////////////////////////////////////////////
        
		$planes_suscripcions = planes_suscripcion_landings::find($this->selected_id);
		
		$planes_suscripcions->update([
		'nombre' => $this->nombre_origen,
		'url' => $this->url_origen
		]);
        
	    $users = User::where('origen',$this->selected_id)->get();
	    
	    foreach($users as $user){
	        $user->url_origen = $this->url_origen;
	        $user->save();
	    }
	    
	    }
	    
	 	$this->resetUI();
		$this->emit('category-updated', 'Categoría Actualizada');



	}


	public function resetUI()
	{
		$this->nombre = '';
		$this->origen = '';
		$this->preapproval_plan_id = '';
		$this->monto = 0;
		$this->selected_id =0;
		$this->agregar = 0;
	}

	protected $listeners =[
		'deleteRow' => 'Destroy',
		'RestaurarCategoria' => 'RestaurarCategoria',
        'accion-lote' => 'AccionEnLote'
	];


	public function Destroy(planes_suscripcion $planes_suscripcion)
	{

		$planes_suscripcion->eliminado = 1;
		$planes_suscripcion->save();

		$this->resetUI();
		$this->emit('category-updated', 'Registro Eliminada');

	}


	public function Filtro($estado)
	{
	   $this->estado_filtro = $estado;
	   
	}
	
	    // Eliminar en lote 
    
		
	public function RestaurarCategoria(planes_suscripcion $planes_suscripcion)
	{
		$planes_suscripcion->update([
			'eliminado' => 0
		]);

		$this->resetUI();
		$this->emit('category-updated', 'Registro Restaurado');
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
    
    $checked = planes_suscripcion::select('planes_suscripcions.id')->whereIn('planes_suscripcions.id',$ids)->get();

    $this->id_check = [];
    
    foreach($checked as $pc) {
    
    $pc->eliminado = $estado;
    $pc->save();
    }
    
    
    $this->id_check = [];
    $this->accion_lote = 'Elegir';
    
     $this->emit('global-msg',"REGISTROS ".$msg);
    
    }
    
    public function Sincronizar($categoria_id) {
        
        $this->FindOrCreateCategoryByName($categoria_id);
    }



}
