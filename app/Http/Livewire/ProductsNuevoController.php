<?php

namespace App\Http\Livewire;


// Trait


use App\Traits\FacturacionNuevoAfip;

use App\Traits\WocommerceTrait;
use App\Traits\ProductsTraitNuevo;
use App\Traits\CartTrait;
use App\Traits\ProductsImagenesTrait;


// Modelos


use App\Models\ColumnConfiguration; // 6-6-2024

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

class ProductsNuevoController extends Component //Scaner //Component
{

	use WithPagination;
	use WithFileUploads;
	use CartTrait;
//	use WocommerceTrait;
	use ProductsTraitNuevo;
    use ProductsImagenesTrait;
    use FacturacionNuevoAfip;

    public $stock_variaciones = [];
    public $producto_variaciones;

	public $name,$barcode,$cost,$price, $agregar, $stock_origen, $style_tipo_2, $search_imagenes, $style_tipo_1, $id_check, $imagen_seleccionada, $accion_lote,$tipo_carga_imagen, $sucursal_destino, $cod_variacion, $tipo_producto, $stock,$alerts,$categoryid,$search,$image,$selected_id,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $inv_ideal, $proveedor, $cod_proveedor, $iva, $relacion_precio_iva, $proveedor_elegido, $wc_products, $ecommerce_canal, $wc_canal, $mostrador_canal, $name_almacen, $descripcion, $image_categoria, $name_categoria, $woocommerce,$costos_variacion, $variacion_atributo, $caja, $precio_lista, $stock_sucursal, $sucursal_id, $stock_sucursales,  $productos_lista_precios, $vista_id, $productos_stock_sucursales, $producto_tipo, $cart, $costo;
	public $id_almacen;
	public $id_categoria;
	public $id_proveedor;
	public $base64;
	private $pagination = 15;
	private $wc_product_id;
	
	public $lista_precios = [];
    public $SelectedProducts = [];
	public $selectedAll = FALSE;

	public $sortColumn = "name";
  public $sortDirection = "asc";

  protected $products;

    // 6-6-2024
    public $marca_id,$name_marca,$id_marca;
    
    public $mostrarFiltros = false;

    public function MostrarFiltro()
    {
        $this->mostrarFiltros = !$this->mostrarFiltros;
    }
    

	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function mount(Request $request)
	{
	    
	    $this->initializeVariables();
        
        // Carga de configuraciones generales y de productos
        $this->loadGeneralData();
        $this->loadProductData();

        // Determina si WooCommerce est¨¢ configurado
        $this->checkWooCommerce();

		// 13-9-2024
		$this->SetConfiguracionInsumoElegido($request);
		
		$this->loadColumns();
        
		$this->SetListaCostoDefecto();
		
		$this->GetUnidadMedida();
		
		$productSection = $request->input('view');
		
		$this->product_section = $productSection ?? 'Products';

		//$this->loadProducts();
	}

	public function render()
	{
	    //	'ProductsPrecio';
		//	'ProductsStock';
		//	'Products';
		    $productSection = $this->product_section;
		    
		    $this->loadProducts($productSection);
		    
            $this->loadSucursales();
            
            
            $this->getSubcategorias();
    		//Cart
    		$cart = new CartVariaciones;
    		$this->cart = $cart->getContent();
            
    		$this->etiqueta = $this->GetEtiquetas($this->comercio_id,"productos");
                
            $this->etiqueta_json = $this->GetEtiquetasJson($this->comercio_id,"productos");
            
			return view('livewire.products-nuevo.component', [
				'lista_precios' => $this->lista_precios,
				'lista_precios_reglas' => $this->lista_precios_reglas,
				'data' => $this->products,
				'cart' => $this->cart,
				'wc_yes' => $this->wc_yes,
				'variaciones' => $this->variaciones,
				'atributos_var' => $this->atributos_var,
				'comercio_id' => $this->comercio_id,
				'sucursales' => $this->sucursales,
				'marcas' => $this->marcas,
				'categories' => $this->categorias,
				'almacenes' => $this->almacenes,
				'prov' => $this->proveedores
			])
			->extends('layouts.theme-pos.app')
			->section('content');

}

    public function loadColumns()
    {
        $columns = ColumnConfiguration::where(['user_id' => Auth::id(), 'table_name' => 'products_precios'])
            ->pluck('is_visible', 'column_name')
            ->toArray();
    
        // AÃ±adir columnas dinÃ¡micas para cada lista de precios
        $listaPrecios = lista_precios::where('comercio_id',$this->casa_central_id)->get(); // Suponiendo que tienes un modelo ListaPrecio
        foreach ($listaPrecios as $list) {
            $columns['precio_' . $list->id] = $columns['precio_' . $list->id] ?? true; // Columna visible por defecto
        }
    
    
        // Todas las columnas disponibles
        $allColumns = [
            'costo' => true,
            'precio_interno' => true,
            'precio_base' => true,
        ];
    
        // Fusionar columnas personalizadas con todas las columnas disponibles
        $this->columns = array_merge($allColumns, $columns);
    }

    public function aplicarCambiosColumnas()
    {
        foreach ($this->columns as $column => $isVisible) {
            ColumnConfiguration::updateOrCreate(
                ['user_id' => Auth::id(), 'table_name' => 'products_precios', 'column_name' => $column],
                ['is_visible' => $isVisible]
            );
        }
    
        // Opcional: NotificaciÃ³n o redireccionamiento si quieres mostrar un mensaje de confirmaciÃ³n.
        //session()->flash('message', 'Cambios aplicados exitosamente.');
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

  
  	public function StoreProveedor()
    {
        $this->StoreProveedorProduct();
    }


	protected $listeners =[
	    'Swicth' => 'SwicthProducts',
		'deleteRow' => 'Destroy',
		'Base64' => 'seleccionarImagenBase64',
		'accion-lote' => 'AccionEnLote',
		'deleteVariacion' => 'DestroyVariacion',
		'ConfirmCheck' => 'DeleteSelected',
		'RestaurarProducto' => 'RestaurarProducto',
		'EtiquetasSeleccionadas',
        'SearchEtiquetas',
        'guardarDatos',
        'ObtenerReglaListaPrecios',
        'GuardarDescuento',
        'GuardarDescuentosEnLote'
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

    $this->emit('modal-export-stock-hide', '');

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

    // Eliminar un producto 
    
    public function Destroy(Product $product) {
	
    $this->DestroyProduct($product);

    }
    
    // Modal para agregar un producto 
    /*
    public function ModalAgregar() {

    $this->ModalAgregarProduct();
    
    }
    */
	
    public function Agregar() {
        
        $this->GetEtiquetasEdit(0,"gastos",$this->comercio_id);
	    $cart = new CartVariaciones;
		$cart->clear(); 
		$this->GetUnidadMedida();
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



    // 6-6-2024
	public function ModalMarca($value)
	{
		if($value == 'AGREGAR') {
		$this->emit('modal-marca-show', '');
		}
	}	
	public function ModalCategoria($value)
	{
		if($value == 'AGREGAR') {
		$this->emit('modal-categoria-show', '');
		}

	}

public function procesarSeleccionProductosPrecios() {
    foreach($this->id_check as $seleccion) {
        // Separar el tipo de producto (S o V) y el ID
        $tipo = substr($seleccion, 0, 1);
        $idProducto = substr($seleccion, 1);

        // Aqu¨ª puedes manejar el c¨®digo seg¨²n el tipo de producto
        if($tipo == 'V') {
            // L¨®gica para productos variables
            dd($idProducto);
        } else {
            // L¨®gica para productos simples
        }
    }
}

  //SET COMMERCIO ID
  public function setComercioId(){
    return Auth::user()->comercio_id == 1 ?  Auth::user()->id :  Auth::user()->comercio_id;
  }

    public function OcultarMostrarStock() {
    
    $this->emit('modal-stock-hide','');
    
    }
    
/*
public function CorroborarFacturaAFIP(){
    $cuit = 30716318792;
    $numero_de_factura = 1605;
    $punto_de_venta = 16;
    $this->CorroborarFactura($numero_de_factura,$punto_de_venta,$cuit);
}
*/

}


