<?php

namespace App\Http\Livewire;


use App\Http\Livewire\Scaner;
use App\Models\Category;
use Illuminate\Validation\Rule;
use App\Models\proveedores;
use App\Models\lista_precios;
use App\Models\Product;
use App\Models\wocommerce;
use App\Models\receta;
use App\Models\sucursales;
use App\Models\productos_lista_precios;
use App\Models\actualizacion_precios;
use App\Models\historico_stock;
use Livewire\Component;
use App\Models\seccionalmacen;
use App\Models\datos_facturacion;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Traits\CartTrait;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;

class StockController extends Scaner //Component
{

	use WithPagination;
	use WithFileUploads;
	use CartTrait;


	public $name,$barcode,$cost,$price, $accion_lote, $sucursal_destino, $tipo_producto, $stock,$alerts,$categoryid,$search,$image,$selected_id,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $inv_ideal, $proveedor, $cod_proveedor, $iva, $relacion_precio_iva, $proveedor_elegido, $wc_products, $ecommerce_canal, $wc_canal, $mostrador_canal, $name_almacen, $descripcion, $image_categoria, $name_categoria, $woocommerce, $precio_lista, $sucursal_id;
	public $id_almacen;
	public $id_categoria;
	public $id_proveedor;
	private $pagination = 25;
	private $wc_product_id;

	public $SelectedProducts = [];
	public $selectedAll = FALSE;

	public $sortColumn = "name";
  public $sortDirection = "asc";


	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function mount()
	{
		$this->metodo_pago = session('MetodoPago');
		$this->pageTitle = 'Stock';
		$this->proveedor = '1';
		$this->componentName = 'Productos';
		$this->categoryid = 'Elegir';
		$this->almacen = 'Elegir';
		$this->stock_descubierto = 'Elegir';
		$this->OrderNombre = "ASC";
		$this->OrderBarcode = "ASC";
		$this->iva = 0;
		$this->relacion_precio_iva = 0;
		$this->tipo_producto = "Elegir";
		$this->wc_canal = false;
		$this->ecommerce_canal = false;
		$this->mostrador_canal = false;


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

			if($this->sucursal_id != null) {
				$this->sucursal_id = $this->sucursal_id;
			} else {
				$this->sucursal_id = $comercio_id;
			}


			$products = Product::join('categories as c','c.id','products.category_id')
			->join('seccionalmacens as a','a.id','products.seccionalmacen_id')
			->join('proveedores as pr','pr.id','products.proveedor_id')
			->select('products.*','c.name as category','a.nombre as almacen','pr.nombre as nombre_proveedor')
			->where('products.comercio_id', 'like', $this->sucursal_id)
			->where('products.eliminado', 'like', 0);

			if($this->id_categoria) {

			$products = $products->where('products.category_id', 'like', $this->id_categoria);

			}

			if($this->id_almacen) {

			$products = $products->where('products.seccionalmacen_id', 'like', $this->id_almacen);

			}

			if($this->proveedor_elegido) {

			$products = $products->where('products.proveedor_id', 'like', $this->proveedor_elegido);

			}

			if(strlen($this->search) > 0) {

			$products = $products->where( function($query) {
					 $query->where('products.name', 'like', '%' . $this->search . '%')
						->orWhere('products.barcode', 'like',$this->search . '%');
					});

			}

			$products = $products->orderBy($this->sortColumn, $this->sortDirection);

			$products = $products->paginate($this->pagination);

			//* ---------    TODAS LAS VARIABLES  -------------- */

			$this->datos_facturacion = datos_facturacion::where('comercio_id',$this->sucursal_id)->first();

			if($this->datos_facturacion != null) {
			$this->iva_defecto = 	$this->datos_facturacion->iva_defecto;
		} else {
			$this->iva_defecto = 0;
		}



			$this->lista_precios = lista_precios::where('comercio_id',$this->sucursal_id)->get();

			$this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')->where('casa_central_id', $comercio_id)->get();

			$this->comercio_id = $comercio_id;

			/*-------------------------------------------------*/

			//////////////////////////////////////////////

			$wc = wocommerce::where('comercio_id', $comercio_id)->first();

			if($wc == null){
				$this->wc_yes = 0;
			} else {
					$this->wc_yes = $wc->id;
			}

			return view('livewire.stock.component', [
				'datos_facturacion' => $this->datos_facturacion,
				'iva_defecto' => $this->iva_defecto,
				'lista_precios' => $this->lista_precios,
				'data' => $products,
				'wc_yes' => $this->wc_yes,
				'comercio_id' => $this->comercio_id,
				'sucursales' => $this->sucursales,
				'categories' => Category::orderBy('name','asc')->where('comercio_id', 'like', $this->sucursal_id)->get(),
				'almacenes' => seccionalmacen::orderBy('nombre','asc')->where('comercio_id', 'like', $this->sucursal_id)->get(),
				'prov' => proveedores::orderBy('nombre','asc')->where('comercio_id', 'like', $this->sucursal_id)->get()
			])
			->extends('layouts.theme.app')
			->section('content');

}

	public function Store()
	{

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$rules  =[
			'name' => 'required|min:3',
			'barcode' => ['required',Rule::unique('products')->where('comercio_id',$comercio_id)->where('eliminado',0)],
			'price' => 'required',
			'stock' => 'required',
			'almacen' => 'not_in:Elegir',
			'alerts' => 'required',
			'categoryid' => 'required|not_in:Elegir',
			'stock_descubierto' => 'required|not_in:Elegir',
			'tipo_producto' => 'required|not_in:Elegir'
		];

		$messages = [
			'name.required' => 'Nombre del producto requerido',
			'name.min' => 'El nombre debe tener al menos 3 caracteres',
			'name.required' => 'El codigo del producto requerido',
			'barcode.unique' => 'El codigo del producto ya esta en uso',
			'barcode.min' => 'El codigo debe tener al menos 3 caracteres',
			'price.required' => 'Precio de venta requerido',
			'stock.required' => 'Ingresa las existencias',
			'almacen.not_in' => 'Ingresa el almacen',
			'alerts.required' => 'Falta el valor para las alertas',
			'categoryid.not_in' => 'Elige una categoría válida',
			'tipo_producto.not_in' => 'Elige el tipo de producto',
			'stock_descubierto.not_in' => 'Elige si maneja stock o no.'
		];


		if($this->relacion_precio_iva === 0) {
		    $this->relacion_precio_iva = 1;
		} else {
		     $this->relacion_precio_iva = $this->relacion_precio_iva;
		}
		if($this->iva === 0) {
				$this->iva = 0;
		} else {
				 $this->iva = $this->iva;
		}

		$this->validate($rules, $messages);

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;



		if($this->tipo_producto == 2) {
			$this->cost = 0;
		} else {
			$this->cost = $this->cost;
		}

						if($this->wc_canal == true) {

							////////// WooCommerce ////////////

							if(Auth::user()->comercio_id != 1)
							$comercio_id = Auth::user()->comercio_id;
							else
							$comercio_id = Auth::user()->id;

							$wc = wocommerce::where('comercio_id', $comercio_id)->first();



							if($wc != null){

							$woocommerce = new Client(
								$wc->url,
								$wc->ck,
								$wc->cs,

									[
											'version' => 'wc/v3',
									]
							);

					///////// DEFINIR SI TRABAJA O NO CON STOCK  /////////

							if($this->stock_descubierto == "si") {

								$this->manage_stock = true;
								$this->stock_quantity = $this->stock;

							} else {

								$this->manage_stock = false;
								$this->stock_quantity  = null;

							}


							if($this->precio_lista != null) {


					///////// WOCOMMERCE CON VARIAS LISTAS DE PRECIOS /////////


								foreach ($this->precio_lista as $key => $value) {



										$this->key_precio_lista = lista_precios::find($key);

									$this->wc_key_price =	$this->key_precio_lista->wc_key."_wholesale_price";
									$this->wc_key_have =	$this->key_precio_lista->wc_key."_have_wholesale_price";

							$data = [
									'name' => $this->name,
									'type' => 'simple',
									'sku' => $this->barcode,
									'status' => 'publish',
									'manage_stock' => $this->manage_stock,
									'stock_quantity' => $this->stock_quantity,
									'stock_status' => "instock",
									'regular_price' => $this->price,
									'categories' => [
											[
													'id' => $this->categoryid,
											]
									],

									'meta_data' => [
											[
												'key' => $this->wc_key_price,
												'value' => $this->precio_lista[$key]
											],
											[
												'key' => $this->wc_key_have,
												'value' => "yes"
											]
										]
							];

							}

						} else {

							////// WOCOMMERCE CON UNA SOLA LISTA DE PRECIOS //////

							$data = [
									'name' => $this->name,
									'type' => 'simple',
									'sku' => $this->barcode,
									'status' => 'publish',
									'manage_stock' => $this->manage_stock,
									'stock_quantity' => $this->stock_quantity,
									'stock_status' => "instock",
									'regular_price' => $this->price,
									'categories' => [
											[
													'id' => $this->categoryid,
											]
									],
							];



						}

							$this->wc_product_id = $woocommerce->post('products', $data);

							$product = Product::create([
								'name' => $this->name,
								'cost' => $this->cost,
								'price' => $this->price,
								'barcode' => $this->barcode,
								'stock' => $this->stock,
								'alerts' => $this->alerts,
								'tipo_producto' => $this->tipo_producto,
								'stock_descubierto' => $this->stock_descubierto,
								'seccionalmacen_id' => $this->almacen,
								'category_id' => $this->categoryid,
								'proveedor_id' => $this->proveedor,
								'comercio_id' => $comercio_id,
								'iva' => $this->iva,
								'relacion_precio_iva' => $this->relacion_precio_iva,
								'cod_proveedor' => $this->cod_proveedor,
								'mostrador_canal' => $this->mostrador_canal,
								'ecommerce_canal' => $this->ecommerce_canal,
								'wc_canal' => $this->wc_canal,
								'wc_product_id' => $this->wc_product_id->id,
								'descripcion' => $this->descripcion
							]);

							if($this->lista_precios) {

								if($this->precio_lista != null) {

								foreach ($this->precio_lista as $key => $value) {

										productos_lista_precios::create([
											'precio_lista' => $this->precio_lista[$key],
											'lista_id' => $key,
											'comercio_id' => $comercio_id,
											'product_id' => $product->id,
										]);
								}

								}

							}



							if($product) {

								$usuario_id = Auth::user()->id;

								$actualizacion = actualizacion_precios::create([
									'precio_viejo' => $product->price,
									'precio_nuevo' => $this->price,
									'comercio_id' => $comercio_id,
									'user_id' => $usuario_id,
									'product_id' => $product->id,
								]);

								$historico = historico_stock::create([

									'tipo_movimiento' => 6,
									'producto_id' => $product->id,
									'cantidad_movimiento' => $this->stock,
									'stock' => $this->stock,
									'usuario_id' => $usuario_id,
									'comercio_id'  => $comercio_id
								]);
							}


							if($this->image)
							{
								$customFileName = uniqid() . '_.' . $this->image->extension();
								$this->image->storeAs('public/products', $customFileName);
								$product->image = $customFileName;
								$product->save();


								if($this->wc_canal == true) {
									$data = [

										'images' => [
												[
														'src' => 'https://expressprueba.flamincoapp.com.ar/storage/products/'.$customFileName
												]
										]

									];

									$this->wocommerce_product_id = 'products/'.$this->wc_product_id->id;

									$woocommerce->put($this->wocommerce_product_id , $data);


								}
							}

							$this->resetUI();
							$this->emit('product-added', 'Producto Registrado y agregado en wocommerce');
						}


					} else {


		$product = Product::create([
			'name' => $this->name,
			'cost' => $this->cost,
			'price' => $this->price,
			'barcode' => $this->barcode,
			'stock' => $this->stock,
			'alerts' => $this->alerts,
			'tipo_producto' => $this->tipo_producto,
			'stock_descubierto' => $this->stock_descubierto,
			'seccionalmacen_id' => $this->almacen,
			'category_id' => $this->categoryid,
			'proveedor_id' => $this->proveedor,
			'comercio_id' => $comercio_id,
			'iva' => $this->iva,
			'relacion_precio_iva' => $this->relacion_precio_iva,
			'cod_proveedor' => $this->cod_proveedor,
			'mostrador_canal' => $this->mostrador_canal,
			'ecommerce_canal' => $this->ecommerce_canal,
			'wc_canal' => $this->wc_canal,
			'descripcion' => $this->descripcion
		]);

		if($this->lista_precios) {

			if($this->precio_lista != null) {

			foreach ($this->precio_lista as $key => $value) {

					productos_lista_precios::create([
						'precio_lista' => $this->precio_lista[$key],
						'lista_id' => $key,
						'comercio_id' => $comercio_id,
						'product_id' => $product->id,
					]);
			}

			}

		}

		if($product) {

			$usuario_id = Auth::user()->id;

			$actualizacion = actualizacion_precios::create([
				'precio_viejo' => $product->price,
				'precio_nuevo' => $this->price,
				'comercio_id' => $comercio_id,
				'user_id' => $usuario_id,
				'product_id' => $product->id,
			]);

			$historico = historico_stock::create([

				'tipo_movimiento' => 6,
				'producto_id' => $product->id,
				'cantidad_movimiento' => $this->stock,
				'stock' => $this->stock,
				'usuario_id' => $usuario_id,
				'comercio_id'  => $comercio_id
			]);
		}


		if($this->image)
		{
			$customFileName = uniqid() . '_.' . $this->image->extension();
			$this->image->storeAs('public/products', $customFileName);
			$product->image = $customFileName;
			$product->save();
		}

		$this->resetUI();
		$this->emit('product-added', 'Producto Registrado');

			}
	}


	public function Edit(Product $product)
	{
		if($product->tipo_producto == 2) {


		$this->receta = Product::leftjoin('recetas as r','r.product_id','products.id')
	    ->leftjoin('insumos','insumos.id','r.insumo_id')
	    ->join('unidad_medidas','unidad_medidas.id','r.unidad_medida')
	    ->select(receta::raw(' SUM(r.cantidad*r.costo_unitario*r.relacion_medida) AS cost'))
	    ->where('products.id', 'like', $product->id)
	    ->where('products.eliminado', 'like', 0)
	    ->first();

			if($this->receta != null) {
			$this->cost = number_format($this->receta->cost,2);
		} else {
			$this->cost = 0;
		}
	} else {
		$this->cost = $product->cost;
	}




		$this->selected_id = $product->id;
		$this->name = $product->name;
		$this->tipo_producto = $product->tipo_producto;
		$this->barcode = $product->barcode;

		$this->price = $product->price;
		$this->wc_product_id = $product->wc_product_id;
		$this->stock = $product->stock;
		$this->almacen = $product->seccionalmacen_id;
		$this->alerts = $product->alerts;
		$this->inv_ideal = $product->inv_ideal;
		$this->proveedor = $product->proveedor_id;
		$this->stock_descubierto = $product->stock_descubierto;
		$this->categoryid = $product->category_id;
		$this->cod_proveedor = $product->cod_proveedor;
		$this->image = $product->image;
		$this->mostrador_canal = $product->mostrador_canal;
		$this->ecommerce_canal = $product->ecommerce_canal;
		$this->wc_canal = $product->wc_canal;
		$this->descripcion = $product->descripcion;
		$this->iva = $product->iva;
		$this->relacion_precio_iva = $product->relacion_precio_iva;

	if($this->lista_precios != null) {

	$this->productos_lista_precios = productos_lista_precios::where('product_id', $this->selected_id)->get();


	foreach ($this->productos_lista_precios as $key => $lp) {

		$this->precio_lista[$lp['lista_id']] = $lp['precio_lista'];

		}


}
		$this->emit('modal-show','Show modal');


}

	public function Update()
	{
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$rules  =[
			'name' => "required|min:3,name,{$this->selected_id}",
			'barcode' => ['required',Rule::unique('products')->ignore($this->selected_id)->where('comercio_id',$comercio_id)->where('eliminado',0)],
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

		if($product->price != $this->price) {

			$actualizacion = actualizacion_precios::create([
				'precio_viejo' => $product->price,
				'precio_nuevo' => $this->price,
				'comercio_id' => $comercio_id,
				'user_id' => $usuario_id,
				'product_id' => $product->id,
			]);


		}

		if ($product->wc_canal != $this->wc_canal) {

			////////// WooCommerce ////////////

			if(Auth::user()->comercio_id != 1)
			$comercio_id = Auth::user()->comercio_id;
			else
			$comercio_id = Auth::user()->id;

			$wc = wocommerce::where('comercio_id', $comercio_id)->first();


			if($wc != null){


			$woocommerce = new Client(
				$wc->url,
				$wc->ck,
				$wc->cs,

					[
							'version' => 'wc/v3',
					]
			);


		if($this->wc_canal == true) {


			///////// DEFINIR SI TRABAJA O NO CON STOCK  /////////

					if($this->stock_descubierto == "si") {

						$this->manage_stock = true;
						$this->stock_quantity = $this->stock;

					} else {

						$this->manage_stock = false;
						$this->stock_quantity  = null;

					}


					if($this->lista_precios) {


			///////// WOCOMMERCE CON VARIAS LISTAS DE PRECIOS /////////


						foreach ($this->precio_lista as $key => $value) {



								$this->key_precio_lista = lista_precios::find($key);

							$this->wc_key_price =	$this->key_precio_lista->wc_key."_wholesale_price";
							$this->wc_key_have =	$this->key_precio_lista->wc_key."_have_wholesale_price";

					$data = [
							'name' => $this->name,
							'type' => 'simple',
							'sku' => $this->barcode,
							'status' => 'publish',
							'manage_stock' => $this->manage_stock,
							'stock_quantity' => $this->stock_quantity,
							'stock_status' => "instock",
							'regular_price' => $this->price,
							'categories' => [
									[
											'id' => $this->categoryid,
									]
							],

							'meta_data' => [
									[
										'key' => $this->wc_key_price,
										'value' => $this->precio_lista[$key]
									],
									[
										'key' => $this->wc_key_have,
										'value' => "yes"
									]
								]
					];

					}

				} else {

					////// WOCOMMERCE CON UNA SOLA LISTA DE PRECIOS //////

					$data = [
							'name' => $this->name,
							'type' => 'simple',
							'sku' => $this->barcode,
							'status' => 'publish',
							'manage_stock' => $this->manage_stock,
							'stock_quantity' => $this->stock_quantity,
							'stock_status' => "instock",
							'regular_price' => $this->price,
							'categories' => [
									[
											'id' => $this->categoryid,
									]
							],
					];



				}

			/////////  CHEQUEA SI LOS PRODUCTOS ESTAN EN WOCOMMERCE O NO  /////////

			/////////  SI EL PRODUCTO ESTA REGISTRADO EN WOCOMMERCEK  /////////

			if($product->wc_product_id != null) {

			$this->wocommerce_product_id = 'products/'.$product->wc_product_id;


			$woocommerce->put($this->wocommerce_product_id , $data);

		} else {

			/////////  SI EL PRODUCTO NO ESTA REGISTRADO EN WOCOMMERCEK  /////////
			$this->wc_product_id = $woocommerce->post('products', $data);

			$product->update([
				'wc_product_id' => $this->wc_product_id->id
			]);

		}

		/////////////////////////////////////////////////////////////////////////////

		} else {


		$data = [
				'status' => 'draft'
		];


$this->wocommerce_product_id = 'products/'.$product->wc_product_id;

$woocommerce->put($this->wocommerce_product_id , $data);


	}

}

}


		$product->update([
			'name' => $this->name,
			'cost' => $this->cost,
			'price' => $this->price,
			'barcode' => $this->barcode,
			'stock' => $this->stock,
			'tipo_producto' => $this->tipo_producto,
			'seccionalmacen_id' => $this->almacen,
			'alerts' => $this->alerts,
			'proveedor_id' => $this->proveedor,
			'stock_descubierto' => $this->stock_descubierto,
			'category_id' => $this->categoryid,
			'iva' => $this->iva,
			'relacion_precio_iva' => $this->relacion_precio_iva,
			'cod_proveedor' => $this->cod_proveedor,
			'mostrador_canal' => $this->mostrador_canal,
			'ecommerce_canal' => $this->ecommerce_canal,
			'wc_canal' => $this->wc_canal,
			'descripcion' => $this->descripcion
		]);


		if($this->precio_lista != null) {


			foreach ($this->precio_lista as $key => $value) {

				$this->prod = productos_lista_precios::where('lista_id',$key)->where('product_id',$product->id)->first();

				if($this->prod != null) {

					if($this->precio_lista[$key] == '') {
						$this->precio_lista[$key] = 0;
					} else {
						$this->precio_lista[$key] = $this->precio_lista[$key];
					}

					$this->prod->update([
						'precio_lista' => $this->precio_lista[$key],
						'lista_id' => $key,
						'comercio_id' => $comercio_id,
						'product_id' => $product->id,

					]);

				} else {

					productos_lista_precios::create([
						'precio_lista' => $this->precio_lista[$key],
						'lista_id' => $key,
						'comercio_id' => $comercio_id,
						'product_id' => $product->id,
					]);


				}

			}

		}

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

			if($this->wc_canal == true) {
				$data = [

					'images' => [
							[
									'src' => 'https://expressprueba.flamincoapp.com.ar/storage/products/'.$customFileName
							]
					]

				];

				$this->wocommerce_product_id = 'products/'.$product->wc_product_id;

				$woocommerce->put($this->wocommerce_product_id , $data);


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
		$this->iva = 0;
		$this->relacion_precio_iva = 0;
		$this->stock ='';
		$this->mostrador_canal = 0;
		$this->tipo_producto = "Elegir";
		$this->wc_canal =0;
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


	public function AccionEnLote()
	{

	if($this->accion_lote == 1) {

$this->emit('confirm-eliminar', 1);


	}

if($this->accion_lote == 2) {

$this->emit('modal-cambio-sucursal', 1);

$this->productos_sucursal = Product::whereIn('id',$this->SelectedProducts)->get();


}



	}




	public function Destroy(Product $product)
	{
		$imageTemp = $product->image;
		$product->update([
			'eliminado' => 1
		]);

		if($product->wc_product_id != null) {

			////////// WooCommerce ////////////

			if(Auth::user()->comercio_id != 1)
			$comercio_id = Auth::user()->comercio_id;
			else
			$comercio_id = Auth::user()->id;

			$wc = wocommerce::where('comercio_id', $comercio_id)->first();

			if($wc != null){

			$woocommerce = new Client(
				$wc->url,
				$wc->ck,
				$wc->cs,

					[
							'version' => 'wc/v3',
					]
			);


			$woocommerce->delete('products/'.$product->wc_product_id , ['force' => true]);

		}
		}

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

			$usuario_id = Auth::user()->id;

			if(Auth::user()->comercio_id != 1)
			$comercio_id = Auth::user()->comercio_id;
			else
			$comercio_id = Auth::user()->id;

			$this->product = Product::find($id_prod);

			if($this->product->price != $cant) {

				$actualizacion = actualizacion_precios::create([
					'precio_viejo' => $this->product->price,
					'precio_nuevo' => $cant,
					'comercio_id' => $comercio_id,
					'user_id' => $usuario_id,
					'product_id' => $this->product->id,
				]);


			}

			$this->product->update([
				'price' => $cant
				]);


	////////// WooCommerce ////////////

				if($this->product->wc_canal = true) {

					if(Auth::user()->comercio_id != 1)
					$comercio_id = Auth::user()->comercio_id;
					else
					$comercio_id = Auth::user()->id;

					$wc = wocommerce::where('comercio_id', $comercio_id)->first();

					if($wc != null){

					$woocommerce = new Client(
						$wc->url,
						$wc->ck,
						$wc->cs,

							[
									'version' => 'wc/v3',
							]
					);


					$data = [
			    'regular_price' => $this->price,
			    ];

					$this->wocommerce_product_id = 'products/'.$this->product->wc_product_id;

					$woocommerce->put($this->wocommerce_product_id , $data);

				}
			}


			///////////////////////////////////////////////////


			$this->emit('product-updated', 'Precio Actualizado');

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


					////////// WooCommerce ////////////

								if($this->product->wc_canal = true) {

									if(Auth::user()->comercio_id != 1)
									$comercio_id = Auth::user()->comercio_id;
									else
									$comercio_id = Auth::user()->id;

									$wc = wocommerce::where('comercio_id', $comercio_id)->first();

									if($wc != null){

									$woocommerce = new Client(
										$wc->url,
										$wc->ck,
										$wc->cs,

											[
													'version' => 'wc/v3',
											]
									);


									$data = [
											"stock_quantity" => $cant,
											"stock_status" => "instock",
									];

									$this->wocommerce_product_id = 'products/'.$this->product->wc_product_id;

									$woocommerce->put($this->wocommerce_product_id , $data);

								}
							}


							///////////////////////////////////////////////////

			$this->emit('product-updated', 'Producto Actualizado');

		}

		public function wc_sincronizar() {

			if(Auth::user()->comercio_id != 1)
			$comercio_id = Auth::user()->comercio_id;
			else
			$comercio_id = Auth::user()->id;

			$wc = wocommerce::where('comercio_id', $comercio_id)->first();

			if($wc != null){

			$woocommerce = new Client(
				$wc->url,
				$wc->ck,
				$wc->cs,

					[
							'version' => 'wc/v3',
					]
			);

			// ------------ PRODUCTOS DE WOCOMMERCE QUE NO ESTAN EN LA APP ----------------------//.

			$wc_products = $woocommerce->get('products');



			foreach ($wc_products as $item) {

				// CHEQUEAR QUE LOS PRODUCTOS DE WOCOMMERCE ESTEN EN LA APP...

				$product_agregar =  Product::where('wc_product_id', $item->id)->where('comercio_id', $comercio_id)->first();

				// CREAR LOS PRODUCTOS QUE ESTEN EN WOCOMMERCE Y NO ESTEN EN LA APP...


	      if($product_agregar == null) {

		// AGREGAR PRODUCTO QUE NO ESTA EN EL SISTEMA...

					$product = Product::create([
						'name' => $item->name,
						'price' => $item->price,
						'barcode' => $item->sku,
						'stock' => $item->stock_quantity,
						'alerts' => 1,
						'tipo_producto' => 1,
						'stock_descubierto' => "si",
						'seccionalmacen_id' => 1,
						'category_id' => 1,
						'comercio_id' => $comercio_id,
						'mostrador_canal' => false,
						'ecommerce_canal' => false,
						'wc_canal' => true,
						'wc_product_id' => $item->id,
						'descripcion' => $item->description
					]);

					if($product) {

						$usuario_id = Auth::user()->id;

						$actualizacion = actualizacion_precios::create([
							'precio_viejo' => 0,
							'precio_nuevo' => $product->price,
							'comercio_id' => $comercio_id,
							'user_id' => $usuario_id,
							'product_id' => $product->id,
						]);

						$historico = historico_stock::create([

							'tipo_movimiento' => 6,
							'producto_id' => $product->id,
							'cantidad_movimiento' => $item->stock_quantity,
							'stock' => $item->stock_quantity,
							'usuario_id' => $comercio_id,
							'comercio_id'  => $comercio_id
						]);

					}


			}

		}


		// ------------ PRODUCTOS DE LA APP QUE NO ESTAN WOCOMMERCE ----------------------//

$products_app = Product::where('comercio_id', $comercio_id)->where('wc_canal',true)->where('eliminado',0)->get();

foreach ($products_app as $p_app) {

    if($p_app->wc_product_id == null) {

	if($this->stock_descubierto == "si") {

	$data = [
			'name' => $p_app->name,
			'type' => 'simple',
			'sku' => $p_app->barcode,
			'status' => 'publish',
			"manage_stock" => true,
			"stock_quantity" => $p_app->stock,
			"stock_status" => "instock",
			'regular_price' => $p_app->price,

	];

} else {



$data = [
	'name' => $p_app->name,
	'type' => 'simple',
	'sku' => $p_app->barcode,
	'status' => 'publish',
	'manage_stock' => false,
	'stock_quantity' => $p_app->stock,
	'stock_status' => "instock",
	'regular_price' => $p_app->price,

];


}
	$this->wc_product_id = $woocommerce->post('products', $data);
	if($this->wc_product_id) {
		$this->emit('product-added', 'SINCRONIZACIÓN EXITOSA');
	}


    } else {
    $this->emit('product-added', 'PRODUCTOS SINCRONIZADOS');
    }
}



	} else {
		$this->emit('almacen-added', '	CONFIGURE LA API DE WOCOMMERCE');
	}

}

public function list_wc() {

	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;

	$wc = wocommerce::where('comercio_id', $comercio_id)->first();

	if($wc != null){

	$woocommerce = new Client(
		$wc->url,
		$wc->ck,
		$wc->cs,

			[
					'version' => 'wc/v3',
			]
	);

dd($woocommerce->get('products'));

}
}


public function ElegirSucursal($sucursal_id) {

	$this->sucursal_id = $sucursal_id;


}

}
