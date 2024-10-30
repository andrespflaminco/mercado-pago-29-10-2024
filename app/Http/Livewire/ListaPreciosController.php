<?php

namespace App\Http\Livewire;


use App\Models\Category;


use App\Models\lista_precios_muestra_sucursales; // 13-8-2024

use App\Models\configuracion_lista_precios; // 14-8-2024


use App\Models\lista_precios_reglas; // 29-8-2024 -- Actualizacion lista precios

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


class ListaPreciosController extends Component
{

	use WithFileUploads;
	use WithPagination;



	public $name,$agregar, $search, $image, $selected_id, $wc_key, $pageTitle, $componentName, $nombre, $descripcion;
	public $sucursales_elegidas = [];
	private $pagination = 25;
	public $tipo;
	public $visibilidad;
	
	public $selected_lista_defecto;

    // 13-8-2024
    public $sucursales,$lista_precios;
    
    // 14-8-2024
    public $forma_mostrar;
    
    public $sucursal_lista = [];
    
    public $casa_central_id;
    
    public $regla_precio,$porcentaje_regla_precio,$modificar_porcentajes_lista_todos; // 29-8-2024 -- Actualizacion lista precios
    
    public $es_lista_defecto = 0;
    
	public function mount()
	{
		$this->pageTitle = 'Listado';
		$this->componentName = 'Lista de precios';
		$this->tipo = 1;
		$this->visibilidad = 0;
		$this->es_lista_defecto = 0;
		$this->selected_lista_defecto = null;
		
		// 15-9-2024
		$this->regla_precio = 1;
		$this->porcentaje_regla_precio = 0;
		
    // 13-8-2024
        $this->comercio_id = Auth::user()->comercio_id != 1 ? Auth::user()->comercio_id : Auth::user()->id;

        $this->GetDatos();
        
        // 14-8-2024
        $this->forma_mostrar = $this->GetConfiguracionListaPrecios();
        
        $this->modificar_porcentajes_lista_todos = 0;
	}
	
	

function convertirFormatoMoneda($valor) {
    // Eliminar los puntos
    $valor = str_replace('.', '', $valor);
    // Reemplazar la coma con punto
    $valor = str_replace(',', '.', $valor);
    return $valor;
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
        
        $this->casa_central_id = Auth::user()->casa_central_user_id;
        	
        } else {
        	  
        $this->casa_central = sucursales::where('sucursal_id', $this->comercio_id)->first();
        $this->casa_central_id = $this->casa_central->casa_central_id;
        
        $this->casa_central_id = Auth::user()->casa_central_user_id;
        }

        
		if(strlen($this->search) > 0)
			$data = lista_precios::where('nombre', 'like', '%' . $this->search . '%')
			->where('comercio_id', 'like', $this->casa_central_id)
			->where('eliminado',0)
			->paginate($this->pagination);
		else
			$data = lista_precios::where('comercio_id', 'like', $this->casa_central_id)
			->where('eliminado',0)
			->orderBy('id','desc')
			->paginate($this->pagination);

		$this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')->where('casa_central_id', $this->casa_central_id)->get();

     //   $this->SetMuestra();
        
		return view('livewire.lista-precios.component', [
			'data' => $data ,
			'sucursales' => $this->sucursales ,
		])
		->extends('layouts.theme-pos.app')
		->section('content');
	}


    public function Agregar() {
        $this->agregar = 1;
        $this->es_lista_defecto = 0;
        $this->selected_lista_defecto = null;
        
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
	    //dd($id);
	    $this->es_lista_defecto = 0;
	    $this->selected_lista_defecto = null;
        $this->agregar = 1;	
		$record = lista_precios::find($id, ['tipo','id','nombre','descripcion','wc_key']);
		$this->nombre = $record->nombre;
		$this->descripcion = $record->descripcion;
		$this->tipo = $record->tipo;
		$this->wc_key = $record->wc_key;
		$this->selected_id = $record->id;
		$this->image = null;
		
		$lista_precios_muestra_sucursales = lista_precios_muestra_sucursales::where('lista_id',$record->id)->get();

		foreach($lista_precios_muestra_sucursales as $lp){
		    $this->sucursal_lista[$lp->sucursal_id] = $lp->muestra;
		}

		$lista_precios_regla = lista_precios_reglas::where('lista_id',$record->id)->first();
		$this->regla_precio = $lista_precios_regla->regla;
		$this->porcentaje_regla_precio = $lista_precios_regla->porcentaje_defecto;
		
		
		$this->emit('show-modal', 'show modal!');
	}

	public function EditListasDefecto($id)
	{
        $this->agregar = 1;	
        $this->es_lista_defecto = 1;


        // lista de precios a sucursales
        if($id == 1){
		$this->nombre = 'Precio de venta a sucursales';
		$this->descripcion = '';
		$this->tipo = 2;
		$this->wc_key = null;
		$this->selected_id = 1;
		$this->image = null;

		$lista_precios_regla = lista_precios_reglas::where('lista_id',$id)->where('comercio_id',$this->comercio_id)->first();
		$this->regla_precio = $lista_precios_regla ? $lista_precios_regla->regla : 1;
		$this->porcentaje_regla_precio = $lista_precios_regla ? $lista_precios_regla->porcentaje_defecto : 0;
            
        }
        
        
        // lista de precio base
        if($id == 0){
        $this->selected_lista_defecto = 0;
		$this->nombre = 'Precio base';
		$this->descripcion = '';
		$this->tipo = 1;
		$this->wc_key = null;
		$this->selected_id = 0;
		$this->image = null;

		$lista_precios_regla = lista_precios_reglas::where('lista_id',$id)->where('comercio_id',$this->comercio_id)->first();
		$this->regla_precio = $lista_precios_regla ? $lista_precios_regla->regla : 1;
		$this->porcentaje_regla_precio = $lista_precios_regla ? $lista_precios_regla->porcentaje_defecto : 0;
            
        }
        
		
		
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
			'tipo' => $this->tipo,
			'comercio_id' => $comercio_id
		]);

// Si el producto es simple
        
		$productos_simples = Product::where('comercio_id',$comercio_id)->where('producto_tipo','s')->where('eliminado',0)->get();
        $lista_precios_reglas = lista_precios_reglas::create([
            'lista_id' => $lista_precios->id,
            'comercio_id' => $lista_precios->comercio_id,
            'regla' => $this->regla_precio,
            'porcentaje_defecto' => $this->porcentaje_regla_precio
            ]);
            
                    

        foreach($productos_simples as $p) {

        $precio_lista = 0;
        $porcentaje_regla_precio = 0;
        if($this->regla_precio == 2){
        $porcentaje_regla_precio = $this->porcentaje_regla_precio/100;
        $precio_lista = $p->cost * (1 + $porcentaje_regla_precio);    
        }
        
        productos_lista_precios::create([
			'lista_id' => $lista_precios->id,
			'product_id' => $p->id,
			'precio_lista' => $precio_lista,
			'referencia_variacion' => 0,
			'comercio_id' => $comercio_id,
			'regla_precio' => $this->regla_precio,
			'porcentaje_regla_precio' => $porcentaje_regla_precio
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
        
        $precio_lista = 0;
        $porcentaje_regla_precio = 0;
        if($this->regla_precio == 2){
        $porcentaje_regla_precio = $this->porcentaje_regla_precio/100;
        $precio_lista = $pv->cost * (1 + $porcentaje_regla_precio);    
        }
        
        productos_lista_precios::create([
			'lista_id' => $lista_precios->id,
			'product_id' => $pv->product_id,
			'precio_lista' => $precio_lista,
			'referencia_variacion' => $pv->referencia_variacion,
			'comercio_id' => $comercio_id,
			'regla_precio' => $this->regla_precio,
			'porcentaje_regla_precio' => $porcentaje_regla_precio
		]);

        } 
            
        $this->saveSucursales($lista_precios->id);
        
		$this->resetUI();
		$this->emit('data-added','Lista de precios Registrada');
		$this->emit('msg','Lista de precios Registrada');

	}

	public function UpdateListaDefecto()
	{
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		
		
        $lista_precios_regla = lista_precios_reglas::where('lista_id',$this->selected_lista_defecto)->where('comercio_id',$comercio_id)->first();
		
        $lista_precios_regla = lista_precios_reglas::updateOrCreate(
            [
                'lista_id' => $this->selected_lista_defecto,
                'comercio_id' => $comercio_id
            ],
            [
                'regla' => $this->regla_precio,
                'porcentaje_defecto' => $this->porcentaje_regla_precio
            ]
        );

        $productos_lista_precios = productos_lista_precios::where('comercio_id',$comercio_id)->where('lista_id',$this->selected_lista_defecto)->get();
        
        foreach($productos_lista_precios as $plp){
        if($plp->lista_id == $this->selected_lista_defecto){
        $plp->regla_precio = $this->regla_precio;
        
        if($this->modificar_porcentajes_lista_todos == true && $this->regla_precio == 2){
            $margen = $this->porcentaje_regla_precio/100;    
            
            // obtenemos el costo
            $product = Product::find($plp->product_id);
                if($product->producto_tipo == "s"){
                    $costo = $product->cost;
                } else {
                    $pvd = productos_variaciones_datos::where('referencia_variacion',$plp->referencia_variacion)->where('product_id',$plp->product_id)->where('eliminado',0)->first();
                    $costo = $pvd->cost;
                }
            
            $precio_nuevo = $costo * (1 + $margen);
            $plp->precio_lista = $precio_nuevo;
            $plp->porcentaje_regla_precio = $margen;
            $plp->save();
        }
        
        $plp->save();
        }
        }
        
		$this->resetUI();
		$this->emit('data-updated', 'Lista de precios Actualizada');
		$this->emit('msg','Lista de precios Actualizada');

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
			'tipo' => $this->tipo,
			'descripcion' => $this->descripcion,
		]);
        
        $this->saveSucursales($lista_precios->id);
        
        $lista_precios_regla = lista_precios_reglas::where('lista_id',$lista_precios->id)->first();
		$lista_precios_regla->regla = $this->regla_precio;
		$lista_precios_regla->porcentaje_defecto = $this->porcentaje_regla_precio;
		$lista_precios_regla->save();


        $productos_lista_precios = productos_lista_precios::where('comercio_id',$comercio_id)->get();
        
        foreach($productos_lista_precios as $plp){
        if($plp->lista_id == $lista_precios->id){
        $plp->regla_precio = $this->regla_precio;
        
        if($this->modificar_porcentajes_lista_todos == true && $this->regla_precio == 2){
            $margen = $this->porcentaje_regla_precio/100;    
            
            // obtenemos el costo
            $product = Product::find($plp->product_id);
                if($product->producto_tipo == "s"){
                    $costo = $product->cost;
                } else {
                    $pvd = productos_variaciones_datos::where('referencia_variacion',$plp->referencia_variacion)->where('product_id',$plp->product_id)->where('eliminado',0)->first();
                    $costo = $pvd->cost;
                }
            
            $precio_nuevo = $costo * (1 + $margen);
            $plp->precio_lista = $precio_nuevo;
            $plp->porcentaje_regla_precio = $margen;
            $plp->save();
        }
        
        $plp->save();
        }
        }
        
		$this->resetUI();
		$this->emit('data-updated', 'Lista de precios Actualizada');
		$this->emit('msg','Lista de precios Actualizada');

	}

	public function resetUI()
	{
	    $this->es_lista_defecto = 0;
	    $this->selected_lista_defecto = null;
	    $this->tipo = 1;
		$this->nombre = '';
		$this->descripcion = '';
		$this->selected_id = 0;
		$this->wc_key = '';
		$this->agregar = 0;	 
		$this->regla_precio = 1;
        $this->porcentaje_regla_precio = 0;
	}

	protected $listeners =[
		'deleteRow' => 'Destroy'
	];


	public function Destroy(lista_precios $lista_precios)
	{
	    
	    $lista_precios->delete();
	    /*
		$lista_precios->update([
		    'eliminado' => 1
		    ]);
        */
		$this->resetUI();
		$this->emit('data-deleted', 'Lista de Precios Eliminada');
		$this->emit('msg','Lista de precios Actualizada');

	}

    public function toggleMuestra($sucursalId, $listaId){
        
        // Buscar o crear el registro correspondiente
        $registro = lista_precios_muestra_sucursales::where('sucursal_id',$sucursalId)->where('lista_id', $listaId)->first();
        
        $muestra =  $registro ? $registro->muestra : 0;
        
        if($muestra == 1){$muestra_nueva = 0;}
        if($muestra == 0){$muestra_nueva = 1;}
        
        //dd($muestra_nueva);
        
        lista_precios_muestra_sucursales::UpdateOrCreate(
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
        $configuracion = configuracion_lista_precios::where('casa_central_id',Auth::user()->casa_central_user_id)->first();
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
    
    public function SetListasBases(){
        $listas_base = User::where('comercio_id',1)->get();
        
        foreach($listas_base as $lb){
        
        lista_precios_reglas::create([
            'lista_id' => 0,
            'comercio_id' => $lb->id,
            'regla' => 1,
            'porcentaje_defecto' => 0
            ]);
            
        lista_precios_reglas::create([
            'lista_id' => 1,
            'comercio_id' => $lb->id,
            'regla' => 1,
            'porcentaje_defecto' => 0
        ]);
            
        }
        
        
    }

}
