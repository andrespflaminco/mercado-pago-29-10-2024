<?php

namespace App\Http\Livewire;


use App\Models\Category;


use App\Models\lista_precios_muestra_sucursales; // 13-8-2024

use App\Models\configuracion_lista_precios; // 14-8-2024


use App\Models\Product;
use App\Models\User;
use App\Models\productos_lista_precios;
use App\Models\productos_variaciones_datos;
use App\Models\lista_precios;
use App\Models\sucursales;
use App\Models\lista_precios_sucursales;
use App\Models\lista_precios_insumos;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;

use App\Models\insumo;
use App\Models\insumos_costos;

class ListaPreciosInsumosController extends Component
{

	use WithFileUploads;
	use WithPagination;



	public $name,$agregar, $search, $image, $selected_id, $wc_key, $pageTitle, $componentName, $nombre, $descripcion;
	public $sucursales_elegidas = [];
	private $pagination = 25;
	public $tipo;
	public $visibilidad;

    // 13-8-2024
    public $sucursales,$lista_precios;
    
    // 14-8-2024
    public $forma_mostrar;
    
    public $sucursal_lista = [];
    
    public $casa_central_id;
    
	public function mount()
	{
		$this->pageTitle = 'Listado';
		$this->componentName = 'Lista de precios';
		$this->tipo = 1;
		$this->visibilidad = 0;
		
    // 13-8-2024
        $this->comercio_id = Auth::user()->comercio_id != 1 ? Auth::user()->comercio_id : Auth::user()->id;
        
        
        $this->GetDatos();
        
        // 14-8-2024
        $this->forma_mostrar = $this->GetConfiguracionListaPrecios();
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
			$data = lista_precios_insumos::where('nombre', 'like', '%' . $this->search . '%')
			->where('comercio_id', 'like', $this->casa_central_id)
			->orWhere('id',1)
			->orderBy('id','asc')
			->paginate($this->pagination);
		else
			$data = lista_precios_insumos::where('comercio_id', 'like', $this->casa_central_id)
			->orWhere('id',1)
			->orderBy('id','asc')
			->paginate($this->pagination);

		$this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')->where('casa_central_id', $this->casa_central_id)->get();

     //   $this->SetMuestra();
        
		return view('livewire.lista-precios-insumos.component', [
			'data' => $data ,
			'sucursales' => $this->sucursales ,
		])
		->extends('layouts.theme-pos.app')
		->section('content');
	}


    public function Agregar() {
        $this->agregar = 1;
        
        // Marcar la casa central como seleccionada
        $this->sucursal_lista[auth()->user()->casa_central_user_id] = true;
        
         $this->sucursales = $this->GetSucursales($this->comercio_id);
         
        // Marcar todas las sucursales como seleccionadas
        foreach ($this->sucursales as $sucursal) {
            $this->sucursal_lista[$sucursal->sucursal_id] = true;
        }
        
    }
    
	public function Edit($id)
	{
        $this->agregar = 1;	
		$record = lista_precios_insumos::find($id, ['id','nombre']);
		$this->nombre = $record->nombre;
		$this->descripcion = $record->descripcion;
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
		    'nombre' => ['required',Rule::unique('lista_precios_insumos')->where('comercio_id',$comercio_id)->where('eliminado',0)],
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

		$lista_precios = lista_precios_insumos::create([
			'nombre' => $this->nombre,
			'descripcion' => $this->descripcion,
			'comercio_id' => $comercio_id
		]);

        // Si el producto es simple
        
		$insumos = insumo::where('comercio_id',$comercio_id)->where('eliminado',0)->get();
        
        foreach($insumos as $p) {

        insumos_costos::create([
			'lista_id' => $lista_precios->id,
			'insumo_id' => $p->id,
			'costo' => 0,
			'comercio_id' => $comercio_id
		]);

        } 
    
        //$this->saveSucursales($lista_precios->id);
        
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
		'nombre' => ['required',Rule::unique('lista_precios_insumos')->ignore($this->selected_id)->where('comercio_id',$comercio_id)->where('eliminado',0)],
		];

		$messages =[
			'nombre.required' => 'Nombre de lista de precios requerido',
			'nombre.min' => 'El nombre de la lista de precios debe tener al menos 3 caracteres',
			'nombre.unique' => 'ya hay una lista de precios con ese nombre'
		];

		$this->validate($rules, $messages);


		$lista_precios = lista_precios_insumos::find($this->selected_id);

		$lista_precios->update([
			'nombre' => $this->nombre,
			'tipo' => $this->tipo,
			'descripcion' => $this->descripcion,
		]);
        
    //    $this->saveSucursales($lista_precios->id);
        
		$this->resetUI();
		$this->emit('data-updated', 'Lista de precios Actualizada');
		$this->emit('msg','Lista de precios Actualizada');

	}

	public function resetUI()
	{
	    $this->tipo = 1;
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

    public function toggleMuestra($sucursalId, $listaId){
        
        // Buscar o crear el registro correspondiente
        $registro = lista_precios_muestra_sucursales::where('sucursal_id',$sucursalId)->where('lista_id', $listaId)->first();
        
        $muestra = $registro->muestra;
        
        if($muestra == 1){$muestra_nueva = 0;}
        if($muestra == 0){$muestra_nueva = 1;}
        
        //dd($muestra_nueva);
        
        // Actualizar el valor de 'muestra' basado en el estado del checkbox
        $registro->UpdateOrCreate(
            [
            'lista_id' => $listaId,
            'sucursal_id' => $sucursalId,
            ],
            [
            'lista_id' => $listaId,
            'sucursal_id' => $sucursalId,
            'muestra' => $muestra_nueva
            ]);
            
       // $this->GetDatos();
        
    }

        
    public function SetMuestra()
    {
        $usuarios = User::whereColumn('id', 'casa_central_user_id')->get();
        
        foreach ($usuarios as $usuario) {
            // Obtener las sucursales asociadas a este usuario (casa central)
            $sucursales = $this->GetSucursales($usuario->id);
            // Obtener las listas de precios asociadas a este usuario (casa central)
            $lista_precios = $this->GetListaPrecios($usuario->id);
            
            // Iterar sobre las sucursales y las listas de precios
            foreach ($sucursales as $sucursal) {
                foreach ($lista_precios as $lista_precio) {
                    // Verificar si el registro ya existe
                    $registro = lista_precios_muestra_sucursales::firstOrNew([
                        'sucursal_id' => $sucursal->sucursal_id,
                        'lista_id' => $lista_precio->id,
                    ]);
    
                    // Establecer el valor de muestra en 1
                    $registro->muestra = 1;
    
                    // Guardar el registro en la base de datos
                    $registro->save();
                }
            }
    
            // Ahora agregar lista_id para el propio usuario->id (como si fuera una sucursal)
            foreach ($lista_precios as $lista_precio) {
                // Verificar si el registro ya existe
                $registro = lista_precios_muestra_sucursales::firstOrNew([
                    'sucursal_id' => $usuario->id, // Aquí usamos el ID del usuario como sucursal_id
                    'lista_id' => $lista_precio->id,
                ]);
    
                // Establecer el valor de muestra en 1
                $registro->muestra = 1;
    
                // Guardar el registro en la base de datos
                $registro->save();
            }
        }
    }


    public function GetSucursales($casa_central_id){
        $sucursales = sucursales::join('users', 'users.id', 'sucursales.sucursal_id')
            ->select('users.name', 'sucursales.sucursal_id')
            ->where('sucursales.casa_central_id', $casa_central_id)
            ->where('eliminado', 0)
            ->get();
            
        return $sucursales;    
    }
    
    public function GetListaPrecios($comercio_id){
        return lista_precios::where('comercio_id', $comercio_id)->get();
    }
    
    public function GetDatos(){

        $this->sucursales = $this->GetSucursales($this->comercio_id);

        $this->lista_precios = $this->GetListaPrecios($this->comercio_id);	    
	}
	
	public function VerVisibilidad(){
	    $this->visibilidad = 1;
	}
	
	public function CerrarVerVisibilidad(){
	    $this->visibilidad = 0;
	}
	
	public function saveSucursales($lista_id)
    {
        foreach ($this->sucursal_lista as $sucursalId => $checked) {
            lista_precios_muestra_sucursales::updateOrCreate(
                [
                    'sucursal_id' => $sucursalId,
                    'lista_id' => $lista_id, // Asumiendo que tienes un ID de lista de precios
                ],
                [
                    'muestra' => $checked,
                ]
            );
        }
    
    }
    
    // 14-8-2024
    public function GetConfiguracionListaPrecios(){
        $configuracion = configuracion_lista_precios::where('casa_central_id',$this->casa_central_id)->first();
        if($configuracion == null){ return 1;} else { return $configuracion->forma_mostrar;}
    }
    
    public function UpdateConfiguracionListaPrecios(){
        
        
        $configuracion = configuracion_lista_precios::UpdateOrCreate([
            'casa_central_id' => $this->casa_central_id
            ],
            [
            'casa_central_id' => $this->casa_central_id,
            'forma_mostrar' => $this->forma_mostrar
            ]);
    }

}
