<?php
namespace App\Traits;

// Trait

use App\Traits\WocommerceTrait;
use App\Traits\ConfiguracionProductsTrait;
use App\Traits\CartTrait;
use App\Traits\EtiquetasTrait;

// Modelos


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


trait ProductsTrait {


	//Codigo entorno testing:
	use CommonTrait;
	use EtiquetasTrait;
	use ConfiguracionProductsTrait;

    public $list;
    private $wc_category;
    public $precios_internos_variacion;
    public $configuracion_precio_interno;
    
    public $provincias = [];
    public $paises = [];
    public $pais_proveedor;
    public $altura_proveedor, $piso_proveedor, $search_etiqueta,$codigo_postal_proveedor, $depto_proveedor, $id_proveedor;
    
    public $mostrarDiv = false;
    
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

	//Codigo entorno testing:
	protected $erroresCodigoVariaciones;

    // 28-5-2024 ---> Modificacion de codigos
    
    public $numeros_prefijo,$numeros_codigo,$numeros_peso, $tipo_unidad_medida, $cantidad_unidad_medida;
    
    ///////////////// FUNCIONES QUE SEGURO SE REPITEN EN EL CART TRAIT (VER Y ADAPTAR A LO QUE HAY EN EL CART TRAIT) ///////////////////////
    
    public function GetDatosGeneralesProduct() {
    
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
    
    session(['casa_central_id' => $this->casa_central_id]);
    session(['sucursal_id' => $this->sucursal_id]);
    session(['comercio_id' => $this->comercio_id]);
    
    $this->provincias = provincias::all();
	$this->paises = paises::all();

    //	28-5-2024
    $this->tipo_unidad_medida = 9;
    $this->cantidad_unidad_medida = 1;
    
    }
    
    public function GetDatosGeneralesSession() {
        
    $this->casa_central_id = session('casa_central_id');    
    $this->sucursal_id = session('sucursal_id');
    $this->comercio_id = session('comercio_id');
    //$this->configuracion_precio_interno = $this->GetConfiguracion();
 
 	
	// 28-5-2024
	$this->unidades_de_medida = tipo_unidad_medida::join('unidad_medidas','unidad_medidas.tipo_unidad_medida','tipo_unidad_medidas.id')
	->select('unidad_medidas.*','tipo_unidad_medidas.nombre as nombre_tipo_unidad_medida')
	->get();
	   
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
    
    	$this->lista_precios = lista_precios::where('comercio_id',$this->sucursal_id)->get();
    
    	} else {
    		    
    	$this->casa_central = sucursales::where('sucursal_id', $this->comercio_id)->first();
    	$this->casa_central_id = $this->casa_central->casa_central_id;
    
    	$this->lista_precios = lista_precios::where('comercio_id',$this->casa_central_id)->get();
    
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
    
    $this->productos_variaciones = productos_variaciones::join('variaciones','variaciones.id','productos_variaciones.variacion_id')
    ->select('variaciones.nombre as nombre_variacion','productos_variaciones.*')
    ->where('variaciones.comercio_id', $this->comercio_id)->get();
    			
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
    
    public function SwicthProducts($selected_id) {
       // dd($selected_id);
        if(0 < $selected_id) {
            $this->UpdateProduct();
        } else {
            $this->StoreProduct();
        }
    }
    
	public function StoreProduct()
	{

        //dd($this->tipo_unidad_medida);

		//Cod entorno testing
		$comercio_id = $this->setComercioId();

		$this->tipo_producto = 1;
        
		$rules  =[
			'name' => 'required',
			'barcode' => ['required',Rule::unique('products')->where('comercio_id',$comercio_id)->where('eliminado',0)],
			'alerts' => 'required',
			'categoryid' => 'required|not_in:Elegir',
			'tipo_producto' => 'required|not_in:Elegir',
			'producto_tipo' => 'required|not_in:Elegir'

		];

		$messages = [
			'name.required' => 'Nombre del producto requerido',
			'name.unique' => 'El nombre del producto ya esta en uso',
			'name.required' => 'El codigo del producto requerido',
			'barcode.required' => 'El codigo del producto es requerido',
			'barcode.unique' => 'El codigo del producto ya esta en uso',
			'barcode.min' => 'El codigo debe tener al menos 3 caracteres',
			'alerts.required' => 'Falta el valor para las alertas',
			'categoryid.not_in' => 'Elige una categoría válida',
			'tipo_producto.not_in' => 'Elige el tipo de producto',
			'producto_tipo.not_in' => 'Elija el tipo de producto.',
			'producto_tipo.required' => 'Elija el tipo de producto.'
		];

		$this->validate($rules, $messages);
        
        //28-5-2024
        $this->GetConfiguracion();
        
        if($this->configuracion_codigos == 0 && $this->tipo_unidad_medida == 1){
               $this->emit("msg-error","Debe configurar la estructura de codigos pesables. Dirijase a la configuracion");
               return;            
        }
        if($this->tipo_unidad_medida == 1){
           $val = strlen((string)$this->barcode);
           if($val != $this->numeros_codigo){
               $this->emit("msg-error","Los productos pesables deben tener un codigo de ".$this->numeros_codigo." digitos.");
               return;
           }
           if(!ctype_digit($this->barcode)){
            $this->emit("msg-error","El codigo en productos pesables debe contener solo numeros.");
           return;
           } 
           
        }

        //
        
        
        //////////////////////// DATOS GENERALES ////////////////////////////////

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

        //dd($this->producto_tipo);
		// ------------ PRODUCTOS VARIABLES -------------- //

        if($this->producto_tipo == "v") {

        // Ver si hay codigos variables vacios
        $vacios = $this->DetectarCodigosVariablesVacios();
        if($vacios == true) {
        $this->emit("msg-error","existen codigos de variaciones vacios");
        return;
        }
            
        // Ver si hay codigos variables repetidos
        
        $repetidos = $this->DetectarCodigosVariablesRepetidos();
        if($repetidos == true) {
        $this->emit("msg-error","Existen codigos de variaciones repetidos");
        return;
        }

        if(0 < $this->sucursales->count()) {
        // Ver si hay precios internos vacios
        $vacios_pi = $this->DetectarPreciosInternosVacios();
        if($vacios_pi == true) {
        $this->emit("msg-error","Existen precios internos vacios");
        return;
        }
        } else {$vacios_pi = false;}


                
            
        if($vacios == false && $vacios_pi == false && $repetidos == false) {
            
 
	    $product = Product::create([
		'name' => $this->name,
		'cost' => 0,
		'price' => 0,
		'barcode' => $this->barcode,
		'stock' => 0,
		'alerts' => $this->alerts,
		'tipo_producto' => $this->tipo_producto,
		'stock_descubierto' => $this->stock_descubierto,
		'seccionalmacen_id' => 1,
		'category_id' => $this->categoryid,
		'proveedor_id' => $this->proveedor != ''? $this->proveedor : 1 ,
		'comercio_id' => $comercio_id,
		'iva' => $this->iva,
		'relacion_precio_iva' => $this->relacion_precio_iva,
		'cod_proveedor' => $this->cod_proveedor,
		'mostrador_canal' => $this->mostrador_canal,
		'ecommerce_canal' => $this->ecommerce_canal,
		'wc_canal' => $this->wc_canal,
		'descripcion' => $this->descripcion,
		'producto_tipo' => $this->producto_tipo,
	    //	28-5-2024
        'unidad_medida' => $this->tipo_unidad_medida,
        'cantidad_unidad_medida' => $this->cantidad_unidad_medida
    
	]);

    dd($product);

    $this->SetearProductosVariablesEnCero();
    
    ////////////////  GUARDAR PRECIOS VARIABLES //////////////////////////////////

    $this->QueryPrecios($product);


    //////////////////// GUARDAR STOCK VARIABLES //////////////////////////////

    $this->QueryStock($product);

    /////////////////// COSTOS ////////////////////////////////

    if($this->tipo_producto != 2) {
    $this->QueryCostoVariable($product);
    }
	
	if(0 < $this->sucursales->count()) {
    $this->QueryPrecioInternoVariable($product);
	}
    
    /////////////////// ACTUALIZACION DE CODIGO /////////////////////////////

    $this->QueryCodigoVariable($product);
				
    //////////////////////////////////////////////////////////////////////

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
      
      
				/////////////////////////////////////////////////////////////////////////////////////////////////
				//////////////////////////// PRODUCTO VARIABLE CON WOCOMMERCE  //////////////////////////////////
				/////////////////////////////////////////////////////////////////////////////////////////////////
				///////////////////////////////////////////////////////////////////////////////////////////////

            
            $wc = wocommerce::where('comercio_id',$comercio_id)->first();
    
            if($wc != null) {
			 if($this->wc_canal == true) {
                
				$this->WocommerceStoreVariable($product->id);

				}
				
            }

        }  
}

        // --------------PRODUCTO SIMPLE ------------ //

        if($this->producto_tipo == "s") {
        
            
        // si el precio de lista esta vacio pedimos que sea elegido
        
        if($this->precio_lista != null){
        
        $ind = "0|0|0|0";
        
        // comprobar si existe
        if(isset($this->precio_lista[$ind])){
        if($this->precio_lista[$ind] == "") {
        $this->emit('msg-error','Debe elegir el precio de venta');
        return;
        } 
        }

        } else {
        $this->emit('msg-error','Debe elegir el precio de venta');
        return;
        }
    
        // seteamos precio interno
        
        if(0 < $this->sucursales->count()) {
        if(empty($this->precio_interno) && ($this->precio_interno != 0) && ($this->precio_interno == "") ) {
        $this->emit('msg-error','Debe elegir el precio interno');
        return;    
        }
        } else {
        if($this->precio_interno == "") {$this->precio_interno = 0;}    
        }
        
        
        // seteamos el costo
        if($this->cost == "") {$this->cost = 0;}   
        
        $this->SetPrecioInternoCostoSimple();
        
        // tomamos las listas de precios
        $lista_de_precios = $this->getListaPrecios($this->casa_central_id);
        
        // seteamos el precio 
        
        if(0 < $lista_de_precios->count()){
        foreach($lista_de_precios as $lp){
        $in = "0|".$lp->id."|0|0";
        if(isset($this->precio_lista[$in])){
        if($this->precio_lista[$in] == ""){ $this->precio_lista[$in] = 0; }    
        } else {$this->precio_lista[$in] = 0;}
        }            
        }
        
        
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
	         //     dd($this->precio_interno);
             //   dd($this->producto_tipo);
                
        		$product = Product::create([
        			'name' => $this->name,
        			'cost' => $this->cost,
        			'price' => 0,
        			'barcode' => $this->barcode,
        			'stock' => 0,
        			'alerts' => $this->alerts,
        			'tipo_producto' => $this->tipo_producto,
        			'stock_descubierto' => $this->stock_descubierto,
        			'seccionalmacen_id' => 1,
        			'category_id' => $this->categoryid,
        			'proveedor_id' => $this->proveedor != ''? $this->proveedor : 1,
        			'precio_interno' => $this->precio_interno,
        			'comercio_id' => $comercio_id,
        			'iva' => $this->iva,
        			'relacion_precio_iva' => $this->relacion_precio_iva,
        			'cod_proveedor' => $this->cod_proveedor,
        			'mostrador_canal' => $this->mostrador_canal,
        			'ecommerce_canal' => $this->ecommerce_canal,
        			'wc_canal' => $this->wc_canal,
        			'descripcion' => $this->descripcion,
        			'producto_tipo' => $this->producto_tipo,
        		    //	28-5-2024
                    'unidad_medida' => $this->tipo_unidad_medida,
                    'cantidad_unidad_medida' => $this->cantidad_unidad_medida
        		]);
        
                /* ------------ PRECIOS ------------------ */
        
                $this->QueryPrecios($product);
        
        
        		/* ------------ SI TIENE SUCURSALES -- CREA LOS PRODUCTOS EN TODAS LAS SUCURSALES ------------------ */

               // dd($this->stock_sucursal);
                
        		if($this->stock_sucursal != null) {
        			 
                $this->QueryStock($product);
        			 
        			    
        		}
        // -------- CARGA DE IMAGENES ---------  //
        
        
        
        if($this->imagen_seleccionada) {
        $this->guardarImagenBase64($product,$this->base_64_archivo, $this->base_64_nombre);
        //$this->GuardarImagenGaleria($product->id);
        }
        
        ////////////////////////////////// PRODUCTO SIMPLE CON WOCOMMERCE /////////////////////////////////////////////////////////////////////
                $wc = wocommerce::where('comercio_id',$comercio_id)->first();
    
                if($wc != null) {
               if($this->wc_canal == true) {
                  $this->WocommerceStoreSimple($product->id);
               }
               
                }
            
            
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

		$this->emit('product-added', 'Producto Registrado');

        return $product_responde;


	}


    
    
	public function UpdateProduct()
	{

		//Cod entorno testing:
		$comercio_id = $this->setComercioId();

		//Cod entorno testing:
		$this->tipo_producto = (Auth::user()->plan != 4) ? 1 : $this->tipo_producto;
		
		
		$rules  =[
		    'name' => 'required',
			'barcode' => ['required',Rule::unique('products')->ignore($this->selected_id)->where('comercio_id',$comercio_id)->where('eliminado',0)],
			'alerts' => 'required',
			'proveedor' => 'required',
			'categoryid' => 'required|not_in:Elegir',
			'stock_descubierto' => 'required|not_in:Elegir',
			'tipo_producto' => 'required|not_in:Elegir'
		];

		$messages = [
			'name.required' => 'Nombre del producto requerido',
			'name.unique' => 'El nombre del producto ya existe, elija otro',
			'barcode.required' => 'El codigo es requerido',
			'proveedor.required' => 'El proveedor es requerido',
			'alerts.required' => 'Ingresa el valor mínimo en existencias',
			'categoryid.not_in' => 'Elige un nombre de categoría diferente de Elegir',
			'tipo_producto.not_in' => 'Elige el tipo de producto',
		'stock_descubierto.not_in' => 'Elige si maneja o no stock',
		];


		$this->validate($rules, $messages);

        
        //28-5-2024
        if($this->configuracion_codigos == 0 && $this->tipo_unidad_medida == 1){
               $this->emit("msg-error","Debe configurar la estructura de codigos pesables. Dirijase a la configuracion");
               return;            
        }
        
        if($this->tipo_unidad_medida == 1){
           $val = strlen((string)$this->barcode);
           if($val != $this->numeros_codigo){
               $this->emit("msg-error","Los productos pesables deben tener un codigo de ".$this->numeros_codigo." digitos.");
               return;
           }
           if(!ctype_digit($this->barcode)){
            $this->emit("msg-error","El codigo en productos pesables debe contener solo numeros.");
           return;
           } 
           
        }
        //
        
        
        $product = Product::find($this->selected_id);

		$usuario_id = Auth::user()->id;

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

        if($this->producto_tipo == "s") { 
        // seteamos precio interno
        
        if(0 < $this->sucursales->count()) {
        if($this->precio_interno == "") {
        $this->emit('msg-error','Debe elegir el precio interno');
        return;    
        }
        } else {
        if($this->precio_interno == "") {$this->precio_interno = 0;}    
        }
        
        // seteamos el costo
        if(isset($this->cost)){
        if($this->cost == "") {$this->cost = 0;}   
        } else {$this->cost = 0;}
        
        $this->SetPrecioInternoCostoSimple();
        }
    

        // Actualiza la tabla products 
        
		$product->update([
			'name' => $this->name,
			'cost' => $this->cost,
			'barcode' => $this->barcode,
			'tipo_producto' => $this->tipo_producto,
			'alerts' => $this->alerts,
			'proveedor_id' => $this->proveedor,
			'stock_descubierto' => $this->stock_descubierto,
			'category_id' => $this->categoryid,
			'iva' => $this->iva,
			'precio_interno' => $this->precio_interno,
			'relacion_precio_iva' => $this->relacion_precio_iva,
			'cod_proveedor' => $this->cod_proveedor,
			'mostrador_canal' => $this->mostrador_canal,
			'ecommerce_canal' => $this->ecommerce_canal,
			'wc_canal' => $this->wc_canal,
			'descripcion' => $this->descripcion,
			'producto_tipo' => $this->producto_tipo,
			'updated_at' => Carbon::now(),
		    //	28-5-2024
            'unidad_medida' => $this->tipo_unidad_medida,
            'cantidad_unidad_medida' => $this->cantidad_unidad_medida

		]);
		
		$this->QueryIVA($product->id,$product->comercio_id);
		
//Instalar noty en entorno de testing

    if($this->producto_tipo == "v") {            
	
	// Ver si hay codigos variables vacios
	$vacios = $this->DetectarCodigosVariablesVacios();
	if($vacios == true) {
	$this->emit("mensajes","existen codigos de variaciones vacios");
	return;
    }
    
    // Ver si hay codigos variables repetidos
    
    $repetidos = $this->DetectarCodigosVariablesRepetidos();
    if($repetidos == true) {
    $this->emit("mensajes","existen codigos de variaciones repetidos");
    return;
    }
    
    if(0 < $this->sucursales->count()) {
    // Ver si hay precios internos vacios
    $vacios_pi = $this->DetectarPreciosInternosVacios();
    if($vacios_pi == true) {
    $this->emit("msg-error","Existen precios internos vacios");
    return;
    }
    } else {$vacios_pi = false;}
    
    // Ver si hay precios internos vacios
    $vacios_pi = $this->DetectarPreciosBaseVacios();
    if($vacios_pi == true) {
    $this->emit("msg-error","Existen precios base vacios");
    return;
    }

    $this->SetearProductosVariablesEnCero();
    
    if($vacios == false && $vacios_pi == false  && $repetidos == false) {
    $this->QueryProductoVariable($product);    
    }
    } else {
    $this->QueryProductoSimple($product);
    }
    
    ////////////////////////////////////////        SI TIENE UNA IMAGEN LA CARGA      ///////////////////////////////////////////////////////////////
        
       if($this->imagen_seleccionada) {
           //$this->GuardarImagenGaleria($product->id);
           $this->guardarImagenBase64($product,$this->base_64_archivo, $this->base_64_nombre);
       }
							
	//////////////////////////////////////////////////////////////////////////////
							
    ////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////
    ////////////////////////// WOCOMMERCE //////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////
    
    $wc = wocommerce::where('comercio_id',$comercio_id)->first();
    
    if($wc != null) {
        
    if($this->wc_canal == true) {
        
    if($this->producto_tipo == "v") {
    $this->WocommerceUpdateVariable($product->id);
    } else {
    $this->WocommerceUpdateSimple($product->id);
    }
        
        
    }
    
    }
    



       	$this->StoreUpdateEtiquetas($product->id,2,"productos",$product->comercio_id);
       	


		$this->ResetAgregar();
		$this->emit('product-updated', 'Producto Actualizado');


	}
	


	public function EditProduct(Product $product, $forma)
	{
	    
	    $this->tipo_usuario = User::find($this->comercio_id);
		    
	    if($this->tipo_usuario->sucursal == 1) {
	    $this->es_sucursal = 1;
	    }
	//   dd($product);
	    
	    $this->agregar = 1;
	 
		//Cod de entorno testing:
		$this->resetUI();


        $this->emit('modal-show','Show modal');
		
		if($product->tipo_producto == 2) {

			$this->receta = Product::leftjoin('recetas as r','r.product_id','products.id')
									->leftjoin('insumos','insumos.id','r.insumo_id')
									->join('unidad_medidas','unidad_medidas.id','r.unidad_medida')
									->select(receta::raw(' SUM(r.cantidad*r.costo_unitario*r.relacion_medida) AS cost'))
									->where('products.id', 'like', $product->id)
									->where('products.eliminado', 'like', 0)
									->first();

			if($this->receta != null) {
				$this->cost = floatval($this->receta->cost);
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
		
		$this->GetDatosFacturacionDefectoProducto(2);
		
		//dd($this->wc_product_id);
		$this->stock = $product->stock;
		$this->alerts = $product->alerts;
		$this->inv_ideal = $product->inv_ideal;
		$this->precio_interno = $product->precio_interno;
		$this->proveedor = $product->proveedor_id;
		$this->stock_descubierto = $product->stock_descubierto;
		$this->categoryid = $product->category_id;
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
		//	28-5-2024
        $this->tipo_unidad_medida = $product->unidad_medida;
        $this->cantidad_unidad_medida = $product->cantidad_unidad_medida;


		if($this->image != null) { 
		$imagen = imagenes::where('url',$this->image)->where('comercio_id',$this->casa_central_id)->first();
		$this->base_64_archivo = $imagen->base64;
		} else {
		$this->base64 = null; 
		}

		//////////////////////////     SI EL PRODUCTO ES SIMPLE          ////////////////////////////////////////
		if($this->producto_tipo == "s") {

			$this->stock_productos_sucursales = productos_stock_sucursales::select('sucursal_id','stock','stock_real','almacen_id')
			->where('product_id', $this->selected_id)
			->get();
			
            //dd($this->stock_productos_sucursales);
            
			foreach($this->stock_productos_sucursales as $llave => $sucu) {
			    
				$this->almacen_id["0|".$sucu['sucursal_id']."|0|0"] = $sucu['almacen_id'];
				$this->stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] = $sucu['stock'];
				$this->real_stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] = $sucu['stock_real'];
				$this->stock_sucursal_comprometido["0|".$sucu['sucursal_id']."|0|0"] = $this->real_stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] - $this->stock_sucursal["0|".$sucu['sucursal_id']."|0|0"] ; 
			}

			if($this->lista_precios != null) {

				$this->productos_lista_precios = productos_lista_precios::select('lista_id','precio_lista')
															->where('product_id', $this->selected_id)
															->where('eliminado', 0)
															->get();
				
				foreach ($this->productos_lista_precios as $key => $lp) {
					$this->precio_lista["0|".$lp['lista_id']."|0|0"] = $lp['precio_lista'];
				}

			}
		}

		if($this->producto_tipo == "v") {

            $this->ResetVariablesVariaciones();

			//////////////////////////     SI EL PRODUCTO ES VARIABLE          ////////////////////////////////////////
			$cart = new CartVariaciones;

			$cart->clear();

			$this->productos_variaciones = productos_variaciones_datos::where('product_id', $this->selected_id)
			->select('id','referencia_variacion','variaciones','variaciones_id')
			->groupBy('id','referencia_variacion','variaciones','variaciones_id')
			->where('productos_variaciones_datos.eliminado',0)
			->orderBy('created_at','desc')
			->get();

			/*Cod entorno testing:
			/*
			$this->productos_variaciones = productos_variaciones_datos::where('product_id', $this->selected_id)
			->select('referencia_variacion','variaciones','variaciones_id')
			->groupBy('referencia_variacion','variaciones','variaciones_id')
			->where('eliminado',0)
			->orderBy('created_at','desc')
			->get();
			*/

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


			/*Cod entorno testing:
			/*
			foreach ($this->productos_variaciones as $prod_var) {
				// code...
				$product = array(
				'referencia_id' => $prod_var->referencia_variacion,
				'var_nombre'=> $prod_var->variaciones,
				'var_id'=> $prod_var->variaciones_id,
				);

				$cart->addProduct($product);
			}*/

			$this->stock_productos_sucursales = productos_stock_sucursales::join('productos_variaciones_datos','productos_variaciones_datos.referencia_variacion','productos_stock_sucursales.referencia_variacion')
											->where('productos_stock_sucursales.product_id', $this->selected_id)->get();
											
            
											
            // Setea el stock de las variaciones 
            
			foreach($this->stock_productos_sucursales as $llave => $sucu) {
			    
				$this->almacen_id[$sucu['referencia_variacion']."|".$sucu['sucursal_id']."|".$sucu['variaciones']."|".$sucu['variaciones_id']] = $sucu['almacen_id'];
			    
				$this->real_stock_sucursal[$sucu['referencia_variacion']."|".$sucu['sucursal_id']."|".$sucu['variaciones']."|".$sucu['variaciones_id']] = $sucu['stock_real'];
				$this->stock_sucursal[$sucu['referencia_variacion']."|".$sucu['sucursal_id']."|".$sucu['variaciones']."|".$sucu['variaciones_id']] = $sucu['stock'];
			
				$this->stock_sucursal_comprometido[$sucu['referencia_variacion']."|".$sucu['sucursal_id']."|".$sucu['variaciones']."|".$sucu['variaciones_id']] = $this->real_stock_sucursal[$sucu['referencia_variacion']."|".$sucu['sucursal_id']."|".$sucu['variaciones']."|".$sucu['variaciones_id']] - $this->stock_sucursal[$sucu['referencia_variacion']."|".$sucu['sucursal_id']."|".$sucu['variaciones']."|".$sucu['variaciones_id']];
				
				}
            
            // Setea el costo y los codigos de las variaciones 
            
			$this->datos_variaciones = productos_variaciones_datos::where('product_id', $this->selected_id)->where('eliminado',0)->get();

			//Cod entorno de testing:

			/*$this->datos_variaciones = productos_variaciones_datos::where('product_id', $this->selected_id)->get();
			*/

			foreach($this->datos_variaciones as $llaves => $sucus) {
				$this->costos_variacion[$sucus['referencia_variacion']] = $sucus['cost'];
			}
			
			foreach($this->datos_variaciones as $llaves => $sucus) {
				$this->precios_internos_variacion[$sucus['referencia_variacion']] = $sucus['precio_interno'];
			}
			
			
			foreach($this->datos_variaciones as $llaves => $sucus) {
				$this->cod_variacion[$sucus['referencia_variacion']] = $sucus['codigo_variacion'];
			}
			
            // Setea las lista de precios de las variaciones 
            
			$this->productos_lista_precios = productos_lista_precios::where('product_id', $this->selected_id)->get();
            
           // dd($this->productos_lista_precios);
            
			foreach ($this->productos_lista_precios as $key => $lp) {
				$this->precio_lista[$lp['referencia_variacion']."|".$lp['lista_id']] = $lp['precio_lista'];
			}

		}

        //dd($product);
        
	    $this->etiqueta = $this->GetEtiquetas($product->comercio_id,"productos");
	    
	    $this->GetEtiquetasEdit($product->id,"productos",$product->comercio_id);
	    
	    $this->forma_edit = $forma;

	}
	
   //--------- FUNCION QUE ACTUALIZA PRODUCTOS SIMPLES --------- //

    public function QueryProductoSimple($product) {
    
    $this->SetearProductosSimplesEnCero();
    $this->QueryStock($product);
    $this->QueryPrecios($product);

    }
    
   //--------- FUNCION QUE ACTUALIZA PRODUCTOS VARIABLES --------- //
    
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

    $this->QueryCostoVariable($product);

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
    
    //-------- FUNCION QUE CREA / ACTUALIZA EL CODIGO DE LAS VARIACIONES ---- /
    
    public function DetectarCodigosVariablesRepetidos() {
    
    $arreglo = $this->cod_variacion;
    
    
    $nuevoArray = array_values($arreglo);
   
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
    

    
		/*foreach ($this->cod_variacion as $llave => $value) {
		
			$pdv_cod = productos_variaciones_datos::where('referencia_variacion', $llave)->where('product_id',$product->id)->where('eliminado',0)->first();
		
			//dd($llave, $product->id);
			if($pdv_cod != null) {
			$pdv_cod->update([
				'codigo_variacion' => $this->cod_variacion[$llave]
				]);
			}

		}*/

		//Cod entorno testing:
		if($this->cod_variacion !== null){
			foreach ($this->cod_variacion as $llave => $cod){	
				//Cod proyecto woocommerce -->
				//$pdv_cod = productos_variaciones_datos::where('referencia_variacion', $llave)->where('product_id',$product->id)->where('eliminado',0)->first();	
				//<--
				
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
    
    
    public function QueryCostoVariable($product) {
        
                    
   /* foreach ($this->costos_variacion as $llave => $value) {
                        
    		$this->referencia_variacion =  $llave;
    
    		$pdv_cost = productos_variaciones_datos::where('referencia_variacion', $this->referencia_variacion)->where('product_id',$product->id)->where('eliminado',0)->first();
    				    
    		$pdv_cost->update([
    		    'cost' => $this->costos_variacion[$llave]
    		    ]); 		
        
    }    */

	foreach ($this->costos_variacion as $llave => $value) {		
	    
	    if ($value == "") {$this->costos_variacion[$llave] = 0;}
	    
		$this->referencia_variacion =  $llave;	
		
		//code woocommerce proyecto -->
		//$pdv_cost = productos_variaciones_datos::where('referencia_variacion', $this->referencia_variacion)->where('product_id',$product->id)->where('eliminado',0)->first();
		//<--
		$pdv_cost = productos_variaciones_datos::where('referencia_variacion', $this->referencia_variacion)
		->where('product_id',$product->id)
		->get();
		

		foreach ($pdv_cost as $pdv_c) {    					    
			$pdv_c->cost = $this->costos_variacion[$llave];
			$pdv_c->save();    		    
		} 			
	} 
}


    // ------ FUNCION QUE CREA / ACTUALIZA EL PRECIO INTERNO DE LAS VARIACIONES ----/
    
    
    public function QueryPrecioInternoVariable($product) {

	foreach ($this->precios_internos_variacion as $llave => $value) {							
		$this->referencia_variacion =  $llave;	

		$pdv_cost = productos_variaciones_datos::where('referencia_variacion', $this->referencia_variacion)
		->where('product_id',$product->id)
		->get();
		

		foreach ($pdv_cost as $pdv_c) {    					    
			$pdv_c->precio_interno = $this->precios_internos_variacion[$llave];
			$pdv_c->save();    		    
		} 			
	} 
}

    //--------- FUNCION QUE CREA / ACTUALIZA EL STOCK --------- //
    
    public function QueryStock($product) {

	if($this->real_stock_sucursal !== null){

		foreach ($this->real_stock_sucursal as $key => $value) {

		$array = explode('|', $key);
		$referencia_variacion =  $array[0];
		$this->sucursal_id =  $array[1];
		
		if($referencia_variacion != 0) {
		$this->var_nombre =  $array[2];
		$this->var_id =  $array[3];
		}
	
		$this->prod = productos_stock_sucursales::where('sucursal_id',$this->sucursal_id)
		->where('product_id',$product->id)
		->where('referencia_variacion', $referencia_variacion)
		->first();
		
		// dd($this->sucursal_id,$product->id,$referencia_variacion);
		

		if($this->prod != null) {

		$this->stock_anterior = $this->prod->stock;

		$this->prod->update([
		'almacen_id' => $this->almacen_id[$key],
		'stock_real' => $this->real_stock_sucursal[$key],
		'sucursal_id' => $this->sucursal_id,
		'comercio_id' => $this->casa_central_id,
		'product_id' => $product->id,

		]);
	    
	    $this->prod->update([
	    'stock' => $this->stock_sucursal[$key],   
	    ]);
	    
		$this->cantidad_movimiento = $this->real_stock_sucursal[$key] -  $this->stock_anterior;
		
		if($this->sucursal_id == 0) { $this->sucursal_id = $this->casa_central_id;} else {$this->sucursal_id = $this->sucursal_id; }

		$historico_stock = historico_stock::create([
		'tipo_movimiento' => 5,
		'producto_id' => $product->id,
		'cantidad_movimiento' => $this->cantidad_movimiento,
		'referencia_variacion' => $referencia_variacion,
		'stock' => $this->real_stock_sucursal[$key],
		'usuario_id' => $this->sucursal_id,
		'comercio_id'  => $this->sucursal_id
		]);


		} else {

	
		// SI NO ESTA EL STOCK CREADO DE ESE PRODUCTO SE CREA //
        
       // dd($this->real_stock_sucursal[$key]);
        
        $stock_real = intval($this->real_stock_sucursal[$key]);
        
		$product_create = productos_stock_sucursales::create([
		'almacen_id' => $this->almacen_id[$key],
		'stock' => $stock_real,
		'stock_real' => $stock_real,
		'sucursal_id' => $this->sucursal_id,
		'comercio_id' => $this->casa_central_id,
		'referencia_variacion' => $referencia_variacion,
		'product_id' => $product->id,
		]);
		
		//dd($product_create);

		if($this->sucursal_id == 0) { $this->sucursal_id = $this->casa_central_id;} else {$this->sucursal_id = $this->sucursal_id; }

		if($referencia_variacion != 0) {
		
		$chequear = productos_variaciones_datos::where('referencia_variacion', $referencia_variacion)->where('product_id',$product->id)->first();

		//Code entorno de testing:
		//$chequear = productos_variaciones_datos::where('referencia_variacion', $this->referencia_variacion)->where('product_id',$product->id)->first();
		
			if($chequear == null) {
				productos_variaciones_datos::create([
				'product_id' => $product->id,
				'referencia_variacion' => $referencia_variacion,
				'variaciones' => $this->var_nombre,
				'variaciones_id' => $this->var_id,
				'comercio_id' => $this->sucursal_id
				]);
			}
		}

    //	$this->cantidad_movimiento = $this->real_stock_sucursal[$key];

		$historico_stock = historico_stock::create([
		'tipo_movimiento' => 6,
		'producto_id' => $product->id,
		'cantidad_movimiento' => $stock_real,
		'referencia_variacion' => $referencia_variacion,
		'stock' => $stock_real,
		'usuario_id' => $this->sucursal_id,
		'comercio_id'  => $this->sucursal_id
		]);


		}

		}
	//Cod entorno de testing -->
	}
	//<--
}
    
    //--------- FUNCION QUE CREA / ACTUALIZA EL PRECIO --------- //
    
    public function QueryPrecios($product) {
        
        foreach ($this->precio_lista as $key => $value) {

        if ($value === "") {$this->precio_lista[$key] = 0;}
        
				$array = explode('|', $key);
                $referencia_variacion =  $array[0];
                $this->lista_id =  $array[1];
                
                
                if($this->precio_lista[$key] != '') {
        		$this->precio_lista[$key] = $this->precio_lista[$key];
        		} else {
        		$this->precio_lista[$key] = 0;
        		}

                 // Busca el precio de la lista
                 
				$this->prod = productos_lista_precios::where('lista_id',$this->lista_id)
				->where('product_id',$product->id)
				->where('referencia_variacion',$referencia_variacion)
				->first();
                
                
                // si existe el precio de la lista de precios la actualiza 
                
				if($this->prod != null) {

					$this->prod->update([
						'precio_lista' => $this->precio_lista[$key],
						'lista_id' => $this->lista_id,
						'comercio_id' => $this->casa_central_id,
						'product_id' => $product->id,

					]);

				} else {
                
                // si no existe el precio de la lista la crea 
                
					productos_lista_precios::create([
						'precio_lista' => $this->precio_lista[$key],
						'lista_id' => $this->lista_id,
						'comercio_id' => $this->casa_central_id,
						'referencia_variacion' => $referencia_variacion,
						'product_id' => $product->id,
					]);


				}

       
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
		$this->producto_tipo = 'Elegir';
		$this->referencia_variacion = 0;
		$categoria_wc = 0;
		$this->precio_lista = null;
	    $this->stock_sucursal = null;
	    $this->real_stock_sucursal = null;
	    $this->almacen_id = null;
	    $this->stock_sucursal_comprometido = null;

		//Cod entorno testing:
		$cart = new CartVariaciones;     
		$cart->clear();
		$this->cod_variacion = [];
		$this->precio_interno = "";
		
		// 28-5-2024
		$this->tipo_unidad_medida = 9;
        $this->cantidad_unidad_medida = 1;
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
	
	$this->producto_tipo = 'Elegir';
	
	$this->ResetVariablesVariaciones();
	
	$this->emit('modal-show','Show modal');
	
	    
	}
	

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
    
    $this->AccionEnLoteWc($pc->id, $estado);
    
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

    public function SwitchExportarCatalogosOld() {
    // Si es catalogo    
    if($this->product_section == "Products" ) {
        $this->ExportarCatalogoProduct();
    }
    // Si es lista de precios
        if($this->product_section == "ProductsPrecio" ) {
        $this->emit('modal-export-listas-show','');
        
    }
    // Si es stock
    if($this->product_section == "ProductsStock" ) {
        $this->emit('modal-export-stocks-show','');
    }
    
    }


    public function ExportarCatalogoProduct() {
    $comercio_id = (Auth::user()->comercio_id != 1) ? Auth::user()->comercio_id : Auth::user()->id;
    $this->casa_central_id = Auth::user()->casa_central_user_id;

    $p_count = Product::where('comercio_id', $this->casa_central_id)->where('eliminado', 0)->count();
    $filtros = ($this->id_categoria ?? 0) . "|" . ($this->id_almacen ?? 0) . "|" . ($this->proveedor_elegido ?? 0);
    
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

    if ($estado == 1) {
        $reportName = $nombre . '.xlsx';
        Excel::store(new ProductsExport($this->casa_central_id, $re->id), 'catalogos/' . $reportName);
        
        $re->update(['estado' => 2]);
    }
}

    public function ExportarCatalogoProductOld() {

    
	if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;
	
	$this->tipo_usuario = User::find($comercio_id);
	$this->sucursal_id = $comercio_id;
		    
	if($this->tipo_usuario->sucursal != 1) {

	$this->casa_central_id = $comercio_id;
			
	
	} else {
		  
	$this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
	$this->casa_central_id = $this->casa_central->casa_central_id;
	}

	$p_count = Product::where('comercio_id', $this->casa_central_id)->where('eliminado',0)->count();
    
    $filtros = ($this->id_categoria ?? 0)."|".($this->id_almacen ?? 0)."|".($this->proveedor_elegido ?? 0);
   
    
	if($p_count > 400) {

    $re = descargas::create([
    'user_id' => $comercio_id,
    'comercio_id' => $this->casa_central_id,
    'tipo' => 'exportar_productos',
    'estado' => 0,
    'datos_filtros' => $filtros,
    'nombre' => 'Productos_'. $comercio_id .'_'. Carbon::now()->format('d_m_Y_H_i_s')
    ]);
    
	} else {
	    
	
	$this->now = Carbon::now()->format('d_m_Y_H_i_s');
	
	$re = descargas::create([
    'user_id' => $comercio_id,
    'comercio_id' => $this->casa_central_id,
    'tipo' => 'exportar_productos',
    'estado' => 1,
    'datos_filtros' => $filtros,
    'nombre' => 'Productos_'. $comercio_id .'_' . $this->now
    ]);
    
    
    $reportName = 'Productos_'. $comercio_id .'_'.  $this->now.'.xlsx';
    
    Excel::store(new ProductsExport($this->casa_central_id, $re->id), 'catalogos/'.$reportName);
    
    $reports = descargas::find($re->id);
    
    $reports->estado = 2;
    $reports->save();
    
    //   ENVIAR NOTIFICACION CUANDO ESTE LISTO PARA EXPORTAR    //

	$esquema = User::find($re->user_id);

	$notificacion = [
	'titulo' => 'Excel de Catalogo',
	'contenido' => 'Listo para Descargar'
	];

	Notification::sendNow($esquema, new NotificarCambios($notificacion));
    
        
	}


        
        
    return redirect('descargas'); 

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
				
					$this->cod_variacion[$this->referencia_id] = '';
				
					//return dd($this->cod_variacion);
                	
                	
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
                	



    
   $this->SetearAgregarVariacion($this->referencia_id);
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
		['referencia_variacion', '=', $id_variacion]
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
  	    
		//dd('Hola desde producto tipo');

  	if($this->producto_tipo == "s") {


	  	if(0 < $this->selected_id) {
	//	$this->CambiarProductoTipo($this->producto_tipo);	
		$this->emit('cambiar-tipo-producto','');
		//-->
		$this->cost = [];
		$this->precio_lista = [];
		$this->stock_sucursal["0|0|0|0"] = [];
		$cart = new CartVariaciones;
		$cart->clear();
		//<--
        return;
  	    }

	// si el producto es simple y es a agregar, lo setea bien
//	if($this->selected_id == 0) {
//	    $this->SetearProductoSimple();
//	}		

	} 

	if($this->producto_tipo == "v") {
	    
	  	if(0 < $this->selected_id) {
	  //	$this->CambiarProductoTipo($this->producto_tipo);	
        $this->emit('cambiar-tipo-producto','');
		$this->ResetVariablesVariaciones();
		$cart = new CartVariaciones;
		$cart->clear();
        return;
  	    } else {}
	    
	}

  	    
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
				$this->DestroyVariacion($pv->referencia_variacion);
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


	public function renderProductsList($productSection)
	{
			$this->products = Product::join('categories as c','c.id','products.category_id')
				->join('seccionalmacens as a','a.id','products.seccionalmacen_id')
				->join('proveedores as pr','pr.id','products.proveedor_id')
				->select('products.*','c.name as category','a.nombre as almacen','pr.nombre as nombre_proveedor')
				->where('products.comercio_id', 'like', $this->casa_central_id)
				->where('products.eliminado', 'like', $this->estado_filtro);

			//Filtrar productos por categoria, almacen o provedor
			//$this->filtrarProductos();
			$this->products = $this->RenderFiltrar($this->products,$this->id_categoria,$this->id_almacen,$this->proveedor_elegido,$this->etiquetas_filtro);
			
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
				$this->DestroyVariacion($pv->referencia_variacion);
				
			
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
	    $cart = new CartVariaciones;
		$cart->clear();
	    $this->agregar = 0; 
	    $this->producto_tipo = 'Elegir';
	    $this->tipo_unidad_medida = 9;
        $this->cantidad_unidad_medida = 1;


	}
	
	
	public function RenderFiltrar($products,$categoria,$almacen,$proveedor,$etiquetas) {
	    
	    	if($categoria) {

			$products = $products->where('products.category_id', $categoria);

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
	
    public function CambiarStockDisponible($index) {
        
    //    dd($index);
        if($this->selected_id != 0) {
        
        if(empty($this->real_stock_sucursal[$index])){ $this->real_stock_sucursal[$index] = 0;}
        if(empty($this->stock_sucursal_comprometido[$index])){ $this->stock_sucursal_comprometido[$index] = 0;}
        
        $this->stock_sucursal[$index] = $this->real_stock_sucursal[$index] - $this->stock_sucursal_comprometido[$index];
        
        // Puedes hacer lo que quieras con el resultado, como guardarlo en una base de datos o mostrarlo en la vista.            
        }

    }
 
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
     $this->categoryid = "Elegir";
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


public function SetearProductosSimplesEnCero(){
        // si el precio de lista esta vacio pedimos que sea elegido
        
        $validacion = $this->ValidarPrecioBaseSimple();
        if($validacion == false){
        return;    
        }
        
        // tomamos las listas de precios
        $lista_de_precios = $this->getListaPrecios($this->casa_central_id);
        
        // seteamos el precio 
        
        if(0 < $lista_de_precios->count()){
        foreach($lista_de_precios as $lp){
        $in = "0|".$lp->id."|0|0";
        if(isset($this->precio_lista[$in])){
        if($this->precio_lista[$in] == ""){ $this->precio_lista[$in] = 0; }    
        } else {$this->precio_lista[$in] = 0;}
        }            
        }
        
        
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
        
    // seteamos el costo
    $this->costos_variacion[$variaciones['referencia_id']] = "";
   
    // seteamos el precio interno   
    if(0 < $sucursales->count()){
    $this->precios_internos_variacion[$variaciones['referencia_id']] = "";
    }
    
    $this->precio_lista[$variaciones['referencia_id']."|0"] = "";
    
    // tomamos las listas de precios
    $lista_de_precios = $this->getListaPrecios($this->casa_central_id);
        
    // seteamos el precio 
        
    if(0 < $lista_de_precios->count()){
    foreach($lista_de_precios as $lp){
    $this->precio_lista[$variaciones['referencia_id']."|".$lp->id] = "";    
    }
    }
    
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
    
    // Precio base
    if($this->precio_lista[$variaciones['referencia_id']."|0"] == "") {
    $this->emit('msg-error','Debe elegir el precio base');
    return;    
    }
    
    // Precio interno
    if(0 < $this->sucursales->count()) {  
    if($this->precios_internos_variacion[$variaciones['referencia_id']] == "") {
    $this->emit('msg-error','Debe elegir el precio interno');
    return;    
    }
    }
    
    // seteamos el costo
    if($this->costos_variacion[$variaciones['referencia_id']] == "") {$this->costos_variacion[$variaciones['referencia_id']] = 0;}

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
    
    //28-5-2024 --> Modificado
    
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
            $datos_facturacion = datos_facturacion::select('iva_defecto as iva')->where('comercio_id', $sucursal->sucursal_id)->first();
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
	
	//--------- FUNCION QUE CREA / ACTUALIZA EL IVA --------- //
    
    public function QueryIVA($product_id,$comercio_id) {

		foreach($this->porcentaje_iva as $key => $value) {
		    
        productos_ivas::updateOrCreate(
        ['product_id' => $product_id, 'comercio_id' => $comercio_id, 'sucursal_id' => $key],
        ['iva' => $value] // Puedes agregar aquí otros campos y sus valores
        );
        
        }

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
}