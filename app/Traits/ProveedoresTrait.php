<?php
namespace App\Traits;

use App\Models\proveedores;
use App\Models\provincias;
use App\Models\paises;
use App\Models\User;
use App\Models\permisos_sucursales;
use App\Models\sucursales;

use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;


trait ProveedoresTrait {
    

  use WocommerceTrait;

  //use classTestCommon;

  public $testingCommon;
 public $nombre_proveedor,
    $cuit,
    $id_proveedor,
    $direccion_proveedor,
    $altura_proveedor,
    $piso_proveedor,
    $depto_proveedor,
    $codigo_postal_proveedor,
    $provincia_proveedor,
    $localidad_proveedor,
    $telefono_proveedor,
    $mail_proveedor,
    $casa_central_id;
  
  public function StoreProveedorTrait(){
 

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		
		$rules  =[
		    'id_proveedor' => ['nullable','numeric',Rule::unique('proveedores')->where('comercio_id',$this->casa_central_id)->where('eliminado',0)],
			'nombre_proveedor' =>  'required',
			'mail_proveedor' => 'nullable|is_mail',
            'telefono_proveedor' => 'nullable|numeric|min:8'
		];

		$messages = [
			'id_proveedor.numeric' => 'El codigo del proveedor debe ser numerico',
			'id_proveedor.unique' => 'El codigo del proveedor ya existe',
			'nombre_proveedor.required' => 'Nombre del proveedor requerido',
			'nombre_proveedor.unique' => 'El nombre del proveedor ya existe',
            'mail_proveedor.is_mail' => 'Ingresa un correo válido',
            'telefono_proveedor.numeric' => 'El telefono deben ser solo numeros',
            'telefono_proveedor.min' => 'El telefono deben contener por lo menos 8 caracteres. Acuerdese de agregar la caracteristica primero.'

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
        
		$proveedores = proveedores::create([
		'nombre' => $this->nombre_proveedor,
		'id_proveedor' => $this->id_proveedor,
        'direccion' => $this->direccion_proveedor,
        'altura' => $this->altura_proveedor,
        'piso' => $this->piso_proveedor,
        'cuit' => $this->cuit,
        'depto' => $this->depto_proveedor,
        'codigo_postal' => $this->codigo_postal_proveedor,
        'provincia' => $this->provincia_proveedor,
        'localidad' => $this->localidad_proveedor,
        'telefono' => $this->telefono_proveedor,
        'mail' => $this->mail_proveedor,
		'comercio_id' => $this->casa_central_id,
		'creador_id' => $comercio_id
		]);
		
		//dd($proveedores);

		$this->reiniciarUIProveedor();
		
		return $proveedores;


	
      
  }


         public function reiniciarUIProveedor()
         {
          $this->cuit = '';
          $this->id_proveedor = '';
          $this->nombre_proveedor ='';
          $this->telefono_proveedor ='';
          $this->mail_proveedor ='';
          $this->provincia_proveedor ='';
          $this->localidad_proveedor ='';
          $this->direccion_proveedor ='';
          $this->altura_proveedor ='';
          $this->depto_proveedor ='';
          $this->piso_proveedor ='';
          $this->codigo_postal_proveedor ='';
        }


}

