<?php

namespace App\Http\Livewire;


// Trait

use App\Traits\WocommerceTrait;
use App\Traits\ProductsTrait;
use App\Traits\CartTrait;

//


use Illuminate\Support\Facades\Validator;

use App\Models\Category;
use Illuminate\Validation\Rule;
use App\Models\proveedores;
use App\Models\Product;
use App\Models\lista_precios;
use App\Models\productos_variaciones;
use App\Models\sucursales;
use App\Models\atributos;
use Illuminate\Http\Request;
use App\Models\imagenes;
use App\Services\CartVariaciones;
use App\Services\CartProductosAtributos;
use App\Models\productos_lista_precios;
use App\Models\productos_stock_sucursales;
use App\Models\productos_variaciones_datos;
use App\Models\productos_atributos;
use App\Models\variaciones;
use App\Models\products_price;
use App\Models\User;
use App\Models\wocommerce;
use App\Models\receta;
use App\Models\historico_stock;
use Livewire\Component;
use App\Models\seccionalmacen;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProductsAddedController extends Component
{

	use WithPagination;
	use WithFileUploads;
	use CartTrait;
	use WocommerceTrait;
	use ProductsTrait;


	public $name,$barcode,$stock_sucursal,$search_imagenes,$referencia_variacion,$variacion_atributo,$tipo_carga_imagen, $wc_canal,$style_tipo_1 , $style_tipo_2, $cost,$price,$tipo_producto, $iva, $relacion_precio_iva , $precio_lista, $stock,$alerts,$categoryid,$search,$image,$selected_id,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $inv_ideal, $proveedor, $cod_proveedor, $product_added, $proveedor_elegido, $ecommerce_canal, $mostrador_canal, $name_almacen, $descripcion, $image_categoria, $name_categoria, $sucursal_selected;
	public $id_almacen;
	public $producto_tipo;
	public $id_categoria;
	public $base64;
	public $id_proveedor;
	private $pagination = 25;
	private $wc_product_id;
	public $imagen_seleccionada;
	public $costos_variacion, $cod_variacion;
	public $atributos_asociados = [];
	public $atributo_agregar;

	public $sucursal = [];
	public $SelectedProducts = [];
	public $selectedAll = FALSE;

	public $sortColumn = "name";
    public $sortDirection = "asc";


	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function mount($id)
	{
	    
	   
		$this->metodo_pago = session('MetodoPago');
		$this->atributos_asociados = [];
		$this->pageTitle = 'Listado';
		$this->proveedor = '1';
		$this->componentName = 'Productos';
		$this->categoryid = 'Elegir';
		$this->almacen = 'Elegir';
		$this->stock_descubierto = 'Elegir';
		$this->OrderNombre = "ASC";
		$this->OrderBarcode = "ASC";
		$this->iva = 0;
		$this->relacion_precio_iva = 0;
		$this->tipo_producto = "Elegir";
		$this->producto_tipo = "Elegir";
		$this->wc_canal = false;
		$this->ecommerce_canal = false;
		$this->mostrador_canal = true;
		$this->producto_tipo = 'Elegir';
		$this->product_added = $id;


	}


	public function sort($column)
	{
			$this->sortColumn = $column;
			$this->sortDirection = $this->sortDirection == 'asc' ? 'desc' : 'asc';
	}


	public function ModalCategoria($value)
	{
		if($value == 'AGREGAR') {

		$this->emit('modal-categoria-show', '');

		}

	}

	public function ModalAlmacen($value)
	{
		if($value == 'AGREGAR') {

		$this->emit('modal-almacen-show', '');

		}

	}

	public function StoreCategoria()
	{
		$rules = [
			'name_categoria' => 'required|min:3'
		];

		$messages = [
			'name_categoria.required' => 'Nombre de la categoría es requerido',
			'name_categoria.min' => 'El nombre de la categoría debe tener al menos 3 caracteres'
		];

		$this->validate($rules, $messages);

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$category = Category::create([
			'name' => $this->name_categoria,
			'comercio_id' => $comercio_id
		]);


		$customFileName;
		if($this->image_categoria)
		{
			$customFileName = uniqid() . '_.' . $this->image_categoria->extension();
			$this->image_categoria->storeAs('public/categories', $customFileName);
			$category->image_categoria = $customFileName;
			$category->save();
		}

		$this->categoryid = $category->id;

		$this->resetUICategoria();
		$this->emit('category-added','Categoría agregada');
			$this->emit('modal-show','Show modal');

	}



	public function StoreAlmacen()
  {
 	 //validation rules
 	 $rules = [
 		 'name_almacen' => 'required|min:3'
 	 ];

 	 //custom messages
 	 $customMessages = [
 		 'name_almacen.required' => 'Nombre de categoría requerido',
 		 'name_almacen.min' => 'El nombre debe tener al menos 3 caracteres',
 	 ];

 	 //execute validate
 	 $this->validate($rules, $customMessages);


 	 //insert
 	 $almacen =  seccionalmacen::create([
 		 'nombre' => $this->name_almacen,
 		 'comercio_id' => Auth::user()->id
 	 ]);

 				 // clear inputs
 	 $this->resetUIAlmacen();

	 $this->almacen = $almacen->id;
 	 // emit frontend notification
 	 $this->emit('almacen-added', 'Almacen Agregado');
	 	$this->emit('modal-show','Show modal');
  }



	public function render()
	{

			if(Auth::user()->comercio_id != 1)
			$comercio_id = Auth::user()->comercio_id;
			else
			$comercio_id = Auth::user()->id;
            
            $this->tipo_usuario = User::find($comercio_id);
            
            // si el usuario no es sucursal
            
            if($this->tipo_usuario->sucursal != 1) {

			$this->casa_central_id = $comercio_id;
			$this->sucursal_id = $comercio_id;
			
			$this->lista_precios = lista_precios::where('comercio_id',$this->sucursal_id)->get();
			
			$this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
			->select('users.name','sucursales.sucursal_id')
			->where('casa_central_id', $comercio_id)
			->get();


			} else {
		    
		    // si el usuario es sucursal
		    
			$this->sucursal_id = $comercio_id;
			$this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
			$this->casa_central_id = $this->casa_central->casa_central_id;

			$this->lista_precios = lista_precios::where('comercio_id',$this->casa_central_id)->get();

			$this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
			->select('users.name','sucursales.sucursal_id')
			->where('casa_central_id', $this->casa_central->casa_central_id)
			->get();

			}
			
			// Tiene wocommerce 
			
			$wc = wocommerce::where('comercio_id', $comercio_id)->first();

			if($wc == null){
				$this->wc_yes = 0;
			} else {
				$this->wc_yes = $wc->id;

			}
			
			// Atributos 
			
			$this->atributos_var = atributos::where('comercio_id', $comercio_id)->get();
			
			// Variaciones
			
			$this->variaciones = variaciones::join('atributos','atributos.id','variaciones.atributo_id')
			->select('variaciones.*','atributos.nombre as atributo')
			->where('atributos.comercio_id', $comercio_id)->get();
			
			// Carrito variaciones
			
			$cart = new CartVariaciones;
			$this->cart = $cart->getContent();
			
			$cart_productos_atributos = new CartProductosAtributos;
			$this->cart_productos_atributos = $cart_productos_atributos->getContent();
			
			$this->relacion_precio_iva = 0;
			
			$this->RenderProduct();
			
			return view('livewire.products_add.added', [
			    'cart' => $this->cart,
			    'product_added' => $this->product_added,
			    'cart_productos_atributos' => $this->cart_productos_atributos,
			    'relacion_precio_iva' => $this->relacion_precio_iva,
				'categories' => Category::orderBy('name','asc')->where('comercio_id', 'like', $comercio_id)->get(),
				'sucursales' => User::orderBy('name','asc')->where('profile', 'Sucursal')->where('comercio_id', 'like', $comercio_id)->get(),
				'almacenes' => seccionalmacen::orderBy('nombre','asc')->where('comercio_id', 'like', $comercio_id)->get(),
				'prov' => proveedores::orderBy('nombre','asc')->where('comercio_id', 'like', $comercio_id)->get()
			])
			->extends('layouts.theme.app')
			->section('content');

}

	public function Update()
	{
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$rules  =[
			'name' => "required|min:3,name,{$this->selected_id}",
			'barcode' => ['required',Rule::unique('products')->ignore($this->selected_id)->where('comercio_id',$comercio_id)->where('eliminado',0)],
			'alerts' => 'required',
			'proveedor' => 'required',
			'almacen' => 'not_in:Elegir',
			'categoryid' => 'required|not_in:Elegir',
			'stock_descubierto' => 'required|not_in:Elegir',
			'tipo_producto' => 'required|not_in:Elegir'
		];

		$messages = [
			'name.required' => 'Nombre del producto requerido',
			'name.min' => 'El nombre del producto debe tener al menos 3 caracteres',
			'barcode.required' => 'El codigo es requerido',
			'proveedor.required' => 'El proveedor es requerido',
			'alerts.required' => 'Ingresa el valor mínimo en existencias',
			'almacen.not_in' => 'Ingresa la seccion del almacen',
			'categoryid.not_in' => 'Elige un nombre de categoría diferente de Elegir',
			'tipo_producto.not_in' => 'Elige el tipo de producto',
		'stock_descubierto.not_in' => 'Elige si maneja o no stock',
		];


		$this->validate($rules, $messages);

		$product = Product::find($this->selected_id);
		
		// dd($product);

		$usuario_id = Auth::user()->id;

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$product->update([
			'name' => $this->name,
			'cost' => $this->cost,
			'barcode' => $this->barcode,
			'tipo_producto' => $this->tipo_producto,
			'seccionalmacen_id' => $this->almacen,
			'alerts' => $this->alerts,
			'proveedor_id' => $this->proveedor,
			'stock_descubierto' => $this->stock_descubierto,
			'category_id' => $this->categoryid,
			'iva' => $this->iva,
			'relacion_precio_iva' => $this->relacion_precio_iva,
			'cod_proveedor' => $this->cod_proveedor,
			'mostrador_canal' => $this->mostrador_canal,
			'ecommerce_canal' => $this->ecommerce_canal,
			'wc_canal' => $this->wc_canal,
			'descripcion' => $this->descripcion,
			'producto_tipo' => $this->producto_tipo,
			'updated_at' => Carbon::now()

		]);
		

    if($this->producto_tipo == "v") {
    $this->QueryProductoVariable($product);
    } else {
    $this->QueryProductoSimple($product);
    }
    
    
    ////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////
    ////////////////////////// WOCOMMERCE //////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////
    
    if($this->wc_canal == true) {
        $this->UpdateWocommerce($product);
    }
    

////////////////////////////////////////        SI TIENE UNA IMAGEN LA CARGA      ///////////////////////////////////////////////////////////////
        
       if($this->imagen_seleccionada) {
           $this->GuardarImagenGaleria($product->id);
       }
							
							//////////////////////////////////////////////////////////////////////////////



		$this->resetUI();
		$this->emit('product-updated', 'Producto Actualizado');


	}


    
	protected $listeners =[
		'deleteRow' => 'Destroy',
		'Base64' => 'Base64',
		'accion-lote' => 'AccionEnLote',
		'deleteVariacion' => 'DestroyVariacion',
		'ConfirmCheck' => 'DeleteSelected'
	];


	public function DestroyImage(Product $product)
	{

		if($this->image === $product->image) {

		$imageTemp = $product->image;

		$product->update([
			'image' => null
		]);

		if($imageTemp !=null) {
			if(file_exists('storage/products/' . $imageTemp )) {
				unlink('storage/products/' . $imageTemp);
			}
		}

		$this->image = '';
		$this->emit('product-deleted', 'Imagen Eliminada');
	} else {
		$this->image = $product->image;
	}

}



/// ---------- VARIACIONES -------------------//


/////// GUARDAR VARIACION   ////////


public function GuardarVariacion() {

						$cart = new CartVariaciones;

						///////// GUARDAR VARIACIONES  /////////

					    if(Auth::user()->comercio_id != 1)
						$comercio_id = Auth::user()->comercio_id;
						else
						$comercio_id = Auth::user()->id;

						$this->referencia_id = Carbon::now()->format('dmYHis').'-'.$comercio_id;

                	$v_arr = [];
                	$v_id_arr = [];
                	$v_id_arr = [];
                	
                	
                	if($this->variacion_atributo != "c") {
									
									foreach ($this->variacion_atributo as $key => $value) {

									    $var_arr = variaciones::find($this->variacion_atributo[$key]);
                            
									    $var_arr = $var_arr->nombre;
									    $var_id_arr = $this->variacion_atributo[$key];
									    

											productos_variaciones::create([
												'atributo_id' =>  $key,
												'variacion_id' => $this->variacion_atributo[$key],
												'comercio_id' => $comercio_id,
												'referencia_id' => $this->referencia_id
											]);

											array_push($v_arr,$var_arr);
											array_push($v_id_arr,$var_id_arr);
									}

									$v_arr = implode(" - " , $v_arr);
									natsort($v_id_arr); 
									$v_id_arr = implode("," , $v_id_arr);


									$product = array(
									'referencia_id' => $this->referencia_id,
									'var_nombre' => $v_arr,
									'var_id' => $v_id_arr
								);


								$cart->addProduct($product);
								
                	} else {
                	    dd("Debe agregar al menos una variacion");
                	}
                	


}


///////// ELIMINAR VARIACION   /////////////


public function DestroyVariacion($referencia_variacion) {

if(Auth::user()->comercio_id != 1)
$comercio_id = Auth::user()->comercio_id;
else
$comercio_id = Auth::user()->id;

$productos_variaciones_datos = productos_variaciones_datos::where('referencia_variacion', $referencia_variacion)->first();

$productos_variaciones_datos->eliminado = 1;
$productos_variaciones_datos->save();


$wc = wocommerce::where('comercio_id', $comercio_id)->first();

if($wc != null){

$woocommerce = new Client(
	$wc->url,
	$wc->ck,
	$wc->cs,

		[
				'version' => 'wc/v3',
		]
);


$pvw = productos_variaciones_datos::where('referencia_variacion', $referencia_variacion)->first();

if($pvw != null) {
$woocommerce->delete('products/'.$pvw->product_id.'/variations/'.$pvw->wc_variacion_id, ['force' => true]);

}

}


$cart = new CartVariaciones;
$cart->removeProduct($referencia_variacion);

}

/// ------------------------------------------------------------------------------ ////




// ------------------------------------------------------------- //



  	public function ProductoTipo() {
  	    
  	    if($this->producto_tipo == "s") {
	
	// ESTABLECER EL STOCK PREDETERMINADO CON 0 ANTES DE AGREGAR //
	
	$this->productos_stock_sucursales = sucursales::where('casa_central_id', $this->casa_central_id)->get();
	
	$this->stock_sucursal["0|0|0|0"] = 0;
	foreach($this->productos_stock_sucursales as $llave => $sucu) {
			$this->stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] = 0;
			}
			
	// ESTABLECER EL PRECIO PREDETERMINADO EN 0 ANTES DE AGREGAR //
	
	} 
	if($this->producto_tipo == "v") {
	    
	}

  	    
  	}


////// MOSTRAR PREVISUALIZACION DE BASE 64  //////////

      	public function Base64($base64)

  	{

  	    $this->base64 = $base64;

  	    $this->mostrar_base64 = $base64;

  	    $this->emit('hide-cropp', '');


  	}
 	  	

// ---------  RESET UI   --------------- //


	public function resetUI()
	{
	    $this->stock_productos_sucursales = 0;
		$this->name ='';
		$this->tipo_carga_imagen = '';
		$this->imagen_seleccionada = '';
		$this->barcode ='';
		$this->base64 = '';
		$this->cost ='';
		$this->price ='';
		$this->iva = 0;
		$this->relacion_precio_iva = 0;
		$this->stock ='';
		$this->mostrador_canal = 0;
		$this->tipo_producto = "Elegir";
		$this->wc_canal =0;
		$this->ecommerce_canal =0;
		$this->alerts ='';
		$this->proveedor ='';
		$this->descripcion ='';
		$this->search ='';
		$this->inv_ideal ='';
		$this->categoryid ='Elegir';
		$this->image = null;
		$this->selected_id = 0;
		$this->pageTitle = 'Listado';
		$this->proveedor = '1';
		$this->componentName = 'Productos';
		$this->categoryid = 'Elegir';
		$this->almacen = 'Elegir';
		$this->stock_descubierto = 'Elegir';
		$this->producto_tipo = '';
		$this->referencia_variacion = 0;
		$categoria_wc = 0;
		$this->precio_lista = null;
	    $this->stock_sucursal = null;
		

	}

	public function resetUICategoria()
	{
		$this->name_categoria ='';

	}

	public function resetUIAlmacen()
	{
		$this->name_almacen ='';

	}


public function ModalImagenes() {
    
     $this->tipo_carga_imagen = 1;
     
     $this->style_tipo_1 = "active";
     $this->style_tipo_2 = "";
     
    	if(Auth::user()->comercio_id != 1)
			$comercio_id = Auth::user()->comercio_id;
			else
			$comercio_id = Auth::user()->id;
            
            $this->comercio_id = $comercio_id;
            
			$this->tipo_usuario = User::find($comercio_id);
			$this->sucursal_id = $comercio_id;
		    
		    if($this->tipo_usuario->sucursal != 1) {

			$this->casa_central_id = $comercio_id;
			
	
		    } else {
		  
			$this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
			$this->casa_central_id = $this->casa_central->casa_central_id;
		    }


   $this->imagenes = imagenes::where('eliminado',0);
   
   	// Buscar imagen 
	
	if(strlen($this->search_imagenes) > 0) {
	
	
	$this->imagenes = $this->imagenes->where('imagenes.name', 'like', '%' . $this->search_imagenes . '%');    
	}	
   
      $this->imagenes = $this->imagenes->where( function($query) {
					 $query->where('comercio_id', $this->comercio_id)
						->orWhere('comercio_id', $this->casa_central_id);
					});
		

   $this->imagenes = $this->imagenes->orderBy('created_at','desc')
   ->get();

    $this->emit('modal-imagen-show',"");
}

public function SeleccionarImagen($id) {
    
    $this->imagen_seleccionada = $id;
   
    
}

public function AceptarSeleccionarImagen() {
    
    // si la imagen ya esta guardada, la busca no mas
    
    if($this->tipo_carga_imagen == 1) {
        
    $imagen = imagenes::find($this->imagen_seleccionada);
    $this->base64 = $imagen->base64;    
    
        
    }
    
    // Si la imagen no esta guardada, la guarda
    
    if($this->tipo_carga_imagen == 2) {
        
        if(Auth::user()->comercio_id != 1)
        $comercio_id = Auth::user()->comercio_id;
        else
        $comercio_id = Auth::user()->id;
        		
        $rules = [
        	'images' => 'image|max:3024', // 3MB Max
        ];
        
        $messages = [
            'images.image' => 'El archivo debe ser una imagen',
        	'images.max' => 'La imagen debe pesar 3 MB como maximo'
        ];
        
        $this->validate($rules, $messages);
        
        $nombre_imagen1 = $this->images->getClientOriginalName();
                
                
        $nombre_imagen2 =str_replace(' ', '+|-|+', $nombre_imagen1);    
        
        $urlfoto    = $this->images;
        $ruta=public_path('/storage/products/'.$nombre_imagen2);
                 
        Image::make($urlfoto->getRealPath())->save($ruta,80);
                 
        $data = (string) Image::make($urlfoto->getRealPath())->encode('data-url', 80);
        
        $this->base64 = $data;
                
        $imagen_seleccionada = imagenes::create([
                    'name' => $nombre_imagen1,
                    'url' => $nombre_imagen2,
                    'base64' => $data,
                    'comercio_id' => $comercio_id,
                    'eliminado' => 0
                
                ]);
                 
        $this->imagen_seleccionada = $imagen_seleccionada->id;
        
    }
    
    $this->emit('modal-imagen-hide',"");
    
}

public function TipoCargaImagen($id) {
    $this->tipo_carga_imagen = $id;
    
    if($id == 1) {
     $this->style_tipo_1 = "active";
     $this->style_tipo_2 = "";
    } else {
        $this->style_tipo_1 = "";
     $this->style_tipo_2 = "active";
     
    }
}

public function GuardarImagenGaleria($product_id) {
    
    $imagen = imagenes::find($this->imagen_seleccionada);
    $imageName = $imagen->url;
    
    $product = Product::find($product_id);
    $product->image = $imageName;
	$product->save();
	
    // Wocommerce
    
	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

	$wc = wocommerce::where('comercio_id', $comercio_id)->first();
    
//    $wc = null;
    
	if($wc != null){

	$woocommerce = new Client(
		$wc->url,
		$wc->ck,
		$wc->cs,

			[
					'version' => 'wc/v3',
			]
	);


	if($this->wc_canal == true) {
	    
	    
	$data = [

	'images' => [
	[
	'src' => 'https://express.flamincoapp.com.ar/storage/products/'.$imageName
	]
	]

	];

	$this->wocommerce_product_id = 'products/'.$product->wc_product_id;

	$woocommerce->put($this->wocommerce_product_id , $data);

    

	}
	}
	
}

public function resetUIImagen() {
    $this->imagen_seleccionada = '';
}

public function Buscarimagen() {
    
    $this->search_imagenes = $this->search_imagenes;
    
    $this->ModalImagenes();
}


public function RenderProduct() {
        
        $product = Product::find($this->product_added);
		
		// dd($this->product_added);
		
		if($product->tipo_producto == 2) {

			$this->receta = Product::leftjoin('recetas as r','r.product_id','products.id')
									->leftjoin('insumos','insumos.id','r.insumo_id')
									->join('unidad_medidas','unidad_medidas.id','r.unidad_medida')
									->select(receta::raw(' SUM(r.cantidad*r.costo_unitario*r.relacion_medida) AS cost'))
									->where('products.id', 'like', $product->id)
									->where('products.eliminado', 'like', 0)
									->first();

			if($this->receta != null) {
				$this->cost = floatval($this->receta->cost);
			} else {
				$this->cost = 0;
			}
		} else {

			$this->cost = $product->cost;
		}

		$this->selected_id = $product->id;
		$this->name = $product->name;
		$this->tipo_producto = $product->tipo_producto;
		$this->barcode = $product->barcode;

		$this->price = $product->price;
		$this->wc_product_id = $product->wc_product_id;
		$this->stock = $product->stock;
		$this->almacen = $product->seccionalmacen_id;
		$this->alerts = $product->alerts;
		$this->inv_ideal = $product->inv_ideal;
		$this->proveedor = $product->proveedor_id;
		$this->stock_descubierto = $product->stock_descubierto;
		$this->categoryid = $product->category_id;
		$this->cod_proveedor = $product->cod_proveedor;
		$this->image = $product->image;
		$this->mostrador_canal = $product->mostrador_canal;
		$this->ecommerce_canal = $product->ecommerce_canal;
		$this->wc_canal = $product->wc_canal;
		$this->wc_product_id = $product->wc_product_id;
		$this->descripcion = $product->descripcion;
		$this->iva = $product->iva;
		$this->relacion_precio_iva = $product->relacion_precio_iva;
		$this->producto_tipo = $product->producto_tipo;
		
		if($this->image != null) { 
		 $ruta=public_path('/storage/products/'.	$this->image);
		 
		 if (file_exists($ruta)){
            $this->base64 = (string) Image::make($ruta)->encode('data-url');
            } else {
            $this->base64 = null;    
            }

		 
         
		}

		//////////////////////////     SI EL PRODUCTO ES SIMPLE          ////////////////////////////////////////
		if($this->producto_tipo == "s") {

			$this->stock_productos_sucursales = productos_stock_sucursales::select('sucursal_id','stock')
														->where('product_id', $this->selected_id)
														->get();

			foreach($this->stock_productos_sucursales as $llave => $sucu) {
				$this->stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] = $sucu['stock'];
			}

			if($this->lista_precios != null) {

				$this->productos_lista_precios = productos_lista_precios::select('lista_id','precio_lista')
															->where('product_id', $this->selected_id)
															->get();
				foreach ($this->productos_lista_precios as $key => $lp) {
					$this->precio_lista["0|".$lp['lista_id']."|0|0"] = $lp['precio_lista'];
				}

			}
		}

		if($this->producto_tipo == "v") {


			//////////////////////////     SI EL PRODUCTO ES VARIABLE          ////////////////////////////////////////
			$cart = new CartVariaciones;

			$cart->clear();


			$this->productos_variaciones = productos_variaciones_datos::where('product_id', $this->selected_id)->select('referencia_variacion','variaciones','variaciones_id')->groupBy('referencia_variacion','variaciones','variaciones_id')->where('eliminado',0)->orderBy('created_at','desc')->get();

			$cart = new CartVariaciones;

			foreach ($this->productos_variaciones as $prod_var) {
				// code...
				$product = array(
				'referencia_id' => $prod_var->referencia_variacion,
				'var_nombre'=> $prod_var->variaciones,
				'var_id'=> $prod_var->variaciones_id,
				);

				$cart->addProduct($product);
			}

			$this->stock_productos_sucursales = productos_stock_sucursales::join('productos_variaciones_datos','productos_variaciones_datos.referencia_variacion','productos_stock_sucursales.referencia_variacion')
											->where('productos_stock_sucursales.product_id', $this->selected_id)->get();
											

			foreach($this->stock_productos_sucursales as $llave => $sucu) {
			    
			    
				$this->stock_sucursal[$sucu['referencia_variacion']."|".$sucu['sucursal_id']."|".$sucu['variaciones']."|".$sucu['variaciones_id']] = $sucu['stock'];
				
				}

			$this->datos_variaciones = productos_variaciones_datos::where('product_id', $this->selected_id)->get();

			foreach($this->datos_variaciones as $llaves => $sucus) {
				$this->costos_variacion[$sucus['referencia_variacion']] = $sucus['cost'];
			}
			
			
			foreach($this->datos_variaciones as $llaves => $sucus) {
				$this->cod_variacion[$sucus['referencia_variacion']] = $sucus['codigo_variacion'];
			}
			
			$this->productos_lista_precios = productos_lista_precios::where('product_id', $this->selected_id)->get();

			foreach ($this->productos_lista_precios as $key => $lp) {
				$this->precio_lista[$lp['referencia_variacion']."|".$lp['lista_id']] = $lp['precio_lista'];
			}

		}
}
}
