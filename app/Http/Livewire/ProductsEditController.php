<?php

namespace App\Http\Livewire;


use App\Models\Category;
use Illuminate\Validation\Rule;
use App\Models\proveedores;
use App\Models\Product;
use App\Models\products_price;
use App\Models\User;
use App\Models\receta;
use App\Models\historico_stock;
use Livewire\Component;
use App\Models\seccionalmacen;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Traits\CartTrait;
use Illuminate\Support\Facades\Auth;

class ProductsEditController extends Component
{

	use WithPagination;
	use WithFileUploads;
	use CartTrait;


	public $name,$name_product, $barcode,$cost,$price,$tipo_producto, $stock,$alerts,$categoryid,$search,$image,$selected_id,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $inv_ideal, $proveedor, $cod_proveedor, $product_added, $proveedor_elegido, $ecommerce_canal, $mostrador_canal, $name_almacen, $descripcion, $image_categoria, $name_categoria, $sucursal_selected;
	public $id_almacen;
	public $id_categoria;
	public $id_proveedor;
	private $pagination = 25;

	public $sucursal = [];
	public $SelectedProducts = [];
	public $selectedAll = FALSE;

	public $sortColumn = "name";
  public $sortDirection = "asc";


	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function mount($id)
	{

		$this->metodo_pago = session('MetodoPago');
		$this->pageTitle = 'Listado';
		$this->proveedor = '1';
		$product_added = 0;
		$this->componentName = 'Productos';
		$this->categoryid = 'Elegir';
		$this->almacen = 'Elegir';
		$this->stock_descubierto = 'Elegir';
		$this->OrderNombre = "ASC";
		$this->OrderBarcode = "ASC";
		$this->tipo_producto = "Elegir";
		$this->sucursal = [];
		$this->product_added = $id;

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

	public function ModalAlmacen($value)
	{
		if($value == 'AGREGAR') {

		$this->emit('modal-almacen-show', '');

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



	public function StoreAlmacen()
  {
 	 //validation rules
 	 $rules = [
 		 'name_almacen' => 'required|min:3'
 	 ];

 	 //custom messages
 	 $customMessages = [
 		 'name_almacen.required' => 'Nombre de categoría requerido',
 		 'name_almacen.min' => 'El nombre debe tener al menos 3 caracteres',
 	 ];

 	 //execute validate
 	 $this->validate($rules, $customMessages);


 	 //insert
 	 $almacen =  seccionalmacen::create([
 		 'nombre' => $this->name_almacen,
 		 'comercio_id' => Auth::user()->id
 	 ]);

 				 // clear inputs
 	 $this->resetUIAlmacen();

	 $this->almacen = $almacen->id;
 	 // emit frontend notification
 	 $this->emit('almacen-added', 'Almacen Agregado');
	 	$this->emit('modal-show','Show modal');
  }



	public function render()
	{

			if(Auth::user()->comercio_id != 1)
			$comercio_id = Auth::user()->comercio_id;
			else
			$comercio_id = Auth::user()->id;

			$this->name_product = Product::find($this->product_added);

			$products = products_price::select('products_prices.*','users.name as name_sucursal')->join('users','users.id','products_prices.sucursal_id')->where('products_prices.product_id', $this->product_added)->get();

			return view('livewire.products_price.added', [
				'data' => $products,
				'categories' => Category::orderBy('name','asc')->where('comercio_id', 'like', $comercio_id)->get(),
				'sucursales' => User::orderBy('name','asc')->where('profile', 'Sucursal')->where('comercio_id', 'like', $comercio_id)->get(),
				'almacenes' => seccionalmacen::orderBy('nombre','asc')->where('comercio_id', 'like', $comercio_id)->get(),
				'prov' => proveedores::orderBy('nombre','asc')->where('comercio_id', 'like', $comercio_id)->get()
			])
			->extends('layouts.theme.app')
			->section('content');

}

public function StorePrecios()
{

	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

	$rules  =[
		'price' => 'required',
		'stock' => 'required',
		'almacen' => 'not_in:Elegir',
		'alerts' => 'required',
		'stock_descubierto' => 'required|not_in:Elegir',
	];

	$messages = [
		'price.required' => 'Precio de venta requerido',
		'stock.required' => 'Ingresa las existencias',
		'almacen.not_in' => 'Ingresa el almacen',
		'alerts.required' => 'Falta el valor para las alertas',
		'stock_descubierto.not_in' => 'Elige si maneja stock o no.'
	];

	$this->validate($rules, $messages);

	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

	if($this->tipo_producto = 2) {
		$this->cost = 0;
	} else {
		$this->cost = $this->cost;
	}

	$product = products_price::create([
		'product_id' => $this->product_added,
		'sucursal_id' => $this->sucursal_selected,
		'price' => $this->price,
		'stock' => $this->stock,
		'alerts' => $this->alerts,
		'stock_descubierto' => $this->stock_descubierto,
		'seccionalmacen_id' => $this->almacen,
		'comercio_id' => $comercio_id,
	]);

/*
	if($product) {

		$usuario_id = Auth::user()->id;

		$historico = historico_stock::create([

			'tipo_movimiento' => 6,
			'producto_id' => $product->id,
			'cantidad_movimiento' => $this->stock,
			'stock' => $this->stock,
			'usuario_id' => $usuario_id,
			'comercio_id'  => $comercio_id
		]);
	}

	*/

	$this->resetUI();
	$this->emit('product-added', 'Precio Registrado');


}


	public function Store()
	{

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$rules  =[
			'name' => 'required|min:3',
			'barcode' => ['required',Rule::unique('products')->where('comercio_id',$comercio_id)],
			'categoryid' => 'required|not_in:Elegir',
			'tipo_producto' => 'required|not_in:Elegir'
		];

		$messages = [
			'name.required' => 'Nombre del producto requerido',
			'name.min' => 'El nombre debe tener al menos 3 caracteres',
			'name.required' => 'El codigo del producto requerido',
			'barcode.unique' => 'El codigo del producto ya esta en uso',
			'barcode.min' => 'El codigo debe tener al menos 3 caracteres',
			'categoryid.not_in' => 'Elige una categoría válida',
			'tipo_producto.not_in' => 'Elige el tipo de producto',
		];

		$this->validate($rules, $messages);

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		if($this->tipo_producto = 2) {
			$this->cost = 0;
		} else {
			$this->cost = $this->cost;
		}

		$product = Product::create([
			'name' => $this->name,
			'cost' => $this->cost,
			'barcode' => $this->barcode,
			'tipo_producto' => $this->tipo_producto,
			'category_id' => $this->categoryid,
			'proveedor_id' => $this->proveedor,
			'comercio_id' => $comercio_id,
			'mostrador_canal' => $this->mostrador_canal,
			'ecommerce_canal' => $this->ecommerce_canal,
			'descripcion' => $this->descripcion
		]);

		$this->product_added = $product->id;

/*
		if($product) {

			$usuario_id = Auth::user()->id;

			$historico = historico_stock::create([

				'tipo_movimiento' => 6,
				'producto_id' => $product->id,
				'cantidad_movimiento' => $this->stock,
				'stock' => $this->stock,
				'usuario_id' => $usuario_id,
				'comercio_id'  => $comercio_id
			]);
		}

		*/

		if($this->image)
		{
			$customFileName = uniqid() . '_.' . $this->image->extension();
			$this->image->storeAs('public/products', $customFileName);
			$product->image = $customFileName;
			$product->save();
		}

		$this->emit('product-added', 'Producto Registrado');


	}


		public function Sucursal()
		{
				$this->sucursal_selected = $this->sucursal;

				$this->emit('show-modal-prices', '');

		}

	public function Edit(products_price $product)
	{
		$this->selected_id = $product->id;

		$this->sucursal_selected = $product->sucursal_id;
		$this->price = $product->price;
		$this->stock = $product->stock;
		$this->almacen = $product->seccionalmacen_id;
		$this->alerts = $product->alerts;
		$this->stock_descubierto = $product->stock_descubierto;

		$this->emit('show-modal-prices', '');
	}

	public function Update()
	{
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$rules  =[
			'name' => "required|min:3,name,{$this->selected_id}",
			'barcode' => ['required',Rule::unique('products')->ignore($this->selected_id)->where('comercio_id',$comercio_id)],
			'price' => 'required',
			'stock' => 'required',
			'alerts' => 'required',
			'proveedor' => 'required',
			'almacen' => 'not_in:Elegir',
			'categoryid' => 'required|not_in:Elegir',
			'stock_descubierto' => 'required|not_in:Elegir',
			'tipo_producto' => 'required|not_in:Elegir'
		];

		$messages = [
			'name.required' => 'Nombre del producto requerido',
			'name.min' => 'El nombre del producto debe tener al menos 3 caracteres',
			'barcode.required' => 'El codigo es requerido',
			'price.required' => 'El precio es requerido',
			'stock.required' => 'El stock es requerido',
			'proveedor.required' => 'El proveedor es requerido',
			'alerts.required' => 'Ingresa el valor mínimo en existencias',
			'almacen.not_in' => 'Ingresa la seccion del almacen',
			'categoryid.not_in' => 'Elige un nombre de categoría diferente de Elegir',
			'tipo_producto.not_in' => 'Elige el tipo de producto',
			'stock_descubierto.not_in' => 'Elige si maneja o no stock',
		];


		$this->validate($rules, $messages);

		$product = Product::find($this->selected_id);

		$usuario_id = Auth::user()->id;

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$this->cantidad_movimiento = $this->stock -  $product->stock;

		$historico_stock = historico_stock::create([
			'tipo_movimiento' => 5,
			'producto_id' => $this->selected_id,
			'cantidad_movimiento' => $this->cantidad_movimiento,
			'stock' => $this->stock,
			'usuario_id' => $usuario_id,
			'comercio_id'  => $comercio_id
		]);

		$product->update([
			'name' => $this->name,
			'cost' => $this->cost,
			'price' => $this->price,
			'barcode' => $this->barcode,
			'stock' => $this->stock,
			'tipo_producto' => $this->tipo_producto,
			'seccionalmacen_id' => $this->almacen,
			'alerts' => $this->alerts,
			'tipo_producto' => $this->tipo_producto,
			'proveedor_id' => $this->proveedor,
			'stock_descubierto' => $this->stock_descubierto,
			'category_id' => $this->categoryid,
			'cod_proveedor' => $this->cod_proveedor,
			'mostrador_canal' => $this->mostrador_canal,
			'ecommerce_canal' => $this->ecommerce_canal,
			'descripcion' => $this->descripcion
		]);

		if($this->image != $product->image)
		{
			$customFileName = uniqid() . '_.' . $this->image->extension();
			$this->image->storeAs('public/products', $customFileName);
			$imageTemp = $product->image; // imagen temporal
			$product->image = $customFileName;
			$product->save();

			if($imageTemp !=null)
			{
				if(file_exists('storage/products/' . $imageTemp )) {
					unlink('storage/products/' . $imageTemp);
				}
			}
		}

		$this->resetUI();
		$this->emit('product-updated', 'Producto Actualizado');


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
		$this->categoryid ='Elegir';
		$this->image = null;
		$this->selected_id = 0;
		$this->pageTitle = 'Listado';
		$this->proveedor = '1';
		$this->componentName = 'Productos';
		$this->categoryid = 'Elegir';
		$this->almacen = 'Elegir';
		$this->stock_descubierto = 'Elegir';

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
		'ConfirmCheck' => 'DeleteSelected'
	];

	public function ScanCode($code)
	{
		$this->ScanearCode($code);
		$this->emit('global-msg',"SE AGREGÓ EL PRODUCTO AL CARRITO");
	}


	public function Destroy(Product $product)
	{
		$imageTemp = $product->image;
		$product->update([
			'eliminado' => 1
		]);

		$usuario_id = Auth::user()->id;

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		if($product) {
			$historico = historico_stock::create([

				'tipo_movimiento' => 7,
				'producto_id' => $product->id,
				'cantidad_movimiento' => - $product->stock,
				'stock' => 0,
				'usuario_id' => $usuario_id,
				'comercio_id'  => $comercio_id
			]);
		}



		if($imageTemp !=null) {
			if(file_exists('storage/products/' . $imageTemp )) {
				unlink('storage/products/' . $imageTemp);
			}
		}

		$this->resetUI();
		$this->emit('product-deleted', 'Producto Eliminado');
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

			$this->product = Product::find($id_prod);

			$this->original = $this->product->stock;

			$this->product->update([
				'price' => $cant
				]);

			}


		public function UpdateQty($id_prod, $cant = 1)
		{

			$this->product = Product::find($id_prod);

			$this->original = $this->product->stock;

			$this->product->update([
				'stock' => $cant
				]);


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

			$this->emit('product-updated', 'Producto Actualizado');

		}



}
