<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\gastos;
use App\Models\gastos_categoria;
use App\Models\bancos;
use App\Models\forma_pagos;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\etiquetas_gastos_relacion;
use App\Models\sucursales;
use App\Models\proveedores;
use App\Models\cajas;
use App\Models\beneficios;
use App\Models\EtiquetaGastos;
use App\Models\pagos_facturas;
use App\Models\metodo_pago;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;


// Trait
use App\Traits\BancosTrait;
use App\Traits\EtiquetasTrait;


class GastosController extends Component
{

  use WithPagination;
  use WithFileUploads;
  
  use BancosTrait;
  use EtiquetasTrait;
    
    public $nombre,$recargo,$descripcion,$etiqueta_gastos,$forma_pago_json,$forma_pago,$caja_abierta,$forma_pego_elegida,$etiqueta_gastos_json,$agregar,$id_check,$price,$stock,$caja, $monto_inicial , $fecha_ap, $fecha_gasto,$sucursal_id, $alerts,$categoryid,$ultimas_cajas,$caja_seleccionada, $search,$image,$selected_id,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $metodos, $categoria, $monto, $dateFrom, $dateTo, $categorias_gastos, $categoria_filtro, $suma_totales, $gastos_total, $fecha_pedido, $lista_cajas_dia,$metodo_pago_filtro ,$forma_pago_filtro, $metodo_pago_elegido, $gastos_categoria;
	private $pagination = 25;
	public $accion_lote;
	public $procedencia;
	
	public $proveedor;

    public $metodo_pago_ap_div,$iva_total_dividido,$iva_pago_dividido,$a_cobrar;
    
    public $monto_ap_div = [];
    
    public $monto_con_iva,$iva,$deuda;
    
    public $metodo_pago = [];
    
    public $proveedor_filtro;

	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}

    public $mostrarFiltros = false;

    public function MostrarFiltro()
    {
        $this->mostrarFiltros = !$this->mostrarFiltros;
    }
    

    public function FechaElegida($startDate, $endDate)
    {
      // Manejar las fechas seleccionadas aquí
      $this->dateFrom  = $startDate;
      $this->dateTo = $endDate;
      
      //dd($startDate,$endDate);
    }
    
	public function mount()
	{
	$this->proveedor_filtro = "all";
	$this->proveedor = 1;
	$this->metodos_pago_dividido = [];
	$this->origen = "gastos";
	$this->etiquetas_seleccionadas = [];
	$this->procedencia = 0;
	$this->estado_filtro = 0;
	$this->accion_lote = 'Elegir';
	
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    
    $this->comercio_id = $comercio_id;

	
    $this->caja = null;

    $this->lista_cajas_dia = [];
    $this->categorias_gastos = [];
    $gastos_categoria = [];
	$this->pageTitle = 'Listado';
	$this->componentName = 'Gastos';
	$this->categoria = 'Elegir';
    $this->metodo_pago_elegido = 'Elegir';
    $this->categoria_filtro = '';
	$this->almacen = 'Elegir';
    $this->etiqueta_form = 1;
    $this->etiquetas_filtro = '';
	$this->stock_descubierto = 'Elegir';
    $this->dateFrom = '01-01-2000';
    $this->dateTo = Carbon::now()->format('d-m-Y');
    
    $this->fecha_gasto = Carbon::now();


    $this->metodo_pago = $this->GetBancosTrait($comercio_id);
	}





	public function render()
	{

    if($this->dateFrom !== '' || $this->dateTo !== '')
    {
      $from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
      $to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';

    }

    
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		
		$this->comercio_id = $comercio_id;
		
		$this->caja_abierta = cajas::where('estado',0)->where('eliminado',0)->where('comercio_id',$comercio_id)->max('id');

		
		$this->tipo_usuario = User::find(Auth::user()->id);

        if($this->tipo_usuario->sucursal != 1) {
        $this->casa_central_id = $comercio_id;
        } else {

        $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
        $this->casa_central_id = $this->casa_central->casa_central_id;
        }


      $this->ultimas_cajas = cajas::where('comercio_id', $comercio_id)->where('eliminado',0)->orderby('created_at','desc')->limit(5)->get();

      $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')->where('casa_central_id', $comercio_id)->get();
    

    $gastos = gastos::join('gastos_categorias','gastos_categorias.id','gastos.categoria')
    ->join('proveedores','gastos.proveedor_id','proveedores.id')
    ->select('gastos.*','gastos.etiquetas as nombre_etiqueta','gastos_categorias.nombre as nombre_categoria','proveedores.nombre as nombre_proveedor')
    ->where('gastos.comercio_id', 'like', $comercio_id);

    if(strlen($this->search) > 0) {

		$gastos = $gastos->where('gastos.nombre', 'like', '%' . $this->search . '%');

    }
    // Filtrar por método de pago
    if($this->metodo_pago_filtro) {
        $gastos = $gastos->whereRaw("FIND_IN_SET(?, cuenta)", [$this->metodo_pago_filtro]);
    }

    if($this->categoria_filtro) {

      $gastos = $gastos->where('gastos.categoria',$this->categoria_filtro);

    }

    if($this->etiquetas_filtro) {

      $gastos = $gastos->whereIn('gastos.id',$this->etiquetas_filtro);

    }
    
    
    if($this->proveedor_filtro != "all") {

      $gastos = $gastos->where('gastos.proveedor_id',$this->proveedor_filtro);

    } 
    
    /*
    if($this->forma_pago_filtro) {

      $gastos = $gastos->where('gastos.forma_pago',$this->forma_pago_filtro);

    } 
    */

    $gastos = $gastos->whereBetween('gastos.created_at', [$from, $to])
		->orderBy('gastos.created_at','desc')
		->where('gastos.eliminado',$this->estado_filtro)
		->paginate($this->pagination);


    // $metodo_pagos = bancos::where('bancos.comercio_id', 'like', $this->casa_central_id)->get();

  /*  $metodo_pagos = bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
    ->where('bancos_muestra_sucursales.muestra', 1)
    ->where('bancos_muestra_sucursales.sucursal_id', $comercio_id)
    ->orderBy('bancos.nombre','asc')
    ->get();
    
    */
    
    
    //dd($metodo_pagos);
    
    $this->forma_pago =  $this->GetFormaPagoTrait($comercio_id);
    
    $this->forma_pago_json = $this->GetFormaPagoTraitJson($comercio_id);
    
    $this->gastos_total = $gastos->sum('monto');

    $this->etiqueta = $this->GetEtiquetas($comercio_id,"gastos");
    
    $this->etiqueta_json = $this->GetEtiquetasJson($comercio_id,"gastos");
    
    $this->gastos_categoria = gastos_categoria::where('comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();
    
    $this->gastos_proveedor = proveedores::where('comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();


		return view('livewire.gastos.component', [
		'data' => $gastos,
        'etiquetas' => $this->etiqueta,
    //    'metodo_pago' =>   $this->metodo_pago,
        'gastos_categoria' => $this->gastos_categoria,
        'gastos_proveedor' => $this->gastos_proveedor
		])
		->extends('layouts.theme-pos.app')
		->section('content');

	}

//////////////////////   ETIQUETAS   /////////////////////////////////

  public function CreateEtiqueta()
  {
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $etiqueta = $this->CreateEtiquetas($this->nombre_etiqueta,$comercio_id,"gastos");
    
    $this->etiqueta = $this->GetEtiquetas($comercio_id,"gastos");
    
    if($this->procedencia == 1) {
    $this->emit('tabs-hide','Show modal');
    $this->etiqueta_form = $etiqueta->id;
    $this->nombre_etiqueta = '';
    $this->procedencia = 0;
    } else {
    $this->GetEtiqueta();
    }
    
    $this->nombre_etiqueta = '';

  }

public function ModalFormaPago() {
    
}

public function DismissEtiqueta(){
 //   dd('hola');
  $this->emit('tabs-hide','Show modal');  
  $this->etiqueta_form = 1;
}

public function DismissCategoria() {
  $this->emit('categorias-hide','Show modal');  
  $this->categoria = 1;
}

public function Filtrar() {
    $this->render();
}



public function AbrirModal() {
  $this->emit('modal-show','Show modal');
}


    public function updateEtiqueta($id_etiqueta, $nombre_etiqueta)
    {

      $this->updateEtiquetas($id_etiqueta,$nombre_etiqueta,"gastos");
 
      $this->GetEtiqueta();
      
      // debemos actualizar el nombre de las etiquetas con todos los gastos con la $id_etiqueta
      
      
      
      $this->emit('msg','Etiqueta Actualizada');

    }




public function GetEtiqueta()
{

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $this->etiqueta = $this->GetEtiquetas($comercio_id,"gastos");

  $this->emit('tabs-show','Show modal');
}

public function ModalEtiqueta($value) {
    
    if($value == "AGREGAR") {
    $this->procedencia = 1;
    $this->GetEtiqueta();
    }
    
}

public function ModalCategoria($value) {
    
    if($value == "AGREGAR") {
    $this->procedencia = 1;
    $this->GetCategorias();
    }
    
}

public function ModalProveedor($value) {
    
    if($value == "AGREGAR") {
    $this->procedencia = 1;
    $this->GetProveedores();
    }
    
}


public function GetCategorias()
{

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $this->categorias_gastos = gastos_categoria::where('comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();


  $this->emit('categorias-show','Show modal');
}


public function GetProveedores()
{

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $this->proveedores_gastos = proveedores::where('comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();


  $this->emit('categorias-show','Show modal');
}




  public function CambioCaja() {

  	$this->tipo_click = 1;

  	if(Auth::user()->comercio_id != 1)
  	$comercio_id = Auth::user()->comercio_id;
  	else
  	$comercio_id = Auth::user()->id;
  	
  	$this->fecha_pedido = $this->fecha_ap;

  	$this->fecha_pedido_desde = $this->fecha_pedido.' 00:00:00';

  	$this->fecha_pedido_hasta = $this->fecha_pedido.' 23:59:50';

  	$this->emit('modal-estado','details loaded');

  	$this->lista_cajas_dia = cajas::where('comercio_id', $comercio_id)->whereBetween('fecha_inicio',[$this->fecha_pedido_desde, $this->fecha_pedido_hasta])->where('eliminado',0)->get();


  }


/////////////////////////////////////////////////////////////////////




//////////////////////   CATEGORIAS   /////////////////////////////////

  public function CreateCategoria()
  {
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $etiqueta = gastos_categoria::create([
      'nombre' => $this->nombre_etiqueta,
      'comercio_id' => $comercio_id
    ]);

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $this->categorias_gastos = gastos_categoria::where('comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();

    if($this->procedencia == 1) {
    $this->emit('categorias-hide','Show modal');
    $this->categoria = $etiqueta->id;
    $this->nombre_etiqueta = '';
    $this->procedencia = 0;
    } else {
    $this->GetCategorias();
    }
    
    $this->nombre_etiqueta = '';

  }

    public function updateCategorias($id_etiqueta, $etiqueta)
    {

      $categoria = gastos_categoria::find($id_etiqueta);

  		$categoria->update([
  			'nombre' => $etiqueta
  		]);

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

      $this->categorias_gastos = gastos_categoria::where('comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();


      $this->GetCategorias();

    $this->emit('msg','Categoria Actualizada');
        
    }


  public function DestroyCategoria(gastos_categoria $id_etiqueta)
	{
		$id_etiqueta->update([
			'eliminado' => 1
		]);

    // Volver todos los gastos de esta categoria a sin categoria
    
    $g = gastos::where('categoria',$id_etiqueta->id)->get();

    foreach($g as $gastos_c) {
    $gastos_c->categoria = 1;
    $gastos_c->save();
    }
    
    //
    
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $this->categorias_gastos = gastos_categoria::where('comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();

    $this->GetCategorias();
    $this->emit('msg','Categoria Eliminada');
	}






/////////////////////////////////////////////////////////////////////


  public function CerrarModalEstado()
	{



		$this->emit('modal-estado-hide','close');

			$this->tipo_click = 0;

	}

  public function ElegirCaja($caja_id)
  {

  $this->caja = $caja_id;
  
  $this->caja_seleccionada =  cajas::find($caja_id);


  $this->emit('modal-estado-hide','close');

  }


	public function Store()
	{
    
        $this->monto = str_replace(',', '.', $this->monto);
        $this->monto_con_iva = str_replace(',', '.', $this->monto_con_iva);
        
        if(0 < $this->deuda && $this->proveedor == 1){
            $this->emit("msg-error","Tenes que elegir un proveedor si quedan saldos impagos");
            return;
        }
		
		$rules  =[
		'nombre' => 'required|min:3',
		'categoria' => 'required|not_in:Elegir|not_in:AGREGAR',
        'monto' => 'required|numeric',
        'etiqueta_form' => 'required|not_in:Elegir',
        'fecha_gasto' => 'required|date|date_not_after_current|after_or_equal:1900-01-01',

		];

		$messages = [
		  'nombre.required' => 'Nombre del gasto es requerido',
		  'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
          'categoria.not_in' => 'La categoria del gasto es requerida',
          'monto.required' => 'El monto es requerido',
          'monto.numeric' => 'El monto debe ser solo numeros',
          'etiqueta_form.not_in' => 'Elija una etiqueta',
          'fecha_gasto.date_not_after_current' => 'La fecha no debe ser posterior a la fecha actual.',
          'fecha_gasto.after_or_equal' => 'La fecha no debe ser anterior a 1900.',
          

		];

	
	$this->validate($rules, $messages);

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;


    $this->monto =str_replace(',', '.', preg_replace( '/,/', '', $this->monto, preg_match_all( '/,/', $this->monto) - 1));
    
    $iva_total = $this->monto * $this->iva;
 
    // Filtrar los métodos de pago donde 'eliminado' es igual a 0 y luego acumular los valores de 'metodo_pago_ap_div'
    $cuentas = collect($this->metodos_pago_dividido)
        ->filter(function ($metodo) {
            return $metodo['eliminado'] == 0;
        })
        ->pluck('metodo_pago_ap_div')
        ->implode(',');

   
	$gasto = gastos::create([
	'nombre' => $this->nombre,
	'categoria' => $this->categoria,
    'cuenta' => $cuentas, // Modificado
    'forma_pago' => 1, // Modificado
    'monto' => $this->monto_con_iva,
    'proveedor_id' => $this->proveedor, // Nuevo
    'alicuota_iva' => $this->iva, // Nuevo
    'iva' => $iva_total, // Nuevo
    'monto_sin_iva' => $this->monto, // Nuevo
    'deuda' => 0, // Nuevo
	'comercio_id' => $comercio_id,
    'etiqueta_id' => $this->etiqueta_form,
    'created_at' => $this->fecha_gasto,
	]);

    $this->StoreUpdateEtiquetas($gasto->id,1,"gastos",$gasto->comercio_id);
    
    // 13-8-2024
    $total_pagos = $this->GuardarPagos($gasto,$comercio_id);
    
    $deuda = $this->monto_con_iva - $total_pagos;
    
    $gasto->update([
        'deuda' => $deuda  
        ]);

    
	$this->resetUI();
	$this->emit('product-added', 'Gasto Registrado');
    $this->emit('msg','Gasto Creado');


	}


	public function Edit(gastos $metodo)
	{
	    $this->agregar = 1;
	    //dd($metodo->id);
	    $pago = pagos_facturas::where('id_gasto', $metodo->id)->first();
	    
	    $fechaFormateada = Carbon::parse($metodo->created_at)->format('Y-m-d');
        
        $this->proveedor = $metodo->proveedor_id;
        
        
		$this->selected_id = $metodo->id;
		if($pago != null){
		$this->caja = $pago->caja;
		$this->caja_seleccionada =  cajas::find($pago->caja);
		} else {
		$this->caja = null;    
		$this->caja_seleccionada =  null;
		}
		$this->fecha_gasto = $fechaFormateada;
        
		$this->nombre = $metodo->nombre;
        $this->metodo_pago_elegido = $metodo->cuenta;
		$this->categoria = $metodo->categoria;
		
		$this->monto = $metodo->monto_sin_iva;
		$this->monto_con_iva = $metodo->monto;
        
        $this->forma_pago_elegida = $metodo->forma_pago;
        
        // ETIQUETAS 
		
        //dd($this->etiquetas_seleccionadas);
        
	    $this->etiqueta = $this->GetEtiquetas($metodo->comercio_id,"gastos");
	    
	    //dd($this->etiqueta_gastos);
	    
	    $this->GetEtiquetasEdit($metodo->id,"gastos",$metodo->comercio_id);
	    
	    $this->GetFormaPagoEditTrait($metodo->forma_pago, $metodo->comercio_id);
	    
	    $pagos = pagos_facturas::where('id_gasto', $metodo->id)->get();
	    
	    $this->metodos_pago_dividido = [];
	    $this->SetearMetodosPago($pagos);
        $this->deuda = $metodo->deuda;
        $this->iva = $metodo->alicuota_iva;
	}

    public function Agregar() {
    $this->GetEtiquetasEdit(0,"gastos",$this->comercio_id);
    $this->GetFormaPagoEditTrait(0, $this->comercio_id);
    $this->agregar = 1;
    }
    
	public function Update()
	{
	    
	    $this->monto = str_replace(',', '.', $this->monto);
		
		if(0 < $this->deuda && $this->proveedor == 1){
            $this->emit("msg-error","Tenes que elegir un proveedor si quedan saldos impagos");
            return;
        }
        
		$rules  =[
		'nombre' => 'required|min:3',
		'categoria' => 'required|not_in:Elegir|not_in:AGREGAR',
        'monto' => 'required|numeric',
        'metodo_pago_elegido' => 'not_in:Elegir',
        'etiqueta_form' => 'required|not_in:Elegir',
        'fecha_gasto' => 'required|date|date_not_after_current|after_or_equal:1900-01-01',

		];

		$messages = [
		  'nombre.required' => 'Nombre del gasto es requerido',
		  'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
          'categoria.not_in' => 'La categoria del gasto es requerida',
          'monto.required' => 'El monto es requerido',
          'monto.numeric' => 'El monto debe ser solo numeros',
          'metodo_pago_elegido.not_in' => 'Elija un metodo de pago',
          'etiqueta_form.not_in' => 'Elija una etiqueta',
          'fecha_gasto.date_not_after_current' => 'La fecha no debe ser posterior a la fecha actual.',
          'fecha_gasto.after_or_equal' => 'La fecha no debe ser anterior a 1900.',

		];

		$this->validate($rules, $messages);

        $iva_total = $this->monto * $this->iva;
        
        // Filtrar los métodos de pago donde 'eliminado' es igual a 0 y luego acumular los valores de 'metodo_pago_ap_div'
        $cuentas = collect($this->metodos_pago_dividido)
            ->filter(function ($metodo) {
                return $metodo['eliminado'] == 0;
            })
            ->pluck('metodo_pago_ap_div')
            ->implode(',');
            
            
		$gasto = gastos::find($this->selected_id);

		$gasto->update([
		'nombre' => $this->nombre,
		'categoria' => $this->categoria,
        'monto' => $this->monto_con_iva,
        'proveedor_id' => $this->proveedor, // Nuevo
        'alicuota_iva' => $this->iva, // Nuevo
        'iva' => $iva_total, // Nuevo
        'monto_sin_iva' => $this->monto, // Nuevo
        'deuda' => 0, // Nuevo
        'etiqueta_id' => $this->etiqueta_form,
        'created_at' => $this->fecha_gasto,
        'cuenta' => $cuentas,
        'forma_pago' => 1
		]);
        
        $this->CalcularDeuda();
        
        $this->StoreUpdateEtiquetas($gasto->id,2,"gastos",$gasto->comercio_id);
        
        $total_pagos = $this->ActualizarPagos($gasto,$gasto->comercio_id);
        
        $gasto->update([
            'deuda' => $this->deuda  
            ]);
    

		$this->resetUI();
		$this->emit('product-updated', 'Gasto Actualizado');
        $this->emit('msg','Gasto Actualizado');


	}



	public function resetUI()
	{
	$this->proveedor = 1; // Nuevo
    $this->iva = 0; // Nuevo
    $this->monto_con_iva = null; // Nuevo
    $this->deuda = null; // Nuevo
    
	$this->metodos_pago_dividido = [];
	$this->etiquetas_seleccionadas = [];    
	$this->agregar = 0;
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $this->caja = null;

    $this->selected_id ='';
    $this->fecha_gasto = Carbon::now();
	$this->nombre ='';
    $this->etiqueta_form =1;
	$this->categoryid ='Elegir';
	$this->image = null;
	$this->monto = '';
	$this->nombre_etiqueta = '';
    $this->categoria = 'Elegir';
    $this->metodo_pago_elegido = 'Elegir';
    $this->LimpiarFiltros();
    $this->mostrarFiltros = false;

	}

	protected $listeners =[
	'deleteRow' => 'Destroy',
    'deleteRow2' => 'DestroyEtiqueta',
    'deleteRowCategoria' => 'DestroyCategoria',
    'RestaurarGasto' => 'RestaurarGasto',
    'accion-lote' => 'AccionEnLote',
    'EtiquetasSeleccionadas',
    'SearchEtiquetas',
    'FechaElegida' => 'FechaElegida',
    'FormaPagoSeleccionado'
    
	];

    public function FormaPagoSeleccionado($value){
       
    //dd($value,$this->comercio_id,$this->casa_central_id);   
    $this->forma_pago_elegida = $this->SetFormaPago($value,$this->comercio_id,$this->casa_central_id);
    
    }
    
    
    public function LimpiarFiltros(){
    
    $this->search = null;
    $this->proveedor_filtro = "all";
    $this->categoria_filtro = null;
    $this->etiquetas_filtro = [];
    $this->forma_pago_filtro = null;
    $this->metodo_pago_filtro = null;
    $this->dateFrom  = Carbon::now()->firstOfYear()->format('Y-m-d');
    $this->dateTo = Carbon::now()->format('Y-m-d');
    $this->emit("set-fecha",$this->dateFrom,$this->dateTo);
    $this->LimpiarEtiquetas($this->comercio_id,"gastos");
    $this->GetEtiquetasEdit(0,"gastos",$this->comercio_id);
    $this->GetFormaPagoEditTrait(0, $this->comercio_id);

    }
    
    public function EtiquetasSeleccionadas($Etiquetas)
    {
    $this->SetEtiquetasSeleccionadas($this->comercio_id,$Etiquetas,"gastos");
    }
    
    public function SearchEtiquetas($value){
    
    $this->etiquetas_filtro = $this->BuscarEtiquetas($value,$this->comercio_id,"gastos"); 
    //dd($this->etiquetas_filtro);
    $this->etiquetas_filtro_excel = implode(",",$this->etiquetas_filtro);
    }


	public function Destroy(gastos $metodo)
	{
		$imageTemp = $metodo->image;
		$metodo->eliminado = 1;
		$metodo->save();

        $pagos_facturas = pagos_facturas::where('id_gasto', $metodo->id)->get();
        foreach($pagos_facturas as $pago_factura){
            $pago = pagos_facturas::find($pago_factura->id);
            $pago->eliminado = 1;
            $pago->save();            
        }

		$this->resetUI();
		$this->emit('product-deleted', 'Gasto Eliminado');
		$this->emit('msg','Gasto Eliminado');
	}
	
	

  public function ElegirSucursal($sucursal_id) {

  	$this->sucursal_id = $sucursal_id;

  }
  
  
  
  public function SinCaja() {

  	$this->caja = null;
  	$this->caja_elegida = null;

  }
  
  public function ModalAbrirCaja(){
   $this->emit('abrir-caja', '');   
  }
  

  public function AbrirCaja() {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    if($this->sucursal_id != null) {
      $this->sucursal_id = $this->sucursal_id;
    } else {
      $this->sucursal_id = $comercio_id;
    }

    $ultimo = cajas::where('cajas.comercio_id', 'like', $this->sucursal_id)->where('estado',1)->where('eliminado', 0)->latest('nro_caja')->first();
    
   
    
    if($ultimo != null) {
    $nro = $ultimo->nro_caja + 1;
    }
    else {
    $nro = 1;
    }

    DB::beginTransaction();

    try {

      $cajas = DB::table('cajas')->insertGetId([
        'user_id' => Auth::user()->id,
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
    
    $this->caja = $cajas;
    $this->caja_seleccionada = cajas::find($this->caja);
    
    
    $this->emit('abrir-caja-hide','Show modal');

    $this->emit('msg','Caja Abierta');
    
  }
  
      
    public function ExportarReporte($url) {

    //dd($url);
    
    return redirect('report-gastos/excel/'. $url .'/'. Carbon::now()->format('d_m_Y_H_i_s'));

}
    // Filtra por eliminado o activos 
    
	public function Filtro($estado)
	{
	   $this->estado_filtro = $estado;
	   
	}
	
	    // Eliminar en lote 
    
		
	public function RestaurarGasto(gastos $gastos)
	{
		$gastos->update([
			'eliminado' => 0
		]);
		
		$pagos_facturas = pagos_facturas::where('id_gasto', $gastos->id)->first();
        $pagos_facturas->eliminado = 0;
        $pagos_facturas->save();


		$this->resetUI();
		$this->emit('product-deleted', 'Gasto Restaurado');
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
    
    $gastos_checked = gastos::select('gastos.id')->whereIn('gastos.id',$ids)->get();

    $this->id_check = [];
    
    foreach($gastos_checked as $pc) {
    
    $pc->eliminado = $estado;
    $pc->save();
    
    $pagos_facturas = pagos_facturas::where('id_gasto', $pc->id)->get();
    
    foreach($pagos_facturas as $pago_factura){
        $pago = pagos_facturas::find($pago_factura->id);
        $pago->eliminado = $estado;
        $pago->save();            
    }
        
    }
    
    $this->id_check = [];
    $this->accion_lote = 'Elegir';
    
     $this->emit('global-msg',"GASTOS ".$msg);
    
    }
    
  public function DestroyEtiqueta($id_etiqueta)
	{
	
	$id_etiqueta = $this->DeleteEtiqueta($id_etiqueta);
    

    // Volver todos los gastos de esta categoria a sin categoria
    
    $g = gastos::where('etiqueta_id',$id_etiqueta->id)->get();

    foreach($g as $gastos_c) {
    $gastos_c->etiqueta_id = 1;
    $gastos_c->save();
    }
    
    //
    $this->GetEtiqueta();
    $this->emit('msg','Etiqueta Eliminada');

	    
	}
	
	
	public function cargarEtiquetas(){
	    $this->cargarEtiquetasTrait($this->comercio_id);
	}



//18-5-2024
public function GetPlazoAcreditacionPago($id){
    return $id == 1 ? 1 : 0;
}

public function SetearGastos(){
  $gastos = gastos::where('eliminado',1)->get();    
  
  foreach($gastos as $g){
  $pf = pagos_facturas::where('id_gasto',$g->id)->first();
  if($pf != null){
  $pf->eliminado = $g->eliminado;
  $pf->save();
  }

  
  }
  
 $this->emit('msg','Actualizado');      
}


// NUEVO PAGO DIVIDIDO
    
    /*
    public function quitarMetodoPago($index) {
        if (isset($this->metodos_pago_dividido[$index])) {
            unset($this->metodos_pago_dividido[$index]);
        }
        
        $this->CalcularDeuda();                                        
    }
    */
    
    public function quitarMetodoPago($index)
    {
        // Actualiza el valor de efectivo en el índice correspondiente
        $this->metodos_pago_dividido[$index]['eliminado'] = 1;
        // Puedes agregar cualquier otra lógica de actualización necesaria aquí
        $this->CalcularDeuda();
    }                                    
                                        
    public function agregarMetodoPago()
    {
        $array = [
            'id' => 0,
            'efectivo' => 0,
            'metodo_pago_ap_div' => 1,
            'eliminado' => 0
            ];
                                                    
        array_push($this->metodos_pago_dividido, $array);

        $this->metodo_pago = $this->GetBancos($this->comercio_id);
        //dd($this->metodo_pago);
    }

    public function CambiarMontoPago($index, $value)
    {
        $value = $this->convertirFormatoMoneda($value);
        // Actualiza el valor de efectivo en el índice correspondiente
        $this->metodos_pago_dividido[$index]['efectivo'] = $value;
        // Puedes agregar cualquier otra lógica de actualización necesaria aquí
        $this->CalcularDeuda();
    }

    public function CambiarMetodoPago($index, $value)
    {
        $this->metodos_pago_dividido[$index]['metodo_pago_ap_div'] = $value;
    }
    
    public function CambiarMonto(){
        $this->monto = $this->convertirFormatoMoneda($this->monto);
        $this->monto_con_iva = round($this->monto * (1 + $this->iva),2);
        $this->CalcularDeuda();
    //    $this->monto_con_iva =  number_format($this->monto_con_iva,2,",",".");
    }

    public function CambiarMontoConIVA(){
        $this->monto_con_iva = $this->convertirFormatoMoneda($this->monto_con_iva);
        $this->monto = round($this->monto_con_iva / (1 + $this->iva),2);
        $this->CalcularDeuda();
    //    $this->monto =  number_format($this->monto,2,",",".");
    }
                                            
    public function CambiarIVA(){
        //$this->monto = $this->convertirFormatoMoneda($this->monto);
        $this->monto_con_iva = round($this->monto * (1 + $this->iva),2);
        $this->CalcularDeuda();
    //    $this->monto_con_iva =  number_format($this->monto_con_iva,2,",",".");
    }
    
    public function CalcularDeuda(){
        
        $pago_total = 0;
        foreach($this->metodos_pago_dividido as $metodo_pago_dividido){
            if($metodo_pago_dividido['eliminado'] == 0){
                $pago_total += $metodo_pago_dividido['efectivo'];    
            }
            
        }
        
        $this->deuda = $this->monto_con_iva - $pago_total;

    }

function convertirFormatoMoneda($valor) {
    // Eliminar los puntos
    $valor = str_replace('.', '', $valor);
    // Reemplazar la coma con punto
    $valor = str_replace(',', '.', $valor);
    return $valor;
}

public function GetBancos($comercio_id)
{
    $bancos = bancos::join('bancos_muestra_sucursales', 'bancos_muestra_sucursales.banco_id', '=', 'bancos.id')
        ->where('bancos_muestra_sucursales.muestra', 1)
        ->where('bancos_muestra_sucursales.sucursal_id', $comercio_id)
        ->select('bancos.id', 'bancos.nombre') // Selecciona solo los campos necesarios
        ->orderBy('bancos.nombre', 'asc')
        ->get();

    return $bancos;
}    


public function ActualizarPagos($gasto,$comercio_id){
    
    $monto_total = 0;
    // Guardar los métodos de pago asociados al gasto
    foreach ($this->metodos_pago_dividido as $pago) {
    
    if($pago['id'] != 0){
    
    if($pago['metodo_pago_ap_div'] == 1) {$mp = 1;} else {$mp = 0;}
    
    $pagos = pagos_facturas::find($pago['id']);  
    //dd($pago);
    if($pagos->banco_id != $pago['metodo_pago_ap_div']){
    $estado_pago = $this->GetPlazoAcreditacionPago($pago['metodo_pago_ap_div']);          
    } else {
    $estado_pago =  $pagos->estado_pago;   
    }

    $pagos->update([
      'estado_pago' => $estado_pago,
      'monto_gasto' => $pago['efectivo'],
      'caja' => $this->caja,
      'proveedor_id' => $this->proveedor,
      'banco_id'  => $pago['metodo_pago_ap_div'],
      'metodo_pago'  => $mp,
      'eliminado' => $pago['eliminado'],
    ]);
    
    //dd($pagos);
    }

    
    if($pago['eliminado'] == 0 && $pago['id'] == 0){
        
    if($pago['metodo_pago_ap_div'] == 1) {$mp = 1;} else {$mp = 0;}
    $estado_pago = $this->GetPlazoAcreditacionPago($pago['metodo_pago_ap_div']);        
        
    $pagos = pagos_facturas::create([
      'estado_pago' => $estado_pago,
      'monto_gasto' => $pago['efectivo'],
      'id_gasto' => $gasto->id,
      'comercio_id' => $comercio_id,
      'caja' => $this->caja,
      'proveedor_id' => $this->proveedor,
      'banco_id'  => $pago['metodo_pago_ap_div'],
      'metodo_pago'  => $mp,
      'eliminado' => 0,
    ]);
    
    }    
    
    
    $monto_total += $pago['efectivo'];
    
    }
    
    return $monto_total;
    
}

public function GuardarPagos($gasto,$comercio_id){
    
    $monto_total = 0;
    // Guardar los métodos de pago asociados al gasto
    foreach ($this->metodos_pago_dividido as $pago) {
    
    if($pago['eliminado'] == 0 && $pago['id'] == 0){
        
    if($pago['metodo_pago_ap_div'] == 1) {$mp = 1;} else {$mp = 0;}
    $estado_pago = $this->GetPlazoAcreditacionPago($pago['metodo_pago_ap_div']);        
        
    $pagos = pagos_facturas::create([
      'estado_pago' => $estado_pago,
      'monto_gasto' => $pago['efectivo'],
      'id_gasto' => $gasto->id,
      'comercio_id' => $comercio_id,
      'caja' => $this->caja,
      'proveedor_id' => $this->proveedor,
      'banco_id'  => $pago['metodo_pago_ap_div'],
      'metodo_pago'  => $mp,
      'eliminado' => 0,
    ]);
    
    $monto_total += $pago['efectivo'];
    
    }
    
    }
    
    return $monto_total;
    
}

public function SetearMetodosPago($pagos){
    
    foreach($pagos as $pago){

        $array = [
            'id' => $pago->id,
            'efectivo' => $pago->monto_gasto,
            'metodo_pago_ap_div' => $pago->banco_id,
            'eliminado' => $pago->eliminado
            ];
                                                    
        array_push($this->metodos_pago_dividido, $array);        
    }
}

public function SetMontoGastos(){
    $gastos = gastos::get();
    foreach($gastos as $gasto){
      $g =  gastos::find($gasto->id);
      $g->monto_sin_iva = $g->monto;
      $g->save();
    }
}


}
