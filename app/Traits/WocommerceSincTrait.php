<?php
namespace App\Traits;


// Modelos

use App\Models\Product;
use App\Models\wocommerce;
use App\Models\datos_facturacion;
use App\Models\sucursales;
use App\Models\lista_precios;
use App\Models\productos_variaciones;
use App\Models\productos_variaciones_datos;
use App\Models\User;
use App\Models\atributos;
use App\Models\Category;
use App\Models\variaciones;
use App\Models\productos_stock_sucursales;
use App\Models\ClientesMostrador;
use App\Models\metodo_pago;
use App\Models\productos_lista_precios;

// Otros

use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use Carbon\Carbon;

trait WocommerceSincTrait {

public function SincWC() {

    

  ////////// WooCommerce ////////////

  $wooco = wocommerce::get();

  foreach($wooco as $wc) {

  if($wc != null){

  $woocommerce = new Client(
    $wc->url,
    $wc->ck,
    $wc->cs,
      [
          'wp_api' => true,
          'version' => 'wc/v3',
      ]
  );

  /* ------------------- ORDENES ---------------- */


  $this->wc_order = $woocommerce->get('orders');

  $ultima_venta = Sale::where('comercio_id', $wc->comercio_id)->orderBy('wc_order_id','desc')->first();
  
  if($ultima_venta != null) { $fecha_ultima_venta = $ultima_venta->created_at; } else { $fecha_ultima_venta = Carbon::now(); }
  
  
  

  foreach($this->wc_order as $wc_order) {

    if($fecha_ultima_venta < $wc_order->date_created) {

    /* ------------------- CLIENTE ---------------- */

    $wc_customer = $woocommerce->get('customers/'.$wc_order->customer_id);

    if($wc_customer) {

      $this->cliente = ClientesMostrador::where('email', $wc_customer->email)
      ->where('comercio_id', 'like', $wc->comercio_id)
      ->where('wc_customer_id', $wc_customer->id)
      ->select('*')->first();



      if($this->cliente == null) {

        $this->agregar_cliente = ClientesMostrador::create([
          'nombre' => $wc_customer->first_name ." ".$wc_customer->last_name,
          'telefono' => $wc_customer->billing->phone,
          'email' => $wc_customer->email,
          'wc_customer_id' => $wc_customer->id,
          'direccion' => $wc_customer->billing->address_1,
          'localidad' => $wc_customer->billing->city,
          'status' => 'Active',
          'comercio_id' => $wc->comercio_id
          ]);

          $this->cliente_agregar = $this->agregar_cliente->id;


        } else {
          $this->cliente_agregar = $this->cliente->id;
        }

    }

    /* ----------------------------------------------- */

    $this->orden = Sale::where('wc_order_id', $wc_order->id)
    ->where('comercio_id', 'like', $wc->comercio_id)
    ->select('*')->first();

    $caja = cajas::where('estado',0)->where('comercio_id',$wc->comercio_id)->max('id');

    $this->caja = $caja;

    if($this->orden == null) {


  if($wc_order->status == "processing") {
    $this->status = "En proceso";
  }
  if($wc_order->status == "completed") {
    $this->status = "Entregado";
  }
  if($wc_order->status == "cancelled") {
    $this->status = "Cancelado";
  }
  if($wc_order->status == "on-hold") {
    $this->status = "Pendiente";
  }


/* ------------------- METODO DE PAGO ---------------- */


  if($wc_order->payment_method == "cod") {
    $this->metodo_pago_wc = "1";
    $this->deuda = $wc_order->total;
  }

  if($wc_order->payment_method == "bacs") {
    $this->metodo_pago_wc = "1"; //TRANSFERENCIA/
    $this->deuda = "";
  }

  $this->wc_total = $wc_order->total;

  /* ------------ INSERTAR VENTA ----------------- */

    $this->order = Sale::create([
      'subtotal' =>   $this->wc_total,
      'total' =>   $this->wc_total,
      'recargo' => 0,
      'descuento' => 0,
      'items' => 1,
      'tipo_comprobante'  => 'CF',
      'cash' => 0,
      'metodo_pago'  => $this->metodo_pago_wc,
      'comercio_id' => $wc->comercio_id,
      'cliente_id' => $this->cliente_agregar,
      'user_id' => $wc->comercio_id,
      'canal_venta' => 'WooCommerce',
      'caja' => $this->caja,
      'status' => $this->status,
      'wc_order_id' => $wc_order->id,
      'deuda' => $this->deuda,
      'nota_interna' => 'WooCommerce ID '.$wc_order->id
    ]);


    foreach ($wc_order->line_items as $item) {

    $product_wc =  Product::where('wc_product_id', $item->product_id)->where('comercio_id', $wc->comercio_id)->where('eliminado', 0)->first();

    if($product_wc != null) {



       if($item->variation_id != null) {
          $productos_variaciones_datos = productos_variaciones_datos::where('wc_variacion_id',$item->variation_id)->first();

          $productos_variaciones_datos = $productos_variaciones_datos->referencia_variacion;
       } else {
           $productos_variaciones_datos = 0;
       }


      // DETALLE DE PRODUCTOS...

             $detalle_venta =  SaleDetail::create([
                  'price' => $item->price,
                  'recargo' => 0,
                  'descuento' => 0,
                  'quantity' => $item->quantity,
                  'metodo_pago'  => $this->metodo_pago_wc,
                  'product_id' => $product_wc->id,
                  'product_name' => $item->name,
                  'product_barcode' => $product_wc->barcode,
                  'iva' => 0,
                  'seccionalmacen_id' => $product_wc->seccionalmacen_id,
                  'comercio_id' => $product_wc->comercio_id,
                  'sale_id' => $this->order->id,
                  'caja' => $this->caja,
                  'canal_venta' => 'WooCommerce',
                  'cliente_id' => $this->cliente_agregar,
                  'referencia_variacion' => $productos_variaciones_datos
                ]);


                //update stock
                $product = productos_stock_sucursales::where('referencia_variacion', $productos_variaciones_datos)->where('product_id',$product_wc->id)->first();
                $product->stock = $product->stock - $item->quantity;
                $product->save();

                  historico_stock::create([
                  'tipo_movimiento' => 1,
                  'referencia_variacion' => $productos_variaciones_datos,
                  'producto_id' => $product_wc->id,
                  'sale_id' => $this->order->id,
                  'cantidad_movimiento' => -$item->quantity,
                  'stock' => $product->stock,
                  'usuario_id' =>  $product_wc->comercio_id,
                  'comercio_id'  => $product_wc->comercio_id,
                ]);



    } else {

    $product_agregar = $woocommerce->get('products/'.$item->product_id);


    // AGREGAR PRODUCTO QUE NO ESTA EN EL SISTEMA Y DETALLE DE PRODUCTOS...

    $product = Product::create([
      'name' => $product_agregar->name,
      'price' => $product_agregar->price,
      'barcode' => $product_agregar->sku,
      'stock' => $product_agregar->stock_quantity,
      'alerts' => 1,
      'tipo_producto' => 1,
      'stock_descubierto' => "si",
      'seccionalmacen_id' => 1,
      'category_id' => 1,
      'comercio_id' => $wc->comercio_id,
      'mostrador_canal' => false,
      'ecommerce_canal' => false,
      'wc_canal' => true,
      'wc_product_id' => $product_agregar->id,
      'descripcion' => $product_agregar->description
    ]);

      try {

            DB::beginTransaction();

                  DB::table('sale_details')->insert([
                  'price' => $item->price,
                  'recargo' => $item->price*$item->quantity,
                  'descuento' => $item->price*$item->quantity,
                  'quantity' => $item->quantity,
                  'metodo_pago'  => $this->metodo_pago_wc,
                  'product_id' => $product->id,
                  'product_name' => $product->name,
                  'iva' => 0,
                  'product_barcode' => $product->barcode,
                  'seccionalmacen_id' => $product->seccionalmacen_id,
                  'comercio_id' => $product->comercio_id,
                  'sale_id' => $this->order->id,
                  'caja' => $this->caja,
                  'canal_venta' => 'WooCommerce',
                  'cliente_id' => $this->cliente_agregar
                ]);


                //update stock
                $product = productos_stock_sucursales::where('referencia_variacion', $productos_variaciones_datos)->where('product_id',$product_wc->id)->first();
                $product->stock = $product->stock - $item->quantity;
                $product->save();

                  historico_stock::create([
                  'tipo_movimiento' => 1,
                  'referencia_variacion' => $productos_variaciones_datos,
                  'producto_id' => $product_wc->id,
                  'sale_id' => $this->order->id,
                  'cantidad_movimiento' => -$item->quantity,
                  'stock' => $product->stock,
                  'usuario_id' =>  $product_wc->comercio_id,
                  'comercio_id'  => $product_wc->comercio_id,
                ]);



                      DB::commit();

    } catch (Exception $e) {
        DB::rollback();

      }
    }



  }



/* --------------------------------------------------------------------- */

      } else {

           if ($wc_order->date_modified){

  //    $wc_order = $woocommerce->get('orders/'.$this->orden->wc_order_id);

   //   $modificacion_orden = $wc_order->date_modified_gmt;
////////////////////////////////////////////////////////////////////////////


  }

  }

  }


  }


  ////////////////////// ACTUALIZAR STOCK DE PRODUCTOS //////////////////////////////

        $ultima_importacion = importaciones::where('comercio_id', $wc->comercio_id)->orderBy('id','desc')->first();
        
        $user = User::find($wc->comercio_id);
        
        if($ultima_importacion < $user->last_sinc) {
        
        $lista_productos = Product::where('comercio_id', $wc->comercio_id)
        ->where('wc_canal', 1)
        ->where('eliminado', 0)
        ->get();

        foreach($lista_productos as $lp) {

        $product = Product::find($lp->id);


        $product_id = $lp->id;


        if($product->wc_canal == 1 && $product->wc_canal != null) {

    		///    CATEGORIA WC/////

			$categoria = Category::find($product->category_id);

			if($categoria != null) {
			$categoria_wc = $categoria->wc_category_id;
			if($categoria_wc != null) {
			  $categoria_wc = $categoria_wc;
			} else {

			$woocommerce = new Client(
				$wc->url,
				$wc->ck,
				$wc->cs,

					[
							'version' => 'wc/v3',
					]
			);

			////// CREAR LA CATEGORIA //////
			    $data_c = [
			    'name' => $categoria->name,
			    'image' => [
			        'src' => ''
			    ]
			];


			$this->wc_category = $woocommerce->post('products/categories', $data_c);

	    	$categoria->update([
			'wc_category_id' => $this->wc_category->id
		    ]);

		    $categoria_wc = $this->wc_category->id;

			/////////////////////////////
			}

			}

			////////////////////////////////



          ////////////////////////////// PRODUCTOS SIMPLES /////////////////////////////////////////


          if($product->producto_tipo == "s") {

          // STOCK ORIGEN//
          $this->stock_origen = productos_stock_sucursales::where('product_id', $product_id )->where('referencia_variacion',0)->where('sucursal_id',0)->first();

          // STOCK ORIGEN//
          $this->precio_origen = productos_lista_precios::where('product_id', $product_id )->where('referencia_variacion',0)->where('lista_id',0)->first();



          ///////// DEFINIR SI TRABAJA O NO CON STOCK  /////////

          if($product->stock_descubierto == "si") {

          $this->manage_stock = 'no';
          $this->stock_quantity = $this->stock_origen->stock;

          } else {

          $this->manage_stock = 'yes';
          $this->stock_quantity  = null;

          }

          $this->lista_precios = lista_precios::where('comercio_id', $product->comercio_id)->get();

          $list = [];

          foreach ($this->lista_precios as $wc_lista_precios) {

          $productos_lista = productos_lista_precios::where('product_id', $product_id)
          ->where('lista_id', $wc_lista_precios->id )
          ->where('lista_id','<>',0)
          ->first();

          if($productos_lista != null) {

          $wc_lista_precios->wc_key."_wholesale_price";
          $wc_lista_precios->wc_key."_have_wholesale_price";
          $this->precio_lista = $productos_lista->precio_lista;


          $list =
          array(
          array(
          "key" => $wc_lista_precios->wc_key."_wholesale_price",
          "value" => $this->precio_lista,
          ),	array(
          "key" => $wc_lista_precios->wc_key."_have_wholesale_price",
          "value" => "yes",
          )
          );

        }

          }

          $data = [
          'name' => $product->name,
          'type' => 'simple',
          'sku' => $product->barcode,
          'status' => 'publish',
          'manage_stock' => true,
          'backorders' => $this->manage_stock,
          'stock_quantity' => $this->stock_origen->stock,
          'stock_status' => "instock",
          'regular_price' => $this->precio_origen->precio_lista,
          'categories' => [
          [
          'id' => $categoria_wc,
          ]
          ],

          'meta_data' => $list
          ];

    		/////////  CHEQUEA SI LOS PRODUCTOS ESTAN EN WOCOMMERCE O NO  /////////

			     /////////  SI EL PRODUCTO ESTA REGISTRADO EN WOCOMMERCEK  /////////

          if($product->wc_product_id != null) {

          $this->wocommerce_product_id = 'products/'.$product->wc_product_id;


          $woocommerce->put($this->wocommerce_product_id , $data);

          } else {

          /////////  SI EL PRODUCTO NO ESTA REGISTRADO EN WOCOMMERCEK  /////////
          $this->wc_product_id = $woocommerce->post('products', $data);

          $product->update([
          'wc_product_id' => $this->wc_product_id->id
          ]);


          }


/////////////////////////////////////////////////////////////////////////////////////////////////////////////
          }


          /////////////////////// PRODUCTOS VARIABLES ///////////////////////////////


          if($product->producto_tipo == "v") {

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
              'visible' => true,
              'variation' => true,
              'options' => $v,

              );

              array_push($atribut,$atributos);

          }


            $data_p = [
              'name' => $product->name,
              'sku' => $product->barcode,
              'type' => 'variable',
              'categories' => [
          [
          'id' => $categoria_wc,
          ]
          ],
              'attributes' => $atribut
          ];

          /////////  CHEQUEA SI LOS PRODUCTOS ESTAN EN WOCOMMERCE O NO  /////////

             /////////  SI EL PRODUCTO ESTA REGISTRADO EN WOCOMMERCEK  /////////

            if($product->wc_product_id != null) {


            $data_p = $woocommerce->put('products/'.$product->wc_product_id, $data_p);

            } else {

            /////////  SI EL PRODUCTO NO ESTA REGISTRADO EN WOCOMMERCEK  /////////
            $data_p = $woocommerce->post('products', $data_p);

            $product->update([
            'wc_product_id' => $data_p->id
            ]);


            }

            /////////////////////////////////////////////////////////////////////////////



          $a = [];
          $lista_precios_array = [];
          $datex = [];

          $datos_origen =	productos_variaciones::where('productos_variaciones.producto_id', $product_id)
          ->select('productos_variaciones.referencia_id')
          ->groupBy('productos_variaciones.referencia_id')
          ->get();

          $i = 0;

          foreach ($datos_origen as $d) {

          // LISTA DE PRECIO BASE //
          $this->precio_origen = productos_lista_precios::where('referencia_variacion', $d->referencia_id )->where('lista_id',0)->first();

          // STOCK ORIGEN//
          $this->stock_origen = productos_stock_sucursales::where('referencia_variacion', $d->referencia_id )->where('sucursal_id',0)->first();

          // DATOS DE VARIACIONES DE WOCOMMERCE//

          $datos_variacion_wocommerce = productos_variaciones_datos::where('referencia_variacion', $d->referencia_id)->first();
          // DATOS DE VARIACIONES //

          $datos =	productos_variaciones::join('variaciones','variaciones.id','productos_variaciones.variacion_id')
          ->join('atributos','atributos.id','productos_variaciones.atributo_id')
          ->select('atributos.nombre as name', 'variaciones.nombre as option')
          ->where('referencia_id', $d->referencia_id )->get();

          $datos = $datos->toArray();
          array_push($a,$datos);


          // DATOS DE LISTAS DE PRECIOS //
          $lista_precios = productos_lista_precios::join('lista_precios','lista_precios.id','productos_lista_precios.lista_id')
          ->where('lista_id','<>',0)
          ->where('referencia_variacion', $d->referencia_id )
          ->select('productos_lista_precios.precio_lista', 'lista_precios.wc_key')
          ->first();

          if($lista_precios != null) {

            $lista_precios->wc_key."_wholesale_price";
            $lista_precios->wc_key."_have_wholesale_price";

            $list =
            array(
            array(
              "key" => $lista_precios->wc_key."_wholesale_price",
              "value" => $lista_precios->precio_lista,
            ),	array(
              "key" => $lista_precios->wc_key."_have_wholesale_price",
              "value" => "yes",
            )
            );

          } else {
              $list = [];
          }

          ///////// DEFINIR SI TRABAJA O NO CON STOCK  /////////

          if($product->stock_descubierto == "si") {

          $this->manage_stock = 'no';
          $this->stock_quantity = $this->stock_origen->stock;

          } else {

          $this->manage_stock = 'yes';
          $this->stock_quantity  = null;

          }

          ////////////////////////////////////////////////////////////////


          $data_v = [
              'regular_price' => $this->precio_origen->precio_lista,
              'backorders' => $this->manage_stock,
              'stock_quantity' => $this->stock_origen->stock,
              'stock_status' => "instock",
              'attributes' => $a[$i++],
              'meta_data' =>  $list
          ];


              $d_variacion = $woocommerce->post('products/'.$product->wc_product_id.'/variations/'.$datos_variacion_wocommerce->wc_variacion_id, $data_v);

              $productos_variaciones_datos = productos_variaciones_datos::where('referencia_variacion',$d->referencia_id)->first();

                $productos_variaciones_datos->update([
                	'wc_product_id' => $data_p->id,
                	'wc_variacion_id' => $d_variacion->id
                ]);




          }

          }

        
                  // ingregar la imagen del produto 
    

                	if($product->imagen != null) {
	    
	                
                	$data = [

                	'images' => [
                    	[
                    	'src' => 'https://express.flamincoapp.com.ar/storage/products/'.$product->imagen
                	]
                	]

                	];

                	$this->wocommerce_product_id = 'products/'.$product->wc_product_id;

                	$woocommerce->put($this->wocommerce_product_id , $data);

    

                	}
                	
                	// fin de imagenes
	

        }
        
          }
          

        
         // aca ponemos cuando fue la ultima sincronizacion de stock
      
        $user->last_sinc = Carbon::now();
        $user->save();

        }
        
      
      //
    }


    }


  ////////////////////////////       ACA TERMINA EL SINCRONIZADOR DE STOCKS     /////////////////////////////////////////////////////

  
    
}



}
