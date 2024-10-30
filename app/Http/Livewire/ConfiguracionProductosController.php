<?php

namespace App\Http\Livewire;

// Trait

use App\Models\User;
use App\Models\configuracion_impresion;
use App\Traits\ConfiguracionProductsTrait;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;


class ConfiguracionProductosController extends Component
{

	use WithFileUploads;
	use WithPagination;
	use ConfiguracionProductsTrait;
    
    public $componentName,$configuracion_ver,$casa_central_id;	

	public function mount()
	{
	    $this->estado_filtro = 0;
	    $this->accion_lote = 'Elegir';
		$this->pageTitle = 'Listado';
		$this->componentName = 'productos';
		$this->configuracion_ver = "codigos";
		$this->casa_central_id = auth()->user()->casa_central_user_id;
		$this->GetConfiguracion();
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

        
		return view('livewire.configuracion-productos.component', [
		    ])
		->extends('layouts.theme-pos.app')
		->section('content');
	}


}
