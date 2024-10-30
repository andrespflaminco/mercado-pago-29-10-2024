<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

use Livewire\WithPagination;
use App\Models\Product;
use App\Models\provincias;
use App\Models\etiquetas;
use App\Models\etiquetas_productos;
use App\Models\Category;
use App\Models\User;
use App\Models\ecommerce_mp_pago;
use Illuminate\Http\Request;
use App\Models\SaleDetail;
use App\Models\ecommerce_envio;
use App\Models\ecommerce;
use App\Services\CartEcommerce;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\Session;
use App\Models\metodo_pago;
use App\Models\cajas;
use App\Models\historico_stock;
use App\Models\bancos;
use DB;


class EcommercethanksController extends Component
{

    use WithPagination;

    public $products, $comercio_id, $search, $name, $barcode, $price, $stock, $image, $selected_id, $cost, $cantidad, $slug, $nombre_destinatario, $dni, $ciudad, $pais, $provincia, $direccion, $depto, $codigo_postal, $ecommerce, $departamento, $observaciones, $metodo_pago, $caja, $descuento;

  	private $pagination = 25;

    public function paginationView()
    {
    return 'vendor.livewire.bootstrap';
    }

    public function mount($slug) {
      $this->cantidad = 1;
      $this->slug = $slug;

      $this->ecommerce = ecommerce::where('slug',$slug)->first();

      $this->descuento = session('descuento');

    }

    public function resetUI() {
      $this->cantidad = 1;
      $this->selected_id = "";
      $this->name = "";
      $this->barcode = "";
      $this->stock = "";
      $this->price = "";
      $this->cost = "";
      $this->image = "";
    }

    protected $listeners =[
      'SaveSale' => 'SaveSale'
    ];

    public function render()
    {

      $this->data = User::find($this->ecommerce->comercio_id);
      
      $this->casa_central_id = $this->data->casa_central_user_id;
      
    $this->categories_menu = Category::orderBy('name','asc')
    ->where('comercio_id', 'like', $this->casa_central_id)
    ->get();
    
    
    $this->etiquetas_menu = etiquetas::orderBy('nombre','asc')
    ->where('eliminado',0)
    ->where('origen','productos')
    ->where('comercio_id', 'like', $this->casa_central_id)
    ->get();
    

      return view('livewire.ecommerce_thanks.component', [
        'data_e' => $this->data,
        'categories_menu' => $this->categories_menu,
        'imagen' => $this->data->image,
        'data' => $this->data,
        'categories' => Category::orderBy('name','asc')->where('comercio_id', 'like', $this->ecommerce->comercio_id)->get(),
        'etiquetas_menu' => $this->etiquetas_menu,
        'provincias' => provincias::orderBy('provincia','asc')->get()
      ])
      ->extends('layouts.theme-ecommerce.app')
      ->section('content');

    }


    public function pay(Request $request) {

      $payment_id = $request->get('payment_id');

      $response = Http::get("https://api.mercadopago.com/v1/payments/$payment_id" . "?access_token=APP_USR-122023056725190-021316-8deebf83d1eaa95cf96a9245fd70b2d7-185905689");

      $response = json_decode($response);

      $status = $response->status;

      $mp = ecommerce_mp_pago::create([
        'status' => $response->status,
        'mp_id' => $response->id,
        'payment_method_id' => $response->payment_method_id,
        'payer_id' => $response->payer->id
  		]);




    }


}
