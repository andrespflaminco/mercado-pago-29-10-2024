<?php

namespace App\Http\Livewire;

use App\Services\CartRecetas;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Session;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\proveedores;
use App\Models\recetas_costos;
use App\Models\receta;
use App\Models\listas_precios;
use App\Models\Category;
use App\Models\productos_lista_precios;
use App\Models\productos_variaciones_datos;
use App\Models\insumo;
use App\Models\metodo_pago;
use App\Models\cajas;
use App\Models\tipo_unidad_medida;
use App\Models\historico_stock;
use App\Models\pagos_facturas;
use App\Models\unidad_medida_relacion;
use App\Models\Product;
use App\Models\bancos;
use App\Models\unidad_medida;
use App\Models\compras_proveedores;
use App\Models\detalle_compra_proveedores;
use DB;

use App\Traits\RecetasTrait;

use Illuminate\Http\Request;

class RecetasDetalleController extends Component
{
  use WithPagination;
  use WithFileUploads;
  use RecetasTrait;

  public $name,$barcode,$cost,$price,$pago, $proveedor_id, $rinde, $caja, $stock,$alerts,$categoryid, $codigo, $monto_total, $search, $image, $selected_id, $pageTitle,$componentName,$comercio_id,$data_Cart, $observaciones, $unidad_medida, $unidad_medida_elegida, $query_product, $products_s , $cart_recetas, $cart, $deuda,$metodo_pago_elegido, $product,$total, $itemsQuantity, $cantidad, $carrito, $qty, $tipo_unidad_medida_producto, $product_id, $referencia_variacion;
  private $pagination = 25;

  public function mount(Request $request)
  {
      
    
    $this->producto_id = $request->input('product_id');
    $this->referencia_variacion = $request->input('referencia_variacion');
    $this->accion = $request->input('accion'); // 1 es agregar uno nuevo, 2 es editar, 3 es ver
    
    
    
    $this->pageTitle = 'Listado';
    $this->cantidad = 1;
    $this->componentName = 'Productos';
    $this->metodo_pago_elegido = 'Elegir';
    $this->categoryid = 'Elegir';
    $this->unidad_medida = [];

    //$array = explode('&',$product_id);
    //$this->producto_id = $array[0];
    //$this->referencia_variacion = $array[1];


    $cart_recetas = new CartRecetas;
    $cart_recetas->clear();
    
    // Si la accion es distinta a 1 cargar la receta
    if($this->accion != 1){
          $this->CargarReceta();
    }
    
    
  }

public function CargarReceta(){

    // VER ACA
    $products = receta::leftjoin('unidad_medidas','unidad_medidas.id','recetas.unidad_medida')
    ->leftjoin('products','products.id','recetas.insumo_id')
    ->where('recetas.product_id',$this->producto_id)
    ->where('recetas.eliminado',0)
    ->select('unidad_medidas.nombre as nombre_unidad_medida','recetas.*','products.name','products.barcode')
    ->get();
    
    /*
    $products = Product::leftjoin('recetas as r','r.product_id','products.id')
    ->join('unidad_medidas','unidad_medidas.id','r.unidad_medida')
    ->select('products.id','r.rinde','unidad_medidas.nombre as nombre_unidad_medida','products.name','products.barcode','products.cost','r.unidad_medida','r.cantidad','r.costo_unitario','r.cantidad','r.relacion_medida','products.relacion_unidad_medida','r.product_id')
    ->where('products.id', $this->producto_id)
    ->where('r.referencia_variacion', $this->referencia_variacion)
    ->where('products.eliminado', 0)
    ->where('r.eliminado', 0)
    ->get();
    */
    
    $cart_recetas = new CartRecetas;
    $items = $cart_recetas->getContent();

    if($cart_recetas->hasProducts()) {

     $items = $cart_recetas->getContent();

   } else {

    
     $cart_recetas = new CartRecetas;
     $items = $cart_recetas->getContent();
     $cart_recetas->clear();

     foreach ($products as $p)
  {
      
      $this->rinde = $p['rinde'];

           $product = array(
               "id" => $p['insumo_id'],
               "barcode" => $p['barcode'],
               "name" => $p['name'],
               "cost" => $p['costo_unitario'],
               "qty" => $p['cantidad'],
               "unidad_medida" => $p['unidad_medida'],
               "nombre_unidad_medida" => $p['nombre_unidad_medida'],
               "relacion" => $p['relacion_medida'],
               "relacion_cantidades" => $p['relacion_cantidades']
           );

           $cart_recetas->addProduct($product);

 }

  }

}
  public function render()
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

      $products = Product::join('categories as c','c.id','products.category_id')
			->join('seccionalmacens as a','a.id','products.seccionalmacen_id')
			->join('proveedores as pr','pr.id','products.proveedor_id')
			->select('products.*','c.name as category','a.nombre as almacen','pr.nombre as nombre_proveedor')
			->where('products.comercio_id', 'like', $comercio_id)
			->where('products.eliminado', 'like', 0)
			->orderBy('products.name','asc')
			->get();
      

      $metodo_pagos = bancos::where('bancos.comercio_id', 'like', $comercio_id)->get();

      $proveedores = proveedores::where('proveedores.comercio_id', 'like', $comercio_id)->where('eliminado',0)->get();

    return view('livewire.recetas_detalle.component',[
      'data' => $products,
      'proveedores' => $proveedores,
      'metodo_pago' => $metodo_pagos,
      'categorias_fabrica' => Category::orderBy('name','asc')->get()
    ])
    ->extends('layouts.theme-pos.app')
    ->section('content');
  }




  public function showCart() {
      return view('livewire.ventas-fabrica.cart')
      ->extends('layouts.theme.app')
      ->section('content');
  }



  public function CalcularRelacion($product){
  
  $unidad_base_elegida = unidad_medida_relacion::where('unidad_medida',$this->unidad_medida_elegida)->first()->relacion;
  $unidad_base_producto = unidad_medida_relacion::where('unidad_medida',$product->unidad_medida)->first()->relacion;
  
  return $unidad_base_elegida/$unidad_base_producto;     
  }
  
  
  public function Agregar() {

    if($this->unidad_medida_elegida == null){
        $this->emit("msg-error","Debe elegir la unidad de medida");
        return;
    }
    
    $cart_recetas = new CartRecetas;
    $items = $cart_recetas->getContent();

    $product = Product::find($this->selected_id);
    $this->unidad_medida_selected = unidad_medida::find($this->unidad_medida_elegida);
    
    $relacion = $this->CalcularRelacion($product); 
  
    $this->relacion = $relacion;
     
    $this->costo_unitario_insumo = $product->cost/$product->cantidad; // ver aca para calcular el de todas las listas de precios

    $this->cost = $this->costo_unitario_insumo;
    
    $relacion_cantidad = $this->cantidad/$product->cantidad;

    /////////////////////////////////////////////////////////////
    
    
     if ($items->contains('id', $this->selected_id)) {
    
       $cart_recetas = new CartRecetas;
       $items = $cart_recetas->getContent();
    
       $product = Product::find($this->selected_id);
    
    
       foreach ($items as $i)
    {
           if($i['id'] === $product['id']) {
    
             $cart_recetas->removeProduct($i['id']);
    
             $product = array(
                 "id" => $i['id'],
                 "barcode" => $i['barcode'],
                 "name" => $i['name'],
                 "cost" => $i['cost'],
                 "qty" => $i['qty']+$this->cantidad,
                 "unidad_medida" => $this->unidad_medida_selected->id,
                 "nombre_unidad_medida" => $this->unidad_medida_selected->nombre,
                 "relacion" => $this->relacion,
                 "relacion_cantidades" => $relacion_cantidad
             );
    
             $cart_recetas->addProduct($product);
    
         }
    }
    
        $this->emit('product-added','Producto agregado');
    
       return back();
    
    } else {
    
          $cart_recetas = new CartRecetas;
    
          $product = array(
              "id" => $this->selected_id,
              "barcode" => $this->barcode,
              "name" => $this->name,
              "cost" => $this->cost,
              "qty" => $this->cantidad,
              "unidad_medida" => $this->unidad_medida_selected->id,
              "nombre_unidad_medida" => $this->unidad_medida_selected->nombre,
              "relacion" => $this->relacion,
              "relacion_cantidades" => $relacion_cantidad
          );
          
          /*
          $product = array(
              "id" => 0,
              "barcode" => 0,
              "name" => "Hs hombre",
              "cost" => 100,
              "qty" => 2,
              "unidad_medida" => 9,
              "nombre_unidad_medida" => "Unidad",
              "relacion" => 1,
              "relacion_cantidades" => 1
          );
          */
          
    
          $cart_recetas->addProduct($product);
    
          $this->emit('product-added','Insumo agregado');
    
      }

}
  

  public function Actualizar($selected_id) {

     $cart_recetas = new CartRecetas;
     $items = $cart_recetas->getContent();
     $this->unidad_medida_selected = unidad_medida::find($this->unidad_medida_elegida);
       
     if ($items->contains('id', $selected_id)) {

       $product = Product::find($selected_id);

       $relacion = $this->CalcularRelacion($product);
       $costo_unitario_insumo = $product->cost/$product->cantidad;
       $cost = $costo_unitario_insumo;
       $relacion_cantidad = $this->cantidad/$product->cantidad;
    
       foreach ($items as $i)
    {
           if($i['id'] === $product['id']) {
    
             $cart_recetas->removeProduct($i['id']);
    
             $product = array(
                 "id" => $i['id'],
                 "barcode" => $i['barcode'],
                 "name" => $i['name'],
                 "cost" => $cost,
                 "qty" => $this->cantidad,
                 "unidad_medida" => $this->unidad_medida_selected->id,
                 "nombre_unidad_medida" => $this->unidad_medida_selected->nombre,
                 "relacion" => $relacion,
                 "relacion_cantidades" => $relacion_cantidad
             );
    
             $cart_recetas->addProduct($product);
    
         }
    }
    
    $this->resetUI();

    $this->emit('product-added','Insumo actualizado');
    return back();
    }

  }

    public function removeProductFromCart(Product $insumo) {
        $cart_recetas = new CartRecetas;
        $cart_recetas->removeProduct($insumo->id);
        session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
        return back();
    }


  // escuchar eventos
  protected $listeners = [
    'scan-code'  =>  'BuscarCode',
  ];

  public function BuscarCode($barcode)
  {


    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $record = Product::where('barcode',$barcode)->where('comercio_id', $comercio_id)->where('eliminado',0)->first();

    if($record == null || empty($record))
    {

    $this->emit('scan-notfound','El insumo no está registrado');

    $this->codigo = '';

    }  else {

    $this->selected_id = $record->id;
    $this->name = $record->name;
    $this->barcode = $record->barcode;
    $this->cost = $record->cost;
    $this->stock = $record->stock;
    $this->image = null;
    $this->unidad_medida = unidad_medida::where('tipo_unidad_medida', $record->tipo_unidad_medida)->get();

    $this->emit('show-modal','Show modal!');

    $this->codigo = '';
  }

}

  public function AbrirModal($id)
	{
		$record = Product::find($id);

		$this->selected_id = $record->id;
		$this->name = $record->name;
		$this->barcode = $record->barcode;
		$this->cost = $record->cost;
		$this->price = $record->price;
		$this->stock = $record->stock;
		$this->categoryid = $record->categorias_fabrica_id;
		$this->image = null;

		$this->emit('show-modal','Show modal!');
	}

  public function Edit($id, $cantidad, $unidad_medida)
	{

    $this->cantidad = $cantidad;
    $this->selected_id = $id;
    $this->record = unidad_medida::find($unidad_medida);
    $this->unidad_medida = unidad_medida::where('tipo_unidad_medida', $this->record->tipo_unidad_medida)->get();

    $this->unidad_medida_elegida = $unidad_medida;


		$this->emit('show-modal-editar','Show modal!');
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
    $this->selected_id = 0;

  }

  // guardar receta ---> aca si cada sucursal tiene su propia lista de costos --> tomar el costo de cada sucursal y guardarlo ahi al costo del producto
  public function StoreReceta()
  {
      
    if($this->rinde == "")
	{
		$this->emit('sale-error','DEBE ESPECIFICAR CUANTAS UNIDADES SE PRODUCEN CON ESTA RECETA.');
		return;
	}


    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $cart = new CartRecetas;

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;


    DB::beginTransaction();

    try {

        $items = $cart->getContent();

        foreach ($items as  $item) {

        $unidad_medida_guardar = unidad_medida::find($item['unidad_medida']);

        $receta =  receta::create([
            'insumo_id' => $item['id'],
            'nombre' => $item['name'],
            'cantidad' => $item['qty'],
            'costo_unitario' => $item['cost'],
            'relacion_medida' => $item['relacion'],
            'relacion_cantidades' => $item['relacion_cantidades'],
            'unidad_medida' => $item['unidad_medida'],
            'tipo_unidad_medida' => $unidad_medida_guardar->tipo_unidad_medida,
            'comercio_id' => $comercio_id,
            'product_id' => $this->producto_id,
            'rinde' => $this->rinde,
            'referencia_variacion' => $this->referencia_variacion
          ]);

        
      }


      DB::commit();
      
      // Actualizar el costo de la receta en el producto 

	  $this->CreateOrUpdateCostosRecetas($this->producto_id,$this->referencia_variacion,$comercio_id);
      $this->CreateOrUpdateCostosRecetasListas($this->producto_id,$this->referencia_variacion,$comercio_id);

      $cart->clear();
      $this->emit('sale-ok','Receta registrada con éxito');



    } catch (Exception $e) {
      DB::rollback();
      $this->emit('sale-error', $e->getMessage());
    }

  }

  // actualizar receta
  public function UpdateReceta()
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $cart_recetas = new CartRecetas;

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    if($this->rinde == "")
	{
		$this->emit('sale-error','DEBE ESPECIFICAR CUANTAS UNIDADES SE PRODUCEN CON ESTA RECETA.');
		return;
	}


    DB::beginTransaction();

        try {

        $items_v = receta::where('product_id',$this->producto_id)->where('referencia_variacion',$this->referencia_variacion)->get();

        foreach($items_v as $i) {

            $ib = receta::where('product_id',$this->producto_id)->where('referencia_variacion',$this->referencia_variacion)->where('insumo_id',$i->insumo_id)->first();
            $ib->delete();

        }

          $items = $cart_recetas->getContent();


          foreach ($items as  $item) {

            $exist_r = receta::where('product_id', $this->producto_id)->where('referencia_variacion',$this->referencia_variacion)->where('insumo_id',$item['id'])->first();
            
            $tipo_unidad_medida = unidad_medida::find($item['unidad_medida']);
            
            if($exist_r != null) {
            
            $exist_r->update([
            'cantidad' => $item['qty'],
            'relacion_cantidades' => $item['relacion_cantidades'],
            'costo_unitario' => $item['cost'],
            'relacion_medida' => $item['relacion'],
            'unidad_medida' => $item['unidad_medida'],
            'tipo_unidad_medida' => $tipo_unidad_medida->tipo_unidad_medida,
            'rinde' => $this->rinde,
                ]);
                
            } else {
                
            receta::create([
            'insumo_id' => $item['id'],
            'nombre' => $item['name'],
            'cantidad' => $item['qty'],
            'costo_unitario' => $item['cost'],
            'relacion_medida' => $item['relacion'],
            'unidad_medida' => $item['unidad_medida'],
            'relacion_cantidades' => $item['relacion_cantidades'],
            'comercio_id' => $comercio_id,
            'tipo_unidad_medida' => $tipo_unidad_medida->tipo_unidad_medida,
            'rinde' => $this->rinde,
            'product_id' => $this->producto_id,
            'referencia_variacion' => $this->referencia_variacion
            ]);
            
            }


      }
      
	  $this->CreateOrUpdateCostosRecetas($this->producto_id,$this->referencia_variacion,$comercio_id);
      $this->CreateOrUpdateCostosRecetasListas($this->producto_id,$this->referencia_variacion,$comercio_id);


      DB::commit();

      $this->emit('sale-ok','Receta registrada con éxito');

    } catch (Exception $e) {
      DB::rollback();
      $this->emit('sale-error', $e->getMessage());
    }

  }


  public function resetProduct()
 {
   $this->products_s = [];
 }

  public function selectProduct()
  {
      $this->query_product = '';

      $this->resetProduct();
  }


  public function updatedQueryProduct()
  {
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;



      $this->products_s = 	Product::where('comercio_id', 'like', $comercio_id)->where('es_insumo',1)->where('products.eliminado',0)->where( function($query) {
            $query->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
        })
          ->limit(5)
          ->get()
          ->toArray();



  }




}
