<?php
namespace App\Traits;


// Trait

use App\Traits\WocommerceTrait;
use App\Traits\ProductsTrait;
use App\Traits\CartTrait;

// Modelos


use App\Models\lista_precios;
use App\Models\Product;
use App\Models\Category;
use App\Models\proveedores;
use App\Models\wocommerce;
use App\Models\atributos;
use App\Models\imagenes;
use App\Models\descargas;
use App\Models\variaciones;
use App\Models\productos_variaciones_datos;
use App\Models\ClientesMostrador;
use App\Models\productos_variaciones;
use App\Models\receta;
use App\Models\User;
use App\Models\sucursales;
use App\Models\productos_stock_sucursales;
use App\Models\productos_lista_precios;
use App\Models\actualizacion_precios;
use App\Models\historico_stock;
use App\Models\seccionalmacen;
use App\Models\datos_facturacion;

// services 

use App\Services\CartVariaciones;

// Otros

use Illuminate\Support\Facades\Storage;
use Notification;
use App\Notifications\NotificarCambios;
use Illuminate\Validation\Rule;
use DB;
use Intervention\Image\Facades\Image;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use Carbon\Carbon;


trait ProductsImagenesTrait {

// IMAGENES


// Modal de la seccion imagenes 

public function ModalImagenesProduct() {
    
    //dd('hola');
    
    //dd($this->base64);
    
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

// cambiar el tipo de carga de la imagen 

public function TipoCargaImagenProduct($id) {
    
    $this->tipo_carga_imagen = $id;
    
    if($id == 1) {
     $this->style_tipo_1 = "active";
     $this->style_tipo_2 = "";
    } else {
        $this->style_tipo_1 = "";
     $this->style_tipo_2 = "active";
     
    }
    
}

// Desvincular una imagen de un producto 

	public function DestroyImageProduct($product)
	{

        $product = Product::find($product);
        
		if($this->image === $product->image) {

		$imageTemp = $product->image;

		$product->update([
			'image' => null
		]);

//		if($imageTemp !=null) {
//			if(file_exists('storage/products/' . $imageTemp )) {
//				unlink('storage/products/' . $imageTemp);
//			}
//		}

		$this->image = '';
		$this->emit('product-deleted', 'Imagen Eliminada');
	} else {
		$this->image = $product->image;
	}

}


// Guardar una imagen nueva


public function AceptarSeleccionarImagenProduct() {
    
    // si la imagen ya esta guardada, la busca no mas
    
    if($this->tipo_carga_imagen == 1) {
        
    $imagen = imagenes::find($this->imagen_seleccionada);
    $this->base64 = $imagen->base64;    
    
    $this->imagen_seleccionada = $this->imagen_seleccionada;    
    }
    
    // Si la imagen no esta guardada, la guarda
    
    if($this->tipo_carga_imagen == 2) {
        
        if(Auth::user()->comercio_id != 1)
        $comercio_id = Auth::user()->comercio_id;
        else
        $comercio_id = Auth::user()->id;
        		
        $rules = [
        	'images' => 'image', // 3MB Max
        ];
        
        $messages = [
            'images.image' => 'El archivo debe ser una imagen'
        ];
        
        $this->validate($rules, $messages);
        
        $nombre_imagen1 = $this->images->getClientOriginalName();
        
        $nombre_imagen2 =str_replace(' ', '+|-|+', $nombre_imagen1);   
        
        $nombre_imagen2 = Carbon::now()->format('d_m_Y_H_i_s') . '_' . $nombre_imagen2;
        
        $urlfoto    = $this->images;
        $ruta=public_path('/storage/products/'.$nombre_imagen2);
                 
        Image::make($urlfoto->getRealPath())->save($ruta,80);
                 
        $data = (string) Image::make($urlfoto->getRealPath())->encode('data-url', 60);
        
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


// Vincula una imagen a una galeria 

public function GuardarImagenGaleriaProduct($product_id) {
    
    
    $imagen = imagenes::find($this->imagen_seleccionada);
    
   // dd($imagen);
    
    $imageName = $imagen->url;
    
    $product = Product::find($product_id);
    $product->image = $imageName;
	$product->save();
	
	//dd($product);
	
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
	'src' => 'https://pruebaexpress.flamincoapp.com.ar/storage/products/'.$imageName
	]
	]

	];

	$this->wocommerce_product_id = 'products/'.$product->wc_product_id;

	$woocommerce->put($this->wocommerce_product_id , $data);

    

	}
	}
	
}

// resetea la UI de la imagen 

public function resetUIImagen() {
    $this->imagen_seleccionada = '';
}


public function BuscarimagenProduct() {
    
    $this->search_imagenes = $this->search_imagenes;
    
    $this->ModalImagenes();
}


// IMAGENES

    // Modal para seccion imagenes
    
    public function ModalImagenes() {
        
        $this->ModalImagenesProduct();
    
        
    }
    
    // cambio de tipo de carga de la imagen 
    
    public function TipoCargaImagen($id) {

    $this->TipoCargaImagenProduct($id);

    }

    // Guardar una imagen nueva
    
    public function AceptarSeleccionarImagen() {
        
        $this->AceptarSeleccionarImagenProduct();
    
        
    }
    
    // Selecciona una imagen de la biblioteca
    
    public function SeleccionarImagen($id) {
    
    $this->imagen_seleccionada = $id;
 
    }
    
    // Asocia una imagen a un producto 
    
    
    public function GuardarImagenGaleria($product_id) {
    $this->GuardarImagenGaleriaProduct($product_id);
    }
    
    // Desvincular una imagen de un producto 

	public function DestroyImage(Product $product)
	{

        // $product = Product::find($product);
        
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
		$this->base64 = null;
		$this->emit('product-deleted', 'Imagen Eliminada');
	} else {
		$this->image = $product->image;
	}

}


public function DestroyImageBase64() {
    $this->base64 = null;
}

     
   public function StoreImagen($imagenBase64, $nombreArchivo)
    {
    //dd($nombreArchivo);
    // dd($base64);
   
    if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;
    
    // Obtener el nombre de la imagen
    $nombre_imagen1 = $nombreArchivo;
    
    $nombre_imagen2 =str_replace(' ', '+|-|+', $nombre_imagen1); 
        
    $nombre_imagen2 = Carbon::now()->format('d_m_Y_H_i_s') . '_' . $nombre_imagen2;

 
    // Decodificar la cadena Base64 y guardar la imagen
    $image = Image::make($imagenBase64);
    $image->save(public_path('/storage/products/'.$nombre_imagen2));
    
     
    if ($image) {
    
    $imagen_id = imagenes::create([
        'name' => $nombre_imagen1,
        'url' => $nombre_imagen2,
        'base64' => $imagenBase64,
        'comercio_id' => $comercio_id,
        'eliminado' => 0
    ]);
    
    $this->TipoCargaImagen(1);
    $this->SeleccionarImagen($imagen_id->id);
    $this->ModalImagenesProduct();
        
    } 
   
    }
    
    
}
