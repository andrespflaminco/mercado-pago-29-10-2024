<?php

namespace App\Http\Livewire;

// Trait

use App\Traits\WocommerceTrait;

use App\Services\CartPromociones;
use App\Models\promos;
use App\Models\promos_productos;
use App\Models\productos_variaciones_datos;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\sucursales;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use App\Models\wocommerce;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Carbon\Carbon;


class PromosController extends Component
{

	use WithFileUploads;
	use WithPagination;
	
	use WocommerceTrait;


    public $precio_promo_combinada,$ProductId;
	public $name, $search, $image, $agregar,$id_check,$limitar_vigencia,$limitar_cantidad,$selected_id, $pageTitle, $componentName, $wc_category_id;
	public $nombre_promo, $cantidad, $product_id,$descuento, $productos,$vigencia_promo;
	private $pagination = 25;
	private $wc_category,$query_product;
	public $products_s;
	public $stock_promo;
	public $seleccionados = [];
	public $productos_seleccionados = [];
    public $producto_activo = [];
    public $productos_variaciones_datos = [];
    public $prod_seleccionados;
    public $vigencia_desde,$vigencia_hasta;

	public function mount()
	{
	    $this->limitar_vigencia = 0;
	    $this->limitar_cantidad = 0;
	    $this->productos_seleccionados = [];
	    $this->estado_filtro = 1;
	    $this->accion_lote = 'Elegir';
		$this->pageTitle = 'Listado';
		$this->componentName = 'Categorías';
		$this->tipo_promo = null;
		$this->precio_promo_combinada = 0;


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
        
        $prod_seleccionados = new CartPromociones;
        $this->prod_seleccionados = $prod_seleccionados->getContent();
        
        //dd($this->productos_seleccionados);
        
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

        //dd($this->casa_central_id);
		if(strlen($this->search) > 0) {
			$data = promos::where('nombre_promo', 'like', '%' . $this->search . '%')
			->where('eliminado',0)
			->where('comercio_id', $this->casa_central_id)
			->get();
		} else {
			$data = promos::where('comercio_id', $this->casa_central_id)
			->where('eliminado',0)
			->orderBy('id','desc')
			->get();
		}
        
        //dd($data);
        
        $this->productos = Product::where("comercio_id",$this->casa_central_id)->where("eliminado",0)->get();
        
		return view('livewire.promos.component', [
		    'promos' => $data,
		    'productos' => $this->productos
		    ])
		->extends('layouts.theme-pos.app')
		->section('content');
	}


    public function ToggleHabilitarPromo($id) {
        $promos = promos::find($id);
        $promos->activo = !$promos->activo;
        $promos->save();
    }

    public function UpdateQuantity($product_id,$referencia_variacion,$cant) {
    
      $cart_promocion = new CartPromociones;
      $items = $cart_promocion->getContent();

      foreach ($items as $i)
   {
          if( ($i['id'] === $product_id) && ($i['referencia_variacion'] === $referencia_variacion)) {

            $cart_promocion->removeProductById($product_id,$referencia_variacion);

            $array = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "variacion" => $i['variacion'],
                "referencia_variacion" => $i['referencia_variacion'],
                "id_promos_productos" => $i['id_promos_productos'],
                "activo" => $i['activo'],
                "cantidad" => $cant,
                "eliminado" => $i['eliminado']
            );

            $cart_promocion->addProduct($array);

        }


   }        
    }    
    public function ToggleHabilitar($id_promo_product) {
    
      $cart_promocion = new CartPromociones;
      $items = $cart_promocion->getContent();

      foreach ($items as $i)
   {
          
          if($i['id_promos_productos'] === $id_promo_product) {
            
            if($i['activo'] == 1){ $activo = 0;} else {$activo = 1;}
            
            $cart_promocion->removeProduct($id_promo_product);

            $array = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "variacion" => $i['variacion'],
                "referencia_variacion" => $i['referencia_variacion'],
                "id_promos_productos" => $i['id_promos_productos'],
                "cantidad" => $i['cantidad'],
                "activo" => $activo,
                "eliminado" => $i['eliminado']
            );

            $cart_promocion->addProduct($array);

        }


   }        
    }
    
    
	public function Edit($id)
	{
	    $cart_promociones = new CartPromociones;
		$cart_promociones->clear();
	    
	    $this->agregar = 1;
		
		$promos = promos::find($id, ['id','nombre_promo','cantidad','porcentaje_descuento','limitar_vigencia','limitar_cantidad','vigencia_desde','vigencia_hasta','tipo_promo','precio_promo']);
		$this->nombre_promo = $promos->nombre_promo;
		$this->cantidad = $promos->cantidad;
		$this->precio_promo_combinada = $promos->precio_promo;
		$this->descuento = $promos->porcentaje_descuento;
		$this->limitar_vigencia = $promos->limitar_vigencia;
		$this->limitar_cantidad = $promos->limitar_cantidad;
		$this->tipo_promo = $promos->tipo_promo;
		
		$this->selected_id = $promos->id;
		
		$vigencia_desde = Carbon::parse($promos->vigencia_desde)->format('Y-m-d');
        $vigencia_hasta = Carbon::parse($promos->vigencia_hasta)->format('Y-m-d');

		$this->vigencia_desde = $vigencia_desde;
		$this->vigencia_hasta = $vigencia_hasta;
		
        $this->productos_seleccionados = $this->GetProductosSeleccionados($this->selected_id);

	     $prod_seleccionados = new CartPromociones;
         $items = $prod_seleccionados->getContent();
         
         foreach($this->productos_seleccionados as $ps){
        
         $pvd = productos_variaciones_datos::where("product_id",$ps->id)->where("referencia_variacion",$ps->referencia_variacion)->first();

         if($pvd != null){$variacion = $pvd->variaciones;} else {$variacion = "0";}
         
         $array = array(
            "id" => $ps->id,
            "name" => $ps->name,
            "variacion" => $variacion,
            "barcode" => $ps->barcode,
            "id_promos_productos" => $ps->id_promos_productos,
            "activo" => $ps->activo,
            "cantidad" => $ps->cantidad,
            "referencia_variacion" => $ps->referencia_variacion,
            "eliminado" => $ps->eliminado
          );
          
          $prod_seleccionados->addProduct($array);             
         }
         
         $items = $prod_seleccionados->getContent();
         //dd($items);
	    
	}
    
    public function GetProductosSeleccionados($selected_id){
		return promos_productos::join('products','products.id','promos_productos.product_id')
		->where('promos_productos.promo_id',$selected_id)
		->select('promos_productos.referencia_variacion','products.id','products.name','products.barcode','promos_productos.id as id_promos_productos','promos_productos.cantidad','promos_productos.activo','promos_productos.eliminado')
		->get();        
    }

    public function Agregar() {
	    $cart_promociones = new CartPromociones;
		$cart_promociones->clear();        
	    $this->productos_seleccionados = [];
        $this->agregar = 1;
    }
    
	public function Store()
	{
	   
	    if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		if($this->limitar_vigencia == true){
		if($this->vigencia_desde == null ||$this->vigencia_hasta == null){$this->emit("msg-error","Chequee las fechas de vigencia"); 
		return;    
		}    
		}
		
		$rules = [
			'nombre_promo' => ['required',Rule::unique('promos')->where('comercio_id',$comercio_id)->where('activo',1)],
		];

		$messages = [
			'nombre_promo.required' => 'Nombre de la promocion es requerido',
			'nombre_promo.min' => 'El nombre de la promocion debe tener al menos 3 caracteres',
			'nombre_promo.unique' => 'El nombre de la promocion ya existe. Elija otro.'
		];

		$this->validate($rules, $messages);

		
        $cart_promocion = new CartPromociones;
        $items = $cart_promocion->getContent();

        $nombre_productos = $items->where('eliminado', 0)->map(function($item) {
            if($item['variacion'] != 0){
            return $item['name'] . '-' . $item['variacion'];
            } else {
            return $item['name'];    
            }
        })->implode(',');
        

        foreach ($items as $item) {
            $existingPromo = promos_productos::join('promos','promos.id','promos_productos.promo_id')
                ->where('product_id', $item['id'])
                ->where('referencia_variacion', $item['referencia_variacion'])
                ->where('promos_productos.eliminado',0)
                ->where('promos.eliminado',0)
                ->first();
        
            if ($existingPromo != null) {
                $this->emit("msg-error","El producto ".$item['name'] . $item['variacion']." ya existe en otra promocion." );
                return;
            }
        }
        
        if($this->tipo_promo == 1){ $cantidades_de_promo = $this->cantidad;} else {$cantidades_de_promo = 1;}
        if($this->tipo_promo == 1){ $porcentajes_de_promo = $this->descuento;} else {$porcentajes_de_promo = 0;}
        
		$promo = promos::create([
			'nombre_promo' => $this->nombre_promo,
			'productos' => $nombre_productos,
			'cantidad' => $cantidades_de_promo,
			'tipo_promo' => $this->tipo_promo,
			'precio_promo' => $this->precio_promo_combinada,
			'porcentaje_descuento' => $porcentajes_de_promo,
			'comercio_id' => $comercio_id,
			'activo' => 1,
			'limitar_vigencia' => $this->limitar_vigencia,
			'limitar_cantidad' => $this->limitar_cantidad,
			'vigencia_desde' => $this->vigencia_desde,
            'vigencia_hasta' => $this->vigencia_hasta,
            
		]);    

        
        foreach($items as $item){
        
        if($this->tipo_promo == 1){ $cantidad = $this->cantidad;} else {$cantidad = $item['cantidad'];}
        if($this->tipo_promo == 1){ $porcentajes_de_promo = $this->descuento;} else {$porcentajes_de_promo = 0;}
        
        if($item['eliminado'] == 0) {
		$promos = promos_productos::create([
			'nombre_promo' => $this->nombre_promo,
			'product_id' => $item['id'],
			'promo_id' => $promo->id,
			'referencia_variacion' => $item['referencia_variacion'],
			'cantidad' => $cantidad,
			'porcentaje_descuento' => $porcentajes_de_promo,
			'comercio_id' => $comercio_id,
			'activo' => 1,
			'eliminado' => 0
		]);   
        }
        }

        
		$this->resetUI();
		$this->emit('category-added','Promocion Registrada');

	}
    
    public function QuitarProducto($product_id,$referencia_variacion){
        
    //dd($referencia_variacion);
    
    $cart_promocion = new CartPromociones;
    $items = $cart_promocion->getContent();
    
    //dd($items);
      foreach ($items as $i)
    {
          if( ($i['id'] == $product_id) && ($i['referencia_variacion'] == $referencia_variacion) ) {
     
            $cart_promocion->removeProductById($product_id,$referencia_variacion);

            $array = array(
                "id" => $i['id'],
                "referencia_variacion" => $i['referencia_variacion'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "id_promos_productos" => $i['id_promos_productos'],
                "cantidad" => $i['cantidad'],
                "activo" => 0,
                "eliminado" => 1
            );

            $cart_promocion->addProduct($array);

        }
    }      
        
    }

	public function Update()
	{

	    if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$promos = promos::find($this->selected_id);
        
        $cart_promocion = new CartPromociones;
        $items = $cart_promocion->getContent();
      
		//$nombre_productos = $items->where('eliminado',0)->pluck('name')->implode(',');
        
        $nombre_productos = $items->where('eliminado', 0)->map(function($item) {
            if($item['variacion'] != 0){
            return $item['name'] . '-' . $item['variacion'];
            } else {
            return $item['name'];    
            }
        })->implode(',');		
				
		if($this->vigencia_promo == true){
		if($this->vigencia_desde == null ||$this->vigencia_hasta == null){$this->emit("msg-error","Chequee las fechas de vigencia"); 
		return;    
		}    
		}
		
		
		$promos->update([
			'nombre_promo' => $this->nombre_promo,
			'porcentaje_descuento' => $this->descuento,
			'cantidad' => $this->cantidad,
			'productos' => $nombre_productos,
			'limitar_vigencia' => $this->limitar_vigencia,
			'limitar_cantidad' => $this->limitar_cantidad,
			'vigencia_desde' => $this->vigencia_desde,
            'vigencia_hasta' => $this->vigencia_hasta,
		]);

        
        foreach($items as $ps){
        $promos_productos = promos_productos::find($ps['id_promos_productos']);
        
        if($promos_productos == null){
        promos_productos::create([
			'nombre_promo' => $this->nombre_promo,
			'product_id' => $ps['id'],
			'promo_id' => $this->selected_id,
			'referencia_variacion' => $ps['referencia_variacion'],
			'cantidad' => $this->cantidad,
			'porcentaje_descuento' => $this->descuento,
			'comercio_id' => $comercio_id,
			'activo' => $ps['activo']
			]);
        } else {
         if($this->tipo_promo == 1){$cantidad = $this->cantidad;} else {$cantidad = $ps['cantidad'];}
	     $promos_productos->nombre_promo = $this->nombre_promo;
		 $promos_productos->porcentaje_descuento = $this->descuento;
		 $promos_productos->cantidad = $cantidad;
	     $promos_productos->activo = $ps['activo'];
	     $promos_productos->eliminado = $ps['eliminado'];
	     $promos_productos->save();
	    }
	            
        }
        
		$this->resetUI();
		$this->emit('category-updated', 'Promocion Actualizada');

	}


	public function resetUI()
	{
	    
	    $this->productos_seleccionados = [];
		$this->nombre_promo ='';
		$this->cantidad ='';
		$this->descuento ='';
		$this->image = null;
		$this->search ='';
		$this->selected_id =0;
		$this->agregar = 0;
		$this->tipo_promo = null;
		$this->precio_promo = null;
	}

	protected $listeners =[
		'Seleccionados' => 'ProductosSeleccionados',
		'deleteRow' => 'Destroy',
		'Restaurar' => 'Restaurar',
        'accion-lote' => 'AccionEnLote',
        'GuardarVariaciones' => 'GuardarVariaciones'
    ];


        public function ProductosSeleccionados($value){
        
        $ps = Product::find($value);
        
        if($ps->producto_tipo == "v"){
        $this->productos_variaciones_datos = productos_variaciones_datos::where("product_id",$ps->id)->where("eliminado",0)->get();
        //dd($this->productos_variaciones_datos);
        $this->ProductId = $ps->id;
        $this->emit("variaciones","");
        return;
        }
        
        $fecha_actual = Carbon::now()->toDateString(); // Obtener la fecha actual en formato 'Y-m-d'

        $exists = promos_productos::join('promos','promos.id','promos_productos.promo_id')->where('promos_productos.product_id', $value)
            ->where('promos_productos.activo', 1)
            ->where('promos.eliminado', 0)
            ->whereDate('promos.vigencia_desde', '<=', $fecha_actual) // Verificar que la fecha actual sea posterior o igual a vigencia_desde
            ->whereDate('promos.vigencia_hasta', '>=', $fecha_actual) // Verificar que la fecha actual sea anterior o igual a vigencia_hasta
            ->first();
		if($exists != null){
        $this->emit("msg-error","No puede incluir el producto porque esta agregado en otra promocion");
            return;
		}
		
        $cart_promocion = new CartPromociones;
        $items = $cart_promocion->getContent();
        
        $existingProduct = $items->where('id', $ps->id)->where('referencia_variacion',0)->where('eliminado',0)->first();
        
        if(!$existingProduct){
         $array = array(
            "id" => $ps->id,
            "name" => $ps->name,
            "barcode" => $ps->barcode,
            "variacion" => null,
            "referencia_variacion" => "0",
            "id_promos_productos" => 0,
            "cantidad" => 1,
            "activo" => 1,
            "eliminado" => 0
          );

          $cart_promocion->addProduct($array);             
        }
    }

        public function GuardarVariaciones($value){
            foreach($value as $v){
             $this->ProductoVariableSeleccionado($v);   
            }
            
            
        $this->emit("variaciones-hide","");
        }
        
        public function ProductoVariableSeleccionado($value){
        
        $datos = explode("|-|",$value);
        $product_id = $datos[0];
        $referencia_variacion = $datos[1];
        
        $ps = Product::find($product_id);
        
        $pvd = productos_variaciones_datos::where('product_id', $product_id)->where('referencia_variacion',$referencia_variacion)->where('eliminado',0)->first();
        
        
        $cart_promocion = new CartPromociones;
        $items = $cart_promocion->getContent();
        
        $existingProduct = $items->where('id', $ps->id)->where('referencia_variacion',$referencia_variacion)->where('eliminado',0)->first();
        
        if(!$existingProduct){
         $array = array(
            "id" => $ps->id,
            "name" => $ps->name,
            "variacion" => $pvd->variaciones,
            "barcode" => $ps->barcode,
            "referencia_variacion" => $referencia_variacion,
            "id_promos_productos" => 0,
            "cantidad" => 1,
            "activo" => 1,
            "eliminado" => 0
          );

          $cart_promocion->addProduct($array);             
        }
        
    }
    
    
	public function Destroy(promos $promo)
	{
		$promo->eliminado = 1;
		$promo->activo = 0;
		$promo->save();

		$this->resetUI();
		$this->emit('category-updated', 'Promo Eliminada');

	}


	public function Filtro($estado)
	{
	   $this->estado_filtro = $estado;
	   
	}
	
	    // Eliminar en lote 
    
		
	public function Restaurar(promos $promos)
	{
		$promos->update([
			'activo' => 1
		]);

		$this->resetUI();
		$this->emit('category-updated', 'Promocion Restaurada');
	}
	
	
	public function AccionEnLote($ids, $id_accion)
    {
    
    if($id_accion == 1) {
        $estado = 1;
        $msg = 'RESTAURADOS';
    } else {
        $estado = 0;
        $msg = 'ELIMINADOS';
    }
    
    $promos_checked = promos::select('promos.id','promos.comercio_id')->whereIn('promos.id',$ids)->get();

    $this->id_check = [];
    
    if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

    foreach($promos_checked as $pc) {
    
    $pc->activo = $estado;
    $pc->save();

    }
    
    
    $this->id_check = [];
    $this->accion_lote = 'Elegir';
    
     $this->emit('global-msg',"PROMOCION ".$msg);
    
    }
    
    public function Sincronizar($categoria_id) {
        
        $this->FindOrCreateCategoryByName($categoria_id);
    }


	      public function updatedQueryProduct()
	      {

	          $this->products_s = 	Product::where('comercio_id', 'like', $this->casa_central_id)->where( function($query) {
							    $query->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
							})
							->where('eliminado',0)
				->limit(25)
	              ->get()
	              ->toArray();


	      }


}
