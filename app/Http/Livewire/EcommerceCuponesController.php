<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;
use App\Models\ecommerce;

use Illuminate\Validation\Rule;
use App\Models\bancos;
use App\Models\ecommerce_cupon;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\Session;
use DB;


class EcommerceCuponesController extends Component
{

    use WithPagination;

    public $products, $comercio_id, $search, $name, $barcode, $price, $stock, $image, $selected_id, $cost, $cantidad, $componentName, $pageTitle, $slug, $nombre_metodo, $banco, $CBU, $CUIT, $titular, $metodo, $cupon_update, $mensaje_transferencia, $mensaje_efectivo, $datos_ecommerce, $mp_key, $mp_secret, $mensaje_mp, $cupon, $descuento;

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

      $this->ecommerce =  ecommerce::where('comercio_id', $comercio_id)->first();

      return view('livewire.ecommerce_cupones.component', [
        'ecommerce' => $this->ecommerce,
        'id' => $this->ecommerce->id,
        'bancos' => bancos::orderBy('nombre','desc')->where('comercio_id', $comercio_id)->get(),
        'cupones' => ecommerce_cupon::where('comercio_id', $comercio_id)->get()
      ])
      ->extends('layouts.theme.app')
      ->section('content');

    }




        public function Edit(ecommerce_cupon $cupon)
        {

          $this->selected_id = $cupon->id;
          $this->cupon_ratio = $cupon->cupon;
          $this->cupon =  $this->cupon_ratio;
          $this->descuento = $cupon->descuento;

          $this->emit('gestionar-show','Show modal');
        }



public function Update() {


      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;


    		$rules  =[
    			'cupon' => ['required',Rule::unique('ecommerce_cupons')->ignore($this->selected_id)->where('comercio_id',$comercio_id)],
    			'descuento' => 'required'
    		];

    		$messages = [
    			'cupon.required' => 'El codigo del cupon requerido',
    			'cupon.unique' => 'Ya hay un cupon con ese nombre',
    			'descuento.required' => 'El % de descuento es requerido.'
    		];

    		$this->validate($rules, $messages);


        $cupon_update = ecommerce_cupon::find($this->selected_id);

        $cupon_update->update([
          'cupon' => $this->cupon,
          'descuento' => $this->descuento,
          'comercio_id'  => $comercio_id
        ]);


$this->emit('gestionar-updated','Metodo de pago actualizado');

}

public function Store() {


    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;


  		$rules  =[
  			'cupon' => ['required',Rule::unique('ecommerce_cupons')->where('comercio_id',$comercio_id)],
  			'descuento' => 'required'
  		];

  		$messages = [
  			'cupon.required' => 'El codigo del cupon requerido',
  			'cupon.unique' => 'Ya hay un cupon con ese nombre',
  			'descuento.required' => 'El % de descuento es requerido.'
  		];

  		$this->validate($rules, $messages);



  $ecommerce = ecommerce_cupon::create([
    'cupon' => $this->cupon,
    'comercio_id' => $comercio_id,
    'descuento' => $this->descuento
  ]);

$this->emit('gestionar-updated','El cupon ha sido agregado');

}





}
