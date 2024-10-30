<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;
use App\Models\gastos;
use App\Models\Sale;
use App\Models\wocommerce;
use App\Models\cajas;
use App\Models\pagos_facturas;
use App\Models\ingresos_retiros;
use App\Models\bancos;
use App\Models\beneficios;
use App\Models\sucursales;
use App\Models\User;
use App\Models\UsersController;
use App\Models\EtiquetaGastos;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
//use Codexshaper\WooCommerce\Facades\Product AS ProductWC;
use Automattic\WooCommerce\Client;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use App\Traits\CajasTrait;


use App\Models\configuracion_cajas; // 26-6-2024

use Illuminate\Support\Facades\Hash;


class IngresosRetirosController extends Component
{
      use WithPagination;
      use WithFileUploads;
      use CajasTrait;
        
        public $ultimas_cajas;
        
        public $nombre,$detalle_ingresos_egresos,$total_ventas_efectivo,$total_compras_efectivo,$total_gastos_efectivo,$casa_central_id,$total_ventas_totales,$descripcion_ingreso_retiro,$total_ventas_bancos,$recargo,$efectivo_disponible,$descripcion,$caja_es_activa, $estado_caja, $detalle_nro_caja, $numero_caja_ca, $monto_final_ca, $monto_inicial_ca, $fecha_inicial_ca, $fecha_final_ca, $price,$stock,$alerts,$categoryid,$search,$sucursal_id ,$image,$selected_id,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $metodos, $categoria, $monto, $dateFrom, $dateTo, $categoria_filtro, $suma_totales, $gastos_total, $etiquetas_gastos, $etiqueta, $nombre_etiqueta, $etiqueta_form, $etiquetas_filtro, $total_faltante, $cajas, $cajas_total, $etiquetas, $cajas_cajas_total,$details, $caja_id, $details_efectivo, $details_total, $details_bancos, $details_plataformas, $details_a_cobrar, $count_bancos, $count_a_cobrar, $count_efectivo, $count_plataformas, $total_efectivo_inicial, $total_efectivo_final , $details_efectivo_inicial, $total_bancos, $total_a_cobrar, $total_efectivo, $total_plataformas, $caja_activa, $monto_final, $monto_inicial, $ultimo;
    	private $pagination = 25;
    	
    	
    	
    	// Actualizacion 25-9
    	public $selected_ingreso_retiro,$ingresos_bancos, $total_ventas_plataformas,$retiros_bancos,$listado_bancos,$ingresos_efectivo,$retiros_efectivo, $metodo_ingreso_retiro, $tipo_ingreso_retiro,$caja,$monto_ingreso_retiro,$ingresos_resumen,$retiros_resumen,$total_ingresos_efectivo,$total_retiros_efectivo;
        
        // Actualizacion 26-6-2024
        public $configuracion_ver,$configuracion_cantidad_cajas,$configuracion_caja,$nro_caja_modal,$cajas_inactivas_comercio,$cajas_inactivas_usuario,$usuario_caja,$usuarios;
        
        public $user_id;
        public $cajas_activas_comercio = [];
        public $cajas_activas_usuario = [];
        
    	public function paginationView()
    	{
    		return 'vendor.livewire.bootstrap';
    	}

    	public function mount()
    	{

        // Actualizacion 25-9
        $this->selected_ingreso_retiro = 0;
        $this->total_ingresos_efectivo = 0;
        $this->total_retiros_efectivo = 0;
        $this->retiros_efectivo = [];
        $this->ingresos_efectivo = [];
        $this->tipo_ingreso_retiro = "Elegir";
        $this->metodo_ingreso_retiro = "Elegir";
        
        $this->password_ingreso_retiro = null;
        $this->descripcion_ingreso_retiro =  "";
        
        $this->compras_bancos = [];
        $this->gastos_bancos = [];
        $this->ingresos_bancos = [];
        $this->retiros_bancos = [];
        $this->totales_bancos = [];
        
        $this->compras_plataformas = [];
        $this->gastos_plataformas = [];
        $this->ingresos_plataformas = [];
        $this->retiros_plataformas = [];
        $this->totales_plataformas = [];
        
        $this->listado_bancos = [];
        $this->listado_plataformas = [];
        //
        $this->details_bancos =[];
        $this->details_efectivo = [];
        $this->details_efectivo_inicial = [];
        $this->details_a_cobrar = [];
        $this->details_total = [];
        $this->detalle_nro_caja = [];
        $this->details_plataformas = [];
    	$this->pageTitle = 'Listado';
    	$this->componentName = 'Cajas';
    	$this->categoria = 'Elegir';
        $this->categoria_filtro = '';
    	$this->almacen = 'Elegir';
        $this->etiqueta_form = 'Sin etiqueta';
        $this->etiquetas_filtro = '';
    	$this->stock_descubierto = 'Elegir';
        $this->dateFrom = '01-01-2000';
        $this->dateTo = Carbon::now()->format('d-m-Y');
        $this->detalle_ingresos_egresos = [];

        
        // Actualizacion 26-6-2024
        
        if(Auth::user()->comercio_id != 1)
        $comercio_id = Auth::user()->comercio_id;
        else
        $comercio_id = Auth::user()->id;
        $this->comercio_id = $comercio_id;
        $this->casa_central_id = Auth::user()->casa_central_user_id;
        
        $this->configuracion_ver = 0;
        //
        
        $this->usuarios = User::where('comercio_id',$comercio_id)->orWhere('id',$comercio_id)->get();
        
        $this->caja_id = null;
        $this->caja = null;
        $this->caja_seleccionada = null;

        }

        
	protected $listeners =[
		'deleteCaja' => 'EliminarCaja',
		'DeleteIngresoRetiro' => 'EliminarIngresoRetiro'
	];

    protected function redirectLogin()
    {
        return \Redirect::to("login");
    }

    public function GetCajaActiva(){
        
        $user_id = Auth::user()->id; // 26-6-2024     
        
        // CAJAS POR SUCURSALES   
        if($this->configuracion_caja == 0){
        $this->cajas_activas_comercio = cajas::where('estado',0)->where('eliminado', 0)->where('comercio_id', $this->sucursal_id)->get(); // 26-6-2024
        }
        
        // CAJAS POR USUARIO
        if($this->configuracion_caja == 1){ 
        
        if(Auth::user()->sucursal != 1 ){ // si es casa central
        $this->cajas_activas_comercio = cajas::where('estado',0)->where('eliminado', 0)->where('comercio_id', $this->sucursal_id)->get(); // 26-6-2024  
        $this->cajas_activas_usuario = []; // 26-6-2024
   
    // Obtener los usuarios del comercio excluyendo los clientes
    $usuarios_del_comercio = User::where('profile', '<>', 'Cliente')
                                 ->where(function($query) {
                                     $query->where('comercio_id', $this->sucursal_id)
                                           ->orWhere('id', $this->sucursal_id);
                                 })
                                 ->get();

    // Obtener el usuario actual
    $usuarios_del_usuario = $usuarios_del_comercio->filter(function($user) {
        return $user->id == Auth::user()->id;
    });

    //dd($this->sucursal_id);
    
    // Inicializar la variable para cajas inactivas del comercio
    $cajas_inactivas_comercio = [];

    // Recorrer cada usuario del comercio
    foreach ($usuarios_del_comercio as $uc) {
        // Verificar si el usuario no tiene ninguna caja con estado 0
        $caja = Cajas::where('estado', 0)
                     ->where('eliminado', 0)
                     ->where('user_id', $uc->id)
                     ->first();

        // Si no existe ninguna caja con estado 0, agregar el usuario a la lista
        if (!$caja) {
            $cajas_inactivas_comercio[] = ['id' => $uc->id, 'nombre_comercio' => $uc->name];
        }
    }

    // Inicializar la variable para cajas inactivas del usuario
    $cajas_inactivas_usuario = [];

    // Recorrer cada usuario del usuario (autenticado)
    foreach ($usuarios_del_usuario as $uu) {
        // Verificar si el usuario no tiene ninguna caja con estado 0
        $caja = Cajas::where('estado', 0)
                     ->where('eliminado', 0)
                     ->where('user_id', $uu->id)
                     ->first();

        // Si no existe ninguna caja con estado 0, agregar el usuario a la lista
        if (!$caja) {
            $cajas_inactivas_usuario[] = ['id' => $uu->id, 'nombre_comercio' => $uu->name];
        }
    }

    // Asignar la lista de cajas inactivas a la propiedad del componente
    $this->cajas_inactivas_comercio = $cajas_inactivas_comercio;
    $this->cajas_inactivas_usuario = $cajas_inactivas_usuario; // 26-6-2024

        } else {
        
        // si es sucursal
        $this->cajas_activas_comercio = cajas::where('estado',0)->where('eliminado', 0)->where('comercio_id', $user_id)->get(); // 26-6-2024  
        $this->cajas_activas_usuario = cajas::where('estado',0)->where('eliminado', 0)->where('user_id', $user_id)->get(); // 26-6-2024  
 
        $this->user_id = $user_id;         
        // Obtener los usuarios del comercio excluyendo los clientes
        $usuarios_del_comercio = User::where('profile', '<>', 'Cliente')
                                     ->where(function($query) {
                                         $query->where('comercio_id', $this->user_id)
                                               ->orWhere('id', $this->user_id);
                                     })
                                     ->get();
    
       // dd($usuarios_del_comercio);
        
        // Obtener el usuario actual
        $usuarios_del_usuario = $usuarios_del_comercio->filter(function($user) {
            return $user->id == Auth::user()->id;
        });
        
        // Inicializar la variable para cajas inactivas del comercio
        $cajas_inactivas_comercio = [];
    
        // Recorrer cada usuario del comercio
        foreach ($usuarios_del_comercio as $uc) {
            // Verificar si el usuario no tiene ninguna caja con estado 0
            $caja = Cajas::where('estado', 0)
                         ->where('eliminado', 0)
                         ->where('user_id', $uc->id)
                         ->first();
    
            // Si no existe ninguna caja con estado 0, agregar el usuario a la lista
            if (!$caja) {
                $cajas_inactivas_comercio[] = ['id' => $uc->id, 'nombre_comercio' => $uc->name];
            }
        }
        
        //dd($cajas_inactivas_comercio);
        
        // Inicializar la variable para cajas inactivas del usuario
        $cajas_inactivas_usuario = [];
    
        // Recorrer cada usuario del usuario (autenticado)
        foreach ($usuarios_del_usuario as $uu) {
            // Verificar si el usuario no tiene ninguna caja con estado 0
            $caja = Cajas::where('estado', 0)
                         ->where('eliminado', 0)
                         ->where('user_id', $uu->id)
                         ->first();
    
            // Si no existe ninguna caja con estado 0, agregar el usuario a la lista
            if (!$caja) {
                $cajas_inactivas_usuario[] = ['id' => $uu->id, 'nombre_comercio' => $uu->name];
            }
        }
    
        // Asignar la lista de cajas inactivas a la propiedad del componente
        $this->cajas_inactivas_comercio = $cajas_inactivas_comercio;
        $this->cajas_inactivas_usuario = $cajas_inactivas_usuario; // 26-6-2024
    

        }
        
            
        }
        
        
    }    
    
    
	public function render()
	{
        if (!Auth::check()) {
            // Redirigir al inicio de sesi贸n y retornar una vista vac铆a
            $this->redirectLogin();
            return view('auth.login');
        }
 
        $user_id = Auth::user()->id; // 26-6-2024
        
        if(Auth::user()->comercio_id != 1)
        $comercio_id = Auth::user()->comercio_id;
        else
        $comercio_id = Auth::user()->id;
        
        $this->ultimas_cajas = cajas::where('comercio_id', $comercio_id)->where('eliminado',0)->orderby('created_at','desc')->get();
        
        $this->listado_bancos = $this->getBancos($comercio_id);
        
        $bancos_ids = $this->listado_bancos->pluck('id')->toArray();
        
        if($this->sucursal_id != null) {
          $this->sucursal_id = $this->sucursal_id;
        } else {
          $this->sucursal_id = $comercio_id;
        }

        $this->casa_central_id = Auth::user()->casa_central_user_id;
        
        $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')->where('sucursales.eliminado',0)->where('casa_central_id', $comercio_id)->get();

        if($this->dateFrom !== '' || $this->dateTo !== '')
        {
          $from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
          $to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';

        }
        
        $ingresos_retiros = pagos_facturas::select('ingresos_retiros.descripcion','pagos_facturas.id','bancos.nombre as metodo_pago','pagos_facturas.banco_id','pagos_facturas.monto_ingreso_retiro as monto','pagos_facturas.caja','pagos_facturas.created_at')
        ->join('ingresos_retiros','ingresos_retiros.id','pagos_facturas.id_ingresos_retiros')
        ->join('bancos','bancos.id','pagos_facturas.banco_id')
        ->whereIn('pagos_facturas.banco_id', $bancos_ids)
        ->where('pagos_facturas.eliminado', 0)
        ->orderBy('pagos_facturas.id','desc')
        ->paginate($this->pagination);
        
        
        $this->comercio_id = $comercio_id;
        
    	return view('livewire.ingresos-retiros.component', [
    	  'datos' => $ingresos_retiros,
          'sucursales' => $this->sucursales,
          'comercio_id' => $this->comercio_id
    		])
    		->extends('layouts.theme-pos.app')
    		->section('content');

    	}

    public function CerrarModalResumen() {
    $this->emit('tabs-hide','Hide modal');    
    }

    public function AbrirModal($user_id) {
      $this->usuario_caja = $user_id;
      $this->emit('modal-abrir-show','Show modal');
    }

  public function resetUI() {
    $this->monto_final = '';
    $this->monto_inicial = '';

  }

function convertirFormatoMoneda($valor) {
    // Eliminar los puntos
    $valor = str_replace('.', '', $valor);
    // Reemplazar la coma con punto
    $valor = str_replace(',', '.', $valor);
    return $valor;
}

  public function ElegirSucursal($sucursal_id) {
  	$this->sucursal_id = $sucursal_id;
  	$this->GetCajaActiva();
  }
  

public function Filtrar(){
    $this->render();
}

public function ElegirCaja($caja_id){
    $this->caja_id = $caja_id;
    $this->caja = $caja_id;
   
    $this->caja_seleccionada = cajas::find( $this->caja_id);    
}

public function ModalIngresoRetiro() {
    $this->ResetIngresoRetiro();
    
    $this->caja_id = null;
    $this->caja = null;
    $this->caja_seleccionada = null;
        
    
    $this->emit('modal-ingreso-retiro','');
    $this->emit('modal-resumen-ingreso-retiro-hide','');
}


public function CerrarModalIngresoRetiro(){
    $this->ResetIngresoRetiro();
    $this->emit('modal-ingreso-retiro-hide','');
    $this->ModalResumenIngresoRetiro($this->caja_id);   
}


public function StoreIngresoRetiro() {

  $caja = cajas::find($this->caja_id);
  if($caja){
  if($caja->estado == 1){
      if($this->tipo_ingreso_retiro == "Ingreso") {
          $this->emit("msg-error","No puede ingresar dinero en una caja cerrada");
          return;
      }
  }
  }
  
   if($this->tipo_ingreso_retiro == "Retiro") {
   
   if($this->password_ingreso_retiro == null || $this->password_ingreso_retiro == "")    {
    $this->emit("msg-error","Debe ingresar el contraseña cuando es un retiro");
    return;
   } else {
   $response = $this->verificarContraseña();    
   if($response == false) {
    $this->emit("msg-error","La contraseña ingresada es invalida");
    return;
   }
   }
   
   }
   
   if($this->tipo_ingreso_retiro == "Retiro" && $this->metodo_ingreso_retiro == 1) {
   $efectivo_disponible = $this->CalcularEfectivoDisponible($this->caja_id,"store",0);       
   if($efectivo_disponible < $this->monto_ingreso_retiro) {
       $this->emit("msg-error","El dinero disponible para retirar es: $".$efectivo_disponible);
       return;
   }
   }
   
    // Eliminar cualquier carácter no numérico, excepto el punto decimal
//    $valorNumerico = preg_replace("/[^0-9.]/", "", $this->monto_ingreso_retiro);

//   dd(is_numeric($this->monto_ingreso_retiro),$this->monto_ingreso_retiro,$valorNumerico);

    
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    if($this->sucursal_id != null) {
      $this->sucursal_id = $this->sucursal_id;
    } else {
      $this->sucursal_id = $comercio_id;
    }

    $rules  =[
		'monto_ingreso_retiro' => 'required|not_negative|numeric',
		'tipo_ingreso_retiro' => 'not_in:Elegir',
		'metodo_ingreso_retiro' => 'not_in:Elegir',
	
		];

	$messages = [
		'monto_ingreso_retiro.required' => 'El monto es requerido',
		'monto_ingreso_retiro.numeric' => 'El monto debe contener solo numeros',
		'monto_ingreso_retiro.not_negative' => 'El monto debe ser un numero positivo',
		'tipo_ingreso_retiro.not_in' => 'Elija el tipo de movimiento',
		'metodo_ingreso_retiro.not_in' => 'Elija el tipo de movimiento',
		];

	$this->validate($rules, $messages);
	
	// Lo ponemos para mostrar en negativo solo para ingresos_retiros
	
	if($this->tipo_ingreso_retiro == "Retiro") {$monto_ingreso_retiro = $this->monto_ingreso_retiro * -1;} else {$monto_ingreso_retiro = $this->monto_ingreso_retiro;}
	$bancos = bancos::find($this->metodo_ingreso_retiro);
	
//	dd($monto_ingreso_retiro);
	
	$ingresos_retiros = ingresos_retiros::create([
	      'monto' => $monto_ingreso_retiro ,
	      'tipo' => $this->tipo_ingreso_retiro,
	      'categoria' => $bancos->tipo,
	      'banco_id' => $this->metodo_ingreso_retiro,
	      'descripcion' => $this->descripcion_ingreso_retiro,
	      'comercio_id' => $this->sucursal_id,
	       ]);
	       
	   //    dd($ingresos_retiros);
	        
		$pago_factura = pagos_facturas::create([
			 'monto_ingreso_retiro' => $monto_ingreso_retiro,
			 'caja' => $this->caja_id,
			 'banco_id' => $this->metodo_ingreso_retiro,
			 'id_ingresos_retiros' => $ingresos_retiros->id,
			 'comercio_id' => $this->sucursal_id,
			 'created_at' => Carbon::now(),
			 'id_factura' => null,
			 'eliminado' => 0,
			 'estado_pago' => 1
		 ]);
		 
	//	 dd($pago_factura);
	
	
	if($caja && $caja->estado == 1 && $this->metodo_ingreso_retiro == 1){
	$nuevo_monto_final = $caja->monto_final +  $monto_ingreso_retiro;
	    $caja->update([
  		'monto_final' => $nuevo_monto_final,
  		]);
	}
	
	
    $this->ResetIngresoRetiro();
    
    $this->emit('modal-ingreso-retiro-hide',$this->tipo_ingreso_retiro.' registrado con exito.');
    $this->emit('msg','Registro guardado');

}

public function UpdateIngresoRetiro() {

  $caja = cajas::find($this->caja_id);
  if($caja){
  if($caja->estado == 1){
      if($this->tipo_ingreso_retiro == "Ingreso") {
          $this->emit("msg-error","No puede actualizar un ingreso de dinero ya efectuado en una caja cerrada");
          return;
      }
  }
  }

   if($this->tipo_ingreso_retiro == "Retiro") {
   
   if($this->password_ingreso_retiro == null || $this->password_ingreso_retiro == "")    {
    $this->emit("msg-error","Debe ingresar el contraseña cuando es un retiro");
    return;
   } else {
   $response = $this->verificarContraseña();    
   if($response == false) {
    $this->emit("msg-error","La contraseña ingresada es invalida");
    return;
   }
   }
   
   }
   
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    if($this->sucursal_id != null) {
      $this->sucursal_id = $this->sucursal_id;
    } else {
      $this->sucursal_id = $comercio_id;
    }

    $rules  =[
		'monto_ingreso_retiro' => 'required|not_negative|numeric',
		'tipo_ingreso_retiro' => 'not_in:Elegir',
		'metodo_ingreso_retiro' => 'not_in:Elegir',
	
		];

	$messages = [
		'monto_ingreso_retiro.required' => 'El monto es requerido',
		'monto_ingreso_retiro.numeric' => 'El monto debe contener solo numeros',
		'monto_ingreso_retiro.not_negative' => 'El monto debe ser un numero positivo',
		'tipo_ingreso_retiro.not_in' => 'Elija el tipo de movimiento',
		'metodo_ingreso_retiro.not_in' => 'Elija el tipo de movimiento',
		];

	$this->validate($rules, $messages);
	    
	
	$pf = pagos_facturas::find($this->selected_ingreso_retiro);
    $ir = ingresos_retiros::find($pf->id_ingresos_retiros);

    if($this->tipo_ingreso_retiro == "Retiro" && $this->metodo_ingreso_retiro == 1) {
    $efectivo_disponible = $this->CalcularEfectivoDisponible($this->caja_id,"update",$pf->monto_ingreso_retiro);  
    if($efectivo_disponible < $this->monto_ingreso_retiro) {
       $this->emit("msg-error","El dinero disponible para retirar es: $".$efectivo_disponible);
       return;
    }
    }

    
    $bancos = bancos::find($this->metodo_ingreso_retiro);
    
	if($this->tipo_ingreso_retiro == "Retiro") {$monto_ingreso_retiro = $this->monto_ingreso_retiro * -1;} else {$monto_ingreso_retiro = $this->monto_ingreso_retiro;}
	  
	  $diferencia_ingreso_retiro = $ir->monto - $monto_ingreso_retiro;
	  
	  $ir->update([
	      'monto' => $monto_ingreso_retiro ,
	      'tipo' => $this->tipo_ingreso_retiro,
	      'categoria' => $bancos->tipo,
	      'banco_id' => $this->metodo_ingreso_retiro,
	      'descripcion' => $this->descripcion_ingreso_retiro,
	       ]);
	  
		$pf->update([
			 'monto_ingreso_retiro' => $monto_ingreso_retiro,
			 'banco_id' => $this->metodo_ingreso_retiro,
			 'comercio_id' => $this->sucursal_id,
			 'created_at' => Carbon::now(),
			 'id_factura' => null,
			 'eliminado' => 0,
			 'caja' => $this->caja_id
		 ]);
	
	$caja = cajas::find($this->caja_id);
	if($caja && $caja->estado == 1 && $this->metodo_ingreso_retiro == 1){
	$nuevo_monto_final = $caja->monto_final +  abs($diferencia_ingreso_retiro);
	    $caja->update([
  		'monto_final' => $nuevo_monto_final,
  		]);
	}
	
    $this->ResetIngresoRetiro();	 
    $this->emit('modal-ingreso-retiro-hide','');
    $this->emit('msg','Registro actualizado');
    
    
}

public function EliminarIngresoRetiro($id) {
 
   // dd($this->caja);
    
	$pf = pagos_facturas::find($id);
	//dd($pf);
    $ir = ingresos_retiros::find($pf->id_ingresos_retiros);

    
    $pf->update([
        'eliminado' => 1
        ]);
        
        
    $ir->update([
        'eliminado' => 1
        ]);
  
    $this->emit('modal-resumen-ingreso-retiro-hide','');
        
    $this->emit("msg","Registro eliminado con exito");

    
    $this->ModalResumenIngresoRetiro($this->caja_id);     
   
    $this->emit('modal-resumen-ingreso-retiro','');
   
}



public function EditIngresoRetiro($id) {
    
    $record = pagos_facturas::join('ingresos_retiros','ingresos_retiros.id','pagos_facturas.id_ingresos_retiros','pagos_facturas.caja')
    ->select('pagos_facturas.*','ingresos_retiros.tipo','ingresos_retiros.descripcion')
    ->where('id_ingresos_retiros','<>',null)
    ->where('pagos_facturas.id',$id)
    ->first();
    
    $this->caja_id = $record->caja;
    $this->caja = $record->caja;
   
    $this->caja_seleccionada = cajas::find( $this->caja_id);
    
    $this->descripcion_ingreso_retiro = $record->descripcion;
    $this->tipo_ingreso_retiro = $record->tipo;
    $this->monto_ingreso_retiro = abs($record->monto_ingreso_retiro);
    $this->selected_ingreso_retiro =  $record->id;
    $this->metodo_ingreso_retiro =  $record->banco_id;
    
    $this->emit('modal-resumen-ingreso-retiro-hide','');
    $this->emit('modal-ingreso-retiro','');
   
}

public function ResetIngresoRetiro() {
    $this->tipo_ingreso_retiro = "Elegir";
    $this->monto_ingreso_retiro = "";
    $this->selected_ingreso_retiro = 0; 
    $this->password_ingreso_retiro = null;
    $this->descripcion_ingreso_retiro =  "";
}


public function CerrarModalResumenIngresoRetiro(){
    $this->emit('modal-resumen-ingreso-retiro-hide','');
    $this->ResetIngresoRetiro();
}


public function CalcularEfectivoDisponible($caja_id,$tipo_operacion,$monto_anterior) {
$caja = cajas::find($caja_id);
if($caja->estado == 1){
$efectivo_disponible = $caja->monto_final;

if($tipo_operacion == "store") {
return $efectivo_disponible;
}

if($tipo_operacion == "update") {
$efectivo_disponible = abs($monto_anterior) + $efectivo_disponible;
return $efectivo_disponible;
}    
    
} else {
    
$this->GetEfectivoCaja($caja_id);      

//dd($caja_id,$tipo_operacion,$monto_anterior);

if($tipo_operacion == "store") {
$efectivo_disponible = $this->total_efectivo;
return $efectivo_disponible;
}
if($tipo_operacion == "update") {
$efectivo_disponible = $monto_anterior + $this->total_efectivo;
return $efectivo_disponible;
}
}


}


public function verificarContraseña()
{
    $usuario = Auth::user(); // Obtén el usuario autenticado

    // Verifica la contraseña proporcionada en la solicitud con la contraseña almacenada en la base de datos
    if (Hash::check($this->password_ingreso_retiro, $usuario->password)) {
        // La contraseña es correcta, realiza la acción que necesitas aquí
        return true;
        } else {
        // La contraseña no es correcta
        return false;
        }
    }
    
  
  public function SetearIngresosRetiros(){
  $ingresos_retiros = ingresos_retiros::where('tipo','Retiro')->get();    
  
  foreach($ingresos_retiros as $i){
  $pf = pagos_facturas::where('id_ingresos_retiros',$i->id)->first();
  
  $pf->monto_ingreso_retiro = $i->monto;
  $pf->save();
  
  }
  
 $this->emit('actualizacion','Actualizado');  
  }  
 
  public function getBancos($comercio_id){
    return bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
    ->where('bancos_muestra_sucursales.muestra', 1)
    ->where('bancos_muestra_sucursales.sucursal_id', $comercio_id)
    ->select('bancos.*')
    ->orderBy('bancos.nombre','asc')
    ->get();
    
   
  } 
  
}
