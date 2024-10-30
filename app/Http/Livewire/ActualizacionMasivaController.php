<?php

namespace App\Http\Livewire;


use App\Models\Category;
use App\Models\ayuda;
use App\Models\Product;
use App\Models\User;
use App\Models\marcas;
use App\Models\productos_variaciones_datos;
use App\Models\productos_lista_precios;
use App\Models\sucursales;
use App\Models\lista_precios;
use App\Models\proveedores;
use App\Models\seccionalmacen;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use App\Models\wocommerce;
use Livewire\WithFileUploads;
use Livewire\WithPagination;


use App\Traits\WocommerceTrait;

class ActualizacionMasivaController extends Component
{

	use WithFileUploads;
	use WithPagination;
	use WocommerceTrait;



	public $name, $search,$procesamiento,$paso4, $procesamiento_total, $image,$agregar,$categoria,$productos,$elegir_actualizar,$lista_id,$selected_id, $pageTitle, $listado_productos,$componentName, $wc_category_id, $paso1,$paso2,$paso3,$logo_paso1,$logo_paso2,$logo_paso3;
	private $pagination = 5;
	
    public $progress = 0;

	private $wc_category;
	
	public $numero_actualizar, $redondeo_actualizar;
	
	public $categoria_id,$proveedor_id,$almacen_id;
	
	public $productos_actualizados = [];
	
	public $marca_id;


	public function mount()
	{
		$this->pageTitle = 'Listado';
		$this->procesamiento = 0;
		$this->componentName = 'Categorías';
        $this->agregar = 0;
        $this->paso1 = "display:block;";
        $this->paso2 = "display:none;";
        $this->paso3 = "display:none;";
        $this->paso4 = "display:none;";
        $this->redondeo_actualizar = 1;
        $this->elegir_actualizar = 0;
        $this->logo_paso1 = "background-color: rgba(81,86,190,.2); color: #5156be; border-color: rgba(81,86,190,.2);width:150px !important; border-radius:5px !important;";
        $this->logo_paso2 = "width:150px !important; border-radius:5px !important;";
        $this->logo_paso3 = "width:150px !important; border-radius:5px !important;";    
        
        $this->lista_id = "all";
        $this->categoria_id = 0;
        $this->marca_id = 0;
        $this->almacen_id = 0;
        $this->proveedor_id = 0;



	}

	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}

    protected function redirectLogin()
    {
        return \Redirect::to("login");
    }
    
	public function render()
	{
    
    if (!Auth::check()) {
        // Redirigir al inicio de sesión y retornar una vista vacía
        $this->redirectLogin();
        return view('auth.login');
    }
        
     if(Auth::user()->comercio_id != 1)
        $this->comercio_id = Auth::user()->comercio_id;
        else
        $this->comercio_id = Auth::user()->id;
        
        $this->tipo_usuario = User::find($this->comercio_id);
        $this->sucursal_id = $this->comercio_id;
        		    
        if($this->tipo_usuario->sucursal != 1) {
        
        $this->casa_central_id = $this->comercio_id;
        	
        } else {
        	  
        $this->casa_central = sucursales::where('sucursal_id', $this->comercio_id)->first();
        $this->casa_central_id = $this->casa_central->casa_central_id;
        
        }


        $data = Product::select('products.*','categories.name as nombre_categoria','seccionalmacens.nombre as nombre_almacen','proveedores.nombre as nombre_proveedor')
        ->join('categories','categories.id','products.category_id')
        ->join('seccionalmacens','seccionalmacens.id','products.seccionalmacen_id')
        ->join('proveedores','proveedores.id','products.proveedor_id')
        ->where('products.comercio_id',$this->casa_central_id);
        
        if($this->categoria_id != 0) {
        $data = $data->where('products.category_id',$this->categoria_id);    
        }
        
        if($this->marca_id != 0) {
        $data = $data->where('products.marca_id',$this->marca_id);    
        }
        
        if($this->almacen_id != 0) {
        $data = $data->where('products.seccionalmacen_id',$this->almacen_id);    
        }
        
        
        if($this->proveedor_id != 0) {
        $data = $data->where('products.proveedor_id',$this->proveedor_id);    
        }
        
        $data = $data->where('products.eliminado',0)->get();

		//dd($this->productos);
		
		return view('livewire.actualizacion-masiva.component', [
		    'data' => $data,
		    'productos' => $this->productos,
		    'marcas' => marcas::orderBy('name','asc')->where('eliminado', 0)->where('comercio_id', $this->casa_central_id)->get(),
		    'categorias' => Category::orderBy('name','asc')->where('eliminado', 0)->where('comercio_id', $this->casa_central_id)->get(),
			'almacenes' => seccionalmacen::orderBy('nombre','asc')->where('comercio_id', $this->casa_central_id)->where('eliminado',0)->get(),
			'proveedores' => proveedores::orderBy('nombre','asc')->where('comercio_id', $this->casa_central_id)->where('eliminado',0)->get(),
			'lista_precios' => lista_precios::orderBy('nombre','asc')->where('comercio_id', $this->casa_central_id)->where('eliminado',0)->get(),

		    ])
		->extends('layouts.theme-pos.app')
		->section('content');
	}

	protected $listeners =[
		'Actualizar' => 'Actualizar',
		'progressUpdated' => 'updateProgress'
	];


public function updateProgress($current, $total) {
    $this->progress = ($current / $total) * 100;
}

public function Filtrar(){
    $this->Paso2();
    $this->render();
}

public function Paso1(){
    $this->paso1 = "display:block;";
    $this->paso2 = "display:none;";
    $this->paso3 = "display:none;";
    $this->paso4 = "display:none;";
    
    $this->logo_paso1 = "background-color: rgba(81,86,190,.2); color: #5156be; border-color: rgba(81,86,190,.2);width:150px !important; border-radius:5px !important;";
    $this->logo_paso2 = "width:150px !important; border-radius:5px !important;";
    $this->logo_paso3 = "width:150px !important; border-radius:5px !important;";
    $this->logo_paso4 = "width:150px !important; border-radius:5px !important;";
}

public function Paso2(){
    $this->paso2 = "display:block;";
    $this->paso1 = "display:none;";
    $this->paso3 = "display:none;";
    $this->paso4 = "display:none;";
    
    $this->logo_paso2= "background-color: rgba(81,86,190,.2); color: #5156be; border-color: rgba(81,86,190,.2);width:150px !important; border-radius:5px !important;";
    $this->logo_paso1 = "width:150px !important; border-radius:5px !important;";
    $this->logo_paso3 = "width:150px !important; border-radius:5px !important;";
    $this->logo_paso4 = "width:150px !important; border-radius:5px !important;";
}


public function Paso3(){
    $this->paso3 = "display:block;";
    $this->paso2 = "display:none;";
    $this->paso1 = "display:none;";
    $this->paso4 = "display:none;";
    
    $this->logo_paso3 = "background-color: rgba(81,86,190,.2); color: #5156be; border-color: rgba(81,86,190,.2);width:150px !important; border-radius:5px !important;";
    $this->logo_paso2 = "width:150px !important; border-radius:5px !important;";
    $this->logo_paso1 = "width:150px !important; border-radius:5px !important;";
    $this->logo_paso4 = "width:150px !important; border-radius:5px !important;";
}


public function Paso4(){
    $this->paso4 = "display:block;";
    $this->paso1 = "display:none;";
    $this->paso2 = "display:none;";
    $this->paso3 = "display:none;";
    
    $this->logo_paso3 = "background-color: rgba(81,86,190,.2); color: #5156be; border-color: rgba(81,86,190,.2);width:150px !important; border-radius:5px !important;";
    $this->logo_paso1 = "width:150px !important; border-radius:5px !important;";
    $this->logo_paso2 = "width:150px !important; border-radius:5px !important;";
}

public function Actualizar() {

		$rules  =[
			'numero_actualizar' => 'required|numeric'
		];

		$messages = [
			'numero_actualizar.required' => 'El % a actualizar es requerido',
			'numero_actualizar.numeric' => 'El % a actualizar debe contener solo numeros',
		];

	$this->validate($rules, $messages);

    $this->Paso4();
// aca tenemos un array con todos los productos pero sin ser paginados
        
$this->productos = Product::join('productos_lista_precios','productos_lista_precios.product_id','products.id')
->select('productos_lista_precios.precio_lista','productos_lista_precios.lista_id','productos_lista_precios.id as id_precio_lista','products.*')
->where('products.comercio_id',$this->casa_central_id);

//dd($this->lista_id,$this->categoria_id,$this->almacen_id,$this->proveedor_id);

if($this->lista_id != "all") {
$this->productos = $this->productos->where('productos_lista_precios.lista_id',$this->lista_id);    
}

if($this->categoria_id != 0) {
$this->productos = $this->productos->where('products.category_id',$this->categoria_id);    
}

if($this->marca_id != 0) {
$this->productos = $this->productos->where('products.marca_id',$this->marca_id);    
}
    
        
if($this->almacen_id != 0) {
$this->productos = $this->productos->where('products.seccionalmacen_id',$this->almacen_id);    
}
        
        
if($this->proveedor_id != 0) {
$this->productos = $this->productos->where('products.proveedor_id',$this->proveedor_id);    
}
        
$this->productos = $this->productos->where('products.eliminado',0)->get();

//dd($this->productos);

// no trae bien la lista aca 


// ACTUALIZAR PRECIOS
if($this->elegir_actualizar == 1) {

$this->ActualizarPrecios();
}

// ACTUALIZAR PRECIO INTERNO
if($this->elegir_actualizar == 2) {
$this->ActualizarPrecioInterno();
}

// ACTUALIZAR COSTOS
if($this->elegir_actualizar == 3) {
$this->ActualizarCostos();
}

//$this->emit('msg','Precios actualizados correctamente');



}

public function ActualizarPrecioInterno() {
    $numero_actualizar = 1 + ($this->numero_actualizar / 100);
    $this->procesamiento_total = $this->productos->count();

    $i = 0;
    foreach ($this->productos as $lp) {
        $i++;

        $product = Product::find($lp->id);
        $costo_anterior = $lp->precio_interno;
        $numero = $costo_anterior * $numero_actualizar;

        // Actualizar redondeando
        if ($this->redondeo_actualizar == 1) { 
            $redondeado = ceil($numero);
        }
        if ($this->redondeo_actualizar == 2) { 
            $redondeado = floor($numero);
        }

        // Actualizar producto tipo simple
        if ($lp->producto_tipo == "s") {
            $pl = Product::find($lp->id);
            $pl->update(['precio_interno' => $redondeado]);
        }

        // Actualizar producto tipo variable
        if ($lp->producto_tipo == "v") {
            $pvd = productos_variaciones_datos::where('product_id', $lp->id)->where('eliminado', 0)->get();
            foreach ($pvd as $pv) {
            $costo_anterior = $pv->precio_interno;
            $numero = $costo_anterior * $numero_actualizar;
                // Actualizar redondeando
                if ($this->redondeo_actualizar == 1) { 
                    $redondeado = ceil($numero);
                }
                if ($this->redondeo_actualizar == 2) { 
                    $redondeado = floor($numero);
                }
            $pl->update(['precio_interno' => $redondeado]);
            }
        }

        // Sincronización con WooCommerce
        if ($product->wc_canal == 1) {
            $product->update(['wc_push' => 1]);
            if ($product->producto_tipo == "v") {
                $pvd = productos_variaciones_datos::where('product_id', $product->id)->where('eliminado', 0)->get();
                foreach ($pvd as $pv) {
                    $pv->update(['wc_push' => 1]);
                }
            }
        }

        // Registrar el cambio
        if (!in_array($lp->id, $this->productos_actualizados)) {
        $this->productos_actualizados[] = [
            'id' => $product->id,
            'name' => $product->name,
            'barcode' => $product->barcode,
            'precio_anterior' => $costo_anterior,
            'precio_nuevo' => $redondeado
        ];
        }

        // Emitir evento de progreso
        $this->emit('progressUpdated', $i, $this->procesamiento_total);

        $this->procesamiento = $i;
    }
}

public function ActualizarCostos() {
    $numero_actualizar = 1 + ($this->numero_actualizar / 100);
    $this->procesamiento_total = $this->productos->count();

    $i = 0;
    foreach ($this->productos as $lp) {
        $i++;

        $product = Product::find($lp->id);
        $costo_anterior = $lp->cost;
        $numero = $costo_anterior * $numero_actualizar;

        // Actualizar redondeando
        if ($this->redondeo_actualizar == 1) { 
            $redondeado = ceil($numero);
        }
        if ($this->redondeo_actualizar == 2) { 
            $redondeado = floor($numero);
        }

        // Actualizar producto tipo simple
        if ($lp->producto_tipo == "s") {
            $pl = Product::find($lp->id);
            $pl->update(['cost' => $redondeado]);
        }

        // Actualizar producto tipo variable
        if ($lp->producto_tipo == "v") {
            $pvd = productos_variaciones_datos::where('product_id', $lp->id)->where('eliminado', 0)->get();
            foreach ($pvd as $pv) {
            $costo_anterior = $pv->cost;
            $numero = $costo_anterior * $numero_actualizar;
                // Actualizar redondeando
                if ($this->redondeo_actualizar == 1) { 
                    $redondeado = ceil($numero);
                }
                if ($this->redondeo_actualizar == 2) { 
                    $redondeado = floor($numero);
                }
            $pl->update(['cost' => $redondeado]);
            }
        }

        // Sincronización con WooCommerce
        if ($product->wc_canal == 1) {
            $product->update(['wc_push' => 1]);
            if ($product->producto_tipo == "v") {
                $pvd = productos_variaciones_datos::where('product_id', $product->id)->where('eliminado', 0)->get();
                foreach ($pvd as $pv) {
                    $pv->update(['wc_push' => 1]);
                }
            }
        }

        // Registrar el cambio
        if (!in_array($lp->id, $this->productos_actualizados)) {
        $this->productos_actualizados[] = [
            'id' => $product->id,
            'name' => $product->name,
            'barcode' => $product->barcode,
            'precio_anterior' => $costo_anterior,
            'precio_nuevo' => $redondeado
        ];
        }

        // Emitir evento de progreso
        $this->emit('progressUpdated', $i, $this->procesamiento_total);

        $this->procesamiento = $i;
    }
}

public function ActualizarPrecios() {
    $numero_actualizar = 1 + ($this->numero_actualizar / 100);
    $this->procesamiento_total = $this->productos->count();

    $i = 0;
    foreach ($this->productos as $lp) {
        $i++;

        $product = Product::find($lp->id);
        $precio_anterior = $lp->precio_lista;
        $numero = $precio_anterior * $numero_actualizar;

        // Actualizar redondeando
        if ($this->redondeo_actualizar == 1) { 
            $redondeado = ceil($numero);
        }
        if ($this->redondeo_actualizar == 2) { 
            $redondeado = floor($numero);
        }

        // Actualizar producto tipo simple
        if ($lp->producto_tipo == "s") {
            $pl = productos_lista_precios::find($lp->id_precio_lista);
            $pl->update(['precio_lista' => $redondeado]);
        }

        // Actualizar producto tipo variable
        if ($lp->producto_tipo == "v") {
            $pvd = productos_variaciones_datos::where('product_id', $lp->id)->where('eliminado', 0)->get();
            foreach ($pvd as $pv) {
                $pli = productos_lista_precios::where('product_id', $pv->product_id)->where('referencia_variacion', $pv->referencia_variacion)->where('lista_id', $lp->lista_id)->get();
                foreach ($pli as $pl) {
                    $pl->update(['precio_lista' => $redondeado]);
                }
            }
        }

        // Sincronización con WooCommerce
        if ($product->wc_canal == 1) {
            $product->update(['wc_push' => 1]);
            if ($product->producto_tipo == "v") {
                $pvd = productos_variaciones_datos::where('product_id', $product->id)->where('eliminado', 0)->get();
                foreach ($pvd as $pv) {
                    $pv->update(['wc_push' => 1]);
                }
            }
        }

        // Registrar el cambio
        $this->productos_actualizados[] = [
            'name' => $product->name,
            'barcode' => $product->barcode,
            'precio_anterior' => $precio_anterior,
            'precio_nuevo' => $redondeado
        ];

        // Emitir evento de progreso
        $this->emit('progressUpdated', $i, $this->procesamiento_total);

        $this->procesamiento = $i;
    }
}



public function ActualizarPreciosOld(){
 
$numero_actualizar = 1 + ($this->numero_actualizar/100);

$this->procesamiento_total = $this->productos->count();

$i = 0;
foreach($this->productos as $lp) {

$i++;

$product = Product::find($lp->id);

$numero = $lp->precio_lista * $numero_actualizar;

// Actualizar redondeando para arriba
if($this->redondeo_actualizar == 1) { 
$redondeado = ceil($numero);
}

// Actualizar redondeando para abajo
if($this->redondeo_actualizar == 2) { 
$redondeado = floor($numero);
}

// si el producto tipo es simple

if($lp->producto_tipo == "s") {
$pl = productos_lista_precios::find($lp->id_precio_lista);
$pl->update([
   'precio_lista' => $redondeado
   ]);
}

// si el producto tipo es variable

if($lp->producto_tipo == "v") {
    
$pvd = productos_variaciones_datos::where('product_id',$lp->id)->where('eliminado',0)->get();

foreach($pvd as $pv) {

$pli = productos_lista_precios::where('product_id',$pv->product_id)->where('referencia_variacion',$pv->referencia_variacion)->where('lista_id',$lp->lista_id)->get();

foreach($pli as $pl) {
$pl->update([
   'precio_lista' => $redondeado
   ]);
}
}

}
// Sincronizacion de wocommerce
if($product->wc_canal == 1) {

if($product->producto_tipo == "s") {
    $product->update([
        'wc_push' => 1
        ]);
}

if($product->producto_tipo == "v") {
    $product->update([
        'wc_push' => 1
        ]);
    
    $pvd = productos_variaciones_datos::where('product_id',$product->id)->where('eliminado',0)->get();
    foreach($pvd as $pv) {
        $pv->update([
        'wc_push' => 1
        ]);    
    }
}
}




$this->procesamiento = $i;
} 



    
}




}
