<?php

namespace App\Http\Livewire;

use Livewire\Component;


// Trait

use App\Traits\WocommerceTrait;


use Illuminate\Support\Facades\Auth;
use App\Models\ecommerce;
use App\Models\wocommerce;
use App\Models\ecommerce_image;
use Illuminate\Validation\Rule;
use App\Models\bancos;

use Livewire\WithFileUploads;
use App\Models\ecommerce_cupon;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\Session;
use DB;


class WooCommerceController extends Component
{


	use WithFileUploads;
    use WithPagination;
    use WocommerceTrait;

    public $products, $comercio_id, $valido, $search, $name, $barcode, $price, $stock, $image, $selected_id, $cost, $cantidad, $componentName, $pageTitle, $slug, $nombre_metodo, $banco, $CBU, $CUIT, $titular, $metodo, $cupon_update, $mensaje_transferencia, $mensaje_efectivo, $slug_guardar, $datos_ecommerce, $mp_key, $mp_secret, $mensaje_mp, $cupon, $descuento, $tipo;

  	private $pagination = 25;

    public function paginationView()
    {
    return 'vendor.livewire.bootstrap';
    }

    public function mount() {
      $componentName = "Ajustes";
      $this->banco = "Elegir";
      $pageTitle = "Metodo de pago";
      $this->selected_id = 0;
      $this->productos_creados = [];
    }

    public function resetUI() {
      $this->selected_id = '';
      $this->cupon_ratio = '';
      $this->cupon =  '';
      $this->descuento = '';
    }

    // escuchar eventos
    protected $listeners = [
      'Habilitado' => 'Habilitado',
    ];

    public function render()
    {

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

      $this->comercio_id = $comercio_id;

      $this->ecommerce =  wocommerce::where('comercio_id', $comercio_id)->first();

			if($this->ecommerce != null) {

			$this->ck = $this->ecommerce->ck;
			$this->cs = $this->ecommerce->cs;
			$this->url = $this->ecommerce->url;
			$this->user = $this->ecommerce->user;
			$this->pass = $this->ecommerce->pass;
			$this->ecommerce_id = $this->ecommerce->id;
			
			$response = $this->checkCredentials($this->url,$this->ck,$this->cs);
			
			$this->valido = $response;

		} else {
			$this->ck = '';
			$this->cs = '';
			$this->url = '';
			$this->user = '';
			$this->pass = '';
			$this->ecommerce_id = 0;
		}



      return view('livewire.woocommerce.component', [
				'cs' => $this->cs,
        'ecommerce' => $this->ecommerce,
        'ecommerce_id' => $this->ecommerce_id,
				'user' => $this->user,
				'pass' => $this->pass,
      ])
      ->extends('layouts.theme-pos.app')
      ->section('content');

    }


public function Update() {

	$rules  =[
		'ck' => 'required',
		'cs' => 'required',
		'url' => 'required',
		'user' => 'required',
		'pass' => 'required',
	];

	$messages = [
		'ck.required' => 'Debe ingresar el Consumer Key',
		'cs.required' => 'Debe ingresar el Consumer secret',
		'url.required' => 'Debe ingresar la URL de su tienda',
		'ck.required' => 'Debe ingresar el usuario administrador',
		'ck.required' => 'Debe ingresar la contraseña del usuario administrador',
	];


	$this->validate($rules, $messages);

    
      $ecommerce = wocommerce::find($this->ecommerce_id);

        $ecommerce->update([
					'user' => $this->user,
					'pass' => $this->pass,
          'url' => $this->url,
					'ck' => $this->ck,
					'cs' => 	$this->cs,
        ]);

$this->emit('gestionar-updated','Datos del Wocommerce actualizados');


}




public function Delete() {


      $ecommerce = wocommerce::find($this->ecommerce_id);

        $ecommerce->delete();

$this->emit('gestionar-updated','Datos del Wocommerce desincronizados');

}



public function Store() {

	$rules  =[
		'ck' => 'required',
		'cs' => 'required',
		'url' => 'required',
		'user' => 'required',
		'pass' => 'required',
	];

	$messages = [
		'ck.required' => 'Debe ingresar el Consumer Key',
		'cs.required' => 'Debe ingresar el Consumer secret',
		'url.required' => 'Debe ingresar la URL de su tienda',
		'user.required' => 'Debe ingresar el usuario administrador',
		'pass.required' => 'Debe ingresar la contraseña del usuario administrador',
	];


	$this->validate($rules, $messages);
        
        
      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

				$ecommerce = wocommerce::create([
					'user' => $this->user,
					'pass' => $this->pass,
					'url' => $this->url,
					'ck' => $this->ck,
					'cs' => 	$this->cs,
					'comercio_id' => $comercio_id
				]);

      $this->emit('gestionar-updated','Datos del Wocommerce cargados');

 
}

public function GetWocommerceProductsList111($comercio_id){
    $respuesta = $this->GetWocommerceProductsList($comercio_id);

if ($respuesta['success']) {
    $this->emit('msg-success', $respuesta['message']);
} else {
    $this->emit('msg-error', $respuesta['message']);
}

}

}
