<?php

namespace App\Http\Livewire;

// Trait

use App\Traits\WocommerceTrait;
use App\Traits\ProductsTrait;
use App\Traits\ProductsImagenesTrait;
use App\Traits\CartTrait;
use App\Traits\CategoriasTrait;

// Modelos

use App\Http\Livewire\Scaner;
use App\Models\Category;
use App\Models\marcas;
use App\Models\etiquetas;
use Illuminate\Validation\Rule;
use App\Models\proveedores;
use Notification;
use App\Notifications\NotificarCambios;
use App\Models\lista_precios;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use App\Models\wocommerce;
use App\Models\atributos;
use App\Models\imagenes;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;
use App\Models\descargas;
use App\Models\variaciones;
use App\Models\productos_variaciones_datos;
use App\Models\ClientesMostrador;
use App\Models\productos_variaciones;
use App\Models\Sale;
use App\Models\cajas;
use App\Models\SaleDetail;
use App\Services\CartVariaciones;
use DB;
use Intervention\Image\Facades\Image;
use App\Models\receta;
use App\Models\User;
use App\Models\sucursales;
use App\Models\productos_stock_sucursales;
use App\Models\productos_lista_precios;
use App\Models\actualizacion_precios;
use App\Models\historico_stock;
use Livewire\Component;
use App\Models\seccionalmacen;
use App\Models\datos_facturacion;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use Carbon\Carbon;


use Illuminate\Http\Request; // 13-9-2024





class ProductsController extends Component //Scaner //Component
{

	use WithPagination;
	use WithFileUploads;
	use CartTrait;
	use WocommerceTrait;
	use ProductsTrait;
//	use ProductsImagenesTrait;
    use CategoriasTrait;


	public $name,$barcode,$cost,$price, $id_check, $accion_lote,$tipo_carga_imagen , $search_imagenes, $style_tipo_1 , $style_tipo_2, $stock_origen, $cod_variacion, $sucursal_destino, $tipo_producto, $stock,$alerts,$categoryid,$search,$images,$image,$selected_id,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $inv_ideal, $proveedor, $cod_proveedor, $iva, $relacion_precio_iva, $proveedor_elegido, $wc_products, $ecommerce_canal, $wc_canal, $mostrador_canal, $name_almacen, $descripcion,  $image_categoria, $name_categoria, $woocommerce,$costos_variacion, $variacion_atributo, $caja, $precio_lista, $stock_sucursal, $sucursal_id, $stock_sucursales, $productos_lista_precios, $vista_id, $productos_stock_sucursales, $producto_tipo, $cart, $costo, $nro_cheque_ch;
	public $id_almacen;
	public $id_categoria;
	public $id_proveedor;
	private $pagination = 15;
	private $wc_product_id;
	public $base64;
	public $imagen_seleccionada;

    public $imagen_checked = [];
	public $SelectedProducts = [];
	public $selectedAll = FALSE;
	public $agregar;
	public $ver_configuracion;
	
	public $imagenes = [];

	public $sortColumn = "name";
  public $sortDirection = "asc";
  
    public $mostrarFiltros = false;

    // 6-6-2024
    public $marca_id,$name_marca,$id_marca;
    
    public function MostrarFiltro()
    {
        $this->mostrarFiltros = !$this->mostrarFiltros;
    }
    

	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}

	public function sort($column)
	{
			$this->sortColumn = $column;
			$this->sortDirection = $this->sortDirection == 'asc' ? 'desc' : 'asc';
	}



// Modales abrir y guardar 

    public function ModalListaPrecio()
	{
	$this->emit('modal-lista-precios-show', '');
    }
    

	
	public function StoreListaPrecio()
	{
	 $this->StoreListaPrecioProduct();
	}

	public function ModalCategoria($value)
	{
		if($value == 'AGREGAR') {

		$this->emit('modal-categoria-show', '');

		}

	}		
	public function StoreCategoria()
	{
	 $this->StoreCategoriaProduct();
	}


	public function ModalAlmacen($value)
	{
		if($value == 'AGREGAR') {

		$this->emit('modal-almacen-show', '');

		}

	}

	public function StoreAlmacen()
    {
        $this->StoreAlmacenProduct();
    }
    
    	public function ModalProveedor($value)
	{
		if($value == 'AGREGAR') {
        
		$this->emit('modal-proveedor-show', '');

		}

	}
  
  	public function StoreProveedor()
    {
        $this->StoreProveedorProduct();
    }
  


	public function mount(Request $request) // 13-9-2024
	{
        // 28-5-2024 ---> Modificado
        $this->ver_configuracion = 0;
        $this->configuracion_precio_interno = 0;
        
        //
        
        
        $this->etiquetas_seleccionadas = [];
	    $this->estado_filtro = 0;
		$this->metodo_pago = session('MetodoPago');
		$this->pageTitle = 'Listado';
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
		$this->mostrador_canal = true;
	//	$this->producto_tipo = 's';
		$this->producto_tipo = 'Elegir';
		$this->accion_lote = 'Elegir';

        $this->GetDatosGenerales();
        $this->GetDatosFacturacionProduct();
        $this->GetCasaCentralId();
        $this->GetSucursalId();
        $this->GetAtributosYvariaciones();
        

        $this->GetConfiguracion();
        
      

		/*------------- VE SI TIENE WOCOMMERCE --------------*/

        $wc = wocommerce::where('comercio_id', $this->comercio_id)->first();

		if($wc == null){ $this->wc_yes = 0; } else { $this->wc_yes = $wc->id; }
	

		$this->comercio_id = $this->setComercioId();
		
		// 13-9-2024
		$this->SetConfiguracionInsumoElegido($request);

		
	}

	public function render()
	{
	    	$productSection = 'Products';
		    $this->product_section = $productSection;
	        
	        $this->GetDatosGeneralesSession();

			$products = Product::join('categories as c','c.id','products.category_id')
			->join('seccionalmacens as a','a.id','products.seccionalmacen_id')
			->join('proveedores as pr','pr.id','products.proveedor_id')
			->select('products.*','c.name as category','a.nombre as almacen','pr.nombre as nombre_proveedor')
			->where('products.comercio_id', $this->casa_central_id)
			->where('products.eliminado', $this->estado_filtro);

            $products = $this->RenderFiltrar($products,$this->id_categoria,$this->id_almacen,$this->proveedor_elegido,$this->etiquetas_filtro,$this->id_marca,$this->es_insumo_elegido);
 
			if(strlen($this->search) > 0) {

			$products = $products->where( function($query) {
					 $query->where('products.name', 'like', '%' . $this->search . '%')
						->orWhere('products.barcode', 'like',$this->search . '%');
					});

			}

			$products = $products->orderBy($this->sortColumn, $this->sortDirection);

			$products = $products->paginate($this->pagination);
			
			//dd($products);
			
			$item_ids = $products->items();

			$items_id = [];
			
	    	foreach ($item_ids as $i_id) {
		    $id_id = $i_id->id;
		    array_push($items_id, $id_id);
		    
		    } 
            // -------- DETERMINA LOS PRECIOS Y SOTCK -------- //
            
			if($this->tipo_usuario->sucursal != 1) {

			$this->productos_lista_precios = productos_lista_precios::where('comercio_id', $this->casa_central_id)->whereIn('product_id',$items_id)->get();

			$this->stock_sucursales = productos_stock_sucursales::whereIn('product_id',$items_id)->get();
			
			$this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
		    ->select('users.name','sucursales.sucursal_id')
		    ->where('casa_central_id', $this->comercio_id)
		    ->where('sucursales.eliminado',0)
		    ->get();
		    
		    //dd($this->sucursales);


			} else {
			$this->stock_sucursales = productos_stock_sucursales::where('comercio_id', $this->casa_central_id)->whereIn('product_id',$items_id)->get();

            $this->productos_lista_precios = productos_lista_precios::where('comercio_id', $this->comercio_id)->whereIn('product_id',$items_id)->get();
            
            $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
		    ->select('users.name','sucursales.sucursal_id')
		    ->where('casa_central_id', $this->casa_central->casa_central_id)
		    ->where('sucursales.eliminado',0)
		    ->get();
		    
		    
		    //dd($this->sucursales);

			}

            // ---------------------------------------------------// 
            
        	$cart = new CartVariaciones;
			$this->cart = $cart->getContent();
            
                    
            $this->etiqueta = $this->GetEtiquetas($this->comercio_id,"productos");
            
            $this->etiqueta_json = $this->GetEtiquetasJson($this->comercio_id,"productos");

			return view('livewire.products.component', [
				'datos_facturacion' => $this->datos_facturacion,
				'iva_defecto' => $this->iva_defecto,
				'lista_precios' => $this->lista_precios,
				'data' => $products,
				'cart' => $this->cart,
				'variaciones' => $this->variaciones,
				'atributos_var' => $this->atributos_var,
				'productos_variaciones' => $this->productos_variaciones,
				'wc_yes' => $this->wc_yes,
				'comercio_id' => $this->comercio_id,
				'sucursales' => $this->sucursales,
				'marcas' => marcas::orderBy('name','asc')->where('eliminado', 0)->where( function($query) {
					 $query->where('comercio_id', 'like', $this->sucursal_id)->orWhere('comercio_id', $this->casa_central_id);
					})->get(),
				'categories' => Category::orderBy('name','asc')->where('eliminado', 0)->where( function($query) {
					 $query->where('comercio_id', 'like', $this->sucursal_id)->orWhere('comercio_id', $this->casa_central_id);
					})->get(),
				'almacenes' => seccionalmacen::orderBy('nombre','asc')->where('comercio_id', 'like', $this->sucursal_id)->orWhere('comercio_id', $this->casa_central_id)->where('eliminado',0)->get(),
				'prov' => proveedores::orderBy('nombre','asc')->where('comercio_id', 'like', $this->sucursal_id)->orWhere('comercio_id', $this->casa_central_id)->where('eliminado',0)->get()
			])
			->extends('layouts.theme-pos.app')
			->section('content');

}

//Codigo entortno Testing:

//METODO RENDER ACTUAL (Actualizar productTrait) para que corra
/*----------Elimnar esto 

public function render()
{
  
	//return dd('test');
	$this->GetDatosGeneralesSession();

	$productSection = 'Products';

	$this->renderProductsList($productSection);

		/*$products = Product::join('categories as c','c.id','products.category_id')
		->join('seccionalmacens as a','a.id','products.seccionalmacen_id')
		->join('proveedores as pr','pr.id','products.proveedor_id')
		->select('products.*','c.name as category','a.nombre as almacen','pr.nombre as nombre_proveedor')
		->where('products.comercio_id', 'like', $this->casa_central_id)
		->where('products.eliminado', 'like', $this->estado_filtro);

		//$products = $this->filtrarProducto($products, $this->id_categoria, $this->id_categoria, $this->proveedor_elegido );

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
		
		$item_ids = $products->items();

		$items_id = [];
		
		foreach ($item_ids as $i_id) {
		$id_id = $i_id->id;
		array_push($items_id, $id_id);
		
		} 
		// -------- DETERMINA LOS PRECIOS Y SOTCK -------- //
		
		if($this->tipo_usuario->sucursal != 1) {

		$this->productos_lista_precios = productos_lista_precios::where('comercio_id', $this->casa_central_id)->whereIn('product_id',$items_id)->get();

		$this->stock_sucursales = productos_stock_sucursales::whereIn('product_id',$items_id)->get();
		
		$this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
		->select('users.name','sucursales.sucursal_id')
		->where('casa_central_id', $this->comercio_id)
		->get();


		} else {
		$this->stock_sucursales = productos_stock_sucursales::where('comercio_id', $this->casa_central_id)->whereIn('product_id',$items_id)->get();

		$this->productos_lista_precios = productos_lista_precios::where('comercio_id', $this->comercio_id)->whereIn('product_id',$items_id)->get();
		
		$this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
		->select('users.name','sucursales.sucursal_id')
		->where('casa_central_id', $this->casa_central->casa_central_id)
		->get();

		}

		// ---------------------------------------------------// 
		
		$cart = new CartVariaciones;
		$this->cart = $cart->getContent();
		*/
		// dd($this->sucursales);

/*----- Eliminar esto
	
		
		return view('livewire.products.component', [
			'datos_facturacion' => $this->datos_facturacion,
			'iva_defecto' => $this->iva_defecto,
			'lista_precios' => $this->lista_precios,
			'data' => $this->products,
			'cart' => $this->cart,
			'variaciones' => $this->variaciones,
			'atributos_var' => $this->atributos_var,
			'productos_variaciones' => $this->productos_variaciones,
			'wc_yes' => $this->wc_yes,
			'comercio_id' => $this->comercio_id,
			'sucursales' => $this->sucursales,
			'categories' => Category::orderBy('name','asc')->where('eliminado', 0)->where( function($query) {
				 $query->where('comercio_id', 'like', $this->sucursal_id)->orWhere('comercio_id', $this->casa_central_id);
				})->get(),
			'almacenes' => seccionalmacen::orderBy('nombre','asc')->where('comercio_id', 'like', $this->sucursal_id)->orWhere('comercio_id', $this->casa_central_id)->get(),
			'prov' => proveedores::orderBy('nombre','asc')->where('comercio_id', 'like', $this->sucursal_id)->orWhere('comercio_id', $this->casa_central_id)->where('eliminado',0)->get()
		])
		->extends('layouts.theme.app')
		->section('content');

}

Eliminar esto*/

	protected $listeners =[
	    'Swicth' => 'SwicthProducts',
		'deleteRow' => 'Destroy',
		'StoreImagen' => 'StoreImagen',
		'Base64' => 'seleccionarImagenBase64',
		'accion-lote' => 'AccionEnLote',
		'deleteVariacion' => 'DestroyVariacion',
		'ConfirmCheck' => 'DeleteSelected',
		'RestaurarProducto' => 'RestaurarProducto',
		'CambiarProductoTipo' => 'CambiarProductoTipo',
		'VolverProductoTipo' => 'VolverProductoTipo', 
		'EtiquetasSeleccionadas',
        'SearchEtiquetas',
        //18-1-2024
        'UpdateConfiguracion'
        
	];

    public function VolverProductoTipo() {
     dd("hola");   
        if($this->producto_tipo == "v") {
        $this->producto_tipo = "s";
        } else {
        $this->producto_tipo = "v";    
        }
    }
    
    public function ExportarCatalogo() {
    
    $this->SwitchExportarCatalogos();
    
    }
    
   public function ExportarLista($lista_id) {

    
    $this->emit('modal-export-listas-hide', '');
 
    return redirect('lista-precios/excel/'. Carbon::now()->format('d_m_Y_H_i_s') .'/'. $lista_id);
 
    }


    public function ExportarStock($sucursal_id) {

    $this->emit('modal-export-stock-hide', '');

    return redirect('stock-sucursal/excel/' . Carbon::now()->format('d_m_Y_H_i_s') . '/' . $sucursal_id);

    }
	

    // Eliminar en lote 
    
    public function AccionEnLote($ids, $id_accion)
    {
    
    // dd($ids, $id_accion);
    
    $this->AccionEnLoteProduct($ids, $id_accion);
    
    }
    
    // Mostrar el stock de productos variables en un nuevo modal 
    
    public function MostrarStock($product_id,$sucu_id) {
    
    $this->MostrarStockProduct($product_id,$sucu_id);
    
    }

    // Eliminar un producto 
    
    public function Destroy(Product $product) {
	
    $this->DestroyProduct($product);

    }
    
    public function RestaurarProducto(Product $product) {
    
    $this->RestaurarProductoProduct($product);

    }
    
    // Modal para agregar un producto 
    
    public function ModalAgregar() {

    $this->ModalAgregarProduct();
    
    }
	
	// Modal de editar los productos 

    public function Agregar() {
        
        $this->GetEtiquetasEdit(0,"gastos",$this->comercio_id);
        $this->producto_tipo = 'Elegir';
	    $cart = new CartVariaciones;
	    
	    $this->GetDatosFacturacionDefectoProducto(1);
	    
		$cart->clear();
        $this->agregar = 1;
    }
    
	public function Edit(Product $product)
	{
	    
	    $cart = new CartVariaciones;
		$cart->clear();
	    $this->EditProduct($product,2);
	}
	
		public function Ver(Product $product)
	{
	    
	    $cart = new CartVariaciones;
		$cart->clear();
	    $this->EditProduct($product,1);
	}
	
	// Actualiza los productos 
	
	public function Update()
	{
	    
	    $response = $this->ValidarNombreRepetido($this->selected_id);

	    if($response == true) {
	        $this->emit("producto-repetido",$this->selected_id); 
	        return;
	    }
	    
	    
	    $this->UpdateProduct();
	}
	
	// Guarda los productos 
	
	public function Store()
	{
	    $response = $this->ValidarNombreRepetido($this->selected_id);

	    if($response == true) {
	        $this->emit("producto-repetido",$this->selected_id); 
	        return;
	    }
	    
	    $this->StoreProduct();
	}

    // Filtra por eliminado o activos 
    
	public function Filtro($estado)
	{
	   $this->estado_filtro = $estado;
	   
	}


	public function Sincronizar() {
	    
	    $products = Product::where('products.comercio_id', 'like', $this->casa_central_id)
		->where('products.eliminado', 'like', $this->estado_filtro)->where('eliminado',0)->get();
		
		foreach($products as $p) {
		    $p->wc_push = 1;
		    $p->save();
		}	
	}


	//Codigo entortno Testing:

	// IMAGENES

    // Modal para seccion imagenes    
    public function ModalImagenes() {        
        $this->ModalImagenesProduct();   
        
    }

	// cambio de tipo de carga de la imagen 
	public function TipoCargaImagen($id) {
		$this->TipoCargaImagenProduct($id);	
	}
		
	// Guardar una imagen nueva	
	public function AceptarSeleccionarImagen() {		
		$this->AceptarSeleccionarImagenProduct();			
	}

	// Selecciona una imagen de la biblioteca

	public function SeleccionarImagen($id) {
		$this->imagen_seleccionada = $id;		
	}
			
	// Asocia una imagen a un producto			
	public function GuardarImagenGaleria($product_id) {
		$this->GuardarImagenGaleriaProduct($product_id);
	}

	    // Desvincular una imagen de un producto 

	public function DestroyImage(Product $product)
	{	
		// $product = Product::find($product);		
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
		$this->base64 = null;
		$this->emit('product-deleted', 'Imagen Eliminada');
		} else {
			$this->image = $product->image;
		}	
	}

	public function DestroyImageBase64() {
		$this->base64 = null;	}
	
	///


	//Metodos en el trait:

	//guardarVariacion() Esta en el trait
	//ProductoTipo() esta en el trait
	//CambiarProductoTipo() 	
	//VolverProductoTipo()
	//GetStock()
	// GetPrecios()


    // 6-6-2024
	public function ModalMarca($value)
	{
		if($value == 'AGREGAR') {
		$this->emit('modal-marca-show', '');
		}
	}	

    //
    

}