<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;
use App\Models\ecommerce;
use App\Models\ecommerce_image;
use Illuminate\Validation\Rule;
use App\Models\bancos;

use Livewire\WithFileUploads;
use App\Models\ecommerce_cupon;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\Session;
use DB;


class EcommerceAjustesController extends Component
{


	use WithFileUploads;
    use WithPagination;

    public $products, $comercio_id, $search, $name, $barcode, $price, $stock, $image, $selected_id, $cost, $cantidad, $componentName, $pageTitle, $slug, $nombre_metodo, $banco, $CBU, $CUIT, $titular, $metodo, $cupon_update, $mensaje_transferencia, $mensaje_efectivo, $slug_guardar, $datos_ecommerce, $mp_key, $mp_secret, $mensaje_mp, $cupon, $descuento, $tipo;

    public $color, $background_color;
    
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

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

      $this->ecommerce =  ecommerce::where('comercio_id', $comercio_id)->first();
			
			$this->color =  $this->ecommerce->color;
			
			$this->background_color =  $this->ecommerce->background_color;    }

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

			$this->slug =  $this->ecommerce->slug;


			$this->tipo = $this->ecommerce->tipo;
			
			$this->comunicacion = $this->ecommerce->comunicacion;
			
			$this->forma_registro = $this->ecommerce->registro;


      return view('livewire.ecommerce_ajustes.component', [
        'imagenes' => ecommerce_image::where('comercio_id', $comercio_id)->where('eliminado',0)->get(),
        'ecommerce' => $this->ecommerce,
        'id' => $this->ecommerce->id,
        'bancos' => bancos::orderBy('nombre','desc')->where('comercio_id', $comercio_id)->get(),
        'cupones' => ecommerce_cupon::where('comercio_id', $comercio_id)->get()
      ])
      ->extends('layouts.theme-pos.app')
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

	$rules  =[
		'slug' => ['required','regex:/^[^\s]*$/',Rule::unique('ecommerces')->ignore($this->selected_id)],
	];

	$messages = [
		'slug.unique' => 'El slug no esta disponible, intente otro.',
		'slug.regex' => 'El slug no puede tener espacios.',
	];


	$this->validate($rules, $messages);

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

        $ecommerce = ecommerce::where('comercio_id', $comercio_id)->first();

        $ecommerce->update([
          'slug' => $this->slug
        ]);


$this->emit('gestionar-updated','Slug actualizado');

}


public function TipoEcommerce($value)
{
	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

		$ecommerce = ecommerce::where('comercio_id', $comercio_id)->first();

		$ecommerce->update([
			'tipo' => $value
		]);

		$this->emit('gestionar-updated','Forma de mostrar la tienda actualizado');

}

public function CambiarColor()
{
	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

		$ecommerce = ecommerce::where('comercio_id', $comercio_id)->first();

		$ecommerce->update([
			'color' => $this->color
		]);

		$this->emit('gestionar-updated','Color de los textos de la tienda actualizado');

}

public function CambiarFondo()
{
	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

		$ecommerce = ecommerce::where('comercio_id', $comercio_id)->first();

		$ecommerce->update([
			'background_color' => $this->background_color
		]);

		$this->emit('gestionar-updated','Color de la tienda actualizado actualizado');

}

public function FormaComunicacion($value)
{
	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

		$ecommerce = ecommerce::where('comercio_id', $comercio_id)->first();

		$ecommerce->update([
			'comunicacion' => $value
		]);

		$this->emit('gestionar-updated','Forma de comunicacion con el cliente actualizada');

}


public function FormaRegistro($value)
{
	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

		$ecommerce = ecommerce::where('comercio_id', $comercio_id)->first();

		$ecommerce->update([
			'registro' => $value
		]);

		$this->emit('gestionar-updated','Valor actualizado');

}


public function StoreImagen() {

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $imagenes = ecommerce_image::create([
    'comercio_id' => $comercio_id,
    'eliminado' => 0
  ]);

  		if($this->image)
  		{
  			$customFileName = uniqid() . '_.' . $this->image->extension();
  			$this->image->storeAs('public/imagenes', $customFileName);
  			$imagenes->imagen = $customFileName;
  			$imagenes->save();
  		}


}


public function DestroyImage($id) {


        $ecommerce_image = ecommerce_image::find($id);

        $ecommerce_image->update([
          'eliminado' => 1
        ]);


$this->emit('gestionar-updated','Imagen eliminada');

}



}
