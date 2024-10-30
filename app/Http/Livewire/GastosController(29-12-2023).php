<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\gastos;
use App\Models\gastos_categoria;
use App\Models\bancos;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\sucursales;
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

class GastosController extends Component
{

  use WithPagination;
  use WithFileUploads;
  
  use BancosTrait;

  public $nombre,$recargo,$descripcion,$caja_abierta,$agregar,$id_check,$price,$stock,$caja, $monto_inicial , $fecha_ap, $fecha_gasto,$sucursal_id, $alerts,$categoryid,$ultimas_cajas,$caja_seleccionada, $search,$image,$selected_id,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $metodos, $categoria, $monto, $dateFrom, $dateTo, $categorias_gastos, $categoria_filtro, $suma_totales, $gastos_total, $etiquetas_gastos, $etiqueta, $nombre_etiqueta, $fecha_pedido, $lista_cajas_dia, $etiqueta_form, $etiquetas_filtro, $forma_pago_filtro, $metodo_pago_elegido, $gastos_categoria;
	private $pagination = 25;
	public $accion_lote;
	public $procedencia;


	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function mount()
	{
	$this->procedencia = 0;
	$this->estado_filtro = 0;
	$this->accion_lote = 'Elegir';
	
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;


	
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


    $gastos = gastos::leftjoin('etiqueta_gastos','etiqueta_gastos.id','gastos.etiqueta_id')
    ->join('bancos','bancos.id','gastos.cuenta')
    ->join('gastos_categorias','gastos_categorias.id','gastos.categoria')
    ->select('gastos.*','etiqueta_gastos.nombre as nombre_etiqueta','bancos.Nombre as nombre_banco','gastos_categorias.nombre as nombre_categoria')
    ->where('gastos.comercio_id', 'like', $comercio_id);

    if(strlen($this->search) > 0) {

		$gastos = $gastos->where('gastos.nombre', 'like', '%' . $this->search . '%');

    }
    if($this->categoria_filtro) {

      $gastos = $gastos->where('gastos.categoria',$this->categoria_filtro);

    }

    if($this->etiquetas_filtro) {

      $gastos = $gastos->where('gastos.etiqueta_id',$this->etiquetas_filtro);

    }

    if($this->forma_pago_filtro) {

      $gastos = $gastos->where('gastos.cuenta',$this->forma_pago_filtro);

    } 

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
    
    $metodo_pagos = $this->GetBancosTrait($comercio_id);
    
    $this->gastos_total = $gastos->sum('monto');

    $etiqueta_gastos = EtiquetaGastos::where('etiqueta_gastos.comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();

    $this->gastos_categoria = gastos_categoria::where('comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();


		return view('livewire.gastos.component', [
			'data' => $gastos,
      'etiquetas' => $etiqueta_gastos,
      'metodo_pago' =>   $metodo_pagos,
      'gastos_categoria' => $this->gastos_categoria
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

    $etiqueta = EtiquetaGastos::create([
      'nombre' => $this->nombre_etiqueta,
      'comercio_id' => $comercio_id
    ]);

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $etiqueta_gastos = EtiquetaGastos::where('etiqueta_gastos.comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();
    
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


    public function updateEtiqueta($id_etiqueta, $etiqueta)
    {

      $metodo = EtiquetaGastos::find($id_etiqueta);

  		$metodo->update([
  			'nombre' => $etiqueta
  		]);

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

      $etiqueta_gastos = EtiquetaGastos::where('etiqueta_gastos.comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();


      $this->GetEtiqueta();
      
      
        $this->emit('msg','Etiqueta Actualizada');

    }




public function GetEtiqueta()
{

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $etiqueta_gastos = EtiquetaGastos::where('etiqueta_gastos.comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();

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



public function GetCategorias()
{

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $this->categorias_gastos = gastos_categoria::where('comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();


  $this->emit('categorias-show','Show modal');
}



  public function DestroyEtiqueta(EtiquetaGastos $id_etiqueta)
	{
		$id_etiqueta->update([
			'eliminado' => 1
		]);

    // Volver todos los gastos de esta categoria a sin categoria
    
    $g = gastos::where('etiqueta_id',$id_etiqueta->id)->get();

    foreach($g as $gastos_c) {
    $gastos_c->etiqueta_id = 1;
    $gastos_c->save();
    }
    
    //
    
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $etiqueta_gastos = EtiquetaGastos::where('etiqueta_gastos.comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();


    $this->GetEtiqueta();
    $this->emit('msg','Etiqueta Eliminada');
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
		
		$rules  =[
		'nombre' => 'required|min:3',
		'categoria' => 'required|not_in:Elegir',
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

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;


    $this->monto =str_replace(',', '.', preg_replace( '/,/', '', $this->monto, preg_match_all( '/,/', $this->monto) - 1));
    
	$product = gastos::create([
	'nombre' => $this->nombre,
	'categoria' => $this->categoria,
    'cuenta' => $this->metodo_pago_elegido,
    'monto' => $this->monto,
	'comercio_id' => $comercio_id,
      'etiqueta_id' => $this->etiqueta_form,
      'created_at' => $this->fecha_gasto
		]);

            
    if($this->metodo_pago_elegido == 1) {
    $mp = 1;
    } else {$mp = 0;}


    $pagos = pagos_facturas::create([
      'monto_gasto' => $this->monto,
      'id_gasto' => $product->id,
      'comercio_id' => $comercio_id,
      'caja' => $this->caja,
      'banco_id'  => $this->metodo_pago_elegido,
      'metodo_pago'  => $mp,
      'eliminado' => 0,
    ]);

		if($this->image)
		{
			$customFileName = uniqid() . '_.' . $this->image->extension();
			$this->image->storeAs('public/products', $customFileName);
			$product->image = $customFileName;
			$product->save();
		}

		$this->resetUI();
		$this->emit('product-added', 'Gasto Registrado');
        $this->emit('msg','Gasto Creado');


	}


	public function Edit(gastos $metodo)
	{
	    $this->agregar = 1;
	    
	    $pago = pagos_facturas::where('id_gasto', $metodo->id)->first();
	    
	    $fechaFormateada = Carbon::parse($metodo->created_at)->format('Y-m-d');
	    
		$this->selected_id = $metodo->id;
		$this->caja = $pago->caja;
		$this->fecha_gasto = $fechaFormateada;
        $this->caja_seleccionada =  cajas::find($pago->caja);
		$this->nombre = $metodo->nombre;
        $this->metodo_pago_elegido = $metodo->cuenta;
		$this->categoria = $metodo->categoria;
		$this->monto = $metodo->monto;
        $this->etiqueta_form = $metodo->etiqueta_id;

	//	$this->emit('modal-show','Show modal');
	}

    public function Agregar() {
    $this->agregar = 1;
    }
    
	public function Update()
	{
	    
	     $this->monto = str_replace(',', '.', $this->monto);
		
		$rules  =[
		'nombre' => 'required|min:3',
		'categoria' => 'required|not_in:Elegir',
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

		$metodo = gastos::find($this->selected_id);

		$metodo->update([
		'nombre' => $this->nombre,
		'categoria' => $this->categoria,
        'monto' => $this->monto,
        'etiqueta_id' => $this->etiqueta_form,
        'created_at' => $this->fecha_gasto,
        'cuenta' => $this->metodo_pago_elegido,
		]);


    $pagos_facturas = pagos_facturas::where('id_gasto', $this->selected_id)->first();

    if($this->metodo_pago_elegido == 1) {
    $mp = 1;
    } else {$mp = 0;}
    
    $pagos_facturas->update([
      'monto_gasto' => $this->monto,
      'caja' => $this->caja,
      'metodo_pago'  => $mp,
      'banco_id'  => $this->metodo_pago_elegido,
    ]);


    
    
		$this->resetUI();
		$this->emit('product-updated', 'Gasto Actualizado');
        $this->emit('msg','Gasto Actualizado');


	}



	public function resetUI()
	{
	    
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

	}

	protected $listeners =[
		'deleteRow' => 'Destroy',
    'deleteRow2' => 'DestroyEtiqueta',
    'deleteRowCategoria' => 'DestroyCategoria',
    'RestaurarGasto' => 'RestaurarGasto',
    'accion-lote' => 'AccionEnLote'
	];


	public function Destroy(gastos $metodo)
	{
		$imageTemp = $metodo->image;
		$metodo->eliminado = 1;
		$metodo->save();

        $pagos_facturas = pagos_facturas::where('id_gasto', $metodo->id)->first();
        $pagos_facturas->eliminado = 1;
        $pagos_facturas->save();

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
 
    }
    
    $this->id_check = [];
    $this->accion_lote = 'Elegir';
    
     $this->emit('global-msg',"GASTOS ".$msg);
    
    }
    

}
