<?php

namespace App\Http\Livewire;

use App\Models\metodo_pago;
use App\Models\bancos;
use App\Models\User;
use App\Models\sucursales;
use App\Models\metodo_pagos_muestra_sucursales;
use App\Models\bancos_muestra_sucursales;
use App\Models\metodo_pago_deducciones;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

use Illuminate\Validation\Rule;

class MetodoPagoController extends Component
{
  use WithPagination;
	use WithFileUploads;


	public $nombre,$recargo,$descripcion,$agregar,$price,$stock,$alerts,$muestra_sucursales,$metodos_muestra_sucursales,$categoryid,$search,$image,$selected_id,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $metodos, $metodo, $categoria, $cuenta, $categoria_search, $id_check;
	private $pagination = 25;
	
	public $acreditacion_inmediata;
	
	public $deducciones = [];

    public function addDeduccion()
    {
        $this->deducciones[] = ['nombre' => '', 'porcentaje' => ''];
    }

    public function removeDeduccion($index)
    {
        // Verifica si el ¨ªndice existe y si tiene un 'id' v¨¢lido
        if (isset($this->deducciones[$index]) && isset($this->deducciones[$index]['id'])) {
            $mp = metodo_pago_deducciones::find($this->deducciones[$index]['id']);
            
            // Verifica si se encontr¨® el registro en la base de datos
            if ($mp !== null) {
                $mp->eliminado = 1;
                $mp->save();
            }
    
            // Elimina el elemento del arreglo y reindexa
            unset($this->deducciones[$index]);
            $this->deducciones = array_values($this->deducciones); // Reindexar el array
        }
    }

    
	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function mount()
	{
		$this->pageTitle = 'Listado';
		$this->componentName = 'Metodos de pago';
		$this->categoria = 'Elegir';
		$this->almacen = 'Elegir';
		$this->cuenta = 'Elegir';
		$this->stock_descubierto = 'Elegir';
        $this->muestra_sucursales = true;
        $this->acreditacion_inmediata = 1;
	}

    
    public function Agregar(){
        $this->agregar = 1;
    }


    protected function redirectLogin()
    {
        return \Redirect::to("login");
    }
    
    /*
	public function render()
	{
        if (!Auth::check()) {
            // Redirigir al inicio de sesi¨®n y retornar una vista vac¨ªa
            $this->redirectLogin();
            return view('auth.login');
        }
     
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

        $this->comercio_id = $comercio_id;

        $this->tipo_usuario = User::find(Auth::user()->id);
        
        if($this->tipo_usuario->sucursal != 1) {
            $this->casa_central_id = $comercio_id;
        } else {
        
            $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
            $this->casa_central_id = $this->casa_central->casa_central_id;
        }

        //dd($this->tipo_usuario->sucursal);
        
		$metodos = metodo_pago::join('users','users.id','metodo_pagos.creador_id')
		->join('metodo_pagos_muestra_sucursales','metodo_pagos_muestra_sucursales.metodo_id','metodo_pagos.id')
        ->join('bancos','bancos.id','metodo_pagos.cuenta')
        ->select('users.name as creador','metodo_pagos.creador_id','metodo_pagos.id','metodo_pagos.comercio_id','metodo_pagos.categoria','metodo_pagos.cuenta','metodo_pagos.recargo','metodo_pagos.descripcion','metodo_pagos.muestra_sucursales','metodo_pagos.nombre','bancos.nombre as nombre_banco');
        
        // SI ES CASA CENTRAL
        if($this->tipo_usuario->sucursal != 1) {
        $metodos = $metodos->where('metodo_pagos.comercio_id', $this->casa_central_id);
        }
        
        // SI ES SUCURSAL
        if($this->tipo_usuario->sucursal == 1) {
         
        $metodos = $metodos->where('metodo_pagos.creador_id', $this->comercio_id);
        
        $metodos = $metodos->orWhere( function($query) {
		 $query->where('metodo_pagos_muestra_sucursales.sucursal_id', $this->comercio_id)
		 ->where('metodo_pagos_muestra_sucursales.muestra', 1);
		});
				
        
        }

     	if(strlen($this->search) > 0) {
           $metodos = $metodos->where('metodo_pagos.nombre', 'like', '%' . $this->search . '%');
       }
    
      if($this->categoria_search) {
          $metodos = $metodos->where('metodo_pagos.categoria', $this->categoria_search);
        }


	$metodos = $metodos->where('metodo_pagos.eliminado', 0)
	->groupBy('users.name','metodo_pagos.creador_id','metodo_pagos.id','metodo_pagos.comercio_id','metodo_pagos.categoria','metodo_pagos.cuenta','metodo_pagos.recargo','metodo_pagos.descripcion','metodo_pagos.muestra_sucursales','metodo_pagos.nombre','bancos.nombre')
    ->orderBy('metodo_pagos_muestra_sucursales.sucursal_id','asc')
    ->orderBy('metodo_pagos.categoria','asc')
    ->orderBy('metodo_pagos.nombre','asc')
	->paginate($this->pagination);


    if($this->tipo_usuario->sucursal != 1) {
    
    $bancos = bancos::where('bancos.comercio_id', $this->casa_central_id)
    ->where('bancos.tipo', 'like', 2)
    ->select('bancos.*')
    ->orderBy('bancos.nombre','asc')
    ->get();


    $plataformas =  bancos::where('bancos.comercio_id', $this->casa_central_id)
    ->where('bancos.tipo', 'like', 3)
    ->select('bancos.*')
    ->orderBy('bancos.nombre','asc')
    ->get();
    
    } else {
    
        
    $bancos = bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
    ->where('bancos.comercio_id', $this->casa_central_id)
    ->where('bancos_muestra_sucursales.sucursal_id',$comercio_id)
    ->where('bancos_muestra_sucursales.muestra',1)
    ->where('bancos.tipo', 'like', 2)
    ->select('bancos.*')
    ->orderBy('bancos.nombre','asc')
    ->get();

    $plataformas = bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
    ->where('bancos.comercio_id', $this->casa_central_id)
    ->where('bancos_muestra_sucursales.sucursal_id',$comercio_id)
    ->where('bancos_muestra_sucursales.muestra',1)
    ->where('bancos.tipo', 'like', 3)
    ->select('bancos.*')
    ->orderBy('bancos.nombre','asc')
    ->get();
            

        
    }
    
        if($this->cuenta != "Elegir"){
        
        $sucursales = sucursales::join('bancos_muestra_sucursales','bancos_muestra_sucursales.sucursal_id','sucursales.sucursal_id')
        ->join('users','users.id','sucursales.sucursal_id')
        ->select('users.name as nombre_sucursal','users.id')
        ->where('bancos_muestra_sucursales.banco_id', $this->cuenta)
        ->where('bancos_muestra_sucursales.muestra',1)
        ->where('sucursales.eliminado',0)
        ->get();
            
        
        } else {
        $sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name as nombre_sucursal','users.id')
        ->where('casa_central_id', $this->casa_central_id)->where('eliminado',0)->get();
    
        }
        
        $this->metodos_muestra_sucursales = metodo_pagos_muestra_sucursales::join('users','users.id','metodo_pagos_muestra_sucursales.sucursal_id')
        ->select('users.name as nombre_sucursal','metodo_pagos_muestra_sucursales.metodo_id')
        ->where('metodo_pagos_muestra_sucursales.muestra',1)->get();
        
        //dd($muestra_sucursales);
        
		return view('livewire.metodo-pago.component', [
		'data' => $metodos,
        'bancos' => $bancos,
        'plataformas' => $plataformas,
        'sucursales' => $sucursales,
        'metodos_muestra_sucursales' => $this->metodos_muestra_sucursales
		])
		->extends('layouts.theme-pos.app')
		->section('content');

	}
    */
    	public function render()
	{
        if (!Auth::check()) {
            // Redirigir al inicio de sesi¨®n y retornar una vista vac¨ªa
            $this->redirectLogin();
            return view('auth.login');
        }
     
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

        $this->comercio_id = $comercio_id;

        $this->tipo_usuario = User::find(Auth::user()->id);
        
        if($this->tipo_usuario->sucursal != 1) {
            $this->casa_central_id = $comercio_id;
        } else {
        
            $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
            $this->casa_central_id = $this->casa_central->casa_central_id;
        }

        //dd($this->tipo_usuario->sucursal);
        
		$metodos = metodo_pago::join('users','users.id','metodo_pagos.creador_id');
		if($this->tipo_usuario->sucursal == 1) {
		$metodos = $metodos->join('metodo_pagos_muestra_sucursales','metodo_pagos_muestra_sucursales.metodo_id','metodo_pagos.id');
		}
        $metodos = $metodos->join('bancos','bancos.id','metodo_pagos.cuenta')
        ->select('users.name as creador','metodo_pagos.creador_id','metodo_pagos.id','metodo_pagos.comercio_id','metodo_pagos.categoria','metodo_pagos.cuenta','metodo_pagos.recargo','metodo_pagos.descripcion','metodo_pagos.muestra_sucursales','metodo_pagos.nombre','bancos.nombre as nombre_banco');
        
        // SI ES CASA CENTRAL
        if($this->tipo_usuario->sucursal != 1) {
        $metodos = $metodos->where('metodo_pagos.comercio_id', $this->casa_central_id);
        }
        
        // SI ES SUCURSAL
        if($this->tipo_usuario->sucursal == 1) {
         
        $metodos = $metodos->where('metodo_pagos.creador_id', $this->comercio_id);
        
        $metodos = $metodos->orWhere( function($query) {
		 $query->where('metodo_pagos_muestra_sucursales.sucursal_id', $this->comercio_id)
		 ->where('metodo_pagos_muestra_sucursales.muestra', 1);
		});
				
        
        }

     	if(strlen($this->search) > 0) {
           $metodos = $metodos->where('metodo_pagos.nombre', 'like', '%' . $this->search . '%');
       }
    
      if($this->categoria_search) {
          $metodos = $metodos->where('metodo_pagos.categoria', $this->categoria_search);
        }


	$metodos = $metodos->where('metodo_pagos.eliminado', 0)
	->groupBy('users.name','metodo_pagos.creador_id','metodo_pagos.id','metodo_pagos.comercio_id','metodo_pagos.categoria','metodo_pagos.cuenta','metodo_pagos.recargo','metodo_pagos.descripcion','metodo_pagos.muestra_sucursales','metodo_pagos.nombre','bancos.nombre')
    ->orderBy('metodo_pagos.creador_id','asc')
    ->orderBy('metodo_pagos.categoria','asc')
    ->orderBy('metodo_pagos.nombre','asc')
	->paginate($this->pagination);


    if($this->tipo_usuario->sucursal != 1) {
    
    $bancos = bancos::where('bancos.comercio_id', $this->casa_central_id)
    ->where('bancos.tipo', 'like', 2)
    ->select('bancos.*')
    ->orderBy('bancos.nombre','asc')
    ->get();


    $plataformas =  bancos::where('bancos.comercio_id', $this->casa_central_id)
    ->where('bancos.tipo', 'like', 3)
    ->select('bancos.*')
    ->orderBy('bancos.nombre','asc')
    ->get();
    
    } else {
    
        
    $bancos = bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
    ->where('bancos.comercio_id', $this->casa_central_id)
    ->where('bancos_muestra_sucursales.sucursal_id',$comercio_id)
    ->where('bancos_muestra_sucursales.muestra',1)
    ->where('bancos.tipo', 'like', 2)
    ->select('bancos.*')
    ->orderBy('bancos.nombre','asc')
    ->get();

    $plataformas = bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
    ->where('bancos.comercio_id', $this->casa_central_id)
    ->where('bancos_muestra_sucursales.sucursal_id',$comercio_id)
    ->where('bancos_muestra_sucursales.muestra',1)
    ->where('bancos.tipo', 'like', 3)
    ->select('bancos.*')
    ->orderBy('bancos.nombre','asc')
    ->get();
            

        
    }
    
        if($this->cuenta != "Elegir"){
        
        $sucursales = sucursales::join('bancos_muestra_sucursales','bancos_muestra_sucursales.sucursal_id','sucursales.sucursal_id')
        ->join('users','users.id','sucursales.sucursal_id')
        ->select('users.name as nombre_sucursal','users.id')
        ->where('bancos_muestra_sucursales.banco_id', $this->cuenta)
        ->where('bancos_muestra_sucursales.muestra',1)
        ->where('sucursales.eliminado',0)
        ->get();
            
        
        } else {
        $sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name as nombre_sucursal','users.id')
        ->where('casa_central_id', $this->casa_central_id)->where('eliminado',0)->get();
    
        }
        
        $this->metodos_muestra_sucursales = metodo_pagos_muestra_sucursales::join('users','users.id','metodo_pagos_muestra_sucursales.sucursal_id')
        ->select('users.name as nombre_sucursal','metodo_pagos_muestra_sucursales.metodo_id')
        ->where('metodo_pagos_muestra_sucursales.muestra',1)->get();
        
        //dd($muestra_sucursales);
        
		return view('livewire.metodo-pago.component', [
		'data' => $metodos,
        'bancos' => $bancos,
        'plataformas' => $plataformas,
        'sucursales' => $sucursales,
        'metodos_muestra_sucursales' => $this->metodos_muestra_sucursales
		])
		->extends('layouts.theme-pos.app')
		->section('content');

	}

	public function Store()
	{

       $this->recargo = $this->convertirFormatoMoneda($this->recargo);
       
       $comercio_id = Auth::user()->comercio_id != 1 ? Auth::user()->comercio_id : Auth::user()->id;
       
       if($this->recargo < 0 && $this->recargo != null){
           $this->emit("msg-error","El recargo no puede ser negativo");
           return;
       }
       
	    if($this->categoria != 1) {
		$rules  =[
		'nombre' => 'required',
		'recargo' => 'required|numeric',
		'cuenta' => 'not_in:Elegir',
        'categoria' => 'not_in:Elegir'

		];

		$messages = [
		'name.required' => 'Nombre del metodo de pago requerido',
        'recargo.required' => 'El recargo es requerido, en caso de ser nulo coloque 0.',
        'recargo.numeric' => 'El recargo debe contener solo numeros.',
        'categoria.not_in' => 'La categoria del metodo de pago requerido',
        'cuenta.not_in' => 'Elija la cuenta.'

		];

		$this->validate($rules, $messages);
		
	    } else {
	        
	    $rules  =[
		'nombre' => 'required',
		'recargo' => 'required|numeric',
	    'categoria' => 'not_in:Elegir'

		];

		$messages = [
		'name.required' => 'Nombre del metodo de pago requerido',
        'recargo.required' => 'El recargo es requerido, en caso de ser nulo coloque 0.',
        'recargo.numeric' => 'El recargo debe contener solo numeros.',
        'categoria.not_in' => 'La categoria del metodo de pago requerido',
    
		];

		$this->validate($rules, $messages);  
	    }

        if($this->categoria == 1) {
        $this->cuenta = 1;
        }

    	//dd($this->cuenta);
	
    	$metodo = metodo_pago::create([
    	'nombre' => $this->nombre,
    	'recargo' => $this->recargo,
        'cuenta' => $this->cuenta,
        'categoria' => $this->categoria,
        'muestra_sucursales' => 1,
        'creador_id' => $comercio_id,
    	'comercio_id' => $this->casa_central_id,
        'acreditacion_inmediata' => $this->acreditacion_inmediata
    	]);
	

        if($this->muestra_sucursales != 1) {
    
          foreach ($this->muestra_sucursales as $key => $value) {
    
            metodo_pagos_muestra_sucursales::create([
              'metodo_id' => $metodo->id,
              'sucursal_id' => $key,
              'muestra' =>  $this->muestra_sucursales[$key]
            ]);
    
        }
        
            
        }
        
        $this->UpdateOrCreateDeduccion($metodo,$comercio_id);

		$this->resetUI();
		$this->emit('product-added', 'Producto Registrado');


		$this->emit('msg','Metodo de cobro registrado');
	}

    public function UpdateOrCreateDeduccion($metodo,$comercio_id){
       
        
        $existingDeducciones = metodo_pago_deducciones::where('metodo_id', $metodo->id)->where('eliminado',0)->get()->keyBy('id');

        foreach ($this->deducciones as $deduccion) {
            
            $porcentaje = str_replace(',', '.', $deduccion['porcentaje']); // Eliminamos los puntos de separaciÃ³n de miles

            if($porcentaje != ""){
            if (isset($deduccion['id'])) {
                $existingDeduccion = $existingDeducciones->get($deduccion['id']);
                if ($existingDeduccion) {
                    $existingDeduccion->update([
                        'nombre' => $deduccion['nombre'],
                        'deduccion' => $porcentaje,
                    ]);
                }
            } else {
                metodo_pago_deducciones::create([
                    'comercio_id' => $comercio_id,
                    'nombre' => $deduccion['nombre'],
                    'metodo_id' => $metodo->id,
                    'deduccion' => $porcentaje,
                ]);
            }                
            }

        }
    }
    
    
	public function Edit(metodo_pago $metodo)
	{
	    $this->agregar = 1;
		$this->selected_id = $metodo->id;
		$this->nombre = $metodo->nombre;
		$this->cuenta = $metodo->cuenta;
		$this->recargo = $metodo->recargo;
        $this->categoria = $metodo->categoria;
        $this->muestra_sucursales = $metodo->muestra_sucursales;
        $this->acreditacion_inmediata = $metodo->acreditacion_inmediata;
    
        $this->muestra_sucursales_metodos = metodo_pagos_muestra_sucursales::where('metodo_id', $metodo->id )->get();
    
        $this->muestra_sucursales = [];
    
        foreach($this->muestra_sucursales_metodos as $llaves => $sucus) {
    
        $this->muestra_sucursales[$sucus['sucursal_id']] = $sucus['muestra'];

        }
    
        $this->deducciones = metodo_pago_deducciones::where('metodo_id', $metodo->id)->where('eliminado',0)
            ->get()
            ->map(function ($deduccion) {
                return [
                    'id' => $deduccion->id,
                    'nombre' => $deduccion->nombre,
                    'porcentaje' => $deduccion->deduccion,
                ];
            })
            ->toArray();

		$this->emit('modal-show','Show modal');
	}

	public function Update()
	{
       $this->recargo = $this->convertirFormatoMoneda($this->recargo);
       
       if($this->recargo < 0 && $this->recargo != null){
           $this->emit("msg-error","El recargo no puede ser negativo");
           return;
       }
       
		$rules  =[
		'nombre' => 'required',
		'recargo' => 'required|numeric',
		'cuenta' => 'not_in:Elegir',
        'categoria' => 'not_in:Elegir'

		];

		$messages = [
		'name.required' => 'Nombre del metodo de pago requerido',
        'recargo.required' => 'El recargo es requerido, en caso de ser nulo coloque 0.',
        'recargo.numeric' => 'El recargo debe contener solo numeros.',
        'categoria.not_in' => 'La categoria del metodo de pago requerido',
        'cuenta.not_in' => 'Elija la cuenta.'

		];


		$this->validate($rules, $messages);

		$metodo = metodo_pago::find($this->selected_id);

		$metodo->update([
		'nombre' => $this->nombre,
		'recargo' => $this->recargo,
        'cuenta' => $this->cuenta,
        'recargo' => $this->recargo,
        'categoria' => $this->categoria,
        'muestra_sucursales' => 1,
        'acreditacion_inmediata' => $this->acreditacion_inmediata
		]);

    foreach ($this->muestra_sucursales as $llave => $value) {

        $bms = metodo_pagos_muestra_sucursales::where('sucursal_id', $llave)->where('metodo_id',$metodo->id)->first();

        if($bms != null) {

          $bms->muestra = $this->muestra_sucursales[$llave];
          $bms->save();

        } else {

          metodo_pagos_muestra_sucursales::create([
            'metodo_id' => $metodo->id,
            'sucursal_id' => $llave,
            'muestra' =>  $this->muestra_sucursales[$llave]
          ]);

        }



      }

    $this->UpdateOrCreateDeduccion($metodo,$metodo->comercio_id);

	$this->resetUI();
	$this->emit('msg','Metodo de cobro Actualizado');
	$this->emit('product-updated', 'Metodo de pago Actualizado');


	}



	public function resetUI()
	{
	$this->agregar = 0;
	$this->selected_id ='';
	$this->nombre ='';
	$this->recargo ='';
    $this->categoria = 'Elegir';
	$this->image = null;
	$this->cuenta = 'Elegir';
    $this->selected_id = '';
    $this->muestra_sucursales = true;
    $this->acreditacion_inmediata = 1;

	}

	protected $listeners =[
		'deleteRow' => 'Destroy'
	];

	public function ScanCode($code)
	{
		$this->ScanearCode($code);
		$this->emit('global-msg',"SE AGREGÃ“ EL PRODUCTO AL CARRITO");
	}


	public function Destroy(metodo_pago $metodo)
	{
		$imageTemp = $metodo->image;
		$metodo->eliminado = 1;
		$metodo->save();

		if($imageTemp !=null) {
			if(file_exists('storage/products/' . $imageTemp )) {
				unlink('storage/products/' . $imageTemp);
			}
		}

		$this->resetUI();
		$this->emit('product-deleted', 'Producto Eliminado');
		
		$this->emit('msg','Metodo de cobro Eliminado');
	}
	
	
	function convertirFormatoMoneda($valor) {
        // Eliminar los puntos
        //$valor = str_replace('.', '', $valor);
        // Reemplazar la coma con punto
        $valor = str_replace(',', '.', $valor);
        return $valor;
    }
}
