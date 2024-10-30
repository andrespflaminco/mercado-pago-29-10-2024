<?php

namespace App\Http\Livewire;


use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use App\Models\wocommerce;
use App\Models\ClientesMostrador;
use App\Models\proveedores;
use App\Models\recordatorios;
use App\Models\recordatorios_comentarios;
use Carbon\Carbon;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use DB;

class RecordatorioController extends Component
{

	use WithFileUploads;
	use WithPagination;



	public $name, $search, $image, $contactos,$venta, $detalle_venta,$fecha, $descripcion, $color_elegido, $titulo, $contacto_elegido, $elegido, $selected_id, $tipo_contacto, $from, $to, $color, $active_3meses,$filtro_fecha, $pageTitle, $query, $fecha_nueva,$comentario, $componentName, $tipo_elegido, $contacto_id_elegido,$datos_clientes, $datos_proveedores;
	private $pagination = 5;
	private $wc_category;

	public function mount()
	{
		$this->pageTitle = 'Listado';
		$this->componentName = 'Categorías';
		$from = Carbon::now()->format('Y-m-d').' 00:00:00';
		$to = Carbon::now()->format('Y-m-d').' 23:59:59';
		
		$this->detalle_venta = [];

		 $this->active_hoy = "active";
		 $this->active_semana = "";
		 $this->active_mes = "";
		 $this->color = "todos";
		 $this->tipo_contacto = 0;
		 $this->contactos = [];
		 $this->datos_clientes = [];
		 $this->datos_proveedores = [];
		 $this->nombre_elegido = 0;
		 $this->color_elegido = 0;



	}

	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function render()
	{
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$from = Carbon::now()->format('Y-m-d').' 00:00:00';
		$to = Carbon::now()->format('Y-m-d').' 23:59:59';


			$data = recordatorios::select('recordatorios.*')
			->where('recordatorios.comercio_id', 'like', $comercio_id)
			->where('estado',0);

			if($this->color != "todos") {
				$data = $data->where('color',$this->color);
			}
			$data = $data->orderBy('recordatorios.id','desc');

			if($this->filtro_fecha == 1) {

				$from = Carbon::now()->format('Y-m-d').' 00:00:00';
				$to = Carbon::now()->format('Y-m-d').' 23:59:59';

				 $this->active_hoy = "active";
				 $this->active_semana = "";
				 $this->active_mes = "";
				 $this->active_3meses = "";

			}


			if($this->filtro_fecha == 2) {


				$from = Carbon::now()->startOfWeek();
			 	$to = Carbon::now()->endOfWeek();

				 $this->active_hoy = "";
				 $this->active_semana = "active";
				 $this->active_mes = "";
				 $this->active_3meses = "";

			}

			if($this->filtro_fecha == 3) {

				$from = Carbon::now()->startOfMonth();
				$to = Carbon::now()->endOfMonth();

				 $this->active_hoy = "";
				 $this->active_semana = "";
				 $this->active_mes = "active";
				 $this->active_3meses = "";


			}

			if($this->filtro_fecha == 4) {

				$from = Carbon::now()->startOfMonth();
				$to = Carbon::now()->add(3, 'month');

				 $this->active_hoy = "";
				 $this->active_semana = "";
				 $this->active_mes = "";
				 $this->active_3meses = "active";

			}


			$data = $data->whereBetween('recordatorios.fecha', [$from, $to])
			->paginate($this->pagination);

			$first = DB::table('clientes_mostradors')
			->where('comercio_id',$comercio_id)
			->select('id','nombre',ClientesMostrador::raw(' "cliente" as tipo'));

			$this->contactos = DB::table('proveedores')
			->select('id','nombre',proveedores::raw(' "proveedor" as tipo'))
			->where('comercio_id',$comercio_id)
      ->union($first)
			->orderBy('nombre','ASC')
      ->get();

			$this->datos_clientes = ClientesMostrador::where('comercio_id',$comercio_id)->get();

			$this->datos_proveedores = proveedores::where('comercio_id',$comercio_id)->get();


		return view('livewire.recordatorio.component', [
			'recordatorio' => $data,
			'contactos' => $this->contactos,
			'datos_clientes' => $this->datos_clientes,
			'datos_proveedores' => $this->datos_proveedores

		])
		->extends('layouts.theme.app')
		->section('content');
	}



	public function Edit($id)
	{
		$record = recordatorios::find($id);

		if($record->tipo_contacto == "cliente") {
			$this->elegido = ClientesMostrador::find($record->contacto_id);
			$this->nombre_elegido = $this->elegido->nombre;

		}

		if($record->tipo_contacto == "proveedor") {
			$this->elegido = proveedores::find($record->contacto_id);
			$this->nombre_elegido = $this->elegido->nombre;
		}

		$this->titulo = $record->titulo;
		$this->fecha = $record->fecha;
		$this->descripcion = $record->descripcion;
		$this->contacto_elegido_id = $record->contacto_id;
		$this->tipo_elegido = $record->tipo_elegido;
		$this->color_elegido = '';

		$this->emit('recordatorio', 'show modal!');
	}



	public function Store()
	{


		$rules = [
			'titulo' => 'required|min:3'
		];

		$messages = [
			'titulo.required' => 'El titulo es requerido',
			'titulo.min' => 'El titulo debe tener al menos 3 caracteres'
		];

		$this->validate($rules, $messages);

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		if($this->elegido) {
			$this->contacto_elegido_id = $this->elegido->id;
		} else {
			$this->contacto_elegido_id = 0;
		}
		

		$category = recordatorios::create([
			'titulo' => $this->titulo,
			'comercio_id' => $comercio_id,
			'fecha' => $this->fecha,
			'descripcion' => $this->descripcion,
			'contacto_id' => $this->contacto_elegido_id,
			'tipo_contacto' => $this->tipo_elegido,
			'color' => $this->color_elegido,
			'estado' => 0
		]);

		$this->resetUI();
		$this->emit('recordatorio-added','Recordatorio Registrado');

	}


	public function Update()
	{
		$rules =[
			'name' => "required|min:3|unique:categories,name,{$this->selected_id}"
		];

		$messages =[
			'name.required' => 'Nombre de categoría requerido',
			'name.min' => 'El nombre de la categoría debe tener al menos 3 caracteres'
		];

		$this->validate($rules, $messages);


		$category = Category::find($this->selected_id);

		////////// WooCommerce ////////////
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$wc = wocommerce::where('comercio_id', $comercio_id)->first();


	////////////////////////////////////////////////

		$category->update([
			'name' => $this->name
		]);

		if($this->image)
		{
			$customFileName = uniqid() . '_.' . $this->image->extension();
			$this->image->storeAs('public/categories', $customFileName);
			$imageName = $category->image;

			$category->image = $customFileName;
			$category->save();

			if($imageName !=null)
			{
				if(file_exists('storage/categories' . $imageName))
				{
					unlink('storage/categories' . $imageName);
				}
			}

		}

		$this->resetUI();
		$this->emit('category-updated', 'Categoría Actualizada');



	}


	public function resetUI()
	{
		$this->titulo = '';
		$this->fecha = '';
		$this->descripcion = '';
		$this->contacto_elegido_id = '';
		$this->tipo_elegido = '';
		$this->color_elegido = 0;
		$this->nombre_elegido = 0;
		
	}

	protected $listeners =[
		'deleteRow' => 'Destroy',
		'ElegirColor' => 'ElegirColor'
	];


	public function Destroy(Category $category)
	{

		$imageName = $category->image;
		$category->delete();

		////////// WooCommerce ////////////
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$wc = wocommerce::where('comercio_id', $comercio_id)->first();

		if($wc != null){

		$woocommerce = new Client(
			$wc->url,
			$wc->ck,
			$wc->cs,

				[
						'version' => 'wc/v3',
				]
		);

		$data = [
				'name' => $this->name,
				'image' => [
						'src' => ''
				]
		];

		$woocommerce->delete('products/categories/'.$category->wc_category_id , ['force' => true]);
	}

	////////////////////////////////////////////////

		if($imageName !=null) {
			unlink('storage/categories/' . $imageName);
		}

		$this->resetUI();
		$this->emit('category-deleted', 'Categoría Eliminada');

	}

public function FiltroFecha($filtro) {
	$this->filtro_fecha = $filtro;
}


public function Completado($id) {

$recordatorios = recordatorios::find($id);

$recordatorios->update([
	'estado' => 1
]);



}



public function ReprogramarModal($id) {

$this->selected_id =  $id;

$this->emit('cambiar-estado', $id);

}

public function Reprogramar() {

	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

	$recordatorios = recordatorios::find($this->selected_id);

	$recordatorios->update([
		'estado' => 2
	]);


	$recordatorios = recordatorios::create([
		'fecha' => $this->fecha_nueva,
		'sale_id' => $recordatorios->sale_id,
		'comercio_id' => $comercio_id,
		'estado' => 0
	]);

	$comentarios = recordatorios_comentarios::create([
		'recordatorio_id' => $recordatorios->id,
		'comentario' => $this->comentario,
		'comercio_id' => $comercio_id,
		'user_id' => Auth::user()->id
	]);

	$this->emit('cambiar-estado-hide', 'hide');

}

public function AbrirModal() {
	$this->emit('recordatorio', '');
}


public function CambiarColor($color, $id) {

	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

	$recordatorios = recordatorios::find($id);

	$recordatorios->update([
		'color' => $color
	]);

}

public function filtrar($color) {
	$this->color = $color;
}

public function ModalContacto() {
	$this->emit('contacto','');
	$this->emit('recordatorio-hide','');
}


public function AgregarContacto() {


$contacto_elegido = $this->contacto_elegido;

$porciones = explode("-", $contacto_elegido);
$this->tipo_elegido = $porciones[0]; // porción1
$this->id_elegido = $porciones[1]; // porción2

if($this->tipo_elegido == "cliente") {
	$this->elegido = ClientesMostrador::find($this->id_elegido);
	$this->nombre_elegido = $this->elegido->nombre;

}

if($this->tipo_elegido == "proveedor") {
	$this->elegido = proveedores::find($this->id_elegido);
	$this->nombre_elegido = $this->elegido->nombre;
}


$this->emit('contacto-hide','');
$this->emit('recordatorio','');
}

public function ElegirColor($color) {
    
	$this->color_elegido = $color;

$this->emit('color-hide','');
$this->emit('recordatorio','');

}


public function ModalColor() {

$this->emit('color','');    
$this->emit('recordatorio-hide','');

    
}

public function RenderFactura($sale_id) {
	
	$this->detalle_venta = SaleDetail::where('sale_details.sale_id',$sale_id)->where('sale_details.eliminado', 0)->get();
	
	$this->venta = Sale::join('metodo_pagos','metodo_pagos.id','sales.metodo_pago')->select('sales.*','metodo_pagos.nombre as metodo_pago')->where('sales.id',$sale_id)->first();
	
	$this->emit('venta','');
}




}
