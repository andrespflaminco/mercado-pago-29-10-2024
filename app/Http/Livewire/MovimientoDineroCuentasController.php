<?php

namespace App\Http\Livewire;

// Trait

use App\Traits\WocommerceTrait;
use App\Traits\BancosTrait;




use App\Models\movimiento_dinero_cuentas;
use App\Models\movimiento_dinero_cuentas_detalle;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\sucursales;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use App\Models\wocommerce;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;



class MovimientoDineroCuentasController extends Component
{

	use WithFileUploads;
	use WithPagination;
	use BancosTrait;
	use WocommerceTrait;



	public $name, $search, $image, $agregar,$id_check,$selected_id, $pageTitle, $componentName, $wc_category_id;
	public $bancos;
	private $pagination = 25;
	private $wc_category;
	
	public $banco_origen_form,$banco_destino_form,$monto_form, $url_comprobante_form, $nro_comprobante_form;

	public function mount()
	{
	    $this->estado_filtro = 0;
	    $this->accion_lote = 'Elegir';
	    $this->banco_origen_form = 'Elegir';
	    $this->banco_destino_form = 'Elegir';
		$this->pageTitle = 'Listado';
		$this->componentName = 'Categorías';


	}

	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function render()
	{
        if(Auth::user()->comercio_id != 1)
        $this->comercio_id = Auth::user()->comercio_id;
        else
        $this->comercio_id = Auth::user()->id;
        
        $this->tipo_usuario = User::find($this->comercio_id);
        $this->sucursal_id = $this->comercio_id;
        		    
        if($this->tipo_usuario->sucursal != 1) {
        
        $this->casa_central_id = $this->comercio_id;
        	
        } else {
        	  
        $this->casa_central = sucursales::where('sucursal_id', $this->comercio_id)->first();
        $this->casa_central_id = $this->casa_central->casa_central_id;
        
        }

        $this->bancos = $this->GetBancosTrait($this->comercio_id);
        
        $data = movimiento_dinero_cuentas::join('bancos as bancos_origen', 'bancos_origen.id', '=', 'movimiento_dinero_cuentas.banco_origen_id')
            ->join('bancos as bancos_destino', 'bancos_destino.id', '=', 'movimiento_dinero_cuentas.banco_destino_id');
 		    
 		    if(strlen($this->search) > 0){
		    $data = $data->where('bancos_origen.nombre', 'like', '%' . $this->search . '%')
            ->orWhere('bancos_destino.nombre', 'like', '%' . $this->search . '%');
            
		    }
		    
		    $data = $data->where('movimiento_dinero_cuentas.comercio_id', $this->comercio_id)
            ->where('movimiento_dinero_cuentas.eliminado', $this->estado_filtro)
            ->select('movimiento_dinero_cuentas.*', 'bancos_origen.nombre as banco_origen_name', 'bancos_destino.nombre as banco_destino_name')
            ->paginate($this->pagination);
            
		return view('livewire.movimiento-dinero-cuentas.component', [
		    'movimientos' => $data,
		    'bancos' => $this->bancos
		    ])
		->extends('layouts.theme-pos.app')
		->section('content');
	}



	public function Edit($id)
	{
	    
	    $this->agregar = 1;
		$record = movimiento_dinero_cuentas::find($id);
		$this->banco_origen_form = $record->banco_origen_id;
		$this->banco_destino_form = $record->banco_destino_id;
		$this->monto_form = $record->monto;
		$this->url_comprobante_form = $record->url_comprobante;
		$this->nro_comprobante_form = $record->nro_comprobante;
		$this->selected_id = $record->id;
		$this->image = null;

		$this->emit('show-modal', 'show modal!');
	}


    public function Agregar() {
        $this->agregar = 1;
    }
    
	public function Store()
	{
	    
	    if($this->banco_origen_form == $this->banco_destino_form){
	    $this->emit("msg-error","La cuenta de origen y de destino no puede ser la misma");
	    return;
	    }
	    
	    if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		
		$rules = [
			'banco_origen_form' => 'required|not_in:Elegir',
            'banco_destino_form' => 'required|not_in:Elegir',
            'monto_form' => 'required',
		];

		$messages = [
			'monto_form.required' => 'El monto es requerido',
			'banco_origen_form.required' => 'La cuenta de origen es requerida',
			'banco_destino_form.required' => 'La cuenta de destino es requerida',
			'banco_origen_form.not_in' => 'La cuenta de origen es requerida',
			'banco_destino_form.not_in' => 'La cuenta de destino es requerida',
		];

		$this->validate($rules, $messages);

        $movimiento_dinero_cuentas = movimiento_dinero_cuentas::create([
            'banco_origen_id' => $this->banco_origen_form,
            'banco_destino_id' => $this->banco_destino_form,
            'monto' => $this->monto_form,
            'url_comprobante' => null,
            'nro_comprobante' => $this->nro_comprobante_form,
			'comercio_id' => $comercio_id
		]);
        
        if($movimiento_dinero_cuentas){
        
        movimiento_dinero_cuentas_detalle::create([
			'banco_id' => $this->banco_origen_form,
			'tipo' => 'Origen',
            'monto' => -$this->monto_form,
			'comercio_id' => $comercio_id,
			'movimiento_dinero_cuenta_id' => $movimiento_dinero_cuentas->id
		]);
		
        movimiento_dinero_cuentas_detalle::create([
			'banco_id' => $this->banco_destino_form,
			'tipo' => 'Destino',
            'monto' => $this->monto_form,
			'comercio_id' => $comercio_id,
			'movimiento_dinero_cuenta_id' => $movimiento_dinero_cuentas->id
		]);
		
        }

		if($this->url_comprobante_form)
		{
			$customFileName = uniqid() . '_.' . $this->url_comprobante_form->extension();
			$this->url_comprobante_form->storeAs('public/comprobantes', $customFileName);
			$movimiento_dinero_cuentas->url_comprobante = $customFileName;
			$movimiento_dinero_cuentas->save();
		}

		$this->resetUI();
		$this->emit('category-added','Movimiento Registrado');

	}


	public function Update()
	{

	    if($this->banco_origen_form == $this->banco_destino_form){
	    $this->emit("msg-error","La cuenta de origen y de destino no puede ser la misma");
	    return;
	    }
	    
	    if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		
		
		$rules = [
			'banco_origen_form' => 'required|not_in:Elegir',
            'banco_destino_form' => 'required|not_in:Elegir',
            'monto_form' => 'required',
		];

		$messages = [
			'monto_form.required' => 'El monto es requerido',
			'banco_origen_form.required' => 'La cuenta de origen es requerida',
			'banco_destino_form.required' => 'La cuenta de destino es requerida',
			'banco_origen_form.not_in' => 'La cuenta de origen es requerida',
			'banco_destino_form.not_in' => 'La cuenta de destino es requerida',
		];

		$this->validate($rules, $messages);
        
        
        ////////////////////////////////////////////////
        
        $movimiento_dinero_cuentas = movimiento_dinero_cuentas::find($this->selected_id);

        $movimiento_dinero_cuentas->update([
            'banco_origen_id' => $this->banco_origen_form,
            'banco_destino_id' => $this->banco_destino_form,
            'monto' => $this->monto_form,
            'url_comprobante' => null,
            'nro_comprobante' => $this->nro_comprobante_form,
			'comercio_id' => $comercio_id
		]);
        
        
        
        $origen = movimiento_dinero_cuentas_detalle::where('movimiento_dinero_cuenta_id',$this->selected_id)->where('tipo','Origen')->first();
        
        $origen->update([
			'banco_id' => $this->banco_origen_form,
            'monto' => -$this->monto_form
		]);
		
		$destino = movimiento_dinero_cuentas_detalle::where('movimiento_dinero_cuenta_id',$this->selected_id)->where('tipo','Destino')->first();
        
        $destino->update([
			'banco_id' => $this->banco_destino_form,
            'monto' => $this->monto_form
		]);
		
        


		if($this->url_comprobante_form)
		{
			$customFileName = uniqid() . '_.' . $this->url_comprobante_form->extension();
			$this->image->storeAs('public/comprobantes', $customFileName);
			$ComprobanteName = $movimiento_dinero_cuentas->url_comprobante;

			$movimiento_dinero_cuentas->url_comprobante = $customFileName;
			$movimiento_dinero_cuentas->save();

			if($ComprobanteName !=null)
			{
				if(file_exists('storage/comprobantes' . $ComprobanteName))
				{
					unlink('storage/comprobantes' . $ComprobanteName);
				}
			}

		}

		$this->resetUI();
		$this->emit('category-updated', 'Movimiento entre cuentas Actualizado');

	}


	public function resetUI()
	{
		$this->name ='';
		$this->image = null;
		$this->search ='';
		$this->selected_id =0;
		$this->agregar = 0;
		$this->banco_origen_form = 'Elegir';
	    $this->banco_destino_form = 'Elegir';
	    $this->monto_form = null;
	    $this->url_comprobante_form = null;
	    $this->nro_comprobante_form = null;
	}

	protected $listeners =[
		'deleteRow' => 'Destroy',
		'Restaurar' => 'Restaurar',
        'accion-lote' => 'AccionEnLote'
	];


	public function Destroy(movimiento_dinero_cuentas $movimiento)
	{
        
		$movimiento->eliminado = 1;
		$movimiento->save();
		
		$movimientos = movimiento_dinero_cuentas_detalle::where('movimiento_dinero_cuenta_id',$movimiento->id)->get();
		
		foreach($movimientos as $p) {
		$movement = movimiento_dinero_cuentas_detalle::find($p->id);
		$movement->eliminado = 1;
		$movement->save();    
		} 


		$this->resetUI();
		$this->emit('category-updated', 'Movimiento Eliminado');

	}

	public function Restaurar(movimiento_dinero_cuentas $movimiento)
	{
        
		$movimiento->eliminado = 0;
		$movimiento->save();
		
		$movimientos = movimiento_dinero_cuentas_detalle::where('movimiento_dinero_cuenta_id',$movimiento->id)->get();
		
		foreach($movimientos as $p) {
		
		$movement = movimiento_dinero_cuentas_detalle::find($p->id);
		$movement->eliminado = 0;
		$movement->save();    
		} 


		$this->resetUI();
		$this->emit('category-updated', 'Movimiento Restaurado');

	}

	public function Filtro($estado)
	{
	   $this->estado_filtro = $estado;
	   
	}
	
	    // Eliminar en lote 

	public function AccionEnLote($ids, $id_accion)
    {
    
    if($id_accion == 1) {
        $estado = 0;
        $msg = 'RESTAURADOS';
    } else {
        $estado = 1;
        $msg = 'ELIMINADOS';
    }
    
    $movimientos_checked = movimiento_dinero_cuentas::select('movimiento_dinero_cuentas.id','movimiento_dinero_cuentas.comercio_id')->whereIn('movimiento_dinero_cuentas.id',$ids)->get();

    $this->id_check = [];
    
    if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

    
    foreach($movimientos_checked as $pc) {
    
    $mc = movimiento_dinero_cuentas::find($pc->id);
    
    if($estado == 0){
    $this->Restaurar($mc);    
    }    
    if($estado == 1){
    $this->Destroy($mc);    
    }
    
    }
    
    
    $this->id_check = [];
    $this->accion_lote = 'Elegir';
    
     $this->emit('global-msg',"MOVIMIENTOS ".$msg);
    
    }
    
    public function Sincronizar($categoria_id) {
        
        $this->FindOrCreateCategoryByName($categoria_id);
    }



}
