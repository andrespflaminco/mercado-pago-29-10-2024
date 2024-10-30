<?php
namespace App\Traits;

use App\Models\Sale;
use App\Models\sucursales;
use App\Models\User;
use App\Models\paises;
use App\Models\provincias;
use App\Models\permisos_sucursales;

use App\Models\lista_precios;
use App\Models\ClientesMostrador;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Automattic\WooCommerce\Client;
use App\Models\wocommerce;
use Spatie\Permission\Models\Role;
use \WeDevs\ORM\Eloquent\Facades\DB;
use WorDBless\Users as WP_User;
use Carbon\Carbon;


use App\Models\saldos_iniciales;


use Illuminate\Validation\Rule;


// Trait

use App\Traits\WocommerceTrait;


trait ClientesTrait {

  use WocommerceTrait;

  //use classTestCommon;

  public $testingCommon;
  public $nombre_cliente,
    $id_cliente,
    $telefono_cliente,
    $email_cliente,
    $pais_cliente,
    $calle_cliente,
    $altura_cliente,
    $piso_cliente,
    $depto_cliente,
    $codigo_postal_cliente,
    $barrio_cliente,
    $localidad_cliente,
    $provincia_cliente,
    $dni_cliente,
    $observaciones_cliente,
    $lista_precio_cliente,
    $sucursal_agregar_cliente;


 public $plazo_cuenta_corriente,$saldo_inicial_cuenta_corriente,$fecha_inicial_cuenta_corriente,$recontacto;
 
  public function StoreClienteTrait(){
 
        
        //dd($this->sucursal_agregar);
        
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		
        if($this->lista_precios != null) {

        $rules  =[
            'id_cliente' => ['nullable','numeric',Rule::unique('clientes_mostradors','id_cliente')->where('comercio_id',$this->casa_central_id)->where('eliminado',0)],
			'nombre_cliente' =>  ['required'],
            'email_cliente' => ['nullable','is_mail'],
            'lista_precio_cliente' => 'not_in:Elegir',
            'telefono_cliente' => ['nullable','numeric'],
            'dni_cliente' => ['nullable','numeric'],
        ];

        $messages = [
          'id_cliente.unique' => 'El codigo del cliente ya existe',
          'id_cliente.numeric' => 'El codigo del cliente debe ser numerico',
          'nombre_cliente.required' => 'Nombre del cliente es requerido',
          'email_cliente.is_mail' => 'Ingresa un correo válido',
          'telefono_cliente.numeric' => 'El telefono deben ser solo numeros',
          'dni_cliente.numeric' => 'El CUIT deben ser solo numeros',
          'lista_precio_cliente.not_in' => 'Seleccione una lista de precios',
        ];

      } else {


        $rules  =[
            'id_cliente' => ['nullable','numeric',Rule::unique('clientes_mostradors')->where('comercio_id',$this->casa_central_id)->where('eliminado',0)],
			'nombre_cliente' =>  ['required'],
            'email_cliente' => ['nullable','is_mail'],
            'lista_precio_cliente' => 'not_in:Elegir',
            'telefono_cliente' => ['nullable','numeric'],
            'dni_cliente' => ['nullable','numeric']

        ];

        $messages = [
          'id_cliente.unique' => 'El codigo del cliente ya existe',
          'id_cliente.numeric' => 'El codigo del cliente debe ser numerico',
          'nombre_cliente.required' => 'Nombre del cliente es requerido',
          'email_cliente.is_mail' => 'Ingresa un correo válido',
          'telefono_cliente.numeric' => 'El telefono deben ser solo numeros',
          'dni_cliente.numeric' => 'El CUIT deben ser solo numeros',
          'lista_precio_cliente.not_in' => 'Seleccione una lista de precios',
        ];

      }
        $this->validate($rules, $messages);

        
        
        if(Auth::user()->comercio_id != 1)
    		$comercio_id = Auth::user()->comercio_id;
    		else
    		$comercio_id = Auth::user()->id;

        if($this->lista_precio_cliente) {
          $this->lista_precio_cliente = $this->lista_precio_cliente;
        } else {
          $this->lista_precio_cliente = 0;
        }
        
        
        if(empty($this->id_cliente) || $this->id_cliente === ' ' || !is_numeric($this->id_cliente)) {
        $ultimo_id = ClientesMostrador::where('comercio_id',$this->casa_central_id)->max('id_cliente');
        
        if($ultimo_id != null){
        $this->id_cliente = $ultimo_id + 1;
        } else {
        $this->id_cliente = 1;    
        }
        } else {
            $this->id_cliente = $this->id_cliente;
        }
        
        
        ////////////////       NO TIENE WC INTEGRADO             ///////////////////////


        $cliente = ClientesMostrador::create([
           'nombre' => $this->nombre_cliente,
          'id_cliente' => $this->id_cliente,
          'telefono' => $this->telefono_cliente,
          'email' => $this->email_cliente,
          'pais' => $this->pais_cliente,
          'direccion' => $this->calle_cliente,
          'altura' => $this->altura_cliente,
          'piso' => $this->piso_cliente,
          'depto' => $this->depto_cliente,
          'codigo_postal' => $this->codigo_postal_cliente,
          'barrio' => $this->barrio_cliente,
          'localidad' => $this->localidad_cliente,
          'provincia' => $this->provincia_cliente,
          'status' => "Activo",
          'dni' => $this->dni_cliente,
          'observaciones' => $this->observaciones_cliente,
          'lista_precio' => $this->lista_precio_cliente,
          'creador_id' => $this->sucursal_agregar_cliente,
          'comercio_id' => $this->casa_central_id,
          'saldo_inicial_cuenta_corriente' => $this->saldo_inicial_cuenta_corriente,
          'fecha_inicial_cuenta_corriente' => $this->fecha_inicial_cuenta_corriente,
          'plazo_cuenta_corriente' => $this->plazo_cuenta_corriente,
          'recontacto' => $this->recontacto,
          'monto_maximo_cuenta_corriente' => $this->monto_maximo_cuenta_corriente

          ]);

    		saldos_iniciales::create([
    		'tipo' => 'cliente',
            'concepto' => 'Saldo inicial',
            'referencia_id' => $cliente->id,
            'comercio_id' => $comercio_id,
            'monto' => $this->saldo_inicial_cuenta_corriente,
            'eliminado' => 0,
            'fecha' => $this->fecha_inicial_cuenta_corriente
    	    ]);

          $wc = wocommerce::where('comercio_id', $this->comercio_id)->first();

          if($wc != null){
          $result = $this->UpdateOrCreateClienteWC($cliente,$wc);
          $this->emit('user-updated', $result);
              
          }


        $this->resetUICliente();
        
        return $cliente;
      
  }


         public function resetUICliente()
         {
                 
        $this->id_cliente = "";   
        $this->piso_cliente = "";
        $this->pais_cliente = "";
        $this->altura_cliente = "";
        $this->depto_cliente = "";
        $this->codigo_postal_cliente = "";
        $this->agregar_cliente  = 0;
        $this->nombre_cliente  ='';
        $this->telefono_cliente ='';
        $this->email_cliente ='';
        $this->observaciones_cliente ='';
        $this->direccion_cliente='';
        $this->barrio_cliente ='';
        $this->image_cliente ='';
        $this->localidad_cliente ='';
        $this->dni_cliente ='';
        $this->provincia_cliente ='';
        }

}