<?php
namespace App\Traits;

// Trait

use App\Traits\ProductsTrait;

// Modelos
use App\Models\detalle_compra_proveedores;
use App\Models\Product;
use App\Models\bancos;
use App\Models\wocommerce;
use App\Models\datos_facturacion;
use App\Models\sucursales;
use App\Models\productos_variaciones;
use App\Models\productos_variaciones_datos;
use App\Models\User;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\atributos;
use App\Models\lista_precios;
use App\Models\Category;
use App\Models\variaciones;
use App\Models\productos_stock_sucursales;
use App\Models\ClientesMostrador;
use App\Models\metodo_pago;
use App\Models\productos_lista_precios;
use Illuminate\Support\Facades\Log;
// Otros

use Spatie\Permission\Models\Role;
use \WeDevs\ORM\Eloquent\Facades\DB;
use WorDBless\Users as WP_User;

use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use Carbon\Carbon;

use GuzzleHttp\Client as ClientG;

trait WocommerceTrait {

//use ProductsTrait;

public $name_flaminco,$name_wc,$sku_flaminco,$totalProducts,$productos_creados, $sku_wc,$price_flaminco,$price_wc,$stock_flaminco,$stock_wc;

public $proceso_importacion = 0;

public function checkCredentials($url, $ck, $cs)
    {
        $url = $url; // Reemplaza con la URL de tu tienda WooCommerce
        $consumerKey = $ck; // Reemplaza con tu consumer key
        $consumerSecret = $cs; // Reemplaza con tu consumer secret

        $client = new ClientG();

        try {
            $response = $client->get($url . '/wp-json/wc/v3/products', [
                'auth' => [$consumerKey, $consumerSecret]
            ]);

            // Si las credenciales son correctas, la solicitud debe devolver una respuesta exitosa
            if ($response->getStatusCode() === 200) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
    
    


// ---------- WOCOMMERCE  --------------- //

public function SincronizarProducto($product_id){
    $product = Product::find($product_id); // El SKU del producto que deseas buscar
    $woocommerce = $this->GetClient($product->comercio_id);	

    if($product->wc_product_id == null){
        // El producto existe
        if($product->producto_tipo == "s"){$this->WocommerceUpdateSimple($product_id);}
        if($product->producto_tipo == "v"){$this->WocommerceUpdateVariable($product_id);}         
    } else {
        // El producto no existe
        if($product->producto_tipo == "s"){$this->WocommerceStoreSimple($product_id);}
        if($product->producto_tipo == "v"){$this->WocommerceStoreVariable($product_id);}
    }
    
    
}

////////  WOCOMMERCE SIMPLE ////////

// GUARDAR
public function WocommerceStoreSimple($product_id) {
            
$product = Product::find($product_id);

$woocommerce = $this->GetClient($product->comercio_id);	

try {

    ///////// DEFINIR SI TRABAJA O NO CON STOCK  /////////
        
    if($product->stock_descubierto == "si") {
            
    $manage_stock = 'no';
  //  $this->stock_quantity = $this->stock;
            
    } else {
            
    $manage_stock = 'yes';
//    $this->stock_quantity  = null;
        
    }
      // STOCK //
      $stock_origen = $this->getStockWC($product->id, 0, 0);
     
     // PRECIO BASE 
    
      $precio_origen = $this->getPrecioWC($product->id, 0,0);

        
      // busca la categoria de WC
      $categoria_wc = $this->FindOrCreateCategoryByName($product->category_id);
      
       // DATOS DE LISTAS DE PRECIOS //
       $list = $this->SetListaPrecios($product->id,0);

        // Determina el estado del producto en función de wc_canal
        $status_product = $product->wc_canal === 1 ? 'publish' : 'draft';

        $data = [
        'name' => $product->name,
        'type' => 'simple',
        'sku' => $product->barcode,
        'status' => $status_product,
        'manage_stock' => true,
        'stock_quantity' => $stock_origen->stock,
        'stock_status' => "instock",
        'backorders' => $manage_stock,
        'regular_price' => $precio_origen->precio_lista,
        'categories' => [
        [
        'id' => $categoria_wc,
        ]
        ],
        
        'meta_data' => $list
        ];
  

        $wc_product_id = $woocommerce->post('products', $data);
        
        $product->update([
        'wc_product_id' => $wc_product_id->id
        ]);
        
        
        
    	// Setea la imagen si es que tiene
	    
        if($product->image != null) {
            $this->SetImagen($product,$woocommerce,true);
        }
		
		

    
} catch (HttpClientException $e) {
    $errorResponse = json_decode($e->getResponse()->getBody());

    if ($errorResponse && isset($errorResponse->message, $errorResponse->code)) {
        $errorMessage = $errorResponse->message;
        $errorCode = $errorResponse->code;

        // Ahora puedes mostrar el mensaje de error de la manera que prefieras
        var_dump("Error: $errorMessage [$errorCode]");
    } else {
        // Si no se puede decodificar la respuesta JSON, muestra un mensaje genérico
        var_dump("Se produjo un error al procesar la solicitud.");
    }

    // También puedes registrar el error, enviar notificaciones, etc., según tus necesidades
}


}
        
// ACTUALIZAR

public function WocommerceUpdateSimple($product_id) {

   
$product = Product::find($product_id);

$woocommerce = $this->GetClient($product->comercio_id);	

            try {
        
            ///////// DEFINIR SI TRABAJA O NO CON STOCK  /////////
                
            if($product->stock_descubierto == "si") {
                    
            $manage_stock = 'no';
          //  $stock_quantity = $stock;
                    
            } else {
                    
            $manage_stock = 'yes';
        //    $stock_quantity  = null;
                
            }
                
                
              // STOCK //
              $stock_origen = $this->getStockWC($product->id, 0, 0);
             
             // dd($this->stock_origen->stock);
             // PRECIO BASE 
            
              $precio_origen = $this->getPrecioWC($product->id, 0,0);
        
              // busca la categoria de WC
              $categoria_wc = $this->FindOrCreateCategoryByName($product->category_id);
              
             // DATOS DE LISTAS DE PRECIOS //
              $list = $this->SetListaPrecios($product->id,0);
        
                // Determina el estado del producto en función de wc_canal
                $status_product = $product->wc_canal === 1 ? 'publish' : 'draft';
        
                $data = [
                'name' => $product->name,
                'type' => 'simple',
                'sku' => $product->barcode,
                'status' => $status_product,
                'manage_stock' => true,
                'stock_quantity' => $stock_origen->stock,
                'stock_status' => "instock",
                'backorders' => $manage_stock,
                'regular_price' => $precio_origen->precio_lista,
                'categories' => [
                [
                'id' => $categoria_wc,
                ]
                ],
                
                'meta_data' => $list
                ];
                
        
        
               
        	/////////  CHEQUEA SI LOS PRODUCTOS ESTAN EN WOCOMMERCE O NO  /////////
        
        	/////////  SI EL PRODUCTO ESTA REGISTRADO EN WOCOMMERCEK  /////////
        
                
            // Busca la categoria por nombre 
            
           /* $params = [
                'search' => $product->name
                ];
                
            $result = $woocommerce->get('products', $params);*/
            
            $result = $this->FindProductWC($product->barcode,$product->comercio_id);
            //$result = $this->VerifyProducto($woocommerce,$product);
            
            // Si el producto esta creado en wocommerce y en laravel
            
            if(!empty($result)) {
                //$product->wc_product_id = $result[0]->id;
                $product->wc_product_id = $result;
        
                $product->save();
                
                $wocommerce_product_id = 'products/'.$product->wc_product_id;
        		$woocommerce->put($wocommerce_product_id , $data);
        		
            } else {
                // crear el producto
               	/////////  SI EL PRODUCTO NO ESTA REGISTRADO EN WOCOMMERCE  /////////
        		$wc_product_id = $woocommerce->post('products', $data);
        		$product->update([
        			'wc_product_id' => $wc_product_id->id
        		]);
        
                
            }
            
            	// Setea la imagen si es que tiene
        	    
                if($product->image != null) {
                    $this->SetImagen($product,$woocommerce,true);
                }
        		
        		
        
        
        // Setea el push
        
        $this->SetWcPush($product_id,0);
        
        /////////////
    
    } catch (HttpClientException $e) {
    $errorResponse = json_decode($e->getResponse()->getBody());

    if ($errorResponse && isset($errorResponse->message, $errorResponse->code)) {
        $errorMessage = $errorResponse->message;
        $errorCode = $errorResponse->code;

        // Ahora puedes mostrar el mensaje de error de la manera que prefieras
        var_dump("Error: $errorMessage [$errorCode]");
    } else {
        // Si no se puede decodificar la respuesta JSON, muestra un mensaje genérico
        var_dump("Se produjo un error al procesar la solicitud.");
    }

    // También puedes registrar el error, enviar notificaciones, etc., según tus necesidades
    }

}        


//////// GUARDAR WOCOMMERCE VARIABLE //////

public function WocommerceStoreVariable($product_id) {

$product = Product::find($product_id);

$woocommerce = $this->GetClient($product->comercio_id);	


    try {
    
        $atribut = [];
        $v = [];
        	// DATOS DE VARIACIONES //
        	$atributos =	productos_variaciones::join('atributos','atributos.id','productos_variaciones.atributo_id')
        	->select('atributos.id','atributos.nombre')
        	->where('productos_variaciones.producto_id', $product_id )
        	->groupBy('atributos.id','atributos.nombre')
        	->get();
        
        $x = 0;
        
        foreach ($atributos as $atributes) {
        ///     VARIACIONES          ///
        
        $variaciones = variaciones::where('atributo_id',$atributes->id)->select('nombre','atributo_id')->get();
        
        foreach ($variaciones as $var) {
        
        	if($var->atributo_id == $atributes->id) {
        
        	$var = $var->nombre;
        	array_push($v,$var);
        
        	}
        
        }
        	///     VARIACIONES          ///
        
        		$atributos =
        		array(
        	    "name" => $atributes->nombre,
        		'position' => 0,
        		'stock_status' => 'instock',
        		'manage_stock' => true,
         		'visible' => true,
         		'variation' => true,
        		'options' => $v
        		);
        
        		array_push($atribut,$atributos);
        
        }
        
        ///    CATEGORIA WC/////
        
        $categoria_wc = $this->FindOrCreateCategoryByName($product->category_id);
        
        ////////////////////////////////
        
        
        	$data_p = [
            'name' => $product->name,
            'type' => 'variable',
            'sku' => $product->barcode,
        		'categories' => [
        				[
        						'id' => $categoria_wc,
        				]
        		],
                'attributes' => $atribut
                ];
        
        $data_p = $woocommerce->post('products', $data_p);   // ACA EL POST DE PRODUCTOS
        
        $product->update([
        'wc_product_id' => $data_p->id
        	]);
        
        $a = [];
        $lista_precios_array = [];
        $datex = [];
        
        $datos_origen =	productos_variaciones::where('productos_variaciones.producto_id', $product_id)
        ->select('productos_variaciones.referencia_id')
        ->groupBy('productos_variaciones.referencia_id')
        ->get();
        
        $i = 0;
        
        foreach ($datos_origen as $d) {
        
        
        // STOCK //
        $stock_origen = productos_stock_sucursales::where('referencia_variacion', $d->referencia_id )->where('sucursal_id',0)->first();
        
        if($stock_origen != null) {
           $stock_origen = $stock_origen->stock;
        } else { $stock_origen = 0;}
        
        // LISTA DE PRECIO BASE //
        $precio_origen = productos_lista_precios::where('referencia_variacion', $d->referencia_id )->where('lista_id',0)->first();
        
        if($precio_origen != null) {
           $precio_origen = $precio_origen->precio_lista;
        } else { $precio_origen = 0;}
        
        // DATOS DE VARIACIONES //
        $datos =	productos_variaciones::join('variaciones','variaciones.id','productos_variaciones.variacion_id')
        ->join('atributos','atributos.id','productos_variaciones.atributo_id')
        ->select('atributos.nombre as name', 'variaciones.nombre as option')
        ->where('referencia_id', $d->referencia_id )->get();
        
        $datos = $datos->toArray();
        array_push($a,$datos);
        
             // DATOS DE LISTAS DE PRECIOS //
              $list = $this->SetListaPrecios($product->id, $d->referencia_id);
        
        /////////////////////////////////
        
        
        
        $data = [
        		'regular_price' => $precio_origen,
        		'stock_quantity' => $stock_origen,
        		'stock_status' => 'instock',
        		'manage_stock' => true,
        		'attributes' => $a[$i++],
        		'meta_data' =>  $list
        ];
        
        
        $d_variacion = $woocommerce->post('products/'.$data_p->id.'/variations', $data);
        
        $productos_variaciones_datos = productos_variaciones_datos::where('referencia_variacion',$d->referencia_id)->first();
        
        $productos_variaciones_datos->update([
        	'wc_product_id' => $data_p->id,
        	'wc_variacion_id' => $d_variacion->id,
        	'wc_push' => 0
        ]);
        
        }
        
        
            	// Setea la imagen si es que tiene
        	    
                if($product->image != null) {
                    $this->SetImagen($product,$woocommerce,true);
                }		
    
    } catch (HttpClientException $e) {
    $errorResponse = json_decode($e->getResponse()->getBody());

    if ($errorResponse && isset($errorResponse->message, $errorResponse->code)) {
        $errorMessage = $errorResponse->message;
        $errorCode = $errorResponse->code;

        // Ahora puedes mostrar el mensaje de error de la manera que prefieras
        var_dump("Error: $errorMessage [$errorCode]");
    } else {
        // Si no se puede decodificar la respuesta JSON, muestra un mensaje genérico
        var_dump("Se produjo un error al procesar la solicitud.");
    }

    // También puedes registrar el error, enviar notificaciones, etc., según tus necesidades
    }
    

}


public function WocommerceUpdateVariable($product_id) {

    $product = Product::find($product_id);

    //Debug:
    //return dd($product->wc_product_id);

    $woocommerce = $this->GetClient($product->comercio_id);	
    
            try {
            $atribut = [];
        
            $v = [];
                // DATOS DE VARIACIONES //
                $atributos =  productos_variaciones::join('atributos','atributos.id','productos_variaciones.atributo_id')
                ->select('atributos.id','atributos.nombre')
                ->where('productos_variaciones.producto_id', $product_id )
                ->groupBy('atributos.id','atributos.nombre')
                ->get();
        
            $x = 0;
        
        foreach ($atributos as $atributes) {
            ///     VARIACIONES          ///
        
            $variaciones = variaciones::where('atributo_id',$atributes->id)->select('nombre','atributo_id')->get();
        
            foreach ($variaciones as $var) {
        
                if($var->atributo_id == $atributes->id) {
        
                $var = $var->nombre;
                array_push($v,$var);
        
                }
        
        }
        	///     VARIACIONES          ///
        
        		$atributos =
        		array(
        	    "name" => $atributes->nombre,
        		'position' => 0,
         		'visible' => true,
         		'variation' => true,
        		'options' => $v,
        
        		);
        
        		array_push($atribut,$atributos);
        
        }
        
        
        ///    CATEGORIA WC/////
        
        //dd($product->category_id);
        
        $categoria_wc = $this->FindOrCreateCategoryByName($product->category_id);
        
        if($product->stock_descubierto == "si") {
        
        $manage_stock = 'no';
        
        } else {
        
        $manage_stock = 'yes';
        
        }
        
        ////////////////////////////////+
        
            
        
            //$stock_origen = $this->getStockWC($product->id, 0, 0);
        
            //return dd($product->alerts);
        
            if($product->wc_canal === 1){
                $status_product = 'publish';
            }else{
                $status_product = 'draft';
            }
            
        
        	$data_p = [
            'name' => $product->name,
            'type' => 'variable',   
            'sku' => $product->barcode,
        		'stock_quantity' => $product->alerts,       
        		'stock_status' => 'instock',
        		'manage_stock' => true,
        		'categories' => [
        				[
        						'id' => $categoria_wc,
        				]
        		],
        	'backorders' => $product->stock_descubierto ? 'no' : 'yes',
            'attributes' => $atribut,
            'status' =>  $status_product,
            ];
        
        
         
        
        
            $result_verify =  $this->VerifyProducto($woocommerce,$product);
            
            //Deebug
            //dd($result_verify);
            
            if($result_verify != 0) {
               // return dd('test 0');
                // Si esta creado en wocommerce
                $data_p = $woocommerce->put('products/'.$result_verify, $data_p);
                $product->wc_product_id = $result_verify;
                $product->save();
                
            } else {
               // return dd('test 1');
                $data_p = $woocommerce->post('products', $data_p);
                
                $product->wc_product_id = $data_p->id;
                $product->save();
            }
        
        $a = [];
        $lista_precios_array = [];
        $datex = [];
        
        $datos_origen =	productos_variaciones_datos::where('productos_variaciones_datos.product_id', $product_id)->where('eliminado',0)->get();
        
        $i = 0;
        
        foreach ($datos_origen as $d) {
        
        
        // STOCK //
        $stock_origen = productos_stock_sucursales::where('referencia_variacion', $d->referencia_variacion )->where('sucursal_id',0)->orderBy('id','desc')->first();
        
        
        if($stock_origen != null) {
           $stock_origen = $stock_origen->stock;
        } else { $stock_origen = 0;}
        //dd($this->stock_origen);
        
        // LISTA DE PRECIO BASE //
        $precio_origen = productos_lista_precios::where('referencia_variacion', $d->referencia_variacion )->where('lista_id',0)->orderBy('id','desc')->first();
        
        if($precio_origen != null) {
            $precio_origen = $precio_origen->precio_lista;
        } else {
           $precio_origen = 0; 
        }
        
        $datos =	productos_variaciones::join('variaciones','variaciones.id','productos_variaciones.variacion_id')
        ->join('atributos','atributos.id','productos_variaciones.atributo_id')
        ->select('atributos.nombre as name', 'variaciones.nombre as option')
        ->where('referencia_id', $d->referencia_variacion )->get();
        
        $datos = $datos->toArray();
        array_push($a,$datos);
        
        
        // DATOS DE LISTAS DE PRECIOS //
        $list = $this->SetListaPrecios($product->id, $d->referencia_id);
        
        
        
        $data = [
        		'regular_price' => $precio_origen,
        		'stock_quantity' => $stock_origen,
        		'stock_status' => 'instock',
        		'manage_stock' => true,
        		'backorders' => $manage_stock,
        		'attributes' => $a[$i++],
        		'meta_data' =>  $list
        ];
        
        //dd($data);
        // verificar si se encuentra la variacion 
        $encontro = $this->VerifyVariacion($woocommerce,$product,$d->wc_variacion_id);
        
        //dd($encontro);
        
        // Verificar si se encontr贸 el ID
        if ($encontro) {
        $wc = $woocommerce->put('products/'.$product->wc_product_id.'/variations/'.$d->wc_variacion_id, $data);
        
        } else {
        $wc = $woocommerce->post('products/'.$product->wc_product_id.'/variations', $data);
        }
        
        
        // NUEVO //
        $productos_variaciones_datos = productos_variaciones_datos::where('referencia_variacion',$d->referencia_variacion)->first();
        
        $productos_variaciones_datos->update([
        	'wc_product_id' => $data_p->id,
        	'wc_variacion_id' => $wc->id,
        	'wc_push' => 0
        ]);
        
        // Setea el push
        
        $this->SetWcPush($product->id,$d->referencia_variacion);
        
        /////////////
        
        }
        
        
        
            	// Setea la imagen si es que tiene
        	    
                if($product->image != null) {
                    $this->SetImagen($product,$woocommerce,true);
                }
        		
    
    } catch (HttpClientException $e) {
    $errorResponse = json_decode($e->getResponse()->getBody());

    if ($errorResponse && isset($errorResponse->message, $errorResponse->code)) {
        $errorMessage = $errorResponse->message;
        $errorCode = $errorResponse->code;

        // Ahora puedes mostrar el mensaje de error de la manera que prefieras
        var_dump("Error: $errorMessage [$errorCode]");
    } else {
        // Si no se puede decodificar la respuesta JSON, muestra un mensaje genérico
        var_dump("Se produjo un error al procesar la solicitud.");
    }

    // También puedes registrar el error, enviar notificaciones, etc., según tus necesidades
    }

    
}

public function VerifyProducto($woocommerce,$product) {
    
    // Busca el producto por nombre 
    
    $params = [
        //'search' => $product->name 
       // 'search' => $product->wc_product_id
       'include' => [$product->wc_product_id],
        ];


    $result = $woocommerce->get('products', $params);
    
    // Si la categoria esta creada en wocommerce
    
    if(!empty($result)) {
        return $result[0]->id;
    } else {
       return 0;
    }
    
}

public function VerifyVariacion($woocommerce,$product,$variacion) {

$GetVariaciones = $woocommerce->get('products/'.$product->wc_product_id.'/variations');

// Supongamos que el arreglo se llama $productos
$idBuscado = $variacion;  // ID que deseas buscar

$hasValueOne = false;

//dd($GetVariaciones);

foreach ($GetVariaciones as $item) {
    if ($item->id == $idBuscado) {
        // encontro el id
        // dd($item->id, $idBuscado);
        $hasValueOne = true;
        break;
    }
}

if ($hasValueOne) {
  return true;
} 
 return false;
}



public function SetWcPush($product_id, $variacion) {
    
// SI EL PRODUCTO ES VARIABLE //
if($variacion != 0) {

// Actualiza productos_variaciones_datos

$productos_variaciones_datos = productos_variaciones_datos::where('referencia_variacion',$variacion)->where('eliminado',0)->first();
$productos_variaciones_datos->wc_push = 0;
$productos_variaciones_datos->save();

// Si todas las variaciones ya fueron pusheadas, hay que modificar el registro en la tabla productos
$array = productos_variaciones_datos::where('product_id',$product_id)->where('eliminado',0)->get();

$hasValueOne = false;

foreach ($array as $item) {
    if ($item->wc_push == 1) {
        $hasValueOne = true;
        break;
    }
}

if (!$hasValueOne) {
   // dd($array);
   $product = Product::find($product_id);
   $product->wc_push = 0; 
   $product->save(); 
} 

}

// SI EL PRODUCTO ES SIMPLE //

if($variacion == 0) {

   $product = Product::find($product_id);
   $product->wc_push = 0;
   $product->save();
    
}

}

public function DeleteProductoIndividual($product_id) {
            
            $woocommerce = $this->GetClient($categoria->comercio_id);
            
            $product = Product::find($product_id);
            
            // Obtén todos los productos
            $products = $woocommerce->get('products');
        
            // ID del producto que deseas verificar
            $targetProductId = $product->wc_product_id; // Reemplaza con el ID que estás buscando
        
            // Busca el ID del producto en la lista
            $productExists = false;
            foreach ($products as $product) {
                if ($product->id == $targetProductId) { // Utiliza la notación de flecha para acceder a propiedades del objeto
                    $productExists = true;
                    break;
                }
            }
            
            if($productExists) {
            $woocommerce->delete('products/' . $targetProductId);
            }   
            
            
}
public function AccionEnLoteWc($id,$estado) {
    
    $product = Product::find($id);
    
    
   if( ($product->wc_product_id != null) && ($estado == 1) ) {

	////////// WooCommerce ////////////

	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

	$wc = wocommerce::where('comercio_id', $comercio_id)->first();

		if($wc != null){

            try {
            } catch (HttpClientException $e) {
            $errorResponse = json_decode($e->getResponse()->getBody());
        
            if ($errorResponse && isset($errorResponse->message, $errorResponse->code)) {
                $errorMessage = $errorResponse->message;
                $errorCode = $errorResponse->code;
        
                // Ahora puedes mostrar el mensaje de error de la manera que prefieras
                echo "Error: $errorMessage [$errorCode]";
            } else {
                // Si no se puede decodificar la respuesta JSON, muestra un mensaje genérico
                echo "Se produjo un error al procesar la solicitud.";
            }
        
            // También puedes registrar el error, enviar notificaciones, etc., según tus necesidades
        }

		$woocommerce = new Client(
			$wc->url,
			$wc->ck,
			$wc->cs,

			[
			'version' => 'wc/v3',
			]
		);

        // validador si lo encuentra lo elimina

            // Obtén todos los productos
            $products = $woocommerce->get('products');
        
            // ID del producto que deseas verificar
            $targetProductId = $product->wc_product_id; // Reemplaza con el ID que estás buscando
        
            // Busca el ID del producto en la lista
            $productExists = false;
            foreach ($products as $product) {
                if ($product->id == $targetProductId) { // Utiliza la notación de flecha para acceder a propiedades del objeto
                    $productExists = true;
                    break;
                }
            }
            
            if($productExists) {
            $woocommerce->delete('products/' . $targetProductId);
            }   
            
            
        


		}
		
		}


    
}

public function getStockWC($product_id,$variacion, $sucursal) {
        
    $stock =  productos_stock_sucursales::where('product_id',$product_id)->where('referencia_variacion',$variacion)->where('sucursal_id',$sucursal)->where('eliminado',0)->first();
    //dd($stock);
    return $stock;
        
}
    
    
      
 public function getPrecioWC($product_id,$variacion, $lista_id) {
    
 $precio = productos_lista_precios::where('product_id',$product_id)->where('referencia_variacion',$variacion)->where('lista_id',$lista_id)->where('eliminado',0)->first();
    
 return $precio;
        
 }
      
    
public function SetListaPrecios($product_id,$variacion) {
    
      // DATOS DE LISTAS DE PRECIOS //
      $lista_precios = productos_lista_precios::join('lista_precios','lista_precios.id','productos_lista_precios.lista_id')
      ->where('lista_id','<>',0)
      ->where('product_id', $product_id)
      ->where('referencia_variacion', $variacion)
      ->select('productos_lista_precios.precio_lista', 'lista_precios.wc_key')
      ->get();
       
//        if($lista_precios != null) {
//    
//        $lista_precios->wc_key."_wholesale_price";
//        $lista_precios->wc_key."_have_wholesale_price";
        
//        $list =
//        array(
//        array(
//        "key" => $lista_precios->wc_key."_wholesale_price",
//        "value" => $lista_precios->precio_lista,
//        ),	array(
//        "key" => $lista_precios->wc_key."_have_wholesale_price",
//        "value" => "yes",
//        )
//        );
        
    
//        } else {
//        $list = [];
//        }
        
    //    return $list;
    
    // PROBAR ESTO PARA PASAR TODAS LAS LISTAS DE PRECIOS 
    
    
            if ($lista_precios != null) {
            $result = array();
        
            foreach ($lista_precios as $precio) {
                $item1 = array(
                    "key" => $precio->wc_key . "_wholesale_price",
                    "value" => $precio->precio_lista,
                );
    
                $item2 = array(
                    "key" => $precio->wc_key . "_have_wholesale_price",
                    "value" => "yes",
                );
        
                $result[] = $item1;
                $result[] = $item2;
            }
        
            return $result;
        } else {
            return [];
        }

        
}


// Debo crear un metodo que vea las categorias que hay en wocommerce y que no hay en la app para traerlos
    
public function FindOrCreateCategoryByName($categoria_id) {
    
    if($categoria_id != 1) {
    
    $categoria = Category::find($categoria_id);
    
    $woocommerce = $this->GetClient($categoria->comercio_id);
    //dd($woocommerce);
    
    if($woocommerce != false) {
    try {
       
    
    // Busca la categoria por nombre 
    
    $params = [
        'search' => $categoria->name
        ];
    
    $result = $woocommerce->get('products/categories', $params);
    
    // Si la categoria esta creada en wocommerce y en laravel
    
    if(!empty($result)) {
        $categoria->wc_category_id = $result[0]->id;
        $categoria->save();
    } else {
        // crear la categoria
        $result = $this->CreateCategoriaWC($categoria);
        $categoria->wc_category_id = $result;
        $categoria->save();
        
    }
    
    
    } catch (HttpClientException $e) {
    // Registrar el mensaje del error en los logs
    Log::error('WooCommerce API Error: ' . $e->getMessage());

    // Registrar el contenido completo de la respuesta
    Log::error('WooCommerce API Response: ' . $e->getResponse());

    // También puedes registrar detalles adicionales si es necesario
    Log::error('WooCommerce API Status Code: ' . $e->getResponse()->getStatusCode());
    }
   
    return $categoria->wc_category_id;
    
    }
    
    } else {
    return 1;    
    }
        
}
    
    
// funcion para crear la categoria en wocommerce
public function CreateCategoriaWC($categoria) {

          
        $woocommerce = $this->GetClient($categoria->comercio_id);
    
        // si no existe la categoria               
        $data_c = [ 'name' => $categoria->name ];
                            
        $result = $woocommerce->post('products/categories', $data_c);
                            	
        $categoria->wc_category_id = $result->id;
        $categoria->save();
        
        return $categoria->wc_category_id;
}


// funcion para crear la categoria en wocommerce
public function UpdateCategoriaWC($categoria_id) {

        $categoria = Category::find($categoria_id);
        
        $woocommerce = $this->GetClient($categoria->comercio_id);	
    
        // si no existe la categoria               
        $data_c = [ 'name' => $categoria->name ];
                            
        $result = $woocommerce->put('products/categories/'.$categoria->wc_category_id, $data_c);
                        	
        
    return $categoria->wc_category_id;
}

public function DeleteCategoriaWC($categoria_id) {
    
    $categoria = Category::find($categoria_id);
    
   // dd($categoria);
    
    $woocommerce = $this->GetClient($categoria->comercio_id);
    
	$woocommerce->delete('products/categories/'.$categoria->wc_category_id , ['force' => true]);
}


    public function SetImagen($product,$woocommerce,$validar) {
        // si validar es igual a true tiene que validar que no sea la misma imagen
        $resultado = $this->ValidateImagen($product,$woocommerce);
        //dd($resultado);
        // si $r es verdadero es porque existe una imagen con ese nombre para ese producto
        if($resultado == false) {
        $this->UpdateImagen($product,$woocommerce);    
        }
    }
    
    public function ValidateImagen($product,$woocommerce) {
        
        $wc_product = $woocommerce->get('products/'.$product->wc_product_id);
        $img = $wc_product->images;
        
        if($img != null) {
        $name_img = $img[0]->name;    
        
        if($product->image == $name_img) {
            return true;
        }  else {
            return false;
        }
        } else {
            return false;
        }
        
    }
    
    
    public function UpdateImagen($product,$woocommerce) {

        $url = 'https://app.flamincoapp.com.ar/storage/products/'.$product->image;
                    
        //dd($url);
                    
      	$data = [

          	'images' => [
                 	[
                    	'src' => $url,
                    	'name' => $product->image
                	]
                	]

            	];

        $url_wc_img = 'products/'.$product->wc_product_id;

        $result = $woocommerce->put($url_wc_img , $data);
        
        //dd($result->images[0]->id);
        $image_url = $result->images[0]->src ?? null;
        
        $product->update([
            'wc_image' => $result->images[0]->id,
            'wc_image_url' => $image_url
            ]);
        
    }
    
    
    public function DesincronizarProductosWC($comercio_id){
        
        //dd($comercio_id);
        
        $products = Product::where('comercio_id',$comercio_id)->get();
        
        foreach ($products as $p) {
        
        $product =Product::find($p->id);
        
        $product->update([
            'wc_push' => 0,
            'wc_product_id' => null
            ]);
        }
            
        $variaciones = productos_variaciones_datos::where('comercio_id',$comercio_id)->get();
        
        //dd($variaciones);
        
        foreach ($variaciones as $v) {
        
        $variacion = productos_variaciones_datos::find($v->id);
        
        $variacion->update([
            'wc_product_id' => null,
            'wc_push' => 0,
            'wc_variacion_id' => null,
            ]);
            
        //dd($variacion);
        }
    }
  
  
  // -------------------- CLIENTES (USUARIOS) --------------------//  

public function CreateOrUpdateWooCommerceCustomer($cliente, $wc){
if($cliente->lista_precio == 0) {
    return $this->CreateOrUpdateCustomerWC($cliente, $wc);
} else {
    return $this->UpdateOrCreateClienteWC($cliente, $wc);
}    
}

public function CreateOrUpdateCustomerWC($cliente, $wc) {
    $woocommerce = $this->GetClient($wc->comercio_id);

    if (!$woocommerce) {
        return ['success' => false, 'message' => 'Error al obtener el cliente de WooCommerce'];
    }

    try {
        $tipo_cliente = $cliente->lista_precio == 0 ? "customer" : lista_precios::find($cliente->lista_precio)->nombre;

        $explode = explode('@', $cliente->email);
        $username = $explode[0];

        $customer_data = [
            'username' => $username,
            'first_name' => $cliente->nombre,
            'last_name' => '',
            'email' => $cliente->email,
            'password' => $username // Consider generating a secure password
        ];

        if ($cliente->wc_customer_id != null) {
            // Update existing customer by ID
            $response = $woocommerce->put('customers/' . $cliente->wc_customer_id, $customer_data);
        } else {
            // Search for existing customer by email
            $existing_customer = $woocommerce->get('customers', [
                'email' => $cliente->email
            ]);

            if (!empty($existing_customer)) {
                // Update existing customer by email
                $response = $woocommerce->put('customers/' . $existing_customer[0]->id, $customer_data);
                $cliente->wc_customer_id = $existing_customer[0]->id; // Update local database with WooCommerce customer ID
                $msg_cliente = 'Cliente actualizado en WooCommerce';
            } else {
                // Create new customer
                $response = $woocommerce->post('customers', $customer_data);
                $cliente->wc_customer_id = $response->id; // Update local database with new WooCommerce customer ID
                $msg_cliente = 'Cliente registrado en WooCommerce';
            }
        }

        // Update customer ID in your local database
        $cliente->update([
            'wc_customer_id' => $cliente->wc_customer_id
        ]);

        return [
            'success' => true,
            'message' => $msg_cliente
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}


    public function UpdateOrCreateClienteWC($cliente, $wc) {
    if ($cliente->lista_precio == 0) {
        $tipo_cliente = "customer";
    } else {
        $tipo_cliente = lista_precios::find($cliente->lista_precio)->nombre;
    }

    // Determinar la URL para la creación o actualización del cliente
    $url = $cliente->wc_customer_id != null 
        ? $wc->url . '/wp-json/wp/v2/users/' . $cliente->wc_customer_id
        : $wc->url . '/wp-json/wp/v2/users/';

    $explode = explode('@', $cliente->email);
    $username = $explode[0];

    $data = [
        'username' => $username,
        'first_name' => $cliente->nombre,
        'last_name' => '',
        'email' => $cliente->email,
        'roles' => $tipo_cliente,
        'password' => $username
    ];

    $wc_user = $wc->user;
    $wc_pass = $wc->pass;

    // Crear un nuevo cliente Guzzle
    $client = new ClientG();

    try {
        // Enviar la solicitud POST con Basic Auth
        $response = $client->post($url, [
            'auth' => [$wc_user, $wc_pass], // Autenticación básica
            'json' => $data, // Enviar los datos como JSON
        ]);

        // Verificar la respuesta
        $statusCode = $response->getStatusCode();

        if ($statusCode == 201) {
            // Decodificar el cuerpo de la respuesta
            $responseBody = json_decode($response->getBody(), true);

            // Actualizar el id de cliente en la base de datos
            $cliente->update([
                'wc_customer_id' => $responseBody['id'] // Guardar el ID del usuario creado
            ]);

            return [
                'success' => true,
                'message' => 'Cliente registrado o actualizado en WooCommerce'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al crear o actualizar el usuario en WooCommerce'
            ];
        }
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'Error al crear o actualizar el usuario en WooCommerce, Error: ' . $e->getMessage()
        ];
    }
}


    public function UpdateOrCreateClienteWCOld($cliente,$wc) {
        
        if($cliente->lista_precio == 0) {$tipo_cliente = "customer";} else {$tipo_cliente = lista_precios::find($cliente->lista_precio)->nombre;}
     
        // Ver los roles 
        
        if($cliente->wc_customer_id != null) {
        $host = $wc->url.'/wp-json/wp/v2/users/'.$cliente->wc_customer_id;
        } else {
        $host = $wc->url.'/wp-json/wp/v2/users/';
        }

        $explode = explode('@', $cliente->email);

        $username = $explode[0];

        $data = array(
          'username' => $username,
          'first_name' => $cliente->nombre,
          'last_name' => '',
          'email' => $cliente->email,
          'roles' => $tipo_cliente,
          'password' => $username
        );
        
        
        $data_string = json_encode($data);
        
        //dd($data_string);
        
        $user_pass = $wc->user.':'.$wc->pass;
        
        //dd($user_pass);
        
        $headers = array(
            'Content-Type:application/json',
            'Content-Length: ' . strlen($data_string),
            'Authorization: Basic '. base64_encode($user_pass)
        );

        $ch = curl_init($host);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

        $result = curl_exec($ch);
        
        $result = json_decode($result); 
        //dd($result);
        
        if (property_exists($result, "message")) {
        $response = $result->message;    
        } else {
        
        // Actualiza el id de cliente    
        $cliente->update([
            'wc_customer_id' => $result->id
            ]);
        
        $response = "Sincronizacion exitosa";
        }
        
        //dd($response);
        
        return $response;
       
        curl_close($ch);
     

        
    }

    
    public function GetClientes($wc) {
    
        $host = $wc->url.'/wp-json/wp/v2/users?role__not_in=administrator';
    
        $user_pass = $wc->user.':'.$wc->pass;
    
        $headers = array(
            'Content-Type:application/json',
            'Authorization: Basic '. base64_encode($user_pass)
        );
    
        $ch = curl_init($host);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    
        $result = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($result);
        
        return $result;
    
    }
    
    public function GetClientesFaltantesTienda($wc) {
    
    // Obtengo los clientes de la tienda    
    $clientes_wc = $this->GetClientes($wc);
   
    // Obtengo los clientes de la app
    $clientes_app = ClientesMostrador::where('comercio_id',$wc->comercio_id)->where('email','<>',null)->where('email','<>',"")->get()->toArray();
    
    
    //dd($clientes_app);
    
    $clientes_wc_id = array_column($clientes_wc,'id');
    $clientes_app_id = array_column($clientes_app, 'wc_customer_id');
    
    // Encontrar elementos en la app que no estan en la tienda
    $elementos_faltantes_tienda = array_diff($clientes_app_id,$clientes_wc_id);
    
    $clientes_actualizar = ClientesMostrador::where('comercio_id',$wc->comercio_id)->get();

    $this->SetClientesFaltantesTienda($wc,$clientes_actualizar);
    
    }
    
    public function SetClientesFaltantesTienda($wc,$clientes_actualizar) {
     
     foreach($clientes_actualizar as $ca) {
         $this->UpdateOrCreateClienteWC($wc,$ca);
     }   
    }

    
 

    public function GetRoles($wc) {
    
        $host = $wc->url.'/wp-json/wp/v2/roles';
    
        $user_pass = $wc->user.':'.$wc->pass;
    
        $headers = array(
            'Content-Type:application/json',
            'Authorization: Basic '. base64_encode($user_pass)
        );
    
        $ch = curl_init($host);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    
        $result = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($result);
        
        return $result;
    
    }
    
    public function GetSaleById($sale_id) {

        $sale = Sale::find($sale_id);
        
        $woocommerce = $this->GetClient($sale->comercio_id);
        
        $sale_wc = $woocommerce->get('orders/'.$sale->wc_order_id);
        
        //dd($sale_wc);
        
        $sale_details = SaleDetail::where('sale_id',$sale_id)->first();
        
        if($sale_details == null) {
            
        foreach($sale_wc->line_items as $d){
            //dd($d);
            
            $product = Product::where('wc_product_id',$d->product_id)->where('comercio_id',$sale->comercio_id)->where('eliminado',0)->first();
            
            if($product == null) {
            $product = Product::where('name',$d->parent_name)->where('comercio_id',$sale->comercio_id)->where('eliminado',0)->first();    
            }
            
            if($product->producto_tipo == "v") {
             $pdv =  productos_variaciones_datos::where('product_id',$product->id)->where('wc_variacion_id',$d->variation_id)->first();
             
             if($pdv == null) {
                $variacion = $d->meta_data[0]->value;
                
               // $pdv = productos_variaciones::join('variaciones','variaciones.id','productos_variaciones.variacion_id')->where('producto_id',$product->id)->
                
                // ver cuando tiene mas de una variacion aca como es
                $pdv =  productos_variaciones_datos::where('product_id',$product->id)->where('variaciones',$variacion)->first();
                
                $referencia_variacion = $pdv->referencia_variacion;
                 
             }
            } else {
                $referencia_variacion = 0;
            }
            

            SaleDetail::create([
           'price' => $d->price,
           'quantity' => $d->quantity,
           'product_name' => $d->name,
           'product_barcode' => $d->sku,
           'referencia_variacion' => $referencia_variacion,
           'product_id' => $product->id,
           'metodo_pago'  => $sale->metodo_pago,
           'seccionalmacen_id' => $product->seccionalmacen_id,
           'comercio_id' => $product->comercio_id,
           'sale_id' => $sale_id,
           'iva' => 0,
           'canal_venta' => 'wocommerce',
           'descuento' => 0,
           'recargo' => 0,
           'cliente_id' => $sale->cliente_id
         ]);
        }
        
            
        } else if($sale_details->sum('total') < $sale_wc->total) {
            
        }
        
    }


    public function wooCommerceUpdateStockGlobal($id,$origen) {
        
        //dd($id,$origen);
        
        // Si se modifica por una venta
        if($origen == 1) {
        $items = SaleDetail::where('sale_id',$id)->where('eliminado',0)->get();
        }
        // Si se modifica por una compra
        if($origen == 2) {
        $items = detalle_compra_proveedores::where('compra_id',$id)->where('eliminado',0)
        ->select('detalle_compra_proveedores.comercio_id','detalle_compra_proveedores.producto_id as product_id','detalle_compra_proveedores.referencia_variacion')
        ->get();
        
        }
        
        $wc = wocommerce::where('comercio_id', $this->comercio_id)->first();
        
        if($wc != null){
          $woocommerce = new Client(
            $wc->url,
            $wc->ck,
            $wc->cs,
            ['version' => 'wc/v3',]
          );
          
       //sync stocks
       $dataProductsSimple = ['update' => []];
       $dataProductsVariable = ['update' => []];
      
      //dd($items);
      foreach ($items as $item) {
          
      // Aca hay que hacer una comprobacion que el producto simple este registrado
     
        $product = Product::find($item->product_id);		
        if($product->wc_canal == 1) {

    	$product_stock = $this->getProductStockWocommerce($item->product_id, $item->comercio_id, $item->referencia_variacion);
    	
        // Si es producto simple
        if($product->producto_tipo == "s") {
    	
        $dataProductsSimple['update'][] = [
            'id' => $product->wc_product_id,
            'stock_quantity' => $product_stock->stock
        ];
        
        
    	}
    	
    	 // Si es producto variable
        if($product->producto_tipo == "v") {
    	
        $productos_variaciones_datos = productos_variaciones_datos::where('product_id',$item->product_id)->where('referencia_variacion',$item->referencia_variacion)->where('eliminado',0)->first();
        
        $dataProductsVariable['update'][] = [
            'id' => $productos_variaciones_datos->wc_variacion_id, // ID de la variación
            'stock_quantity' =>  $product_stock->stock,
        ];
        
        $resultSyncVariable = $this->SyncBatchStockVariable( $product->wc_product_id,$woocommerce,$dataProductsVariable); 
         
    	}
    	
        }

      }
      
   // dd($dataProductsVariable);
   // Actualiza productos simples
    $resultSyncSimple = $this->SyncBatchStockSimple($woocommerce,$dataProductsSimple); 
   // $resultSyncVariable = $this->SyncBatchStockVariable($woocommerce,$dataProductsVariable); 
    
    //dd($resultSyncVariable);
    
    //$this->emit("msg-error",$resultSyncSimple);
    }
        
    }
    

    // Actualiza el stock de wocommerce de los productos simples
        public function SyncBatchStockSimple($woocommerce,$dataProducts)
        {
                //dd($dataProducts);
                $result = $woocommerce->post('products/batch', $dataProducts);
                //dd($result);
                if (isset($result->update) && is_array($result->update)) {
                    return true;
                } else {
                    return false;
                }
    
        }
        
        public function SyncBatchStockVariable($wc_product_id,$woocommerce,$dataProducts)
        {
            
            
           // dd($dataProducts);
            
                $result = $woocommerce->post('products/'.$wc_product_id.'/variations/batch', $dataProducts);
                //dd($result);
                if (isset($result->update) && is_array($result->update)) {
                    return true;
                } else {
                    return false;
                }
    
        }    
        
        
          
          
         //GET PRODUCT STOCK
          public function getProductStockWocommerce($product_id = null, $casaCentralId = null, $variacion = 0, $sucursal = null, $product = null){
            if($sucursal === null){
              return productos_stock_sucursales::join('products','products.id','productos_stock_sucursales.product_id')
              ->where('products.id', $product_id)
              ->where('products.comercio_id', $casaCentralId)
              ->where('products.eliminado', 0)
              ->where('productos_stock_sucursales.referencia_variacion', $variacion)
              ->select('productos_stock_sucursales.stock')
              ->first();
              
            }else{
              return  productos_variaciones_datos::where('sucursal_id',$sucursal)
              ->where('product_id',$product->id)
              ->where('referencia_variacion', $variacion)
              ->first();
            }    
          }
          

    public function GetFormasPago($comercio_id) {
        $woocommerce = $this->GetClient($comercio_id);
        $formas_pago = $woocommerce->get('payment_gateways');
        dd($formas_pago);
    }
    
    public function GetOrdenByID($id,$comercio_id) {
    $woocommerce = $this->GetClient($comercio_id);    
    $orden = $woocommerce->get('orders/'. $id);
    dd($orden);
    }

    public function GetProductByName($name,$comercio_id) {
    $woocommerce = $this->GetClient($comercio_id); 
    // Parámetros de búsqueda
    $parametros_busqueda = [
    'search' => $name,
    ];
    // Realiza la solicitud a la API para obtener productos por nombre
    $productos_encontrados = $woocommerce->get('products', $parametros_busqueda);    
    $primer_producto = reset($productos_encontrados);

    if($primer_producto != null){
    // Devuelve información del primer producto
    return $primer_producto->id;    
    } else {
    return 0;    
    }

    }    
    
    public function GetOrdenes($comercio_id) {
        $woocommerce = $this->GetClient($comercio_id);
        $ordenes = $woocommerce->get('orders');
        
        
        /* asi se ven los metodos de envio
        $i =  0;
        foreach($ordenes as $o){
            dd($o->shipping_lines[$i++]->method_id);
        }*/
        
        
        dd($ordenes);
    }
    
    public function GetWocommerceProductsList($comercio_id) {
        
    //    $comercio_id = 621;
        $woocommerce = $this->GetClient($comercio_id);

        try {
        // Obtener la fecha desde la última sincronización
        $wc = wocommerce::where('comercio_id', $comercio_id)->first();
        $fecha_desde = $wc->last_sinc_productos;
    
        // Si la fecha es nula, utilizar 1 de enero del 2000 como fecha base
        $fecha_formateada = $fecha_desde ? Carbon::parse($fecha_desde)->toIso8601String() : Carbon::parse('2000-01-01')->toIso8601String();

        // Iniciar desde la primera página
        $page = 1;
        $perPage = 20; // Especifica la cantidad de productos por página que deseas
    
        do {
            // Configuración de la solicitud
            $params = ['page' => $page, 'per_page' => $perPage, 'after' => $fecha_formateada];
    
            // Realiza la solicitud a la API de WooCommerce
            $products_wc = $woocommerce->get('products', $params);
  
          
            $array = [];
            $productos_sku_repetido = [];
            $productos_nombre_repetido = [];
            $totalProducts = 0;
                
            foreach($products_wc as $p) {
            
            $product = Product::where('name',$p->name)->where('comercio_id',$comercio_id)->where('eliminado',0)->exists();
            $product_sku = Product::where('barcode',$p->sku)->where('comercio_id',$comercio_id)->where('eliminado',0)->exists();
            
            // si no existe el nombre
            if( ($product == false) /*&& ($product_sku == false)*/ ) {
            
            // si el sku no existe
            if($product_sku == false) {
            $this->CrearProductosEnFlaminco($p,$comercio_id);      
            } else {
            // si existe los acumula 
            array_push($productos_sku_repetido,$p);     
            $this->ActualizarProductosEnFlaminco($p,$comercio_id);
            }
            
            } else {
            $this->ActualizarProductosEnFlaminco($p,$comercio_id);
            // si existe el nombre los acumula 
            array_push($productos_nombre_repetido,$p);        
                 
            }
            
            $this->totalProducts =  $totalProducts++;    
            }
    
            // Incrementa el número de página para obtener la siguiente página
            $page++;
        } while (!empty($products_wc));
    
        $wc->last_sinc_productos = Carbon::now();
        $wc->save();
        
         $this->emit('msg-success', 'Sincronizacion exitosa');
        } catch (\Exception $e) {
         $this->emit('msg-success', $e->getMessage()); ;
        }
        

    }
    
    public function GetListaPreciosWc($p) {
    
    if (isset($p->meta_data) && count($p->meta_data) > 0) {
        
        // $metaDataArray es  array
        
        $resultadosEnWholesalePrice = [];
        
        foreach ($p->meta_data as $metaData) {
            //dd($metaData);
            // Verificar si la clave contiene "_wholesale_price" y si el "value" es un número
            if (strpos($metaData->key, '_wholesale_price') !== false && is_numeric($metaData->value)  && !empty($metaData->value) ) {
                // Extraer el valor antes de "_wholesale_price"
                $valorAntesDeWholesalePrice = strstr($metaData->key, '_wholesale_price', true);
        
                $resultado =[
                    'clave' => $valorAntesDeWholesalePrice,
                    'valor' => $metaData->value,
                ];
                $resultadosEnWholesalePrice[] = $resultado;
            }
        }
        
        //dd($resultadosEnWholesalePrice);
        // Mostrar los resultados encontrados
        return $resultadosEnWholesalePrice;

    }
    }

    public function ActualizarProductosEnFlaminco($product_agregar,$comercio_id) {

       $lista_precios = lista_precios::where('comercio_id', $comercio_id)->where('eliminado',0)->get();
       //dd($lista_precios,$comercio_id);
       
       $this->productos_creados = [];    
       $this->comercio_id = $comercio_id;
       
       //dd($product_agregar);
       if(!empty($product_agregar->categories)) {
        // AGREGAR PRODUCTO QUE NO ESTA EN EL SISTEMA Y DETALLE DE PRODUCTOS...
       $nombre_categoria = $product_agregar->categories[0]->name;
       $categoria = Category::where('name',$nombre_categoria)->where('comercio_id',$this->comercio_id)->where('eliminado',0)->first();

        if($categoria == null) {
            $categoria = Category::create([
                'name' => $nombre_categoria,
                'comercio_id' => $this->comercio_id
                ]);
        }
        $categoria_id = $categoria->id;
               
       } else {
       $nombre_categoria = "Sin categoria";   
       $categoria = 1;
       $categoria_id = 1;
       }
       
        if(isset($product_agregar->type)){if($product_agregar->type == "simple") {$producto_tipo = "s";} else {$producto_tipo = "v";}} else {$producto_tipo = "s";}
        
        if($product_agregar->manage_stock == true){$stock_descubierto = "si";} else {$stock_descubierto = "no";}
        
        $image_url = $product_agregar->images[0]->src ?? null;
        
        $product = Product::where('wc_product_id',$product_agregar->id)->where('eliminado',0)->first();
        
        if($product != null){
            $product->update([
              'name' => $product_agregar->name,
              'price' => 0,
              'barcode' => empty($product_agregar->sku) ? uniqid() : $product_agregar->sku,
              'stock' => 0,
              'alerts' => 1,
              'tipo_producto' => 1,
              'producto_tipo' => $producto_tipo,
              'stock_descubierto' => $stock_descubierto,
              'seccionalmacen_id' => 1,
              'marca_id' => 1,
              'category_id' => $categoria_id?? 1,
              'comercio_id' => $this->comercio_id,
              'mostrador_canal' => false,
              'ecommerce_canal' => false,
              'wc_canal' => true,
              'wc_product_id' => $product_agregar->id,
              'wc_image_url' => $image_url
            ]);    
        }
        
        
        if($product != null){
            
        if($producto_tipo == "s"){
        
        // Elementos pasados $product_agregar,$product,$referencia_variacion,$wc_variacion
            
        // Crear el registro en productos_stock_sucursales
        $this->SetListasApp($product_agregar,$product,0,0);
        
        // Crear el registro en productos_stock_sucursales
        $this->StoreStockApp($product_agregar,$product,0,0);

		
        }
    
        
        if($producto_tipo == "v"){
        
        $woocommerce = $this->GetClient($this->comercio_id);
        
        $variaciones = ($product_agregar->variations);
        foreach($variaciones as $v) {
            
            $wc_variacion = $this->GetVariacionWC($woocommerce,$product_agregar->id,$v);
            
            //dd($wc_variacion);
            
            $referencia_variacion = $this->SetVariacionesApp($product_agregar,$wc_variacion,$product);  
            
            // Elementos pasados $product_agregar,$product,$referencia_variacion,$wc_variacion
            
            
            // Crear el registro en las listas
            $this->SetListasApp($product_agregar,$product,$referencia_variacion,$wc_variacion);
            
            // Crear el registro en productos_stock_sucursales
            $this->StoreStockApp($product_agregar,$product,$referencia_variacion,$wc_variacion);

        }
    
		
        }            
        }

      

        array_push($this->productos_creados,$product);
        
        //dd($this->productos_creados);

        $this->proceso_importacion = $this->proceso_importacion + 1; 
    
        return $product;
    }
    
    public function CrearProductosEnFlaminco($product_agregar,$comercio_id) {

       $lista_precios = lista_precios::where('comercio_id', $comercio_id)->where('eliminado',0)->get();
       //dd($lista_precios,$comercio_id);
       
       $this->productos_creados = [];    
       $this->comercio_id = $comercio_id;
       
       //dd($product_agregar);
       if(!empty($product_agregar->categories)) {
        // AGREGAR PRODUCTO QUE NO ESTA EN EL SISTEMA Y DETALLE DE PRODUCTOS...
       $nombre_categoria = $product_agregar->categories[0]->name;
       $categoria = Category::where('name',$nombre_categoria)->where('comercio_id',$this->comercio_id)->where('eliminado',0)->first();

        if($categoria == null) {
            $categoria = Category::create([
                'name' => $nombre_categoria,
                'comercio_id' => $this->comercio_id
                ]);
        }
        $categoria_id = $categoria->id;
               
       } else {
       $nombre_categoria = "Sin categoria";   
       $categoria = 1;
       $categoria_id = 1;
       }
       
        if(isset($product_agregar->type)){if($product_agregar->type == "simple") {$producto_tipo = "s";} else {$producto_tipo = "v";}} else {$producto_tipo = "s";}
        
        if($product_agregar->manage_stock == true){$stock_descubierto = "si";} else {$stock_descubierto = "no";}
        
        $image_url = $product_agregar->images[0]->src ?? null;
        
        $product = Product::create([
          'name' => $product_agregar->name,
          'price' => 0,
          'barcode' => empty($product_agregar->sku) ? uniqid() : $product_agregar->sku,
          'stock' => 0,
          'alerts' => 1,
          'tipo_producto' => 1,
          'producto_tipo' => $producto_tipo,
          'stock_descubierto' => $stock_descubierto,
          'seccionalmacen_id' => 1,
          'marca_id' => 1,
          'category_id' => $categoria_id?? 1,
          'comercio_id' => $this->comercio_id,
          'mostrador_canal' => false,
          'ecommerce_canal' => false,
          'wc_canal' => true,
          'wc_product_id' => $product_agregar->id,
          'wc_image_url' => $image_url
        ]);
        
        //dd($product_agregar->type);
        //dd($producto_tipo);
        
        if($producto_tipo == "s"){
        
        // Elementos pasados $product_agregar,$product,$referencia_variacion,$wc_variacion
            
        // Crear el registro en productos_stock_sucursales
        $this->SetListasApp($product_agregar,$product,0,0);
        
        // Crear el registro en productos_stock_sucursales
        $this->StoreStockApp($product_agregar,$product,0,0);

		
        }
    
        
        if($producto_tipo == "v"){
        
        $woocommerce = $this->GetClient($this->comercio_id);
        
        $variaciones = ($product_agregar->variations);
        foreach($variaciones as $v) {
            
            $wc_variacion = $this->GetVariacionWC($woocommerce,$product_agregar->id,$v);
            
            //dd($wc_variacion);
            
            $referencia_variacion = $this->SetVariacionesApp($product_agregar,$wc_variacion,$product);  
            
            // Elementos pasados $product_agregar,$product,$referencia_variacion,$wc_variacion
            
            
            // Crear el registro en las listas
            $this->SetListasApp($product_agregar,$product,$referencia_variacion,$wc_variacion);
            
            // Crear el registro en productos_stock_sucursales
            $this->StoreStockApp($product_agregar,$product,$referencia_variacion,$wc_variacion);

        }
    
		
        }
      

        array_push($this->productos_creados,$product);
        
        //dd($this->productos_creados);

        $this->proceso_importacion = $this->proceso_importacion + 1; 
    
        return $product;
    }
    
    public function GetVariacionWC($woocommerce,$product_id,$variacion){
        //dd($woocommerce,$product_id,$variacion);
        $variacion = $woocommerce->get('products/'.$product_id.'/variations/'.$variacion);
        //dd($variacion);
        return $variacion;
    }
    
    
    public function SetVariacionesApp($product_agregar,$datos_variaciones,$product) {
       
       $cod_variacion = [];
       $variaciones_id = [];
       
       //dd($datos_variaciones->attributes);
       $referencia_variacion = Carbon::now()->format('dmYHis').'-'.$this->comercio_id;
       
       foreach($datos_variaciones->attributes as $attribute){
           
           $nombre_atributo = $attribute->name;
           $nombre_variacion = $attribute->option;
           
           $variacion_app = variaciones::where('nombre',$nombre_variacion)->where('comercio_id',$this->comercio_id)->where('eliminado',0)->first();
           // primero verifico que exista el atributo
           $atributo_app = atributos::where('nombre',$nombre_atributo)->where('comercio_id',$this->comercio_id)->where('eliminado',0)->first();

           if($variacion_app != null){
           array_push($variaciones_id,$variacion_app->id);    
           } else {
           // si no se encuentra hay que crearlo
           
           // primero verifico que exista el atributo
           $atributo_app = atributos::where('nombre',$nombre_atributo)->where('comercio_id',$this->comercio_id)->where('eliminado',0)->first();
           // si el atributo es nulo lo crea
           if($atributo_app == null){
           	$atributo_app = atributos::create([
			'nombre' => $nombre_atributo,
			'comercio_id' => $this->comercio_id
		    ]);
            } 
            
            // y luego crea la variacion
			$variacion_app = variaciones::create([
        		'nombre' => $nombre_variacion,
        		'atributo_id' => $atributo_app->id,
        		'comercio_id' => $this->comercio_id
        	]);
        	
        	
        	array_push($variaciones_id,$variacion_app->id);  
           }
           array_push($cod_variacion,$attribute->option);
           
        	productos_variaciones::create([
			'atributo_id' =>  $atributo_app->id,
			'variacion_id' => $variacion_app->id,
			'comercio_id' => $this->comercio_id,
			'referencia_id' => $referencia_variacion
			]);
			
       }
       
       $cod_variacion = implode(" - ",$cod_variacion);
       $variaciones_id = implode(" - ",$variaciones_id);
       
       
       
       $pvd = productos_variaciones_datos::create([
          'wc_product_id' => $product_agregar->id ,
          'wc_variacion_id' => $datos_variaciones->id,
          'cost' => 0,
          'product_id' => $product->id,
          'referencia_variacion' => $referencia_variacion,
          'codigo_variacion' => $cod_variacion,
          'variaciones' => $cod_variacion,
          'comercio_id' => $this->comercio_id,
          'eliminado' => 0,
          'variaciones_id' => $variaciones_id,
          'imagen' => null,
          'precio_interno' => 0,
          'wc_push' => 1
        ]);
        
        return $referencia_variacion;
        
    }
    
        public function SetListasApp($product_agregar, $product, $referencia_variacion, $wc_variacion) {
        // Lista de precios base
        $lista_precios = lista_precios::where('comercio_id', $product->comercio_id)->where('eliminado',0)->get();
        
        $precio_lista = $referencia_variacion == 0 ? $product_agregar->price : $wc_variacion->price;
        
        // Guardar el precio base en la lista de precios principal (id 0)
        $this->StorePrecioApp($product_agregar, $product, $referencia_variacion, 0, $precio_lista);
        
        // Obtener las otras listas
        $GetLista = $referencia_variacion == 0 ? $product_agregar : $wc_variacion;
        $lista_precios_wc = $this->GetListaPreciosWc($GetLista);
        
        // Crear un array indexado por clave para facilitar las comparaciones
        $lista_precios_wc_array = [];
        if ($lista_precios_wc != null) {
            foreach ($lista_precios_wc as $lp_wc) {
                $lista_precios_wc_array[$lp_wc['clave']] = $lp_wc['valor'];
            }
        }
        
        // Crear un array indexado por nombre de lista
        $lista_precios_array = [];
        foreach ($lista_precios as $lp) {
            $lista_precios_array[$lp->nombre] = $lp;
        }
    
        // Comparar listas de precios
        foreach ($lista_precios_wc_array as $clave => $valor) {
            if (isset($lista_precios_array[$clave])) {
                // Lista de precios existe en ambos, actualizar el precio
                $this->StorePrecioApp($product_agregar, $product, $referencia_variacion, $lista_precios_array[$clave]->id, $valor);
            } else {
                // Lista de precios existe en $lista_precios_wc pero no en $lista_precios, crear la lista y luego guardar el precio
                $lista = lista_precios::create([
                    'nombre' => $clave,
                    'wc_key' => $clave,
                    'comercio_id' => $this->comercio_id
                ]);
                $this->StorePrecioApp($product_agregar, $product, $referencia_variacion, $lista->id, $valor);
            }
        }
    
        // Verificar listas de precios que están en $lista_precios pero no en $lista_precios_wc
        foreach ($lista_precios as $lp) {
            if (!isset($lista_precios_wc_array[$lp->nombre])) {
                // Guardar el precio en StorePrecioApp para las listas que no están en $lista_precios_wc
                $this->StorePrecioApp($product_agregar, $product, $referencia_variacion, $lp->id, 0); // O algún valor predeterminado
            }
        }
    }


    public function SetListasAppOld($product_agregar,$product,$referencia_variacion,$wc_variacion){
        
       
        // Lista de precios base
        if($referencia_variacion == 0){$precio_lista = $product_agregar->price;} else {$precio_lista = $wc_variacion->price;}
       
       // variables pasadas $product_agregar,$product,$referencia_variacion,$lista_id,$precio
       
        $this->StorePrecioApp($product_agregar,$product,$referencia_variacion,0,$precio_lista);
        
        // Obtiene las otras listas
        if($referencia_variacion == 0){$GetLista = $product_agregar;} else {$GetLista = $wc_variacion;}
        $lista_precios_wc = $this->GetListaPreciosWc($GetLista);
        // Setea las otras listas 
        if($lista_precios_wc != null){
            
        foreach($lista_precios as $lp){
        
        //dd($lp->clave);
        
        $lista = lista_precios::where('nombre',$lp['clave'])->where('comercio_id',$this->comercio_id)->where('eliminado',0)->first();    
        if($lista != null){
        
        
        $this->StorePrecioApp($product_agregar,$product,$referencia_variacion,$lista->id,$lp['valor']);
        } else {
        
        if(!empty($lp['valor'])) {
        $lista = lista_precios::create([
            'nombre' => $lp['clave'],
            'wc_key' => $lp['clave'],
            'comercio_id' => $this->comercio_id
            ]);
        
        $this->StorePrecioApp($product_agregar,$product,$referencia_variacion,$lista->id,$lp['valor']);
        
        }
        }
        }    
        }
        
        // Seatea las listas de precios que no estan en wocommerce
    
    }
    
    public function StorePrecioApp($product_agregar,$product,$referencia_variacion,$lista_id,$precio) {

        if(empty($precio)){
            $precio = 0;
        }
        
		$precio = productos_lista_precios::UpdateOrCreate(
		    [
    		   'lista_id' => $lista_id,
    		   'comercio_id' => $this->comercio_id,
    		   'referencia_variacion' => $referencia_variacion,
    		   'product_id' => $product->id, 
		    ],
		    [
    	    	'precio_lista' => $precio,
    			'lista_id' => $lista_id,
    			'comercio_id' => $this->comercio_id,
    			'referencia_variacion' => $referencia_variacion,
    		    'product_id' => $product->id
			]);
        
    //    dd($precio);
    
    }
    
    // variables pasadas $product_agregar,$product,$referencia_variacion,$wc_variacion
    
    public function StoreStockApp($product_agregar,$product,$referencia_variacion,$wc_variacion) {
        
    if($referencia_variacion == 0){$stock = $product_agregar->stock_quantity;} else {$stock = $wc_variacion->stock_quantity;}
    if(empty($stock)){$stock = 0;}
    
        $stock = productos_stock_sucursales::UpdateOrCreate(
        [
		'sucursal_id' => 0,
		'comercio_id' => $this->comercio_id,
		'referencia_variacion' => $referencia_variacion,
		'product_id' => $product->id,        
        ],
        [
		'almacen_id' => 1,
		'stock' => $stock,
		'stock_real' => $stock,
		'sucursal_id' => 0,
		'comercio_id' => $this->comercio_id,
		'referencia_variacion' => $referencia_variacion,
		'product_id' => $product->id,
		]);
		
		
		
		$sucursales = sucursales::select('sucursales.sucursal_id')
		->where('casa_central_id', $this->comercio_id)
		->where('sucursales.eliminado',0)
		->get();
		
		if(0 < $sucursales->count()){
		    foreach($sucursales as $suc){
		           $stock = productos_stock_sucursales::UpdateOrCreate(
		           [
            		'sucursal_id' => $suc->sucursal_id,
            		'comercio_id' => $this->comercio_id,
            		'referencia_variacion' => $referencia_variacion,
            		'product_id' => $product->id,		           
		           ],
		           [
            		'almacen_id' => 1,
            		'stock' => 0,
            		'stock_real' => 0,
            		'sucursal_id' => $suc->sucursal_id,
            		'comercio_id' => $this->comercio_id,
            		'referencia_variacion' => $referencia_variacion,
            		'product_id' => $product->id,
            		]);     
		    }
		}
		
    }
    
    
    public function CartelIguales($product) {
        
        if( ($product->name == $p->name) || ($product->barcode == $p->sku) ) {

        $this->producto_tipo = $product->producto_tipo;
        
        if($this->producto_tipo == "s") {
        $product_price = productos_lista_precios::where('product_id',$product->id)->where('lista_id',0)->first();
        $product_stock = productos_stock_sucursales::where('product_id',$product->id)->where('sucursal_id',0)->first();
                
        // datos en flaminco 
        $this->id_flaminco = $product->id;
        $this->name_flaminco = $product->name;
        $this->sku_flaminco = $product->barcode;
        $this->price_flaminco = $product_price->precio_lista;
        $this->stock_flaminco = $product_stock->stock;
        
        // datos en wocommerce 
        //dd($p);
        $this->id_wc = $p->id;
        $this->name_wc = $p->name;
        $this->sku_wc = $p->sku;
        $this->price_wc = $p->price;
        $this->stock_wc = $p->stock_quantity;
        
        if($product->name == $p->name){$this->tipo_coincidencia = "nombre";}
        if($product->barcode == $p->sku){$this->tipo_coincidencia = "sku";}
        }
        
        if($product->producto_tipo == "v") { 
        $this->pvd = productos_variaciones_datos::where('product_id',$product->id)->where('eliminado',0)->get();
        $this->product_price = productos_lista_precios::where('product_id',$product->id)->where('lista_id',0)->get();  
        
        if($product->name == $p->name){$this->tipo_coincidencia = "nombre";}
        if($product->barcode == $p->sku){$this->tipo_coincidencia = "sku";}
        }
        
            
        } 

    }
        
    
    public function SetWocommerceClientList($comercio_id) {
    
    $wc = wocommerce::where('comercio_id', $this->comercio_id)->first();
    
    $clientes_wc = $this->GetClientes($wc);
    
    dd($clientes_wc);
    foreach($clientes_wc as $cw) {
        
        
    }
    
    }
    
        public function UpdateProductStockIndividualWocommerce($product_id,$referencia_variacion,$comercio_id) {
           
            $product = Product::find($product_id);
            $woocommerce = $this->GetClient($product->comercio_id);
           
           if($product->wc_canal == 1) {
            $stock = $this->getProductStockWocommerce($product_id, $comercio_id, $referencia_variacion);
            
            if($referencia_variacion == 0) {
                
            $dataProductsSimple = ['update' => []];
            
            $dataProductsSimple['update'][] = [
            'id' => $product->wc_product_id,
            'stock_quantity' => $stock->stock
            ];
            
            //dd($dataProductsSimple);
            
            $result = $this->SyncBatchStockSimple($woocommerce,$dataProductsSimple);
            
            
            } else {
            $dataProductsVariable = ['update' => []];
            
            $productos_variaciones_datos = productos_variaciones_datos::where('product_id',$product_id)->where('referencia_variacion',$referencia_variacion)->where('eliminado',0)->first();
        
            $dataProductsVariable['update'][] = [
            'id' => $productos_variaciones_datos->wc_variacion_id, // ID de la variación
            'stock_quantity' =>  $stock->stock,
            ];
        
            $result = $this->SyncBatchStockVariable( $product->wc_product_id,$woocommerce,$dataProductsVariable);     
            
            }
            
           
               
            //dd($result);
           }
        
          }

public function CambiarProductoDetalleWC($productId, $referencia_variacion, $venta_id, $cant, $comercio_id)
{
    $product = Product::find($productId);
    $orderId = Sale::find($venta_id)->wc_order_id;
    
    $woocommerce = $this->GetClient($product->comercio_id);
    if ($woocommerce != false) {

        // Obtener el pedido
        $order = $this->getOrderWC($orderId, $woocommerce);
        
        if ($order) {
            if($product->wc_product_id != null){
            
            // Buscar la línea del pedido que contiene el producto
            foreach ($order->line_items as $item) {
                if ($item->product_id == $product->wc_product_id) {
                    // Actualizar el detalle de la venta
                    $item->quantity = $cant;

                    // Actualizar el subtotal y el total si es necesario
                    $item->subtotal = number_format($item->price * $item->quantity, 2, '.', '');
                    $item->total = number_format($item->price * $item->quantity, 2, '.', '');
                }
            }

            // Convertir line_items de objeto a array
            $lineItemsArray = array_map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                    'total' => $item->total,
                    'price' => $item->price,
                    'variation_id' => $item->variation_id,
                ];
            }, $order->line_items);

            // Actualizar el pedido
            $updatedOrder = $this->updateOrderWC($orderId, $woocommerce, [
                'line_items' => $lineItemsArray,
            ]);
            
            $result = $this->UpdateProductStockIndividualWocommerce($productId, $referencia_variacion, $comercio_id);            
            
            }
            
        }
    }
}

public function SincronizarVentaMostradorEnWC($venta_id)
{
    $venta = Sale::find($venta_id);
    $woocommerce = $this->GetClient($venta->comercio_id);

    if ($woocommerce !== false) {
        $detalle_venta = SaleDetail::where('sale_id', $venta->id)->get();
        $order = $woocommerce->get('orders/' . $venta->wc_order_id);
        
        $items = $detalle_venta->map(function ($detail) use ($woocommerce,$order) {
            $product = Product::find($detail->product_id);

            if ($product->wc_product_id == null) {
                $response = $woocommerce->get('products', [
                    'sku' => $product->barcode
                ]);

                if (empty($response)) {
                    // Si el producto no está en WooCommerce, crearlo
                    if ($product->producto_tipo == "s") {
                        $this->WocommerceStoreSimple($product->id);
                    } else {
                        $this->WocommerceStoreVariable($product->id);
                    }
                }

                // Obtener el producto actualizado con el ID de WooCommerce
                $product = Product::find($detail->product_id);
            }

            $productos_variaciones_datos = productos_variaciones_datos::where('product_id', $detail->product_id)
                ->where('referencia_variacion', $detail->referencia_variacion)
                ->first();

            $array = [
                'product_id' => $product->wc_product_id,
                'quantity' => $detail->quantity,
                'price' => $detail->price,
                'name' => $detail->product_name,
            ];

             if (!empty($order->line_items)) {
                  foreach ($order->line_items as $item) {
                    if ($item->product_id == $product->wc_product_id) {
                    $array['id'] = $item->id;
                    }
                  }
             }
             
            if ($productos_variaciones_datos != null) {
                $array['variation_id'] = $productos_variaciones_datos->wc_variacion_id;
            }

            return $array;
        })->toArray();

        $fees = $detalle_venta->map(function ($detail) {
            if ($detail->recargo > 0) {
                return [
                    'name' => 'Recargo',
                    'total' => $detail->recargo,
                ];
            }
            return null;
        })->filter()->values()->toArray();

        $cliente = ClientesMostrador::find($venta->cliente_id);
        $metodo_pago = metodo_pago::find($venta->metodo_pago);

        $payment_method = $metodo_pago->id == 1 ? 'cod' : 'bacs';
        $payment_method_title = $metodo_pago->id == 1 ? 'Efectivo' : bancos::find($metodo_pago->cuenta)->nombre;

        $address_1 = trim($cliente->direccion . ' ' . $cliente->altura) ?: 'N/A';
        $city = $cliente->localidad ?: 'N/A';
        $state = $cliente->provincia ?: 'N/A';
        $postcode = $cliente->codigo_postal ?: 'N/A';
        $country = 'AR';
        $email = $cliente->email ?: 'noemail@example.com';
        $phone = $cliente->telefono ?: '0000000000';

        $billing = [
            'first_name' => $cliente->nombre,
            'last_name' => 'Nombre',
            'address_1' => $address_1,
            'city' => $city,
            'state' => $state,
            'postcode' => $postcode,
            'country' => $country,
            'email' => $email,
            'phone' => $phone,
        ];

        $shipping = [
            'first_name' => $cliente->nombre,
            'last_name' => 'Nombre',
            'address_1' => $address_1,
            'city' => $city,
            'state' => $state,
            'postcode' => $postcode,
            'country' => $country,
        ];

        $set_paid = $venta->deuda <= 0;

        $total_descuento = $venta->descuento + ($venta->descuento_promo * $venta->cantidad_promo);
        if ($total_descuento > 0) {
            $coupon_data = [
                'code' => uniqid() . '_Descuento_' . $venta->id,
                'amount' => strval($total_descuento),
                'discount_type' => 'fixed_cart',
                'individual_use' => true,
                'exclude_sale_items' => false,
                'apply_before_tax' => true,
                'free_shipping' => false,
            ];

            $coupon = $woocommerce->post('coupons', $coupon_data);
        } else {
            $coupon_data = null;
        }

        $order_data = [
            'payment_method' => $payment_method,
            'payment_method_title' => $payment_method_title,
            'set_paid' => $set_paid,
            'line_items' => $items,
            'fee_lines' => $fees,
            'meta_data' => [
                [
                    'key' => 'nro_venta',
                    'value' => $venta->nro_venta
                ]
            ]
        ];

        if ($venta->cliente_id !== 1) {
            $order_data['billing'] = $billing;
            $order_data['shipping'] = $shipping;
        }

        if ($coupon_data !== null) {
            $order_data['coupon_lines'] = [
                [
                    'code' => $coupon->code
                ]
            ];
        }

        try {
            if ($venta->wc_order_id == null) {
                // Crear nueva orden
                $order = $woocommerce->post('orders', $order_data);
                $venta->wc_order_id = $order->id;
                $venta->nota_interna = 'WooCommerce ID ' . $order->id;
                $msg = 'Venta agregada en WooCommerce';
            } else {

                // Actualizar orden con nuevos datos
                $order = $woocommerce->put('orders/' . $venta->wc_order_id, $order_data);
                $msg = 'Venta actualizada en WooCommerce';
            }

            $venta->save();
            return ['success' => true, 'message' => $msg];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

 //   return ['success' => false, 'message' => 'Cliente de WooCommerce no configurado'];
}


public function AgregarVentaMostradorEnWCOld($venta_id)
{
    $venta = Sale::find($venta_id);
    if ($venta->wc_order_id == null) {
        $woocommerce = $this->GetClient($venta->comercio_id);

        if ($woocommerce !== false) {
            $detalle_venta = SaleDetail::where('sale_id', $venta->id)->get();

            $items = $detalle_venta->map(function ($detail) use ($woocommerce) {
                $product = Product::find($detail->product_id);
                
                if($product->wc_product_id == null){
                $response = $woocommerce->get('products', [
                    'sku' => $product->barcode
                ]);
                if($response == []){
                    // aca si no esta creado el producto tiene que crearlo
                    if($product->producto_tipo == "s"){
                        $this->WocommerceStoreSimple($product->id);
                    } else {
                        $this->WocommerceStoreVariable($product->id);    
                    }                         
                }

                }

                $product = Product::find($detail->product_id);
                $productos_variaciones_datos = productos_variaciones_datos::where('product_id', $detail->product_id)
                    ->where('referencia_variacion', $detail->referencia_variacion)
                    ->first();
                    
                $array = [
                    'product_id' => $product->wc_product_id,
                    'quantity' => $detail->quantity,
                    'price' => $detail->price,
                    'name' => $detail->product_name,
                ];

                if ($productos_variaciones_datos != null) {
                    $array['variation_id'] = $productos_variaciones_datos->wc_variacion_id;
                }

                return $array;
            })->toArray();

            $fees = $detalle_venta->map(function ($detail) {
                if ($detail->recargo > 0) {
                    return [
                        'name' => 'Recargo',  // Nombre del recargo
                        'total' => $detail->recargo,  // Monto del recargo
                    ];
                }
                return null;
            })->filter()->values()->toArray();

            $cliente = ClientesMostrador::find($venta->cliente_id);

            $metodo_pago = metodo_pago::find($venta->metodo_pago);
            if ($metodo_pago->id == 1) {
                $payment_method = 'cod';
                $payment_method_title = 'Efectivo';
            } else {
                $banco = bancos::find($metodo_pago->cuenta);
                $payment_method = 'bacs';
                $payment_method_title = $banco->nombre;
            }

            // Valores por defecto para los campos que pueden estar vacíos
            $address_1 = trim($cliente->direccion . ' ' . $cliente->altura) ?: 'N/A';
            $city = $cliente->localidad ?: 'N/A';
            $state = $cliente->provincia ?: 'N/A';
            $postcode = $cliente->codigo_postal ?: 'N/A';
            $country = 'AR'; // Asignar un país predeterminado si está vacío
            $email = $cliente->email ?: 'noemail@example.com';
            $phone = $cliente->telefono ?: '0000000000';

            $billing = [
                'first_name' => $cliente->nombre,
                'last_name' => 'Nombre',
                'address_1' => $address_1,
                'city' => $city,
                'state' => $state,
                'postcode' => $postcode,
                'country' => $country,
                'email' => $email,
                'phone' => $phone,
            ];
            $shipping = [
                'first_name' => $cliente->nombre,
                'last_name' => 'Nombre',
                'address_1' => $address_1,
                'city' => $city,
                'state' => $state,
                'postcode' => $postcode,
                'country' => $country,
            ];

            if ($venta->deuda <= 0) {
                $set_paid = true;
            } else {
                $set_paid = false;
            }

            $total_descuento = $venta->descuento + ($venta->descuento_promo * $venta->cantidad_promo);
            if ($total_descuento > 0) {
            
            $coupon_data = [
                'code' => 'Descuento '.$venta->id, // Nombre del cupón
                'amount' => strval($total_descuento),  // Monto del descuento
                'discount_type' => 'fixed_cart', // Tipo de descuento (porcentaje o monto fijo)
                'individual_use' => true,
                'exclude_sale_items' => false,
                'apply_before_tax' => true,
                'free_shipping' => false,
            ];
            
            $coupon = $woocommerce->post('coupons', $coupon_data);

            // Ahora puedes aplicar el cupón creado a la orden

            } else {
                $coupon_data = 0;
            }
                        
            
        $order_data = [
                'payment_method' => $payment_method,
                'payment_method_title' => $payment_method_title,
                'set_paid' => $set_paid,
                'line_items' => $items,
                'fee_lines' => $fees,  // Añadir recargos
                'meta_data' => [
                    [
                        'key' => 'nro_venta',
                        'value' => $venta->nro_venta
                    ]
                ]
            ];

            // Condicionalmente agregar billing si existe algún dato relevante
            if ($venta->cliente_id !== 1) {
                $order_data['billing'] = $billing;
            }

            // Condicionalmente agregar shipping si existe algún dato relevante
            if ($venta->cliente_id !== 1) {
                $order_data['shipping'] = $shipping;
            }

            // Condicionalmente agregar shipping si existe algún dato relevante
            if ($coupon_data !== 0) {
            $order_data['coupon_lines'] = [
                [
                    'code' => $coupon->code
                ]
            ];  
            }
          
            
            try {
                $order = $woocommerce->post('orders', $order_data);

                $venta->wc_order_id = $order->id;
                $venta->nota_interna = 'WooCommerce ID ' . $order->id;
                $venta->save();
                $msg = 'Venta agregada en WooCommerce';
                return $msg;
            } catch (\Exception $e) {
                $msg = ['error' => $e->getMessage()];
                return $msg;
            }
        }

        return 'Cliente de WooCommerce no configurado';
    } else {
        return 'La venta ya está guardada en WooCommerce';
    }
}





public function AgregarProductoDetalleWC($productId, $referencia_variacion, $venta_id, $cant, $comercio_id,$precio)
{
    $product = Product::find($productId);
    $orderId = Sale::find($venta_id)->wc_order_id;
    
    $woocommerce = $this->GetClient($product->comercio_id);
    if ($woocommerce != false) {

        // Obtener el pedido
        $order = $this->getOrderWC($orderId, $woocommerce);
        
        if ($order) {
            $productFound = false;
            
            if($product->wc_product_id != null){
            
                // Buscar la línea del pedido que contiene el producto
                foreach ($order->line_items as $item) {
                    if ($item->product_id == $product->wc_product_id) {
                        // Actualizar el detalle de la venta
                        $item->quantity = $cant;
                        // Actualizar el subtotal y el total si es necesario
                        $item->subtotal = number_format($item->price * $item->quantity, 2, '.', '');
                        $item->total = number_format($item->price * $item->quantity, 2, '.', '');

                        $productFound = true;
                        break;
                    }
                }

                if (!$productFound) {
                    // Agregar un nuevo ítem al pedido
                    $newItem = (object) [
                        'id' => 0, // Proporcionar un valor predeterminado si es necesario
                        'product_id' => $product->wc_product_id,
                        'quantity' => $cant,
                        'price' => $precio,
                        'subtotal' => number_format($precio * $cant, 2, '.', ''),
                        'total' => number_format($precio * $cant, 2, '.', ''),
                        'variation_id' => $referencia_variacion, // Agregar la variación si es aplicable
                    ];

                    $order->line_items[] = $newItem;
                }

                // Convertir line_items de objeto a array
                $lineItemsArray = array_map(function ($item) {
                    return [
                        'id' => $item->id ?? 0, // Proporcionar un valor predeterminado si es necesario
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->subtotal,
                        'total' => $item->total,
                        'price' => $item->price,
                        'variation_id' => $item->variation_id ?? 0, // Proporcionar un valor predeterminado si es necesario
                    ];
                }, $order->line_items);

                // Depuración: Imprimir el array de line items
                //dd($lineItemsArray);

                // Actualizar el pedido
                $updatedOrder = $this->updateOrderWC($orderId, $woocommerce, [
                    'line_items' => $lineItemsArray,
                ]);
                
            //    dd($updatedOrder);
                
                $result = $this->UpdateProductStockIndividualWocommerce($productId, $referencia_variacion, $comercio_id);   // Ver esto
                
            }
        }
    }
}

private function RegistrarReembolsoWCOld($item)
{
    $venta = Sale::find($item->sale_id);
    $product = Product::find($item->product_id);
    
    $orderId = $venta->wc_order_id;
    
    $woocommerce = $this->GetClient($product->comercio_id);
    if ($woocommerce != false) {

        // Obtener detalles de la orden
        try {
            $order = $woocommerce->get("orders/{$orderId}");
        } catch (Exception $e) {
            \Log::error("Error al obtener los detalles de la orden {$orderId}: " . $e->getMessage());
            return; // Salir si no se pueden obtener los detalles de la orden
        }

        // Filtrar los artículos para eliminar el producto específico
        $filtered_items = [];
        foreach ($order->line_items as $line_item) {
            if ($line_item->product_id != $product->wc_product_id) {
                $filtered_items[] = [
                    'id' => $line_item->id,
                    'product_id' => $line_item->product_id,
                    'quantity' => $line_item->quantity,
                    'subtotal' => $line_item->subtotal,
                    'total' => $line_item->total,
                ];
            }
        }

        // Actualizar el pedido con los artículos filtrados
        $data = [
            'line_items' => $filtered_items
        ];
        
        //dd($data);
        
        try {
           $response = $woocommerce->put("orders/{$orderId}", $data);
           dd($response);
        } catch (Exception $e) {
            // Manejar el error
            \Log::error("Error al actualizar el pedido en WooCommerce: " . $e->getMessage());
        }        
    }
}

private function RegistrarReembolsoWC($item)
{
    $venta = Sale::find($item->sale_id);
    $product = Product::find($item->product_id);
    
    $orderId = $venta->wc_order_id;
    
    $woocommerce = $this->GetClient($product->comercio_id);
    if ($woocommerce != false) {
        
        $quantity = $item->quantity;
        $amount = number_format($item->precio_original * $quantity, 2, '.', '');
        
        $order = $woocommerce->get("orders/{$orderId}");
        $payment_method = $order->payment_method;
        //dd($payment_method);
        
        $reembolso = [
            'amount' => (string) $amount,
            'reason' => 'Producto eliminado del pedido',
            'refund_payment_method' => "other", // Añadir esta línea para especificar el método de reembolso
            'line_items' => [
                [
                    'product_id' => $product->wc_product_id,
                    'quantity' => (int) $item->quantity,
                    'subtotal' => (string) $amount,
                    'total' => (string) $amount,
                ],
            ],
        ];

        //dd($reembolso);
        
        try {
            $woocommerce->post("orders/{$orderId}/refunds", $reembolso);
        } catch (Exception $e) {
            // Manejar el error
            \Log::error("Error al registrar el reembolso en WooCommerce: " . $e->getMessage());
        }        
    }
}

    public function updateOrderWC($orderId,$woocommerce, $data)
    {
       // dd($data);
        $response = $woocommerce->put("orders/{$orderId}", $data);
    //    dd($response);
        return $response;
    }
    
    public function getOrderWC($orderId,$woocommerce)
    {
        return $woocommerce->get("orders/{$orderId}");
    }

    public function GetClient($comercio_id) {
    
    $wc = wocommerce::where('comercio_id', $comercio_id)->first();
    
    if($wc != null){
    
            try {
        //    $this->checkCredentialsWCPlus($wc->url, $wc->ck, $wc->cs);
            
            $woocommerce = new Client(
            	$wc->url,
            	$wc->ck,
            	$wc->cs,
            	[
            	'wp_api' => true,
            	'version' => 'wc/v3',
                'query_string_auth' => true
            	]
            	);
    
            return $woocommerce;
    
            } catch (\Exception $e) {
                // Manejo de excepciones: Puedes loguear o retornar un mensaje de error
                \Log::error('Error al instanciar el cliente de WooCommerce: ' . $e->getMessage());
                return false; // Retornar false o algún mensaje de error según prefieras
            }
             
    } else {
        return false;
    }
    
    
    }
    
    public function checkCredentialsWCPlus($url, $ck, $cs)
    {
        $client = new ClientG(); // Asegúrate de que ClientG está correctamente definido
    
        try {
            // Hacer la solicitud a la API de productos de WooCommerce
            $response = $client->get($url . '/wp-json/wc/v3/products', [
                'auth' => [$ck, $cs]
            ]);
    
            // Si las credenciales son correctas, la solicitud debe devolver una respuesta exitosa
            if ($response->getStatusCode() === 200) {
              //  Log::info('Cuerpo de la respuesta:', ['body' => $response->getBody()->getContents()]);
                return true; // Credenciales válidas
            } else {
                // Para depuración, puedes registrar el código de estado y el cuerpo de la respuesta
            //    Log::info('Código de estado de la respuesta:', ['status' => $response->getStatusCode()]);
            //    Log::info('Cuerpo de la respuesta:', ['body' => $response->getBody()->getContents()]);
                return false; // Credenciales inválidas
            }
        } catch (\Exception $e) {
            // Registrar el mensaje de error en caso de excepción
            Log::error('Error al comprobar las credenciales:', ['message' => $e->getMessage()]);
            return false; // Error en la solicitud
        }
    }

 public function FindProductWC($barcode,$comercio_id){
    
    $woocommerce = $this->GetClient($comercio_id);	
    try {
    $sku = 'tu_sku_a_buscar'; // Reemplaza con el SKU que deseas buscar

    // Realiza la búsqueda
    $productos = $woocommerce->get('products', ['sku' => $barcode]);
    
    // Verifica si se encontraron productos
    if (!empty($productos)) {
            // Devuelve el ID del primer producto encontrado
            return $productos[0]->id; 
    } else {
            // Devuelve false si no se encontró el producto
            return false; 
    }
        
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}

 }   
}
