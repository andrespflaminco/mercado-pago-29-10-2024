<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;
use App\Models\ecommerce;
use App\Models\bancos;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\Session;
use DB;


class EcommerceEnviosController extends Component
{

    use WithPagination;

    public $products, $comercio_id, $search, $name, $barcode, $price, $stock, $image, $selected_id, $cost, $cantidad, $componentName, $pageTitle, $slug, $nombre_metodo, $banco, $CBU, $CUIT, $titular, $metodo, $mensaje_transferencia, $mensaje_efectivo, $datos_ecommerce, $mp_key, $mp_secret, $mensaje_mp;

  	private $pagination = 25;

    public function paginationView()
    {
    return 'vendor.livewire.bootstrap';
    }

    public function mount() {
      $componentName = "Ajustes";
      $this->banco = "Elegir";
      $pageTitle = "Metodo de pago";
    }

    public function resetUI() {

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

      return view('livewire.ecommerce_envios.component', [
        'ecommerce' => $this->ecommerce,
        'id' => $this->ecommerce->id,
        'bancos' => bancos::orderBy('nombre','desc')->where('comercio_id', $comercio_id)->get()
      ])
      ->extends('layouts.theme-pos.app')
      ->section('content');

    }




        public function SaveSlug() {

          if(Auth::user()->comercio_id != 1)
          $comercio_id = Auth::user()->comercio_id;
          else
          $comercio_id = Auth::user()->id;

          $ecommerce = ecommerce::create([
            'slug' => $this->slug,
            'comercio_id' => $comercio_id,
            'mensaje_efectivo' => 'Paga al momento de la entrega.',
            'mensaje_transferencia' => 'Realiza tu pago en nuestra cuenta bancaria. Por favor, usa el número del pedido como referencia de pago, una vez realizada la transferencia envianos el comprobante.',
            'mensaje_mp' => 'Sera redirigido al checkout de Mercado Pago.'
          ]);

          $this->slug = '';

          		$this->emit('slug-added', 'Tienda online creada');

        }


        public function Edit($metodo_ecommerce, $metodo)
        {

        $this->metodo = $metodo;


        //////////// CONTRAREEMBOLSO /////////////////////


        if($this->metodo == 1) {

          $this->nombre_metodo = "Contrareembolso";

          $datos_ecommerce = ecommerce::find($metodo_ecommerce);

          $this->mensaje_efectivo = $datos_ecommerce->mensaje_efectivo;


        }

        ////////////// TRANSFERENCIA /////////////////

        if($this->metodo == 2) {

          $this->nombre_metodo = "Transferencia";

          $datos_ecommerce = ecommerce::find($metodo_ecommerce);

          $this->mensaje_transferencia = $datos_ecommerce->mensaje_transferencia;


        }

        ///////////// MERCADO PAGO //////////////////////

        if($this->metodo == 3) {

          $this->nombre_metodo = "Mercado Pago";

          $datos_ecommerce = ecommerce::find($metodo_ecommerce);

          $this->mensaje_mp = $datos_ecommerce->mensaje_mp;

        }

          $this->emit('gestionar-show','Show modal');
        }


public function Habilitado($estado,$metodo) {

//dd($metodo);

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $this->ecommerce =  ecommerce::where('comercio_id', $comercio_id)->first();

  if($metodo == 1) {


    $this->ecommerce->update([
      'retiro_habilitado' => $estado
      ]);
  }

  if($metodo == 2) {

    $this->ecommerce->update([
      'envio_habilitado' => $estado
      ]);
  }

}


public function Update($metodo) {

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $this->ecommerce =  ecommerce::where('comercio_id', $comercio_id)->first();

  if($metodo == 1) {


    $this->ecommerce->update([
      'mensaje_efectivo' => $this->mensaje_efectivo
      ]);
  }

  if($metodo == 2) {

    $this->ecommerce->update([
      'mensaje_transferencia' => $this->mensaje_transferencia,
      'banco_id' => $this->banco
      ]);
  }

  if($metodo == 3) {

    $this->ecommerce->update([
      'mensaje_mp' => $this->mensaje_mp,
      'mp_key' => $this->mp_key,
      'mp_secret' => $this->mp_secret
      ]);
  }

$this->emit('gestionar-updated','Metodo de pago actualizado');

}





}
