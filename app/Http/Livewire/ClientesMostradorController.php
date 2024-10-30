<?php

namespace App\Http\Livewire;
use App\Models\Sale;
use App\Models\sucursales;
use App\Models\User;
use App\Models\paises;
use App\Models\provincias;
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


class ClientesMostradorController extends Component
{

          use WithPagination;
          use WithFileUploads;
	      use WocommerceTrait;

          public $nombre,$telefono,$recontacto,$sucursal_guardar,$wc,$plazo_cuenta_corriente,$wc_customer_id,$sucursal_agregar,$id_check,$sucursales,$sucursal_id,$email,$agregar, $status,$image,$provincia,$localidad,$observaciones,$id_cliente, $fileLoaded,$direccion,$barrio,$dni, $comercio_id, $selected_id , $usuario_id, $cliente, $clientes, $lista_precio_cliente;
          public $pageTitle, $componentName, $search;
          public $query, $ultimo_id;
          public $accion_lote;
          public $saldo_inicial_cuenta_corriente,$fecha_inicial_cuenta_corriente,$monto_maximo_cuenta_corriente;
          public $contacts;
          
          public $pais,$calle,$altura,$piso,$depto,$codigo_postal;
          public $highlightIndex;
          public $condicion_iva, $tipo_comprobante;
          private $pagination = 25;
          
          public $solo_ver_clientes_propios;

          public function paginationView()
          {
              return 'vendor.livewire.bootstrap';
          }

          public function mount()
          {

              $this->estado_filtro = 0;
              $this->pageTitle ='Listado';
              $this->componentName ='Clientes';
              $this->status ='Elegir';
	          $this->accion_lote = 'Elegir';
              $this->condicion_iva ='';
              $this->tipo_comprobante ='';
              $this->status = "Activo";
              $this->lista_precio_cliente = 'Elegir';
              $this->pais = "Argentina";
              $this->columnaOrden = "id_cliente";
              $this->direccionOrden = "asc";
              $this->plazo_cuenta_corriente = null;

          }

              public function OrdenarColumna($columna)
            {
                if ($this->columnaOrden == $columna) {
                    // Cambiar la dirección de orden si la columna es la misma
                    $this->columnaOrden = $columna;
                    $this->direccionOrden = $this->direccionOrden == 'asc' ? 'desc' : 'asc';
                } else {
                    // Si es una columna diferente, cambiar a la nueva columna y establecer la dirección predeterminada
                    $this->columnaOrden = $columna;
                    $this->direccionOrden = 'asc';
                    
                   // dd($this->columnaOrden,$this->direccionOrden);
                }
                
                $this->render();
            }

          public function Agregar() {
              $this->fecha_inicial_cuenta_corriente = Carbon::now()->format('Y-m-d');
              $this->saldo_inicial_cuenta_corriente = 0;
              $this->monto_maximo_cuenta_corriente = null;
              $this->sucursal_agregar = $this->comercio_id;
              $this->agregar = 1;
          }
          
          public function ConfiguracionClientes($comercio_id){
              $info_sucursal = sucursales::where('sucursal_id',$comercio_id)->first();
              if($info_sucursal == null){return 0;} else {
                  $solo_ver_clientes_propios = $info_sucursal->solo_ver_clientes_propios;
                  return $solo_ver_clientes_propios;
              }
          }
          
          public function render()
          {
              $usuario_id = Auth::user()->id;

              if(Auth::user()->comercio_id != 1)
              $comercio_id = Auth::user()->comercio_id;
              else
              $comercio_id = Auth::user()->id;
              
              $this->comercio_id = $comercio_id;
              
              $this->wc = wocommerce::where('comercio_id',$comercio_id)->first();
              
              $this->tipo_usuario = User::find($comercio_id);
            
              //dd($this->sucursal_id);
            
              if($this->tipo_usuario->sucursal != 1) {
              $this->casa_central_id = $comercio_id;
              if($this->sucursal_id != null) { $this->sucursal_id  = $this->sucursal_id ;} else {$this->sucursal_id = 0;}
              
              } else {
            
               $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
               $this->casa_central_id = $this->casa_central->casa_central_id;
               
               if($this->sucursal_id != null) { $this->sucursal_id  = $this->sucursal_id ;} else {$this->sucursal_id = 0;}
            
              }
            
              if($this->sucursal_id != 0) {
              $this->sucursal_guardar = $this->sucursal_id;
              } else {
              $this->sucursal_guardar = $comercio_id;  
              }
              //dd($this->sucursal_id);
              
              $this->lista_precios = lista_precios::where('comercio_id',$this->casa_central_id)->get();

              $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')
              ->where('sucursales.eliminado',0)
              ->where('casa_central_id', $this->casa_central_id)->get();

              $sucursalIds = $this->sucursales->pluck('sucursal_id')->toArray(); // Convertir a array
              array_push($sucursalIds, $this->casa_central_id); // Agregar 252 al final del array
              
              // 2-9-2024
              $solo_ver_clientes_propios = $this->ConfiguracionClientes($this->comercio_id);
              $this->solo_ver_clientes_propios = $solo_ver_clientes_propios;
              
              if($solo_ver_clientes_propios == 1){
              $this->sucursal_id = $this->comercio_id;    
              } 
              
              
                $data = ClientesMostrador::join('users','users.id','clientes_mostradors.creador_id')
                ->select('clientes_mostradors.*','users.name as nombre_sucursal',ClientesMostrador::raw('DATEDIFF(NOW(), clientes_mostradors.last_sale) as dias_desde_creacion'))
                ->where('clientes_mostradors.comercio_id',$this->casa_central_id)
                ->where('clientes_mostradors.eliminado', $this->estado_filtro);
                
                if(strlen($this->search) > 0) {  
                  $data = $data->where('clientes_mostradors.nombre', 'like', '%' . $this->search . '%');
                }
                
                if($this->sucursal_id != 0) {
                $data = $data->where('clientes_mostradors.creador_id', $this->sucursal_id);    
                } else {
                $data = $data->whereIn('clientes_mostradors.creador_id', $sucursalIds);    
                }
                  
                $data = $data->where('clientes_mostradors.sucursal_id', 0)
                ->orderBy($this->columnaOrden, $this->direccionOrden)
            //    ->orderBy('clientes_mostradors.id_cliente','asc')
                ->paginate($this->pagination);
                
            //    dd($data);
                
                $this->provincias = provincias::all();
                $this->paises = paises::all();
                
               // dd($provincias);
                
             return view('livewire.clientes-mostrador.component',[
              'data' => $data,
              'paises' => $this->paises,
              'provincias' =>  $this->provincias,
              'lista_precios' => $this->lista_precios
          ])
             ->extends('layouts.theme-pos.app')
             ->section('content');
         }

         public function resetUI()
         {
                 
        $this->id_cliente = "";   
        $this->piso = "";
        $this->pais = "";
        $this->altura = "";
        $this->depto = "";
        $this->codigo_postal = "";
        //  $this->sucursal_guardar = $this->sucursal_id;
          $this->agregar = 0;
          $this->nombre ='';
          $this->telefono='';
          $this->email='';
          $this->observaciones ='';
          $this->direccion='';
          $this->barrio ='';
          $this->image ='';
          $this->search ='';
          $this->status ='';
          $this->tipo_comprobante = '';
          $this->condicion_iva = '';
          $this->localidad ='';
          $this->dni ='';
          $this->provincia ='';
          $this->selected_id =0;
          $this->resetValidation();
          $this->resetPage();
      }


      public function Edit(ClientesMostrador $cliente)
      {
          
        $this->agregar = 1;
        
        $this->sucursal_agregar = $cliente->creador_id;
        $this->id_cliente = $cliente->id_cliente;
        $this->selected_id = $cliente->id;
        $this->nombre = $cliente->nombre;
        $this->observaciones = $cliente->observaciones;
        $this->telefono=$cliente->telefono;
        $this->email=$cliente->email;
        $this->calle=$cliente->direccion;
        $this->barrio =$cliente->barrio;
        $this->status =$cliente->status;
        $this->provincia =$cliente->provincia;
        $this->localidad =$cliente->localidad;
        $this->recontacto =$cliente->recontacto;
        $this->dni =$cliente->dni;
        $this->plazo_cuenta_corriente = $cliente->plazo_cuenta_corriente;
        
        $this->piso =$cliente->piso;
        $this->pais =$cliente->pais;
        $this->altura =$cliente->altura;
        $this->depto =$cliente->depto;
        $this->codigo_postal =$cliente->codigo_postal;
        $this->wc_customer_id = $cliente->wc_customer_id;
    //    $this->saldo_inicial_cuenta_corriente = $cliente->saldo_inicial_cuenta_corriente;
        $this->monto_maximo_cuenta_corriente = $cliente->monto_maximo_cuenta_corriente;
        $this->fecha_inicial_cuenta_corriente = $fechaFormateada = Carbon::parse($cliente->fecha_inicial_cuenta_corriente)->format('Y-m-d');

        $sucursales = saldos_iniciales::where('comercio_id', $this->casa_central_id)->where('referencia_id',$cliente->id)->where('tipo','cliente')->where('concepto','Saldo inicial')->get();
        
        $this->saldo_inicial_cuenta_corriente = [];
        
        $monto_total_saldo_inicial = 0;
        foreach ($sucursales as $sucursal) {
            $this->saldo_inicial_cuenta_corriente[$sucursal->sucursal_id] = $sucursal->monto ?? 0;
        }


        $this->lista_precio_cliente =$cliente->lista_precio;

          $this->emit('show-modal','open!');

      }


      protected $listeners =[
          'deleteRow' => 'destroy',
          'resetUI' => 'resetUI',
          'accion-lote' => 'AccionEnLote'

      ];

public function Store()
{
    // Determinar el comercio_id basado en el usuario autenticado
    $comercio_id = Auth::user()->comercio_id != 1 ? Auth::user()->comercio_id : Auth::user()->id;

    // Definir reglas y mensajes de validación
    $rules = [
        'id_cliente' => ['nullable', 'numeric', Rule::unique('clientes_mostradors')->where('comercio_id', $this->casa_central_id)->where('eliminado', 0)],
        'nombre' => ['required'],
        'email' => ['nullable', 'is_mail'],
        'lista_precio_cliente' => 'not_in:Elegir',
        'telefono' => ['nullable', 'numeric'],
        'dni' => 'numeric',
    ];

    $messages = [
        'id_cliente.unique' => 'El codigo del cliente ya existe',
        'id_cliente.numeric' => 'El codigo del cliente debe ser numerico',
        'nombre.required' => 'Nombre del cliente es requerido',
        'email.is_mail' => 'Ingresa un correo válido',
        'telefono.numeric' => 'El telefono deben ser solo numeros',
        'dni.numeric' => 'El CUIT deben ser solo numeros',
        'lista_precio_cliente.not_in' => 'Seleccione una lista de precios',
    ];

    // Validar la solicitud
    $this->validate($rules, $messages);

    $wc = wocommerce::where('comercio_id', $comercio_id)->first();

    if($wc != null){
    if($this->email == null){
        $this->emit("msg-error","Debe incluir el mail para poder sincronizarlo con wocommerce");
        return;
    }
    } 
        
    // Determinar el id_cliente
    if (empty($this->id_cliente) || $this->id_cliente === ' ' || !is_numeric($this->id_cliente)) {
        $ultimo_id = ClientesMostrador::where('comercio_id', $this->casa_central_id)->max('id_cliente');
        $this->id_cliente = $ultimo_id ? $ultimo_id + 1 : 1;
    }

    // Crear el cliente
    $cliente = ClientesMostrador::create([
        'nombre' => $this->nombre,
        'id_cliente' => $this->id_cliente,
        'telefono' => $this->telefono,
        'email' => $this->email,
        'pais' => $this->pais,
        'direccion' => $this->calle,
        'altura' => $this->altura,
        'piso' => $this->piso,
        'depto' => $this->depto,
        'codigo_postal' => $this->codigo_postal,
        'barrio' => $this->barrio,
        'localidad' => $this->localidad,
        'provincia' => $this->provincia,
        'status' => $this->status,
        'dni' => $this->dni,
        'observaciones' => $this->observaciones,
        'lista_precio' => $this->lista_precio_cliente ?: 0,
        'creador_id' => $this->sucursal_agregar,
        'plazo_cuenta_corriente' => $this->plazo_cuenta_corriente,
        'recontacto' => $this->recontacto,
        'comercio_id' => $this->casa_central_id,
        'saldo_inicial_cuenta_corriente' => 0,
        'fecha_inicial_cuenta_corriente' => $this->fecha_inicial_cuenta_corriente,
        'monto_maximo_cuenta_corriente' => $this->monto_maximo_cuenta_corriente
    ]);


    // 5-8-2024
    
    // Procesar los saldos iniciales para cada sucursal
    $sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')
    ->where('sucursales.eliminado',0)
    ->where('casa_central_id', $this->casa_central_id)->get();
              
    $monto_total_saldo_inicial = 0;

    $monto_casa_central = $this->saldo_inicial_cuenta_corriente[Auth::user()->casa_central_user_id] ?? 0;
    
        saldos_iniciales::create([
            'tipo' => 'cliente',
            'concepto' => 'Saldo inicial',
            'referencia_id' => $cliente->id,
            'comercio_id' => Auth::user()->casa_central_user_id,
            'sucursal_id' => Auth::user()->casa_central_user_id,
            'monto' => $monto_casa_central,
            'eliminado' => 0,
            'fecha' => $this->fecha_inicial_cuenta_corriente
        ]);
    
    $monto_total_saldo_inicial += $monto_casa_central;

            
    foreach ($sucursales as $sucursal) {
        
        $saldo_inicial = $this->saldo_inicial_cuenta_corriente[$sucursal->sucursal_id] ?? 0;
        if (empty($saldo_inicial)) {
            $saldo_inicial = 0; // O cualquier valor por defecto que tenga sentido
        }
        
        saldos_iniciales::create([
            'tipo' => 'cliente',
            'concepto' => 'Saldo inicial',
            'referencia_id' => $cliente->id,
            'comercio_id' => $comercio_id,
            'sucursal_id' => $sucursal->sucursal_id,
            'monto' => $saldo_inicial,
            'eliminado' => 0,
            'fecha' => $this->fecha_inicial_cuenta_corriente
        ]);

        $monto_total_saldo_inicial += $saldo_inicial;
    }

    // Actualizar el saldo inicial total del cliente
    $cliente->update(['saldo_inicial_cuenta_corriente' => $monto_total_saldo_inicial]);

    // 5-8-2024
    
    
    // Integrar con WooCommerce si está configurado
    $wc = wocommerce::where('comercio_id', $comercio_id)->first();
    if ($wc != null) {
       // $result = $this->UpdateOrCreateClienteWC($cliente, $wc);
        $result = $this->CreateOrUpdateWooCommerceCustomer($cliente,$wc);
        
        
        if ($result['success']) {
            $this->emit('msg-success', $result['message']);
        } else {
            $this->emit('msg-error', $result['message']);
        }
        
        $this->emit('user-updated', $result);
    }

    // Resetear la interfaz de usuario y emitir evento de éxito
    $this->resetUI();
    $this->emit('user-added', 'Cliente Registrado');
}


      public function Update()
    	{
    	
    	if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

        $wc = wocommerce::where('comercio_id', $comercio_id)->first();

        if($wc != null){
        if($this->email == null){
            $this->emit("msg-error","Debe incluir el mail para poder sincronizarlo con wocommerce");
            return;
        }
        } 
        		
        if($this->lista_precios != null) {

         $rules  =[
           'id_cliente' => ['nullable','numeric',Rule::unique('clientes_mostradors')->ignore($this->selected_id)->where('comercio_id',$this->casa_central_id)->where('eliminado',0)],
           'email' => ['nullable','is_mail'],
           'lista_precio_cliente' => 'not_in:Elegir',
           'telefono' => ['nullable','numeric'],
           'dni' => 'numeric',
        ];

        $messages = [
         'id_cliente.numeric' => 'El codigo del cliente debe ser un numero',
		  'id_cliente.unique' => 'El codigo del cliente ya existe',
         'email.is_mail' => 'Ingresa un correo válido',
          'telefono.numeric' => 'El telefono deben ser solo numeros',
          'dni.numeric' => 'El DNI deben ser solo numeros',
          'lista_precio_cliente.not_in' => 'Seleccione una lista de precios',
        ];


        } else {


       $rules  =[
           'id_cliente' => ['nullable','numeric',Rule::unique('clientes_mostradors')->ignore($this->selected_id)->where('comercio_id',$this->casa_central_id)->where('eliminado',0)],
           'email' => ['nullable','is_mail'],
           'lista_precio_cliente' => 'not_in:Elegir',
           'telefono' => ['nullable','numeric'],
           'dni' => 'numeric',
        ];

        $messages = [
         'id_cliente.numeric' => 'El codigo del cliente debe ser un numero',
		  'id_cliente.unique' => 'El codigo del cliente ya existe',
         'email.is_mail' => 'Ingresa un correo válido',
          'telefono.numeric' => 'El telefono deben ser solo numeros',
          'dni.numeric' => 'El DNI deben ser solo numeros',
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


    	$cliente = ClientesMostrador::find($this->selected_id);
    		
    	if (strpos($this->dni, '-') !== false) {
    	    $this->dni =str_replace('-', '', $this->dni);
    	}
    		
    	//dd($this->sucursal_guardar);
    	
        if(empty($this->id_cliente) || $this->id_cliente === ' ' || !is_numeric($this->id_cliente)){ $this->id_cliente = $cliente->id_cliente; } else {$this->id_cliente = $this->id_cliente;}
    
    	$cliente->update([
          'nombre' => $this->nombre,
          'id_cliente' => $this->id_cliente,
          'telefono' => $this->telefono,
          'email' => $this->email,
          'pais' => $this->pais,
          'direccion' => $this->calle,
          'altura' => $this->altura,
          'piso' => $this->piso,
          'depto' => $this->depto,
          'codigo_postal' => $this->codigo_postal,
          'barrio' => $this->barrio,
          'localidad' => $this->localidad,
          'provincia' => $this->provincia,
          'dni' => $this->dni,
          'observaciones' => $this->observaciones,
          'plazo_cuenta_corriente' => $this->plazo_cuenta_corriente,
          'lista_precio' => $this->lista_precio_cliente,
          'creador_id' => $this->sucursal_agregar,
          'plazo_cuenta_corriente' => $this->plazo_cuenta_corriente,
          'recontacto' => $this->recontacto,
          'fecha_inicial_cuenta_corriente' => $this->fecha_inicial_cuenta_corriente,
          'monto_maximo_cuenta_corriente' => $this->monto_maximo_cuenta_corriente
    	]);

        // 5-8-2024
        /*
        $sucursales = saldos_iniciales::where('comercio_id', $this->casa_central_id)
        ->where('referencia_id',$cliente->id)
        ->where('tipo','cliente')
        ->where('concepto','Saldo inicial')
        ->get();
        */
        
        $monto_total_saldo_inicial = 0;
        
        $sucursales = $this->GetSucursales($comercio_id);
        $saldo_inicial_base = $this->saldo_inicial_cuenta_corriente[$this->casa_central_id] ?? 0;
        $this->UpdateOrCreateSaldoInicial($cliente->id,$comercio_id,$this->casa_central_id,$saldo_inicial_base,$this->fecha_inicial_cuenta_corriente);
        $monto_total_saldo_inicial += $saldo_inicial_base;
        
        foreach ($sucursales as $sucursal) {
        
		$saldo_inicial = $this->saldo_inicial_cuenta_corriente[$sucursal->sucursal_id] ?? 0;
		if (empty($saldo_inicial)) {
            $saldo_inicial = 0; // O cualquier valor por defecto que tenga sentido
        }
        
        $this->UpdateOrCreateSaldoInicial($cliente->id,$comercio_id,$sucursal->sucursal_id,$saldo_inicial,$this->fecha_inicial_cuenta_corriente);
        
        /*
        $si = saldos_iniciales::find($sucursal->id);
		
		if($si != null){
		$si->update([
		    'monto' => $saldo_inicial
		]);
		} else {
		    
		saldos_iniciales::create([
    		'tipo' => 'cliente',
            'concepto' => 'Saldo inicial',
            'referencia_id' => $cliente->id,
            'comercio_id' => $comercio_id,
            'sucursal_id' => $sucursal->sucursal_id,
            'monto' => $saldo_inicial,
            'eliminado' => 0,
            'fecha' => $this->fecha_inicial_cuenta_corriente
    	    ]);
		}
		
		*/
		
		$monto_total_saldo_inicial += $saldo_inicial;
        }
        
        $cliente->update([
        'saldo_inicial_cuenta_corriente' => $monto_total_saldo_inicial    
            ]);
        
        // 5-8-2024
		    
      ////////////////       TIENE WC INTEGRADO             ///////////////////////

      $wc = wocommerce::where('comercio_id', $comercio_id)->first();
      
      if($wc != null){
      //$result = $this->UpdateOrCreateClienteWC($cliente,$wc);
      $result = $this->CreateOrUpdateWooCommerceCustomer($cliente,$wc);
      
        if ($result['success']) {
            $this->emit('msg-success', $result['message']);
        } else {
            $this->emit('msg-error', $result['message']);
        }
        
      $this->emit('user-updated', $result);
              
      }
      $this->resetUI();
      $this->emit('user-updated', 'Cliente Actualizado');


    	}

      public function destroy(ClientesMostrador $cliente)
      {
          
        $cliente->eliminado = 1;
        $cliente->save();
        
        $this->emit('user-deleted','Cliente Eliminado');
        
    //   if($cliente) {
    //      $sales = Sale::where('cliente_id', $cliente->id)->count();
  
            
    //      if($sales > 0)  {
    //          $this->emit('user-withsales','No es posible eliminar el cliente porque tiene ventas registradas');
    //      } else {
    //          $cliente->delete();
    //          $this->resetUI();
    //          $this->emit('user-deleted','Cliente Eliminado');
    //      }
          
    //  }
    }




public function list_wc() {

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;


  $wc = wocommerce::where('comercio_id', $comercio_id)->first();

  if($wc != null){

    $host = $wc->url.'/wp-json/wp/v2/users';


    $headers = array(
        'Content-Type:application/json',
        'Authorization: Basic '. base64_encode('christian2205:ch.flaminco2020')
    );

    $ch = curl_init($host);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

    $result = curl_exec($ch);
    curl_close($ch);

    dd($result);

}

}


public function Filtrar() {
    $this->render();
}

public function ExportarExcel() {
    
    
    $hora = " ".Carbon::now()->format('d-m-Y H:i:s');
    return redirect('report/excel-clientes/'.$this->sucursal_id.'/'. $hora." hs");
}

public function ElegirSucursal($id) {
    $this->sucursal_id = $id;
}

// Filtra por eliminado o activos 
    
public function Filtro($estado)
{
   $this->estado_filtro = $estado;
	   
}
	
	
		public function RestaurarCliente(ClientesMostrador $clientes)
	{
		$clientes->update([
			'eliminado' => 0
		]);

		$this->resetUI();
		$this->emit('msg', 'Cliente Restaurado');
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
    
    $clientes_checked = ClientesMostrador::whereIn('id',$ids)->get();

    $this->id_check = [];
    
    foreach($clientes_checked as $pc) {
    
    $pc->eliminado = $estado;
    $pc->save();
 
    }
    
    $this->id_check = [];
    $this->accion_lote = 'Elegir';
    
     $this->emit('msg',"CLIENTES ".$msg);
    
    }
    
    public function SincronizarCliente($cliente_id) {
    
          if(Auth::user()->comercio_id != 1)
          $comercio_id = Auth::user()->comercio_id;
          else
          $comercio_id = Auth::user()->id;
        
          $cliente = ClientesMostrador::find($cliente_id);
          
          $wc = wocommerce::where('comercio_id', $comercio_id)->first();
        
          //$result = $this->UpdateOrCreateClienteWC($cliente,$wc);
          $result = $this->CreateOrUpdateWooCommerceCustomer($cliente,$wc);
          
        if ($result['success']) {
            $this->emit('msg-success', $result['message']);
        } else {
            $this->emit('msg-error', $result['message']);
        }
           
          $this->emit("msg",$result);
        }
        
    // 5-8-2024    
    public function AjustarSaldosIniciales(){
    

    // obtenemos los saldos iniciales
     $saldo_iniciales = saldos_iniciales::where('tipo','cliente')
    ->where('concepto','Saldo inicial')
   // ->where('referencia_id',693)
    ->get();
    
    foreach($saldo_iniciales as $si){
    
    $saldos_inicial = saldos_iniciales::find($si->id);    
    //dd($saldos_inicial);
    $saldos_inicial->sucursal_id = $saldos_inicial->comercio_id;
    $saldos_inicial->save();

    $sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')
    ->where('sucursales.eliminado',0)
    ->where('casa_central_id', $saldos_inicial->comercio_id)->get();
    
    foreach($sucursales as $sucursal){
        
    $exist = saldos_iniciales::where('referencia_id',$saldos_inicial->referencia_id)->where('tipo','cliente')->where('concepto','Saldo inicial')->where('sucursal_id',$sucursal->sucursal_id)->where('comercio_id',$saldos_inicial->comercio_id)->first();
    
    if($exist == null){
        saldos_iniciales::create([
            'tipo' => 'cliente',
            'concepto' => 'Saldo inicial',
            'referencia_id' => $saldos_inicial->referencia_id,
            'comercio_id' => $saldos_inicial->comercio_id,
            'sucursal_id' => $sucursal->sucursal_id,
            'monto' => 0,
            'eliminado' => 0,
            'fecha' => $saldos_inicial->fecha
        ]);           
    }
        
    }
    
    }
    

    }
    // 5-8-2024
    public function BorrarDatos(){
        $valor = 104;
        $si = saldos_iniciales::where('id','>',$valor)->get();
        foreach($si as $s){
            $s->delete();
        }
        
        
    }
    
    public function GetSucursales($comercio_id){
    return sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')
    ->where('sucursales.eliminado',0)
    ->where('casa_central_id', $comercio_id)->get();
    }
    
    public function UpdateOrCreateSaldoInicial($cliente_id,$comercio_id,$sucursal_id,$saldo_inicial,$fecha_inicial){
        
        saldos_iniciales::updateOrCreate([
            'comercio_id' => $comercio_id,
            'sucursal_id' => $sucursal_id,
            'referencia_id' => $cliente_id,
    		'tipo' => 'cliente',
            'concepto' => 'Saldo inicial',
            'eliminado' => 0,
            ],[
            'monto' => $saldo_inicial,
            'fecha' => $fecha_inicial
    	    ]);
		}
	
}
