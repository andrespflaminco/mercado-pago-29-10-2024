<?php

namespace App\Http\Livewire;

use App\Models\ComisionUsuario;
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


class ComisionesController extends Component
{

	use WithFileUploads;
	use WithPagination;

	public $name, $search, $image, $agregar,$id_check,$selected_id, $pageTitle, $componentName, $wc_category_id;
	private $pagination = 25;
	private $wc_category;
	public $comision, $vendedores, $user_id;

	public function mount()
	{
	    $this->estado_filtro = 0;
	    $this->accion_lote = 'Elegir';
		$this->pageTitle = 'Listado';
		$this->componentName = 'Categorías';
		$this->vendedor_id = "Elegir";


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

        $this->vendedores = User::where('comercio_id',$this->comercio_id)->get();
        	
			$data = User::leftjoin('comision_usuarios','comision_usuarios.user_id','users.id');
			if(strlen($this->search) > 0){
			$data = $data->where('users.name', 'like', '%' . $this->search . '%');
			}
			$data = $data->where('users.comercio_id', $this->casa_central_id)
			->select('users.name','users.id as user_id','users.comercio_id','users.casa_central_user_id as casa_central_id','comision_usuarios.id','comision_usuarios.porcentaje_comision')
			->paginate($this->pagination);

        //dd($data);
        
		return view('livewire.comisiones.component', [
		    'comisiones' => $data,
		    'vendedores' => $this->vendedores
		    ])
		->extends('layouts.theme-pos.app')
		->section('content');
	}



	public function Edit($id)
	{
	    
	    $this->vendedores = User::where('comercio_id',$this->comercio_id)->get();
	    $this->agregar = 1;
		$record = ComisionUsuario::find($id);
		$this->selected_id = $record->id;
		$this->comision = $record->porcentaje_comision;
		$this->vendedor_id = $record->user_id;
		$this->image = null;

		$this->emit('show-modal', 'show modal!');
	}


    public function Agregar($user_id) {
        $this->vendedor_id = $user_id;
        $this->agregar = 1;
    }
    
	public function Store()
	{
	    
	    if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		
        if($this->vendedor_id == "Elegir"){
            $this->emit("msg-error","Debe elegir un vendedor");
            return;
        }

        if($this->comision == 0){
            $this->emit("msg-error","Debe elegir una comision");
            return;
        }

		$category = ComisionUsuario::create([
			'porcentaje_comision' => $this->comision,
			'user_id' => $this->vendedor_id,
			'comercio_id' => $comercio_id,
			'casa_central_id' => $this->casa_central_id
		]);
        
        // Si usa wocommerce

		$this->resetUI();
		$this->emit('category-added','Comision Registrada');

	}


	public function Update()
	{
	    
	    if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		
		
        if($this->vendedor_id == "Elegir"){
            $this->emit("msg-error","Debe elegir un vendedor");
            return;
        }

        if($this->comision == 0){
            $this->emit("msg-error","Debe elegir una comision");
            return;
        }

        $comision = ComisionUsuario::find($this->selected_id);
		
		$comision->update([
			'porcentaje_comision' => $this->comision,
		]);

		$this->resetUI();
		$this->emit('category-updated', 'Comision Actualizada');

	}


	public function resetUI()
	{
		$this->name ='';
		$this->image = null;
		$this->search ='';
		$this->selected_id =0;
		$this->agregar = 0;
	}

	protected $listeners =[
		'deleteRow' => 'Destroy',
		'Restaurar' => 'Restaurar',
        'accion-lote' => 'AccionEnLote'
	];


	public function Destroy(ComisionUsuario $comision)
	{

		$comision->delete();

		$this->resetUI();
		$this->emit('category-updated', 'Comision Eliminada');

	}


	public function Filtro($estado)
	{
	   $this->estado_filtro = $estado;
	   
	}
	
	    // Eliminar en lote 
    
		
	public function Restaurar(ComisionUsuario $comision)
	{
		$comision->update([
			'eliminado' => 0
		]);

		$this->resetUI();
		$this->emit('category-updated', 'Comision Restaurada');
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
    
    $gastos_checked = ComisionUsuario::select('comision_usuario.id','comision_usuario.comercio_id')->whereIn('comision_usuario.id',$ids)->get();

    $this->id_check = [];
    
    if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

    foreach($gastos_checked as $pc) {
    
    $pc->eliminado = $estado;
    $pc->save();
    }
    
    
    $this->id_check = [];
    $this->accion_lote = 'Elegir';
    
     $this->emit('global-msg',"COMISION ".$msg);
    
    }



}
