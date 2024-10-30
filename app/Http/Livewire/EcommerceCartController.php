<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;

use Livewire\WithPagination;
use App\Models\Product;
use App\Models\User;
use App\Models\etiquetas;
use App\Models\etiquetas_productos;
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


class EcommerceCartController extends Component
{

    use WithPagination;

    public $products, $comercio_id, $search, $name,$orderby_id, $barcode, $price, $stock, $image, $selected_id, $cost, $cantidad, $cupon, $descuento, $slug, $ecommerce, $categories, $categories_menu;
    public $background_color, $color,$imagen;
  	
  	private $pagination = 25;

    public function paginationView()
    {
    return 'vendor.livewire.bootstrap';
    }

    public function mount($slug) {

      $this->descuento = session('descuento');

      $this->slug = $slug;

    //  $this->ecommerce = ecommerce::where('slug',$slug)->first();
      $this->cantidad = 1;
      
      // 14-6-2024
      $this->ecommerce_encontrado = 1;
      $this->ecommerce = ecommerce::where('slug',$slug)->first();
      if($this->ecommerce == null){
      $this->ecommerce_encontrado = 0;    
      }       
      
      if($this->ecommerce_encontrado == 1){
      $this->imagen = User::find($this->ecommerce->comercio_id);
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

    public function render()
    {

    // 14-6-2024
    if($this->ecommerce_encontrado == 0) {
          return view('livewire.ecommerce.tienda-no-encontrada', [
          ])
          ->extends('layouts.theme-ecommerce.app')
          ->section('content');
    }    
      
          
    $this->tipo_usuario = User::find($this->ecommerce->comercio_id);
    $this->sucursal_id = $this->comercio_id;
        		    
   // dd($this->tipo_usuario);
    
    if($this->tipo_usuario->sucursal != 1) {
        
    $this->casa_central_id = $this->tipo_usuario->casa_central_user_id;
    $this->sucursal_precio_stock = 0;	
    } else {
        	  
    $this->casa_central_id = $this->tipo_usuario->casa_central_user_id;
    $this->sucursal_precio_stock = $this->comercio_id;
        
    }
        

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

    $ecommerce = ecommerce::where('comercio_id',$this->ecommerce->comercio_id)->first();
    
    
    $this->etiquetas_menu = etiquetas::orderBy('nombre','asc')
    ->where('eliminado',0)
    ->where('origen','productos')
    ->where('comercio_id', 'like', $this->casa_central_id)
    ->get();
    
    $this->categories_menu = Category::orderBy('name','asc')
    ->where('eliminado',0)
    ->where('comercio_id', 'like', $this->casa_central_id)
    ->orWhere('id',1)
    ->get();

    $this->color = $this->ecommerce->color;
    $this->background_color = $this->ecommerce->background_color;
    
    $this->imagen =  $this->data->image;
    
      return view('livewire.ecommerce_cart.component', [
        'color' => $this->color,
        'background_color' => $this->background_color,
        'imagen' => $this->imagen,
        'data_e' => $this->data,
        'products' => $products,
        'ecommerce' => $ecommerce,
        'categories_menu' => $this->categories_menu,
        'etiquetas_menu' => $this->etiquetas_menu,
        'categories' => Category::orderBy('name','asc')->where('comercio_id', 'like', $this->ecommerce->comercio_id)->get()
      ])
      ->extends('layouts.theme-ecommerce.app')
      ->section('content');

    }


    public function Add(Product $product)
    {

      $this->selected_id = $product->id;
      $this->name = $product->name;
      $this->barcode = $product->barcode;
      $this->stock = $product->stock;
      $this->price = $product->price;
      $this->cost = $product->cost;
      $this->image = $product->image;

      $this->emit('add','Show modal');
    }


    protected $listeners =[
   		'Agregar' => 'Agregar',
      'SaveSale' => 'SaveSale'
   	];

      public function Agregar($cantidad, $selected_id) {


        $cart = new CartEcommerce;
        $items = $cart->getContent();


     if ($items->contains('id', $this->selected_id)) {

       $cart = new CartEcommerce;
       $items = $cart->getContent();

       $product = Product::find($this->selected_id);

       foreach ($items as $i)
    {
           if($i['id'] === $product['id']) {

             $cart->removeProduct($i['id']);

             $product = array(
                 "id" => $i['id'],
                "barcode" => $i['barcode'],
                 "name" => $i['name'],
                 "price" => $i['price'],
                 "cost" => $i['cost'],
                 "qty" => $i['qty']+$cantidad,
                 "orderby_id"=> $i['orderby_id'],
             );

             $cart->addProduct($product);

         }
    }

        $this->resetUI();

        $this->emit('product-added','Producto agregado');

       return back();

    } else {

          $cart = new CartEcommerce;

          $product = array(
              "id" => $this->selected_id,
              "barcode" => $this->barcode,
              "name" => $this->name,
              "price" => $this->price,
              "cost" => $this->cost,
              "qty" => $cantidad,
          );

          $cart->addProduct($product);

          $this->resetUI();

          $this->emit('product-added','Producto agregado');

      }

    }


    public function removeProductFromCart($product,$referencia_variacion) {
        
        $id_cart = $product."|".$referencia_variacion;
        
        $cart = new CartEcommerce;
        $cart->removeProduct($id_cart);
        session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
        return back();
    }


  public function Incrementar($product, $referencia_variacion) {
      
      $id_cart = $product."|".$referencia_variacion;
      
      $product = Product::find($product);
      
      $cart = new CartEcommerce;
      $items = $cart->getContent();


      foreach ($items as $i)
   {
          if($i['id'] === $id_cart) {

            $cart->removeProduct($id_cart);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "price" => $i['price'],
                "product_id" => $i['product_id'],
                "referencia_variacion" => $i['referencia_variacion'],
                "cost" => $i['cost'],
                "image" => $i['image'],
                "stock" => $i['stock'],
                "seccion_almacen" => $i['seccion_almacen'],
                "stock_descubierto" => $i['stock_descubierto'],
                "qty" => $i['qty']+1,
                "orderby_id"=> $i['orderby_id'],
            );

            $cart->addProduct($product);

        }
   }

    $this->emit('product-added','Producto actualizado');

      return back();
  }
  
    public function Decrecer($product, $referencia_variacion) {
      
      $id_cart = $product."|".$referencia_variacion;
      
      $product = Product::find($product);
      
      $cart = new CartEcommerce;
      $items = $cart->getContent();


      foreach ($items as $i)
   {
          if($i['id'] === $id_cart) {

            $cart->removeProduct($id_cart);
            
            if(1 < $i['qty']) {
         
            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "price" => $i['price'],
                "product_id" => $i['product_id'],
                "referencia_variacion" => $i['referencia_variacion'],
                "cost" => $i['cost'],
                "image" => $i['image'],
                "stock" => $i['stock'],
                "seccion_almacen" => $i['seccion_almacen'],
                "stock_descubierto" => $i['stock_descubierto'],
                "qty" => $i['qty']-1,
                "orderby_id"=> $i['orderby_id'],
            );

            $cart->addProduct($product);
            
            }

        }
   }

    $this->emit('product-added','Producto actualizado');

      return back();
  }


      public function BuscarCupon() {
        $cupones = ecommerce_cupon::where('cupon',$this->cupon)->first();


        $this->descuento = $cupones->descuento/100;

        session(['descuento' => $this->descuento]);

        $this->emit('cupon-added','Cupon agregado');


      }



}
