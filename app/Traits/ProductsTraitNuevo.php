<?php
namespace App\Traits;


// Trait

use App\Traits\WocommerceTrait;
use App\Traits\ConfiguracionProductsTrait;
use App\Traits\CartTrait;
use App\Traits\EtiquetasTrait;
//use App\Traits\ListaPreciosTrait;
use App\Traits\ListaPreciosTraitNuevo;
use App\Traits\RecetasTrait; // 29-8-2024

// Modelos

use App\Models\lista_precios_reglas; // 29-8-2024 -- Actualizacion lista precios

use App\Models\SaleDetail; // 29-8-2024
use App\Models\marcas; // 6-6-2024

use App\Models\Subcategoria;

use App\Models\productos_descuentos; // Actualizacion descuentos
use App\Models\listas_descuentos; // Actualizacion descuentos


use App\Models\provincias;
use App\Models\paises;
use App\Models\etiquetas;
use App\Models\productos_ivas; // 19-3-2024
use App\Models\tipo_unidad_medida; // 28-5-2024
use App\Models\lista_precios;
use App\Models\Product;
use App\Models\Category;
use App\Models\proveedores;
use App\Models\wocommerce;
use App\Models\atributos;
use App\Models\imagenes;
use App\Models\descargas;
use App\Models\variaciones;
use App\Models\productos_variaciones_datos;
use App\Models\ClientesMostrador;
use App\Models\productos_variaciones;
use App\Models\receta;
use App\Models\User;
use App\Models\sucursales;
use App\Models\productos_stock_sucursales;
use App\Models\productos_lista_precios;
use App\Models\actualizacion_precios;
use App\Models\historico_stock;
use App\Models\seccionalmacen;
use App\Models\datos_facturacion;

use App\Models\unidad_medida; // 28-8-2024
use App\Models\unidad_medida_relacion; // 28-8-2024



// services 

use App\Services\CartVariaciones;

// Otros

use Illuminate\Support\Facades\Storage;
use Notification;
use App\Notifications\NotificarCambios;
use Illuminate\Validation\Rule;
use DB;
use Intervention\Image\Facades\Image;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use Carbon\Carbon;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;


//Codigo entorno testing:
//Common Trait
use App\Traits\CommonTrait;

//Validator
use Illuminate\Support\Facades\Validator;

use App\Jobs\ExportProductsJob; // 5-9-2024

trait ProductsTraitNuevo {


	//Codigo entorno testing:
	use CommonTrait;
	use EtiquetasTrait;
	use ConfiguracionProductsTrait;
	//use ListaPreciosTrait;
	use ListaPreciosTraitNuevo;
	use WocommerceTrait;
	use RecetasTrait; // 29-8-2024
    
    public $descuentos_variaciones = []; // Define el array para guardar los descuentos con la variación como índice

    public $list;
    private $wc_category;
    public $precios_internos_variacion;
    public $configuracion_precio_interno;
    
    public $provincias = [];
    public $paises = [];
    public $pais_proveedor;
    public $altura_proveedor, $piso_proveedor, $search_etiqueta,$codigo_postal_proveedor, $depto_proveedor, $id_proveedor;
    
    public $mostrarDiv = false;
    
    // 25-10-2024
    public $subcategorias = [];
    public $subcategoria_id;
    //
        
    public $id_marca;
    public $error_variacion = '';
    public $mostrarErrorVariacion = false;
    public $mostrarErrorTipoProducto = false;
    
    public $etiquetas_seleccionadas = [];
    public $id_etiquetas_seleccionadas = [];
    public $porcentaje_iva = [];
    public $nombre_proveedor;
    public $direccion_proveedor;
    public $localidad_proveedor;
    public $provincia_proveedor;
    public $telefono_proveedor;
    public $mail_proveedor;
    public $es_sucursal;
    public $precio_interno;
    
    public $almacen_id;
    public $real_stock_sucursal;
    public $stock_sucursal_comprometido;
    public $nombre_lista;
    public $product_section;
    public $wc_key_lista;
    public $descripcion_lista;
    public $forma_edit;
    
    public $base_64_archivo,$base_64_nombre;
    
    public $descuento_costo,$costo_despues_descuento;
    

	//Codigo entorno testing:
	protected $erroresCodigoVariaciones;

    // 28-5-2024 ---> Modificacion de codigos
    
    public $numeros_prefijo,$numeros_codigo,$numeros_peso, $tipo_unidad_medida, $cantidad_unidad_medida;
    
        // 22-9-2024
    public $descuentos_productos = [];

    // 29-8-2024  -- Actualizacion de precios
    public $regla_precio_interno;
    public $regla_precio_base;
    public $regla_precio = [];
    public $porcentaje_regla_precio = [];
    //
    public $marcas,$categorias,$almacenes,$proveedores;
    
    public $es_insumo, $es_insumo_elegido, $es_insumo_elegido_url;
    
    public $porcentaje_regla_precio_interno;
    public $variacion_elegida_modal_descuento;
    
    public $es_descuento_individual; // 26-9-2024 

    // Inicializar variables
    private function initializeVariables()
    {
        $this->ver_configuracion = 0;
        $this->configuracion_precio_interno = 0;
        $this->estado_filtro = 0;
        $this->metodo_pago = session('MetodoPago');
        $this->pageTitle = 'Listado';
        $this->proveedor = '1';
        $this->componentName = 'Productos';
        $this->categoryid = 1;
        $this->subcategoria_id = 1;
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
    }
    
    // Carga de datos generales
    private function loadGeneralData()
    {
        $this->comercio_id = Auth::user()->comercio_id != 1 ? Auth::user()->comercio_id : Auth::user()->id;
        $this->tipo_usuario = User::find($this->comercio_id);
        $this->sucursal_id = $this->comercio_id;
        $this->casa_central_id =  Auth::user()->casa_central_user_id;

        // Configuración adicional
        $this->tipo_unidad_medida = 9;
        $this->cantidad_unidad_medida = 1;
        $this->tipo_producto = 1;
        $this->marca_id = 1;
        
        // Cargar configuraciones de listas de precios
        $this->GetConfiguracionListaPrecios();
        $this->GetReglaListaPrecios();
        $this->GetDatosReglasPreciosMount();
        $this->GetConfiguracion();
        $this->IniciarDescuento();
        $this->getSubcategorias();
    }
    // Obtener el ID de comercio relevante
    private function getRelevantComercioId()
    {
        return $this->tipo_usuario->sucursal != 1 ? $this->comercio_id : $this->casa_central_id;
    }

    // Verificar si WooCommerce está configurado
    private function checkWooCommerce()
    {
        $wc = wocommerce::where('comercio_id', $this->comercio_id)->first();
        $this->wc_yes = $wc ? $wc->id : 0;
    }
    private function loadMarcas(){
        $this->marcas = marcas::where('comercio_id', $this->getRelevantComercioId())
            ->where('eliminado', 0)
            ->orderBy('name', 'asc')
            ->get();        
    }
    private function loadCategorias(){
        $this->categorias = Category::where('comercio_id', $this->getRelevantComercioId())
            ->orderBy('name', 'asc')
            ->get();        
    }
    private function loadAlmacenes(){
        $this->almacenes = seccionalmacen::where('comercio_id', $this->getRelevantComercioId())
            ->orderBy('nombre', 'asc')
            ->get();
    }
    private function loadProveedores(){
        $this->proveedores = proveedores::where('comercio_id', $this->getRelevantComercioId())
            ->orderBy('nombre', 'asc')
            ->get();        
    }
    // Carga de datos relacionados con productos
    private function loadProductData()
    {
        $this->loadMarcas();
    
        $this->loadCategorias();
        
        $this->loadAlmacenes();
    
        $this->loadProveedores();
        
        $this->iva_defecto = 0;
        
        $this->atributos_var = [];
        $this->variaciones = [];
        $this->productos_variaciones = [];
    }    

    public function loadProducts($productSection)
    {
        // Cargar productos con los filtros iniciales
        $this->products = Product::select('products.*','proveedores.nombre as nombre_proveedor','marcas.name as nombre_marca')
            ->join('proveedores','proveedores.id','products.proveedor_id')
            ->join('marcas','marcas.id','products.marca_id')
            ->where('products.comercio_id', $this->casa_central_id)
            ->where('products.eliminado', $this->estado_filtro);

        // Aplicar otros filtros
        $this->products = $this->RenderFiltrar($this->products, $this->id_categoria, $this->id_almacen, $this->proveedor_elegido, $this->etiquetas_filtro, $this->id_marca, $this->es_insumo_elegido);
        
         
		if(strlen($this->search) > 0) {

		$this->products = $this->products->where( function($query) {
				 $query->where('products.name', 'like', '%' . $this->search . '%')
					->orWhere('products.barcode', 'like',$this->search . '%');
				});
		}

        // Ordenar y paginar productos
        $this->products = $this->products->orderBy($this->sortColumn, $this->sortDirection)->paginate($this->pagination);

	    // Obtener IDs de los productos paginados
        $items_id = $this->products->pluck('id');

        if($productSection == "ProductsPrecio"){
        $this->productos_lista_precios = productos_lista_precios::whereIn('product_id',$items_id)->where('eliminado',0)->get();    
        }
		
		if($productSection === 'ProductsStock'){
		$this->stock_sucursales = productos_stock_sucursales::whereIn('product_id',$items_id)
		->where('productos_stock_sucursales.eliminado',0)
		->select('productos_stock_sucursales.product_id','productos_stock_sucursales.sucursal_id', productos_stock_sucursales::raw('SUM(productos_stock_sucursales.stock) AS stock'),productos_stock_sucursales::raw('SUM(productos_stock_sucursales.stock_real) AS stock_real'))
		->groupBy('productos_stock_sucursales.product_id','productos_stock_sucursales.sucursal_id')
		->get();
		}

    }    

    public function SetConfiguracionInsumoElegido($request){
		$es_insumo_elegido = $request->input('tipo');
		$this->es_insumo_elegido  = $es_insumo_elegido ?? null;
		$this->es_insumo_elegido_url = $es_insumo_elegido ?? null;
		if($es_insumo_elegido == "insumo"){
		    $this->es_insumo = 1;
		    $this->es_insumo_elegido = 1;
		    $this->es_insumo_elegido_url = 1;
		} else {
		    $this->es_insumo = 0;
		//    $this->es_insumo_elegido = 0;
		    $this->es_insumo_elegido_url = 0;
		    $this->es_insumo_elegido = 2;
    	}       
    }
    
    
    ///////////////// FUNCIONES QUE SEGURO SE REPITEN EN EL CART TRAIT (VER Y ADAPTAR A LO QUE HAY EN EL CART TRAIT) ///////////////////////
    
    /*
    public function GetDatosGenerales() {
    
    if(Auth::user()->comercio_id != 1)
    $this->comercio_id = Auth::user()->comercio_id;
    else
    $this->comercio_id = Auth::user()->id;
    
    //$this->ObtenerPorcentajesTodos();
    
    $this->tipo_usuario = User::find($this->comercio_id);
    $this->sucursal_id = $this->comercio_id;
    		    
    if($this->tipo_usuario->sucursal != 1) {
    
    $this->casa_central_id = $this->comercio_id;
    	
    } else {
    	  
    $this->casa_central = sucursales::where('sucursal_id', $this->comercio_id)->first();
    $this->casa_central_id = $this->casa_central->casa_central_id;
    
    }
    
    session(['casa_central_id' => $this->casa_central_id]);
    session(['sucursal_id' => $this->sucursal_id]);
    session(['comercio_id' => $this->comercio_id]);

    //	28-5-2024
    $this->tipo_unidad_medida = 9;
    $this->cantidad_unidad_medida = 1;
    $this->tipo_producto = 1;
    $this->marca_id = 1;
    
    // 15-8-2024
    $this->GetConfiguracionListaPrecios();
    
    // 29-8-2024  -- Actualizacion de precios
     $this->GetReglaListaPrecios();
     
    $this->GetDatosReglasPreciosMount();

    // 25-9-2024
    $this->IniciarDescuento();
    
    $this->id_check = [];    

	$this->marcas = marcas::orderBy('name','asc')->where('eliminado', 0)->where( function($query) {
			 $query->where('comercio_id', 'like', $this->sucursal_id)->orWhere('comercio_id', $this->casa_central_id);
	})->get();
	$this->categorias = Category::orderBy('name','asc')->where('comercio_id', 'like', $this->sucursal_id)->orWhere('comercio_id', $this->casa_central_id)->get();
	$this->almacenes = seccionalmacen::orderBy('nombre','asc')->where('comercio_id', 'like', $this->sucursal_id)->orWhere('comercio_id', $this->casa_central_id)->get();
	$this->proveedores = proveedores::orderBy('nombre','asc')->where('comercio_id', 'like', $this->sucursal_id)->orWhere('comercio_id', $this->casa_central_id)->get();

    }
    */
    
    // 29-8-2024  -- Actualizacion de precios
    public function GetDatosReglasPreciosMount(){
    
    $this->regla_precio_interno = 1;
    $this->porcentaje_regla_precio_interno = 0;
    $this->porcentaje_regla_precio_interno_variacion = [];
    
    $this->regla_precio[0] = 1;
    $this->porcentaje_regla_precio[0] = 0;

    // tomamos las listas de precios
    $lista_de_precios = $this->GetReglaListaPrecios($this->casa_central_id);
   
    // seteamos el precio ---> aca tenemos que setear con las reglas de la lista de precios
    if($lista_de_precios->count() > 0){
        // Inicializamos los valores
        foreach ($lista_de_precios as $lp) {
            $this->regla_precio[$lp->lista_id] = $lp->regla;
            $this->porcentaje_regla_precio[$lp->lista_id] = 0; // Valor inicial para el porcentaje
        }
    }
    }
    
    public function GetUnidadMedida() {
	$this->unidades_de_medida = tipo_unidad_medida::join('unidad_medidas','unidad_medidas.tipo_unidad_medida','tipo_unidad_medidas.id')
	->select('unidad_medidas.*','tipo_unidad_medidas.nombre as nombre_tipo_unidad_medida')
	->get();
    }
    
    public function SetListaCostoDefecto(){
    if($this->casa_central_id != $this->comercio_id){
    $sucursal = sucursales::where('sucursal_id',$this->comercio_id)->first();
    $cliente = ClientesMostrador::where('sucursal_id',$sucursal->id)->first();
	$this->lista_costo_defecto = $cliente ? $cliente->lista_precio : 0;
    } else {
    $this->lista_costo_defecto = 0;    
    }        
    }
    
    public function GetDatosFacturacionProduct() {

    // VER ESTA REPETIDO
    
    $this->datos_facturacion = datos_facturacion::where('comercio_id',$this->sucursal_id)->first();
    
    if($this->datos_facturacion != null) {
    $this->iva_defecto = 	$this->datos_facturacion->iva_defecto;
    } else {
    $this->iva_defecto = 0;
    }   

    }
    
    // determina el ID de la casa central
    
    public function GetCasaCentralId() {
        
        if($this->tipo_usuario->sucursal != 1) {
    
    	$this->casa_central_id = $this->comercio_id;
    
    	$this->lista_precios = lista_precios::where('comercio_id',$this->sucursal_id)->where('eliminado',0)->get();
    
    	} else {
    		    
    	$this->casa_central = sucursales::where('sucursal_id', $this->comercio_id)->first();
    	$this->casa_central_id = $this->casa_central->casa_central_id;
    
    	$this->lista_precios = lista_precios::where('comercio_id',$this->casa_central_id)->where('eliminado',0)->get();
    
    	}
    }
    
    // determina el ID de la sucursal
    
    public function GetSucursalId() {
    	$this->sucursal_id = $this->comercio_id;
    }
    
    // trae todos los atributos y variacioens 
    
    public function GetAtributosYvariaciones() {
      
    $this->atributos_var = atributos::where('comercio_id', $this->comercio_id)->get();
    
    $this->variaciones = variaciones::join('atributos','atributos.id','variaciones.atributo_id')
    ->select('variaciones.*','atributos.nombre as atributo')
    ->where('atributos.comercio_id', $this->comercio_id)->get();
    
    /*
    $this->productos_variaciones = productos_variaciones::join('variaciones','variaciones.id','productos_variaciones.variacion_id')
    ->select('variaciones.nombre as nombre_variacion','productos_variaciones.*')
    ->where('variaciones.comercio_id', $this->comercio_id)->get();
    */			
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    
    // FUNCIONES SOLO DE PRODUCTOS 
    public function ValidarNombreRepetido($selected_id) {
        
		//Cod entorno testing
		$comercio_id = $this->setComercioId();
		
		if(0 < $selected_id) {
		$p = Product::where('name',$this->name)->where('eliminado',0)->where('id','<>',$selected_id)->where('comercio_id',$comercio_id)->exists();    
		} else {
		$p = Product::where('name',$this->name)->where('eliminado',0)->where('comercio_id',$comercio_id)->exists();
		}
	    return $p;
		
    }
    
public function ValidarCodigos($origen, $barcode, $cod_proveedor, $casa_central_id)
{
    // Check for 'store' scenario
    if ($origen == "store") {
        $exist_barcode = Product::where('barcode', $barcode)
            ->where('comercio_id', $casa_central_id)
            ->where('eliminado', 0)
            ->exists();
       
        $exist_codigo_proveedor = Product::where('cod_proveedor', $cod_proveedor)
            ->whereNotNull('cod_proveedor')
            ->where('comercio_id', $casa_central_id)
            ->where('eliminado', 0)
            ->exists();

        if ($exist_barcode) {
            return "Ya existe el SKU en otro producto";
        }

        if ($exist_codigo_proveedor) {
            return "Ya existe el código de proveedor en otro producto";
        }
    }

    // Check for 'update' scenario
    if ($origen == "update") {
        $exist_barcode = Product::where('barcode', $barcode)
            ->where('comercio_id', $casa_central_id)
            ->where('eliminado', 0)
            ->where('id', '!=', $this->selected_id) // Exclude the current product
            ->exists();

        $exist_codigo_proveedor = Product::where('cod_proveedor', $cod_proveedor)
            ->whereNotNull('cod_proveedor')
            ->where('comercio_id', $casa_central_id)
            ->where('eliminado', 0)
            ->where('id', '!=', $this->selected_id) // Exclude the current product
            ->exists();

        if ($exist_barcode) {
            return "Ya existe el SKU en otro producto";
        }

        if ($exist_codigo_proveedor) {
            return "Ya existe el código de proveedor en otro producto";
        }
    }
    
    return false;
}

	
	// 29-8-2024 -- Actualizacion de precios
    public function GetReglaListaPrecios(){
    return $this->lista_precios_reglas = lista_precios_reglas::where('comercio_id',$this->comercio_id)->get();    
    }

    public function ObtenerReglaListaPrecios()
    {
        $this->lista_precios_reglas = lista_precios_reglas::where('comercio_id', $this->comercio_id)->get();

        // Emitir los datos actualizados al frontend
        $this->emit('actualizarReglasPrecio', $this->lista_precios_reglas->pluck('regla', 'lista_id')->toArray());
    }
    
    // 29-8-2024 -- Actualizacion de precios
    public function UpdateRegla($lista_id,$reglaNueva){
        
        if($lista_id == 1){
                $productos_simple = Product::where('comercio_id',$this->comercio_id)->get();  
                $productos_variables = productos_variaciones_datos::where('comercio_id',$this->comercio_id)->get(); 
                foreach($productos_simple as $ps){
                    $ps->regla_precio_interno = $reglaNueva;
                    $ps->save();
                }
                foreach($productos_variables as $pv){
                    $pv->regla_precio_interno = $reglaNueva;
                    $pv->save();
                }
            } else {
            $producto_lista_precios = productos_lista_precios::where('lista_id',$lista_id)->where('comercio_id',$this->comercio_id)->get();
            foreach($producto_lista_precios as $plp){
                $productos_lista_precios = productos_lista_precios::find($plp->id);
                $productos_lista_precios->regla_precio = $reglaNueva;
                $productos_lista_precios->save();
            }            
        }
        

        
        lista_precios_reglas::updateOrCreate(
            [
            'lista_id' => $lista_id,
            'comercio_id' => $this->comercio_id
            ],
            [
            'regla' => $reglaNueva,
            'lista_id' => $lista_id,
            'comercio_id' => $this->comercio_id
            ]);
        
        $this->GetReglaListaPrecios(); 
        $this->emit('product-updated', 'Regla actualizada');
    }
    
    
    // 29-8-2024 -- Actualizacion de precios
    public function updatePorcentajeRegla($id,$variacion, $reglaActual)
    {
        $reglaActual = trim($reglaActual);
        $reglaActual = $this->convertirFormatoMoneda($reglaActual);
        
        if($reglaActual < 0){
            $this->emit("msg-error","El valor debe ser positivo");
            return;
        }
        
        if(!is_numeric($reglaActual)){
            $this->emit("msg-error","El valor debe ser un numero");
            return;
        }
        
        if(empty($reglaActual) || $reglaActual == null || $reglaActual == ""){
            $this->emit("msg-error","El valor no puede estar vacio");
            return;
        }
        
        // Buscar el producto y actualizar su precio
        $producto_lista_precios = productos_lista_precios::find($id);
        $product = Product::find($producto_lista_precios->product_id);

        
        if ($producto_lista_precios) {
            $porcentaje_nuevo = $reglaActual/100;
            
            if($product->producto_tipo == "s"){
            $costo = $product->cost;
            $descuento_costo = $product->descuento_costo;
            $costo = $costo * (1 - $descuento_costo);
            } else {
            //dd($variacion,$product->id);
            $pvd = productos_variaciones_datos::where('referencia_variacion',$variacion)->where('product_id',$product->id)->where('eliminado',0)->first();    
            $costo = $pvd->cost;
            $descuento_costo = $pvd->descuento_costo;
            $costo = $costo * (1 - $descuento_costo);
            }
            $precio_anterior = $producto_lista_precios->precio_lista;
            $precio_nuevo = $costo * (1 + $porcentaje_nuevo);
            $producto_lista_precios->precio_lista = $costo * (1 + $porcentaje_nuevo);
            $producto_lista_precios->porcentaje_regla_precio = $porcentaje_nuevo;
            $producto_lista_precios->save();
            
            $this->HistoricoActualizacionPrecios($product->id,$variacion,$producto_lista_precios->lista_id,$precio_anterior,$precio_nuevo,$this->casa_central_id,Auth::user()->id);
        
            $this->ActualizarRecetaDeProductos($producto_lista_precios->product_id,$producto_lista_precios->referencia_variacion,$producto_lista_precios->comercio_id); // ---> VER ESTO
    
            // Mensaje de éxito
            $this->emit('product-updated', 'Porcentaje actualizado');
        } else {
            // Mensaje de error si el producto no se encuentra
            $this->emit("msg-error","Se ha producido un error");
        }
    }
    
    public function updatePorcentajeReglaPrecioInterno($id,$variacion, $reglaActual)
    {
        //dd($reglaActual);
        $reglaActual = trim($reglaActual);
        $reglaActual = $this->convertirFormatoMoneda($reglaActual);
        
        if($reglaActual < 0){
            $this->emit("msg-error","El valor debe ser positivo");
            return;
        }
        
        if(!is_numeric($reglaActual)){
            $this->emit("msg-error","El valor debe ser un numero");
            return;
        }
        
        if(empty($reglaActual) || $reglaActual == null || $reglaActual == ""){
            $this->emit("msg-error","El valor no puede estar vacio");
            return;
        }
        
        // Buscar el producto y actualizar su precio
        $product = Product::find($id);
        $porcentaje_nuevo = $reglaActual/100;
        
        if($product->producto_tipo == "s"){
        $costo = $product->cost;
        $descuento_costo = $product->descuento_costo;
        $costo = $costo * (1 - $descuento_costo);
        $precio_anterior = $product->precio_interno;
        $precioActual = $costo * (1 + $porcentaje_nuevo);
        $product->precio_interno = $costo * (1 + $porcentaje_nuevo);
        $product->porcentaje_regla_precio_interno = $porcentaje_nuevo;
        $product->save();        
        
        $this->HistoricoActualizacionPrecios($product->id,0,1,$precio_anterior,$precioActual,$this->casa_central_id,Auth::user()->id);
        } else {
        $pvd = productos_variaciones_datos::where('referencia_variacion',$variacion)->where('product_id',$product->id)->where('eliminado',0)->first();    
        $precio_anterior = $pvd->precio_interno;
        $costo = $pvd->cost;        
        $descuento_costo = $pvd->descuento_costo;
        $costo = $costo * (1 - $descuento_costo);
        $precioActual = $costo * (1 + $porcentaje_nuevo);
        $pvd->precio_interno = $costo * (1 + $porcentaje_nuevo);
        $pvd->porcentaje_regla_precio_interno = $porcentaje_nuevo;
        $pvd->save(); 
        
        $this->HistoricoActualizacionPrecios($product->id,$variacion,1,$precio_anterior,$precioActual,$this->casa_central_id,Auth::user()->id);
        }

        $this->ActualizarRecetaDeProductos($product->product_id,$product->referencia_variacion,$product->comercio_id); // ---> VER ESTO
    
        // Mensaje de éxito
        $this->emit('product-updated', 'Porcentaje actualizado');

    }
    
    
    
    // 29-8-2024 -- Actualizacion de precios
    public function ActualizarListasPorRegla($product_id,$variacion,$costoNuevo){
        
        $costoNuevo = trim($costoNuevo);
        $costoNuevo = $this->convertirFormatoMoneda($costoNuevo);
        
        $plp = productos_lista_precios::where('product_id',$product_id)->where('referencia_variacion',$variacion)->where('eliminado',0)->get();
       
        foreach($plp as $p){
        $precio_anterior = $p->precio_lista;
        if($p->regla_precio == 2){
            $margen = $p->porcentaje_regla_precio;    
            $precioActual = $costoNuevo * (1 + $margen);
            $this->updatePrice($p->id,$variacion, $precioActual); 
        
        }
        if($p->regla_precio == 1){
            $precio_lista = $p->precio_lista;   
            if($costoNuevo != 0){
            $margen_nuevo = ($precio_lista/$costoNuevo) - 1;    
            } else {
            $margen_nuevo = 0;    
            }
            $p->porcentaje_regla_precio = $margen_nuevo;    
            $p->save();
        }
        }
        
    }
    
    
    // 29-8-2024 -- Actualizacion de precios
    public function updatePrice($id,$variacion,$precioActual)
    {
        $precioActual = trim($precioActual);
        $precioActual = $this->convertirFormatoMoneda($precioActual);
        
        if($precioActual < 0){
            $this->emit("msg-error","El valor debe ser positivo");
            return;
        }
        
        if(!is_numeric($precioActual)){
            $this->emit("msg-error","El valor debe ser un numero");
            return;
        }
        
        if(empty($precioActual) || $precioActual == null || $precioActual == ""){
            $this->emit("msg-error","El valor no puede estar vacio");
            return;
        }
        
        // Buscar el producto y actualizar su precio
        $producto = productos_lista_precios::find($id);
        
        if ($producto) {
            $precio_anterior = $producto->precio_lista;
            $producto->precio_lista = $precioActual;
            $product = Product::find($producto->product_id);
            if($product->producto_tipo == "s"){
                $costo = $product->cost;
                $descuento_costo = $product->descuento_costo;
                $costo = $costo * (1 - $descuento_costo);
            } else {
                $pvd = productos_variaciones_datos::where('referencia_variacion',$variacion)->where('product_id',$product->id)->where('eliminado',0)->first();
                $costo = $pvd->cost;
                $descuento_costo = $pvd->descuento_costo;
                $costo = $costo * (1 - $descuento_costo);
            }
            
            if ($costo != 0) {
            $margen = ($precioActual / $costo) - 1;
            $producto->porcentaje_regla_precio = $margen;                
            } else {
            $producto->porcentaje_regla_precio = 0;     
            }

            $producto->save();
            
            $this->HistoricoActualizacionPrecios($product->id,$variacion,$producto->lista_id,$precio_anterior,$precioActual,$this->casa_central_id,Auth::user()->id);
            
            //	29-8-2024
            $this->ActualizarRecetaDeProductos($producto->product_id,$producto->referencia_variacion,$producto->comercio_id); // ---> VER ESTO
    
            // Mensaje de éxito
            $this->emit('product-updated', 'Precio actualizado');
        } else {
            // Mensaje de error si el producto no se encuentra
            $this->emit("msg-error","Se ha producido un error");
        }
    }
    
    public function updatePrecioInterno($id,$variacion, $costoNuevo)
    {
        
        $costoNuevo = trim($costoNuevo);
        $costoNuevo = $this->convertirFormatoMoneda($costoNuevo);
        
        if($costoNuevo < 0){
            $this->emit("msg-error","El valor debe ser positivo");
            return;
        }
        
        if(!is_numeric($costoNuevo)){
            $this->emit("msg-error","El valor debe ser un numero");
            return;
        }
        
        if(empty($costoNuevo) || $costoNuevo == null || $costoNuevo == ""){
            $this->emit("msg-error","El valor no puede estar vacio");
            return;
        }
        
        if($variacion == 0){
        // Buscar el producto y actualizar su precio
        $product = Product::find($id);
        $producto = $product;
        $product_id = $producto->id;
        $comercio_id = $producto->comercio_id;
        } else {
        $producto = productos_variaciones_datos::find($id);  
        $product_id = $producto->product_id;
        $comercio_id = $producto->comercio_id;
        }

        if ($producto) {
            $costo = $producto->cost;
            $precio_anterior = $producto->precio_interno;
            $producto->precio_interno = $costoNuevo;
            $producto->save();
            $margen = ($costoNuevo / $costo) - 1;
            $producto->porcentaje_regla_precio_interno = $margen;
            $producto->save();           
            
            $this->HistoricoActualizacionPrecios($product_id,$variacion,1,$precio_anterior,$costoNuevo,$this->casa_central_id,Auth::user()->id);
        
            
            //	29-8-2024
            $this->ActualizarRecetaDeProductos($id,0,$comercio_id); // ---> VER ESTO
    
            // Mensaje de éxito
            $this->emit('product-updated', 'Costo actualizado');
        } else {
            // Mensaje de error si el producto no se encuentra
            $this->emit("msg-error","Se ha producido un error");
        }
    }
    
    // 29-8-2024 -- Actualizacion de precios
    public function updateCost($id,$variacion, $costoNuevo)
    {
        $costoNuevo = trim($costoNuevo);
        $costoNuevo = $this->convertirFormatoMoneda($costoNuevo);
        
        if($costoNuevo < 0){
            $this->emit("msg-error","El valor debe ser positivo");
            return;
        }
        
        if(!is_numeric($costoNuevo)){
            $this->emit("msg-error","El valor debe ser un numero");
            return;
        }
        
        if(empty($costoNuevo) || $costoNuevo == null || $costoNuevo == ""){
            $this->emit("msg-error","El valor no puede estar vacio");
            return;
        }
        
        if($variacion == 0){
        // Buscar el producto y actualizar su precio
        $product = Product::find($id);
        $producto = $product;
        $product_id = $producto->id;
        $comercio_id = $producto->comercio_id;
        } else {
        $producto = productos_variaciones_datos::find($id);  
        $product_id = $producto->product_id;
        $comercio_id = $producto->comercio_id;
        }

        if ($producto) {
            $producto->cost = $costoNuevo;
            $producto->save();
            
            $descuento_costo = $producto->descuento_costo;
            $costoNuevo = $costoNuevo * (1- $descuento_costo);
            
            // Actualizar el precio interno 
            $this->ActualizarPrecioInternoPorRegla($producto,$product_id,$variacion,$costoNuevo);
            
            // Actualizar las listas
            $this->ActualizarListasPorRegla($product_id,$variacion,$costoNuevo);
            
            //	29-8-2024
            $this->ActualizarRecetaDeProductos($id,0,$comercio_id); // ---> VER ESTO
    
            // Mensaje de éxito
            $this->emit('product-updated', 'Costo actualizado');
        } else {
            // Mensaje de error si el producto no se encuentra
            $this->emit("msg-error","Se ha producido un error");
        }
    }
    
    public function ActualizarPrecioInternoPorRegla($producto,$product_id,$variacion,$costoNuevo){
        
        $costoNuevo = trim($costoNuevo);
        $costoNuevo = $this->convertirFormatoMoneda($costoNuevo);
        
        if($producto->producto_tipo == "s"){
            $product = $producto;
        } else {
            $product = productos_variaciones_datos::where('product_id',$product_id)->where('referencia_variacion',$variacion)->where('eliminado',0)->first();
        }
        //dd($product->regla_precio_interno);
        if($product->regla_precio_interno == 2){
            $precio_anterior = $product->precio_interno;
            $margen = $product->porcentaje_regla_precio_interno;    
            $precioActual = $costoNuevo * (1 + $margen);
            $product->precio_interno = $precioActual;
            $product->save();
            $this->HistoricoActualizacionPrecios($product_id,$variacion,1,$precio_anterior,$precioActual,$this->casa_central_id,Auth::user()->id);
        
        }
        if($product->regla_precio_interno == 1){
            $precio_interno = $product->precio_interno;
            if($costoNuevo != 0){
            $margen_nuevo = ($precio_interno/$costoNuevo) - 1;    
            } else {
            $margen_nuevo = 0;    
            }
            $product->porcentaje_regla_precio_interno = $margen_nuevo;    
            $product->save();
            //$this->HistoricoActualizacionPrecios($product->id,0,1,$precio_anterior,$datos['precio_interno'],$this->casa_central_id,Auth::user()->id);
        
        }    
    }

    	public function EditProduct(Product $product, $forma)
    	{
    	    $this->GetUnidadMedida();
    	    $this->tipo_usuario = User::find($this->comercio_id);
    		
    		if($this->tipo_usuario != null){
    	    if($this->tipo_usuario->sucursal == 1) {
    	    $this->es_sucursal = 1;
    	    }		    
    		} 
    
    	    $this->agregar = 1;
    	 
    		//Cod de entorno testing:
    		$this->resetUI();
    		$this->selected_id = $product->id;
    		$this->marca_id = $product->marca_id;
    		$this->name = $product->name;
    		$this->tipo_producto = $product->tipo_producto;
    		$this->barcode = $product->barcode;
    		$this->price = $product->price;
    		$this->wc_product_id = $product->wc_product_id;
    	
    		$this->GetDatosFacturacionDefectoProducto(2);
    		
    		//dd($this->wc_product_id);
    		$this->stock = $product->stock;
    		$this->alerts = $product->alerts;
    		$this->inv_ideal = $product->inv_ideal;
    		$this->precio_interno = $product->precio_interno;
    		$this->proveedor = $product->proveedor_id;
    		$this->stock_descubierto = $product->stock_descubierto;
    		$this->categoryid = $product->category_id;
    		$this->subcategoria_id = $product->subcategoria_id;
    		$this->cod_proveedor = $product->cod_proveedor;
    		$this->image = $product->image;
    		$this->mostrador_canal = $product->mostrador_canal;
    		$this->ecommerce_canal = $product->ecommerce_canal;
    		$this->wc_canal = $product->wc_canal;
    		$this->wc_product_id = $product->wc_product_id;
    		$this->descripcion = $product->descripcion;
    		$this->iva = $product->iva;
    		$this->relacion_precio_iva = $product->relacion_precio_iva;
    		$this->producto_tipo = $product->producto_tipo;
    		$this->es_insumo = $product->es_insumo;
            $this->tipo_unidad_medida = $product->unidad_medida;
            $this->cantidad_unidad_medida = floatval($product->cantidad);
            
            // Actualizacion de precios
    		$this->porcentaje_regla_precio_interno = $product->porcentaje_regla_precio_interno * 100; 
    		$this->regla_precio_interno = $product->regla_precio_interno;
    
    		if($this->image != null) { 
    		$imagen = imagenes::where('url',$this->image)->where('comercio_id',$this->casa_central_id)->first();
    		$this->base_64_archivo = $imagen->base64;
    		} else {
    		$this->base_64_archivo = null; 
    		}
    
    		//////////////////////////     SI EL PRODUCTO ES SIMPLE          ////////////////////////////////////////
    		if($this->producto_tipo == "s") {
    		    $this->EditProductSimple($product);
    		}
    
    		if($this->producto_tipo == "v") {
    		    $this->EditProductVariable($product);
    		}
    
            //dd($product);
            
    	    $this->etiqueta = $this->GetEtiquetas($product->comercio_id,"productos");
    	    
    	    $this->GetEtiquetasEdit($product->id,"productos",$product->comercio_id);
    	    
    	    $this->forma_edit = $forma;
    
    	}
    	public function EditProductVariable($product){
    
                $this->ResetVariablesVariaciones();
    			$cart = new CartVariaciones;
    			$cart->clear();
                
                $this->GetAtributosYvariaciones();
    			$this->productos_variaciones = productos_variaciones_datos::where('product_id', $this->selected_id)
    			->select('id','referencia_variacion','variaciones','variaciones_id')
    			->groupBy('id','referencia_variacion','variaciones','variaciones_id')
    			->where('productos_variaciones_datos.eliminado',0)
    			->orderBy('created_at','desc')
    			->get();
    
    			$cart = new CartVariaciones;
                
                // Setea los datos generales de las variaciones 
                
    			foreach ($this->productos_variaciones as $prod_var) {
    				// code...
    				$product_array = array(
    				'referencia_id' => $prod_var->referencia_variacion,
    				'var_nombre'=> $prod_var->variaciones,
    				'var_id'=> $prod_var->variaciones_id,
    				'id'=> $prod_var->id,
    				);
    
    				$cart->addProduct($product_array);
    			}
    
                // Setea el costo y los codigos de las variaciones 
                
    			$this->datos_variaciones = productos_variaciones_datos::where('product_id', $this->selected_id)->where('eliminado',0)->get();
    
    			foreach($this->datos_variaciones as $llaves => $sucus) {
    				$this->cod_variacion[$sucus['referencia_variacion']] = $sucus['codigo_variacion'];
    			}


                $costos = $this->obtenerCostosYPrecioInterno($product); 
                  
                // Obtener el stock de las sucursales
                $stock_sucursales = $this->obtenerStockSucursales();
            
                // Obtener las listas de precios
                $lista_precios = $this->obtenerListasPrecios($costos);
            
                $variaciones = $this->ObtenerVariaciones($product->producto_tipo);
                
                $regla_precio_interno = $this->GetListaPreciosReglaByListaId(1);
                $regla_precio_interno = $regla_precio_interno ? $regla_precio_interno->regla : 1;
                $this->emit('valuesUpdated', $variaciones,$costos, $lista_precios, $regla_precio_interno, $stock_sucursales);
     
    	}
	    
	    public function ObtenerVariaciones($producto_tipo){
            $variaciones = [];
	        if($producto_tipo == "s"){
                $key = 0;
                // Devolver el array con el index 0
                $variaciones[$key] = [
                    'variacion' => 0
                    ];
	            
	        }
	        if($producto_tipo == "v"){
                
                $cart = new CartVariaciones;
                foreach ($cart->getContent() as $key => $variacion){
                $data_variacion = $variacion['referencia_id'];
                $datos = explode("-",$data_variacion);
                $variacion_id = $datos[0];
                
                $variaciones[$key] = [
                    'variacion' => $variacion_id
                    ];
                }
	        }
	        
	        return $variaciones;
	    }
	    
        public function EditProductSimple($product)
        {
            // 26-9-2024
            $costos = $this->obtenerCostosYPrecioInterno($product); 
              
            // Obtener el stock de las sucursales
            $stock_sucursales = $this->obtenerStockSucursales();
        
            // Obtener las listas de precios
            $lista_precios = $this->obtenerListasPrecios($costos);
        
            $variaciones = $this->ObtenerVariaciones($product->producto_tipo);

            $regla_precio_interno = $this->GetListaPreciosReglaByListaId(1);
            $regla_precio_interno = $regla_precio_interno ? $regla_precio_interno->regla : 1;
            
            
            $this->emit('valuesUpdated', $variaciones,$costos, $lista_precios, $regla_precio_interno, $stock_sucursales,$this->base_64_archivo,$this->image);
        }

        private function obtenerCostosYPrecioInterno($product)
        {
            $array_costos = [];
        
            // Si el producto es de tipo "simple" (sin variaciones)
            if ($product->producto_tipo == "s") {
        
                // Calcular el costo y el descuento
                $cost = $product->cost;
                $datos_descuento = $this->calcularCostoDespuesDeDescuento($product->id, 0, $cost);
        
                $porcentaje_regla_precio_interno = $this->porcentaje_regla_precio_interno * 100;
                $precio_interno = $this->precio_interno;
        
                $this->cost = $cost;
                $this->precio_interno = $precio_interno;
        
                // Obtener los valores finales
                $costo_despues_descuento = $datos_descuento['costo_final'];
                $descuento_costo = $datos_descuento['descuento_final'];
        
                // Armar el array de costos
                $key = 0;
                $array_costos = $this->ArmarArrayDeCostosYPrecioInterno($array_costos, $key, 0, $cost, $descuento_costo, $costo_despues_descuento, $porcentaje_regla_precio_interno, $precio_interno);
        
                // Guardar o actualizar el descuento en el array de descuentos para el producto sin variación ---> aca hay que armar los descuentos
                    
                    $descuentos = $this->GetDescuentoByProductId($product->id,0);
                    
                    // Armamos cada descuento
                    foreach ($descuentos as $key => $descuento) {
                        // Asignar el descuento final a la variación
                        $this->descuentos_variaciones[0][$key] = [
                            'id' => $descuento->nro_descuento,
                            'descuento' => $descuento->descuento * 100
                        ];
                    }
            }
        
            // Si el producto es de tipo "variación"
            if ($product->producto_tipo == "v") {
        
                // Iterar sobre las variaciones del producto
                foreach ($this->datos_variaciones as $key => $d) {
        
                    // Calcular el costo y el descuento para cada variación
                    $cost = $d['cost'];
                    $variacion_id = $this->GetVariacionId($d['referencia_variacion']);
                    $datos_descuento = $this->calcularCostoDespuesDeDescuento($product->id, $d['referencia_variacion'], $cost);
        
                    // Obtener los valores finales
                    $costo_despues_descuento = $datos_descuento['costo_final'];
                    $descuento_costo = $datos_descuento['descuento_final'];
        
                    $porcentaje_regla_precio_interno = $d['porcentaje_regla_precio_interno'] * 100;
                    $precio_interno = $d['precio_interno'];
        
                    // Armar el array de costos para cada variación
                    $array_costos = $this->ArmarArrayDeCostosYPrecioInterno($array_costos, $key, $variacion_id, $cost, $descuento_costo, $costo_despues_descuento, $porcentaje_regla_precio_interno, $precio_interno);
        
                    // Guardar o actualizar los descuentos en el array de descuentos para cada variación
                    if (!isset($this->descuentos_variaciones[$d['referencia_variacion']])) {
                        $this->descuentos_variaciones[$d['referencia_variacion']] = [];
                    }
                    
                    $descuentos = $this->GetDescuentoByProductId($product->id,$d['referencia_variacion']);
                    
                    // Armamos cada descuento
                    foreach ($descuentos as $key => $descuento) {
                        // Asignar el descuento final a la variación
                        $this->descuentos_variaciones[$d['referencia_variacion']][$key] = [
                            'id' => $descuento->nro_descuento,
                            'descuento' => $descuento->descuento * 100
                        ];
                    }
                    

                }
            }
            
            
            return $array_costos;
        }
        
        private function ArmarArrayDeCostosYPrecioInterno($array_costos, $key, $variacion_id, $costo, $descuento_costo, $costo_despues_descuento, $porcentaje_regla_precio_interno, $precio_interno)
        {
            if($costo_despues_descuento != 0){
            $porcentaje_regla_precio_interno_calculado = round((($precio_interno/$costo_despues_descuento) - 1) * 100,2);
            } else {
            $porcentaje_regla_precio_interno_calculado = 0;    
            }
            
            $array_costos[$key] = [
                'variacion_id' => $variacion_id,
                'cost' => round($costo,2),
                'descuento_costo' => round($descuento_costo,2),
                'costo_despues_descuento' => round($costo_despues_descuento,2),
                'porcentaje_regla_precio_interno' => round($porcentaje_regla_precio_interno_calculado,2),
                'precio_interno' => round($precio_interno,2)
            ];
        
            return $array_costos;
        }

        private function obtenerStockSucursales()
        {
            // Obtener el stock de las sucursales
            $this->stock_productos_sucursales = productos_stock_sucursales::select('referencia_variacion','sucursal_id', 'stock', 'stock_real', 'almacen_id')
                ->where('product_id', $this->selected_id)
                ->get();
        
            $stock_sucursales = [];
        
            foreach ($this->stock_productos_sucursales as $key => $stock) {
                $sucursal_id = ($stock['sucursal_id'] == 0) ? $this->casa_central_id : $stock['sucursal_id'];
                $stock_comprometido = $this->GetStockComprometido($this->selected_id, 0, $sucursal_id);
                $variacion_id = $this->GetVariacionId($stock['referencia_variacion']);
                
                // aca tengo que obtener que tipo de producto es para ver como pasarle las cantidades 
                
                $stock_sucursales[$key] = [
                    'variacion_id' => $variacion_id,
                    'sucursal_id' => $stock['sucursal_id'],
                    'almacen_id' => $stock['almacen_id'],
                    'stock_real' => $stock['stock_real'],
                    'stock_disponible' => $stock['stock_real'] - $stock_comprometido,
                    'stock_comprometido' => $stock_comprometido
                ];
            }
        
            return $stock_sucursales;
        }
        private function GetVariacionId($variacion_id){
                if( $variacion_id != 0){
                $datos = explode("-", $variacion_id);
                $variacion = $datos[0];
                } else {
                $variacion = 0; 
                }       
                
                return $variacion;
        }
        
        private function obtenerListasPrecios($costos)
        {
            // Obtener las listas de precios
            $this->productos_lista_precios = productos_lista_precios::select('referencia_variacion','lista_id', 'precio_lista', 'porcentaje_regla_precio', 'regla_precio')
                ->where('product_id', $this->selected_id)
                ->where('eliminado', 0)
                ->get();
        
            $lista_precios = [];
            
            foreach ($this->productos_lista_precios as $key => $lp) {
            
            if(isset($costos[$lp['referencia_variacion']]['costo_despues_descuento'])){
                $costo_despues_descuento = $costos[$lp['referencia_variacion']]['costo_despues_descuento'];
                $precio_lista = round($lp['precio_lista'],2);
                if($costo_despues_descuento != 0){
                $porcentaje_regla_precio = round((($precio_lista/$costo_despues_descuento) - 1) * 100,2);    
                } else {
                $porcentaje_regla_precio = 0;    
                }
                
            } else {
                $porcentaje_regla_precio = round($lp['porcentaje_regla_precio'] * 100,2);
            }
                
                    
                $regla = $this->GetListaPreciosReglaByListaId($lp['lista_id']);
                $variacion_id = $this->GetVariacionId($lp['referencia_variacion']);
                
                
                $lista_precios[$key] = [
                    'variacion_id' => $variacion_id,
                    'lista_id' => $lp['lista_id'],
                    'precio_lista' => round($lp['precio_lista'],2),
                    'porcentaje_regla_precio' => $porcentaje_regla_precio,
                    'regla' => $regla ? $regla->regla : 1
                ];
                    
            }
            
            return $lista_precios;
        }

    //-------- FUNCION QUE CREA / ACTUALIZA EL CODIGO DE LAS VARIACIONES ---- /
    
    public function DetectarCodigosVariablesRepetidos() {
    
    $arreglo = $this->cod_variacion;
    
    $nuevoArray = array_values($arreglo);
    
    foreach($nuevoArray as $na) {
        // Convertir ambos valores a minúsculas antes de comparar
        if (strtolower($this->barcode) == strtolower($na)) {
            $this->emit("msg-error", "El código " . $na . " esta repetido para codigo del producto y de una variacion");
            return true;
        }
    
        // Buscar el producto ignorando el case del código de barras
        //$product = Product::whereRaw('LOWER(barcode) = ?', [strtolower($na)])->where('eliminado',0)->first();
        $product = Product::whereRaw('LOWER(barcode) = ?', [strtolower($na)])->where('comercio_id',$this->casa_central_id)->where('eliminado',0)->first();
        
        if ($product != null) {
            $this->emit("msg-error", "El código: " . $na . " ya está en uso en otro producto");
            return true;
        }
    }

    
    if(count($nuevoArray) > count(array_unique($nuevoArray))){
      $hay_repetidos = true;
      }else{
      $hay_repetidos = false;
      }    
      
      return $hay_repetidos;
    }
    

    public function DetectarPreciosInternosVacios() {
        $hayValorVacioONulo = false;

        $arreglo = $this->precios_internos_variacion;
    
        $nuevoArray = array_values($arreglo);
        
        foreach ($nuevoArray as $valor) {
            if (empty($valor)) {
                $hayValorVacioONulo = true;
                break;
            }
        }
        
        if ($hayValorVacioONulo) {
            $hay_vacios = true;
        } else {
            $hay_vacios = false;
        }
        
        return $hay_vacios;
    }
    
    public function DetectarPreciosBaseVacios() {
        $hayValorVacioONulo = false;

    	
        $cart = new CartVariaciones;
    
        foreach ($cart->getContent() as $key => $variaciones){
        $arreglo = $this->precio_lista[$variaciones['referencia_id']."|0"];
        
        if ($arreglo == null) {
            $hay_vacios = true;
            break;
        } else {
            $hay_vacios = false;
        }    
        }

        return $hay_vacios;
    }
    
    public function DetectarCodigosVariablesVacios() {
        $hayValorVacioONulo = false;

        $arreglo = $this->cod_variacion;
    
        $nuevoArray = array_values($arreglo);
        
        foreach ($nuevoArray as $valor) {
            if (empty($valor)) {
                $hayValorVacioONulo = true;
                break;
            }
        }
        
        if ($hayValorVacioONulo) {
            $hay_vacios = true;
        } else {
            $hay_vacios = false;
        }
        
        return $hay_vacios;
    }
    
    public function QueryCodigoVariable($product) {

		//Cod entorno testing:
		foreach ($this->cod_variacion as $llave => $cod) {				
			
			$rules = [
				'cod_variacion.' . $llave => [
					'required',
				],
			];

			$messages = [
				'cod_variacion.' . $llave . '.required' => 'El código es requerido',			
			];

			foreach ($this->cod_variacion as $otraLlave => $otroCod) {
				if ($otraLlave != $llave) {
					$rules['cod_variacion.' . $llave][] = 'different:cod_variacion.' . $otraLlave;
					$messages['cod_variacion.' . $llave . '.different'] = 'El código debe ser unico ';
				}
			}
			
			$this->validate($rules, $messages);
		
		}
		if($this->cod_variacion !== null){
			foreach ($this->cod_variacion as $llave => $cod){	

				$pdv_cod = productos_variaciones_datos::where('referencia_variacion', $llave)				
				->where('product_id',$product->id)
				->get();	

				foreach ($pdv_cod as $pdv_co) {
					$pdv_co->codigo_variacion = $this->cod_variacion[$llave];
					$pdv_co->save();
				}
			}
		}
				
    }
    
    // ------ FUNCION QUE CREA / ACTUALIZA EL COSTO DE LAS VARIACIONES ----/
    
    
    public function QueryCostosYPreciosInternos($product,$variaciones,$Costos) {

        foreach ($variaciones as $variacion) {
        
        // Filtrar precios y porcentajes correspondientes a esta variación
        $costos_y_precio_interno = array_filter($Costos, function ($precio) use ($variacion) {
                return $precio['variacion_id'] == $variacion;
        });
        
        //dd($costos_y_precio_interno);
        foreach($costos_y_precio_interno as $datos) {
    	    $regla_precios_precio_interno = $this->GetListaPreciosReglaByListaId(1);    
            
            $product = Product::find($product->id);
            
    	    if($product->producto_tipo == "s"){
    	        
    	        $product->cost = $datos['cost'];
    	        
    	        $precio_anterior = $product->precio_interno;
    	        
    	        $product->update([
    	            'precio_interno' => $datos['precio_interno'],
    	            'porcentaje_regla_precio_interno' => $datos['porcentaje_precio_interno']/100,
    	            'regla_precio_interno' => $regla_precios_precio_interno ? $regla_precios_precio_interno->regla : 1,
    	            'descuento_costo' => $datos['descuento']/100
    	            ]);

    	        
    	        $this->HistoricoActualizacionPrecios($product->id,0,1,$precio_anterior,$datos['precio_interno'],$this->casa_central_id,Auth::user()->id);
        
    	    }
    	    
    	    if($product->producto_tipo == "v"){
    	        $variacion_id = $variacion."-".$product->comercio_id; 
    		    
    		    $this->StoreUpdateProductosVariacionesDatos($product->id,$variacion_id,$product->comercio_id);
    	        
    		    $pdv_cost = productos_variaciones_datos::where('referencia_variacion', $variacion_id)->where('product_id',$product->id)->first();
    		    $precio_anterior = $pdv_cost->precio_interno;
    	        $pdv_cost->cost = $datos['cost'];
    	        $pdv_cost->precio_interno = $datos['precio_interno'];
    	        $pdv_cost->porcentaje_regla_precio_interno = $datos['porcentaje_precio_interno']/100;
    	        $pdv_cost->regla_precio_interno = $regla_precios_precio_interno ? $regla_precios_precio_interno->regla : 1;
    	        $pdv_cost->descuento_costo = $datos['descuento']/100;
    	        $pdv_cost->save();
    	        
    	        $this->HistoricoActualizacionPrecios($product->id,$variacion_id,1,$precio_anterior,$datos['precio_interno'],$this->casa_central_id,Auth::user()->id);
        
    	    }
		
    	} 
	
    }
}

    // 29-8-2024
	public function GetStockComprometido($product_id,$variacion,$comercio_id) {
 
     return SaleDetail::join('sales','sales.id','sale_details.sale_id')
        ->select(SaleDetail::raw('IFNULL(SUM(sale_details.quantity),0) as stock_comprometido'))
        ->where('sale_details.product_id',$product_id)
        ->where('sale_details.referencia_variacion',$variacion)
        ->where('sale_details.comercio_id',$comercio_id)
        ->where('sale_details.estado',0)
        ->where('sales.eliminado',0)
        ->first()->stock_comprometido;    
    
    }

// 29-8-2024
public function GetStockComprometidoSucurales($product_id, $variacion, $casa_central_id) 
{
    // Obtener las sucursales correspondientes
    $sucursales = sucursales::join('users', 'users.id', 'sucursales.sucursal_id')
        ->select('users.name', 'sucursales.sucursal_id')
        ->where('sucursales.casa_central_id', $casa_central_id)
        ->where('sucursales.eliminado', 0)
        ->get();

    // Obtener los IDs de las sucursales y añadir el ID de casa central
    $sucursales = $sucursales->pluck('sucursal_id')->toArray();
    $sucursales[] = $casa_central_id;

    // Consulta para obtener el stock comprometido, asegurando que las sumas nulas se representen como 0
    $data = User::whereIn('users.id', $sucursales)
        ->leftJoin('sale_details', function ($join) use ($product_id, $variacion) {
            $join->on('sale_details.comercio_id', '=', 'users.id')
                ->where('sale_details.product_id', $product_id)
                ->where('sale_details.estado',0)
                ->where('sale_details.referencia_variacion', $variacion);
        })
        ->leftJoin('sales', 'sales.id', '=', 'sale_details.sale_id')
        ->select(
            'users.id',
            SaleDetail::raw('COALESCE(SUM(sale_details.quantity), 0) as stock_comprometido')
        )
        ->where(function ($query) {
            // Asegura que no afecte los resultados cuando `sale_details` no tiene coincidencias
            $query->where('sales.eliminado', 0)->orWhereNull('sales.eliminado');
        })
        ->groupBy('users.id')
        ->get();

    return $data;
}

//6-9-2024
  public function setUpdateStockDBTrait($sucursal_id, $product_id,$referencia_variacion, $productosStockSucursalesNuevo,$productosStockRealSucursalesNuevo, $almacen_id ){ 

      DB::table('productos_stock_sucursales')
      ->where('product_id',$product_id)
      ->where('referencia_variacion',$referencia_variacion)
      ->where('sucursal_id',$sucursal_id)
      ->limit(1)
      ->update([
        'stock' => $productosStockSucursalesNuevo,
        'stock_real' => $productosStockRealSucursalesNuevo,
        'almacen_id' => $almacen_id
        ]);
      
  }
  
  //6-9-2024
  public function SetHistoricoStock($tipo_movimiento,$product_id,$referencia_variacion,$cantidad_movimiento,$stock_real,$sucursal_id){
		
		if($sucursal_id == 0) { $sucursal_id = $this->casa_central_id;}

		$historico_stock = historico_stock::create([
		'tipo_movimiento' => $tipo_movimiento,
		'producto_id' => $product_id,
		'cantidad_movimiento' => $cantidad_movimiento,
		'referencia_variacion' => $referencia_variacion,
		'stock' => $stock_real,
		'usuario_id' => $sucursal_id,
		'comercio_id'  => $sucursal_id
		]);
		
  }

    //--------- FUNCION QUE CREA / ACTUALIZA EL STOCK --------- //
    
    //6-9-2024
    public function GetSucursalesYCentral($casa_central_id){
    // Obtener las sucursales correspondientes
    $sucursales = sucursales::join('users', 'users.id', 'sucursales.sucursal_id')
        ->select('users.name', 'sucursales.sucursal_id')
        ->where('sucursales.casa_central_id', $casa_central_id)
        ->where('sucursales.eliminado', 0)
        ->get();

    // Obtener los IDs de las sucursales y añadir el ID de casa central
    $sucursales = $sucursales->pluck('sucursal_id')->toArray();
    $sucursales[] = $casa_central_id;
    
    return $sucursales;
    }

    //--------- FUNCION QUE CREA / ACTUALIZA EL PRECIO --------- //
    

    
    public function SetSucursalIdOrCero($sucursal_id,$comercio_id){
     if($sucursal_id == $comercio_id){
         return 0;
     }  else {
         return $sucursal_id;
     } 
    }
    
    
    
    // -------------------    FUNCION QUE PREPARA LOS DATOS PARA ACTUALIZAR WOCOMMERCE   --------------------------------  //
    
    public function UpdateWocommerce($product) {
		    
		   // if($product->producto_tipo == "s") {
		   // $this->WocommerceUpdateSimple($product_id);    
		   // }
		                
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


			if($product->producto_tipo == "s") {

                // STOCK //
                 $this->stock_origen = productos_stock_sucursales::where('product_id', $this->selected_id )->where('referencia_variacion',0)->where('sucursal_id',0)->first();

				// PRECIO ORIGEN//
                
                $this->precio_origen = productos_lista_precios::where('product_id', $this->selected_id )->where('referencia_variacion',0)->where('lista_id',0)->first();

                ///    CATEGORIA WC/////
                
                $categoria = Category::find($this->categoryid);
                
                if($categoria != null) {
                	$categoria_wc = $categoria->wc_category_id;
                	
                	if($categoria_wc != null) {
                	    $categoria_wc = $categoria_wc;
                	} else {
                	$wc = wocommerce::where('comercio_id', $comercio_id)->first();
                
                	$woocommerce = new Client(
                	$wc->url,
                	$wc->ck,
                	$wc->cs,
                
                	[
                	'version' => 'wc/v3',
                	]
                	);
                
                	$data_c = [
                	'name' => $categoria->name,
                	'image' => [
                	   'src' => ''
                	]
                	];
                
                	$this->wc_category = $woocommerce->post('products/categories', $data_c);
                	
                	$categoria->wc_category_id = $this->wc_category->id;
                	$categoria->save();

	}
	
} 
                ///////////////////////////////
                    
		        //////// DEFINIR SI TRABAJA O NO CON STOCK  /////////

					if($this->stock_descubierto == "si") {

						$this->manage_stock = 'no';
						$this->stock_quantity = $this->stock_origen->stock;

					} else {

						$this->manage_stock = 'yes';
						$this->stock_quantity  = null;

					}
                    

					if($this->lista_precios) {


        			///////// WOCOMMERCE CON VARIAS LISTAS DE PRECIOS /////////

		        	if($this->precio_lista != null) {

                        
						foreach ($this->precio_lista as $key => $value) {
                        
                        $array = explode('|', $key);
                        $referencia_variacion =  $array[0];
                        $lista =  $array[1];
                        
						if($lista != 0) {


						$this->key_precio_lista = lista_precios::find($lista);

                          if($this->key_precio_lista != null) {

                        	$this->key_precio_lista->wc_key."_wholesale_price";
                        	$this->key_precio_lista->wc_key."_have_wholesale_price";

                        	$list =
                        	array(
                        	array(
                            "key" => $this->key_precio_lista->wc_key."_wholesale_price",
                            "value" => $this->precio_lista[$key],
                    	    ),	array(
                            "key" => $this->key_precio_lista->wc_key."_have_wholesale_price",
                            "value" => "yes",
                    	    )
                    	    );

                          } else {
                          $list = [];
                          }

						}
						    
						    
						}

                    
					$data = [
							'name' => $this->name,
							'type' => 'simple',
							'sku' => $this->barcode,
							'status' => 'publish',
							'manage_stock' => true,
							'backorders' => $this->manage_stock,
							'stock_quantity' => $this->stock_quantity,
							'stock_status' => "instock",
							'regular_price' => $this->precio_origen->precio_lista,
							'categories' => [
									[
											'id' => $categoria_wc,
									]
							],

							'meta_data' => $list
					];

				} else {

					////// WOCOMMERCE CON UNA SOLA LISTA DE PRECIOS //////

					$data = [
							'name' => $this->name,
							'type' => 'simple',
							'sku' => $this->barcode,
							'status' => 'publish',
							'manage_stock' => true,
							'backorders' => $this->manage_stock,
							'stock_quantity' => $this->stock_quantity,
							'stock_status' => "instock",
							'regular_price' => $this->price,
							'categories' => [
									[
											'id' => $categoria_wc,
									]
							],
					];


				}

				} else {

					////// WOCOMMERCE CON UNA SOLA LISTA DE PRECIOS //////

					$data = [
							'name' => $this->name,
							'type' => 'simple',
							'sku' => $this->barcode,
							'status' => 'publish',
							'manage_stock' => true,
							'backorders' => $this->manage_stock,
							'stock_quantity' => $this->stock_quantity,
							'stock_status' => "instock",
							'regular_price' => $this->price,
							'categories' => [
									[
											'id' => $categoria_wc,
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

		}

		if ($product->producto_tipo == "v") {

		////////////////////////////////////////////////
		/////// WooCommerce con producto varible ///////
		////////////////////////////////////////////////

		$this->WocommerceUpdateVariable($product->id);
	}

}
}


// ---------  RESET UI   --------------- //


	public function resetUI()
	{
	    
	    $this->resetValidation(); // Limpia los errores previos
	    $this->GetDatosReglasPreciosMount(); // Actualizacion precios
	    
	    $this->cod_proveedor = null;
	    $this->es_insumo = 0;
	    $this->marca_id = 1;
	    $this->base_64_archivo = null;
        $this->base_64_nombre = null;
        $this->imagen_seleccionada = null;

	    $this->error_variacion = '';
        $this->mostrarErrorVariacion = false;
        $this->mostrarErrorTipoProducto = false;
	    $this->forma_edit = "";
	    $this->stock_productos_sucursales = 0;
		$this->name ='';
		$this->tipo_carga_imagen = '';
		$this->imagen_seleccionada = '';
		$this->barcode ='';
		$this->base64 = '';
		$this->cost ='';
		$this->price ='';
		$this->iva = 0;
		$this->relacion_precio_iva = 0;
		$this->stock ='';
		$this->mostrador_canal = 1;
		$this->tipo_producto = 1;
		$this->wc_canal =0;
		$this->ecommerce_canal =0;
		$this->alerts ='';
		$this->descripcion ='';
		$this->search ='';
		$this->inv_ideal ='';
		$this->categoryid = 1;
		$this->image = null;
		$this->selected_id = 0;
		$this->pageTitle = 'Listado';
		$this->proveedor = '1';
		$this->componentName = 'Productos';
		$this->almacen = 'Elegir';
		$this->stock_descubierto = 'Elegir';
		$this->producto_tipo = 'Elegir';
		$this->referencia_variacion = 0;
		$categoria_wc = 0;
		$this->precio_lista = null;
	    $this->stock_sucursal = null;
	    $this->real_stock_sucursal = null;
	    $this->almacen_id = null;
	    $this->stock_sucursal_comprometido = null;

		$this->subcategoria_id = 1;
		
		//Cod entorno testing:
		$cart = new CartVariaciones;     
		$cart->clear();
		$this->cod_variacion = [];
		$this->precio_interno = "";
		
		// 28-5-2024
		$this->tipo_unidad_medida = 9;
        $this->cantidad_unidad_medida = 1;
        
        $this->SetListaCostoDefecto();
	}
	
	// Muestra el stock de las variaciones en un modal nuevo 
	
	public function MostrarStockProduct($product_id,$sucu_id) {
	 
	 $this->producto_variaciones = Product::find($product_id)->name;
    
    $this->stock_variaciones = productos_stock_sucursales::join('productos_variaciones_datos','productos_variaciones_datos.referencia_variacion','productos_stock_sucursales.referencia_variacion')
    ->where('productos_stock_sucursales.product_id',$product_id)
    ->where('productos_stock_sucursales.sucursal_id',$sucu_id)
    ->where('productos_variaciones_datos.eliminado',0)
    ->select('productos_stock_sucursales.stock_real','productos_stock_sucursales.comercio_id','productos_stock_sucursales.sucursal_id','productos_stock_sucursales.referencia_variacion','productos_stock_sucursales.id','productos_stock_sucursales.stock','productos_stock_sucursales.product_id','productos_variaciones_datos.codigo_variacion','productos_variaciones_datos.variaciones')
    ->groupBy('productos_stock_sucursales.stock_real','productos_stock_sucursales.comercio_id','productos_stock_sucursales.sucursal_id','productos_stock_sucursales.referencia_variacion','productos_stock_sucursales.id','productos_stock_sucursales.stock','productos_stock_sucursales.product_id','productos_variaciones_datos.codigo_variacion','productos_variaciones_datos.variaciones')
    ->get();
    
     $this->emit('modal-stock','');
    
	}


	// Abrir el modal 
	
	/*
	public function ModalAgregarProduct() {
	
	$this->resetUI();
    $this->costos_variacion = null;
	$cart = new CartVariaciones;
	$cart->clear();
	$this->variacion_atributo = "c";
	$product = null;
	$pvd = null;
	$this->productos_lista_precios = null;
	$this->productos_stock_sucursales = null;
	$this->precio_lista = null;
	$this->stock_sucursal = null;
	$key = null;
	$llave = null;
	$this->selected_id = 0;
	
    $this->descuento_costo = 0;
    $this->costo_despues_descuento = 0;

	
	$this->producto_tipo = 'Elegir';
	
	$this->ResetVariablesVariaciones();
	
	$this->emit('modal-show','Show modal');
	
	    
	}
	*/
	
    /*
	public function StoreListaPrecioProduct()
	{
		$rules = [
			'nombre_lista' => 'required'
		];

		$messages = [
			'nombre.required' => 'Nombre de la lista de precios es requerido'
		];

		$this->validate($rules, $messages);

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$lista_precios = lista_precios::create([
			'nombre' => $this->nombre_lista,
			'wc_key' => $this->wc_key_lista,
			'descripcion' => $this->descripcion_lista,
			'comercio_id' => $comercio_id
		]);

// Si el producto es simple
        
		$productos_simples = Product::where('comercio_id',$comercio_id)->where('producto_tipo','s')->where('eliminado',0)->get();
        
        foreach($productos_simples as $p) {

        productos_lista_precios::create([
			'lista_id' => $lista_precios->id,
			'product_id' => $p->id,
			'precio_lista' => 0,
			'referencia_variacion' => 0,
			'comercio_id' => $comercio_id
		]);

        } 
        
// Si el producto es variable
        
        $productos_variaciables = productos_variaciones_datos::leftjoin('products','products.id','productos_variaciones_datos.product_id')
        ->select('productos_variaciones_datos.*')
        ->where('products.comercio_id',$comercio_id)
        ->where('products.eliminado',0)
        ->where('products.producto_tipo','v')
        ->get();
        
        foreach($productos_variaciables as $pv) {

        productos_lista_precios::create([
			'lista_id' => $lista_precios->id,
			'product_id' => $pv->product_id,
			'precio_lista' => 0,
			'referencia_variacion' => $pv->referencia_variacion,
			'comercio_id' => $comercio_id
		]);

        } 
            
            
		$this->resetUIListaPrecios();
		
		$this->lista_precios = lista_precios::where('comercio_id',$this->sucursal_id)->get();
		
		$this->emit('modal-lista-precios-hide','Lista de precios Registrada');

	}
	*/

	public function EliminarTodos(){
    
    $products = Product::where('comercio_id',$this->casa_central_id)->get();
    
    foreach($products as $product){
    $p = Product::find($product->id);
		$p->update([
			'eliminado' => 1
		]);
		
    }
    
    $this->CerrarModalConfiguracion();
    $this->emit("msg-success","Productos eliminados");
		
	}
	
	public function DestroyProduct(Product $product)
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



//		if($imageTemp !=null) {
//			if(file_exists('storage/products/' . $imageTemp )) {
//				unlink('storage/products/' . $imageTemp);
//			}
//		}

		$this->resetUI();
		$this->emit('product-deleted', 'Producto Eliminado');
	}
	
	
		
	public function RestaurarProductoProduct(Product $product)
	{
		$imageTemp = $product->image;
		$product->update([
			'eliminado' => 0
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

		$this->resetUI();
		$this->emit('product-deleted', 'Producto Restaurado');
	}
	
	
	public function AccionEnLoteProduct($ids, $id_accion)
    {
    
    if($id_accion == 1) {
        $estado = 0;
        $msg = 'RESTAURADOS';
    } else {
        $estado = 1;
        $msg = 'ELIMINADOS';
    }
    
    $productos_checked = Product::select('products.id')->whereIn('products.id',$ids)->get();

    $this->id_check = [];
    
    foreach($productos_checked as $pc) {
    /*
    $this->AccionEnLoteWc($pc->id, $estado);
    */
    $pc->eliminado = $estado;
    $pc->save();
 
    }
    
    $this->id_check = [];
    $this->accion_lote = 'Elegir';
    
     $this->emit('global-msg',"PRODUCTOS ".$msg);
    
  
    }
    
    public function SwitchExportarCatalogos() {
    switch ($this->product_section) {
        case "Products":
            $this->ExportarCatalogoProduct();
            $this->emit('msg-error', 'Generando descarga');
            return redirect('descargas');
        
        case "ProductsPrecio":
            $this->emit('modal-export-listas-show', '');
            break;

        case "ProductsStock":
            $this->emit('modal-export-stocks-show', '');
            break;

        default:
            $this->emit('msg-error', 'Sección no reconocida');
            break;
    }
    }

    public function ExportarCatalogoProduct() {
    $comercio_id = (Auth::user()->comercio_id != 1) ? Auth::user()->comercio_id : Auth::user()->id;
    $this->casa_central_id = Auth::user()->casa_central_user_id;

    $p_count = Product::where('comercio_id', $this->casa_central_id)->where('eliminado', 0)->count();
    $filtros = ($this->id_categoria ?? 0) . "|" . ($this->id_almacen ?? 0) . "|" . ($this->proveedor_elegido ?? 0) . "|" . ($this->es_insumo_elegido ?? 0);
    
    
    $estado = ($p_count > 400) ? 0 : 1;
    $nombre = 'Productos_' . $comercio_id . '_' . Carbon::now()->format('d_m_Y_H_i_s');
    
    $re = descargas::create([
        'user_id' => $comercio_id,
        'comercio_id' => $this->casa_central_id,
        'tipo' => 'exportar_productos',
        'estado' => $estado,
        'datos_filtros' => $filtros,
        'nombre' => $nombre
    ]);

    
    $reportName = $nombre . '.xlsx';
    Excel::store(new ProductsExport($this->casa_central_id, $re->id), 'catalogos/' . $reportName);
        
    $re->update(['estado' => 2]);
    
    
    
    //ExportProductsJob::dispatch($re->id, $re->comercio_id,$this->casa_central_id);
}


// GUARDAR CATEGORIA 

public function StoreCategoriaProduct() {
    
    $rules = [
	'name_categoria' => 'required|min:3'
	];

	$messages = [
	'name_categoria.required' => 'Nombre de la categoría es requerido',
	'name_categoria.min' => 'El nombre de la categoría debe tener al menos 3 caracteres'
	];

	$this->validate($rules, $messages);

    ////////// WooCommerce ////////////

	$wc = wocommerce::where('comercio_id', $this->comercio_id)->first();

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
			    'name' => $this->name_categoria,
			    'image' => [
			        'src' => ''
			    ]
			];

			$this->wc_category = $woocommerce->post('products/categories', $data);

		}

        if($wc != null){
            $this->wc_category_id = $this->wc_category->id;
        } else {
            $this->wc_category_id = 0;
        }
		////////////////////////////////////////////////

		$category = Category::create([
			'name' => $this->name_categoria,
			'comercio_id' => $this->comercio_id,
			'wc_category_id' => $this->wc_category_id
		]);


		$customFileName;
		if($this->image_categoria)
		{
			$customFileName = uniqid() . '_.' . $this->image_categoria->extension();
			$this->image_categoria->storeAs('public/categories', $customFileName);
			$category->image = $customFileName;
			$category->save();
		}

    $this->loadCategorias();
	$this->categoryid = $category->id;

	$this->name_categoria = "";
	$this->emit('category-added','Categoría agregada');
	$this->emit('modal-show','Show modal');


}
 
 // Guarda el almacen 
 
 public function StoreAlmacenProduct() {
     
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
 
 public function StoreProveedorProduct() {

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		

		$rules  =[
		    'id_proveedor' => ['numeric',Rule::unique('proveedores','id_proveedor')->where('creador_id',$comercio_id)->where('eliminado',0)],
			'nombre_proveedor' =>  ['required',Rule::unique('proveedores','nombre')->where('comercio_id',$comercio_id)->where('eliminado',0)],
			'mail_proveedor' => 'is_mail',
            'telefono_proveedor' => 'numeric'
		];

		$messages = [
		    'id_proveedor.numeric' => 'El codigo del proveedor debe ser numerico',
			'id_proveedor.unique' => 'El codigo del proveedor ya existe',
			'nombre_proveedor.required' => 'Nombre del proveedor requerido',
			'nombre_proveedor.unique' => 'El nombre del proveedor ya existe',
            'mail_proveedor.is_mail' => 'Ingresa un correo válido',
            'telefono_proveedor.numeric' => 'El telefono deben ser solo numeros'

		];

		$this->validate($rules, $messages);

        
        $ultimo_id = proveedores::where('comercio_id',$comercio_id)->max('id');
        $ultimo_proveedor = proveedores::find($ultimo_id);
        
        if($ultimo_proveedor != null){
        $nuevo_id = $ultimo_proveedor->id_proveedor + 1;
        } else {
        $nuevo_id = 1;    
        }

    	$proveedores = proveedores::create([
    	  'id_proveedor' => $this->id_proveedor,
    	  'nombre' => $this->nombre_proveedor,
    	  'id_proveedor' => $nuevo_id,
          'direccion' => $this->direccion_proveedor,
          'pais' => $this->pais_proveedor,
          'altura' => $this->altura_proveedor,
          'depto' => $this->depto_proveedor,
          'piso' => $this->piso_proveedor,
          'codigo_postal' => $this->codigo_postal_proveedor,
          'localidad' => $this->localidad_proveedor,
          'provincia' => $this->provincia_proveedor,
          'telefono' => $this->telefono_proveedor,
          'mail' => $this->mail_proveedor,
    	  'comercio_id' => $this->comercio_id,
    	  'creador_id' => $this->comercio_id
    	]);
        $this->loadProveedores();
        $this->proveedor = $proveedores->id;
		$this->nombre_proveedor = "";
        $this->telefono_proveedor = "";
        $this->mail_proveedor = "";
        $this->direccion_proveedor = "";
        $this->localidad_proveedor = "";
        $this->provincia_proveedor = "";
        $this->pais_proveedor = "";
        $this->altura_proveedor = "";
        $this->depto_proveedor = "";
        $this->piso_proveedor = "";
        $this->codigo_postal_proveedor = "";

		$this->emit('proveedor-added', 'Proveedor Registrado');


	     
 }
 
 public function resetUIListaPrecios() {
    	$this->nombre_lista = '';
		$this->wc_key_lista = '';
		$this->descripcion_lista = '';
 }
 

 
 // Funcion para obtener el stock de un producto
 
   public function getProductStockProduct($product_id, $variacion, $sucursal_id)
   {
       
       // ESTA REPETIDO EN EL CART TRAIT
       
      return productos_stock_sucursales::join('products','products.id','productos_stock_sucursales.product_id')
      ->where('productos_stock_sucursales.product_id', $product_id)
      ->where('productos_stock_sucursales.sucursal_id', $sucursal_id)
      ->where('productos_stock_sucursales.referencia_variacion', $variacion)
      ->where('products.eliminado', 0)
      ->select('productos_stock_sucursales.stock')
      ->first();
   }
   
// Funcion para obtener el precio de un producto

   public function getProductPrecioProduct($product_id, $variacion, $lista_id)
   {
       
     //  dd($product_id, $variacion, $lista_id);
       
      return productos_lista_precios::join('products','products.id','productos_lista_precios.product_id')
      ->where('products.id', $product_id)
      ->where('productos_lista_precios.lista_id', $lista_id)
      ->where('productos_lista_precios.referencia_variacion',  $variacion)
      ->where('products.eliminado', 0)
      ->select('productos_lista_precios.precio_lista')
      ->first();

   }
   
   // Funcion para traer todas las listas de precios del comercio (diferentes a la lista base)
   
      public function getListaPrecios($comercio_id)
   {
      return lista_precios::where('comercio_id', $comercio_id)->where('eliminado',0)->get();

   }
   


/////// GUARDAR VARIACION   ////////


public function GuardarVariacion() {

if($this->variacion_atributo == null) { 
    $this->emit("msg-error","DEBE AGREGAR ALGUNA VARIACION");
    $this->error_variacion = 'Debe agregar alguna variacion';
    $this->mostrarErrorVariacion = true;
    return;
    }

					$cart = new CartVariaciones;

					///////// GUARDAR VARIACIONES  /////////

					if(Auth::user()->comercio_id != 1)
					$comercio_id = Auth::user()->comercio_id;
					else
					$comercio_id = Auth::user()->id;

					$this->referencia_id = Carbon::now()->format('dmYHis').'-'.$comercio_id;

					//cod entorno testing
					//$this->referencia_id = Carbon::now()->format('dmYHis').'-'. $this->comercio_id;

                	$v_arr = [];
                	$v_id_arr = [];
                	$v_id_arr = [];
                	$var_id_pasar = [];


					//cod entorno testing
					$productVariacionesCreateDB = [];
 
					//return dd($this->cod_variacion);
				    $referenciaId = $this->referencia_id;

                	if($this->variacion_atributo != "c") {
					

					                // Validar si ya hay combinaciones con esas variaciones 
					                
									foreach ($this->variacion_atributo as $key => $value) {

									    $var_arr = variaciones::find($this->variacion_atributo[$key]);
										//Cod entorno testing
										//-->
										if($var_arr !== null){
											//<--											
											$var_arr = $var_arr->nombre;
											$var_id_arr = $this->variacion_atributo[$key];
											array_push($var_id_pasar,$var_id_arr);
										//-->
										}
										
										
									}
									
									if($var_id_pasar !== []){	
									natsort($var_id_pasar); 
									$var_id_pasar = implode("," , $var_id_pasar);
									    //dd($VarIdPasar);
                                    
                                    $cart = new CartVariaciones;
                                
                                    foreach ($cart->getContent() as $key => $variaciones){
                                          
                                    $var_id = $variaciones['var_id'] ?? 0;
                                    
                                    //dd($VarIdPasar,$var_id);
                                    
                                    if($var_id_pasar == $var_id) {
                                    $this->emit('msg-error','La combinacion de variaciones ya existe');
                                    return;
                                    }
                                    }
									    
									}
									
									// si no hay problemas debe continuar con la carga
									
									foreach ($this->variacion_atributo as $key => $value) {

									    $var_arr = variaciones::find($this->variacion_atributo[$key]);
										//Cod entorno testing
										//-->
										if($var_arr !== null){
											//<--											
											$var_arr = $var_arr->nombre;
											$var_id_arr = $this->variacion_atributo[$key];

											productos_variaciones::create([
												'atributo_id' =>  $key,
												'variacion_id' => $this->variacion_atributo[$key],
												'comercio_id' => $comercio_id,
												'referencia_id' => $this->referencia_id
											]);

											array_push($v_arr,$var_arr);
											array_push($v_id_arr,$var_id_arr);
										//-->
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
										'id' => $this->referencia_id,
										//-->
										'product_variacion_create_db'=> $productVariacionesCreateDB
										//<--
										);
										
										//-->
										//$this->testGuardarReferernciaID = $this->referencia_id;
										//<--

										$cart->addProduct($product);
									}
								
                	} else {
                	    dd("Debe agregar al menos una variacion");
                	}
                	
                	
                	// Seteos
					$this->cod_variacion[$referenciaId] = '';
				    // Setear los precios a 0 para esta variación recién añadida
                    $this->costos_variacion[$referenciaId] = 0;
                    $this->precios_internos_variacion[$referenciaId] = 0;
                    $this->precio_lista["{$referenciaId}|0"] = 0;
                     
                    $lista_precios_reglas = $this->GetListaPreciosReglas();
                    
                    foreach($lista_precios_reglas as $lpr){
                    
                    $this->porcentaje_regla_precio_interno_variacion[$referenciaId] = 0; // ver esto
                    $this->porcentaje_regla_precio["{$referenciaId}|0"] = $lpr['porcentaje_defecto'];
                    $this->porcentaje_regla_precio["{$referenciaId}|{$lpr['lista_id']}"] = $lpr['porcentaje_defecto'];
                    
                    }

                
                    // Si tienes múltiples listas de precios, setéalos también
                    foreach ($this->lista_precios as $lp) {
                        $this->precio_lista["{$referenciaId}|{$lp->id}"] = 0;
                    }
                    
                    $datos_variacion = explode("-",$referenciaId);
            	    $referenciaId = $datos_variacion[0];
            	    
                    $lista_precios_reglas = $this->GetListaPreciosReglas();
        
            		$lista_precios = [];
            		
            		foreach ($lista_precios_reglas as $key => $lpr) {
            		        $lista_precios[$key]['variacion_id'] =  $referenciaId;
            				$lista_precios[$key]['lista_id'] = $lpr['lista_id'];
            				$lista_precios[$key]['precio_lista'] = 0;
            				$lista_precios[$key]['porcentaje_regla_precio'] = $lpr['porcentaje_defecto']; // Actualizacion de precios
            				$regla = $this->GetListaPreciosReglaByListaId($lpr['lista_id']);
            				$lista_precios[$key]['regla'] = $regla ? $regla->regla : 1; // Actualizacion de precios
            		}
            		
            		$stock_sucursales = [];
                	$this->loadSucursales();
                	
                	$stock_sucursales[0]['variacion_id'] =  $referenciaId;	
                	$stock_sucursales[0]['sucursal_id'] = 0;
                	$stock_sucursales[0]['almacen_id'] = 1;
                	$stock_sucursales[0]['stock_real'] = 0;
                	$stock_sucursales[0]['stock_disponible'] = 0;
                	$stock_sucursales[0]['stock_comprometido'] = 0;
                					
                	foreach ($this->sucursales as $key => $stock) {
                	        $i = $key + 1;
                		    $stock_sucursales[$i]['variacion_id'] =  $referenciaId;
            				$stock_sucursales[$i]['sucursal_id'] = $stock['sucursal_id'];
                			$stock_sucursales[$i]['almacen_id'] = 1;
                			$stock_sucursales[$i]['stock_real'] = 0;
                			$stock_sucursales[$i]['stock_disponible'] = 0;
                			$stock_sucursales[$i]['stock_comprometido'] = 0;
                	}

                        	
                    $regla_precio_interno = $this->GetListaPreciosReglaByListaId(1);
                    $regla_precio_interno = $regla_precio_interno ? $regla_precio_interno->regla : 1;

                    $variaciones = [];
                    $variaciones[$key] = [
                    'variacion' => $referenciaId
                    ];
                    $array_costos = [];
                    $array_costos = $this->ArmarArrayDeCostosYPrecioInterno($array_costos,$key,$referenciaId,0,0,0,0,0);
            		
            		  
                    $this->emit('valuesUpdated', $variaciones, $array_costos,$lista_precios,$regla_precio_interno,$stock_sucursales);

                    

   $this->SetearAgregarVariacion($referenciaId);
}

public function DestroyVariacion($id_variacion) {

		
	//return dd('Hola desde destroy variacion');
	/*if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;
	*/

	//Cod entorno de testing
	$comercio_id = $this->setComercioId();
	//dd($id_variacion);

	//$productos_variaciones_datos = productos_variaciones_datos::find($id_variacion);

	

	//Cod entorno de testing:
	$productos_variaciones_datos = productos_variaciones_datos::where([
		['id', '=', $id_variacion]
	])->first();

	//return dd($productos_variaciones_datos);

	//Destroy solo productos_variaciones
	if($productos_variaciones_datos === null) {
		$cart = new CartVariaciones;
		$cart->removeProduct($id_variacion);
		unset($this->cod_variacion[$id_variacion]);

		//productos_variaciones::where('referencia_id', '=', $id_variacion)->delete();
	}
	//Destroy variacion completa
	if($productos_variaciones_datos !== null) {
		//Update como eliminado lisat de precio
		$productos_lista_precios = productos_lista_precios::where([
			['product_id', '=', $productos_variaciones_datos->product_id],
			['referencia_variacion', '=', $productos_variaciones_datos->referencia_variacion]
		])->get();

		//return dd($productos_lista_precios);

		if($productos_lista_precios  !== null){
			foreach($productos_lista_precios as $productolista){
				$productolista->eliminado = 1;
				$productolista->save();
			}
		}

		//Update como producto eliminado en productos_stock_sucursales
		$productos_stock_sucursales = productos_stock_sucursales::where([
			['product_id', '=', $productos_variaciones_datos->product_id],
			['referencia_variacion', '=', $productos_variaciones_datos->referencia_variacion]
			])->get();
		
	
		if($productos_stock_sucursales !== null){
			foreach($productos_stock_sucursales as $productoStock){
				$productoStock->eliminado = 1;
				$productoStock->save();
			}
		}

		//dd($productos_variaciones_datos);

		$wc = wocommerce::where('comercio_id', $comercio_id)->first();

		if($wc != null && $productos_variaciones_datos->wc_variacion_id != null){

			$woocommerce = new Client(
				$wc->url,
				$wc->ck,
				$wc->cs,

					[
							'version' => 'wc/v3',
					]
			);


			$woocommerce->delete('products/'.$productos_variaciones_datos->product_id.'/variations/'.$productos_variaciones_datos->wc_variacion_id, ['force' => true]);
		}

		// aca destroyvariacion
		$cart = new CartVariaciones;
		$cart->removeProduct($productos_variaciones_datos->referencia_variacion);
		$productos_variaciones_datos->delete();


	
		//Code entorno de testing
		//Borrar var
		productos_variaciones::where('referencia_id', '=', $id_variacion)->delete();		
		unset($this->cod_variacion[$id_variacion]);

		// Eliminar variacion

		//$productos_variaciones_datos->eliminado = 1;
		//$productos_variaciones_datos->delete();

		// Eliminar stock de la variacion 
		/*$stock = productos_stock_sucursales::where('referencia_variacion',$productos_variaciones_datos->referencia_variacion)->get();
		foreach($stock as $s) {
			$s->delete();
		}*/


		// Eliminar precios de la variacion 
		/*$precio = productos_lista_precios::where('referencia_variacion',$productos_variaciones_datos->referencia_variacion)->get();
		foreach($precio as $p) {
			$p->delete();
		}*/


		$this->emit('global-msg',"VARIACION ELIMINADA");

	/*	$p = Product::find($this->selected_id);

		$this->ResetVariablesVariaciones();
		$this->EditProduct($p);*/

		
	}

	//63return dd($this->cod_variacion);
}




    public function ResetVariablesVariaciones() {
        $this->cod_variacion = [];
        $this->costos_variacion = [];
        $this->productos_variaciones = [];
        $this->stock_productos_sucursales = [];
        $this->datos_variaciones = [];
        $this->productos_lista_precios = [];
        $this->stock_sucursal = [];
        $this->precios_internos_variacion = [];
        $this->real_stock_sucursal = [];
        $this->almacen_id = [];
    }
    

    public function ProductoTipo() {

        if ($this->producto_tipo == "s") {
            if (0 < $this->selected_id) {
                $this->emit('cambiar-tipo-producto', '');
                $this->descuento_costo = 0;
                $this->costo_despues_descuento = 0;
                $this->cost = 0;
                $cart = new CartVariaciones;
                $cart->clear();
                return;
            }
            
            // Resetea los valores
            $this->ResetearValoresSimples();
    
        } elseif ($this->producto_tipo == "v") {
            $this->GetAtributosYvariaciones();
            
            if (0 < $this->selected_id) {
                $this->emit('cambiar-tipo-producto', '');
                $this->ResetVariablesVariaciones();
                $cart = new CartVariaciones;
                $cart->clear();
                return;
            } else {
                // Opcionalmente agrega lógica aquí si es necesario.
            }
        }
        
    }


    public function ResetearValoresSimples()
    {
        $this->descuento_costo = 0;
        $this->costo_despues_descuento = 0;
        $this->cost = 0;
        $this->precio_lista = [];
        $this->stock_sucursal = [];
        $this->precio_interno = 0;
        $this->porcentaje_regla_precio_interno = 0;
        $this->precio_lista["0|0|0|0"] = 0;
        $this->porcentaje_regla_precio["0|0"] = 0;
    
        $lista_precios_reglas = $this->GetListaPreciosReglas();
        
        $regla_precio_interno = $this->GetListaPreciosReglaByListaId(1);
        $regla_precio_interno = $regla_precio_interno ? $regla_precio_interno->regla : 1;
        
		$lista_precios = [];
		
			foreach ($lista_precios_reglas as $key => $lpr) {
                $lista_precios[$key]['variacion_id'] = 0;
				$lista_precios[$key]['lista_id'] = $lpr['lista_id'];
				$lista_precios[$key]['precio_lista'] = 0;
				$lista_precios[$key]['porcentaje_regla_precio'] = $lpr['porcentaje_defecto']; // Actualizacion de precios
            	$lista_precios[$key]['regla'] = $lpr['regla']; // Actualizacion de precios
		}

		$stock_sucursales = [];
		$this->loadSucursales();
		
		$stock_sucursales[0]['variacion_id'] =  0;	
		$stock_sucursales[0]['sucursal_id'] = 0;
		$stock_sucursales[0]['almacen_id'] = 1;
		$stock_sucursales[0]['stock_real'] = 0;
		$stock_sucursales[0]['stock_disponible'] = 0;
		$stock_sucursales[0]['stock_comprometido'] = 0;
					
		foreach ($this->sucursales as $key => $stock) {
		            $i = $key + 1;
		            $stock_sucursales[$i]['variacion_id'] = 0;
				    $stock_sucursales[$i]['sucursal_id'] = $stock['sucursal_id'];
					$stock_sucursales[$i]['almacen_id'] = 1;
					$stock_sucursales[$i]['stock_real'] = 0;
					$stock_sucursales[$i]['stock_disponible'] = 0;
					$stock_sucursales[$i]['stock_comprometido'] = 0;
		}
        
        $key = 0;
        $array_costos = [];
        $costos = $this->ArmarArrayDeCostosYPrecioInterno($array_costos,$key,0,$this->cost,$this->descuento_costo,$this->costo_despues_descuento,$this->porcentaje_regla_precio_interno,$this->precio_interno);
        $variaciones = $this->ObtenerVariaciones("s");

        $this->emit('valuesUpdated', $variaciones, $costos, $lista_precios,$regla_precio_interno,$stock_sucursales);
    }

    public function CambiarProductoTipo($productoTipo) {

    //dd($this->producto_tipo);
    
    
    $this->ResetVariablesVariaciones();
    
    // Si era variable y se elige simple
    
	 if($this->producto_tipo == "s" ){
		$producto = product::where('id',$this->selected_id)->first();	
		
			$producto->update([
				'producto_tipo' => 's'
				]);		
		
			
			$pvd = productos_variaciones_datos::where('product_id',$this->selected_id)->where('eliminado',0)->get();
			//return dd($pvd[0]->referencia_variacion);
			foreach($pvd as $pv) {
				//return dd($pv);
				$this->DestroyVariacion($pv->id);
			}	
			
			
			$productos_stock_sucursales_simples =  productos_stock_sucursales::where('product_id', $this->selected_id)->where('referencia_variacion', 0)->get();

			foreach($productos_stock_sucursales_simples as $productos_stock_sucursales) {
				$productos_stock_sucursales->eliminado = 1;
				$productos_stock_sucursales->save();				
			}
			
			$productos_lista_precios_simples =  productos_lista_precios::where('product_id', $this->selected_id)->where('referencia_variacion', 0)->get();

			foreach($productos_lista_precios_simples as $producto_lista_precio) {
				$producto_lista_precio->eliminado = 1;
				$producto_lista_precio->save();				
			}
			

	 }

    // Si era simple y se elige variable
    
	 if($this->producto_tipo == "v" ){

		$producto = product::where('id',$this->selected_id)->first();

		//return dd($producto->id);		
			$producto->producto_tipo = 'v';
			$producto->save();
		
		
		  
			
			$productos_lista_precios = productos_lista_precios::where([
				['product_id', '=', $producto->id],			
			])->get();
			
			//return dd($productos_lista_precios);

		   //$productos_lista_precios->eliminado = 1;
		   //$productos_lista_precios->save();
		   
		   if($productos_lista_precios  !== null){
			foreach($productos_lista_precios as $productolista){
				$productolista->eliminado = 1;
				$productolista->save();
			}
		}
		
			
		$productos_stock_sucursales = productos_stock_sucursales::where([
			['product_id', '=', $producto->id],
			['referencia_variacion', '=', 0]
		   ])->get();

		   if($productos_stock_sucursales  !== null){
				foreach($productos_stock_sucursales as $sucursal_stock){
					$sucursal_stock->eliminado = 1;
					$sucursal_stock->save();		
				}
			}
		}   
        
    }


    
    public function GetStock($product_id) {
        
    return productos_stock_sucursales::where('product_id',$product_id)->get();
    
        
    }
    
    public function GetPrecios($product_id) {
        
    return productos_lista_precios::where('product_id',$product_id)->get();
    
        
    }

    
public function SincronizarProductoWC($product_id) {
    
    $product = Product::find($product_id);
    
    $wc = wocommerce::where('comercio_id', $this->comercio_id)->first();
    
    if($this->checkCredentials($wc->url, $wc->ck, $wc->cs)) {
        
                try {
                    
                
                if($product->producto_tipo == "v") {
                $this->WocommerceUpdateVariable($product_id);
                } else {
                    if($product->wc_product_id != null) {
                    $this->WocommerceUpdateSimple($product_id);    
                    }   else {
                    $this->WocommerceStoreSimple($product_id);    
                    } 
                }
                
                $this->emit('product-updated','SINCRONIZACION EXITOSA');
            
                
                } catch (Throwable $t) {
                    dd($t);
                }        
    
        
    } else {
        $this->emit('credenciales-invalidas','');
    }

    
}

//cod entorno-testing:

	public function filtrarProductos(){
		if($this->id_categoria) {
			$this->products = $this->products->where('products.category_id', 'like', $this->id_categoria);
		}

		if($this->id_almacen) {
			$this->products = $this->products->where('products.seccionalmacen_id', 'like', $this->id_almacen);
		}

		if($this->proveedor_elegido) {
			$this->products = $this->products->where('products.proveedor_id', 'like', $this->proveedor_elegido);
		}
		
		if($this->etiquetas_filtro) {
			$this->products = $this->products->whereIn('products.id', $this->etiquetas_filtro);
		}
		
		
	}

	public function searchProducto(){
		if(strlen($this->search) > 0) {
			$this->products = $this->products->where( function($query) {
				 $query->where('products.name', 'like', '%' . $this->search . '%')
					->orWhere('products.barcode', 'like',$this->search . '%');
				});
		}
	}
	
	//cod entorno testing

	public function CambiarProductoTipoaSimple() {	
		return dd('hola desde cambiar tipo a simple');
		
		// si el producto tipo nuevo es simple ---> el anterior era variable
		$producto = product::where('id',$this->selected_id)->first();

		//return dd($producto);
		//return dd($producto->tipo_producto);

		if($producto->tipo_producto === 1){
			$producto->update([
				'tipo_producto' => 2,
				'producto_tipo' => 'v'
				]);		
		}
		
		

		
			
			$pvd = productos_variaciones_datos::where('product_id',$this->selected_id)->where('eliminado',0)->get();
			//return dd($pvd[0]->referencia_variacion);
			foreach($pvd as $pv) {
				//return dd($pv);
				$this->DestroyVariacion($pv->id);
				
			
			//$productos_lista_precios_simples =  productos_lista_precios::where('product_id', $this->selected_id)->where('referencia_variacion', 0)->get();


		}	
	}

	public function CambiarProductoTipoaVariable() {		
		
		// si el producto tipo nuevo es simple ---> el anterior era variable
		$producto = product::where('id',$this->selected_id)->first();

		//return dd($producto->id);

		if($producto->tipo_producto === 2){		
			$producto->tipo_producto = 1;
			$producto->producto_tipo = 's';
			$producto->save();
		}	
		
		$productos_lista_precios = productos_lista_precios::where([
			['product_id', '=', $producto->id],
			['referencia_variacion', '=', 0]
		   ])->get();
		  
		   //$productos_lista_precios->eliminado = 1;
		   //$productos_lista_precios->save();
		   
		   if($productos_lista_precios  !== null){
			foreach($productos_lista_precios as $productolista){
				$productolista->eliminado = 1;
				$productolista->save();
			}
		}
		
			
		$productos_stock_sucursales = productos_stock_sucursales::where([
			['product_id', '=', $producto->id],
			['referencia_variacion', '=', 0]
		   ])->first();

			$productos_stock_sucursales->eliminado = 1;
			$productos_stock_sucursales->save();
		
			$this->resetUI();

	}
	/*public function eliminarVariacionCart(){

	}*/

	public function ResetAgregar() {
	    $this->resetUI();
	    $this->GetDatosReglasPreciosMount(); // Actualizar precios
	    $cart = new CartVariaciones;
		$cart->clear();
	    $this->agregar = 0; 
	    $this->producto_tipo = 'Elegir';
	    $this->tipo_unidad_medida = 9;
        $this->cantidad_unidad_medida = 1;


	}
	
	
	public function RenderFiltrar($products,$categoria,$almacen,$proveedor,$etiquetas,$marcas,$es_insumo_elegido) {
	    	
	    	if($es_insumo_elegido) {
            
            if($es_insumo_elegido == 2){
                $es_insumo_elegido = 0;
            }
			$products = $products->where('products.es_insumo', $es_insumo_elegido);

			}
			
	    	if($categoria) {

			$products = $products->where('products.category_id', $categoria);

			}
			
			if($marcas) {

			$products = $products->where('products.marca_id', $marcas);

			}
			
			if($almacen) {

			$products = $products->where('products.seccionalmacen_id',$almacen);

			}

			if($proveedor) {

			$products = $products->where('products.proveedor_id', $proveedor);

			}
			
			if($etiquetas) {

			$products = $products->whereIn('products.id', $etiquetas);

			}

            return $products;
	}

	public function Filtrar() {
	    $this->render();
	}
	
	/*
    public function CambiarStockDisponible($index) {
        
    //    dd($index);
        if($this->selected_id != 0) {
        
        if(empty($this->real_stock_sucursal[$index])){ $this->real_stock_sucursal[$index] = 0;}
        if(empty($this->stock_sucursal_comprometido[$index])){ $this->stock_sucursal_comprometido[$index] = 0;}
        
        $this->stock_sucursal[$index] = $this->real_stock_sucursal[$index] - $this->stock_sucursal_comprometido[$index];
        
        // Puedes hacer lo que quieras con el resultado, como guardarlo en una base de datos o mostrarlo en la vista.            
        }

    }
    */
 
 public function resetUIProveedor(){
    $this->nombre_proveedor = "";
    $this->telefono_proveedor = "";
    $this->mail_proveedor = "";
    $this->direccion_proveedor = "";
    $this->localidad_proveedor = "";
    $this->provincia_proveedor = "";
    $this->proveedor = "";
    $this->emit('proveedor-hide','');
 }   
 
  public function resetUIAlmacen(){
     $this->name_almacen = "";
     $this->almacen = 'Elegir';
     $this->emit('almacen-hide','');
     
 }  
 
   public function resetUICategoria(){
     $this->name_categoria = "";
     $this->categoryid = 1;
     $this->emit('category-hide','');
     
 }  


    public function FaltaVariacion(){
        
    $this->emit("msg-error","NO PUEDE GUARDAR UN PRODUCTO VARIABLE SIN VARIACIONES ASOCIADAS");
    $this->error_variacion = 'Debe incluir al menos alguna variacion';
    $this->mostrarErrorVariacion = true;
    return;
    }
    
    public function ElegirTipoProducto(){
        
    $this->emit("msg-error","DEBE ELEGIR EL TIPO DE PRODUCTO");
    $this->mostrarErrorTipoProducto = true;

    }

public function SetearProductoSimple() {
    
        //dd($variacion);
		
		if($this->tipo_usuario->sucursal != 1) {			
			
		$sucursales = sucursales::select('sucursales.sucursal_id')
		->where('casa_central_id', $this->comercio_id)
		->where('sucursales.eliminado',0)
		->get();

		} else {			            
		$sucursales = sucursales::select('sucursales.sucursal_id')
		->where('casa_central_id', $this->casa_central_id)
		->where('sucursales.eliminado',0)
		->get();
		}
	
    // seteamos el costo
    $this->cost = "";   
    // seteamos precio interno
    $this->precio_interno = "";
  
    // tomamos las listas de precios
    $lista_de_precios = $this->getListaPrecios($this->casa_central_id);
        
    // seteamos el precio 
    /*    
    if(0 < $lista_de_precios->count()){
    foreach($lista_de_precios as $lp){
    $in = "0|".$lp->id."|0|0";
    $this->precio_lista[$in] = "";
    }            
    }
 
 
    // seteamos la casa central
    $index_cs = "0|0|0|0";
    $this->real_stock_sucursal[$index_cs] = "";
    $this->almacen_id[$index_cs] = 1;
    $this->stock_sucursal[$index_cs] = "";
    $this->stock_sucursal_comprometido[$index_cs] = "";
 
 	foreach($sucursales as $llave => $sucu) {

    $this->almacen_id["0|".$sucu['sucursal_id']."|0|0"] = 1;
	$this->stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] = "";
	$this->real_stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] = "";
	$this->stock_sucursal_comprometido["0|".$sucu['sucursal_id']."|0|0"] = ""; 
	}
	*/
			
                	
    }

public function ValidarPrecioBaseSimple() {
    if ($this->precio_lista != null) {
        $ind = "0|0|0|0";

        // Comprobar si existe
        if (isset($this->precio_lista[$ind])) {
            if ($this->precio_lista[$ind] == "") {
                $this->emit('msg-error', 'Debe elegir el precio de venta');
                return false;
            }
        } else {
            $this->emit('msg-error', 'Debe elegir el precio de venta');
            return false;
        }
    } else {
        $this->emit('msg-error', 'Debe elegir el precio de venta');
        return false;
    }

    // El código restante después de la validación
    // Continuará ejecutándose solo si no se ha producido un 'return'.
}

public function SetearAgregarVariacion($referencia_variacion) {
    
        //dd($variacion);
		
		if($this->tipo_usuario->sucursal != 1) {			
			
		$sucursales = sucursales::select('sucursales.sucursal_id')
		->where('casa_central_id', $this->comercio_id)
		->where('sucursales.eliminado',0)
		->get();


		} else {			            
		$sucursales = sucursales::select('sucursales.sucursal_id')
		->where('casa_central_id', $this->casa_central->casa_central_id)
		->where('sucursales.eliminado',0)
		->get();
		}
	
	
    $cart = new CartVariaciones;

    foreach ($cart->getContent() as $key => $variaciones){
          
    $var_nombre = $variaciones['var_nombre'] ?? 0;
    $var_id = $variaciones['var_id'] ?? 0;
    
    //dd($var_id);
    
    if($referencia_variacion == $variaciones['referencia_id']){
        
    // seteamos la casa central
    $index_cs = $variaciones['referencia_id']."|0|".$var_nombre."|".$var_id;
    $this->almacen_id[$index_cs] = 1;
    $this->real_stock_sucursal[$index_cs] = "";
    $this->stock_sucursal[$index_cs] = "";
    $this->stock_sucursal_comprometido[$index_cs] = "";
    
    // seteamos las sucursales    
    foreach($sucursales as $s){
    
    $index = $variaciones['referencia_id']."|".$s->sucursal_id."|".$var_nombre."|".$var_id;
    $this->real_stock_sucursal[$index] = 1;
    $this->real_stock_sucursal[$index] = "";
    $this->stock_sucursal[$index] = "";
    $this->stock_sucursal_comprometido[$index] = "";
    

    }
                	
    }
                	
    }
                	
}
    public function seleccionarImagenBase64($base64Image, $nombreArchivoOriginal){
    $this->base_64_archivo = $base64Image;
    $this->base_64_nombre = $nombreArchivoOriginal;
    $this->imagen_seleccionada = $base64Image;
    
    // Reemplazar espacios en blanco por "|+|+|!"
    $nombreArchivo = str_replace(' ', '|+|+|!', $nombreArchivoOriginal);
         
    // Decodificar la cadena Base64 y guardar la imagen
    $image = Image::make($base64Image);
    $image->save(public_path('/storage/products/'.$nombreArchivo));

    $imagen_id = imagenes::create([
    'name' => $nombreArchivoOriginal,
    'url' => $nombreArchivo,
    'base64' => $base64Image,
    'comercio_id' => $this->casa_central_id,
    'eliminado' => 0
    ]);

    }
    
    public function guardarImagenBase64($product,$base64Image, $nombreArchivoOriginal)
    {

        // Reemplazar espacios en blanco por "|+|+|!"
        $nombreArchivo = str_replace(' ', '|+|+|!', $nombreArchivoOriginal);
            
        $product->update([
            'image' => $nombreArchivo
            ]);
            
       // $product->save();

    }

public function BuscarEtiqueta() {
   $this->etiquetas = etiquetas::where('comercio_id',$this->casa_central_id)->where('eliminado',0)->where('name', 'like', '%' . $this->search_etiqueta . '%')->get();  
}

public function toggleDiv()
{
    $this->mostrarDiv = !$this->mostrarDiv;
}
    
public function AgregarEtiqueta($value){
        
        $etiquetas = etiquetas::find($value);
        
        if (!$this->existeIdEtiqueta($value)) {
        array_push($this->etiquetas_seleccionadas, ['id' => $value, 'nombre' => $etiquetas->name] );
        }
        
        $this->search_etiqueta = "";
        $this->toggleDiv();
 
}

private function existeIdEtiqueta($id)
    {
        // Verificar si el ID ya existe en el array
        foreach ($this->etiquetas_seleccionadas as $etiqueta) {
            if ($etiqueta['id'] == $id) {
                return true;
            }
        }

        return false;
    }

    public function eliminarEtiqueta($id)
    {
        // Filtrar el array para quitar la etiqueta con el ID dado
        $this->etiquetas_seleccionadas = array_filter($this->etiquetas_seleccionadas, function ($etiqueta) use ($id) {
            return $etiqueta['id'] != $id;
        });
        
        $this->toggleDiv();
    }


    public function LimpiarFiltros(){
    $this->id_categoria = 0;
    $this->id_almacen = 0;
    $this->proveedor_elegido = 0;
    $this->search = "";
    }
    
    public function EtiquetasSeleccionadas($Etiquetas)
    {
    $this->SetEtiquetasSeleccionadas($this->comercio_id,$Etiquetas,"productos");
    }
    
    public function SearchEtiquetas($value){
    
    $this->etiquetas_filtro = $this->BuscarEtiquetas($value,$this->comercio_id,"productos"); 
    
    //dd($this->etiquetas_filtro);
    $this->etiquetas_filtro_excel = implode(",",$this->etiquetas_filtro);
    }

    public function GetDatosFacturacionDefectoProducto($accion) {
        // Obtener todas las sucursales
        $sucursales = sucursales::where('casa_central_id', $this->casa_central_id)
                                ->where('eliminado', 0)
                                ->get();
        
        // Agregar la sucursal central a la colección si no está presente
        $sucursal_central_existente = $sucursales->where('sucursal_id', $this->casa_central_id)->first();
        if (!$sucursal_central_existente) {
            $nuevaSucursal = new sucursales(['sucursal_id' => $this->casa_central_id, 'casa_central_id' => $this->casa_central_id]);
            $sucursales->push($nuevaSucursal);
        }
        
        // Iterar sobre cada sucursal y obtener los datos de facturación
        foreach($sucursales as $sucursal) {
            if($accion == 1){
            $datos_facturacion = datos_facturacion::select('iva_defecto as iva')->where('predeterminado',1)->where('eliminado',0)->where('comercio_id', $sucursal->sucursal_id)->first();
            }
            if($accion == 2){
            $datos_facturacion = productos_ivas::where('product_id', $this->selected_id)->where('sucursal_id', $sucursal->sucursal_id)->first();
            }
            if ($datos_facturacion != null) {
                // Formatear el valor de iva_defecto
                $valor_formateado = number_format($datos_facturacion->iva, 3, '.', ''); // 0.210
                $this->porcentaje_iva[$sucursal->sucursal_id] = $valor_formateado;
            } else {
                $this->porcentaje_iva[$sucursal->sucursal_id] = 0;
            }
        }
    }
    
	public function GetProductIva($product_id,$casa_central_id){

	$productos_iva = productos_ivas::where('product_id',$product_id)->where('comercio_id',$casa_central_id)->get();

    if(0 < $productos_iva->count()){
	foreach($productos_iva as $pi) {
        $valor_formateado = number_format($pi->iva, 3, '.', ''); // 0.210
        $this->porcentaje_iva[$pi->sucursal_id] = $valor_formateado;
    } }       

	}

    
    
    public function ActualizarIVAComoDefecto(){
    

    // Obtener todas las sucursales
    $sucursales = sucursales::where('casa_central_id', $this->casa_central_id)
                            ->where('eliminado', 0)
                            ->get();
    
    // Agregar la sucursal central a la colección si no está presente
    $sucursal_central_existente = $sucursales->where('sucursal_id', $this->casa_central_id)->first();
    if (!$sucursal_central_existente) {
        $nuevaSucursal = new sucursales(['sucursal_id' => $this->casa_central_id, 'casa_central_id' => $this->casa_central_id]);
        $sucursales->push($nuevaSucursal);
    }
    
    $productos = Product::where('comercio_id',$this->casa_central_id)->get();
    
    foreach($productos as $producto){
        foreach($sucursales as $sucursal){
            $datos_facturacion = datos_facturacion::select('iva_defecto as iva')->where('comercio_id', $sucursal->sucursal_id)->first();
            		    
            productos_ivas::updateOrCreate(
            ['product_id' => $producto->id, 'comercio_id' => $producto->comercio_id, 'sucursal_id' => $sucursal->sucursal_id],
            ['iva' => $datos_facturacion->iva] // Puedes agregar aquí otros campos y sus valores
            );
            
        }
    }
        
    
     $this->emit("msg-error","TERMINADO");   
    }
    
    // 6-6-2024 

public function StoreMarca() {
    
    $rules = [
	'name_marca' => 'required|min:3'
	];

	$messages = [
	'name_marca.required' => 'Nombre de la marca es requerido',
	'name_marca.min' => 'El nombre de la marca debe tener al menos 3 caracteres'
	];

	$this->validate($rules, $messages);

	$marca = marcas::create([
		'name' => $this->name_marca,
		'comercio_id' => $this->comercio_id,
	]);

    $this->loadMarcas();
    $this->marca_id = $marca->id;
	$this->name_marca = "";
	$this->emit('marca-added','Marca agregada');
	$this->emit('modal-show','Show modal');

}

    public function UpdateRecetas($insumo_id){
        $insumos = Product::find($insumo_id);
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
    
    public function resetUIMarca(){
    $this->name_marca = null;
    $this->marca_id = 1;
	$this->emit('marca-added','Selecciona una marca');
    }
    
     // Función para validar si el valor es numérico y no está vacío
    public function validarValorNumerico($valor)
    {
        return is_numeric($valor) && $valor !== "" ? $valor : 0;
    }


    public function QueryReglaPrecios($product) {
        
        foreach ($this->regla_precio as $key => $value) {
                
                $variacion = 0;
                // Busca el precio de la lista
                $regla = productos_lista_precios::where('lista_id', $key)
                    ->where('product_id', $product->id)
                    ->where('referencia_variacion', $variacion)
                    ->first();
                
                // si existe el precio de la lista de precios la actualiza 
                if($regla != null) {
                    // Verifica los valores antes de actualizar
                    $regla->regla_precio = $this->regla_precio[$key];
                    $regla->save();
    
                }                 
            

        }
    }
    
    public function HistoricoActualizacionPrecios($product_id,$variacion,$lista_id,$precio_viejo,$precio_nuevo,$comercio_id,$user_id){
        
        if(0 < $precio_viejo){
        $porcentaje_actualizacion = ($precio_nuevo/$precio_viejo) - 1;    
        } else {
        $porcentaje_actualizacion = 0;    
        }
        
        $product = Product::find($product_id);
        $pvd = productos_variaciones_datos::where('referencia_variacion',$variacion)->where('product_id',$product->id)->where('eliminado',0)->first();    
        
        if($porcentaje_actualizacion != "0" || $porcentaje_actualizacion != 0){
        $actualizacion_precios = actualizacion_precios::create([
            /*
            'product_barcode' => $product->barcode. " " . $pvd ? $pvd->barcode : '',
            'product_barcode_variacion' => $product->barcode. " " . $pvd ? $pvd->barcode : '',
            'product_name' => $product->name. " " . $pvd ? $pvd->variaciones : '',
            */
            'product_id' => $product_id,
            'referencia_variacion' => $variacion,
            'lista_id' => $lista_id,
            'precio_viejo' => $precio_viejo,
            'precio_nuevo' => $precio_nuevo,
            'porcentaje_actualizacion' => $porcentaje_actualizacion,
            'user_id' => $user_id,
            'comercio_id' => $comercio_id
            ]);  
        
        }
        
        
    }
    
    public function GetListaPreciosReglas(){
        
        $lista_precios = $this->getListaPrecios($this->casa_central_id);
        $lista_precios_reglas = [];
        
        $lpr_base = $this->GetListaPreciosReglaByListaId(0);
        $porcentaje_defecto_base = $lpr_base ? $lpr_base->porcentaje_defecto : 0;
        $regla_base = $lpr_base ? $lpr_base->regla : 0;
            
            $array = [
                'lista_id' => 0,
                'porcentaje_defecto' => $porcentaje_defecto_base,
                'regla' => $regla_base
            ];
            array_push($lista_precios_reglas,$array);
            
            foreach($lista_precios as $key => $lp){
            $lpr = $this->GetListaPreciosReglaByListaId($lp->id);
            $porcentaje_defecto = $lpr ? $lpr->porcentaje_defecto : 0;
            $regla = $lpr ? $lpr->regla : 0;
            
            $array = [
                'lista_id' => $lp->id,
                'porcentaje_defecto' => $porcentaje_defecto,
                'regla' => $regla
                ];
                
            array_push($lista_precios_reglas,$array);
        }
        /*
        return lista_precios_reglas::join('lista_precios','lista_precios.id','lista_precios_reglas.lista_id')
        ->where('lista_precios_reglas.comercio_id',$this->casa_central_id)
        ->where('lista_precios.eliminado',0)
        ->get();
        */
        return $lista_precios_reglas;
    }
    
    
    public function GetListaPreciosReglaByListaId($lista_id){
        return lista_precios_reglas::where('lista_id',$lista_id)->where('comercio_id',$this->casa_central_id)->first();
    }
    
    public function convertirFormatoMoneda($valor) {
    // Reemplazar la coma con punto
    $valor = str_replace(',', '.', $valor);
    return $valor;
    }
    

  public function SeleccionarEnLoteFiltrado(){
        
        	$products = Product::select('products.id')
				->where('products.comercio_id', $this->casa_central_id)
				->where('products.eliminado', $this->estado_filtro);

			$products = $this->RenderFiltrar($products,$this->id_categoria,$this->id_almacen,$this->proveedor_elegido,$this->etiquetas_filtro,$this->id_marca,$this->es_insumo_elegido);
			
			//Buscador productos
			//$this->searchProducto();
            
            $products_list = [];
			$products = $products->pluck('id')->toArray();	
            foreach($products as $p){
                $product = Product::find($p);
                if($product->producto_tipo == "s"){
                    array_push($products_list,$product->id."-0");
                } else {
                    $pvd = productos_variaciones_datos::where("product_id",$product->id)->get();
                    foreach($pvd as $pv){
                        array_push($products_list,$product->id."-".$pv->referencia_variacion);
                    }
                }
            }
            
          //  dd($products_list);
            
            // Asigna los IDs al array id_check de Livewire
            $this->id_check = $products_list;
            
    }
  
      
      // 22-9-2024
    public function SeleccionEnLoteModal(){
        $this->emit("modal-seleccion-en-lote","");
    }    
    // 22-9-2024
    public function ActualizacionModal(){
        $this->emit("modal-actualizacion-en-lote","");
    }    
    
    
    // 22-9-2024

    public function IniciarDescuento(){
        // Inicializar con un descuento por defecto si lo deseas
        $this->descuentos_productos = [
            ['id' => 1, 'descuento' => 0],
            ['id' => 2, 'descuento' => 0],
            ['id' => 3, 'descuento' => 0],
            ['id' => 4, 'descuento' => 0],
        ];
    }
    
    public function AgregarDescuento()
    {
        // Agregar un nuevo descuento con valor 0
        $this->descuentos_productos[] = ['id' => count($this->descuentos_productos) + 1, 'descuento' => 0];
    }

    public function GuardarDescuentosEnLote($ids)
    {
        $this->id_check = $ids;
        
        foreach($this->id_check as $producto){
        $datos = explode("-",$producto);
        $product_id = $datos[0];
        $referencia_variacion = $datos[1];     
    
        $product = Product::find($product_id);
        
        if($product->producto_tipo == "v"){$referencia_variacion = $referencia_variacion."-".$product->comercio_id;}
                
        // Inicializa el precio final con el precio original
        $descuento_final = 1;
       
       // Aquí puedes hacer la lógica para guardar en la base de datos
        foreach ($this->descuentos_productos as $descuento) {
                
                // Supongamos que tienes un modelo Descuento que usas para guardar
                 $desc = $descuento['descuento']/100;
                
                $listas_descuentos = listas_descuentos::updateOrCreate([
                'nro_descuento' => $descuento['id'],
                'comercio_id' => $this->casa_central_id
                ],
                [
                'comercio_id' => $this->casa_central_id
                ]);   
                
                $lista_id = $listas_descuentos->id;
                
                productos_descuentos::updateOrCreate([
                'product_id' => $product_id,
                'referencia_variacion' => $referencia_variacion,
                'nro_descuento' => $descuento['id'],
                'lista_descuento_id' => $lista_id
                ],
                [
                'descuento' => $desc
                ]);       

        $descuento_valor = (float) $descuento['descuento']/100; // Asegúrate de convertir el descuento a float
        $descuento_final *= (1 - $descuento_valor); // Aplica el descuento
        }
        

        if($product->producto_tipo == "s"){
            $product->descuento_costo = (1 - $descuento_final);
            $product->save();
            $costo = $product->cost;            
        
            $this->updateCost($product->id,$referencia_variacion, $costo);
        } 
        if($product->producto_tipo == "v"){
            $pvd = productos_variaciones_datos::where("product_id",$product->id)->where("referencia_variacion",$referencia_variacion)->where("eliminado",0)->first();
            $pvd->descuento_costo = (1 - $descuento_final);
            $pvd->save();
            $costo = $pvd->cost;            
            
            $this->updateCost($pvd->id,$referencia_variacion, $costo);
        } 

        
        
        
        }

        $this->id_check = [];
    
        $this->emit('msg-success', 'Descuentos guardados exitosamente.');
        $this->IniciarDescuento();
        $this->emit("modal-descuento-en-lote-hide","");
    }
    
    public function resetUIDescuento(){
        $this->emit("modal-descuento-en-lote-hide","");
    }
    
    // Método para abrir el modal y mostrar los descuentos
    public function DescuentosModal($product_id, $referencia_variacion)
    {
        // Guardar la variación actual
        $this->variacion_elegida_modal_descuento = $referencia_variacion;
        
        if($referencia_variacion != "0"){
            $referencia_variacion = $referencia_variacion."-".$this->casa_central_id;
        } 
        
        if(!isset($this->descuentos_variaciones[$referencia_variacion]) || empty($this->descuentos_variaciones[$referencia_variacion])){
         $this->IniciarDescuento();
         $this->descuentos_variaciones[$referencia_variacion] = $this->descuentos_productos;
        } else {
         $this->descuentos_productos = $this->descuentos_variaciones[$referencia_variacion];    
        }
        
        
        // Mostrar el modal de descuentos
        $this->es_descuento_individual = 1;
        $this->emit("modal-descuento-en-lote", "mensaje");
    }
    
    // Método para guardar el descuento
    public function GuardarDescuento()
    {
        $product_id = $this->selected_id;
        $referencia_variacion = $this->variacion_elegida_modal_descuento;
        
        // Calcular el descuento basado en los datos actuales
        $datos_descuento = $this->calcularCostoDespuesDeDescuentoStore($this->descuentos_productos);
        $costo_final = $datos_descuento['costo_final'];
        $descuento_final = $datos_descuento['descuento_final'];
    
        if($referencia_variacion != "0"){
            $referencia_variacion_nueva = $referencia_variacion."-".$this->casa_central_id;
        } else {
            $referencia_variacion_nueva = $referencia_variacion;
        }
        
        $this->descuentos_variaciones[$referencia_variacion_nueva] = [];
        
        // Asignar los nuevos descuentos a la variación
        $this->descuentos_variaciones[$referencia_variacion_nueva] = $this->descuentos_productos;
        
        // Convertir el descuento final en porcentaje y guardarlo
        $this->descuento_costo = round($descuento_final * 100, 2);
    
        // Emitir el evento para actualizar el proveedor con el nuevo descuento
        $this->emit('actualizarDescuentoProveedor', $this->descuento_costo, $referencia_variacion);
        $this->emit("modal-descuento-en-lote-hide", "");
    
        // Limpiar la variación seleccionada después de guardar el descuento
        $this->variacion_elegida_modal_descuento = null;
    }


    /*
    public function DescuentosModal($product_id,$referencia_variacion){
        $descuentos_bd = $this->GetDescuentoByProductId($product_id,$referencia_variacion);
        $cantidad_descuentos_bd = count($descuentos_bd);
        $this->variacion_elegida_modal_descuento = $referencia_variacion;
   //     $this->descuentos_variaciones[$referencia_variacion]; 
        
        if(0 < $cantidad_descuentos_bd){
            $this->descuentos_productos = [];
            foreach($descuentos_bd as $descuento) {
                $this->descuentos_productos[] = ['id' => $descuento->nro_descuento, 'descuento' => $descuento->descuento * 100 ];
            }   
                    
        } else {
            if(isset($this->descuentos_variaciones[$referencia_variacion])){
                foreach($descuentos_bd as $descuento) {
                    $this->descuentos_productos[] = ['id' => $this->descuentos_variaciones[$referencia_variacion]['id'], 'descuento' => $this->descuentos_variaciones[$referencia_variacion]['descuento']];
                }  
            }  else {
                $this->IniciarDescuento();
            }   
        }
               
        
        $this->es_descuento_individual = 1;
        $this->emit("modal-descuento-en-lote","");

    }

    public function GuardarDescuento()
    {
        $product_id = $this->selected_id;
        $referencia_variacion = $this->variacion_elegida_modal_descuento;
        
        // Calcular el descuento
        $datos_descuento = $this->calcularCostoDespuesDeDescuentoStore($this->descuentos_productos);
        $costo_final = $datos_descuento['costo_final'];
        $descuento_final = $datos_descuento['descuento_final'];
        
        $this->descuentos_variaciones[$referencia_variacion] = $this->descuentos_productos;
     
        // Convertir el descuento a porcentaje
        $this->descuento_costo = round($descuento_final * 100, 2);
     
        // Emitir evento con el descuento actualizado
        $this->emit('actualizarDescuentoProveedor', $this->descuento_costo, $referencia_variacion);
        $this->emit("modal-descuento-en-lote-hide", "");
        
        // Limpiar la variación seleccionada
        $this->variacion_elegida_modal_descuento = null;
    }
    */
    /*
        public function GuardarDescuento()
    {
        $product_id = $this->selected_id;
        $referencia_variacion = $this->variacion_elegida_modal_descuento;
        dd($this->descuentos_productos);
        
        $datos_descuento = $this->calcularCostoDespuesDeDescuentoStore($this->descuentos_productos);
	    $costo_final = $datos_descuento['costo_final'];
	    $descuento_final = $datos_descuento['descuento_final'];
	    
	    $this->descuento_costo = round($descuento_final * 100, 2);
        //$this->costo_despues_descuento = round($costo_final, 2);

        $this->emit('actualizarDescuentoProveedor', $this->descuento_costo,$referencia_variacion);
        $this->emit("modal-descuento-en-lote-hide","");
        $this->variacion_elegida_modal_descuento = null;
    }
    */
    
    public function StoreUpdateDescuentosProveedor($product_id,$referencia_variacion){
        
        // Aquí puedes hacer la lógica para guardar en la base de datos
        foreach ($this->descuentos_variaciones as $key => $descuentos) {
            $referencia_variacion = $key;
            
            if (is_array($descuentos)) {
            foreach($descuentos as $descuento){
            
            // Supongamos que tienes un modelo Descuento que usas para guardar
            $desc = $descuento['descuento']/100;
            
            $lista_descuentos = listas_descuentos::updateOrCreate([
                'nro_descuento' => $descuento['id']
                ],
                [
                'comercio_id' => $this->casa_central_id
                ]);  
                
            $productos_descuentos =  productos_descuentos::updateOrCreate([
                'product_id' => $product_id,
                'referencia_variacion' => $referencia_variacion,
                'nro_descuento' => $descuento['id'],
                'lista_descuento_id' => $lista_descuentos->id
                ],
                [
                'descuento' => $desc
                ]);    
            }
            }
        
        }
        
        $this->descuentos_variaciones = [];
    }
    
    public function GetDescuentoByProductId($product_id,$referencia_variacion){
        return productos_descuentos::where('product_id',$product_id)->where('referencia_variacion',$referencia_variacion)->get();
    }
    
    public function calcularCostoDespuesDeDescuentoStore($descuentos) {
    
        // Inicializa el precio final con el precio original
        $descuento_final = 1;
        
        // Aplica cada descuento
        foreach ($descuentos as $descuento) {
            $descuento_valor = (float) $descuento['descuento']/100; // Asegúrate de convertir el descuento a float
            $descuento_final *= (1 - $descuento_valor); // Aplica el descuento
        }
        
        return ['costo_final' => 0 , 'descuento_final' => (1 - $descuento_final) ]; // Devuelve el precio final después de aplicar todos los descuentos
    }
    
    public function calcularCostoDespuesDeDescuento($product_id,$referencia_variacion, $costo_original) {
    // Obtén los descuentos para el producto
    $descuentos = $this->GetDescuentoByProductId($product_id,$referencia_variacion);

    // Inicializa el precio final con el precio original
    $costo_final = $costo_original;
    $descuento_final = 1;

    // Aplica cada descuento
    foreach ($descuentos as $descuento) {
        $descuento_valor = (float) $descuento->descuento; // Asegúrate de convertir el descuento a float
        $costo_final *= (1 - $descuento_valor); // Aplica el descuento
        $descuento_final *= (1 - $descuento_valor); // Aplica el descuento
    }
    $descuento_final =  (1 - $descuento_final);
    $descuento_final = round($descuento_final * 100, 2);
    $costo_final = round($costo_final, 2);
    
    return ['costo_final' => $costo_final , 'descuento_final' => $descuento_final ]; // Devuelve el precio final después de aplicar todos los descuentos
}

    public function guardarDatos($variaciones, $preciosYPorcentajesListas, $Stocks,$Costos,$base64Image, $nombreArchivoOriginal)
	{
		//Cod entorno testing
		$comercio_id = $this->setComercioId();
        
        $this->resetValidation(); // Limpia los errores previos
        
        if(0 < $this->selected_id) {$accion = "update"; } else { $accion = "store";  }
        $resultado_validacion = $this->ValidarCodigos($accion, $this->barcode, $this->cod_proveedor, $this->casa_central_id);
        if($resultado_validacion != false){
        $this->emit("msg-error",$resultado_validacion);
        return;
        }
        
        if($base64Image != ""){
        $this->seleccionarImagenBase64($base64Image, $nombreArchivoOriginal);    
        }
        
        
        $this->validateProduct();
        
        //28-5-2024
        $this->GetConfiguracion();
        
        $msg_validar_codigos_pesables = $this->ValidarCodigosPesables();
        if($msg_validar_codigos_pesables != false){$this->emit("msg-error",$msg_validar_codigos_pesables); return;}

        $this->SetDatosGeneralesProducto();

        // --------------PRODUCTO SIMPLE ------------ //
        
        if($this->producto_tipo == "s") {
        $product = $this->HandleGuardar($variaciones, $preciosYPorcentajesListas, $Stocks,$Costos);
        }
        
        // ------------ PRODUCTOS VARIABLES -------------- //

        if($this->producto_tipo == "v") {
        $msg = $this->ValidarCodigosVariable();
        if($msg != false){$this->emit("msg-error",$msg); return;}
        //$product =  $this-> HandleStoreVariable();
        $product =  $this-> HandleGuardar($variaciones, $preciosYPorcentajesListas, $Stocks,$Costos);
        }

       	// ------------ CREA Y ACTUALIZA EL IVA -------------- //

        $this->QueryIVA($product->id,$product->comercio_id);        
        
       	$this->StoreUpdateEtiquetas($product->id,1,"productos",$product->comercio_id);
       	
		///////////////////////////////////////////////////////
        //dd($product);
        $product_responde = $product;
        
		$this->ResetAgregar();

		$cart = new CartVariaciones;
		$cart->clear();
		$this->variacion_atributo = "c";
		$product = null;
		$pvd = null;
		$this->productos_lista_precios = null;
		$this->productos_stock_sucursales = null;
		$this->precio_lista = null;
		$this->stock_sucursal = null;
		$this->real_stock_sucursal = null;
		$this->almacen_id = null;
		$this->stock_sucursal_comprometido = null;
		$this->costos_variacion = null;
		$key = null;
		$llave = null;
    
        $this->emit('datosGuardados');
        
		$this->emit('product-added', 'Producto Registrado');
    
        return $product_responde;

	}

    protected function validateProduct()
    {
        $rules = [
            'name' => 'required',
            'barcode' => [
                'required',
                Rule::unique('products')->ignore($this->selected_id)->where('comercio_id', $this->setComercioId())->where('eliminado', 0)
            ],
            'alerts' => 'required',
            'proveedor' => 'required|not_in:AGREGAR',
            'categoryid' => 'required|not_in:Elegir|not_in:AGREGAR',
            'marca_id' => 'required|not_in:Elegir|not_in:AGREGAR',
            'stock_descubierto' => 'required|not_in:Elegir',
            'tipo_producto' => 'required|not_in:Elegir'
        ];
    
        $messages = [
            'name.required' => 'Nombre del producto requerido',
            'name.unique' => 'El nombre del producto ya existe, elija otro',
            'barcode.required' => 'El código del producto es requerido',
            'barcode.unique' => 'El código del producto ya está en uso',
            'proveedor.required' => 'El proveedor es requerido',
            'proveedor.not_in' => 'Elija un proveedor válido',
            'alerts.required' => 'Ingresa el valor mínimo en existencias',
            'categoryid.not_in' => 'Elija una categoría válida',
            'marca_id.not_in' => 'Elija una marca válida',
            'tipo_producto.not_in' => 'Elija el tipo de producto',
            'stock_descubierto.not_in' => 'Elija si maneja o no stock',
        ];
    
        $this->validate($rules, $messages);
    }
    public function ValidarCodigosVariable(){
        // Ver si hay codigos variables vacios
        $vacios = $this->DetectarCodigosVariablesVacios();
        if($vacios == true) {
        return "existen codigos de variaciones vacios";
        }
            
        // Ver si hay codigos variables repetidos
        
        $repetidos = $this->DetectarCodigosVariablesRepetidos();
        if($repetidos == true) {
        return "Existen codigos de variaciones repetidos";
        }

        if($vacios != false || $repetidos != false) {
        return false;
        }    
        
    }
    
    public function ArmarArrayProduct($unidad_medida,$relacion_producto_base){
        return [
        			'name' => $this->name,
        			'cost' => $this->cost,
        			'price' => 0,
        			'barcode' => $this->barcode,
        			'stock' => 0,
        			'alerts' => $this->alerts,
        			'tipo_producto' => $this->tipo_producto != ''? $this->tipo_producto : 1 , 
        			'stock_descubierto' => $this->stock_descubierto,
        			'seccionalmacen_id' => 1,
        			'category_id' => $this->categoryid,
        			'proveedor_id' => $this->proveedor != ''? $this->proveedor : 1,
        			'precio_interno' => $this->precio_interno != ''? $this->precio_interno : 0,
        			'comercio_id' => $this->casa_central_id,
        			'iva' => $this->iva,
        			'relacion_precio_iva' => $this->relacion_precio_iva,
        			'cod_proveedor' => $this->cod_proveedor,
        			'mostrador_canal' => $this->mostrador_canal,
        			'ecommerce_canal' => $this->ecommerce_canal,
        			'wc_canal' => $this->wc_canal,
        			'descripcion' => $this->descripcion,
        			'producto_tipo' => $this->producto_tipo,
        		    //	29-8-2024
        		    'relacion_unidad_medida' => $relacion_producto_base, // 29-8-2024
                    'tipo_unidad_medida' => $unidad_medida ? $unidad_medida->tipo_unidad_medida : 3, // 29-8-2024
                    'unidad_medida' => $this->tipo_unidad_medida, // 29-8-2024
                    'cantidad' => $this->cantidad_unidad_medida != ''? $this->cantidad_unidad_medida : 1, //	29-8-2024
                    // 6-6-2024
                    'marca_id' => $this->marca_id != ''? $this->marca_id : 1,
                    'es_insumo' => $this->es_insumo,
                    // Actualizacion Precios
                    'porcentaje_regla_precio_interno' => $this->porcentaje_regla_precio_interno != ''? $this->porcentaje_regla_precio_interno/100 : 0 , 
                    'regla_precio_interno' => $this->regla_precio_interno != ''? $this->regla_precio_interno : 0 , 
                    ];
    }
    
    public function HandleGuardar($variaciones, $preciosYPorcentajesListas, $Stocks,$Costos){

        $unidad_medida = unidad_medida::find($this->tipo_unidad_medida);
    
        $relacion_producto_base = $this->SetearUnidadDeMedida($unidad_medida);

        $array_product = $this->ArmarArrayProduct($unidad_medida,$relacion_producto_base);
        //dd($array_product);
        
        if(0 < $this->selected_id){
            $product = Product::find($this->selected_id);
            $product->update($array_product);
        } else {
            $product = Product::create($array_product);
        }
        
        /* ------------ PRECIOS ------------------ */
        $this->QueryCostosYPreciosInternos($product,$variaciones,$Costos);
        
        $this->QueryPrecios($product,$variaciones,$preciosYPorcentajesListas);
        
        $this->QueryStock($product,$variaciones,$Stocks);
        
        $this->QueryIVA($product->id,$product->comercio_id);

        // -------- CARGA DE IMAGENES ---------  //
        
        if($this->imagen_seleccionada) {
        $this->guardarImagenBase64($product,$this->base_64_archivo, $this->base_64_nombre);
        }
        
        // -------- PRODUCTO VARIABLE ---------  //
        if($product->producto_tipo == "v"){
        $this->HandleVariable($product);    
        }
        
        
        // -------- WOCOMMERCE ---------  //
        $this->HandleWC($product);
        
        return $product;  
    }
    
    public function HandleVariable($product){
        $this->QueryCodigoVariable($product);

	    $cart = new CartVariaciones;
        $cart->getContent();
        $pvd = [];

       foreach ($cart->getContent() as $key => $variaciones) {
      	$prod_v_act =	productos_variaciones::where('referencia_id', $variaciones['referencia_id'])->get();
				foreach ($prod_v_act as $pvac) {
						// code...
					$prod_v_actualizar =	productos_variaciones::find($pvac->id);
					$prod_v_actualizar->update([
					 'producto_id' => $product->id,
					 ]);
				}
  }
  }
    
    public function HandleWC($product){
        
        $wc = wocommerce::where('comercio_id',$product->comercio_id)->first();
    
        if($wc != null) {
        if($product->producto_tipo == "s"){
            if(0 < $this->selected_id){
                $this->WocommerceUpdateSimple($product->id);
            } else {
                $this->WocommerceStoreSimple($product->id);
            }            
        } else {
            if(0 < $this->selected_id){
                $this->WocommerceUpdateVariable($product->id);
            } else {
                $this->WocommerceStoreVariable($product->id);
            }            
        }

        }
        
    }

    public function SetDatosGeneralesProducto(){

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		if($this->tipo_producto == 2) {
			$this->cost = 0;
		} else {
		    if($this->cost == "") { $this->cost = 0; } else {
			$this->cost = $this->cost; }
		}


		if($this->alerts == "") {
		$this->alerts = 0;
		}

		if($this->proveedor == "Elegir") {
		$this->proveedor = 1;
		}


		if($this->stock_descubierto == "Elegir") {
		$this->stock_descubierto = "si";
		}

    }    

    public function ValidarCodigosPesables(){
        
        if($this->configuracion_codigos == 0 && $this->tipo_unidad_medida == 1){
           return "Debe configurar la estructura de codigos pesables. Dirijase a la configuracion";
        }
        if($this->tipo_unidad_medida == 1){
           $val = strlen((string)$this->barcode);
           if($val != $this->numeros_codigo){
               return "Los productos pesables deben tener un codigo de ".$this->numeros_codigo." digitos.";
           }
           if(!ctype_digit($this->barcode)){
            return "El codigo en productos pesables debe contener solo numeros.";
           } 
           
        }     
        
        return false;
    }
    
    public function SetearUnidadDeMedida($unidad_medida){
            
    if($unidad_medida != null){
     	$relacion_unidad_base = unidad_medida_relacion::where('tipo_unidad_medida', $unidad_medida->tipo_unidad_medida)->where('unidad_medida',  $unidad_medida->id)->first();
        $relacion_producto_base = 1/$relacion_unidad_base->relacion;                
    } else {
        $relacion_producto_base = 1;    
        }
    
    return $relacion_producto_base;    
    }

    public function QueryStock($product,$variaciones,$Stocks) {

    foreach ($variaciones as $variacion) {

        // Filtrar stocks correspondientes a esta variación
        $stocks = array_filter($Stocks, function ($stock) use ($variacion) {
            return $stock['variacion_id'] == $variacion;
        });
    
       foreach($stocks as $stock){
        
       if($product->producto_tipo == "v"){
         $referencia_variacion = $stock['variacion_id']."-".$this->casa_central_id;       
       } else {
        $referencia_variacion = $stock['variacion_id'];        
       }
       
        $existe_stock = productos_stock_sucursales::where('sucursal_id',$stock['sucursal_id'])
		->where('product_id',$product->id)
		->where('referencia_variacion', $referencia_variacion)
		->first();
        
        $sucursal_pasar = $this->SetSucursalIdOrCero($stock['sucursal_id'],$this->casa_central_id);
        
		if($existe_stock != null) {

		$stock_anterior = $existe_stock->stock_real;
        $stock_real = $stock['stock_real'] ?? 0;
        $stock_disponible = $stock['stock_disponible'] ?? 0;
        
        $this->setUpdateStockDBTrait($sucursal_pasar, $product->id,$existe_stock->referencia_variacion, $stock_disponible,$stock_real,$stock['almacen_id']);

		$cantidad_movimiento = $stock_real -  $stock_anterior;
		
		$this->SetHistoricoStock(5,$product->id,$referencia_variacion,$cantidad_movimiento,$stock_real,$stock['sucursal_id']);

		} else {

        $stock_real = $stock['stock_real'] ?? 0;
        
		$product_create = productos_stock_sucursales::create([
		'almacen_id' => $stock['almacen_id'] ?? 1,
		'stock' => $stock_real,
		'stock_real' => $stock_real,
		'sucursal_id' => $sucursal_pasar,
		'comercio_id' => $this->casa_central_id,
		'referencia_variacion' => $referencia_variacion,
		'product_id' => $product->id,
		]);
		
		
		if($referencia_variacion != 0) {
		$this->StoreUpdateProductosVariacionesDatos($product->id,$referencia_variacion,$product->comercio_id);
		}

    //	$this->cantidad_movimiento = $this->real_stock_sucursal[$key];
        $this->SetHistoricoStock(6,$product->id,$referencia_variacion,$stock_real,$stock_real,$stock['sucursal_id']);

		}
		
		   
       }

		    
    }
    

	
    }

    public function StoreUpdateProductosVariacionesDatos($product_id,$referencia_variacion,$comercio_id){
        $cart = new CartVariaciones;
		
		$resultado = $cart->buscarPorReferencia($referencia_variacion);
        $this->var_nombre = $resultado['var_nombre']; 
        $this->var_id = $resultado['var_id']; 
                                        
		$chequear = productos_variaciones_datos::where('referencia_variacion', $referencia_variacion)->where('product_id',$product_id)->first();

			if($chequear == null) {
				productos_variaciones_datos::create([
				'product_id' => $product_id,
				'referencia_variacion' => $referencia_variacion,
				'variaciones' => $this->var_nombre,
				'variaciones_id' => $this->var_id,
				'comercio_id' => $comercio_id
				]);
			}
    }

    public function QueryPrecios($product,$variaciones,$preciosYPorcentajesListas) {
        
        foreach ($variaciones as $variacion) {
        
        // Filtrar precios y porcentajes correspondientes a esta variación
        $precios_listas = array_filter($preciosYPorcentajesListas, function ($precio) use ($variacion) {
                return $precio['variacion_id'] == $variacion;
        });
        
        if($product->producto_tipo == "v"){
        $referencia_variacion = $variacion."-".$this->casa_central_id;    
        } else {$referencia_variacion = $variacion;}
        
        foreach($precios_listas as $precio_lista) {
        
        if($precio_lista['lista_id'] != 1){
            
        $regla_precios = $this->GetListaPreciosReglaByListaId($precio_lista['lista_id']);   
        $precio_anterior = $this->GetProductoPrecio($this->casa_central_id,$product->id,$referencia_variacion,$precio_lista['lista_id']);
        
        
        $precios = productos_lista_precios::updateOrCreate([
            'eliminado' => 0,
			'comercio_id' => $this->casa_central_id,
			'referencia_variacion' => $referencia_variacion,
			'product_id' => $product->id,
			'lista_id' => $precio_lista['lista_id']
            ],[
			'precio_lista' => $precio_lista['precio'],
			'porcentaje_regla_precio' => $precio_lista['porcentaje']/100,
			'regla_precio' => $regla_precios ? $regla_precios->regla : 1
            ]);

                
        $this->HistoricoActualizacionPrecios($product->id,$referencia_variacion,$precio_lista['lista_id'],$precio_anterior,$precio_lista['precio'],$this->casa_central_id,Auth::user()->id);
            
        }
        $this->StoreUpdateDescuentosProveedor($product->id,$referencia_variacion);   
        }    
        }

        $this->QueryReglaPrecios($product); // Actualizacion de precios  

        $this->IniciarDescuento();
    }

    public function QueryIVA($product_id,$comercio_id) {
        
        $iva_base = $this->porcentaje_iva[$this->casa_central_id] ?? 0;
        productos_ivas::updateOrCreate(
        [   'product_id' => $product_id, 
            'comercio_id' => $comercio_id, 
            'sucursal_id' => $this->casa_central_id
        ],
        ['iva' => $iva_base] // Puedes agregar aquí otros campos y sus valores
        );  
        
        $this->loadSucursales();
        
        foreach($this->sucursales as $sucursal){
        
        $iva = $this->porcentaje_iva[$sucursal->sucursal_id] ?? 0;
        
        productos_ivas::updateOrCreate(
        [   'product_id' => $product_id, 
            'comercio_id' => $comercio_id, 
            'sucursal_id' => $sucursal->sucursal_id
        ],
        ['iva' => $iva] // Puedes agregar aquí otros campos y sus valores
        );            
 
        }

    }
    
    public function loadSucursales(){
               
		$this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
		->select('users.name','sucursales.sucursal_id')
		->where('casa_central_id', $this->casa_central_id)
		->where('sucursales.eliminado',0)
		->get();
    }
    
    public function GetProductoPrecio($casa_central_id,$product_id,$referencia_variacion,$lista_id){
        $precio_anterior = productos_lista_precios::where('comercio_id',$casa_central_id)
        ->where('referencia_variacion',$referencia_variacion)
        ->where('lista_id',$lista_id)
        ->where('eliminado',0)
        ->where('product_id',$product_id)
        ->first(); 
        
        return $precio_anterior ? $precio_anterior->precio_lista : 0;
    }
    
   public function getSubcategorias(){
     $this->subcategorias =   Subcategoria::where('categoria_id',$this->categoryid)->where('eliminado',0)->get();
   }
   
    public function ModalProveedor($value)
	{
	   	if($value == 'AGREGAR') {
    	$this->emit('modal-proveedor-show', '');
    	}
    }    
    /*
    public function SetearStocks(){

    	if($this->tipo_usuario->sucursal != 1) {			

		$sucursales = sucursales::select('sucursales.sucursal_id')
		->where('casa_central_id', $this->comercio_id)
		->where('sucursales.eliminado',0)
		->get();

		} else {			            
		$sucursales = sucursales::select('sucursales.sucursal_id')
		->where('casa_central_id', $this->casa_central_id)
		->where('sucursales.eliminado',0)
		->get();
		}
            
        
        // seteamos la casa central
        $index_cs = "0|0|0|0";
        
        if(isset($this->real_stock_sucursal[$index_cs])){
        if($this->real_stock_sucursal[$index_cs] == ""){$this->real_stock_sucursal[$index_cs] = 0;}
        } else {$this->real_stock_sucursal[$index_cs] = 0;}
        
                
        if(isset($this->almacen_id[$index_cs])){
        if($this->almacen_id[$index_cs] == ""){$this->almacen_id[$index_cs] = 1;}
        } else {$this->almacen_id[$index_cs] = 1;}
        
         if(isset($this->stock_sucursal[$index_cs])){
         if($this->stock_sucursal[$index_cs] == ""){$this->stock_sucursal[$index_cs] = 0;}     
         } else {$this->stock_sucursal[$index_cs] = 0;}
        
        if(isset($this->stock_sucursal_comprometido[$index_cs])){
        if($this->stock_sucursal_comprometido[$index_cs] == "") {$this->stock_sucursal_comprometido[$index_cs] = 0;}    
        } else {
         $this->stock_sucursal_comprometido[$index_cs] = 0;   
        }
        
      //  dd($this->real_stock_sucursal[$index_cs]);
        
     	foreach($sucursales as $llave => $sucu) {

    	if(isset($this->almacen_id["0|".$sucu['sucursal_id']."|0|0"])){		    
    	if($this->almacen_id["0|".$sucu['sucursal_id']."|0|0"] == ""){ $this->almacen_id["0|".$sucu['sucursal_id']."|0|0"] = 1;}
    	} else {$this->almacen_id["0|".$sucu['sucursal_id']."|0|0"] = 1;}
    	
    	if(isset($this->stock_sucursal["0|".$sucu['sucursal_id']."|0|0"])){		    
    	if($this->stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] == ""){ $this->stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] = 0;}
    	} else {$this->stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] = 0;}
    	
    	if(isset($this->real_stock_sucursal["0|".$sucu['sucursal_id']."|0|0"])){
    	if($this->real_stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] == ""){ $this->real_stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] = 0;}
    	} else {$this->real_stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] = 0;}
    	
    	if(isset($this->stock_sucursal_comprometido["0|".$sucu['sucursal_id']."|0|0"])) {
    	if($this->stock_sucursal_comprometido["0|".$sucu['sucursal_id']."|0|0"] == "")  {$this->stock_sucursal_comprometido["0|".$sucu['sucursal_id']."|0|0"] = 0;}
    	} else {$this->stock_sucursal_comprometido["0|".$sucu['sucursal_id']."|0|0"] = 0;}
    	
    	}
        
    }
    */

/*
public function SetearProductosSimplesEnCero(){
        // si el precio de lista esta vacio pedimos que sea elegido

    	if($this->tipo_usuario->sucursal != 1) {			
			
		$sucursales = sucursales::select('sucursales.sucursal_id')
		->where('casa_central_id', $this->comercio_id)
		->where('sucursales.eliminado',0)
		->get();

		} else {			            
		$sucursales = sucursales::select('sucursales.sucursal_id')
		->where('casa_central_id', $this->casa_central_id)
		->where('sucursales.eliminado',0)
		->get();
		}
            
        
        // seteamos la casa central
        $index_cs = "0|0|0|0";

        if(isset($this->almacen_id[$index_cs])){
        if($this->almacen_id[$index_cs] == ""){$this->almacen_id[$index_cs] = 1;}
        } else {$this->almacen_id[$index_cs] = 1;}
        
        if(isset($this->real_stock_sucursal[$index_cs])){
        if($this->real_stock_sucursal[$index_cs] == ""){$this->real_stock_sucursal[$index_cs] = 0;}
        } else {$this->real_stock_sucursal[$index_cs] = 0;}
        
        
         if(isset($this->stock_sucursal[$index_cs])){
         if($this->stock_sucursal[$index_cs] == ""){$this->stock_sucursal[$index_cs] = 0;}     
         } else {$this->stock_sucursal[$index_cs] = 0;}
        
        if(isset($this->stock_sucursal_comprometido[$index_cs])){
        if($this->stock_sucursal_comprometido[$index_cs] == "") {$this->stock_sucursal_comprometido[$index_cs] = 0;}    
        } else {
         $this->stock_sucursal_comprometido[$index_cs] = 0;   
        }
        
      //  dd($this->real_stock_sucursal[$index_cs]);
        
     	foreach($sucursales as $llave => $sucu) {

    	if(isset($this->almacen_id["0|".$sucu['sucursal_id']."|0|0"])){		    
    	if($this->almacen_id["0|".$sucu['sucursal_id']."|0|0"] == ""){ $this->almacen_id["0|".$sucu['sucursal_id']."|0|0"] = 1;}
    	} else {$this->almacen_id["0|".$sucu['sucursal_id']."|0|0"] = 1;}
    	
    	if(isset($this->stock_sucursal["0|".$sucu['sucursal_id']."|0|0"])){		    
    	if($this->stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] == ""){ $this->stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] = 0;}
    	} else {$this->stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] = 0;}
    	
    	if(isset($this->real_stock_sucursal["0|".$sucu['sucursal_id']."|0|0"])){
    	if($this->real_stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] == ""){ $this->real_stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] = 0;}
    	} else {$this->real_stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] = 0;}
    	
    	if(isset($this->stock_sucursal_comprometido["0|".$sucu['sucursal_id']."|0|0"])) {
    	if($this->stock_sucursal_comprometido["0|".$sucu['sucursal_id']."|0|0"] == "")  {$this->stock_sucursal_comprometido["0|".$sucu['sucursal_id']."|0|0"] = 0;}
    	} else {$this->stock_sucursal_comprometido["0|".$sucu['sucursal_id']."|0|0"] = 0;}
    	
    	}
	

} 
*/


/*
public function SetearProductosVariablesEnCero() {
    
        //dd($variacion);
		
		if($this->tipo_usuario->sucursal != 1) {			
			
		$sucursales = sucursales::select('sucursales.sucursal_id')
		->where('casa_central_id', $this->comercio_id)
		->where('sucursales.eliminado',0)
		->get();


		} else {			            
		$sucursales = sucursales::select('sucursales.sucursal_id')
		->where('casa_central_id', $this->casa_central->casa_central_id)
		->where('sucursales.eliminado',0)
		->get();
		}
	
	
    $cart = new CartVariaciones;

    foreach ($cart->getContent() as $key => $variaciones){
    
    $var_nombre = $variaciones['var_nombre'] ?? 0;
    $var_id = $variaciones['var_id'] ?? 0;

    // seteamos la casa central
    $index_cs = $variaciones['referencia_id']."|0|".$var_nombre."|".$var_id;
    if($this->almacen_id[$index_cs] == "") { $this->almacen_id[$index_cs] = 1;}
    if($this->real_stock_sucursal[$index_cs] == "") { $this->real_stock_sucursal[$index_cs] = 0;}
    if($this->stock_sucursal[$index_cs] == "") { $this->stock_sucursal[$index_cs] = 0;}
    if($this->stock_sucursal_comprometido[$index_cs] == "") { $this->stock_sucursal_comprometido[$index_cs] = 0;}
    

    // seteamos las sucursales    
    foreach($sucursales as $s){
    
    $index = $variaciones['referencia_id']."|".$s->sucursal_id."|".$var_nombre."|".$var_id;
    //dd($index);
    //dd($this->almacen_id[$index]);
    if(!isset($this->almacen_id[$index])) {$this->almacen_id[$index] = 1;};
    if($this->real_stock_sucursal[$index] == "") {$this->real_stock_sucursal[$index] = 0;};
    if($this->stock_sucursal[$index] == "") { $this->stock_sucursal[$index] = 0;}
    if($this->stock_sucursal_comprometido[$index] == "") { $this->stock_sucursal_comprometido[$index] = 0;}
    

    }
                	
    }
                	
    
                	
}
*/

    
    /*
    public function GetConfiguracion(){
    $u = User::find($this->casa_central_id);
    $this->configuracion_precio_interno = $u->costo_igual_precio;
    $this->configuracion_codigos = $u->configuracion_codigos;
    }

    
    
    public function AbrirModalConfiguracion(){
    $this->GetConfiguracion();
    
    $this->ver_configuracion = 1;  
    $this->agregar = 0;
    }
    public function CerrarModalConfiguracion(){
    $this->ver_configuracion = 0;  
    $this->agregar = 0;
    }
    
    public function UpdateConfiguracion($id){
    $u = User::find($this->casa_central_id);
    $u->costo_igual_precio = $this->configuracion_precio_interno;
    $u->save();
    
    $products = Product::where('comercio_id',$this->casa_central_id)->where('eliminado',0)->get();
    
    if($this->configuracion_precio_interno == 1){
    foreach($products as $pr){
    // si es simple
    if($pr->producto_tipo == "s"){
    $costo = $pr->cost;
    $pr->precio_interno = $costo;
    $pr->save();
    }
    // si es variable
    if($pr->producto_tipo == "v"){
    $productos_variaciones_datos = productos_variaciones_datos::where('product_id',$pr->id)->where('eliminado',0)->get();
    foreach($productos_variaciones_datos as $pvd){
    $costo = $pvd->cost;
    $pvd->precio_interno = $costo;
    $pvd->save();
    }
    }
    
    }
    }
    
    $this->CerrarModalConfiguracion();
    if($this->configuracion_precio_interno == 1){
    $this->emit("product-added","Configuracion y Precios internos actualizados");    
    } else {
    
    $this->emit("product-added","Configuracion actualizada");        
    }
        
    }
    
    */    
    
    /*        
    public function SetPrecioInternoCostoSimple(){
        
        if($this->configuracion_precio_interno == 1) {
        $this->precio_interno = $this->cost;    
        } 
    }
    
    public function ModificarPrecioInterno($referencia_id) {
        
    if($this->producto_tipo == "s") {
        $this->precio_interno = $this->cost;
    } else {
        $this->precios_internos_variacion[$referencia_id] = $this->costos_variacion[$referencia_id];
    }    
    
        
    }

    public function CambiarPorcentajeReglaPrecioFijoSimple($variacion, $lista_id, $accion)
    {
        $costo = $this->validarValorNumerico($this->costo_despues_descuento);
        $precio_interno = $this->validarValorNumerico($this->precio_interno);
    
        if ($accion == 1) { // precio interno
            $this->porcentaje_regla_precio_interno = $costo != 0 
                ? round((($precio_interno / $costo) - 1) * 100 , 2)
                : 0;
        }
    
        if ($accion == 2) { // precio de venta
            $in = "0|". $lista_id ."|0|0";
            $precio_lista = $this->validarValorNumerico($this->precio_lista[$in] ?? 0);  // Aseguramos que exista el índice
            
            $this->porcentaje_regla_precio[$in] = $costo != 0 
                ? round((($precio_lista / $costo) - 1) * 100 , 2)
                : 0;
        }
    }
    
    public function CambiarPorcentajeReglaPrecioFijoVariable($variacion, $lista_id, $accion)
    {
        $precio_interno = $this->validarValorNumerico($this->precios_internos_variacion[$variacion] ?? 0);
        $costo = $this->validarValorNumerico($this->costos_variacion[$variacion] ?? 0);
    
        if ($accion == 1) { // precio interno
            $this->porcentaje_regla_precio_interno_variacion[$variacion] = $costo != 0 
                ? round( (($precio_interno / $costo) - 1) * 100 , 2)
                : 0;
        }
    
        if ($accion == 2) { // precio de venta
            $in = $variacion . "|" . $lista_id;
            $precio_lista = $this->validarValorNumerico($this->precio_lista[$in] ?? 0);  // Aseguramos que exista el índice
            
            $this->porcentaje_regla_precio[$in] = $costo != 0 
                ? round( (($precio_lista / $costo) - 1) * 100 , 2)
                : 0;
        }
    }

    public function CambiarPorcentajePorCambioPrecio($variacion,$lista_id,$accion){
    
    // Verificar si el costo está definido y no es cero para evitar división por cero
    if (!isset($this->costo_despues_descuento) || $this->costo_despues_descuento == 0) {
       
       
    } else {
    $this->precio_interno = $this->validarValorNumerico($this->precio_interno ?? 0);
    
    //dd($variacion,$lista_id,$accion);
    if($variacion == 0){
        
        if($accion == 1){ // precio interno
          $this->porcentaje_regla_precio_interno = round( (($this->precio_interno / $this->costo_despues_descuento) - 1 ) * 100 , 2);
        }
        if($accion == 2){ // precio de venta
           $in = "0|". $lista_id ."|0|0";
           $this->precio_lista[$in] = $this->validarValorNumerico($this->precio_lista[$in] ?? 0);
           $this->porcentaje_regla_precio["0|".$lista_id] = round((($this->precio_lista[$in] / $this->costo_despues_descuento) - 1 ) * 100 , 2);
        }
        
    }   
  
    if($variacion != 0){
        
        if($accion == 1){ // precio interno
          $this->porcentaje_regla_precio_interno_variacion[$variacion] = round((($this->precios_internos_variacion[$variacion] / $this->costos_variacion[$variacion]) - 1 ) * 100,2);
        }
        if($accion == 2){ // precio de venta
           $in = $variacion."|". $lista_id ;
           $this->porcentaje_regla_precio[$in] = round((($this->precio_lista[$in] / $this->costos_variacion[$variacion]) - 1 ) * 100,2);
        }
        
    }
    
    
    }
    
    }
        
        
        
    public function SwitchReglaPrecios($lista_id){

    if($this->regla_precio_interno == 1){
        $this->porcentaje_regla_precio_interno = 0;
        $this->precio_interno = "";
    }
    if($this->regla_precio[$lista_id] == 1){
        $this->porcentaje_regla_precio[$lista_id] = 0;
        $in = "0|". $lista_id ."|0|0";
        $this->precio_lista[$in] =  "";
    }
    
    
    }
    
        
    public function CambiarCostoPorDescuento(){
    $costo_despues_descuento =  $this->cost * (1 - $this->descuento_costo / 100);    
    $this->costo_despues_descuento = round($costo_despues_descuento,2);
    }
    
    
    public function CambiarCostoReglaPrecio($variacion){
     
    // Cambiar los precios por cambio de costos
        
        $this->CambiarCostoPorDescuento();

       if($this->regla_precio_interno == 2){
        $this->CambiarPorcentajeReglaPrecio($variacion,0,1);
       }
       
       if($this->regla_precio_interno == 1){
        $this->CambiarPorcentajeReglaPrecioFijo($variacion,0,1);
       }
       
       
        if($this->regla_precio[0] == 2){
        $this->CambiarPorcentajeReglaPrecio($variacion,0,2);
        }
        if($this->regla_precio[0] == 1){
        $this->CambiarPorcentajeReglaPrecioFijo($variacion,0,2);
        }
        
        // tomamos las listas de precios
        $lista_de_precios = $this->getListaPrecios($this->casa_central_id);
        
        if(0 < $lista_de_precios->count()){
        foreach($lista_de_precios as $lp){
        
        // si es regla tipo 2
        
        if($this->regla_precio[$lp->id] == 2){
        $this->CambiarPorcentajeReglaPrecio($variacion,$lp->id,2);
        }
        
        // si es regla tipo 1
        if($this->regla_precio[$lp->id] == 1){
        $this->CambiarPorcentajeReglaPrecioFijo($variacion,$lp->id,2);
        }
        
        }               
        }

        
    }
    
    public function CambiarPorcentajeReglaPrecio($variacion,$lista_id,$accion){
    
    //dd($variacion,$lista_id,$accion);
    $this->porcentaje_regla_precio_interno = $this->validarValorNumerico($this->porcentaje_regla_precio_interno ?? 0);
    
    if($variacion == 0){
        if($accion == 1){ // precio interno
          $this->precio_interno = round($this->costo_despues_descuento * (1 + $this->porcentaje_regla_precio_interno/100),2);
        }
        if($accion == 2){ // precio de venta
           $in = "0|". $lista_id ."|0|0";
           $this->porcentaje_regla_precio["0|".$lista_id] = $this->validarValorNumerico($this->porcentaje_regla_precio["0|".$lista_id] ?? 0);
           $this->precio_lista[$in] =  round($this->costo_despues_descuento * (1 + $this->porcentaje_regla_precio["0|".$lista_id]/100),2);
        }        
    }
    
    if($variacion != 0){
        
        $this->costos_variacion[$variacion] = $this->validarValorNumerico($this->costos_variacion[$variacion] ?? 0);
          
        if($accion == 1){ // precio interno
          $this->precios_internos_variacion[$variacion] = round($this->costos_variacion[$variacion] * (1 + $this->porcentaje_regla_precio_interno/100),2);
        }
        if($accion == 2){ // precio de venta
           $in = $variacion."|". $lista_id ;
           $this->porcentaje_regla_precio[$in]  = $this->validarValorNumerico($this->porcentaje_regla_precio[$in] ?? 0);
           $this->precio_lista[$in] =  round($this->costos_variacion[$variacion] * (1 + $this->porcentaje_regla_precio[$in]/100),2);
        }        
      
    }

        
    }
    
    public function CambiarPorcentajeReglaPrecioFijo($variacion, $lista_id, $accion)
    {
        if ($variacion == 0) {
            $this->CambiarPorcentajeReglaPrecioFijoSimple($variacion, $lista_id, $accion);
        } else {
            $this->CambiarPorcentajeReglaPrecioFijoVariable($variacion, $lista_id, $accion);
        }
    }

    */
    
    

    /*    
    public function guardarDatosOld($cost, $descuento, $costoDespuesDescuento, $PorcentajePrecioInterno, $PrecioInterno, $ReglaPrecio,$preciosYPorcentajesListas,$Stocks)
    {
        // Asignar los valores enviados desde JavaScript
        $this->cost = $cost;
        $this->descuento_costo = $descuento;
        $this->costo_despues_descuento = $costoDespuesDescuento;
        
        $this->precio_lista = $preciosYPorcentajesListas;
        $this->real_stock_sucursal = $Stocks;
        
        $this->porcentaje_regla_precio_interno = $PorcentajePrecioInterno;
        $this->precio_interno = $PrecioInterno;
        

        // Guardar en la base de datos
        if(0 < $this->selected_id){
            $this->UpdateProduct();
        } else {
            $this->StoreProduct();
        }
        
        
    }
    */
    
        /*
	public function renderProductsList($productSection)
	{
			$this->products = Product::join('categories as c','c.id','products.category_id')
			    ->join('marcas','marcas.id','products.marca_id')
				->join('seccionalmacens as a','a.id','products.seccionalmacen_id')
				->join('proveedores as pr','pr.id','products.proveedor_id')
				->select('products.*','c.name as category','a.nombre as almacen','pr.nombre as nombre_proveedor')
				->where('products.comercio_id', 'like', $this->casa_central_id)
				->where('products.eliminado', 'like', $this->estado_filtro);

			//Filtrar productos por categoria, almacen o provedor
			//$this->filtrarProductos();
			$this->products = $this->RenderFiltrar($this->products,$this->id_categoria,$this->id_almacen,$this->proveedor_elegido,$this->etiquetas_filtro,$this->id_marca,$this->es_insumo_elegido);
			
			//Buscador productos
			$this->searchProducto();

			$this->products = $this->products->orderBy($this->sortColumn, $this->sortDirection);
			$this->products = $this->products->paginate($this->pagination);	

			//Determina precios  y stocks
			$this->setPreciosYstock($productSection);
			
			//Cart
			$cart = new CartVariaciones;
			$this->cart = $cart->getContent();
			
			// Etiquetas
			
	}
	*/


    /*
	public function setPreciosYstock($productSection){

		$item_ids = $this->products->items();			
		$items_id = [];
		
		foreach ($item_ids as $i_id) {
			$id_id = $i_id->id;
			array_push($items_id, $id_id);		    
		} 

		if($this->tipo_usuario->sucursal != 1) {			
			
				$this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
				->select('users.name','sucursales.sucursal_id')
				->where('casa_central_id', $this->comercio_id)
				->where('sucursales.eliminado',0)
				->get();

				if($productSection === 'Products'){
					$this->productos_lista_precios = productos_lista_precios::where('comercio_id', $this->casa_central_id)->whereIn('product_id',$items_id)->get();
					$this->stock_sucursales = productos_stock_sucursales::whereIn('product_id',$items_id)->get();
				}
				if($productSection === 'ProductsPrecio'){
					$this->stock_sucursales = productos_stock_sucursales::whereIn('product_id',$items_id)->where('comercio_id', $this->sucursal_id)->get();
				}
				if($productSection === 'ProductsStock'){
					$this->stock_sucursales = productos_stock_sucursales::whereIn('product_id',$items_id)
					->where('productos_stock_sucursales.eliminado',0)
					->select('productos_stock_sucursales.product_id','productos_stock_sucursales.sucursal_id', productos_stock_sucursales::raw('SUM(productos_stock_sucursales.stock) AS stock'),productos_stock_sucursales::raw('SUM(productos_stock_sucursales.stock_real) AS stock_real'))
					->groupBy('productos_stock_sucursales.product_id','productos_stock_sucursales.sucursal_id')
					->get();
					$this->lista_precios = lista_precios::where('comercio_id',$this->sucursal_id)->get();
				}

		} else {			            
				$this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
				->select('users.name','sucursales.sucursal_id')
				->where('casa_central_id', $this->casa_central->casa_central_id)
				->where('sucursales.eliminado',0)
				->get();

				if($productSection === 'Products'){
					$this->stock_sucursales = productos_stock_sucursales::where('comercio_id', $this->casa_central_id)->whereIn('product_id',$items_id)->get();
					$this->productos_lista_precios = productos_lista_precios::where('comercio_id', $this->comercio_id)->whereIn('product_id',$items_id)->get();
				}
				if($productSection === 'ProductsPrecio'){
					$this->stock_sucursales = productos_stock_sucursales::where('comercio_id', $this->casa_central_id)
					->whereIn('product_id',$items_id)->get();
				}
				if($productSection === 'ProductsStock'){					
					$this->stock_sucursales = productos_stock_sucursales::where('comercio_id', $this->casa_central_id)->whereIn('product_id',$items_id)
					->where('productos_stock_sucursales.eliminado',0)
					->select('productos_stock_sucursales.product_id','productos_stock_sucursales.sucursal_id', productos_stock_sucursales::raw('SUM(productos_stock_sucursales.stock) AS stock'),productos_stock_sucursales::raw('SUM(productos_stock_sucursales.stock_real) AS stock_real'))
					->groupBy('productos_stock_sucursales.product_id','productos_stock_sucursales.sucursal_id')
					->get();

					$this->lista_precios = lista_precios::where('comercio_id',$this->casa_central_id)->get();
				}
			}
			if($productSection === 'ProductsPrecio'){
				$this->productos_lista_precios = productos_lista_precios::whereIn('product_id',$items_id)->get();
			}
	}	
    */
    
            /*
        private function obtenerCostosYPrecioInterno($product)
        {
            $array_costos = [];
            // Devolver el array con el index 0
            if($product->producto_tipo == "s"){
 
            // Calcular el costo y el descuento
            $cost = $product->cost;
            $datos_descuento = $this->calcularCostoDespuesDeDescuento($product->id,0, $cost);
            $porcentaje_regla_precio_interno = $this->porcentaje_regla_precio_interno;
            $precio_interno = $this->precio_interno;
            
            $this->cost = $cost;
            $this->precio_interno = $precio_interno;
            
            // Obtener los valores finales
            $costo_despues_descuento = $datos_descuento['costo_final'];
            $descuento_costo = $datos_descuento['descuento_final'];

            $key = 0;
            $array_costos = $this->ArmarArrayDeCostosYPrecioInterno($array_costos,$key,0,$cost,$descuento_costo,$costo_despues_descuento,$porcentaje_regla_precio_interno,$precio_interno);
            }
            
            if($product->producto_tipo == "v"){
                            
            // si es variacion tiene que obtener los datos de aca 
            
            foreach($this->datos_variaciones as $key => $d) {
            // Calcular el costo y el descuento
            $cost = $d['cost'];
            $variacion_id = $this->GetVariacionId($d['referencia_variacion']);
            $datos_descuento = $this->calcularCostoDespuesDeDescuento($product->id,$d['referencia_variacion'], $cost);
            //dd($datos_descuento);
            
            // Obtener los valores finales
            $costo_despues_descuento = $datos_descuento['costo_final'];
            $descuento_costo = $datos_descuento['descuento_final'];
            
            $porcentaje_regla_precio_interno = $d['porcentaje_regla_precio_interno'];
            $precio_interno = $d['precio_interno'];
            

            $porcentaje_regla_precio_interno = $d['porcentaje_regla_precio_interno'] * 100;
            $array_costos = $this->ArmarArrayDeCostosYPrecioInterno($array_costos,$key,$variacion_id,$cost,$descuento_costo,$costo_despues_descuento,$porcentaje_regla_precio_interno,$precio_interno);
    		}
    		
            }
            return  $array_costos; 

        }
        
        private function ArmarArrayDeCostosYPrecioInterno($array_costos,$key,$variacion_id,$costo,$descuento_costo,$costo_despues_descuento,$porcentaje_regla_precio_interno,$precio_interno){
             $array_costos[$key] = [
                    'variacion_id' => $variacion_id, // Si no tienes variaciones, deja esto en 0 o ajusta según sea necesario
                    'cost' => $costo,
                    'descuento_costo' => $descuento_costo,
                    'costo_despues_descuento' => $costo_despues_descuento,
                    'porcentaje_regla_precio_interno' => $porcentaje_regla_precio_interno,
                    'precio_interno' => $precio_interno
                ];   
                
                return $array_costos;
        }
        */
        


    /*
    public function QueryProductoSimple($product) {
    
    //$this->SetearProductosSimplesEnCero();
    $this->QueryStock($product);
    $this->QueryPrecios($product);

    }
    
    
    public function QueryProductoVariable($product) {

    $this->UpdateDatosVariables($product);
	//Cod Entorno testing:
	
	$this->QueryStock($product);
	$this->QueryPrecios($product);
	

    }
   //--------- FUNCION QUE ACTUALIZA DATOS DE LAS VARIACIONES --------- //
   
   
        
    public function UpdateDatosVariables($product) {
    
    
    // aca esta el error 
    
    $cart = new CartVariaciones;
            
    $cart->getContent();
    
    $cart = $cart->getContent();
    $pvd = [];
    
    //dd($cart);
    $array_actualizado = [];
    
    // testeado el problema no esta aca
    
    foreach ($cart as $key => $variaciones) {


	//Cod entorno testing:
	$productosVariaciones = productos_variaciones::where('referencia_id', $variaciones['referencia_id'])->first();			
		
	if ($productosVariaciones === null && isset($variaciones["product_variacion_create_db"])) {
		//return dd($variaciones["product_variacion_create_db"]);
		productos_variaciones::create([
			'atributo_id' =>  $variaciones["product_variacion_create_db"]['atributo_id'],
			'variacion_id' => $variaciones["product_variacion_create_db"]['variacion_id'],
			'comercio_id' =>  $variaciones["product_variacion_create_db"]['comercio_id'],
			'referencia_id' => $variaciones["product_variacion_create_db"]['referencia_id']
		]);
	}

    $prod_v_act =	productos_variaciones_datos::where('referencia_variacion', $variaciones['referencia_id'])->where('eliminado',0)->orderBy('created_at','desc')->first();

    if($prod_v_act == null) {
                
    $prod_v_act = productos_variaciones_datos::create([
		'product_id' => $product->id,
	    'referencia_variacion' => $variaciones['referencia_id'],
		'variaciones' => $variaciones['var_nombre'],
		'variaciones_id' => $variaciones['var_id'],
		'comercio_id' => $this->sucursal_id
	]);
                
    } else {
    $prod_v_act->update([
		'variaciones' => $variaciones['var_nombre'],
		'variaciones_id' => $variaciones['var_id'],
	]);
    }
        
            
    // NUEVO //
            
    $prod_variaciones_act =	productos_variaciones::where('referencia_id', $variaciones['referencia_id'])->get();
            
    foreach($prod_variaciones_act as $pvariacionesact) {
                
    $pvariacionesact->producto_id = $product->id;
    $pvariacionesact->save();
                
    }

	//Cod entrono testing:
	//$productosVariaciones = null;
            
    /////////////
    
    array_push($array_actualizado, $prod_v_act->id);

    }    
    
    // break;
    

    /////////////////// ACTUALIZACION DE CODIGO /////////////////////////////
    
   $this->QueryCodigoVariable($product);
    
    /////////////////// ACTUALIZACION DE COSTOS /////////////////////////////

    $this->QueryCosto($product);

    /////////////////// ACTUALIZACION DE PRECIO INTERNO /////////////////////////////
    if(0 < $this->sucursales->count()) {
    $this->QueryPrecioInternoVariable($product);
    }
    
	/////////////////// ACTUALIZACION DE PRECIOS --- ACA ESTA EL ERROR /////////////////////////////
	
	$this->QueryStock($product);
	
	 /////////////////// ACTUALIZACION DE STOCK /////////////////////////////
	 
	$this->QueryPrecios($product);
	
	// dd($array_actualizado);

    }
   */    
}