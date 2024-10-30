<?php

namespace App\Http\Livewire;


use App\Http\Livewire\Scaner;
use App\Models\Category;
use App\Models\User;
use App\Models\insumo;
use App\Models\tipo_unidad_medida;
use App\Models\unidad_medida;
use App\Models\insumos_stock_sucursales;
use App\Models\unidad_medida_relacion;
use Illuminate\Validation\Rule;
use App\Models\proveedores;
use App\Models\Product;
use App\Models\sucursales;
use App\Models\productos_variaciones_datos;
use App\Models\historico_stock;
use Livewire\Component;
use App\Models\seccionalmacen;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Traits\CartTrait;
use App\Models\receta;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Traits\ProduccionTrait;

class InsumosController extends Scaner //Component
{

	use WithPagination;
	use WithFileUploads;
	use CartTrait;
	use ProduccionTrait;


	public $name,$barcode,$cost,$price,$stock, $alerts,$categoryid,$search,$image,$selected_id,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $inv_ideal, $proveedor, $cod_proveedor, $proveedor_elegido, $ecommerce_canal, $mostrador_canal, $name_almacen, $descripcion, $image_categoria, $name_categoria, $cantidad, $tipo_unidad_medida, $unidad_medida;
	public $id_almacen;
	public $id_categoria;
	public $es_sucursal;
	public $id_proveedor;
	private $pagination = 25;
	public $unidad_medida_select;

	public $SelectedProducts = [];
	public $selectedAll = FALSE;

	public $sortColumn = "name";
  public $sortDirection = "asc";
  
  public $id_check,$accion_lote; // 25-6-2024


	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


    public function Agregar(){
        $this->sucursales = $this->getSucursal($this->casa_central_id);

        // Inicializa todos los stocks a 0
        foreach ($this->sucursales as $sucursal) {
            $this->stock[$sucursal->sucursal_id] = 0;
        }

        if (Auth::user()->sucursal != 1) {
            $this->stock[auth()->user()->id] = 0;
        }
        
        $this->agregar = 1;
    }
    
    
	public function mount()
	{
	    $this->forma_edit = 0;
	    $this->agregar = 0;
	    $this->estado_filtro = 0;
		$this->metodo_pago = session('MetodoPago');
		$this->pageTitle = 'Listado';
		$this->proveedor = '1';
		$this->tipo_unidad_medida = 'Elegir';
		$this->unidad_medida = 'Elegir';
		$this->componentName = 'INSUMOS';
		$this->categoryid = 'Elegir';
		$this->almacen = 'Elegir';
		$this->stock_descubierto = 'Elegir';
		$this->OrderNombre = "ASC";
		$this->OrderBarcode = "ASC";


	}


	public function sort($column)
	{
			$this->sortColumn = $column;
			$this->sortDirection = $this->sortDirection == 'asc' ? 'desc' : 'asc';
	}


	public function ModalCategoria($value)
	{
		if($value == 'AGREGAR') {

		$this->emit('modal-categoria-show', '');

		}

	}

	public function StoreCategoria()
	{
		$rules = [
			'name_categoria' => 'required|min:3'
		];

		$messages = [
			'name_categoria.required' => 'Nombre de la categoría es requerido',
			'name_categoria.min' => 'El nombre de la categoría debe tener al menos 3 caracteres'
		];

		$this->validate($rules, $messages);

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$category = Category::create([
			'name' => $this->name_categoria,
			'comercio_id' => $comercio_id
		]);


		$customFileName;
		if($this->image_categoria)
		{
			$customFileName = uniqid() . '_.' . $this->image_categoria->extension();
			$this->image_categoria->storeAs('public/categories', $customFileName);
			$category->image_categoria = $customFileName;
			$category->save();
		}

		$this->categoryid = $category->id;

		$this->resetUICategoria();
		$this->emit('category-added','Categoría agregada');
			$this->emit('modal-show','Show modal');

	}


    protected function redirectLogin()
    {
        return \Redirect::to("login");
    }
	public function renderOld()
	{
   
    if(!Auth::check()) {
            // Redirigir al inicio de sesión y retornar una vista vacía
            $this->redirectLogin();
            return view('auth.login');
        }
 
    $comercio_id = Auth::user()->comercio_id != 1 ? Auth::user()->comercio_id : Auth::user()->id;
    $casa_central_id = Auth::user()->casa_central_user_id;
    $user = User::find(Auth::user()->casa_central_user_id);
    $casa_central_nombre = $user->name;
        
$array_casa_central = (object) [
    'sucursal_id' => $casa_central_id,
    'name' => $casa_central_nombre,
];
    
    $this->comercio_id = $comercio_id;
    $this->casa_central_id = $casa_central_id;
    $this->es_sucursal = Auth::user()->sucursal;
    $this->sucursales = $this->getSucursal($casa_central_id);
    
    $this->sucursales_con_central = $this->sucursales;
    
    // Convertir $array_casa_central a una colección y agregarlo a la colección de sucursales
    $collection_casa_central = collect([$array_casa_central]);
    $this->sucursales_con_central = $collection_casa_central->merge($this->sucursales_con_central);

    
    $insumos = Insumo::select('insumos.*', 'tipo_unidad_medidas.nombre as tipo_unidad_medida', 'unidad_medidas.nombre as unidad_medida')
        ->join('tipo_unidad_medidas', 'tipo_unidad_medidas.id', 'insumos.tipo_unidad_medida')
        ->join('unidad_medidas', 'unidad_medidas.id', 'insumos.unidad_medida')
        ->where('insumos.comercio_id', 'like', $casa_central_id)
        ->where('insumos.eliminado', $this->estado_filtro); // 25-6-2024

    if(strlen($this->search) > 0) {
        $insumos = $insumos->where(function($query) {
            $query->where('insumos.name', 'like', '%' . $this->search . '%')
                ->orWhere('insumos.barcode', 'like', $this->search . '%');
        });
    }

    if($this->id_proveedor) {
        $insumos = $insumos->where('insumos.proveedor_id', $this->id_proveedor);
    }

    $insumos = $insumos->orderBy($this->sortColumn, $this->sortDirection)
        ->paginate($this->pagination);

    // Obtención de unidad de medida
    $this->unidad_medida_select = unidad_medida::select('*');

    if($this->tipo_unidad_medida != "Elegir") {
        $this->unidad_medida_select = $this->unidad_medida_select->where('tipo_unidad_medida', $this->tipo_unidad_medida);
    }

    $this->unidad_medida_select = $this->unidad_medida_select->get();

    // Obtener el stock de insumos por sucursal
    $insumosStockSucursales = insumos_stock_sucursales::where('comercio_id',$casa_central_id)->get();
    
    // Agregar información de stock a cada insumo
    $insumos = $insumos->map(function($insumo) use ($insumosStockSucursales) {
        foreach ($insumosStockSucursales as $stock) {
            if ($stock->insumo_id == $insumo->id) {
                $insumo->{'stock_' . $stock->sucursal_id} = $stock->stock;
            }
        }
        return $insumo;
    });
    
    
			return view('livewire.insumos.component', [
                'data' => $insumos,
                'sucursales' => $this->sucursales,
                'sucursales_con_central' => $this->sucursales_con_central,
				'prov' => proveedores::orderBy('nombre','asc')->where('comercio_id', 'like', $comercio_id)->get(),
				'tipo_unidad_medida_select' => tipo_unidad_medida::orderBy('nombre','asc')->get(),
				'unidad_medida_select' => $this->unidad_medida_select
			])
			->extends('layouts.theme-pos.app')
			->section('content');

}
public function render()
{
    if (!Auth::check()) {
        // Redirigir al inicio de sesión y retornar una vista vacía
        $this->redirectLogin();
        return view('auth.login');
    }

    $comercio_id = Auth::user()->comercio_id != 1 ? Auth::user()->comercio_id : Auth::user()->id;
    $casa_central_id = Auth::user()->casa_central_user_id;
    $user = User::find(Auth::user()->casa_central_user_id);
    $casa_central_nombre = $user->name;

    $array_casa_central = (object) [
        'sucursal_id' => $casa_central_id,
        'name' => $casa_central_nombre,
    ];

    $this->comercio_id = $comercio_id;
    $this->casa_central_id = $casa_central_id;
    $this->es_sucursal = Auth::user()->sucursal;
    $this->sucursales = $this->getSucursal($casa_central_id);

    $this->sucursales_con_central = $this->sucursales;

    // Convertir $array_casa_central a una colección y agregarlo a la colección de sucursales
    $collection_casa_central = collect([$array_casa_central]);
    $this->sucursales_con_central = $collection_casa_central->merge($this->sucursales_con_central);

    $insumosQuery = Insumo::select('insumos.*', 'tipo_unidad_medidas.nombre as tipo_unidad_medida', 'unidad_medidas.nombre as unidad_medida')
        ->join('tipo_unidad_medidas', 'tipo_unidad_medidas.id', 'insumos.tipo_unidad_medida')
        ->join('unidad_medidas', 'unidad_medidas.id', 'insumos.unidad_medida')
        ->where('insumos.comercio_id', 'like', $casa_central_id)
        ->where('insumos.eliminado', $this->estado_filtro); // 25-6-2024

    if (strlen($this->search) > 0) {
        $insumosQuery = $insumosQuery->where(function($query) {
            $query->where('insumos.name', 'like', '%' . $this->search . '%')
                ->orWhere('insumos.barcode', 'like', $this->search . '%');
        });
    }

    if ($this->id_proveedor) {
        $insumosQuery = $insumosQuery->where('insumos.proveedor_id', $this->id_proveedor);
    }

    $insumos = $insumosQuery->orderBy($this->sortColumn, $this->sortDirection)
        ->paginate($this->pagination);

    // Obtención de unidad de medida
    $this->unidad_medida_select = unidad_medida::select('*');

    if ($this->tipo_unidad_medida != "Elegir") {
        $this->unidad_medida_select = $this->unidad_medida_select->where('tipo_unidad_medida', $this->tipo_unidad_medida);
    }

    $this->unidad_medida_select = $this->unidad_medida_select->get();

    // Obtener el stock de insumos por sucursal
    $insumosStockSucursales = insumos_stock_sucursales::where('comercio_id', $casa_central_id)->get();

    // Agregar información de stock a cada insumo
    $insumos->getCollection()->transform(function ($insumo) use ($insumosStockSucursales) {
        foreach ($insumosStockSucursales as $stock) {
            if ($stock->insumo_id == $insumo->id) {
                $insumo->{'stock_' . $stock->sucursal_id} = $stock->stock;
            }
        }
        return $insumo;
    });

    return view('livewire.insumos.component', [
        'data' => $insumos,
        'sucursales' => $this->sucursales,
        'sucursales_con_central' => $this->sucursales_con_central,
        'prov' => proveedores::orderBy('nombre', 'asc')->where('comercio_id', 'like', $comercio_id)->get(),
        'tipo_unidad_medida_select' => tipo_unidad_medida::orderBy('nombre', 'asc')->get(),
        'unidad_medida_select' => $this->unidad_medida_select
    ])->extends('layouts.theme-pos.app')
      ->section('content');
}

	public function Store()
	{

		$rules  =[
			'name' => 'required|min:3',
			'barcode' => ['required',Rule::unique('insumos')->where('eliminado',0)->where('comercio_id',$this->comercio_id)],
			'cost' => 'required',
			'stock' => 'required',
			'proveedor' => 'required',
			'alerts' => 'required',
			'cantidad' => 'required',
			'unidad_medida' => 'required|not_in:Elegir',
			'tipo_unidad_medida' => 'required|not_in:Elegir'
		];

		$messages = [
			'name.required' => 'Nombre del producto requerido',
			'name.min' => 'El nombre debe tener al menos 3 caracteres',
			'name.required' => 'El codigo del producto requerido',
			'barcode.unique' => 'El codigo del producto ya esta en uso',
			'barcode.min' => 'El codigo debe tener al menos 3 caracteres',
			'cost.required' => 'El costo es requerido',
			'proveedor.required' => 'El proveedor es un campo requerido',
			'stock.required' => 'Ingresa las existencias',
			'alerts.required' => 'Falta el valor para las alertas',
			'unidad_medida.not_in' => 'Ingresa la unidad de medida',
			'tipo_unidad_medida.not_in' => 'Ingresa la unidad de medida'
		];

		$this->validate($rules, $messages);

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$this->tipo_unidad_medida =  unidad_medida::find($this->unidad_medida);

		 $this->unidad_medida_producto =  $this->unidad_medida;
		 $this->tipo_unidad_medida_producto =  $this->tipo_unidad_medida->tipo_unidad_medida;

		 $this->unidad_base = tipo_unidad_medida::where('id', $this->tipo_unidad_medida_producto)->select('unidad_base')->first();

		 $this->relacion_unidad_base = unidad_medida_relacion::where('tipo_unidad_medida', $this->tipo_unidad_medida_producto)->where('unidad_medida',  $this->unidad_medida_producto)->first();

		 $this->relacion_producto_base = 1/$this->relacion_unidad_base->relacion;


			 /////////////////////////////////////////////////////////////


		$insumos = insumo::create([
			'name' => $this->name,
			'cost' => $this->cost,
			'barcode' => $this->barcode,
			'stock' => 0,
			'alerts' => $this->alerts,
			'cantidad' => $this->cantidad,
			'proveedor_id' => $this->proveedor,
			'unidad_medida' => $this->unidad_medida,
			'tipo_unidad_medida' =>  $this->tipo_unidad_medida->tipo_unidad_medida,
			'comercio_id' => $this->casa_central_id,
			'relacion_unidad_medida' => $this->relacion_producto_base
		]);

        $this->UpdateOrCreateStock($insumos,$this->casa_central_id);
        
        
		$this->resetUI();
		$this->emit('product-added', 'Insumo Registrado');


	}


	public function Edit(insumo $insumos)
	{
	    
	    $this->forma_edit = 0;
		$this->selected_id = $insumos->id;
		$this->name = $insumos->name;
		$this->barcode = $insumos->barcode;
		$this->cost = $insumos->cost;
		$this->alerts = $insumos->alerts;
		$this->proveedor = $insumos->proveedor_id;
		$this->tipo_unidad_medida = $insumos->tipo_unidad_medida;
		$this->unidad_medida = $insumos->unidad_medida;
		$this->cantidad = $insumos->cantidad;

        $insumos_stock_sucursales = insumos_stock_sucursales::where('insumo_id',$insumos->id)->get();
        
        
        //dd($insumos_stock_sucursales);
        $this->stock = [];
        
        foreach ($insumos_stock_sucursales as $valor) {
            $this->stock[$valor->sucursal_id] = $valor->stock;
            }
            
        $this->agregar = 1;

	}

	public function Update()
	{
		$rules  =[
			'name' => "required|min:3,name,{$this->selected_id}",
			'cost' => 'required',
			'barcode' => ['required',Rule::unique('insumos')->ignore($this->selected_id)->where('eliminado',0)->where('comercio_id',$this->comercio_id)],
			'stock' => 'required',
			'alerts' => 'required',
			'proveedor' => 'required',
			'cantidad' => 'required',
			'unidad_medida' => 'required|not_in:Elegir',
			'tipo_unidad_medida' => 'required|not_in:Elegir',

		];

		$messages = [
			'name.required' => 'Nombre del producto requerido',
			'name.min' => 'El nombre debe tener al menos 3 caracteres',
			'name.required' => 'El codigo del producto requerido',
			'barcode.unique' => 'El codigo del producto ya esta en uso',
			'barcode.min' => 'El codigo debe tener al menos 3 caracteres',
			'cost.required' => 'El costo es requerido',
			'proveedor.required' => 'El proveedor es un campo requerido',
			'stock.required' => 'Ingresa las existencias',
			'alerts.required' => 'Falta el valor para las alertas',
			'unidad_medida.not_in' => 'Ingresa la unidad de medida',
			'tipo_unidad_medida.not_in' => 'Ingresa el tipo de unidad de medida'
		];


		$this->validate($rules, $messages);

		$insumos = insumo::find($this->selected_id);

		$usuario_id = Auth::user()->id;

		$this->tipo_unidad_medida =  unidad_medida::find($this->unidad_medida);

		$this->unidad_medida_producto =  $insumos->unidad_medida;
		$this->tipo_unidad_medida_producto =  $insumos->tipo_unidad_medida;

		$this->unidad_base = tipo_unidad_medida::where('id', $this->tipo_unidad_medida_producto)->select('unidad_base')->first();

		$this->relacion_unidad_base = unidad_medida_relacion::where('tipo_unidad_medida', $this->tipo_unidad_medida_producto)->where('unidad_medida',  $this->unidad_medida_producto)->first();
        
		$this->relacion_producto_base = 1/($this->relacion_unidad_base->relacion);



		$insumos->update([
			'name' => $this->name,
			'cost' => $this->cost,
			'barcode' => $this->barcode,
			'stock' => 0,
			'alerts' => $this->alerts,
			'proveedor_id' => $this->proveedor,
			'cantidad' => $this->cantidad,
			'unidad_medida' => $this->unidad_medida,
			'tipo_unidad_medida' =>  $this->tipo_unidad_medida->tipo_unidad_medida,
			'relacion_unidad_medida' => $this->relacion_producto_base
		]);

		$this->costo_unitario = $insumos->cost/$insumos->cantidad;
		
        $this->UpdateOrCreateStock($insumos,$this->casa_central_id);
        
        $this->UpdateRecetas($insumos->id);
        
		// $costo_receta = $receta->cantidad*$receta->costo_unitario*$receta->relacion_medida;
           

		$this->resetUI();
		$this->emit('noty', 'Producto Actualizado');


	}



	public function resetUI()
	{
		$this->name ='';
		$this->barcode ='';
		$this->cost ='';
		$this->price ='';
		$this->stock ='';
		$this->mostrador_canal = 0;
		$this->ecommerce_canal =0;
		$this->alerts ='';
		$this->proveedor ='';
		$this->descripcion ='';
		$this->search ='';
		$this->inv_ideal ='';
		$this->cantidad ='';
		$this->categoryid ='Elegir';
		$this->tipo_unidad_medida ='Elegir';
		$this->unidad_medida ='Elegir';
		$this->image = null;
		$this->selected_id = 0;
		$this->pageTitle = 'Listado';
		$this->proveedor = '1';
		$this->componentName = 'Productos';
		$this->categoryid = 'Elegir';
		$this->almacen = 'Elegir';
		$this->stock_descubierto = 'Elegir';
		$this->agregar = 0;

	}

	public function resetUICategoria()
	{
		$this->name_categoria ='';

	}

	public function resetUIAlmacen()
	{
		$this->name_almacen ='';

	}

	protected $listeners =[
		'deleteRow' => 'Destroy',
		'ConfirmCheck' => 'DeleteSelected',
        'accion-lote' => 'AccionEnLote' // 25-6-2024
	];
    
    // 25-6-2024
	
	public function Filtro($estado)
	{
	   $this->estado_filtro = $estado;
	   
	}
	
	// 25-6-2024
	public function AccionEnLote($ids, $id_accion)
    {
    
    //dd($ids);
    
    if($id_accion == 1) {
        $estado = 0;
        $msg = 'RESTAURADOS';
    } else {
        $estado = 1;
        $msg = 'ELIMINADOS';
    }
    
    $insumos_checked = insumo::select('insumos.id','insumos.comercio_id')->whereIn('insumos.id',$ids)->get();
    
    foreach($insumos_checked as $i){
        $i->eliminado = $estado;
        $i->save();
    }
    
    $this->id_check = [];
    $this->accion_lote = 'Elegir';
    
     $this->emit('global-msg',"INSUMOS ".$msg);
    
    }
    
    
	public function ScanCode($code)
	{
		$this->ScanearCode($code);
		$this->emit('global-msg',"SE AGREGÓ EL PRODUCTO AL CARRITO");
	}


	public function Destroy(insumo $insumos)
	{
	    
	    $receta = receta::where('insumo_id',$insumos->id)->where('eliminado',0)->first();
	    if($receta != null){
	        $this->emit("msg-error","No se puede eliminar el insumo porque esta presente en una receta");
	        return;
	    }
	    
	    
		$insumos->update([
			'eliminado' => 1
		]);


/* HISTORICO DE STOCK DE INSUMOS
		$usuario_id = Auth::user()->id;

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		if($insumos) {
			$historico = historico_stock_insumo::create([

				'tipo_movimiento' => 7,
				'producto_id' => $product->id,
				'cantidad_movimiento' => - $product->stock,
				'stock' => 0,
				'usuario_id' => $usuario_id,
				'comercio_id'  => $comercio_id
			]);
		}

		*/

		$this->resetUI();
		$this->emit('noty', 'Insumo Eliminado');
	}

	public function DestroyImage(Product $product)
	{

		if($this->image === $product->image) {

		$imageTemp = $product->image;

		$product->update([
			'image' => null
		]);

		if($imageTemp !=null) {
			if(file_exists('storage/products/' . $imageTemp )) {
				unlink('storage/products/' . $imageTemp);
			}
		}

		$this->image = '';
		$this->emit('product-deleted', 'Imagen Eliminada');
	} else {
		$this->image = $product->image;
	}

}


	public function DeleteSelected()
	{
		Product::query()
		->whereIn('products.id', $this->SelectedProducts)
		->update([
			'eliminado' => 1
		]);

		$this->resetUI();
		$this->emit('product-deleted', 'Productos Eliminados');

		$this->SelectedProducts = [];
		$this->SelectedAll = false;
	}



	public function updatedSelectedAll($value) {
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

			if($value) {


		if($this->proveedor_elegido) {

		$this->SelectedProducts = Product::where('comercio_id',$comercio_id)
		->where('products.proveedor_id', 'like', $this->proveedor_elegido)
		->pluck('id');

	}

	if($this->id_almacen) {

	$this->SelectedProducts = Product::where('comercio_id',$comercio_id)
	->where('products.seccionalmacen_id', 'like', $this->id_almacen)
	->pluck('id');

	}

	if($this->id_categoria) {

	$this->SelectedProducts = Product::where('comercio_id',$comercio_id)
	->where('products.category_id', 'like', $this->id_categoria)
	->pluck('id');

	}
		if($this->id_categoria && $this->proveedor_elegido) {

		$this->SelectedProducts = Product::where('comercio_id',$comercio_id)
		->where('products.category_id', 'like', $this->id_categoria)
		->where('products.proveedor_id', 'like', $this->proveedor_elegido)
		->pluck('id');

		}

		if($this->id_categoria && $this->id_almacen) {

		$this->SelectedProducts = Product::where('comercio_id',$comercio_id)
		->where('products.category_id', 'like', $this->id_categoria)
		->where('products.seccionalmacen_id', 'like', $this->id_almacen)
		->pluck('id');

		}

		if($this->id_almacen && $this->proveedor_elegido) {

		$this->SelectedProducts = Product::where('comercio_id',$comercio_id)
		->where('products.seccionalmacen_id', 'like', $this->id_almacen)
		->where('products.proveedor_id', 'like', $this->proveedor_elegido)
		->pluck('id');

	}

	if($this->id_categoria && $this->id_almacen && $this->proveedor_elegido) {

	$this->SelectedProducts = Product::where('comercio_id',$comercio_id)
	->where('products.category_id', 'like', $this->id_categoria)
	->where('products.seccionalmacen_id', 'like', $this->id_almacen)
	->where('products.proveedor_id', 'like', $this->proveedor_elegido)
	->pluck('id');


	$this->id_categoria = $this->id_categoria;
	$this->id_almacen = $this->id_almacen;
	$this->proveedor_elegido = $this->proveedor_elegido;

}


if(($this->id_categoria = '') && ($this->id_almacen = '') && ($this->proveedor_elegido = '')) {

$this->SelectedProducts = Product::where('comercio_id',$comercio_id)
->pluck('id');

}

			} else {
				$this->SelectedProducts = [];
			}
		}


		public function UpdatePrice($id_prod, $cant = 1)
		{

			$this->product = insumo::find($id_prod);

			$this->product->update([
				'cost' => $cant
				]);



							$this->emit('product-updated', 'Insumo Actualizado');
			}


		public function UpdateQty($id_prod, $cant = 1)
		{

			$this->product = insumo::find($id_prod);

			$this->original = $this->product->stock;

			$this->product->update([
				'stock' => $cant
				]);

/* HISTORICO DE STOCK DE INSUMO ACA
				$usuario_id = Auth::user()->id;

				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;

				$this->cantidad_movimiento = $cant - $this->original;

				$historico_stock = historico_stock::create([
					'tipo_movimiento' => 5,
					'producto_id' => $id_prod,
					'cantidad_movimiento' => $this->cantidad_movimiento,
					'stock' => $cant,
					'usuario_id' => $usuario_id,
					'comercio_id'  => $comercio_id
				]);

				*/

			$this->emit('product-updated', 'Insumo Actualizado');

		}


		public function ExportarInsumos() {

		    return redirect('insumos/excel/'. Carbon::now()->format('d_m_Y_H_i_s'));
		    }



  //GET SUCURSALES DE UN COMERCIO
  public function getSucursal($casa_central_id)
  {
	return sucursales::join('users','users.id','sucursales.sucursal_id')
	->select('users.name','sucursales.sucursal_id')
	->where('casa_central_id', $casa_central_id)
	->where('sucursales.eliminado',0)
	->get();
  }
  
  
        public function UpdateOrCreateStock($insumos,$casa_central_id){
        
          foreach ($this->stock as $key => $value) {
            
            if(empty($value)){$value = 0;}
           insumos_stock_sucursales::updateOrCreate(
            [ 
            'sucursal_id' => $key,
            'insumo_id' => $insumos->id,
            'comercio_id' => $casa_central_id
            ],
            [
            'stock' => $value,
            'sucursal_id' => $key,
            'insumo_id' => $insumos->id,
            'comercio_id' => $casa_central_id
            ]
            );
            }

        }
  

        public function UpdateRecetas($insumo_id){
        $insumos = insumo::find($insumo_id);
        $recetas = receta::where('insumo_id', $insumos->id )->get();
        $productos_recetas = [];
        
		foreach($recetas as $r) {

            $relacion = $this->GetRelacionUnidadesMedida($r->unidad_medida,$insumos->unidad_medida);
            
			$receta = receta::find($r->id);
            $array_receta = ['costo_unitario' => $this->costo_unitario,	'relacion_medida' => $relacion];
            

            $receta->update($array_receta);
			
			array_push($productos_recetas , $receta->product_id."|".$receta->referencia_variacion);
			
			$this->UpdateCostosProductos($productos_recetas);
		}            
        }
        
        public function UpdateCostosProductos($productos_recetas){
		
		
		foreach ($productos_recetas as $pr) {
		$productos_recetas = explode("|",$pr);
		
		$producto_id = $productos_recetas[0];
		$referencia_variacion = $productos_recetas[1];
		
		$cost = receta::where('product_id',$producto_id)
		->where('referencia_variacion',$referencia_variacion)
		->select('recetas.product_id','recetas.referencia_variacion','recetas.rinde',receta::raw(' (CASE WHEN recetas.eliminado = 0 THEN ( SUM(recetas.cantidad*recetas.costo_unitario*recetas.relacion_medida)) ELSE 0 END) AS cost'))
		->groupBy('recetas.product_id','recetas.referencia_variacion','recetas.rinde','recetas.eliminado')
		->first();
		
		if($referencia_variacion != 0) {
		$update_product = productos_variaciones_datos::where('product_id',$producto_id)->where('referencia_variacion',$referencia_variacion)->orderBy('id','desc')->first();
		$update_product->cost = $cost->cost/$cost->rinde;
		$update_product->save();
		} else {
		 $update_product = Product::find($producto_id);
		 $update_product->cost = $cost->cost/$cost->rinde;
		 $update_product->save();
		}
		 
		}            
        }


		

  
}