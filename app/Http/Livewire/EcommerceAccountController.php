<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;


use App\Models\etiquetas_relacion;
use App\Models\sucursales;
use App\Models\etiquetas;

use Livewire\WithPagination;
use App\Models\Product;
use App\Models\User;
use App\Models\ecommerce_cupon;
use App\Models\ecommerce;
use App\Models\Category;
use App\Services\CartEcommerce;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\Session;
use App\Models\metodo_pago;
use App\Models\cajas;
use App\Models\historico_stock;
use App\Models\bancos;
use DB;


class EcommerceAccountController extends Component
{

    use WithPagination;

    public $products, $comercio_id, $search, $name, $barcode, $price, $stock, $mensaje, $image, $selected_id, $categories_menu, $cost, $cantidad, $cupon, $descuento, $slug, $ecommerce, $password_vieja, $password, $password_confirm;
    public $color,$background_color;
    
  	private $pagination = 25;

    public function paginationView()
    {
    return 'vendor.livewire.bootstrap';
    }

    public function mount($slug) {

      $this->descuento = session('descuento');

      $this->slug = $slug;

      $this->ecommerce = ecommerce::where('slug',$slug)->first();

      $this->imagen = User::find($this->ecommerce->comercio_id);


      $this->cantidad = 1;
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

    public function render()
    {

    $products = Product::join('categories as c','c.id','products.category_id')
    ->select('products.*','c.name as category')
    ->where('products.comercio_id', 'like', $this->ecommerce->comercio_id)
    ->where('products.eliminado', 'like', 0)
    ->where( function($query) {
         $query->where('products.name', 'like', '%' . $this->search . '%')
          ->orWhere('products.barcode', 'like',$this->search . '%');
        })
    ->paginate($this->pagination);
    
    $this->data = User::find($this->ecommerce->comercio_id);
      
    $this->casa_central_id = $this->data->casa_central_user_id;
      


    $this->categories_menu = Category::orderBy('name','asc')
    ->where('eliminado',0)
    ->where('comercio_id', 'like', $this->casa_central_id)
    ->orWhere('id',1)
    ->get();

    $this->etiquetas_menu = etiquetas::orderBy('nombre','asc')
    ->where('eliminado',0)
    ->where('origen','productos')
    ->where('comercio_id', 'like', $this->casa_central_id)
    ->get();
    
    
    $this->data = User::find($this->ecommerce->comercio_id);

    $this->datos_cliente = User::where('users.cliente_id',Auth::user()->cliente_id)->first();

    $this->imagen = $this->data->image;
    $this->color = $this->ecommerce->color;
    $this->background_color = $this->ecommerce->background_color;
    
      return view('livewire.ecommerce_acount.component', [
        'color' =>  $this->color,
        'background_color' => $this->background_color,
        'datos_cliente' => $this->datos_cliente,
        'imagen' => $this->imagen,
        'data_e' => $this->data,
        'categories_menu' => $this->categories_menu,
        'products' => $products,
        'categories' => Category::orderBy('name','asc')->where('comercio_id', 'like', $this->ecommerce->comercio_id)->get()
      ])
      ->extends('layouts.theme-ecommerce.app')
      ->section('content');

    }


    public function CambiarContraseña()
    {

      $user = Auth::user(); // Obtenga la instancia del usuario en sesión

      if($this->password == $this->password_confirm) {
      //validation rules
      $rules = [
        'password' => 'required|min:6|max:32'

      ];

      //custom messages
      $customMessages = [
        'password.required' => 'El password es requerido',
        'password.min' => 'El password debe tener al menos 6 caracteres',
      ];

      //execute validate
      $this->validate($rules, $customMessages);
       // Note la regla de validación "confirmed", que solicitará que usted agregue un campo extra llamado password_confirm

       $password = bcrypt($this->password); // Encripte el password


       $user->password = $password; // Rellene el usuario con el nuevo password ya encriptado
       $user->save(); // Guarde el usuario

       $this->password = '';
       $this->password_confirm = '';

     } else {
       $this->mensaje = "Las contraseñas ingresadas no coinciden.";
     }

    }


    public function logout()
    {
        Auth::logout();
        return redirect()->to('/tienda/'. $this->slug);
    }
    


}
