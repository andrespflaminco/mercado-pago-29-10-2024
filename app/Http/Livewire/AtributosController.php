<?php

namespace App\Http\Livewire;


use App\Models\Category;
use App\Models\sucursales;
use App\Models\User;
use App\Models\productos_variaciones_datos;
use App\Models\atributos;
use App\Models\productos_variaciones;
use App\Models\variaciones;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use App\Models\wocommerce;
use Livewire\WithFileUploads;
use Livewire\WithPagination;


class AtributosController extends Component
{

	use WithFileUploads;
	use WithPagination;



	public $name, $search, $image, $selected_id, $pageTitle, $componentName, $nombre_atributo, $wc_category_id, $id_atributo, $search_atributo, $name_variacion,$name_variacion_editar,$atributo_editar, $id_variacion;
	public $lista_atributos = [];
	private $pagination = 5;
	private $wc_category;
	
	public $atributos;

	public function mount()
	{
		$this->pageTitle = 'Listado';
		$this->componentName = 'Atributos';


	}

	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


    protected function redirectLogin()
    {
        return \Redirect::to("login");
    }
	public function render()
	{
   
    if(!Auth::check()) {
            // Redirigir al inicio de sesión y retornar una vista vacía
            $this->redirectLogin();
            return view('auth.login');
        }

     
     
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
			$data = atributos::where('nombre', 'like', '%' . $this->search . '%')
			->where('comercio_id', $this->casa_central_id)
			->orWhere('comercio_id',0)
			->where('eliminado',0)
			->get();
		else
			$data = atributos::where('comercio_id', $this->casa_central_id)
			->orWhere('comercio_id',0)
			->orderBy('id','desc')
			->get();

			$data_variaciones = variaciones::where('comercio_id', $this->casa_central_id)
			->orWhere('id',1)
			->where('eliminado',0)
			->orderBy('nombre','asc')
			->get();
			
			$count_variaciones = productos_variaciones::select('variacion_id', \DB::raw('count(*) as cantidad'))
			->where('comercio_id',$this->casa_central_id)
			->orWhere('id',1)
			->groupBy('variacion_id')
			->get();
			
		//	dd($count_variaciones);
		
		    $this->atributos = $data;

		return view('livewire.atributos.component', [
			'categories' => $data,
			'variaciones' => $data_variaciones
		])
		->extends('layouts.theme-pos.app')
		->section('content');
	}


	public function Agregar()
	{
		$this->name = "";
		$this->selected_id = 0;
		$this->emit('show-modal', 'show modal!');
	}

	public function Edit($id)
	{
		$record = atributos::find($id, ['id','nombre']);
		$this->name = $record->nombre;
		$this->selected_id = $record->id;
		$this->emit('show-modal', 'show modal!');
	}




	public function Store()
	{
		$rules = [
			'name' => 'required'
		];

		$messages = [
			'name.required' => 'Nombre de la categoría es requerido'
		];

		$this->validate($rules, $messages);

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$atributos = atributos::create([
			'nombre' => $this->name,
			'comercio_id' => $comercio_id
		]);


		$this->resetUI();
		$this->emit('category-added','Atributo Agregado');

	}


	public function Update()
	{
		$rules =[
			'name' => "required|min:3"
		];

		$messages =[
			'name.required' => 'Nombre del atributo es requerido',
			'name.min' => 'El nombre del atributo debe tener al menos 3 caracteres'
		];

		$this->validate($rules, $messages);


		$atributos = atributos::find($this->selected_id);

		$atributos->update([
			'nombre' => $this->name
		]);


		$this->resetUI();
		$this->emit('category-updated', 'Atributo Actualizado');



	}


	public function resetUI()
	{
		$this->name ='';
		$this->image = null;
		$this->search ='';
		$this->selected_id =0;
		$this->id_atributo = 0;
		$this->variacion_id = 0;
		$this->lista_atributos = [];
	    $this->name_variacion = "";
	}

	protected $listeners =[
		'deleteRow' => 'Destroy',
		'deleteVariacion' => 'DestroyVariacion',
		'deleteAtributo' => 'DestroyAtributo',
	];


	public function Destroy(atributos $category)
	{

		$category->eliminado = 1;
        $category->save();
        
		$this->resetUI();
		$this->emit('category-deleted', 'Atributo Eliminada');

	}
	
		public function DestroyVariacion(variaciones $variacion)
	{

		$variacion->eliminado = 1;
		$variacion->save();

		$this->resetUI();
		$this->emit('msg','Variacion eliminada');
		$this->EditVariacion($variacion->atributo_id);

	}

	public function AgregarVariacion($id_atributo) {

		$this->id_atributo = $id_atributo;

		$this->emit('show-modal-variacion', '');
	}
	
		public function AgregarVariacion2($id_atributo) {

		$this->id_atributo = $id_atributo;

		$this->emit('show-modal-variacion', '');
		$this->emit('variacion-editar-hide', '');
	}


public function StoreVariacion() {

	$rules = [
		'name_variacion' => 'required'
	];

	$messages = [
		'name_variacion.required' => 'Nombre de la variacion es requerido'
	];

	$this->validate($rules, $messages);

	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

	$variacion = variaciones::create([
		'nombre' => $this->name_variacion,
		'atributo_id' => $this->id_atributo,
		'comercio_id' => $comercio_id
	]);


	$this->resetUI();
	$this->emit('variacion-added','Variacion agregada');

}

public function EditVariacion($id)
{
    
    		$data_variaciones = variaciones::leftjoin('productos_variaciones','productos_variaciones.variacion_id','variaciones.id')
			->select('variaciones.*','productos_variaciones.variacion_id', \DB::raw('count(productos_variaciones.id) as cantidad'))
			->where('variaciones.comercio_id', $this->casa_central_id)
			->where('variaciones.eliminado',0)
			->groupBy('variaciones.id','variaciones.nombre','variaciones.created_at','variaciones.updated_at','variaciones.atributo_id','variaciones.eliminado','variaciones.comercio_id','productos_variaciones.variacion_id')
			->orderBy('variaciones.nombre','asc')
			->get();
			
            $atributo = atributos::find($id, ['id','nombre']);
	        
	        $lista_atributos = variaciones::leftjoin('productos_variaciones','productos_variaciones.variacion_id','variaciones.id')
			->select('variaciones.*','productos_variaciones.variacion_id', \DB::raw('count(productos_variaciones.id) as cantidad'))
			->where('variaciones.comercio_id', $this->casa_central_id)
			->where('variaciones.eliminado',0)
			->where('variaciones.atributo_id',$id)
			->groupBy('variaciones.id','variaciones.nombre','variaciones.created_at','variaciones.updated_at','variaciones.atributo_id','variaciones.eliminado','variaciones.comercio_id','productos_variaciones.variacion_id')
			->orderBy('variaciones.nombre','asc')
			->get();
    
	foreach($lista_atributos as $llave => $LA) {
	$this->name_variacion_editar[$LA['id']] = $LA['nombre'];
	$this->atributo_editar[$LA['id']] = $LA['atributo_id'];
	}
	
    $this->lista_atributos = $lista_atributos;
    $this->nombre_atributo = $atributo->nombre;
    $this->id_atributo = $id;
    
	$this->emit('show-modal-editar-variacion', 'show modal!');
}

public function BuscarProductoAtributo($id) {
    
    $atributo = atributos::find($id, ['id','nombre']);
	$lista_atributos = variaciones::where('atributo_id',$id)->where('eliminado',0)->where('nombre', 'like', '%' . $this->search_atributo . '%')->get();

    $this->lista_atributos = $lista_atributos;
    $this->nombre_atributo = $atributo->nombre;
    $this->id_atributo = $id;
    
	$this->emit('show-modal-editar-variacion', 'show modal!');
}

public function UpdateVariacion()
{

    foreach ($this->name_variacion_editar as $key => $variaciones) {
    
    $var = variaciones::find($key);
    
    $nombre_anterior = $var->nombre;
    $id_variacion = $var->id;
    
    // Aca hay que poner tambien que busque que sea la misma id de variacion
    $resultados = productos_variaciones_datos::where('variaciones', 'LIKE', '%'.$nombre_anterior.'%')->where('variaciones_id', 'LIKE', '%'.$id_variacion.'%')->where('comercio_id',$this->casa_central_id )->get();

    foreach($resultados as $r) {
    $reemplazo = str_replace($nombre_anterior, $variaciones, $r->variaciones);    
    
    $r->variaciones = $reemplazo;
    $r->save();
    }
    
    
	$var->update([
		'nombre' => $variaciones
	]);

    }
    
    foreach ($this->atributo_editar as $key => $variaciones) {
    
    $var = variaciones::find($key);

	$var->update([
		'atributo_id' => $variaciones
	]);

    }
    
    $this->name_variacion_editar = [];
    $this->atributo_editar = [];
    
	$this->resetUI();
	$this->emit('variacion-editar-updated', 'Variaciones Actualizada');



}

public function DestroyAtributo(atributos $atributos)
{

	$atributos->eliminado = 1;
	$atributos->save();

	$this->resetUI();
	$this->emit('category-deleted', 'Atributo Eliminado');
	$this->EditVariacion($this->id_atributo);

}

public function CambiarAtributo($valor,$variacion_id){
    
    $variacion = variaciones::find($variacion_id);
    $variacion->atributo_id = $valor;
    $variacion->save();
    
    $this->emit('category-deleted', 'Variacion cambiada de atributo');
	
}


}
