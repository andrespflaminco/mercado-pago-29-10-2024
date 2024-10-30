<?php

namespace App\Http\Livewire;


use App\Models\Category;

use App\Models\Product;
use App\Models\User;
use App\Models\productos_lista_precios;
use App\Models\productos_variaciones_datos;
use App\Models\lista_precios;
use App\Models\sucursales;
use App\Models\lista_precios_sucursales;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;


class ReglaPreciosController extends Component
{

	use WithFileUploads;
	use WithPagination;



	public $name,$agregar, $search, $image, $selected_id, $wc_key, $pageTitle, $componentName, $nombre, $descripcion;
	public $sucursales_elegidas = [];
	private $pagination = 25;


	public function mount()
	{
		$this->pageTitle = 'Listado';
		$this->componentName = 'Lista de precios';
		

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
        if (!Auth::check()) {
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
			$data = lista_precios::where('nombre', 'like', '%' . $this->search . '%')
			->where('comercio_id', 'like', $this->casa_central_id)
			->paginate($this->pagination);
		else
			$data = lista_precios::where('comercio_id', 'like', $this->casa_central_id)
			->orderBy('id','desc')
			->paginate($this->pagination);

		$this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')->where('casa_central_id', $this->casa_central_id)->get();


		return view('livewire.regla-precios.component', [
			'data' => $data ,
			'sucursales' => $this->sucursales ,
		])
		->extends('layouts.theme-pos.app')
		->section('content');
	}


    public function Agregar() {
        $this->agregar = 1;
    }
    
	public function Edit($id)
	{
        $this->agregar = 1;	
		$record = lista_precios::find($id, ['id','nombre','descripcion','wc_key']);
		$this->nombre = $record->nombre;
		$this->descripcion = $record->descripcion;
		$this->wc_key = $record->wc_key;
		$this->selected_id = $record->id;
		$this->image = null;

		$this->emit('show-modal', 'show modal!');
	}



	public function Store()
	{
	    	    
	    if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		
		$rules = [
		    'nombre' => ['required',Rule::unique('lista_precios')->where('comercio_id',$comercio_id)->where('eliminado',0)],
		];

		$messages = [
			'nombre.required' => 'Nombre de la categoría es requerido',
			'nombre.unique' => 'Nombre de la lista de precios debe ser unico'
		];

		$this->validate($rules, $messages);

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$lista_precios = lista_precios::create([
			'nombre' => $this->nombre,
			'wc_key' => $this->wc_key,
			'descripcion' => $this->descripcion,
			'comercio_id' => $comercio_id
		]);

// Si el producto es simple
        
		$productos_simples = Product::where('comercio_id',$comercio_id)->where('producto_tipo','s')->where('eliminado',0)->get();
        
        foreach($productos_simples as $p) {

        productos_lista_precios::create([
			'lista_id' => $lista_precios->id,
			'product_id' => $p->id,
			'precio_lista' => 0,
			'referencia_variacion' => 0,
			'comercio_id' => $comercio_id
		]);

        } 
        
// Si el producto es variable
        
        $productos_variaciables = productos_variaciones_datos::leftjoin('products','products.id','productos_variaciones_datos.product_id')
        ->select('productos_variaciones_datos.*')
        ->where('products.comercio_id',$comercio_id)
        ->where('products.eliminado',0)
        ->where('products.producto_tipo','v')
        ->get();
        
        foreach($productos_variaciables as $pv) {

        productos_lista_precios::create([
			'lista_id' => $lista_precios->id,
			'product_id' => $pv->product_id,
			'precio_lista' => 0,
			'referencia_variacion' => $pv->referencia_variacion,
			'comercio_id' => $comercio_id
		]);

        } 
            

		$this->resetUI();
		$this->emit('data-added','Lista de precios Registrada');
		$this->emit('msg','Lista de precios Registrada');

	}


	public function Update()
	{
	    
	    if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		
		$rules =[
		'nombre' => ['required',Rule::unique('lista_precios')->ignore($this->selected_id)->where('comercio_id',$comercio_id)->where('eliminado',0)],
		];

		$messages =[
			'nombre.required' => 'Nombre de categoría requerido',
			'nombre.min' => 'El nombre de la categoría debe tener al menos 3 caracteres',
			'nombre.unique' => 'ya hay una lista de precios con ese nombre'
		];

		$this->validate($rules, $messages);


		$lista_precios = lista_precios::find($this->selected_id);

		$lista_precios->update([
			'nombre' => $this->nombre,
			'wc_key' => $this->wc_key,
			'descripcion' => $this->descripcion,
		]);

		$this->resetUI();
		$this->emit('data-updated', 'Lista de precios Actualizada');
		$this->emit('msg','Lista de precios Actualizada');



	}


	public function resetUI()
	{
	   
		$this->nombre = '';
		$this->descripcion = '';
		$this->selected_id = 0;
		$this->wc_key = '';
		$this->agregar = 0;	 
	}

	protected $listeners =[
		'deleteRow' => 'Destroy'
	];


	public function Destroy(lista_precios $lista_precios)
	{

		$lista_precios->delete();


		$this->resetUI();
		$this->emit('data-deleted', 'Lista de Precios Eliminada');
		$this->emit('msg','Lista de precios Actualizada');

	}



}
