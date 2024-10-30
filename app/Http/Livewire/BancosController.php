<?php

namespace App\Http\Livewire;

use App\Models\metodo_pago;
use App\Models\bancos;
use App\Models\permisos_sucursales;
use App\Models\saldos_iniciales;
use App\Models\User;
use App\Models\sucursales;
use App\Models\bancos_muestra_sucursales;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class BancosController extends Component
{
  use WithPagination;
	use WithFileUploads;


	public $nombre,$recargo,$descripcion,$agregar,$saldo_inicial,$price,$muestra_sucursales,$bancos_muestra_sucursales,$stock,$alerts,$categoryid,$search,$image,$selected_id,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $metodos, $metodo, $categoria, $cuit, $CBU, $tipo, $id_check;
	private $pagination = 25;
	public $creador_id;


	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function mount()
	{
		$this->pageTitle = 'Listado';
		$this->componentName = 'Bancos';
		$this->categoria = 'Elegir';
		$this->tipo = 'Elegir';
        $this->stock_descubierto = 'Elegir';
        $this->muestra_sucursales = 1;
	}




    protected function redirectLogin()
    {
        return \Redirect::to("login");
    }
    
    /*
	public function render()
	{
        if (!Auth::check()) {
            // Redirigir al inicio de sesión y retornar una vista vacía
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

		
		$metodos = bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
		->join('users','users.id','bancos.creador_id');
        
        // SI ES CASA CENTRAL
        if($this->tipo_usuario->sucursal != 1) {
        $metodos = $metodos->where('bancos.comercio_id', $this->casa_central_id);
        }
        
        // SI ES SUCURSAL
        if($this->tipo_usuario->sucursal == 1) {
         
        $metodos = $metodos->where('bancos.creador_id', $this->comercio_id);
        
        $metodos = $metodos->orWhere( function($query) {
		 $query->where('bancos_muestra_sucursales.sucursal_id', $this->comercio_id)
		 ->where('bancos_muestra_sucursales.muestra', 1);
		});
				
        
        }
        
        if(strlen($this->search) > 0) {
        $metodos = $metodos->where('bancos.nombre', 'like', '%' . $this->search . '%');    
        }
        
        $metodos = $metodos->select('users.name as creador','bancos.creador_id','bancos.id','bancos.cbu','bancos.cuit','bancos.tipo','bancos.nombre','bancos.muestra_sucursales','bancos.comercio_id')
		->groupBy('users.name','bancos.creador_id','bancos.id','bancos.cbu','bancos.cuit','bancos.tipo','bancos.nombre','bancos.muestra_sucursales','bancos.comercio_id')
		->orderBy('bancos_muestra_sucursales.sucursal_id','desc')
		->paginate($this->pagination);


    if($this->tipo_usuario->sucursal != 1) {
    $sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name as nombre_sucursal','users.id')
    ->where('casa_central_id', $comercio_id)->where('eliminado',0)->get();
    }
    
    if($this->tipo_usuario->sucursal == 1) {
    $sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name as nombre_sucursal','users.id')
    ->where('sucursal_id', $comercio_id)->where('eliminado',0)->get();
    }
    
    $permisos_sucursales = permisos_sucursales::join('permisos_listas','permisos_listas.id','permisos_sucursales.permiso_id')
    ->where('permisos_sucursales.sucursal_id',$comercio_id)
    ->select('permisos_sucursales.*','permisos_listas.slug')
    ->get();
    
    //dd($permisos_sucursales);
    
    $this->bancos_muestra_sucursales = bancos_muestra_sucursales::join('users','users.id','bancos_muestra_sucursales.sucursal_id')
    ->select('users.name as nombre_sucursal','bancos_muestra_sucursales.banco_id')
    ->where('bancos_muestra_sucursales.muestra',1)->get();
        
        
	
		return view('livewire.bancos.component', [
		'data' => $metodos,
		'permisos_sucursales' => $permisos_sucursales,
        'sucursales' => $sucursales,
        'bancos_muestra_sucursales' => $this->bancos_muestra_sucursales
		])
		->extends('layouts.theme-pos.app')
		->section('content');

	}
    */
    	public function render()
	{
        if (!Auth::check()) {
            // Redirigir al inicio de sesión y retornar una vista vacía
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

		
		$metodos = bancos::join('users','users.id','bancos.creador_id');
        if($this->tipo_usuario->sucursal == 1) {
        $metodos = $metodos->join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id');
        }
        
        // SI ES CASA CENTRAL
        if($this->tipo_usuario->sucursal != 1) {
        $metodos = $metodos->where('bancos.comercio_id', $this->casa_central_id);
        }
        
        // SI ES SUCURSAL
        if($this->tipo_usuario->sucursal == 1) {
         
        $metodos = $metodos->where('bancos.creador_id', $this->comercio_id);
        
        $metodos = $metodos->orWhere( function($query) {
		 $query->where('bancos_muestra_sucursales.sucursal_id', $this->comercio_id)
		 ->where('bancos_muestra_sucursales.muestra', 1);
		});
				
        
        }
        
        if(strlen($this->search) > 0) {
        $metodos = $metodos->where('bancos.nombre', 'like', '%' . $this->search . '%');    
        }
        
        $metodos = $metodos->select('users.name as creador','bancos.creador_id','bancos.id','bancos.cbu','bancos.cuit','bancos.tipo','bancos.nombre','bancos.muestra_sucursales','bancos.comercio_id')
		->groupBy('users.name','bancos.creador_id','bancos.id','bancos.cbu','bancos.cuit','bancos.tipo','bancos.nombre','bancos.muestra_sucursales','bancos.comercio_id')
		->orderBy('bancos.creador_id','desc')
		->paginate($this->pagination);


    if($this->tipo_usuario->sucursal != 1) {
    $sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name as nombre_sucursal','users.id')
    ->where('casa_central_id', $comercio_id)->where('eliminado',0)->get();
    }
    
    if($this->tipo_usuario->sucursal == 1) {
    $sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name as nombre_sucursal','users.id')
    ->where('sucursal_id', $comercio_id)->where('eliminado',0)->get();
    }
    
    $permisos_sucursales = permisos_sucursales::join('permisos_listas','permisos_listas.id','permisos_sucursales.permiso_id')
    ->where('permisos_sucursales.sucursal_id',$comercio_id)
    ->select('permisos_sucursales.*','permisos_listas.slug')
    ->get();
    
    //dd($permisos_sucursales);
    
    $this->bancos_muestra_sucursales = bancos_muestra_sucursales::join('users','users.id','bancos_muestra_sucursales.sucursal_id')
    ->select('users.name as nombre_sucursal','bancos_muestra_sucursales.banco_id')
    ->where('bancos_muestra_sucursales.muestra',1)->get();
        
        
	
		return view('livewire.bancos.component', [
		'data' => $metodos,
		'permisos_sucursales' => $permisos_sucursales,
        'sucursales' => $sucursales,
        'bancos_muestra_sucursales' => $this->bancos_muestra_sucursales
		])
		->extends('layouts.theme-pos.app')
		->section('content');

	}

	public function Store()
	{
	    
	    if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;


      $this->tipo_usuario = User::find(Auth::user()->id);

      if($this->tipo_usuario->sucursal != 1) {
        $this->casa_central_id = $comercio_id;
      } else {

        $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
        $this->casa_central_id = $this->casa_central->casa_central_id;
      }

	    
	    //dd($this->muestra_sucursales);

		$rules  =[
			'nombre' => 'required|min:3',
            'tipo' => 'required|not_in:Elegir',
            'saldo_inicial' => 'required'
		];

		$messages = [
			'name.required' => 'Nombre del metodo de pago requerido',
			'tipo.required' => 'Debe seleccionar un tipo de cuenta',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'tipo.not_in' => 'Debe seleccionar un tipo de cuenta',
            'saldo_inicial.required' => 'Ingrese el saldo inicial'
		];

	$this->validate($rules, $messages);

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

	 $banco = bancos::create([
			'nombre' => $this->nombre,
            'tipo' => $this->tipo,
			'CBU' => $this->CBU,
            'cuit' => $this->cuit,
            'saldo_inicial' => $this->saldo_inicial,
			'comercio_id' => $this->casa_central_id,
            'muestra_sucursales' => 1,
            'creador_id' => Auth::user()->id
            
		]);

     $array_saldos = [
        'tipo' => 'Banco',
        'concepto' => 'Saldo inicial',
        'referencia_id' => $banco->id,
        'comercio_id' => $banco->comercio_id,
        'eliminado' => 0,
        'monto' => $banco->saldo_inicial,
        'metodo_pago' => $banco->id,
        'fecha' => $banco->created_at
        ];
    
    $s = saldos_iniciales::create($array_saldos);       
    
    if($this->muestra_sucursales != 1) {

    // Si es casa central y tiene sucursales crea la relacion banco con cada sucursal asociada sucursal
    
      foreach ($this->muestra_sucursales as $key => $value) {

        bancos_muestra_sucursales::create([
          'banco_id' => $banco->id,
          'sucursal_id' => $key,
          'muestra' =>  $this->muestra_sucursales[$key]
        ]);

    }

    }
    

    $this->resetUI();
		$this->emit('product-added', 'Banco/Platadorma de pago Registrado');


	}

    public function Agregar() {
	$this->agregar = 1;
    }
    
	public function Edit(bancos $metodo)
	{
	
	$this->agregar = 1;
	$this->selected_id = $metodo->id;
	$this->nombre = $metodo->nombre;
	$this->saldo_inicial = $metodo->saldo_inicial;
	$this->CBU = $metodo->CBU;
    $this->tipo = $metodo->tipo;
	$this->cuit = $metodo->cuit;
	$this->creador_id =  $metodo->creador_id;
    $this->muestra_sucursales = $metodo->muestra_sucursales;

    $this->muestra_sucursales_bancos = bancos_muestra_sucursales::where('banco_id', $metodo->id )->get();

    $this->muestra_sucursales = [];

    foreach($this->muestra_sucursales_bancos as $llaves => $sucus) {

  	$this->muestra_sucursales[$sucus['sucursal_id']] = $sucus['muestra'];

  	}
  	
	

		$this->emit('modal-show','Show modal');
	}

	public function Update()
	{
		$rules  =[
			'nombre' => "required|min:3"

		];

		$messages = [
  			'name.required' => 'Nombre del metodo de pago requerido',
  			'tipo.required' => 'Debe seleccionar un tipo de cuenta',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'tipo.not_in' => 'Debe seleccionar un tipo de cuenta'
		];

		$this->validate($rules, $messages);

		$banco = bancos::find($this->selected_id);

		$banco->update([
			'nombre' => $this->nombre,
			'CBU' => $this->CBU,
            'tipo' => $this->tipo,
            'cuit' => $this->cuit,
            'saldo_inicial' => $this->saldo_inicial,
            'muestra_sucursales' => 1
		]);

     $saldos_iniciales = saldos_iniciales::where('tipo','Banco')->where('concepto','Saldo inicial')->where('referencia_id',$banco->id)->first();

     $array_saldos = [
        'monto' => $banco->saldo_inicial,
        'fecha' => $banco->created_at
        ];
    
    $saldos_iniciales->update($array_saldos);      
    
    foreach ($this->muestra_sucursales as $llave => $value) {

        $bms = bancos_muestra_sucursales::where('sucursal_id', $llave)->where('banco_id',$banco->id)->first();

        if($bms != null) {

          $bms->muestra = $this->muestra_sucursales[$llave];
          $bms->save();

        } else {

          bancos_muestra_sucursales::create([
            'banco_id' => $banco->id,
            'sucursal_id' => $llave,
            'muestra' =>  $this->muestra_sucursales[$llave]
          ]);

        }



      }

		$this->resetUI();
		$this->emit('product-updated', 'Banco/Plataforma de pago Actualizado');


	}



	public function resetUI()
	{
	$this->saldo_inicial = 0;
	$this->agregar = 0;
    $this->selected_id ='';
	$this->nombre ='';
	$this->CBU ='';
	$this->cuit = '';
    $this->tipo = 'Elegir';

	}

	protected $listeners =[
		'deleteRow' => 'Destroy'
	];


	public function Destroy(bancos $metodo)
	{
		$metodo->delete();

		$this->resetUI();
		$this->emit('product-deleted', 'Producto Eliminado');
	}
}
