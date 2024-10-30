<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;

use Livewire\WithPagination;
use App\Models\Product;
use App\Models\provincias;
use App\Models\productos_lista_precios;
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


class EcommerceLoginController extends Component
{

    use WithPagination;

    public $products, $comercio_id, $search, $name, $categories_menu, $barcode, $price, $stock, $image, $selected_id, $cost, $cantidad, $slug, $nombre_destinatario, $dni, $ciudad, $pais, $provincia, $direccion, $depto, $codigo_postal, $ecommerce, $departamento, $observaciones, $metodo_pago, $caja, $descuento, $email, $phone, $profile, $password;
    public $color, $background_color;
    
  	private $pagination = 25;

    public function paginationView()
    {
    return 'vendor.livewire.bootstrap';
    }

    public function mount($slug) {
      $this->cantidad = 1;
      $this->slug = $slug;
      $this->descuento = session('descuento');


      // 14-6-2024
      $this->ecommerce_encontrado = 1;
      $this->ecommerce = ecommerce::where('slug',$slug)->first();
      if($this->ecommerce == null){
      $this->ecommerce_encontrado = 0;    
      }       
      
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
      
       // 14-6-2024
       if($this->ecommerce_encontrado == 0) {
          return view('livewire.ecommerce.tienda-no-encontrada', [
          ])
          ->extends('layouts.theme-ecommerce.app')
          ->section('content');
       }    
      
      
      $this->comercio_id = $this->ecommerce->comercio_id;
      $this->data = User::find($this->ecommerce->comercio_id);
      
         
       $this->tipo_usuario = User::find($this->comercio_id);
        $this->sucursal_id = $this->comercio_id;
        		    
        if($this->tipo_usuario->sucursal != 1) {
        
        $this->casa_central_id = $this->comercio_id;
        $this->sucursal_precio_stock = 0;	
        } else {
        	  
        $this->casa_central = sucursales::where('sucursal_id', $this->comercio_id)->first();
        $this->casa_central_id = $this->casa_central->casa_central_id;
        $this->sucursal_precio_stock = $this->comercio_id;
        
        }
        
        $this->etiquetas_menu = etiquetas::orderBy('nombre','asc')
        ->where('eliminado',0)
        ->where('origen','productos')
        ->where('comercio_id', 'like', $this->casa_central_id)
        ->get();
   
   
        $this->categories_menu = Category::orderBy('name','asc')
        ->where('eliminado',0)
        ->where('comercio_id', 'like',  $this->casa_central_id )
        ->get();
    
        $this->color = $this->ecommerce->color;
        $this->background_color = $this->ecommerce->background_color;
      
      return view('auth.ecommerce-login', [
        'color' => $this->color,
        'background_color' => $this->background_color,
        'imagen' => $this->data->image,
        'data_e' => $this->data,
        'etiquetas_menu' => $this->etiquetas_menu,
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


    $ultimo_id = ClientesMostrador::where('comercio_id',$this->casa_central_id)->max('id');
    $ultimo_cliente = ClientesMostrador::find($ultimo_id);
        
    if($ultimo_cliente != null){
    $this->id_cliente = $ultimo_cliente->id_cliente + 1;
    } else {
    $this->id_cliente = 1;    
    }

    $cliente = ClientesMostrador::create([
      'nombre' => $this->name,
      'id_cliente' => $this->id_cliente,
      'telefono' => $this->phone,
      'email' => $this->email,
      'status' => 'Active',          
      'comercio_id' => $this->casa_central_id,
      'creador_id' => $this->ecommerce->comercio_id
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

    return redirect('/ecommerce-billing/'.$slug)->with('message','Cliente registrado con exito.');;

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
        
        if($cliente == null) {
        return redirect('ecommerce-login/'.$request->slug)->with('message', 'Las credenciales no coinciden con un cliente.');    
        }
        
    //    if($cliente->comercio_id != $this->ecommerce->comercio_id) {
    //        return redirect('ecommerce-login/'.$request->slug)->with('message', 'Las credenciales no son validas.');
    //    }
        
        
        if($cliente->lista_precio != null) { 
            $this->lista_id = $cliente->lista_precio;
        } else { 
            $this->lista_id = 0;
            }
        
            // Si la lista es distinta a minorista modificamos los precios de la venta en cuestion    
           if($this->lista_id != 0) {
        
           $this->ModificarPrecios($this->lista_id);
           return redirect()->intended('/ecommerce-billing/'.$request->slug)
           ->with('message','Logueado con exito. Precios del carrito modificados con su lista de precios.');
           
           // Si la lista es igual a minorista dejamos como esta y redirigimos
           } else {
               
           return redirect()->intended('/ecommerce-billing/'.$request->slug)->with('message','Logueado con exito.');     
           
               
           }
           

       }

       return redirect('ecommerce-login/'.$request->slug)->with('message', 'Las credenciales no son validas.');
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
