<?php

namespace App\Http\Livewire;

// Trait

use App\Models\User;
use App\Models\configuracion_impresion;


use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;


class ConfiguracionImpresionController extends Component
{

	use WithFileUploads;
	use WithPagination;
    
    public $componentName,$configuracion_impresion,$size,$muestra_cta_cte;	

	public function mount()
	{
	    $this->estado_filtro = 0;
	    $this->accion_lote = 'Elegir';
		$this->pageTitle = 'Listado';
		$this->componentName = 'impresion';


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
        
        $configuracion_impresion = configuracion_impresion::where('user_id',Auth::user()->id)->first();
        if($configuracion_impresion == null){
           $this->size = 58; 
           $this->muestra_cta_cte = false;
        } else {
            $this->size = $configuracion_impresion->size; 
            $this->muestra_cta_cte = $configuracion_impresion->muestra_cta_cte;
        }
        
        
        
		return view('livewire.configuracion-impresion.component', [
		    'size' => $this->size
		    ])
		->extends('layouts.theme-pos.app')
		->section('content');
	}


	public function CreateOrUpdate()
	{
	    
	    if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		
        configuracion_impresion::UpdateOrCreate([
            'user_id' => Auth::user()->id
            ],
            [
            'size' => $this->size,
            'user_id' => Auth::user()->id,
            'comercio_id' => $comercio_id,
            'muestra_cta_cte' => $this->muestra_cta_cte
            ]);
            
		$this->emit('msg', 'Configuracion Actualizada');
		$this->render();

	}


}
