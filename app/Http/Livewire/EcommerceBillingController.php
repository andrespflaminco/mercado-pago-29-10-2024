<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;


use App\Models\ecommerce_mp_pago;

use Illuminate\Support\Facades\Http;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\etiquetas;
use App\Models\etiquetas_productos;
use App\Models\Sale;
use App\Models\ClientesMostrador;
use App\Models\provincias;
use App\Models\Category;
use App\Models\productos_stock_sucursales;
use App\Models\User;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use App\Models\ecommerce_envio;
use App\Models\ecommerce;
use App\Models\datos_facturacion;
use App\Services\CartEcommerce;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\Session;
use App\Models\metodo_pago;
use App\Models\cajas;
use App\Models\historico_stock;
use App\Models\bancos;
use DB;


class EcommerceBillingController extends Component
{

    use WithPagination;

    public $products, $comercio_id, $search, $name, $barcode, $price, $stock, $image, $categories_menu, $selected_id, $cost, $cantidad, $slug, $nombre_destinatario, $dni, $ciudad, $pais, $provincia, $direccion, $depto, $codigo_postal, $metodo_entrega, $ecommerce, $metodo_pago_nuevo, $departamento, $observaciones, $tk, $metodo_pago, $caja, $descuento, $data_ecommerce, $item, $cart, $preference;
    public $color, $background_color;
    
  	private $pagination = 25;

    public function paginationView()
    {
    return 'vendor.livewire.bootstrap';
    }

    public function mount($slug) {
      $this->cantidad = 1;
      $this->slug = $slug;
      $this->data_ecommerce = [];
    //  $this->ecommerce = ecommerce::where('slug',$slug)->first();
      $this->descuento = session('descuento');

      // 14-6-2024
      $this->ecommerce_encontrado = 1;
      $this->ecommerce = ecommerce::where('slug',$slug)->first();
      if($this->ecommerce == null){
      $this->ecommerce_encontrado = 0;    
      }       
      
      if($this->ecommerce_encontrado == 1){
      $this->comercio_id = $this->ecommerce->comercio_id;
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
      'Save' => 'Save'
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
       
    $this->data = User::find($this->ecommerce->comercio_id);
      
    $this->casa_central_id = $this->data->casa_central_user_id;

    $this->datos_retiro = User::leftjoin('datos_facturacions','datos_facturacions.comercio_id','users.id')->where('users.id', $this->ecommerce->comercio_id)->first();

    $this->tk = $this->ecommerce->mp_token;
    $this->ky = $this->ecommerce->mp_key;
      
    $registro = $this->ecommerce->registro;

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
    
      $cart = new CartEcommerce;
      $this->total = $cart->totalAmount();
      
      $this->color = $this->ecommerce->color;
      $this->background_color = $this->ecommerce->background_color;
        
      return view('livewire.ecommerce_billing.component', [
        'background_color' => $this->background_color,
        'color' => $this->color,
        'tk' => $this->tk,
        'ky' => $this->ky,
        'imagen' => $this->data->image,
        'data_e' => $this->data,
        'registro' => $registro,
        'etiquetas_menu' => $this->etiquetas_menu,
        'datos_retiro' => $this->datos_retiro,
        'categories' => Category::orderBy('name','asc')->where('comercio_id', 'like', $this->ecommerce->comercio_id)->get(),
        'provincias' => provincias::orderBy('provincia','asc')->get(),
        'categories_menu' => $this->categories_menu,
        'ecommerce' => ecommerce::leftjoin('bancos','bancos.id','ecommerces.banco_id')->select('ecommerces.*','bancos.id','bancos.nombre as nombre_banco','bancos.CBU','bancos.cuit')->where('ecommerces.comercio_id', $this->ecommerce->comercio_id )->first(),
        'banco' => ecommerce::join('bancos','bancos.id','ecommerces.banco_id')->select('ecommerces.*','bancos.id','bancos.nombre as nombre_banco','bancos.CBU','bancos.cuit')->where('ecommerces.comercio_id', $this->ecommerce->comercio_id )->get()
      ])
      ->extends('layouts.theme-ecommerce.app')
      ->section('content');

    }

    public function MetodoPago()
    {
    $this->metodo_pago_nuevo = $this->metodo_pago;
    }

    // guardar venta
    public function prueba()
    {
    dd("prueba");
    }    
    
    public function SaveSale(Request $request)
    {
       $this->metodo_entrega = $request->metodo_entrega;
       $this->metodo_pago = $request->metodo_pago;
       
      if($this->metodo_pago == "Elegir")
      {
        $this->emit('sale-error','DEBE ELEGIR LA FORMA DE PAGO');
        return;
      }

      if($this->metodo_entrega == "Elegir")
      {
        $this->emit('sale-error','DEBE ELEGIR LA FORMA DE ENVIO');
        return;
        
      }
      
      
      $this->ecommerce = ecommerce::where('slug',$request->slug)->first();
      


      $this->caja = cajas::where('estado',0)->where('comercio_id',$this->ecommerce->comercio_id)->max('id');

          
      // Determinar el cliente 
      if($this->ecommerce->registro == 2) 
      {
      $nombre_destinatario = $request->nombre_destinatario;
      $telefono = $request->telefono;
      $cliente_id = 1;
      } else { 
        
      $cliente = ClientesMostrador::find(Auth::user()->cliente_id);
      
      $nombre_destinatario = $cliente->nombre;
      
      if($request->telefono == null) {
      $telefono = $cliente->phone;    
      } else {
      $telefono = $request->telefono;
      }
      
      $cliente_id = $cliente->id;
      }
      
      

      $cart = new CartEcommerce;

      DB::beginTransaction();

      try {

        $this->descuento_ratio = session('descuento');
        $this->total = $cart->totalAmount();
        $this->itemsQuantity = $cart->totalCantidad();
        $this->descuento = $this->descuento_ratio * $this->total;

        $cash = $request->paga_con;
        
        if($request->metodo_pago != 1) {
        $cash=$this->total;    
        } else {
        $cash = $cash;    
        }
        
        // Tomamos los datos de facturacion
        
        $datos_facturacion = datos_facturacion::where('comercio_id',$this->ecommerce->comercio_id)->first();
        

        $nro_venta = $this->SetNroVenta($this->ecommerce->comercio_id);
        
        
        $sale = DB::table('sales')->insertGetId([
          'subtotal' => $this->total,
          'nro_venta' => $nro_venta,
          'total' => $this->total,
          'recargo' => 0,
          'descuento' =>   $this->descuento,
          'items' => $this->itemsQuantity,
          'tipo_comprobante'  => 'CF',
          'cash' => $cash,
          'change' => 0,
          'iva' => 0,
          'metodo_pago'  => $request->metodo_pago,
          'comercio_id' =>  $this->ecommerce->comercio_id,
          'cliente_id' => $cliente_id,
          'user_id' =>  $this->ecommerce->comercio_id,
          'observaciones' => $request->observaciones,
          'canal_venta' => 'Ecommerce',
          'estado_pago' => 'Pendiente',
          'caja' => $this->caja,
          'deuda' => $this->total,
          'status' => 'Pendiente',
          'nota_interna' => ''
        ]);

        if($sale)
        {

            $items = $cart->getContent();

           ecommerce_envio::create([
                'metodo_entrega' => $request->metodo_entrega,
                'nombre_destinatario' => $nombre_destinatario,
                'telefono' => $telefono ?? null,
                'dni' => $request->dni?? null,
                'sale_id' => $sale,
                'comercio_id' => $this->ecommerce->comercio_id,
                'ciudad' => $request->ciudad ?? null,
                'direccion' => $request->direccion ?? null,
                'depto' => $request->departamento ?? null,
                'provincia' => $request->provincia ?? null,
                'pais' => $request->pais ?? null,
                'codigo_postal' => $request->codigo_postal ?? null 
           ]);


          foreach ($items as  $item) {
              
           $p = Product::find($item['product_id']);
            
            // Vemos el IVA defecto que tiene y lo tomamos en cuenta (EN PREPARACION)
            
            if($datos_facturacion == null){
                                     
            $this->iva = 0;
            $this->precio = $item['price'];
            $this->relacion_precio_iva = 0;
            
            } else {
            
            if($datos_facturacion->relacion_precio_iva == 2) {
                
            $this->iva = $datos_facturacion->iva_defecto;
            $this->precio = $item['price']/(1+$datos_facturacion->iva_defecto) ;
            $this->relacion_precio_iva = $datos_facturacion->relacion_precio_iva;
                        
            } 
        
            if($datos_facturacion->relacion_precio_iva == 1) {
                         
            $this->iva = $datos_facturacion->iva_defecto;
            $this->precio = $item['price'];
            $this->relacion_precio_iva = $datos_facturacion->relacion_precio_iva;
            }
                
            if($datos_facturacion->relacion_precio_iva == 0) {
                         
            $this->iva = 0;
            $this->precio = $item['price'];
            $this->relacion_precio_iva = $datos_facturacion->relacion_precio_iva;
            }                
            }

            
            // Insertamos el detalle del producto 
            
            SaleDetail::create([
              'product_id' => $item['product_id'],
              'referencia_variacion' => $item['referencia_variacion'],
              'price' => $item['price'],
              'product_name' => $item['name'],
              'product_barcode' => $item['barcode'],
              'quantity' => $item['qty'],
              'seccionalmacen_id' => $item['seccion_almacen'],
              'sale_id' => $sale,
              'recargo' => 0,
              'descuento' => 0,
              'iva' => 0,
              'cliente_id' => $cliente_id,
              'stock_de_sucursal_id' => $this->ecommerce->comercio_id,
              'comercio_id' => $this->ecommerce->comercio_id,
              'metodo_pago'  => $this->metodo_pago,
              'seccionalmacen_id' => $p->seccionalmacen_id,
              'caja' => $this->caja,
              'canal_venta' => 'Ecommerce'
            ]);

            //update stock
            $product = productos_stock_sucursales::where('productos_stock_sucursales.product_id', $item['product_id'])
            ->where('productos_stock_sucursales.comercio_id', $this->ecommerce->comercio_id)
            ->where('productos_stock_sucursales.referencia_variacion', $item['referencia_variacion'])
            ->first();
            
            $product->stock = $product->stock - $item['qty'];
            $product->save();

            $historico_stock = historico_stock::create([
              'tipo_movimiento' => 10,
              'producto_id' => $item['product_id'],
              'referencia_variacion' => $item['referencia_variacion'],
              'cantidad_movimiento' => $item['qty'],
              'stock' => $product->stock,
              'comercio_id'  =>  $this->ecommerce->comercio_id,
              'usuario_id'  =>  $this->ecommerce->comercio_id
            ]);


          }

        }


        DB::commit();


        $this->pago = 0;
        $this->observaciones = '';
        $this->monto = 0;
        $this->metodo_pago_elegido = 'Elegir';
        session(['descuento' => 0]);

        $cart->clear();
        
        $this->forma_aviso = $this->ecommerce->comunicacion;
    
       if($this->forma_aviso == 1) {
         
       return \Redirect::to("ecommerce-email/pdf/" . $sale . "/"  .$cliente->email ."/".  $this->ecommerce->slug  );

        }
        
        if($this->forma_aviso == 2) {
         return \Redirect::to("ecommerce-ws/" . $sale ."/".  $this->ecommerce->slug);
         
        }
        




      } catch (Exception $e) {
        DB::rollback();
        $this->emit('sale-error', $e->getMessage());
      }

    }




    // guardar venta
    public function pay(Request $request, $slug)
    {

      $cart = new CartEcommerce;

      $items = $cart->getContent();



      $this->ecommerce = ecommerce::where('slug',$slug)->first();

      $tk = $this->ecommerce->mp_token;

      $payment_id = $request->get('payment_id');

      $response = Http::get("https://api.mercadopago.com/v1/payments/$payment_id" . "?access_token=$tk");

      $response = json_decode($response);

      $status = $response->status;


      ////// SI LA RESPUESTA ESTA APROBADA //////////


      if($status == "approved") {

        $this->caja = cajas::where('estado',0)->where('comercio_id',$this->ecommerce->comercio_id)->max('id');

        $this->cliente = ClientesMostrador::find(Auth::user()->cliente_id);

        $cart = new CartEcommerce;

        DB::beginTransaction();

        try {

          $this->descuento_ratio = session('descuento');
          $this->total = $cart->totalAmount();
          $this->itemsQuantity = $cart->totalCantidad();
          $this->descuento = $this->descuento_ratio * $this->total;
          $this->total_sin_descuento = $this->total - $this->descuento;

          $nro_venta = $this->SetNroVenta();

          $sale = DB::table('sales')->insertGetId([
            'total' => $this->total,
            'nro_venta' => $nro_venta,
            'recargo' => 0,
            'descuento' =>   $this->descuento,
            'items' => $this->itemsQuantity,
            'tipo_comprobante'  => 'CF',
            'cash' => 0,
            'change' => 0,
            'iva' => 0,
            'metodo_pago'  => 1,
            'comercio_id' =>  $this->ecommerce->comercio_id,
            'cliente_id' => Auth::user()->cliente_id,
            'user_id' =>  $this->ecommerce->comercio_id,
            'observaciones' => $this->observaciones,
            'canal_venta' => 'Ecommerce',
            'estado_pago' => 'Pendiente',
            'caja' => $this->caja,
            'deuda' => 0,
            'status' => 'Pendiente',
            'nota_interna' => ''
          ]);

          if($sale)
          {

            $mp = ecommerce_mp_pago::create([
              'sale_id' => $sale,
              'comercio_id' =>  $this->ecommerce->comercio_id,
              'cliente_id' => Auth::user()->cliente_id,
              'status' => $response->status,
              'mp_id' => $response->id,
              'payment_method_id' => $response->payment_method_id,
              'payer_id' => $response->payer->id
            ]);


            $pagos = DB::table('pagos_facturas')->insert([
              'monto' => $this->total_sin_descuento,
              'cambio' => 0,
              'recargo' => 0,
              'id_factura' => $sale,
              'comercio_id' => $this->ecommerce->comercio_id,
              'caja' => $this->caja,
              'metodo_pago'  => 1,
              'eliminado' => 0
            ]);


              if($this->metodo_entrega == 2)
              {
                ecommerce_envio::create([
                  'nombre_destinatario' => $this->nombre_destinatario,
                  'dni' => $this->dni,
                  'sale_id' => $sale,
                  'comercio_id' => $this->ecommerce->comercio_id,
                  'ciudad' => $this->ciudad,
                  'direccion' => $this->direccion,
                  'depto' => $this->departamento,
                  'provincia' => $this->provincia,
                  'pais' => $this->pais,
                  'codigo_postal' => $this->codigo_postal
                ]);

              }


            //////// GUARDA EL DETALLE DEL PEDIDO /////////


            $cart = new CartEcommerce;
            $items = $cart->getContent();

            foreach ($items as  $item) {
              SaleDetail::create([
                'product_id' => $item['id'],
                'price' => $item['price'],
                'precio_original' => $item['price'],
                'product_name' => $item['name'],
                'product_barcode' => $item['barcode'],
                'quantity' => $item['qty'],
                'sale_id' => $sale,
                'cliente_id' => Auth::user()->cliente_id,
                'comercio_id' => $this->ecommerce->comercio_id,
    			'metodo_pago'  => $this->metodo_pago,
    			'seccionalmacen_id' => $item['seccion_almacen'],
    			'caja' => $this->caja,
    			'canal_venta' => 'Ecommerce'
              ]);

              //update stock
              $product = Product::find($item['id']);
              $product->stock = $product->stock - $item['qty'];
              $product->save();

              $historico_stock = historico_stock::create([
                'tipo_movimiento' => 10,
                'producto_id' => $item['id'],
                'cantidad_movimiento' => $item['qty'],
                'stock' => $product->stock,
                'comercio_id'  =>  $this->ecommerce->comercio_id,
                'usuario_id'  =>  $this->ecommerce->comercio_id
              ]);


            }

          }
          
        // link

        $ws_phone = "https://api.whatsapp.com/send?phone=".Auth::user()->phone;
        
        // Resumen del pedido
        
        if($this->metodo_entrega == 2) { 
            $nombre_destinatario = $this->nombre_destinatario;
            $telefono_destinatario = 0;
            $metodo_entrega = "Envio%20a%20domicilio";
        } else { 
            $nombre_destinatario = Auth::user()->name;
            $telefono_destinatario = Auth::user()->phone;
             $metodo_entrega = "Retiro%20personalmente";
        }
        
        $metodo_pago = $this->metodo_pago;
        
        
        $ws_info_del_pedido = "&text=_%C2%A1Hola!%20Te%20paso%20el%20resumen%20de%20mi%20pedido:_%0A%0A%20*Pedido:*%20".$sale->id."%0A%20*Nombre:*%20".$nombre_destinatario."%0A%20*Tel%C3%A9fono:*%20".$telefono_destinatario."%0A%0A%20*Forma%20de%20pago:*%20".$metodo_pago."%0A%20*Total:*%20$%20".$sale->total."%0A%20*Pago%20con:*%20$%202.000%0A%0A%20*Entrega:*%20".$metodo_entrega."%20%0A%0A_Mi%20pedido%20es:_%0A%0A";
        
        // Resumen del detalle de la compra
        
        $cart = new CartEcommerce;
        $items = $cart->getContent();

        $ws_detalle_venta = "%20";
        foreach ($items as  $item) {
        
        $producto = $item['name'];
        $producto =str_replace(' ', '%20', $producto);
        
        $ws_detalle_venta .= "*Carnicer%C3%ADa%20(Novillito)*%0A20".$item['quantity']."x%20".$producto.":%20$%20".$item['price']."%0A";
        }
        // Total
        
        $ws_total = "%0A*TOTAL:%20$%20".$sale->total."*%0A%0A%20_Espero%20tu%20respuesta%20para%20confirmar%20mi%20pedido_";
        
        $ws_link = $ws_phone.$ws_info_del_pedido.$ws_detalle_venta.$ws_total;
        
        dd($ws_link);

          DB::commit();

        //  $this->pago = 0;
        //  $this->observaciones = '';
        //  $this->monto = 0;
        //  $this->metodo_pago_elegido = 'Elegir';
        //  session(['descuento' => 0]);

        //  $cart->clear();
        //  return \Redirect::to("ecommerce-email/pdf/" . $sale . "/"  .$this->cliente->email ."/".  $this->ecommerce->slug  );



        } catch (Exception $e) {
          DB::rollback();
          $this->emit('sale-error', $e->getMessage());
        }


        }

    }



  public function SetNroVenta($comercio_id){
      $sale = Sale::where('comercio_id',$comercio_id)->orderBy('id','desc')->first();
      
      if($sale != null) {
      if($sale->nro_venta != null) {
      $nro_venta = $sale->nro_venta + 1;    
      } else {
      $nro_venta = 1;    
      }
          
      } else {$nro_venta = 1;}
      
      return $nro_venta;
      
    }
    
    
}
