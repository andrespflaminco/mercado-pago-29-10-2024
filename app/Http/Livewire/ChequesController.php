<?php

namespace App\Http\Livewire;


use App\Models\Category;
use App\Models\cheques;
use App\Models\pagos_facturas;
use App\Models\recordatorios;
use App\Models\ClientesMostrador;
use App\Models\Sale;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use App\Models\wocommerce;
use Livewire\WithFileUploads;
use Livewire\WithPagination;


class ChequesController extends Component
{

	use WithFileUploads;
	use WithPagination;



	public $name, $search,$cheque,$nro_cheque_ch, $emisor_ch, $banco_ch, $fecha_cobro_ch, $fecha_emision_ch, $image, $selected_id, $pageTitle, $componentName, $wc_category_id;
	private $pagination = 15;
	private $wc_category;

	public function mount()
	{
		$this->pageTitle = 'Listado';
		$this->componentName = 'Cheques';
		$this->status_ch = 'Elegir';


	}
	protected $casts = [
	    'fecha_emision_ch' => 'date:Y-m-d h:i:s',
	];

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

		if(strlen($this->search) > 0)
			$data = cheques::join('clientes_mostradors','clientes_mostradors.id','cheques.cliente_id')
			->select('cheques.*','clientes_mostradors.nombre as cliente')
			->where('emisor', 'like', '%' . $this->search . '%')
			->where('cheques.comercio_id', 'like', $comercio_id)
			->orderBy('cheques.id','desc')
			->paginate($this->pagination);
		else

			$data = cheques::join('clientes_mostradors','clientes_mostradors.id','cheques.cliente_id')
			->select('cheques.*','clientes_mostradors.nombre as cliente')
			->where('cheques.comercio_id', 'like', $comercio_id)
			->orderBy('cheques.id','desc')
			->paginate($this->pagination);

			$activo =  cheques::join('clientes_mostradors','clientes_mostradors.id','cheques.cliente_id')
			->select(cheques::raw('SUM(cheques.monto) as monto'))
			->where('cheques.comercio_id', 'like', $comercio_id)
			->where('cheques.status', 'like', 'Activo')
			->first();

			$vencido =  cheques::join('clientes_mostradors','clientes_mostradors.id','cheques.cliente_id')
			->select(cheques::raw('SUM(cheques.monto) as monto'))
			->where('cheques.comercio_id', 'like', $comercio_id)
			->where('cheques.status', 'like', 'Vencido')
			->first();

			$cobrado =  cheques::join('clientes_mostradors','clientes_mostradors.id','cheques.cliente_id')
			->select(cheques::raw('SUM(cheques.monto) as monto'))
			->where('cheques.comercio_id', 'like', $comercio_id)
			->where('cheques.status', 'like', 'Cobrado')
			->first();

			$incobrable =  cheques::join('clientes_mostradors','clientes_mostradors.id','cheques.cliente_id')
			->select(cheques::raw('SUM(cheques.monto) as monto'))
			->where('cheques.comercio_id', 'like', $comercio_id)
			->where('cheques.status', 'like', 'Incobrable')
			->first();

			$clientes = ClientesMostrador::where('comercio_id',$comercio_id)->get();

		return view('livewire.cheques.component',
		[
			'cheques' => $data,
			'activo' => $activo,
			'cobrado' => $cobrado,
			'vencido' => $vencido,
			'incobrable' => $incobrable,
			'clientes' => $clientes
		])
		->extends('layouts.theme.app')
		->section('content');
	}



	public function Edit($id)
	{
		$record = cheques::where('cheques.id',$id)->first();

		$this->emisor_ch = $record->emisor;
		$this->selected_id = $record->id;
		$this->monto_ch = $record->monto;
		$this->fecha_emision_ch = $record->fecha_emision;
		$this->fecha_cobro_ch = $record->fecha_cobro;
		$this->cliente_id_ch = $record->cliente_id;
		$this->banco_ch = $record->banco;
		$this->nro_cheque_ch = $record->nro_cheque;

		$this->emit('show-modal', 'show modal!');
	}



	    public function CambioEstado($chequeId)
	    {

	      $this->selected_id = $chequeId;

        $this->emit('estado','details loaded');

		}


	public function Store()
	{
		$rules = [
			'nro_cheque_ch' => 'required',
			'banco_ch' => 'required',
			'emisor_ch' => 'required',
			'cliente_id_ch' => 'required',
			'monto_ch' => 'required',
			'status_ch' => 'required|not_in:Elegir',
			'fecha_emision_ch' => 'required',
			'fecha_cobro_ch' => 'required',
			];

		$messages = [
			'nro_cheque_ch.required' => 'Numero de cheque es requerido',
			'banco_ch.required' => 'El nombre del banco es requerido',
			'emisor_ch.required' => 'El nombre y apellido del emisor es requerido',
			'cliente_id_ch.required' => 'El cliente es requerido',
			'status_ch.required' => 'El estado es requerido',
			'status_ch.not_in' => 'El estado es requerido',
			'fecha_emision_ch.required' => 'La fecha de emision es requerida',
			'fecha_cobro_ch.required' => 'La fecha de cobro es requerida',
		];

		$this->validate($rules, $messages);

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$cheques = DB::table('cheques')->insert([
			'nro_cheque' =>  $this->nro_cheque_ch,
			'banco'  => $this->banco_ch,
			'emisor' =>  $this->emisor_ch,
			'cliente_id' => $this->query_id,
			'monto' =>  $this->efectivo_real,
			'sale_id' => $sale,
			'comercio_id' => $comercio_id,
			'status' => 'Activo',
			'fecha_emision' => $this->fecha_emision_ch,
			'fecha_cobro' =>  $this->fecha_emision_ch
		]);

		$this->resetUI();
		$this->emit('category-added','Categoría Registrada');

	}

public function UpdateEstado($estado) {
	$cheque = cheques::find($this->selected_id);

	////////// WooCommerce ////////////
	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

$cheque->update([
	'status' => $estado
]);

$this->emit('estado-hide','');

}

	public function Update()
	{
		$rules =[
			'nro_cheque_ch' => "required|min:3",
			'banco_ch' => "required",
			'fecha_emision_ch' => "required",
			'fecha_cobro_ch' => "required",
			'emisor_ch' => "required",
			'cliente_id_ch' => "required",
			'monto_ch' => "required",
		];

		$messages =[
			'nro_cheque_ch' => "El numero de cheque es requerido",
			'banco_ch' => "El nombre de banco es requerido",
			'fecha_emision_ch' => "Inserte una fecha de emision",
			'fecha_cobro_ch' => "Inserte una fecha de cobro",
			'emisor_ch' => "Ingrese un emisor",
			'cliente_id_ch' => "Elija un cliente",
			'monto_ch' => "Ingrese el monto",
		];

		$this->validate($rules, $messages);


		$cheques = cheques::find($this->selected_id);

		$cheques->update([
			'nro_cheque' =>  $this->nro_cheque_ch,
			'banco' =>  $this->banco_ch,
			'fecha_emision' => $this->fecha_emision_ch,
			'fecha_cobro' =>  $this->fecha_cobro_ch,
			'emisor' => $this->emisor_ch,
			'cliente_id' =>  $this->cliente_id_ch,
			'monto' =>  $this->monto_ch,
        ]);
        
        
		$pagos = pagos_facturas::find($cheques->id_pago);

		$pagos->update([
		'monto' => $this->monto_ch,
		'eliminado' => 0,
		'tipo_pago' => 1,
		'cliente_id' => $this->cliente_id_ch
        ]);
        
        $recordatorios = recordatorios::where('cheque_id', $cheques->id)->first();
        
        if($recordatorios != null) {
           
        $recordatorios->update([
		'monto' => $this->monto_ch,
		'descripcion' => 'Cobrar cheque nro '.$this->nro_cheque_ch.' del emisor '.$this->emisor_ch.', por el monto de $ '.$this->monto_ch,
		'cliente_id' => $this->cliente_id_ch
        ]);
        
        }

		$this->resetUI();
		$this->emit('category-updated', 'Cheque Actualizado');

        $ventaId = $cheques->sale_id;
        
        $this->ActualizarEstadoDeuda($ventaId);


	}



   public function ActualizarEstadoDeuda($ventaId)
   {
     /////////////////   ACTUALIZAR ESTADO DE PAGO   /////////////////////


          $this->data_cash = Sale::select('sales.cash','sales.created_at as fecha_factura')
          ->where('sales.id', $ventaId)
          ->get();

          $this->data_total = Sale::select('sales.total','sales.recargo','sales.descuento')
          ->where('sales.id', $ventaId)
          ->get();

          $this->pagos2 = pagos_facturas::join('metodo_pagos as mp','mp.id','pagos_facturas.metodo_pago')
          ->select('mp.nombre as metodo_pago','pagos_facturas.id','pagos_facturas.recargo','pagos_facturas.monto','pagos_facturas.created_at as fecha_pago')
          ->where('pagos_facturas.id_factura', $ventaId)
          ->where('pagos_facturas.eliminado',0)
          ->get();


          $this->suma_monto = $this->pagos2->sum('monto');

          $this->tot = $this->data_total->sum('total');

          $this->rec = $this->pagos2->sum('recargo');


          $deuda = $this->tot - $this->suma_monto;



         $this->deuda_vieja = Sale::find($ventaId);

          $this->deuda_vieja->update([
            'deuda' => $deuda
            ]);


          ///////////////////////////////////////////////////////////////////
   }


	public function resetUI()
	{
		$this->name ='';
		$this->image = null;
		$this->search ='';
		$this->selected_id =0;
	}

	protected $listeners =[
		'deleteRow' => 'Destroy'
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



}
