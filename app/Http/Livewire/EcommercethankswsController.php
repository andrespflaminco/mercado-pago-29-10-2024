<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

use Livewire\WithPagination;
use App\Models\Product;
use App\Models\provincias;
use App\Models\ClientesMostrador;
use App\Models\Category;
use App\Models\Sale;
use App\Models\User;
use App\Models\etiquetas;
use App\Models\etiquetas_productos;
use App\Models\ecommerce_mp_pago;
use Illuminate\Http\Request;
use App\Models\SaleDetail;
use App\Models\ecommerce_envio;
use App\Models\ecommerce;
use App\Services\CartEcommerce;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\Session;
use App\Models\metodo_pago;
use App\Models\cajas;
use App\Models\historico_stock;
use App\Models\bancos;
use DB;


class EcommercethankswsController extends Component
{

    use WithPagination;

    public $products, $comercio_id, $search, $name, $barcode, $price, $stock, $image, $selected_id, $cost, $cantidad, $slug, $nombre_destinatario, $dni, $ciudad, $pais, $provincia, $direccion, $depto, $codigo_postal, $ecommerce, $departamento, $observaciones, $metodo_pago, $caja, $descuento;

  	private $pagination = 25;

    public function paginationView()
    {
    return 'vendor.livewire.bootstrap';
    }

    public function mount($id,$slug) {
      $this->cantidad = 1;
      $this->slug = $slug;

      $this->ecommerce = ecommerce::where('slug',$slug)->first();

      $this->descuento = session('descuento');
      $this->id = $id;
      

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
     
    $this->casa_central_id = $this->data->casa_central_user_id;
    
    $this->categories_menu = Category::orderBy('name','asc')
    ->where('comercio_id', 'like', $this->ecommerce->comercio_id)
    ->get();
    
    $this->etiquetas_menu = etiquetas::orderBy('nombre','asc')
    ->where('eliminado',0)
    ->where('comercio_id', $this->casa_central_id)
    ->get();
    
      // ENVIAR LINK DE WHATSAPP
        
        // link
        
        $sale = Sale::find($this->id);
        
        $ecommerce_envio = ecommerce_envio::where('sale_id',$sale->id)->first();
        
        $cliente = ClientesMostrador::join('sales','sales.cliente_id','clientes_mostradors.id')->where('sales.id',$this->id)->first();
        
        $u = User::find($sale->comercio_id);
        
        $telefono_comercio = ltrim($cliente->telefono, "0");
         
        $ws_phone = "https://api.whatsapp.com/send?phone=".$telefono_comercio;
        
        // Resumen del pedido
        
        if($ecommerce_envio->metodo_entrega == 2) { 
            $nombre_destinatario = $ecommerce_envio->nombre_destinatario;
            $nombre_destinatario =str_replace(' ', '%20', $nombre_destinatario);
            $telefono_destinatario = $ecommerce_envio->telefono;
            $metodo_entrega = "Envio%20a%20domicilio";
            $telefono_destinatario = ltrim($telefono_destinatario, "0");
            
            // Envios
            
            $direccion_envio = $ecommerce_envio->direccion." ".$ecommerce_envio->depto;
            $direccion_envio =str_replace(' ', '%20', $direccion_envio);
            $ciudad_envio = $ecommerce_envio->ciudad;
            $ciudad_envio =str_replace(' ', '%20', $ciudad_envio);
            $provincia_envio = provincias::find($ecommerce_envio->provincia)->nombre;
            $provincia_envio =str_replace(' ', '%20', $provincia_envio);
            
            $direccion = "%20%0A%0A%20Direccion%20de%20envio:%20".$direccion_envio."%20,%20".$ciudad_envio."%20,%20".$provincia_envio;
            
            } else { 
            $nombre_destinatario = $ecommerce_envio->nombre_destinatario;
            $nombre_destinatario =str_replace(' ', '%20', $nombre_destinatario);
            $telefono_destinatario = $ecommerce_envio->telefono;
            $telefono_destinatario = ltrim($telefono_destinatario, "0");
            //dd($telefono_destinatario);
            $metodo_entrega = "Retiro%20personalmente";
            $direccion = "%20";
        }
        
        $metodo_pago = bancos::find($sale->metodo_pago)->nombre;
        
        
        $ws_info_del_pedido = "&text=_%C2%A1Hola!%20Te%20paso%20el%20resumen%20de%20mi%20pedido:_%0A%0A%20*Pedido:*%20".$sale->id."%0A%20*Nombre:*%20".$nombre_destinatario."%0A%20*Tel%C3%A9fono:*%20".$telefono_destinatario."%0A%0A%20*Forma%20de%20pago:*%20".$metodo_pago."%0A%20*Total:*%20$%20".$sale->total."%0A%20*Pago%20con:*%20$%20".$sale->cash."%0A%0A%20*Entrega:*%20".$metodo_entrega;
        
        $ws_mi_pedido_es = "%0A%0A_Mi%20pedido%20es:_%0A%0A";
        
        
        // Resumen del detalle de la compra
        $items = SaleDetail::where('sale_id', $sale->id)->get();
        
        $ws_detalle_venta = "%20";
        foreach ($items as  $item) {
        
        $producto = $item['product_name'];
        $producto =str_replace(' ', '%20', $producto);
        
        $p = Product::find($item['product_id']);
        $categoria = Category::find($p->category_id);
        
        $categoria = $categoria->name;
        
        $categoria =str_replace(' ', '%20', $categoria);
        
        $ws_detalle_venta .= "*".$categoria."*%0A%20".$item['quantity']."x%20".$producto.":%20$%20".$item['price']."%0A";
        
        
        }
        // Total
        
        if($sale->observaciones != null) {
        $ws_nota = "%0A*NOTA AL LOCAL:*%20%20".$sale->observaciones."%0A";
        } else {
        $ws_nota = "%20";    
        }
        
        $ws_total = "%0A*TOTAL:%20$%20".$sale->total."*%0A%0A%20Espero%20tu%20respuesta%20para%20confirmar%20mi%20pedido";
        
        $ws_link = $ws_phone.$ws_info_del_pedido.$direccion.$ws_mi_pedido_es.$ws_detalle_venta.$ws_nota.$ws_total;
        
        
      
      
      return view('livewire.ecommerce_thanks_ws.component', [
        'data_e' => $this->data,
        'categories_menu' => $this->categories_menu,
        'imagen' => $this->data->image,
        'data' => $this->data,
        'ws_link' => $ws_link,
        'etiquetas_menu' => $this->etiquetas_menu,
        'categories' => Category::orderBy('name','asc')->where('comercio_id', 'like', $this->ecommerce->comercio_id)->get(),
        'provincias' => provincias::orderBy('provincia','asc')->get()
      ])
      ->extends('layouts.theme-ecommerce.app')
      ->section('content');

    }


    public function pay(Request $request) {

      $payment_id = $request->get('payment_id');

      $response = Http::get("https://api.mercadopago.com/v1/payments/$payment_id" . "?access_token=APP_USR-122023056725190-021316-8deebf83d1eaa95cf96a9245fd70b2d7-185905689");

      $response = json_decode($response);

      $status = $response->status;

      $mp = ecommerce_mp_pago::create([
        'status' => $response->status,
        'mp_id' => $response->id,
        'payment_method_id' => $response->payment_method_id,
        'payer_id' => $response->payer->id
  		]);




    }


}
