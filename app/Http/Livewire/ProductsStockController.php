<?php

namespace App\Http\Livewire;

// Trait

use App\Traits\WocommerceTrait;
use App\Traits\ProductsTrait;
use App\Traits\CartTrait;
use App\Traits\ProductsImagenesTrait;

// Modelos


use App\Models\marcas;
use App\Http\Livewire\Scaner;
use App\Models\Category;
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

class ProductsStockController extends Component
{

	use WithPagination;
	use WithFileUploads;
	use CartTrait;
	use WocommerceTrait;
	use ProductsTrait;
    use ProductsImagenesTrait;

 

    // 6-6-2024
    public $marca_id,$name_marca,$id_marca;
    
	public $name,$barcode,$cost,$price, $stock_origen,$id_check, $accion_lote, $style_tipo_1, $search_imagenes, $imagen_seleccionada, $style_tipo_2, $tipo_carga_imagen, $sucursal_destino, $cod_variacion, $tipo_producto, $stock,$alerts,$categoryid,$search,$image,$selected_id,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $inv_ideal, $proveedor, $cod_proveedor, $iva, $relacion_precio_iva, $proveedor_elegido, $wc_products, $ecommerce_canal, $wc_canal, $mostrador_canal, $name_almacen, $descripcion, $image_categoria, $name_categoria, $woocommerce,$costos_variacion, $variacion_atributo, $caja, $precio_lista, $stock_sucursal, $sucursal_id, $stock_sucursales, $productos_lista_precios, $vista_id, $productos_stock_sucursales, $producto_tipo, $cart, $costo;
	public $id_almacen;
	public $id_categoria;
	public $id_proveedor;
	private $pagination = 15;
	private $wc_product_id;
	public $base64;
    public $agregar;
    
    public $producto_variaciones;
    public $stock_variaciones = [];
    
	public $SelectedProducts = [];
	public $selectedAll = FALSE;

	public $sortColumn = "name";
  public $sortDirection = "asc";

  protected $products;


	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}

    public $mostrarFiltros = false;

    public function MostrarFiltro()
    {
        $this->mostrarFiltros = !$this->mostrarFiltros;
    }
    
    
	public function mount(Request $request) // 13-9-2024
	{
	
	    // 18-1-2024
        $this->ver_configuracion = 0;
        $this->configuracion_precio_interno = 0;
        
        $this->estado_filtro = 0;
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
		$this->producto_tipo = 'Elegir';
		$this->accion_lote = 'Elegir';
		
		$this->GetDatosGenerales();
        $this->GetDatosFacturacionProduct();
        $this->GetCasaCentralId();
        $this->GetSucursalId();
        $this->GetAtributosYvariaciones();
      
        // 28-5-2024
        $this->GetConfiguracion();
        
		/*------------- VE SI TIENE WOCOMMERCE --------------*/

        $wc = wocommerce::where('comercio_id', $this->comercio_id)->first();

		if($wc == null){ $this->wc_yes = 0; } else { $this->wc_yes = $wc->id; }

		
		// 13-9-2024
		$this->SetConfiguracionInsumoElegido($request);
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
  
  


	public function render()
	{
            $this->GetDatosGeneralesSession();            
            set_time_limit(300);

			$productSection = 'ProductsStock';
			$this->renderProductsList($productSection);
		    $this->product_section = $productSection; 

			/*$products = Product::join('categories as c','c.id','products.category_id')
			->join('seccionalmacens as a','a.id','products.seccionalmacen_id')
			->join('proveedores as pr','pr.id','products.proveedor_id')
			->select('products.*','c.name as category','a.nombre as almacen','pr.nombre as nombre_proveedor')
			->where('products.comercio_id', 'like', $this->casa_central_id)
			->where('products.eliminado', 'like', $this->estado_filtro);

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
		
			if($this->tipo_usuario->sucursal != 1) {

			$this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
			->select('users.name','sucursales.sucursal_id')
			->where('casa_central_id', $this->comercio_id)
			->get();

			$this->stock_sucursales = productos_stock_sucursales::whereIn('product_id',$items_id)
			->select('productos_stock_sucursales.product_id','productos_stock_sucursales.sucursal_id', productos_stock_sucursales::raw('SUM(productos_stock_sucursales.stock) AS stock'))
			->groupBy('productos_stock_sucursales.product_id','productos_stock_sucursales.sucursal_id')
			->get();

			$this->lista_precios = lista_precios::where('comercio_id',$this->sucursal_id)->get();
	
			} else {

	    	$this->lista_precios = lista_precios::where('comercio_id',$this->casa_central_id)->get();

        	$this->stock_sucursales = productos_stock_sucursales::where('comercio_id', $this->casa_central_id)->whereIn('product_id',$items_id)
			->select('productos_stock_sucursales.product_id','productos_stock_sucursales.sucursal_id', productos_stock_sucursales::raw('SUM(productos_stock_sucursales.stock) AS stock'))
			->groupBy('productos_stock_sucursales.product_id','productos_stock_sucursales.sucursal_id')
			->get();

			$this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
			->select('users.name','sucursales.sucursal_id')
			->where('casa_central_id', $this->casa_central->casa_central_id)
			->get();

			}

	        // Trae las variaciones del carrito 
	        
    	    $cart = new CartVariaciones;
			$this->cart = $cart->getContent();
			*/
			
            
                    
            $this->etiqueta = $this->GetEtiquetas($this->comercio_id,"productos");
            
            $this->etiqueta_json = $this->GetEtiquetasJson($this->comercio_id,"productos");
            
			return view('livewire.products-stock.component', [
				'data' => $this->products,
				'cart' => $this->cart,
				'variaciones' => $this->variaciones,
				'atributos_var' => $this->atributos_var,
				'productos_variaciones' => $this->productos_variaciones,
				'wc_yes' => $this->wc_yes,
				'comercio_id' => $this->comercio_id,
				'sucursales' => $this->sucursales,
				'categories' => Category::orderBy('name','asc')->where('comercio_id', 'like', $this->sucursal_id)->orWhere('comercio_id', $this->casa_central_id)->get(),
				'marcas' => marcas::orderBy('name','asc')->where('eliminado', 0)->where( function($query) {
					 $query->where('comercio_id', 'like', $this->sucursal_id)->orWhere('comercio_id', $this->casa_central_id);
				})->get(),
				'almacenes' => seccionalmacen::orderBy('nombre','asc')->where('comercio_id', 'like', $this->sucursal_id)->orWhere('comercio_id', $this->casa_central_id)->get(),
				'prov' => proveedores::orderBy('nombre','asc')->where('comercio_id', 'like', $this->sucursal_id)->orWhere('comercio_id', $this->casa_central_id)->where('eliminado',0)->get()
			])
			->extends('layouts.theme-pos.app')
			->section('content');

}

	protected $listeners =[
	    'Swicth' => 'SwicthProducts',
		'deleteRow' => 'Destroy',
		'Base64' => 'Base64',
		'accion-lote' => 'AccionEnLote',
		'deleteVariacion' => 'DestroyVariacion',
		'ConfirmCheck' => 'DeleteSelected',
		'RestaurarProducto' => 'RestaurarProducto',
		'EtiquetasSeleccionadas',
        'SearchEtiquetas',
	];
	
    public function RestaurarProducto(Product $product) {
    
    $this->RestaurarProductoProduct($product);

    }


    public function ExportarCatalogo() {
    
    $this->SwitchExportarCatalogos();
    
    }

   public function ExportarLista($lista_id) {

    
    $this->emit('modal-export-listas-hide', '');
 
    return redirect('lista-precios/excel/'. Carbon::now()->format('d_m_Y_H_i_s') .'/'. $lista_id);
 
    }


    public function ExportarStock($sucursal_id) {

    $this->emit('modal-export-stocks-hide', '');

    return redirect('stock-sucursal/excel/' . Carbon::now()->format('d_m_Y_H_i_s') . '/' . $sucursal_id);

    }

	

    // Eliminar en lote 
    
    public function AccionEnLote($ids, $id_accion)
    {
    
    $this->AccionEnLoteProduct($ids, $id_accion);
    
    }
    
    // Mostrar el stock de productos variables en un nuevo modal 
    
    public function MostrarStock($product_id,$sucu_id) {
    
    $this->MostrarStockProduct($product_id,$sucu_id);
    
    }
    
    public function OcultarMostrarStock() {
    
    $this->emit('modal-stock-hide','');
    
    }

    // Eliminar un producto 
    
    public function Destroy(Product $product) {
	
    $this->DestroyProduct($product);

    }
    
    // Modal para agregar un producto 
    
    public function ModalAgregar() {

    $this->ModalAgregarProduct();
    
    }
	
    public function Agregar() {
        
        $this->GetEtiquetasEdit(0,"gastos",$this->comercio_id);
	    $cart = new CartVariaciones;
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
		$this->emit('product-deleted', 'Imagen Eliminada');
	} else {
		$this->image = $product->image;
	}

}


///

public $testGuardarvariacionID;

//SELECT * FROM `productos_variaciones_datos` where eferencia_variacion like '09062023172704-178';
//SELECT * FROM `productos_variaciones` where referencia_id like '09062023181719-178';

public function GuardarVariacion() {

	$cart = new CartVariaciones;
	$this->referencia_id = Carbon::now()->format('dmYHis').'-'. $this->comercio_id;

	//return dd($this->referencia_id);

	$v_arr = [];
	$v_id_arr = [];
	$v_id_arr = [];

	$productVariacionesCreateDB = [];
 
	//return dd($this->cod_variacion);

	$this->cod_variacion[$this->referencia_id] = '';

	//return dd($this->cod_variacion);
	
	if($this->variacion_atributo != "c") {
				
		foreach ($this->variacion_atributo as $key => $value) {	
			
				$var_arr = variaciones::find($this->variacion_atributo[$key]);
				if($var_arr !== null){
					$var_arr = $var_arr->nombre;
					$var_id_arr = $this->variacion_atributo[$key];
					
					$productVariacionesCreateDB = array(
						'atributo_id' => $key,
						'variacion_id' => $this->variacion_atributo[$key],
						'comercio_id' => $this->comercio_id,
						'referencia_id' => $this->referencia_id,
					);


					array_push($v_arr,$var_arr);
					array_push($v_id_arr,$var_id_arr);
				}
			}
			if($v_arr !== []){					
				$v_arr = implode(" - " , $v_arr);
				natsort($v_id_arr); 
				$v_id_arr = implode("," , $v_id_arr);

				$product = array(
				'referencia_id' => $this->referencia_id,
				'var_nombre' => $v_arr,
				'var_id' => $v_id_arr,
				'product_variacion_create_db'=> $productVariacionesCreateDB
				);

				
				$cart->addProduct($product);	
		}	
		$this->testGuardarReferernciaID = $this->referencia_id;
	} else {
		dd("Debe agregar al menos una variacion");
	}
}

public function ProductoTipo() {
  	    
	

	if($this->producto_tipo == "s") {

	
	// Comprueba si es editar un producto ya existente
	
	if(0 < $this->selected_id) {
		 //$this->emit('cambiar-tipo-producto','');
		 $this->CambiarProductoTipo();
		 $cart = new CartVariaciones;
		 $cart->clear();
		  return;
	}
	
  // ESTABLECER EL STOCK PREDETERMINADO CON 0 ANTES DE AGREGAR //

  $this->productos_stock_sucursales = sucursales::where('casa_central_id', $this->casa_central_id)->get();

		  $this->stock_sucursal["0|0|0|0"] = 0;
	 foreach($this->productos_stock_sucursales as $llave => $sucu) {
		  $this->stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] = 0;
	  }
	  
	  // ESTABLECER EL PRECIO PREDETERMINADO EN 0 ANTES DE AGREGAR //

	} 
	if($this->producto_tipo == "v") {
		
		// Comprueba si es editar un producto ya existente
			
			if(0 < $this->selected_id) {
		$this->emit('cambiar-tipo-producto','');
		return;
			}
		
	}
	
}

	public function CambiarProductoTipo() {		 
		
		// si el producto tipo nuevo es simple ---> el anterior era variable
		if($this->producto_tipo == "s") {
			
			
			$pvd = productos_variaciones_datos::where('product_id',$this->selected_id)->where('eliminado',0)->get();
			//return dd($pvd[0]->referencia_variacion);
			foreach($pvd as $pv) {
				$this->DestroyVariacion($pv->referencia_variacion);
			}
		}		
		//dd($stocks, $precios);		
	}

	public function VolverProductoTipo() {
		dd('volver');
	}

	public function GetStock($product_id) {
        
		$p = productos_stock_sucursales::where('product_id',$product_id)->get();
	//	dd($p);
		return $p;
			
		}
		
		public function GetPrecios($product_id) {
			
		return productos_lista_precios::where('product_id',$product_id)->get();
		
			
		}


    // 6-6-2024
	public function ModalMarca($value)
	{
		if($value == 'AGREGAR') {
		$this->emit('modal-marca-show', '');
		}
	}	

    //
}
