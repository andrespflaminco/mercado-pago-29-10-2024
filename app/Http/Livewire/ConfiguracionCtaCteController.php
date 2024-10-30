<?php

namespace App\Http\Livewire;

// Trait

use App\Models\User;
use App\Models\configuracion_ctas_ctes;


use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;


class ConfiguracionCtaCteController extends Component
{

	use WithFileUploads;
	use WithPagination;
    
    public $componentName,$configuracion_valor, $configuracion_sucursales_agregan_pago;

	public function mount()
	{
	    $this->estado_filtro = 0;
	    $this->accion_lote = 'Elegir';
		$this->pageTitle = 'Listado';
		$this->componentName = 'cta_cte';


        $this->GetConfiguracionCtaCte();

	}

  public function GetConfiguracionCtaCte(){
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    
    $configuracion_ctas_ctes = configuracion_ctas_ctes::where('comercio_id',$comercio_id)->first();
    
    if($configuracion_ctas_ctes == null){
    $this->valor = "por_sucursal";
    $this->sucursales_agregan_pago = 0;   
    $this->configuracion_valor = "por_sucursal";
    $this->configuracion_sucursales_agregan_pago = 0;   
    } else {
    $this->valor = $configuracion_ctas_ctes->valor;
    $this->sucursales_agregan_pago = $configuracion_ctas_ctes->sucursales_agregan_pago;  
    $this->configuracion_valor = $configuracion_ctas_ctes->valor;
    $this->configuracion_sucursales_agregan_pago = $configuracion_ctas_ctes->sucursales_agregan_pago;  
    }

  }
  
  public function CerrarModalConfiguracion(){
//  $this->ver_configuracion = 0;  
  $this->emit("msg","Configuracion actualizada");
  $this-> GetConfiguracionCtaCte();
  }
  
  public function UpdateConfiguracion(){
  $configuracion_ctas_ctes = configuracion_ctas_ctes::updateOrCreate(
      [
      'comercio_id' => $this->comercio_id
      ],
      [
      'valor' => $this->configuracion_valor ,
      'sucursales_agregan_pago' => $this->configuracion_sucursales_agregan_pago 
      ]);    
      
      $this->CerrarModalConfiguracion();
      $this->mount();
      $this->render();
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

        
        
        
		return view('livewire.configuracion-cta-cte.component', [
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
