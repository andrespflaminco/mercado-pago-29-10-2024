<?php
namespace App\Http\Livewire;


use App\Models\proveedores;
use App\Models\provincias;
use App\Models\paises;
use App\Models\User;
use App\Models\sucursales;
use App\Models\saldos_iniciales;

use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;

use Livewire\Component;
use Carbon\Carbon;

class ProveedoresController extends Component
{
  use WithPagination;
  use WithFileUploads;


  public $nombre, $cuit, $search, $proveedores,$agregar,$sucursal_id,  $componentName, $id_proveedor,$pageTitle, $id_check, $selected_id, $direccion, $localidad, $provincia, $mail, $telefono;
  private $pagination = 25;
  public $proveedores_orden;
  public $accion_lote;
  public $altura,$piso,$depto,$codigo_postal;

  
  //12-3-2024
  public $plazo_cuenta_corriente,$saldo_inicial_cuenta_corriente,$fecha_inicial_cuenta_corriente;
  
  public function paginationView()
  {
    return 'vendor.livewire.bootstrap';
  }
  
  public function Agregar() {
      $this->resetUI();
      $this->agregar = 1;
  }

  public function mount()
	{
	    $this->estado_filtro = 0;
	    $this->accion_lote = 'Elegir';
		$this->pageTitle = 'Listado';
		$this->componentName = 'Proveedores';
		$this->plazo_cuenta_corriente = 0;
	}

	// escuchar eventos
	protected $listeners = [
		'deleteRow' => 'Destroy',
		'RestaurarProveedor' => 'RestaurarProveedor',
        'accion-lote' => 'AccionEnLote'
	];

  public function render()
  {
      
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    $this->casa_central_id = Auth::user()->casa_central_user_id;
    
    $this->comercio_id = $comercio_id;
              
    $this->tipo_usuario = User::find($comercio_id);
            
              //dd($this->sucursal_id);
            
    if($this->tipo_usuario->sucursal != 1) {
    if($this->sucursal_id != null) { $this->sucursal_id  = $this->sucursal_id ;} else {$this->sucursal_id = 0;}
              
    } else {
               
    if($this->sucursal_id != null) { $this->sucursal_id  = $this->sucursal_id ;} else {$this->sucursal_id = $comercio_id;}
            
    }


    if(strlen($this->search) > 0)
    $proveedores = proveedores::where('proveedores.comercio_id', $this->casa_central_id)
    ->where('proveedores.nombre', 'like', '%' . $this->search . '%')
     ->where('proveedores.eliminado', $this->estado_filtro)
    ->orderBy('proveedores.id_proveedor','asc')
    ->paginate($this->pagination);
    else
    $proveedores = proveedores::where('proveedores.comercio_id', $this->casa_central_id)
     ->where('proveedores.eliminado', $this->estado_filtro)
    ->orderBy('proveedores.id_proveedor','asc')
    ->paginate($this->pagination);

    $provincias = provincias::all();
    
  //  $this->OrdenarProveedores();
    
    return view('livewire.proveedores.component', [
      'data' => $proveedores,
      'paises' => paises::all(),
      'provincias' =>  $provincias,
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
		
		$rules  =[
		    'id_proveedor' => ['nullable','numeric',Rule::unique('proveedores')->where('comercio_id',$this->casa_central_id)->where('eliminado',0)],
			'nombre' =>  'required',
			'mail' => 'nullable|is_mail',
            'telefono' => 'nullable|numeric|min:8'
		];

		$messages = [
			'id_proveedor.numeric' => 'El codigo del proveedor debe ser numerico',
			'id_proveedor.unique' => 'El codigo del proveedor ya existe',
			'nombre.required' => 'Nombre del proveedor requerido',
			'nombre.unique' => 'El nombre del proveedor ya existe',
            'mail.is_mail' => 'Ingresa un correo válido',
            'telefono.numeric' => 'El telefono deben ser solo numeros',
            'telefono.min' => 'El telefono deben contener por lo menos 8 caracteres. Acuerdese de agregar la caracteristica primero.'

		];

		$this->validate($rules, $messages);
        
        if(empty($this->id_proveedor)) {
        $ultimo_id = proveedores::where('comercio_id',$comercio_id)->max('id');
        $ultimo_proveedor = proveedores::find($ultimo_id);
        
        if($ultimo_proveedor != null){
        $this->id_proveedor = $ultimo_proveedor->id_proveedor + 1;
        } else {
        $this->id_proveedor = 1;    
        }
        } else {
            $this->id_proveedor = $this->id_proveedor;
        }
        
        if(empty($this->plazo_cuenta_corriente)) {$plazo_cuenta_corriente = 0;} else {$plazo_cuenta_corriente = $this->plazo_cuenta_corriente;}
        if(empty($this->fecha_inicial_cuenta_corriente)) {$fecha_inicial_cuenta_corriente = Carbon::now();} else {$fecha_inicial_cuenta_corriente = $this->fecha_inicial_cuenta_corriente;}

        
		$proveedores = proveedores::create([
		'cuit' => $this->cuit,
		'nombre' => $this->nombre,
		'id_proveedor' => $this->id_proveedor,
        'direccion' => $this->direccion,
        'altura' => $this->altura,
        'piso' => $this->piso,
        'depto' => $this->depto,
        'codigo_postal' => $this->codigo_postal,
        'provincia' => $this->provincia,
        'localidad' => $this->localidad,
        'telefono' => $this->telefono,
        'mail' => $this->mail,
		'comercio_id' => $this->casa_central_id,
		'creador_id' => $comercio_id,
        'plazo_cuenta_corriente' => $plazo_cuenta_corriente,
        'saldo_inicial_cuenta_corriente' => $this->saldo_inicial_cuenta_corriente,
        'fecha_inicial_cuenta_corriente' => $fecha_inicial_cuenta_corriente,
		]);
		
		//dd($proveedores);

		saldos_iniciales::create([
		'tipo' => 'proveedor',
        'concepto' => 'Saldo inicial',
        'referencia_id' => $proveedores->id,
        'comercio_id' => $comercio_id,
        'monto' => $this->saldo_inicial_cuenta_corriente,
        'eliminado' => 0,
        'fecha' => $fecha_inicial_cuenta_corriente
	    ]);
		    
		    
		$this->resetUI();
		$this->emit('msg', 'Proveedor Registrado');


	}



  public function Edit(proveedores $proveedor)
	{
	
	//dd($proveedor);
	
	$this->agregar = 1;
	$this->cuit = $proveedor->cuit;
	$this->selected_id = $proveedor->id;
	$this->id_proveedor = $proveedor->id_proveedor;
	$this->nombre = $proveedor->nombre;
    $this->direccion = $proveedor->direccion;
    $this->localidad = $proveedor->localidad;
    $this->provincia = $proveedor->provincia;
    $this->telefono = $proveedor->telefono;
    $this->altura = $proveedor->altura;
    $this->piso = $proveedor->piso;
    $this->depto = $proveedor->depto;
    $this->codigo_postal = $proveedor->codigo_postal;
    $this->mail = $proveedor->mail;

    $this->plazo_cuenta_corriente = $proveedor->plazo_cuenta_corriente;
    $this->saldo_inicial_cuenta_corriente = $proveedor->saldo_inicial_cuenta_corriente;
    $this->fecha_inicial_cuenta_corriente = Carbon::parse($proveedor->fecha_inicial_cuenta_corriente)->format('Y-m-d');
    
	$this->emit('modal-show','Show modal');
	}

	public function Update()
	{
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$rules  =[
			'id_proveedor' => ['nullable','numeric',Rule::unique('proveedores')->ignore($this->selected_id)->where('comercio_id',$this->casa_central_id)->where('eliminado',0)],
			'nombre' => 'required',
			'mail' => 'nullable|is_mail',
            'telefono' => 'nullable|numeric|min:8',
		];

		$messages = [
			'id_proveedor.numeric' => 'El codigo del proveedor debe ser un numero',
			'id_proveedor.unique' => 'El codigo del proveedor ya existe',
			'nombre.required' => 'Nombre del proveedor requerido',
			'nombre.unique' => 'El nombre del proveedor ya existe',
            'mail.is_mail' => 'Ingresa un correo válido',
            'telefono.numeric' => 'El telefono deben ser solo numeros',
             'telefono.min' => 'El telefono deben tener minimo 8 caracteres. Acuerdese de agregar la caracteristica de la zona.',
		];

		$this->validate($rules, $messages);

		$proveedores = proveedores::find($this->selected_id);

        if(empty($this->id_proveedor)) {
        $this->id_proveedor = $proveedores->id_proveedor;
        }

        if(empty($this->plazo_cuenta_corriente)) {$plazo_cuenta_corriente = 0;} else {$plazo_cuenta_corriente = $this->plazo_cuenta_corriente;}

		$proveedores->update([
		'cuit' => $this->cuit,
		'id_proveedor' => $this->id_proveedor,
		'nombre' => $this->nombre,
        'direccion' => $this->direccion,
        'localidad' => $this->localidad,
        'provincia' => $this->provincia,
        'telefono' => $this->telefono,
        'mail' => $this->mail,        
        'altura' => $this->altura,
        'piso' => $this->piso,
        'depto' => $this->depto,
        'codigo_postal' => $this->codigo_postal,
        'plazo_cuenta_corriente' => $plazo_cuenta_corriente,
        'saldo_inicial_cuenta_corriente' => $this->saldo_inicial_cuenta_corriente,
        'fecha_inicial_cuenta_corriente' => $this->fecha_inicial_cuenta_corriente,
        ]);

        $si = saldos_iniciales::where('referencia_id',$proveedores->id)->where('concepto','Saldo inicial')->where('tipo','proveedor')->first();
		
		if($si != null){
		$si->update([
		    'monto' => $this->saldo_inicial_cuenta_corriente
		    ]);		    
		} else {
		saldos_iniciales::create([
		'tipo' => 'proveedor',
        'concepto' => 'Saldo inicial',
        'referencia_id' => $proveedores->id,
        'comercio_id' => $comercio_id,
        'monto' => $this->saldo_inicial_cuenta_corriente,
        'eliminado' => 0,
        'fecha' => $this->fecha_inicial_cuenta_corriente
	    ]);
		        
		}

		    
		$this->resetUI();
		$this->emit('msg', 'Proveedor Actualizado');

}


public function resetUI()
{
  $this->id_proveedor = '';
  $this->nombre ='';
  $this->cuit ='';
  $this->telefono ='';
  $this->mail ='';
  $this->provincia ='';
  $this->localidad ='';
  $this->direccion ='';
  $this->altura ='';
  $this->depto ='';
  $this->piso ='';
  $this->codigo_postal ='';
  $this->search ='';
  $this->selected_id =0;
  $this->agregar = 0;
  $this->plazo_cuenta_corriente = "";
  $this->saldo_inicial_cuenta_corriente = 0;
  $this->fecha_inicial_cuenta_corriente = "";
}

public function Destroy(proveedores $proveedor)
{

  
  		$proveedores = proveedores::find($proveedor->id);

		$proveedores->update([
			'eliminado' => 1,

		]);


  $this->resetUI();
  $this->emit('msg', 'Proveedor Eliminado');
}

	public function RestaurarProveedor(proveedores $proveedores)
	{
		$proveedores->update([
			'eliminado' => 0
		]);

		$this->resetUI();
		$this->emit('msg', 'Proveedor Restaurado');
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
    
    $proveedores_checked = proveedores::select('proveedores.id')->whereIn('proveedores.id',$ids)->get();

    $this->id_check = [];
    
    foreach($proveedores_checked as $pc) {
    
    $pc->eliminado = $estado;
    $pc->save();
 
    }
    
    $this->id_check = [];
    $this->accion_lote = 'Elegir';
    
     $this->emit('global-msg',"PROVEEDOR ".$msg);
    
    }
    // Filtra por eliminado o activos 
    
	public function Filtro($estado)
	{
	   $this->estado_filtro = $estado;
	   
	}
	
	public function OrdenarProveedores(){
	    
	$this->proveedores_orden  = proveedores::where('proveedores.comercio_id', $this->casa_central_id)
    ->orderBy('proveedores.nombre','asc')
    ->get();
    
    $i = 1;
    foreach($this->proveedores_orden as $po) {
    $p = proveedores::find($po->id);
    $x = $i++;
    $p->update([
        'id_proveedor' => $x
        ]);
	}
	
	}



}
