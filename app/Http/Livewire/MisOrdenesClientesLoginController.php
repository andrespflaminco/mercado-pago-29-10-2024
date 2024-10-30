<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;

use Livewire\WithPagination;
use App\Models\Product;
use App\Models\provincias;
use App\Models\productos_lista_precios;
use App\Models\Category;
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


class MisOrdenesClientesLoginController extends Component
{

    use WithPagination;

    public $products, $comercio_id, $search, $name, $categories_menu, $barcode, $price, $stock, $image, $selected_id, $cost, $cantidad, $slug, $nombre_destinatario, $dni, $ciudad, $pais, $provincia, $direccion, $depto, $codigo_postal, $ecommerce, $departamento, $observaciones, $metodo_pago, $caja, $descuento, $email, $phone, $profile, $password;

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
      
        $this->categories_menu = Category::orderBy('name','asc')
        ->where('eliminado',0)
        ->where('comercio_id', 'like', $this->ecommerce->comercio_id)
        ->get();

      return view('auth.mis-ordenes-login', [
          
        'imagen' => $this->data->image,
        'data_e' => $this->data,
        'categories_menu' => $this->categories_menu,
        'categories' => Category::orderBy('name','asc')->where('comercio_id', 'like', $this->ecommerce->comercio_id)->get(),
        'provincias' => provincias::orderBy('provincia','asc')->get()
      ])
      ->extends('layouts.theme-ecommerce.app')
      ->section('content');

    }

    public function Store($slug)
    {
     $rules =[
        'name' => 'required|min:3',
        'email' => 'required|unique:users|email',
        'password' => 'required|min:3'
    ];

    $messages =[
        'name.required' => 'Ingresa el nombre',
        'name.min' => 'El nombre del usuario debe tener al menos 3 caracteres',
        'email.required' => 'Ingresa el correo ',
        'email.email' => 'Ingresa un correo válido',
        'email.unique' => 'El email ya existe en sistema',
        'password.required' => 'Ingresa el password',
        'password.min' => 'El password debe tener al menos 3 caracteres'
    ];

    $this->validate($rules, $messages);

    $cliente = ClientesMostrador::create([
      'nombre' => $this->name,
      'telefono' => $this->phone,
      'email' => $this->email,
      'status' => 'Active',
      'comercio_id' => $this->ecommerce->comercio_id
      ]);

    $user = User::create([
        'name' => $this->name,
        'email' => $this->email,
        'phone' => $this->phone,
        'status' => 'Active',
        'profile' => 'Cliente',
        'cliente_id' => $cliente->id,
        'comercio_id' => $this->ecommerce->comercio_id,
        'email_verified_at' => Carbon::now(),
        'confirmed' => 1,
        'password' => bcrypt($this->password)
    ]);




    $user->syncRoles('Cliente');

    $this->resetUI();
    $this->emit('user-added','Usuario Registrado');

    Auth::login($user);

    return redirect('/mis-ordenes/'.$slug)->with('message','Cliente registrado con exito.');;

    }

    public function customLogin(Request $request)
   {
       $request->validate([
           'email' => 'required',
           'password' => 'required',
       ]);

       $credentials = $request->only('email', 'password');
       if (Auth::attempt($credentials)) {
           
           // Si el logueo del cliente es correcto, chequeamos que lista de precios tiene
           
        $cliente = ClientesMostrador::find(Auth::user()->cliente_id);
        
        //dd($cliente);
        
        if($cliente == null) {
        return redirect('mis-ordenes-login/'.$request->slug)->with('message', 'Las credenciales no coinciden con un cliente.');    
        }
        
    //    if($cliente->comercio_id != $this->ecommerce->comercio_id) {
    //        return redirect('ecommerce-login/'.$request->slug)->with('message', 'Las credenciales no son validas.');
    //    }
          
           return redirect()->intended('/mis-ordenes/'.$request->slug)->with('message','Logueado con exito.');     
     
           

       }

       return redirect('mis-ordenes-login/'.$request->slug)->with('message', 'Las credenciales no son validas.');
   }
   
   
   // funcion para modificar los precios
   
   
  public function ModificarPrecios($lista_id) {
      
      $cart = new CartEcommerce;
      $items = $cart->getContent();


      foreach ($items as $i)
   {
       
    // sacamos el precio de la lista de precios
       
     $price = productos_lista_precios::where('productos_lista_precios.product_id',$i['product_id'])
      ->where('productos_lista_precios.referencia_variacion', $i['referencia_variacion'])
      ->where('productos_lista_precios.lista_id',$lista_id)
      ->first();

    // eliminamos el producto con el precio viejo
    
            $cart->removeProduct($i['id']);

    // incorporamos el producto con el precio nuevo
    
            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "price" => $price->precio_lista,
                "product_id" => $i['product_id'],
                "referencia_variacion" => $i['referencia_variacion'],
                "cost" => $i['cost'],
                "image" => $i['image'],
                "stock" => $i['stock'],
                "seccion_almacen" => $i['seccion_almacen'],
                "stock_descubierto" => $i['stock_descubierto'],
                "qty" => $i['qty']
            );

            $cart->addProduct($product);

   }

  }


}
