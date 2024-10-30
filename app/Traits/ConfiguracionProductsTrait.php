<?php
namespace App\Traits;

// Modelos
use App\Models\User;
use App\Models\Product;
use App\Models\configuracion_codigos;
use App\Models\configuracion_stock;

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


//Validator
use Illuminate\Support\Facades\Validator;


trait ConfiguracionProductsTrait {


    // 28-5-2024 ---> Modificacion de codigos
    
    public $numeros_prefijo,$numeros_codigo,$numeros_peso,$prefijo_pesables,$configuracion_ver,$configuracion_codigos;
    
    public $digitos_cantidad_unidades, $configuracion_decimales_unidades, $digitos_cantidad_kg, $muestra_stock_otras_sucursales,$muestra_stock_casa_central; // 10-6-2024

    public $casa_central_id;
    // MOUNT CONFIGURACIONES 

    public function GetConfiguracion(){
    
    $this->GetConfiguracionCostoPrecioInterno();
    
    $this->GetConfiguracionDecimales();

    $this->GetConfiguracionCodigos();
    
    $this->configuracion_ver = 'codigos';
    }
    
    public function GetConfiguracionCostoPrecioInterno(){
    $u = User::find($this->casa_central_id);
    $this->configuracion_precio_interno = $u->costo_igual_precio;    
    }
    
    public function GetConfiguracionDecimales(){
        $configuracion = configuracion_stock::where('comercio_id',$this->casa_central_id)->first(); 
        if($configuracion == null){
        $this->configuracion_decimales_unidades = 0;
        $this->digitos_cantidad_unidades = 0;
        $this->digitos_cantidad_kg = 0;
        $this->muestra_stock_otras_sucursales = 1;
        $this->muestra_stock_casa_central = 1;
        } else {
        $this->configuracion_decimales_unidades = $configuracion->digitos_cantidad_unidades;
        $this->digitos_cantidad_unidades = $configuracion->digitos_cantidad_unidades;   
        $this->digitos_cantidad_kg = $configuracion->digitos_cantidad_kg;   
        $this->muestra_stock_otras_sucursales = $configuracion->muestra_stock_otras_sucursales;
        $this->muestra_stock_casa_central = $configuracion->muestra_stock_casa_central;
        }    
    }
    
    public function GetConfiguracionCodigos(){
        $configuracion_codigos = configuracion_codigos::where('comercio_id',$this->casa_central_id)->first(); 
       if($configuracion_codigos == null){
        $this->configuracion_codigos = 0;
        $this->numeros_prefijo = 2;
        $this->numeros_codigo = 5;
        $this->numeros_peso = 5; 
        $this->prefijo_pesables = null;
        } else {
        $this->configuracion_codigos = $configuracion_codigos->tipo_codigo;
        $this->numeros_prefijo = $configuracion_codigos->digitos_prefijo;
        $this->numeros_codigo = $configuracion_codigos->digitos_codigo;
        $this->numeros_peso = $configuracion_codigos->digitos_peso;  
        $this->prefijo_pesables = $configuracion_codigos->prefijo_pesable;
        }
    }

    /// ------------------------------------------  //
    
    
    // Actualizar Configuracion de Codigos
    
    public function UpdateConfiguracionCodigos(){
    
    if($this->configuracion_codigos == 1){
    
    $products = Product::where('comercio_id', $this->casa_central_id)->where('unidad_medida',1)->where('eliminado',0)->count();
    
    if(0 < $products){
    $this->emit("msg-error","No puede modificar la estructura de codigos teniendo productos pesables con codigo.");
    return;
    }
    
    $prefijoLength = strlen($this->prefijo_pesables);

    if ( ($this->numeros_prefijo + $this->numeros_codigo + $this->numeros_peso) != 12  ) {
    $this->emit("msg-error","La codigos pesables deben tener 12 digitos. Chequee la Estructura del codigo");
    return;
    }
    
    if ($this->numeros_prefijo != $prefijoLength) {
    $this->emit("msg-error","La cantidad de digitos del prefijo es distinto al numero de digitos asignados para ello");
    return;
    }
    }   
    
    $u = User::find($this->casa_central_id);
    $u->configuracion_codigos = $this->configuracion_codigos;
    $u->save();
    
    $this->ActualizarConfiguracionCodigos();    
    
        
    $this->CerrarModalConfiguracion();
    $this->emit("product-added","Configuracion actualizada"); 
    
    }
    
    public function ActualizarConfiguracionCodigos(){
        // Actualizar o crear el usuario
        $configuracion = configuracion_codigos::updateOrCreate(
            [
            'comercio_id' => $this->casa_central_id
            ],
            [
            'tipo_codigo' => $this->configuracion_codigos,
            'prefijo_pesable' => $this->prefijo_pesables,
            'digitos_prefijo' => $this->numeros_prefijo,
            'digitos_codigo' => $this->numeros_codigo,
            'digitos_peso' => $this->numeros_peso
            ]
        );
    }

    // Configuracion de precios internos
    
    public function UpdateConfiguracion($id){
    
    // Si detecta que tiene productos pesables dados de alta no debe dejar cambiar la configuracion de los codigos de barra
    $u = User::find($this->casa_central_id);
    $u->costo_igual_precio = $this->configuracion_precio_interno;
    $u->save();
    
    if($this->configuracion_precio_interno == 1){
    $this->ActualizarPreciosInternosCambioConfiguracion();    
    }
    
    $this->CerrarModalConfiguracion();
    
    if($this->configuracion_precio_interno == 1){
    $this->emit("product-added","Configuracion y Precios internos actualizados");    
    } else {
    $this->emit("product-added","Configuracion actualizada");        
    }
        
    }

    public function ActualizarPreciosInternosCambioConfiguracion(){
    
    $products = Product::where('comercio_id',$this->casa_central_id)->where('eliminado',0)->get();
    
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
    
    // Configuracion de digitos de stock 
    
    public function UpdateConfiguracionDigitosStock(){
    
    
        // Actualizar o crear el usuario
        $configuracion = configuracion_stock::updateOrCreate(
            [
            'comercio_id' => $this->casa_central_id
            ],
            [
            'digitos_cantidad_unidades' => $this->digitos_cantidad_unidades,
            'digitos_cantidad_kg' => $this->digitos_cantidad_kg,
            'muestra_stock_otras_sucursales' => $this->muestra_stock_otras_sucursales,
            'muestra_stock_casa_central' => $this->muestra_stock_casa_central
            ]
        );
    
    
    $this->CerrarModalConfiguracion();
    $this->emit("product-added","Configuracion actualizada");    
                
    }
    
    // Modales ---
    
    public function AbrirModalConfiguracion(){
    $this->GetConfiguracion();
    
    $this->ver_configuracion = 1;  
    $this->agregar = 0;
    }
    public function CerrarModalConfiguracion(){
    $this->ver_configuracion = 0;  
    $this->agregar = 0;
    }

    public function CambiarConfiguracionVer($value){
        $this->configuracion_ver = $value;
    }
    
    public function CambioTipoCodigo(){
       $configuracion_codigos = configuracion_codigos::where('comercio_id',$this->casa_central_id)->first(); 
       if($configuracion_codigos != null){
        $this->configuracion_codigos = $configuracion_codigos->tipo_codigo;
        $this->numeros_prefijo = $configuracion_codigos->digitos_prefijo;
        $this->numeros_codigo = $configuracion_codigos->digitos_codigo;
        $this->numeros_peso = $configuracion_codigos->digitos_peso;  
        $this->prefijo_pesables = $configuracion_codigos->prefijo_pesable;
        }
    }
    
    
}