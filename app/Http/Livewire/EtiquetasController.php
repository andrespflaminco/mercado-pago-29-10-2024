<?php

namespace App\Http\Livewire;

use App\Services\CartEtiquetas;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Session;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Component;

// Modelos
use App\Models\sucursales;
use App\Models\proveedores;
use App\Models\descargas;
use App\Models\descargas_etiquetas;
use App\Models\ClientesMostrador;
use App\Models\Category;
use App\Models\User;
use App\Models\productos_variaciones_datos;
use App\Models\productos_variaciones;
use App\Models\variaciones;
use App\Models\atributos;
use App\Models\Product;
use DB;
use Notification;
use App\Notifications\NotificarCambios;
use Carbon\Carbon;

class EtiquetasController extends Component
{
  use WithPagination;
  use WithFileUploads;

  public $nombre_empresa, $nombre_producto, $precio, $cantidad_imprimir, $codigo_barra, $fecha_impresion, $size, $imprimir_codigo,$codigo, $producto_elegido;

  public $name,$barcode,$cost,$price,$pago, $metodos, $proveedor_id, $caja, $referencia_variacion, $stock,$alerts,$categoryid, $monto_total, $search, $image, $product_id, $pageTitle,$componentName,$comercio_id,$data_Cart, $observaciones, $nombre_sucursal_origen, $query_product, $products_s , $Cart, $deuda,$metodo_pago_elegido, $product,$total, $iva, $iva_general, $itemsQuantity, $cantidad, $carrito, $qty, $sucursal_origen, $tipo_factura, $numero_factura, $query, $tipo_pago, $nombre_sucursal_destino, $query_id, $vigencia;
  private $pagination = 25;
  
  public $filtro_categoria,$filtro_proveedor,$cantidad_filtro;

  public $productos_variaciones_datos = [];

  public function mount()
  {
    $this->nombre_producto = true;
    $this->nombre_empresa = true;
    $this->imprimir_codigo = true;
    $this->precio = true;
    $this->codigo_barra = true;
    $this->fecha_impresion = true;
    $this->size = 2;
    $this->producto_elegido = 2;
    $this->producto_checked = [];

    $this->pageTitle = 'Listado';
    $this->cantidad = 1;
    $this->pago = 0;
    $this->metodos = [];
    $this->tipo_pago = "Elegir";
    $this->iva_general = 0;
    $this->sucursal_origen = 0;
    $this->componentName = 'Productos';
    $this->categoryid = 'Elegir';
    $this->tipo_factura = 'Elegir';


    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $this->casa_central = Auth::user()->casa_central_user_id;
    }


  public function render()
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $cart = new CartEtiquetas;

    $this->tipo_usuario = User::find(Auth::user()->id);
    $this->casa_central_id = Auth::user()->casa_central_user_id;

    $proveedores = proveedores::where('proveedores.comercio_id', $this->casa_central_id)->where('eliminado',0)->get();
    $categorias = Category::where('comercio_id',$this->casa_central_id)->where('eliminado',0)->orderBy('name','asc')->get();
    
    return view('livewire.etiquetas.component',[
      'proveedores' => $proveedores,
      'categorias' => $categorias
    ])
    ->extends('layouts.theme-pos.app')
    ->section('content');
  }




  public function showCart() {
      return view('livewire.ventas-fabrica.cart')
      ->extends('layouts.theme.app')
      ->section('content');
  }

public function ACash() {

  $this-> pago = $this->monto_total;
}


 public function AgregarDesdeModal(){
    $id_cart = $this->id_cart;
    $barcode = $this->barcode;
    $product_id = $this->product_id;
    $referencia_variacion = $this->referencia_variacion;
    $name = $this->name;
    $qty = $this->cantidad;
    $this->Agregar($id_cart,$barcode,$product_id,$referencia_variacion,$name,$qty);
 }

  public function Agregar($id_cart,$barcode,$product_id,$referencia_variacion,$name,$cantidad) {

    $cart = new CartEtiquetas;
    $items = $cart->getContent();


 if ($items->contains('id', $id_cart)) {

   $cart = new CartEtiquetas;
   $items = $cart->getContent();


   foreach ($items as $i)
{
       if($i['id'] === $id_cart) {

         $cart->removeProduct($id_cart);

         $product = array(
             "id" => $i['id'],
             "barcode" => $i['barcode'],
             "product_id" => $i['product_id'],
             "referencia_variacion" => $i['referencia_variacion'],
             "name" => $i['name'],
             "qty" => $cantidad
         );

         $cart->addProduct($product);

     }
}

    $this->resetUI();

    $this->emit('product-added','Producto agregado');

   return back();

} else {

      $cart = new CartEtiquetas;

      $product = array(
          "id" => $id_cart,
          "barcode" => $barcode,
          "product_id" => $product_id,
          "referencia_variacion" => $referencia_variacion,
          "name" => $name,
          "qty" => $cantidad
      );

      $cart->addProduct($product);

      $this->resetUI();



      $this->emit('product-added','Producto agregado');

  }

}

    public function removeProductFromCart($product) {
        $cart = new CartEtiquetas;
        $cart->removeProduct($product);
        session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
        return back();
    }


  public function updateQty($product, $qty) {
      $cart = new CartEtiquetas;
      $items = $cart->getContent();
      
     // dd($product);
      
      foreach ($items as $i)
   {
        if($i['id'] === $product) {
    
            $cart->removeProduct($product);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "product_id" => $i['product_id'],
                "referencia_variacion" => $i['referencia_variacion'],
                "barcode" => $i['barcode'],
                "qty" => $qty,
            );

            $cart->addProduct($product);
          

        }


   }


    $this->emit('product-added','Cantidad modificada');
      return back();
  }


  // escuchar eventos
  protected $listeners = [
    'scan-code'  =>  'BuscarCode',
    	'clearCart'  => 'clearCart',
  ];


  public function BuscarCode($barcode)
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $record = Product::where('barcode',$barcode)->where('comercio_id', $this->casa_central_id)->where('eliminado',0)->first();


    if($record == null || empty($record))
    {

    $this->emit('scan-notfound','El producto no está registrado');

    $this->codigo = '';

    }  else {

      ////////// SI ES VARIACION //////////////////////

    if($record->producto_tipo == "v") {

      $this->productos_variaciones_datos =  productos_variaciones_datos::where('product_id',$record->id)->where('comercio_id', $comercio_id)->get();

      $this->atributos = productos_variaciones::join('variaciones','variaciones.id','productos_variaciones.variacion_id')
      ->select('variaciones.nombre','variaciones.id as atributo_id', 'productos_variaciones.referencia_id')
      ->where('productos_variaciones.producto_id', $record->id)
      ->get();

      $this->product_id = $record->id;
      $this->barcode = $record->barcode;

      $this->variaciones = variaciones::where('variaciones.comercio_id', $comercio_id)->get();

      $this->emit('variacion-elegir', $record->id);

      return $this->barcode;
      }

    $this->product_id = $record->id;
    $this->referencia_variacion = 0;
    $this->id_cart = $record->id."-0";
    $this->name = $record->name;
    $this->barcode = $record->barcode;
    $this->cost = $record->precio_interno;
    $this->price = $record->price;
    $this->stock = $record->stock;
    $this->image = null;

    $this->emit('show-modal','Show modal!');

    $this->codigo = '';
  }

}

public function BuscarCodeVariacion($barcode)
{

$this->product = explode('|-|',$barcode);

$barcode = 	$this->product[0];
$variacion = 	$this->product[1];

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

    $record = Product::join('productos_variaciones_datos','productos_variaciones_datos.product_id','products.id')
    ->select('products.id','products.name','products.barcode','productos_variaciones_datos.variaciones')
    ->where('products.barcode',$barcode)
    ->where('productos_variaciones_datos.referencia_variacion', $variacion)
    ->where('products.eliminado', 0)
    ->where('products.comercio_id', $this->casa_central_id)
    ->first();

  $this->product_id = $record->id;
  $this->referencia_variacion = $variacion;
  $this->barcode = $record->barcode;
  $this->image = null;

  $this->id_cart = $record->id.'-'.$variacion;

  $productos_variaciones_datos = productos_variaciones::join('variaciones','variaciones.id','productos_variaciones.variacion_id')
  ->select('variaciones.nombre')
  ->where('productos_variaciones.referencia_id',$variacion)
  ->get();

  $pvd = [];

  foreach ($productos_variaciones_datos as $pv) {

        array_push($pvd, $pv->nombre);

          }

  $var = implode(" ",$pvd);

  $this->name = $record->name." - ".$var;

  $this->emit('variacion-elegir-hide','Show modal!');
  $this->emit('show-modal','Show modal!');

  $this->codigo = '';
}

public function AbrirModal($id)
{
		$record = Product::find($id);

		$this->product_id = $record->id;
		$this->name = $record->name;
		$this->barcode = $record->barcode;
		$this->cost = $record->cost;
		$this->price = $record->price;
		$this->stock = $record->stock;
		$this->categoryid = $record->categorias_fabrica_id;
		$this->image = null;

		$this->emit('show-modal','Show modal!');
	}


  // reset values inputs
  public function resetUI()
  {
    $this->name ='';
    $this->cantidad =1;
    $this->barcode ='';
    $this->cost ='';
    $this->price ='';
    $this->stock ='';
    $this->alerts ='';
    $this->search ='';
    $this->categoryid = 'Elegir';
    $this->image = null;
    $this->product_id = 0;

  }

  // exportar etiquetas
  public function ExportarEtiquetas()
  {

    $cart = new CartEtiquetas;
    
    if(2500 < $cart->totalCantidad())
    {
      $this->emit('sale-error','MAXIMO PERMITIDO POR DESCARGA: 2500 ETIQUETAS');
      return;
    }


    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    DB::beginTransaction();

    try {

    // GUARDAR LOS DATOS

      $etiquetas = $cart->getContent();

        $datos = $this->nombre_producto."|".$this->precio."|".$this->imprimir_codigo."|".$this->codigo_barra."|".$this->fecha_impresion."|".$this->size."|".$this->producto_elegido;
        
    	$p_count = Product::where('comercio_id', $this->casa_central_id)->where('eliminado',0)->count();
        
        $descargas = descargas::create([
        'user_id' => $comercio_id,
        'comercio_id' => $this->casa_central_id,
        'tipo' => 'exportar_etiquetas',
        'estado' => 0,
        'datos_filtros' => $datos,
        'nombre' => 'Etiquetas_'. $comercio_id . '_' . Carbon::now()->format('d_m_Y_H_i_s')
        ]);

      if($descargas && $this->producto_elegido == '2') {

      foreach ($etiquetas as $item)
      {

            $de = descargas_etiquetas::create([
            'user_id' => $comercio_id,
            'comercio_id' => $this->casa_central_id,
            'descargas_id' => $descargas->id,
            'producto_id' => $item['product_id'],
            'referencia_variacion' => $item['referencia_variacion'],
            'cantidad' => $item['qty']
            ]);

    }
    
    }

      DB::commit();

      $cart->clear();
      //$this->emit('sale-ok','ESTAMOS PREPARANDO SUS ETIQUETAS');
      
      return redirect('etiquetas')->with('status', 'ESTAMOS GENERANDO SU DESCARGA DE ETIQUETAS. '); 
      
    } catch (Exception $e) {
      DB::rollback();
      dd($e->getMessage());
    }

  }


  public function resetProduct()
 {
   $this->products_s = [];
 }

 public function clearCart() {
  $cart = new CartEtiquetas;
  $cart->clear();
 }

  public function selectProduct()
  {
      $this->query_product = '';

      $this->resetProduct();
  }


  public function updatedQueryProduct()
  {

    $this->products_s = 	Product::where('comercio_id',Auth::user()->casa_central_user_id)->where('eliminado',0)->where( function($query) {
            $query->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
        })
          ->limit(5)
          ->get()
          ->toArray();

  }


public function AbrirFiltrosAccionEnLote(){
$this->cantidad_filtro = 1;
$this->filtro_categoria = 0;
$this->filtro_proveedor = 0;
$this->emit('agregar-en-lote','');    
}

public function resetUIFiltroLote(){
$this->filtro_categoria = 0;
$this->filtro_proveedor = 0;    
}

public function AgregarEnLote(){

 $record = Product::where('comercio_id', $this->casa_central_id);
 
 if($this->filtro_proveedor != 0){
     $record = $record->where('proveedor_id',$this->filtro_proveedor);
 }
 if($this->filtro_categoria){
     $record = $record->where('category_id',$this->filtro_categoria);
 }
 
 $record = $record->where('eliminado',0)
 ->get();
 
$cantidad_a_imprimir = $this->cantidad_filtro;

foreach($record as $r){
    
    if($r->producto_tipo == "s"){
        
    $this->AgregarEnLoteSimple($r,$cantidad_a_imprimir);
    
    }
    
    if($r->producto_tipo == "v"){
        
    $this->AgregarEnLoteVariable($r,$cantidad_a_imprimir);
    
    }
    
    
}

$this->emit('agregar-en-lote-hide','Productos agregados');

}


public function AgregarEnLoteSimple($r,$cantidad){
    
    $barcode = $r->barcode;
    $product_id = $r->id;
    $referencia_variacion = 0;
    $id_cart = $product_id."|".$referencia_variacion;
    $name = $r->name;
    
    $this->Agregar($id_cart,$barcode,$product_id,$referencia_variacion,$name,$cantidad);
}

public function AgregarEnLoteVariable($r,$cantidad){
    
    $product_id = $r->id;
    
    $pvd = productos_variaciones_datos::where('product_id',$product_id)->where('eliminado',0)->get();
    foreach($pvd as $pv){
    $barcode = $r->barcode;
    $name = $r->name.' '.$pv->variaciones;        
    $referencia_variacion = $pv->referencia_variacion;
    $id_cart = $product_id."|".$referencia_variacion;        
    
    $this->Agregar($id_cart,$barcode,$product_id,$referencia_variacion,$name,$cantidad);
    }
    
}





}
