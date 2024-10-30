<?php

namespace App\Http\Livewire;

// Trait

use App\Models\User;
use App\Models\configuracion_cajas;
use App\Models\cajas;


use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;


class ConfiguracionCajaController extends Component
{

	use WithFileUploads;
	use WithPagination;
    
    public $componentName,$configuracion_cantidad_cajas,$configuracion_caja,$comercio_id;	

	public function mount()
	{
	    $this->estado_filtro = 0;
	    $this->accion_lote = 'Elegir';
		$this->pageTitle = 'Listado';
		$this->componentName = 'cajas';

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
        
        $this->GetConfiguracionCajas();
        
		return view('livewire.configuracion-cajas.component', [
		    ])
		->extends('layouts.theme-pos.app')
		->section('content');
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
            $this->emit("msg","Configuracion Actualizada");
        }
        
        // 26-6-2024
        public function UpdateConfiguracionCaja(){
        
        $cajas = cajas::where('estado',0)->where('comercio_id',$this->casa_central_id)->first();

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

}
