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
use App\Models\receta;
use App\Models\Category;
use App\Models\insumo;
use App\Models\metodo_pago;
use App\Models\produccion_detalle;
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

use App\Models\produccion_detalles_insumos;

use DB;

class MostrarRecetaProduccionController extends Component
{
  use WithPagination;
  use WithFileUploads;

  public $name,$barcode,$cost,$receta_id, $price,$pago, $proveedor_id, $caja, $rinde,  $stock,$alerts,$categoryid, $codigo, $monto_total, $search, $image, $selected_id, $pageTitle,$componentName,$comercio_id,$data_Cart, $observaciones, $unidad_medida, $unidad_medida_elegida, $query_product, $products_s , $cart_recetas, $cart, $deuda,$metodo_pago_elegido, $product,$total, $itemsQuantity, $cantidad, $carrito, $qty, $tipo_unidad_medida_producto, $product_id;
  private $pagination = 25;

  public function mount($id)
  {
    $this->pageTitle = 'Listado';
    $this->cantidad = 1;
    $this->componentName = 'Productos';
    $this->metodo_pago_elegido = 'Elegir';
    $this->categoryid = 'Elegir';
    $this->unidad_medida = [];


    $this->id = $id;
    
    $this->receta_id = $id;

    $cart_recetas = new CartRecetas;
    $cart_recetas->clear();
  }

  public function render()
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    
    
    $produccion_detalles_insumos = produccion_detalle::find($this->id);

    // dd($produccion_detalles_insumos);
    
    $products = produccion_detalles_insumos::join('unidad_medidas','unidad_medidas.id','produccion_detalles_insumos.unidad_medida_consumida')
    ->where('produccion_detalles_insumos.produccion_detalles_id',$this->id)
    ->select('produccion_detalles_insumos.*','unidad_medidas.nombre as nombre_unidad_medida')
    ->get();    
    
    $productos = Product::leftjoin('recetas as r','r.product_id','products.id')
    ->leftjoin('insumos','insumos.id','r.insumo_id')
    ->join('unidad_medidas','unidad_medidas.id','r.unidad_medida')
    ->select('insumos.id','r.rinde','unidad_medidas.nombre as nombre_unidad_medida','insumos.name','insumos.barcode','insumos.cost','r.unidad_medida','r.cantidad','r.costo_unitario','r.cantidad','r.relacion_medida','insumos.relacion_unidad_medida','r.product_id')
    ->where('products.id', 'like', $produccion_detalles_insumos->producto_id)
    ->where('r.referencia_variacion', 'like', $produccion_detalles_insumos->referencia_variacion)
    ->where('products.eliminado', 'like', 0)
    ->where('r.eliminado', 'like', 0)
    ->get();
    
   // dd($products);
   
    $suma = $productos->sum(function($item){
              return $item->cost*$item->relacion_medida*$item->cantidad;
    });
    
    $rinde = $productos->sum(function($item){
              return $item->rinde;
    });
    
    // Obtener longitud
    $cantidadDeElementos = count($products);
    // Dividir, y listo
    $promedio = $rinde / $cantidadDeElementos;
    
    $this->rinde = $promedio;

    return view('livewire.mostrar_receta_produccion.component',[
      'data' => $products,
      'rinde' => $promedio,
      'receta_id' => $this->receta_id,
      'produccion_detalles_insumos' => $produccion_detalles_insumos,
      'suma' => $suma
    ])
    ->extends('layouts.theme-pos.app')
    ->section('content');
  }




  public function showCart() {
      return view('livewire.ventas-fabrica.cart')
      ->extends('layouts.theme.app')
      ->section('content');
  }






  public function Agregar() {

    $cart_recetas = new CartRecetas;
    $items = $cart_recetas->getContent();

  $product = insumo::find($this->selected_id);

   $this->relacion_producto_base = $product->relacion_unidad_medida;

     /////////          UNIDAD DE MEDIDA ELEGIDO     ///////////


       $this->unidad_medida_selected = unidad_medida::find($this->unidad_medida_elegida);

       $this->unidad_medida_elegida =   $this->unidad_medida_elegida;
       $this->tipo_unidad_medida_elegida =   $this->unidad_medida_selected->tipo_unidad_medida;

       $this->unidad_base_elegida = tipo_unidad_medida::where('id', $this->unidad_medida_selected->tipo_unidad_medida)->select('unidad_base')->first();

       $this->relacion_unidad_base_elegida = unidad_medida_relacion::where('tipo_unidad_medida', $this->tipo_unidad_medida_elegida)->where('unidad_medida',  $this->unidad_medida_elegida)->first();

      $this->relacion_elegida_base =  $this->relacion_unidad_base_elegida->relacion;

      //////////////////////////////////////////////////////////////////

    $this->costo_unitario_insumo = $product->cost/$product->cantidad;


    $this->relacion = $this->relacion_elegida_base*$this->relacion_producto_base;


    $this->cost = $this->costo_unitario_insumo ;

     /////////////////////////////////////////////////////////////


 if ($items->contains('id', $this->selected_id)) {

   $cart_recetas = new CartRecetas;
   $items = $cart_recetas->getContent();

   $product = insumo::find($this->selected_id);


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
             "relacion" => $this->relacion
         );

         $cart_recetas->addProduct($product);

     }
}

    $this->resetUI();

    $this->emit('product-added','Producto agregado');

   return back();

} else {

      $cart_recetas = new CartRecetas;


         $this->unidad_medida_selected = unidad_medida::find($this->unidad_medida_elegida);

      $product = array(
          "id" => $this->selected_id,
          "barcode" => $this->barcode,
          "name" => $this->name,
          "cost" => $this->cost,
          "qty" => $this->cantidad,
          "unidad_medida" => $this->unidad_medida_selected->id,
          "nombre_unidad_medida" => $this->unidad_medida_selected->nombre,
          "relacion" => $this->relacion
      );

      $cart_recetas->addProduct($product);

      $this->resetUI();

      $this->emit('product-added','Insumo agregado');

  }



}

    public function removeProductFromCart(insumo $insumo) {
        $cart_recetas = new CartRecetas;
        $cart_recetas->removeProduct($insumo->id);
        session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
        return back();
    }


  public function Actualizar(insumo $insumo) {

      $cart_recetas = new CartRecetas;
      $items = $cart_recetas->getContent();
      $this->unidad_medida_selected = unidad_medida::find($this->unidad_medida_elegida);

      foreach ($items as $i)
   {
          if($i['id'] === $insumo['id']) {


            $cart_recetas->removeProduct($i['id']);

            $insumo = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "cost" => $i['cost'],
                "qty" => $this->cantidad,
                "unidad_medida" => $this->unidad_medida_selected->id,
                "nombre_unidad_medida" => $this->unidad_medida_selected->nombre,
                "relacion" => $i['relacion']
            );

            $cart_recetas->addProduct($insumo);

        }
   }

   $this->resetUI();

   $this->emit('product-added','Insumo actualizado');
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

    $record = insumo::where('barcode',$barcode)->where('comercio_id', $comercio_id)->first();

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

  public function MontoPago()
  {


    $this->deuda = $this->monto_total - $this->pago;

    $this->emit('show-modal2','Show modal!');
  }

  public function orders()
  {
      $orders = auth()->user()->processedOrders();
      $suma = 0;
      return view('products.orders', compact('orders', 'suma'));
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

  // guardar venta
  public function saveSale()
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
            'comercio_id' => $comercio_id,
            'tipo_unidad_medida' => $tipo_unidad_medida->tipo_unidad_medida,
            'rinde' => $this->rinde,
            'product_id' => $this->producto_id,
            'referencia_variacion' => $this->referencia_variacion
            ]);
            
            }


      }


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



      $this->products_s = 	insumo::where('comercio_id', 'like', $comercio_id)->where( function($query) {
            $query->where('eliminado',0)->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
        })
          ->limit(5)
          ->get()
          ->toArray();



  }




}
