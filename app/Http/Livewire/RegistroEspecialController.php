<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Redirect;

use Livewire\WithPagination;
use App\Models\Product;
use App\Models\provincias;
use App\Models\productos_lista_precios;
use App\Models\planes_suscripcion_landings;

use App\Models\Category;
use App\Models\sucursales;
use App\Models\etiquetas;
use App\Models\etiquetas_productos;
use App\Models\User;
use Carbon\Carbon;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use App\Models\ecommerce_envio;
use App\Models\ClientesMostrador;
use App\Models\ecommerce;
use App\Services\CartEcommerce;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\Session;
use App\Models\metodo_pago;
use App\Models\cajas;
use App\Models\historico_stock;
use App\Models\bancos;
use DB;

// Suscripciones
use App\Models\Suscripcion;
use App\Models\SuscripcionCobros;
use App\Models\planes_suscripcion;

//Traits
use App\Traits\ConsumesExternalServices;

class RegistroEspecialController extends Component
{
    
	use ConsumesExternalServices;
    use WithPagination;

    public $comercio_id, $profile;
    public $name;
    public $email;
    public $phone;
    public $slug;
    public $rubro;
    public $password;
    public $password_confirmation;
	
	protected $baseUri;	
	protected $key;	
	protected $token;
	protected $preapproval_plan_id;

	public $planId;
	public $initPoint;
	public $suscripcionStatus;
	public $origen,$user,$intencion_compra;

    public function mount($slug) {
      $this->slug = $slug;
      $this->intencion_compra = 0;
    }

    public function render()
    {
 
     $countries = [
        ['name' => 'Argentina', 'phone_code' => '+54','phone_code_slug' => '549'],
        ['name' => 'Bolivia', 'phone_code' => '+591','phone_code_slug' => '5919'],
        ['name' => 'Brasil', 'phone_code' => '+55','phone_code_slug' => '559'],
        ['name' => 'Canadá', 'phone_code' => '+1','phone_code_slug' => '19'],
        ['name' => 'Chile', 'phone_code' => '+56','phone_code_slug' => '569'],
        ['name' => 'Colombia', 'phone_code' => '+57','phone_code_slug' => '579'],
        ['name' => 'Costa Rica', 'phone_code' => '+506','phone_code_slug' => '5069'],
        ['name' => 'Cuba', 'phone_code' => '+53','phone_code_slug' => '539'],
        ['name' => 'Ecuador', 'phone_code' => '+593','phone_code_slug' => '5939'],
        ['name' => 'El Salvador', 'phone_code' => '+503','phone_code_slug' => '5039'],
        ['name' => 'Estados Unidos', 'phone_code' => '+1','phone_code_slug' => '19'],
        ['name' => 'Guatemala', 'phone_code' => '+502','phone_code_slug' => '5029'],
        ['name' => 'Honduras', 'phone_code' => '+504','phone_code_slug' => '5049'],
        ['name' => 'México', 'phone_code' => '+52','phone_code_slug' => '529'],
        ['name' => 'Nicaragua', 'phone_code' => '+505','phone_code_slug' => '5059'],
        ['name' => 'Panamá', 'phone_code' => '+507','phone_code_slug' => '5079'],
        ['name' => 'Paraguay', 'phone_code' => '+595','phone_code_slug' => '5959'],
        ['name' => 'Perú', 'phone_code' => '+51','phone_code_slug' => '519'],
        ['name' => 'Uruguay', 'phone_code' => '+598','phone_code_slug' => '5989'],
    ];
    
      $this->intencion_compra = 0;
      
      return view('auth.register-especial', [
        'provincias' => provincias::orderBy('provincia','asc')->get(),
        'countries' => $countries
      ])
      ->extends('layouts.theme-pos-especial.app')
      ->section('content');

    }

}
