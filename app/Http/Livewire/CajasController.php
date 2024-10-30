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


class CajasController extends Component
{
      use WithPagination;
      use WithFileUploads;
      use CajasTrait;

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
        $this->GetConfiguracionCajas();
        //
        
        $this->usuarios = User::where('comercio_id',$comercio_id)->orWhere('id',$comercio_id)->get();
        }

        
        // 26-6-2024
        public function GetConfiguracionCajas(){
        $this->casa_central_id = Auth::user()->casa_central_user_id;
        $configuracion_cajas = configuracion_cajas::where('comercio_id',$this->casa_central_id)->first();
        if($configuracion_cajas != null){
        $this->configuracion_cantidad_cajas = $configuracion_cajas->configuracion_caja;
        $this->configuracion_caja = $configuracion_cajas->configuracion_caja;            
        } else {
        $this->configuracion_cantidad_cajas = 0;
        $this->configuracion_caja = 0;    
        }
        } 
        
        // 26-6-2024
        public function CerrarModalConfiguracionCaja(){
            $this->configuracion_ver = 0;
        }
        // 26-6-2024
        public function UpdateConfiguracionCaja(){
        
        $cajas = cajas::where('estado',0)->where('comercio_id',$this->casa_central_id)->where('eliminado',0)->first();
       // dd($cajas);
        if($cajas != null){
        $this->emit("msg-error","Debe cerrar todas las cajas de todas sus sucursales antes de modificar la configuracion");
        return;
        }
        
        configuracion_cajas::UpdateOrCreate(
            [
            'comercio_id' => $this->comercio_id
            ],
            [
            'configuracion_caja' => $this->configuracion_cantidad_cajas,
            'comercio_id' => $this->comercio_id
            ]
        );
        
        $this->emit('actualizacion','Configuracion actualizada');
        $this->GetConfiguracionCajas();
        $this->CerrarModalConfiguracionCaja(); 
        }
        
	protected $listeners =[
		'deleteCaja' => 'EliminarCaja',
		'DeleteIngresoRetiro' => 'EliminarIngresoRetiro'
	];

        // 26-6-2024
        public function AbrirModalConfiguracion(){
            $this->configuracion_ver = 1;
        }


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
        
        $this->GetCajaActiva();

    	$cajas = cajas::join('users','users.id','cajas.user_id')
        ->leftjoin('pagos_facturas','pagos_facturas.caja','cajas.id')
        ->select('cajas.user_id','cajas.nro_caja','cajas.faltante_caja','cajas.monto_inicial','cajas.monto_final','cajas.fecha_inicio','cajas.fecha_cierre','cajas.created_at','cajas.estado','cajas.id','users.name',pagos_facturas::raw('COUNT(monto) as count'),pagos_facturas::raw('SUM(monto) as total'),pagos_facturas::raw('SUM(pagos_facturas.recargo) as recargo'))
        ->where('cajas.comercio_id', 'like', $this->sucursal_id)
        ->where('cajas.eliminado',0)
        ->whereBetween('cajas.created_at', [$from, $to])
        ->groupBy('cajas.user_id','cajas.monto_inicial','cajas.faltante_caja','cajas.monto_final','cajas.nro_caja','cajas.fecha_inicio','cajas.fecha_cierre','cajas.created_at','cajas.estado','cajas.id','users.name')
        ->orderBy('cajas.nro_caja','desc')
    	->paginate($this->pagination);

        $this->comercio_id = $comercio_id;

    	return view('livewire.cajas.component', [
    	   'datos' => $cajas,
          'sucursales' => $this->sucursales,
          'comercio_id' => $this->comercio_id
    		])
    		->extends('layouts.theme-pos.app')
    		->section('content');

    	}
    
    // 26-6-2024
    public function GetCaja($caja_id)
    {
    $this->GetResumenCaja($caja_id);      
    $this->emit('tabs-show','Show modal');
    }
    //
    
    
    public function CerrarModalResumen() {
    $this->emit('tabs-hide','Hide modal');    
    }

    public function AbrirModal($user_id) {
      $this->usuario_caja = $user_id;
      $this->emit('modal-abrir-show','Show modal');
    }

    public function EditCaja($caja_id) {

      $this->emit('modal-editar-show','Show modal');

      $caja = cajas::find($caja_id);

      if($caja->monto_final == null )
      $this->caja_es_activa = 1;
      else
      $this->caja_es_activa = 0;

      $this->caja_id = $caja_id;
      
      $this->usuario_caja = $caja->user_id;
      $this->monto_inicial = $caja->monto_inicial;
      $this->monto_final = $caja->monto_final;
      $this->nro_caja_form = $caja->nro_caja;
      $this->fecha_inicial_form = $caja->fecha_inicio;
      $this->fecha_final_form = $caja->fecha_cierre;

    }

    // 26-6-2024
    public function CerrarModal($caja_id) {
      $this->caja_activa = $caja_id;
      $caja = cajas::find($caja_id);
      if($caja != null){$this->nro_caja_modal = $caja->nro_caja;} else {$this->nro_caja_modal =  null;} 
      $this->emit('modal-cerrar-show','Show modal');
    }

    public function ActualizarCaja($caja_id) {
    
    $this->monto_inicial = $this->convertirFormatoMoneda($this->monto_inicial);
    $this->monto_final = $this->convertirFormatoMoneda($this->monto_final);
    
    if (empty($this->monto_inicial) || $this->monto_inicial == "") {
        $this->emit("msg-error", "Debe ingresar el monto inicial");
        return;
    }
    
    if (empty($this->monto_final) || $this->monto_final == "") {
        $this->emit("msg-error", "Debe ingresar el monto final");
        return;
    }
    
    if (!is_numeric($this->monto_inicial)) {
        $this->emit("msg-error", "El monto inicial debe ser un número");
        return;
    }
    
    if (!is_numeric($this->monto_final)) {
        $this->emit("msg-error", "El monto final debe ser un número");
        return;
    }

      
      $cajas = cajas::join('users','users.id','cajas.comercio_id')
      ->select('cajas.id','cajas.nro_caja','cajas.fecha_cierre','cajas.fecha_inicio','cajas.user_id')
      ->where('cajas.comercio_id', 'like', $this->sucursal_id)
      ->where('cajas.eliminado',0)
      ->orderBy('cajas.nro_caja','desc')
      ->get();


      $respuesta = $this->ValidarFechasCajas($caja_id,$cajas,$this->fecha_inicial_form,$this->fecha_final_form);
      if($respuesta == true) {
          return;
      }
      
      $caja = cajas::find($caja_id);

      $this->caja = cajas::find($caja_id);

      $this->cajas_efectivo = pagos_facturas::join('metodo_pagos','metodo_pagos.id','pagos_facturas.metodo_pago')
      ->join('cajas','cajas.id','pagos_facturas.caja')
      ->select('cajas.estado','metodo_pagos.categoria as metodo_pago','cajas.monto_inicial','cajas.monto_final','pagos_facturas.monto','pagos_facturas.recargo')
      ->where('cajas.id', 'like', $caja_id)
      ->where('metodo_pagos.categoria',1)
      ->first();

      if($this->cajas_efectivo == null)
      $this->cerrar_ventas = 0;
      else
      $this->cerrar_ventas = $this->cajas_efectivo->sum('monto');

      $this->faltante_caja = 0;
      


      $caja->update([
        'monto_inicial' => $this->monto_inicial,
        'monto_final' => $this->monto_final,
        'faltante_caja' => $this->faltante_caja,
        'fecha_inicio' => $this->fecha_inicial_form,
        'fecha_cierre' => $this->fecha_final_form,
        'nro_caja' => $this->nro_caja_form,
        
      ]);


        $this->emit('modal-editar-hide','Show modal');
        
        $this->resetUI();
        
        $this->ActualizarNumeroDeCajas();
        
        $this->emit('actualizacion','Caja actualizada');



    }



    public function CerrarCaja($caja_id = null) {
    
    if($caja_id == null) {$caja_id = $this->caja_activa;}
    
    $rules  =[
		'monto_final' => 'required|not_negative|numeric'
	
		];

	$messages = [
		'monto_final.required' => 'El monto es requerido',
		'monto_final.numeric' => 'El monto debe contener solo numeros',
		'monto_final.not_negative' => 'El monto debe ser un numero positivo'
		];

	  $this->validate($rules, $messages);
	
	$this->monto_final = $this->convertirFormatoMoneda($this->monto_final);
	
      $caja = cajas::find($caja_id);

      $this->GetEfectivoCaja($caja_id);
      
      if($this->details_efectivo == null){
      $total = 0;
      $recargo = 0;
      $total_gasto = 0;
      $total_compras = 0;          
      } else {
      $total = $this->details_efectivo->sum('total');
      $recargo = $this->details_efectivo->sum('recargo');
      $total_gasto = $this->details_efectivo->sum('total_gasto');
      $total_compras = $this->details_efectivo->sum('total_compras');
      }
     
     
      $this->faltante_caja = $this->monto_final-($total+$recargo-$total_gasto-$total_compras+$this->total_ingresos_efectivo-$this->total_retiros_efectivo+$this->total_efectivo_inicial);

  		$caja->update([
  			'monto_final' => $this->monto_final,
        'faltante_caja' => $this->faltante_caja,
  			'estado' => '1',
        'fecha_cierre' => Carbon::now()
  		]);
  		
  	  $this->caja_activa = null;
  	  $this->monto_final = "";
      $this->emit('modal-cerrar-hide','Show modal');

    }


  public function resetUI() {
    $this->monto_final = '';
    $this->monto_inicial = '';

  }

/// ELIMINAR CAJA //
public function EliminarCaja($id) {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    if($this->sucursal_id != null) {
      $this->sucursal_id = $this->sucursal_id;
    } else {
      $this->sucursal_id = $comercio_id;
    }
    
    $caja_seleccionada = cajas::find($id);
    
    $caja_seleccionada->eliminado = 1;
    $caja_seleccionada->save();
    
    $cajas = cajas::where('cajas.comercio_id', $this->sucursal_id)->where('id','>',$caja_seleccionada->id)->where('eliminado', 0)->get();
    
    
    foreach($cajas as $c) {
        
        $caja = cajas::find($c->id);
        
        $caja_nro_caja = $caja->nro_caja - 1;
        
        $caja->update([
            'nro_caja' => $caja_nro_caja
            ]);
            
    }
    
    $this->emit('modal-caja-hide','Caja eliminada correctamente.');


}

function convertirFormatoMoneda($valor) {
    // Eliminar los puntos
    $valor = str_replace('.', '', $valor);
    // Reemplazar la coma con punto
    $valor = str_replace(',', '.', $valor);
    return $valor;
}

  public function AbrirCaja($user_id = null) {

    $rules  =[
		'monto_inicial' => 'required|not_negative|numeric'
	
		];

	$messages = [
		'monto_inicial.required' => 'El monto es requerido',
		'monto_inicial.numeric' => 'El monto debe contener solo numeros',
		'monto_inicial.not_negative' => 'El monto debe ser un numero positivo'
		];
    
	$this->validate($rules, $messages);
	
    $this->monto_inicial = $this->convertirFormatoMoneda($this->monto_inicial);
    
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    if($this->sucursal_id != null) {
      $this->sucursal_id = $this->sucursal_id;
    } else {
      $this->sucursal_id = $comercio_id;
    }

    $ultimo = cajas::where('cajas.comercio_id', 'like', $this->sucursal_id)->where('eliminado', 0)->latest('nro_caja')->first();

    if($ultimo != null) {
    $nro = $ultimo->nro_caja + 1;
    }
    else {
    $nro = 1;
    }

    DB::beginTransaction();

    try {

      $cajas = DB::table('cajas')->insertGetId([
        'user_id' => $this->usuario_caja,
        'comercio_id' => $this->sucursal_id,
        'nro_caja' => $nro,
        'monto_inicial' => $this->monto_inicial,
        'estado' => '0',
        'fecha_inicio' => Carbon::now(),
    
      ]);


      DB::commit();

    } catch (Exception $e) {
      DB::rollback();
      $this->emit('sale-error', $e->getMessage());

 
    }
    
    $this->monto_inicial = "";
    $this->emit('modal-abrir-hide','Show modal');
    
    $this->usuario_caja = null;
    $this->emit('cierre','Caja abierta correctamente.');
    
  }


  public function ElegirSucursal($sucursal_id) {
  	$this->sucursal_id = $sucursal_id;
  	$this->GetCajaActiva();
  }
  
  
  public function AgregarCajaAnteriorModal() {

    $this->emit('modal-caja-show','');


  }
  
    public function AbrirCajaAnterior() {
  
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    if($this->sucursal_id != null) {
      $this->sucursal_id = $this->sucursal_id;
    } else {
      $this->sucursal_id = $comercio_id;
    }

    $cajas = cajas::join('users','users.id','cajas.comercio_id')
    ->select('cajas.id','cajas.nro_caja','cajas.fecha_cierre','cajas.fecha_inicio','cajas.user_id')
    ->where('cajas.comercio_id', 'like', $this->sucursal_id)
    ->where('cajas.eliminado',0)
    ->orderBy('cajas.nro_caja','desc')
    ->get();

    /*    
    $respuesta = $this->ValidarFechasCajas($caja_id,$cajas,$this->fecha_inicial_ca,$this->fecha_final_ca);
      if($respuesta == true) {
          return;
      }
    */
    
    DB::beginTransaction();

    try {

      
      $cajas = DB::table('cajas')->insertGetId([
        'user_id' => Auth::user()->id,
        'comercio_id' => $this->sucursal_id,
        'nro_caja' => $this->numero_caja_ca,
        'monto_final' => $this->monto_final_ca,
        'monto_inicial' => $this->monto_inicial_ca,
        'estado' => '1',
        'fecha_inicio' => $this->fecha_inicial_ca,
        'fecha_cierre' => $this->fecha_final_ca

      ]);
      

      DB::commit();

    } catch (Exception $e) {
      DB::rollback();
      $this->emit('sale-error', $e->getMessage());
    }

    $this->numero_caja_ca = '';
    $this->monto_final_ca = '';
    $this->monto_inicial_ca = '';
    $this->fecha_inicial_ca = '';
    $this->fecha_final_ca = '';
  
    $this->ActualizarNumeroDeCajas();
    
    $this->emit('modal-caja-hide','Caja creada correctamente.');

  }



public function Filtrar(){
    $this->render();
}

// Actualizacion 25-9

public function ModalIngresoRetiro() {

    
    $this->detalle_ingresos_egresos = pagos_facturas::join('ingresos_retiros','ingresos_retiros.id','pagos_facturas.id_ingresos_retiros')
    ->select('pagos_facturas.*','ingresos_retiros.tipo','ingresos_retiros.descripcion')
    ->where('id_ingresos_retiros','<>',null)
    ->where('caja',$this->caja_id)
    ->get();
    
    
    $this->emit('modal-ingreso-retiro','');
    $this->emit('modal-resumen-ingreso-retiro-hide','');
}


public function CerrarModalIngresoRetiro(){
    $this->ResetIngresoRetiro();
    $this->emit('modal-ingreso-retiro-hide','');
    $this->ModalResumenIngresoRetiro($this->caja_id);   
}

public function ModalResumenIngresoRetiro($caja_id) {
   
    $this->caja_id = $caja_id;
         
    $this->detalle_ingresos_egresos = pagos_facturas::join('ingresos_retiros','ingresos_retiros.id','pagos_facturas.id_ingresos_retiros')
    ->join('bancos','bancos.id','pagos_facturas.banco_id')
    ->select('pagos_facturas.*','ingresos_retiros.tipo','bancos.nombre as nombre_banco','ingresos_retiros.descripcion')
    ->where('id_ingresos_retiros','<>',null)
    ->where('pagos_facturas.eliminado',0)
    ->where('caja',$caja_id)
    ->get();
    
    $this->emit('modal-resumen-ingreso-retiro','');
}

public function EditIngresoRetiro($id) {
    
    $record = pagos_facturas::join('ingresos_retiros','ingresos_retiros.id','pagos_facturas.id_ingresos_retiros')
    ->select('pagos_facturas.*','ingresos_retiros.tipo','ingresos_retiros.descripcion')
    ->where('id_ingresos_retiros','<>',null)
    ->where('pagos_facturas.id',$id)
    ->first();
    
   // dd($record);
    
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
public function StoreIngresoRetiro() {

  $caja = cajas::find($this->caja_id);
  if($caja->estado == 1){
      if($this->tipo_ingreso_retiro == "Ingreso") {
          $this->emit("msg-error","No puede ingresar dinero en una caja cerrada");
          return;
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
	
	
	if($caja->estado == 1 && $this->metodo_ingreso_retiro == 1){
	$nuevo_monto_final = $caja->monto_final +  $monto_ingreso_retiro;
	    $caja->update([
  		'monto_final' => $nuevo_monto_final,
  		]);
	}
	
	
    $this->ResetIngresoRetiro();
    
    $this->emit('modal-ingreso-retiro-hide',$this->tipo_ingreso_retiro.' registrado con exito.');
    $this->emit('msg','Registro guardado');
    $this->ModalResumenIngresoRetiro($this->caja_id);     

    
}



public function UpdateIngresoRetiro() {

  $caja = cajas::find($this->caja_id);
  if($caja->estado == 1){
      if($this->tipo_ingreso_retiro == "Ingreso") {
          $this->emit("msg-error","No puede actualizar un ingreso de dinero ya efectuado en una caja cerrada");
          return;
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
			 'eliminado' => 0
		 ]);
	
	$caja = cajas::find($this->caja_id);
	if($caja->estado == 1 && $this->metodo_ingreso_retiro == 1){
	$nuevo_monto_final = $caja->monto_final +  abs($diferencia_ingreso_retiro);
	    $caja->update([
  		'monto_final' => $nuevo_monto_final,
  		]);
	}
	
    $this->ResetIngresoRetiro();	 
    $this->emit('modal-ingreso-retiro-hide','');
    $this->ModalResumenIngresoRetiro($this->caja_id);     
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

public function ValidarFechasCajas($caja_id,$items,$fecha_inicial,$fecha_final) {

// Convertir la cadena de fecha a un objeto Carbon
$fecha_inicial = Carbon::parse($fecha_inicial);
$fecha_final = Carbon::parse($fecha_final);

// Obtener la fecha actual
$fecha_actual = Carbon::now();

// Verificar que alguna de las fechas elegidas no sea posterior a la fecha del dia de hoy
if ($fecha_final < $fecha_inicial) {
$this->emit("msg-error","La fecha inicial elegida es posterior a la fecha final elegida.");
return true;    
}

// Verificar que alguna de las fechas elegidas no sea posterior a la fecha del dia de hoy
if ($fecha_actual < $fecha_inicial) {
$this->emit("msg-error","La fecha inicial elegida es posterior a la fecha actual.");
return true;    
} 
if ($fecha_actual < $fecha_final) {
$this->emit("msg-error","La fecha final elegida es posterior a la fecha actual.");
return true;    
} 
    
    
// Iterar sobre cada elemento en $items
foreach ($items as $item) {

    if ($item['user_id'] == $this->usuario_caja) { 
 
    // Convertir las fechas del array a objetos Carbon
    $fecha_inicio = Carbon::parse($item['fecha_inicio']);
    $fecha_cierre = Carbon::parse($item['fecha_cierre']);

    
    // Verificar si $fecha_inicial está entre $fecha_inicio y $fecha_cierre
    if ($fecha_inicial->between($fecha_inicio, $fecha_cierre)) {
     if($item['id'] != $caja_id) {
       $this->emit("msg-error","La fecha de apertura elegida coincide con el rango de fechas de la caja ".$item['nro_caja']);
       return true;    
     }
    }
    
    // Verificar si $fecha_final está entre $fecha_inicio y $fecha_cierre
    if ($fecha_final->between($fecha_inicio, $fecha_cierre)) {
      //  dd($fecha_final,$fecha_inicio,$fecha_cierre);
     if($item['id'] != $caja_id) {
       $this->emit("msg-error","La fecha de cierre elegida coincide con el rango de fechas de la caja ".$item['nro_caja']);
       return true;    
     }
    }
    
    // Verifica si alguna otra caja quedo entre $fecha_inicio y $fecha_cierre
    
    if ($fecha_inicio->between($fecha_inicial, $fecha_final)) {
     if($item['id'] != $caja_id) {
       $this->emit("msg-error","El rango de fechas elegida coincide con el rango de fechas de la caja ".$item['nro_caja']);
       return true;    
     }
    }
    
    if ($fecha_cierre->between($fecha_inicial, $fecha_final)) {
     if($item['id'] != $caja_id) {
       $this->emit("msg-error","El rango de fechas elegida coincide con el rango de fechas de la caja  ".$item['nro_caja']);
       return true;    
     }
    }

    
}
}}


public function ActualizarNumeroDeCajas() {

// traigo las cajas acomodadas por fecha de cierre 

    $cajas = Cajas::join('users', 'users.id', 'cajas.comercio_id')
    ->select('cajas.id', 'cajas.nro_caja', 'cajas.fecha_cierre', 'cajas.fecha_inicio')
    ->where('cajas.comercio_id', 'like', $this->sucursal_id)
    ->where('cajas.eliminado', 0)
    ->orderBy('cajas.fecha_cierre', 'asc')
    ->get();

// Variable para llevar un seguimiento del nuevo número de caja
$nuevoNumeroCaja = 1;

foreach ($cajas as $caja) {
    // Actualizar el número de caja según tu lógica
    // En este ejemplo, simplemente asigno $nuevoNumeroCaja al campo nro_caja
    Cajas::where('id', $caja->id)->update(['nro_caja' => $nuevoNumeroCaja]);

    // Incrementar el nuevo número de caja para el siguiente registro
    $nuevoNumeroCaja++;
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
  
  
}
