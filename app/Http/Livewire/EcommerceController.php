<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;

use Livewire\WithPagination;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\etiquetas_relacion;
use App\Models\sucursales;
use App\Models\etiquetas;
use App\Models\productos_variaciones_datos;
use Illuminate\Http\Request;
use App\Models\productos_variaciones;
use App\Models\ClientesMostrador;
use App\Models\productos_stock_sucursales;
use App\Models\productos_lista_precios;
use App\Models\atributos;
use App\Models\variaciones;
use App\Services\CartEcommerce;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\Session;
use App\Models\metodo_pago;
use App\Models\cajas;
use App\Models\ecommerce;
use App\Models\ecommerce_image;
use App\Models\historico_stock;
use App\Models\bancos;
use DB;


class EcommerceController extends Component
{

    use WithPagination;

    public $comercio_id, $search,$encontrado,$orderby_id, $categoria_elegida ,$etiqueta,$sucursal_precio_stock, $nombre_categoria, $cantidad_modal, $atributos, $name, $barcode, $price, $stock, $image, $selected_id, $cost, $seccion_almacen, $descripcion, $cantidad_agregada, $cantidad, $ecommerce, $slug, $category, $stock_descubierto, $categories;
    public $productos_variaciones_datos = [];
    public $atributos_form = [];
    public $variaciones_form = [];
    public $variacion_elegida = [];
    public $product_id = 0;
    private $products = [];
    
    public $backgorund_color, $color;
  	
  	private $pagination = 24;

    public function paginationView()
    {
    return 'vendor.livewire.bootstrap';
    }

    public function mount($slug) {
        
      $this->cantidad = 1;
      $this->slug = $slug;
      //$this->ecommerce = ecommerce::where('slug',$slug)->first();
      $this->estado_variacion = 0;
      $this->encontrado = 1;
      $this->category = 0;

      // 14-6-2024
      $this->ecommerce_encontrado = 1;
      $this->ecommerce = ecommerce::where('slug',$slug)->first();
      if($this->ecommerce == null){
      $this->ecommerce_encontrado = 0;    
      }       
    }

    public function resetUI() {
      $this->cantidad = 1;
      $this->atributos_form = [];
      $this->variaciones_form = [];
      $this->variacion_elegida = [];
      $this->encontrado = 1;
      $this->cantidad_agregada = 0;
      $this->selected_id = 0;
      $this->name = "";
      $this->barcode = "";
      $this->stock = "";
      $this->price = "";
      $this->cost = "";
      $this->image = "";
    }
    
   public function Search() {
      $this->search = $this->search;
    }

public function ListaPrecio() {

        if(Auth::guest()) {
        $this->lista_id = 0;    
        } else {
        $cliente_id = Auth::user()->cliente_id;
        if($cliente_id != null){
        $cliente = ClientesMostrador::find(Auth::user()->cliente_id);
        if($cliente->lista_precio != null) { $this->lista_id = $cliente->lista_precio;} else { $this->lista_id = 0; }
        } else {
        $this->lista_id = 0;       
        }
        }    

    
}

    public function render(Request $request)
    {

       // 14-6-2024
       if($this->ecommerce_encontrado == 0) {
          return view('livewire.ecommerce.tienda-no-encontrada', [
          ])
          ->extends('layouts.theme-ecommerce.app')
          ->section('content');
       }    
      
      $this->ListaPrecio();
        
 //       dd($request->all());
        
//    Filtros por categoria     //
   if($request->categoria == null) {
    $this->comercio_id = intval($this->ecommerce->comercio_id);
    
 // dd($this->comercio_id);
    
   } else {
    
    $this->comercio_id = intval($request->comercio_id);   
    $this->category = $request->categoria;   
    
    $this->nombre_categoria = Category::find($this->category);
    
    $this->nombre_categoria = $this->nombre_categoria->name;
   }
   
   //    Filtros por categoria     //
   if($request->search == null) {
    $this->comercio_id = intval($this->ecommerce->comercio_id);
    
 // dd($this->comercio_id);
    
   } else {
    
    $this->comercio_id = intval($request->comercio_id);   
    $this->search = $request->search;   
    
   }

   //    Filtros por etiqueta     //
   if($request->etiqueta == null) {
    $this->comercio_id = intval($this->ecommerce->comercio_id);
    
 // dd($this->comercio_id);
    
   } else {
    
    $this->comercio_id = intval($request->comercio_id);   
    $this->etiqueta = $request->etiqueta;   
    
   }


   // dd($this->comercio_id);
    
    $this->ecommerce = ecommerce::where('comercio_id',$this->comercio_id)->first();
   
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
        
        
        // Etiqueta 
        
            
    if($this->etiqueta) {
        
    //dd($this->etiqueta);
    
    $this->id_productos_etiquetas = etiquetas_relacion::where('etiqueta_id',$this->etiqueta)->where('estado',1)->where('origen','productos')->where('estado',1)->get();
    
    // Extraer solo los IDs
    $idArray = [];

    foreach ($this->id_productos_etiquetas as $element) {
        array_push($idArray,$element["relacion_id"]);
    }

    //dd($idArray);
    }
    
    
   

/////////// PRODUCTOS ///////////////
    $this->products = Product::join('categories as c','c.id','products.category_id')
    ->select('products.*','c.name as category')
    ->where('products.comercio_id', $this->casa_central_id);
    
    if($this->category) {
    $this->products = $this->products->where('products.category_id', $this->category);
    }
    
    if($this->etiqueta) {
    $this->products = $this->products->whereIn('products.id', $idArray);
    }
    
    if(strlen($this->search) > 0) {

	$this->products = $this->products->where('products.name', 'like', '%' . $this->search . '%');

	}

    $this->products = $this->products->where('products.eliminado', 'like', 0)
    ->where('products.ecommerce_canal', 'like', 1)
    ->paginate($this->pagination);
    
    
    /////////// LISTA DE PRECIOS ///////////////
    
    $this->precios = productos_lista_precios::rightjoin('products','products.id','productos_lista_precios.product_id')
    ->where('products.comercio_id', $this->casa_central_id)
    ->where('productos_lista_precios.lista_id', $this->lista_id);
    
    if($this->category) {
    $this->precios = $this->precios->where('products.category_id', $this->category);
    }
    
    if(strlen($this->search) > 0) {

	$this->precios = $this->precios->where('products.name', 'like', '%' . $this->search . '%');

	}

    $this->precios = $this->precios->where('products.eliminado', 0)
    ->where('products.ecommerce_canal', 'like', 1)
    ->get();
    
    
    ///////////////////////////////////

    $this->categories = Category::orderBy('name','asc')
    ->where('eliminado',0)
    ->where('comercio_id', $this->casa_central_id);
    
    if($this->category) {
    $this->categories = $this->categories->where('categories.id', $this->category);
    }

    $this->categories = $this->categories->get();
    
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

    $this->data = User::find($this->comercio_id);

        
              
      $this->color = $this->ecommerce->color;
    //  $this->background_color = "#343a40;";
    //  $this->background_color = "#dc3545;";
    $this->background_color = $this->ecommerce->background_color;
    
      

      return view('livewire.ecommerce.component', [
        'color' => $this->color,
        'background_color' => $this->background_color,
        'imagen' => $this->data->image,
        'precios' => $this->precios,
        'data_e' => $this->data,
        'datos_ecommerce' => $this->ecommerce,
        'prod' => $this->products,
        'slug' => $this->slug,
        'tipo' => $this->ecommerce->tipo,
        'categories_menu' => $this->categories_menu,
        'etiquetas_menu' => $this->etiquetas_menu,
        'categories' => $this->categories
      ])
      ->extends('layouts.theme-ecommerce.app')
      ->section('content');

    }

    public function BuscarVariacion() {

    $q_ve = [];
    
    foreach ($this->variacion_elegida as $key => $ve) {

    $query_VE = $ve;

    array_push($q_ve , $query_VE);

    }
    
    $variaciones_sin_orden = $q_ve;
     natsort($q_ve); 
     
     $q_ve = implode(",",$q_ve);

    $this->productos_variaciones_datos = productos_variaciones_datos::where('variaciones_id', $q_ve)
    ->where('product_id',$this->product_id)
    ->first();

    ////////////////////////   PRODUCTO    //////////////////////////////////////////////////////
    
    $product = Product::find($this->product_id);
    
    if($this->productos_variaciones_datos != null) {
    

    ////////////////////////   PRECIO    //////////////////////////////////////////////////////

      $price = productos_lista_precios::where('productos_lista_precios.product_id',$this->productos_variaciones_datos->product_id)
      ->where('productos_lista_precios.referencia_variacion', $this->productos_variaciones_datos->referencia_variacion)
      ->where('productos_lista_precios.lista_id',$this->lista_id)
      ->first();

     //////////////////////    STOCK     ///////////////////////////////////////////////////

      $PSS = productos_stock_sucursales::where('productos_stock_sucursales.product_id',$this->productos_variaciones_datos->product_id)
      ->where('productos_stock_sucursales.comercio_id', $this->casa_central_id)
      ->where('productos_stock_sucursales.sucursal_id', $this->sucursal_precio_stock)
      ->where('productos_stock_sucursales.referencia_variacion', $this->productos_variaciones_datos->referencia_variacion)
      ->first();
      
      $price = $price->precio_lista;
      $cantidad =  $PSS->stock;
      $this->encontrado = 1;
      $this->referencia_variacion = $this->productos_variaciones_datos->referencia_variacion;
      $this->name1 = $product->name." ".$this->productos_variaciones_datos->variaciones;
      $this->image = $this->productos_variaciones_datos->imagen;
      
    } else {
      $price = 0;
      $cantidad =  0;
      $this->encontrado = 0;
      $this->referencia_variacion = 0;
      $this->name1 = $product->name;
      $this->image = $product->image;
    }
  
      $this->tipo_producto = 2;
      $this->estado_variacion = 1;
      $this->selected_id = $product->id;
      $this->product_id = $product->id;
      $this->name = $this->name1;
      $this->barcode = $product->barcode;
      $this->stock = $cantidad;
      $this->price = $price;
      $this->seccion_almacen = $product->seccion_almacen;
      $this->cost = $product->cost;
      $this->descripcion = $product->descripcion;
      
      $this->stock_descubierto = $product->stock_descubierto;

      $this->atributos_form = productos_variaciones::join('atributos','atributos.id','productos_variaciones.atributo_id')
      ->select('atributos.nombre as nombre_atributo','atributos.id as atributo_id')
      ->where('productos_variaciones.producto_id', $product->id)
      ->groupBy('atributos.nombre','atributos.id')
      ->get();
      
      $this->variaciones_form = productos_variaciones::join('variaciones','variaciones.id','productos_variaciones.variacion_id')
      ->select('variaciones.nombre as nombre_variacion','variaciones.id as variacion_id','productos_variaciones.atributo_id')
      ->where('productos_variaciones.producto_id', $product->id)
      ->groupBy('variaciones.nombre','variaciones.id','productos_variaciones.atributo_id')
      ->get();
      
      $this->emit('add','Show modal');  
        
    }

    public function Add(Product $product)
    {
      $cart = new CartEcommerce;
      $items = $cart->getContent();
      
      $this->cantidad_modal = 1;
      
     ////////// SI ES VARIACION //////////////////////


      if($product->producto_tipo == "v") {

      $this->productos_variaciones_datos =  productos_variaciones_datos::where('product_id',$product->id)->where('comercio_id', $this->casa_central_id)->get();

      $this->atributos_form = productos_variaciones::join('atributos','atributos.id','productos_variaciones.atributo_id')
      ->select('atributos.nombre as nombre_atributo','atributos.id as atributo_id')
      ->where('productos_variaciones.producto_id', $product->id)
      ->groupBy('atributos.nombre','atributos.id')
      ->get();
      
      $this->variaciones_form = productos_variaciones::join('variaciones','variaciones.id','productos_variaciones.variacion_id')
      ->select('variaciones.nombre as nombre_variacion','variaciones.id as variacion_id','productos_variaciones.atributo_id')
      ->where('productos_variaciones.producto_id', $product->id)
      ->groupBy('variaciones.nombre','variaciones.id','productos_variaciones.atributo_id')
      ->get();


      $this->product_id = $product->id;
      $this->barcode = $product->barcode;

      $this->variaciones = variaciones::where('variaciones.comercio_id', $this->casa_central_id)->get();

      $this->tipo_producto = 2;
      $price = 0;
      $cantidad = 0;
      $this->estado_variacion = 0;
      
      } else {
          
     //////////////////////////////////////////////////////////////////////////////

      $price = productos_lista_precios::where('productos_lista_precios.product_id',$product->id)
      ->where('productos_lista_precios.referencia_variacion', 0)
      ->where('productos_lista_precios.lista_id',$this->lista_id)
      ->first();

     //////////////////////////////////////////////////////////////////////////////
    
      $PSS = productos_stock_sucursales::where('productos_stock_sucursales.product_id',$product->id)
      ->where('productos_stock_sucursales.comercio_id', $this->casa_central_id)
      ->where('productos_stock_sucursales.sucursal_id', $this->sucursal_precio_stock)
      ->where('productos_stock_sucursales.referencia_variacion', 0)
      ->first();
      
      $price = $price->precio_lista;
      $cantidad =  $PSS->stock;
  
      $this->tipo_producto = 1;
      $this->estado_variacion = 1;
      }

    
      $this->selected_id = $product->id;
      $this->name = $product->name;
      $this->barcode = $product->barcode;
     
      $this->categoria_elegida = $this->categoria_elegida;
      
      $this->barcode = $product->barcode;
      $this->stock = $cantidad;
      $this->price = $price;
      $this->seccion_almacen = $product->seccion_almacen;
      $this->cost = $product->cost;
      $this->descripcion = $product->descripcion;
      $this->image = $product->image;
      $this->stock_descubierto = $product->stock_descubierto;
      $this->referencia_variacion = 0;

      $this->emit('add','Show modal');
    }


    protected $listeners =[
   		'Agregar' => 'Agregar',
   		'Sumar' => 'Sumar'
   	];

      public function Agregar($selected_id, $referencia_variacion) {

	  //Aumentar id order by
     $this->orderby_id =  $this->orderby_id + 1;

        $cart = new CartEcommerce;
        $items = $cart->getContent();
        
        $id_cart = $selected_id."|".$referencia_variacion;
       


     if ($items->contains('id', $id_cart)) {

       $cart = new CartEcommerce;
       $items = $cart->getContent();

       foreach ($items as $i)
    {
           if($i['id'] === $id_cart) {

             $cart->removeProduct($id_cart);

             $product = array(
                 "id" => $i['id'],
                "barcode" => $i['barcode'],
                 "name" => $i['name'],
                "product_id" => $i['product_id'],
                "referencia_variacion" => $i['referencia_variacion'],
                 "price" => $i['price'],
                 "cost" => $i['cost'],
                 "image" => $i['image'],
                "stock" => $i['stock'],
                "seccion_almacen" => $i['seccion_almacen'],
                "stock_descubierto" => $i['stock_descubierto'],
                "qty" => $i['qty']+$this->cantidad_modal,
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
              "id" => $id_cart,
              "barcode" => $this->barcode,
              "name" => $this->name,
              "product_id" => $selected_id,
              "referencia_variacion" => $referencia_variacion,
              "price" => $this->price,
              "cost" => $this->cost,
              "image" => $this->image,
              "stock" => $this->stock,
              "seccion_almacen" => $this->seccion_almacen,
              "stock_descubierto" => $this->stock_descubierto,
              "qty" => $this->cantidad_modal,
              "orderby_id"=>  $this->orderby_id
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

    $this->emit('product-added','Unidad agregada');

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

    $this->emit('product-added','Unidad agregada');

      return back();
  }



function Sumar($selected_id, $referencia_variacion) {
    
    $id_cart = $selected_id."|".$referencia_variacion;
    
    $cart = new CartEcommerce;
    $items = $cart->getContent();
    
    if ($items->contains('id', $id_cart)) {

       $cart = new CartEcommerce;
       $items = $cart->getContent();

       foreach ($items as $i)
    {
           if($i['id'] === $id_cart) {
               
               $cantidad_cart =  $i['qty'];
    } else {
        $cantidad_cart =  0;
    }
    }
    
    $this->cantidad_modal = $this->cantidad_modal + 1; 

   if(($this->stock_descubierto == "si") && ($this->cantidad_modal > ($this->stock - $cantidad_cart))) {

            $this->emit('alerta-stock', $this->stock);
            $this->cantidad_modal = $this->stock - $cantidad_cart;

          } else {
            $this->cantidad_modal = $this->cantidad_modal;
          }
          
    } else {
        
        /////////////////////////      CUANDO NO ESTA TODAVIA EN EL CARRITO       ///////////////////////////////////
        
        
        if(($this->stock_descubierto == "si") && ( ($this->cantidad_modal + 1) > $this->stock)) {

        $this->cantidad_modal = $this->stock;
        
        
        $this->emit('alerta-stock', $this->stock);

        } else {
        $this->cantidad_modal = $this->cantidad_modal + 1;
        }
    
        
    }
    
    
    if($referencia_variacion != 0) {
    $this->BuscarVariacion();    
    }
    
    

}

function Restar($selected_id, $referencia_variacion) {
    
    $id_cart = $selected_id."|".$referencia_variacion;
    
        $this->cantidad_modal = $this->cantidad_modal - 1;
     
    
    if($referencia_variacion != 0) {
    $this->BuscarVariacion();    
    }
    

}

    public function logout()
    {
        Auth::logout();
        return redirect()->to('/tienda/'. $this->slug);
    }
    
}
