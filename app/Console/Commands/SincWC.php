<?php

namespace App\Console\Commands;

use App\Imports\ProductsImport;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;


// Trait

use App\Traits\WocommerceTrait;

use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use App\Models\User;
use App\Models\cajas;
use App\Models\SaleDetail;
use App\Models\wocommerce;
use App\Models\atributos;
use App\Models\importaciones;
use App\Models\variaciones;
use App\Models\historico_stock;
use App\Models\productos_variaciones_datos;
use App\Models\sucursales;
use App\Models\lista_precios;
use App\Models\productos_stock_sucursales;
use App\Models\productos_lista_precios;
use App\Models\ClientesMostrador;
use App\Models\productos_variaciones;
use App\Models\provincia;
//use App\Models\ecommerce_envio;
use Automattic\WooCommerce\Client;
use Carbon\Carbon;

class SincWC extends Command
{

    use WocommerceTrait;
    
    protected $signature = 'sinc:wc';

    protected $description = 'Laravel Excel importer';

    protected $saleId = null;

    public function handle()
    {


                
                
        ////////// WooCommerce ////////////

        $wooco = wocommerce::orderBy('id','desc')->get();

        if(0 < $wooco->count()){
        foreach($wooco as $wc) {

            var_dump($wc->url,$wc->ck,$wc->cs);
            
            $response = $this->checkCredentials($wc->url,$wc->ck,$wc->cs);  
              
          
            if($wc != null && isset($wc) && $response == true){

              try {
                              
                $woocommerce = $this->GetClient($wc->comercio_id);

                $this->SyncProductosNuevosWC($woocommerce,$wc,$wc->comercio_id);

                $this->wc_order = $this->GetArrayTotalOrders($wc->comercio_id);

                if (!empty($this->wc_order)) {

                  //$i = 1;
                  foreach($this->wc_order as $wc_order) {

                  if (isset($wc_order)) {
                      
                    // obtenemos el cliente          
                    $this->GetCliente($woocommerce, $wc, $wc_order);    

                    // manipulamos la venta 
                    //Testeando
                    dump($wc_order->id);
                    $this->SetSale($woocommerce, $wc, $wc_order);                  

                   }
                    
                  }  
                }


                //Testeando
                $this->ActualizarProductos($woocommerce, $wc);
                

                // aca ponemos cuando fue la ultima sincronizacion de stock      
                $user = User::find($wc->comercio_id);
                if($user != null) {
                $user->last_sinc = Carbon::now();
                $user->save(); 
                }
                
                
              } catch (Throwable $t) {
              var_dump("Error: ". $t);
              }
            }
        }
        }
        
                

        $lockFile = storage_path('logs/reporte_handle.lock');
        $lockFileLifetime = 900;
        // Intentar obtener el bloqueo
        if ($this->acquireLock($lockFile, $lockFileLifetime)) {
            try {
                
                
                // Aca va todo el codigo 
                
            } finally {
                    // Liberar el bloqueo
                    $this->releaseLock($lockFile);
                }
        } else {
            echo 'tarea en ejecucion';
        }
        


      ////// ACA TERMINA EL SINCRONIZADOR DE STOCKS  /////////
    }

    public function GetCliente($woocommerce, $wc, $wc_order) {

        /* ------------------- CLIENTE ---------------- */

       // return dd($wc_order->billing->first_name);

        if($wc_order->customer_id !== 0){
          $wc_customer = $woocommerce->get('customers/'. $wc_order->customer_id);

          if($wc_customer != null) {
            
            //$wc_email = $wc_customer->email;
            
            $this->cliente = ClientesMostrador::where('email', $wc_customer->email)
            ->where('comercio_id', 'like', $wc->comercio_id)
            ->where('wc_customer_id', $wc_customer->id)
            ->select('*')->first();
  
            if($this->cliente == null) {
  
            $ultimo_id = ClientesMostrador::where('comercio_id',$wc->comercio_id)->max('id_cliente');
        
            if($ultimo_id != null){
            $this->id_cliente = $ultimo_id + 1;
            } else {
            $this->id_cliente = 1;    
            }
        
        
              $this->agregar_cliente = ClientesMostrador::create([
                'id_cliente' => $this->id_cliente,  
                'nombre' => $wc_customer->first_name ." ".$wc_customer->last_name,
                'telefono' => $wc_customer->billing->phone,
                'email' => $wc_customer->email,
                'wc_customer_id' => $wc_customer->id,
                'direccion' => $wc_customer->billing->address_1,
                'localidad' => $wc_customer->billing->city,
                'status' => 'Active',
                'comercio_id' => $wc->comercio_id,
                'creador_id' => $wc->comercio_id
                ]);
  
                $this->cliente_agregar = $this->agregar_cliente->id;
  
  
              } else {
                $this->cliente_agregar = $this->cliente->id;
              }
   


          }
        }else{
          $this->cliente = ClientesMostrador::where('email', $wc_order->billing->email)
          ->where('comercio_id', 'like', $wc->comercio_id)
          ->select('*')->first();

          if($this->cliente == null) {  
              
              
            $ultimo_id = ClientesMostrador::where('comercio_id',$wc->comercio_id)->max('id_cliente');
        
            if($ultimo_id != null){
            $this->id_cliente = $ultimo_id + 1;
            } else {
            $this->id_cliente = 1;    
            }
            
            $this->agregar_cliente = ClientesMostrador::create([
              'id_cliente' => $this->id_cliente,  
              'nombre' => $wc_order->billing->first_name ." ".$wc_order->billing->last_name,
              'telefono' => $wc_order->billing->phone,
              'email' => $wc_order->billing->email,
              'wc_customer_id' => null,
              'direccion' => $wc_order->billing->address_1,
              'localidad' => $wc_order->billing->city,
              'status' => 'Active',
              'comercio_id' => $wc->comercio_id,
              'creador_id' => $wc->comercio_id
              ]);

              $this->cliente_agregar = $this->agregar_cliente->id;


            } else {
              $this->cliente_agregar = $this->cliente->id;
            }
 
        }        
    }
     
    // variables: $wocommerce (respuesta de autenticacion en wc) , $wc (datos de la tabla wocommerce de la app), $wc_order (venta en la tienda)
    
    public function SetSaleStatus($wc_order) {
            
              $status = "Pendiente";
              
              if($wc_order->status == "processing") {
                $status = "En proceso";
              }
              if($wc_order->status == "completed") {
                $status = "Entregado";
              }
              if($wc_order->status == "cancelled") {
                $status = "Cancelado";
              }
              if($wc_order->status == "on-hold" || $wc_order->status == "pending") {
                $status = "Pendiente";
              }      
              
              return $status;
    }   
    
    public function SetSale($woocommerce, $wc, $wc_order) {  

        //dump($wc_order);
              
        $this->orden = Sale::where('wc_order_id', $wc_order->id)
          ->where('comercio_id', 'like', $wc->comercio_id)
          ->where('eliminado',0)
          ->select('*')->first();
        
        $comercio_id = $wc->comercio_id;
        
        $caja = cajas::where('estado',0)->where('comercio_id',$wc->comercio_id)->max('id');
            
        $this->caja = $caja;            
         
          if($this->orden == null) {            
            
            $this->status = $this->SetSaleStatus($wc_order);
 
             // Calcular descuentos
            $descuento = $wc_order->discount_total;
            
            // Calcular recargos (si se aplican como una l¨ªnea de producto o de alguna otra manera)
            $recargo = 0;
            foreach ($wc_order->fee_lines as $fee) {
                $recargo += $fee->total;
            }


              /* ------------------- METODO DE PAGO ---------------- */            
              //var_dump($wc_order->payment_method);
              $this->metodo_pago_wc = "1";
              $this->deuda = $wc_order->total; // Subtotal antes de aplicar recargos y descuentos;
                
              if($wc_order->payment_method == "") {
                $this->metodo_pago_wc = "1";
                $this->deuda = $wc_order->total; // Subtotal antes de aplicar recargos y descuentos;
              }
              
              if($wc_order->payment_method == "cod") {
                $this->metodo_pago_wc = "1";
                $this->deuda = $wc_order->total; // Subtotal antes de aplicar recargos y descuentos;
              }
            
              if($wc_order->payment_method == "bacs") {
                $this->metodo_pago_wc = "1"; //TRANSFERENCIA/
                $this->deuda = 0;
              }
            
              $this->wc_total = $wc_order->total; // Subtotal antes de aplicar recargos y descuentos;
            
              /* ------------ INSERTAR VENTA ----------------- */
            
            $nro_venta = $this->SetNroVenta($wc->comercio_id);
            $subtotal = $this->wc_total - $recargo + $descuento;
            $alicuota_recargo = $recargo / ($subtotal - $descuento);
            
            $this->order = Sale::create([
                  'nro_venta' => $nro_venta,
                  'subtotal' =>  $subtotal,
                  'total' =>   $this->wc_total,
                  'recargo' => $recargo,
                  'descuento' => $descuento,
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
                  'nota_interna' => 'WooCommerce ID '.$wc_order->id,
                  'created_at' => Carbon::now()
                ]);
            
              
              // variables: $wocommerce (respuesta de autenticacion en wc) , $wc (datos de la tabla wocommerce de la app), $wc_order (venta en la tienda), id de la venta en la app
              $this->SetEnvio($woocommerce, $wc, $wc_order, $this->order->id);                
              
              $items = $wc_order->line_items;
              
              foreach ($items as $item) {
                
                if($item->product_id == 0) {
                $product_id = $this->GetProductByName($item->name,$wc->comercio_id);    
                } else {
                $product_id = $item->product_id;    
                }
                
                // si el product_id es igual a 0 el producto no existe en wc, fue eliminado por lo tal hay que crearlo como eliminado con los datos que traemos de $item;
               // var_dump($product_id);
                
                if($product_id != 0) {
                $this->SetProductosDetailExisteEnWC($woocommerce,$item,$product_id,$wc,$this->status,$alicuota_recargo);
                } else {
                $this->SetProductosDetailNoExisteEnWC($woocommerce,$item,$product_id,$wc,$alicuota_recargo);    
                }
                

              }
            
            
          
            /* --------------------------------------------------------------------- */
            
          } else {
            
              if ($wc_order->date_modified){
              
                //    $wc_order = $woocommerce->get('orders/'.$this->orden->wc_order_id);            
                //   $modificacion_orden = $wc_order->date_modified_gmt;      
              }            
          }    

        
    } 
    
    public function SetProductosDetailNoExisteEnWC($woocommerce,$item,$product_id,$wc,$alicuota_recargo) {
    
    $product_wc = $this->CrearProductosEnFlaminco($item,$wc->comercio_id);
            
    $productos_variaciones_datos = $this->SetVariacionesDatos($item);
    $this->SetSaleDetail($item,$product_wc,$productos_variaciones_datos,$alicuota_recargo);
                    
    }
                    
    public function SetProductosDetailExisteEnWC($woocommerce,$item,$product_id,$wc,$status,$alicuota_recargo){
                
                // si el product_id es distinto a 0 existe en wc
                $product_wc =  Product::where('wc_product_id', $product_id)->where('comercio_id', $wc->comercio_id)->where('eliminado', 0)->first();
  
                //$this->CorroborarProductoPorNombre();
                if($product_wc != null) { 
                    
                   $productos_variaciones_datos = $this->SetVariacionesDatos($item);
                   $this->SetSaleDetail($item,$product_wc,$productos_variaciones_datos,$alicuota_recargo);
                    
                   //update stock

                   $this->UpdateStockAppSync($productos_variaciones_datos,$product_wc,$item,$status);       
              
                  } else {   

                   $item_wc = $woocommerce->get('products/'.$product_id);
                    
                   if($item_wc != null) {
                    $product_wc = $this->CrearProductosEnFlaminco($item_wc,$wc->comercio_id);
                    
                    $productos_variaciones_datos = $this->SetVariacionesDatos($item);
                    $this->SetSaleDetail($item,$product_wc,$productos_variaciones_datos,$alicuota_recargo);
                    }
                    
                }        
    }
    
    public function SetEnvio($woocommerce, $wc, $wc_order, $saleId = null) {

     
      try {
        
          DB::beginTransaction();
          if($saleId !== null){
          
          if($wc_order->customer_id !== 0 ){
            $wc_customer = $woocommerce->get('customers/'. $wc_order->customer_id);

              if($wc_customer) {      
                                         
                DB::table('ecommerce_envios')->insert([
                  'sale_id' => $saleId,
                  'comercio_id' => $wc->comercio_id,
                  'nombre_destinatario' => $wc_customer->first_name ." ".$wc_customer->last_name,
                  'telefono' => $wc_customer->billing->phone,
                  'dni'  => null,
                  'direccion' => $wc_customer->billing->address_1,
                  'depto' => null,
                  'ciudad' => $wc_customer->billing->city,
                  'provincia' => null,
                  'pais' => $wc_customer->billing->country,
                  'codigo_postal' => $wc_customer->billing->postcode,
                  'metodo_entrega' => 2,
                ]);
              }
          }else {
              DB::table('ecommerce_envios')->insert([
                'sale_id' => $saleId,
                'comercio_id' => $wc->comercio_id,
                'nombre_destinatario' => $wc_order->billing->first_name ." ".$wc_order->billing->last_name ,
                'telefono' => $wc_order->billing->phone,
                'dni'  => null,
                'direccion' => $wc_order->billing->address_1,
                'depto' => null,
                'ciudad' => $wc_order->billing->city,
                'provincia' => null,
                'pais' => $wc_order->billing->country,
                'codigo_postal' => $wc_order->billing->postcode,
                'metodo_entrega' => 2,
              ]);
            }
          DB::commit();
          }
      } catch (Exception $e) {
            DB::rollback();            
      }
      
    }

    public function ActualizarProductos($woocommerce, $wc) {
      /////////// ACTUALIZAR STOCK DE PRODUCTOS ///////////////////
      //$ultima_importacion = importaciones::where('comercio_id', $wc->comercio_id)->orderBy('id','desc')->first();
            
        $lista_productos = Product::where('comercio_id', $wc->comercio_id)
        ->where('wc_canal', 1)
        ->where('eliminado', 0)
        ->where('wc_push',1)
        ->limit(50)
        ->get();

        foreach($lista_productos as $lp) {

          $product = Product::find($lp->id);

          $product_id = $lp->id;

        
          if($product->wc_canal == 1 && $product->wc_canal != null) {

                ////////////////////////////// PRODUCTOS SIMPLES /////////////////////////////////////////

                if($product->producto_tipo == "s") {
                  $this->ActualizarProductosSimples($product);
                }


                /////////////////////// PRODUCTOS VARIABLES ///////////////////////////////
                
                if($product->producto_tipo == "v") {            
               $this->ActualizarProductosVariables($product); 
                }
            

              // fin de imagenes

          } 
        }
      }
      
    public function ActualizarProductosSimples($product) {

                    if($product->wc_product_id != null) {
                      $this->WocommerceUpdateSimple($product->id);      
                    } else {
                      $this->WocommerceStoreSimple($product->id); 
                    }       
                    
    //                 $product->wc_push = 0;
    //                 $product->save();
                  
    }
      
    public function ActualizarProductosVariables($product) {

                /////////////////////// PRODUCTOS VARIABLES ///////////////////////////////
                     
                    if($product->wc_product_id != null) {
                      $this->WocommerceUpdateVariable($product->id);      
                    } else {
                      $this->WocommerceStoreVariable($product->id); 
                    }
                    
        //            $pvd = productos_variaciones_datos::where('product_id',$product->id)->where('eliminado',0)->where('wc_push',1)->get();
                    
                    // si $count_pdv es 0, es porque no hay ningun wc_push para actualizar.
        //            $count_pdv = $pvd->count();
                    
        //            if($count_pdv < 1) {
        //            $product->wc_push = 0;
        //            $product->save();    
        //            }
                    

      }

    
    public function CrearProductoSiNoExiste(){
        
    }
    
      public function SetNroVenta($comercio_id){
      $sale = Sale::where('comercio_id',$comercio_id)->orderBy('id','desc')->first();
      
      if($sale == null) {
      $nro_venta = 1;      
      }
      if($sale != null){
      $nro_venta = $sale->nro_venta + 1;    
      }
      return $nro_venta;
      
    }
    
    
public function SetVariacionesDatos($item){
        
        if($item->variation_id != null && $item->variation_id != 0) {
        $productos_variaciones_datos = productos_variaciones_datos::where('wc_variacion_id',$item->variation_id)->first();            
        $productos_variaciones_datos = $productos_variaciones_datos->referencia_variacion;
        } else {
        $productos_variaciones_datos = 0;
        }
        
        return $productos_variaciones_datos;
                   
    }
    
    public function ObtenerDescuentoSaleDetail($item){
     // Obtener descuento (cupones)
    $descuento = 0;
    if (!empty($item->meta_data)) {
        foreach ($item->meta_data as $meta) {
            if ($meta->key === '_line_subtotal') {
                $subtotal = $meta->value;
            }
            if ($meta->key === '_line_total') {
                $total = $meta->value;
            }
        }
        // Calcular descuento como la diferencia entre el subtotal y el total
        $descuento = $subtotal - $total;
    }        
    return $descuento;
    }
    
    
    public function ObtenerRecargoSaleDetail(){
    $recargo = 0;
    if (!empty($this->order->fee_lines)) {
        foreach ($this->order->fee_lines as $fee_line) {
            if ($fee_line->name === 'Recargo Cuota') { // Ajustar el nombre del recargo seg¨²n tu caso
                $recargo = $fee_line->amount;
            }
        }
    }   
    return $recargo;
    }
    
    
    public function SetSaleDetail($item,$product_wc,$productos_variaciones_datos,$alicuota_recargo){
    
    
    // Calcular el descuento como la diferencia entre el subtotal y el total
    $descuento = $item->total - $item->subtotal;
    
    // Obtener recargo (cuota)
    $recargo = $item->total * $alicuota_recargo; // esto lo podemos obtener como un % de recargo en los totales de la compra y calcular aca cuanto seria ese % aplicado
      
                
                  // DETALLE DE PRODUCTOS...            
                  $detalle_venta =  SaleDetail::create([
                      'price' => $item->total,
                      'precio_original' => $item->total,
                      'recargo' => $recargo,
                      'descuento' => $descuento,
                      'quantity' => $item->quantity,
                      'metodo_pago'  => $this->metodo_pago_wc,
                      'product_id' => $product_wc->id,
                      'product_name' => $item->name,
                      'product_barcode' => $product_wc->barcode,
                      'iva' => 0,
                      'iva_total' => 0,
                      'seccionalmacen_id' => $product_wc->seccionalmacen_id,
                      'comercio_id' => $product_wc->comercio_id,
                      'sale_id' => $this->order->id,
                      'caja' => $this->caja,
                      'canal_venta' => 'WooCommerce',
                      'cliente_id' => $this->cliente_agregar,
                      'referencia_variacion' => $productos_variaciones_datos
                    ]);

        return $detalle_venta;   
    }
    
    // Aca hay que corregir para que cuando venga pendiente quede bien 
    
    public function UpdateStockAppSync($productos_variaciones_datos,$product_wc,$item,$status){
    
                    $product = productos_stock_sucursales::where('referencia_variacion', $productos_variaciones_datos)->where('product_id',$product_wc->id)->first();
                
                    $stock = null;
                    $cantidad_venta_wc = $item->quantity;
                    
                    //var_dump($status);
                    
                    if($status == "Entregado"){
                        if($product !== null){
                          
                          $stock_comprometido = $this->GetStockComprometido($product_wc->id,$productos_variaciones_datos,$product_wc->comercio_id);
                          
                          $stock_real = $product->stock_real - $cantidad_venta_wc;
                          $stock_disponible = $product->stock_real - $cantidad_venta_wc - $stock_comprometido;
                          $product->stock_real =  $stock_real ;          
                          $product->stock = $stock_disponible;
                          $product->save();
                        
                         //Sin corregir
                            historico_stock::create([
                            'tipo_movimiento' => 1,
                            'referencia_variacion' => $productos_variaciones_datos,
                            'producto_id' => $product_wc->id,
                            'sale_id' => $this->order->id,
                            'cantidad_movimiento' => $item->quantity,
                            'stock' =>  $stock_real,                   
                            'usuario_id' =>  $product_wc->comercio_id,
                            'comercio_id'  => $product_wc->comercio_id,
                          ]);   
                          
                        }      
                    } else {
                        if($product !== null){
                
                          $stock_comprometido = $this->GetStockComprometido($product_wc->id,$productos_variaciones_datos,$product_wc->comercio_id);
                          
                          $stock_disponible = $product->stock_real - $cantidad_venta_wc - $stock_comprometido;
                          $product->stock = $stock_disponible;
                          $product->save();
                          
                        //  var_dump($product);
                        }
                    }
                    
                    
                  
        
    }
    
    public function GetArrayTotalOrders($comercio_id){
    $woocommerce = $this->GetClient($comercio_id);
    
    // Iniciar desde la primera p¨¢gina
    $page = 1;
    $perPage = 20; // Especifica la cantidad de productos por p¨¢gina que deseas
    
    $array_ordenes = [];
    
    do {
        // Obtener la fecha de inicio (primera hora del d¨ªa de hoy)
        $user = User::find($comercio_id);
        $fecha_desde = $user->last_sinc;
        
        // si fecha desde no existe
        if($fecha_desde == null){
        $params = ['page' => $page, 'per_page' => $perPage ];    
        } else {
        $fecha_desde = Carbon::parse($fecha_desde)->toIso8601String();
        $params = ['page' => $page, 'per_page' => $perPage , 'after' => $fecha_desde];    
        }
        
        
        $ordenes = $woocommerce->get('orders', $params);
        
        $page++;
        
        dump($page * $perPage);

        // Combina el array actual con el array global
        $array_ordenes = array_merge($array_ordenes, $ordenes);
                
    } while (!empty($ordenes));

    // Ordenar las ¨®rdenes en orden inverso si es necesario
     $array_ordenes = array_reverse($array_ordenes);    

    return $array_ordenes;
}

    /*
        public function GetArrayTotalOrders($comercio_id){
        
        $woocommerce = $this->GetClient($comercio_id);
        // Iniciar desde la primera pÃ¡gina
        $page = 1;
        $perPage = 20; // Especifica la cantidad de productos por pÃ¡gina que deseas
        
        $array_ordenes = [];
        do {
        // ConfiguraciÃ³n de la solicitud
        $params = ['page' => $page, 'per_page' => $perPage];
    
        $ordenes = $woocommerce->get('orders', $params);
        
        $page++;
        
        dump($page*$perPage);

        array_push($array_ordenes,$ordenes);
                
        } while (!empty($ordenes));

        // Ordenar las Ã³rdenes en orden inverso
        $array_ordenes = array_reverse($array_ordenes);    
        
        return $array_ordenes;
        
    }
    */

    public function GetStockComprometido($product_id,$variacion,$comercio_id) {
 
     return SaleDetail::join('sales','sales.id','sale_details.sale_id')
        ->select(SaleDetail::raw('IFNULL(SUM(sale_details.quantity),0) as stock_comprometido'))
        ->where('sale_details.product_id',$product_id)
        ->where('sale_details.referencia_variacion',$variacion)
        ->where('sale_details.comercio_id',$comercio_id)
        ->where('sale_details.estado',0)
        ->where('sales.eliminado',0)
        ->first()->stock_comprometido;    
    
    }
    


    
    private function acquireLock($lockFile, $lifetime)
    {
        
        if (file_exists($lockFile)) {
            $fileModificationTime = filemtime($lockFile);
            if ((time() - $fileModificationTime) < $lifetime) {
                return false; // El archivo de bloqueo a¨²n es v¨¢lido
            } else {
                // El archivo de bloqueo ha expirado
                unlink($lockFile); // Eliminar el archivo de bloqueo
            }
        }
    
        // Intentar crear el archivo de bloqueo
        return touch($lockFile);
    }
    
    private function releaseLock($lockFile)
    {
        if (file_exists($lockFile)) {
            unlink($lockFile);
        }
    }
    
    public function SyncProductosNuevosWC($woocommerce,$wc,$comercio_id){
                
                $user = User::find($comercio_id);
                $fecha_desde = $user->last_sinc ? Carbon::parse($user->last_sinc) : Carbon::parse('2000-08-15');
                
                // Ahora puedes usar $fecha_desde como un objeto Carbon
                $fecha_formateada = $fecha_desde->toIso8601String();
                
                // Obtener productos creados o actualizados despu¨¦s de $fecha_desde
                $productos = $woocommerce->get('products', [
                    'after' => $fecha_formateada
                ]);
                
                foreach ($productos as $item) {
                    $date_created = Carbon::parse($item->date_created);
                    $date_modified = Carbon::parse($item->date_modified);
                    
                    $product_exist = Product::where('wc_product_id',$item->id)->exists();
                    if(!$product_exist){
                    $this->CrearProductosEnFlaminco($item,$comercio_id);    
                    } else {
                    $this->ActualizarProductosEnFlaminco($item,$comercio_id);    
                    }
                    
                }
                
    }
    
    
    
}



