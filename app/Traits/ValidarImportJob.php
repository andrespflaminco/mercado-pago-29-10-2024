<?php
namespace App\Traits;


// Trait


// Modelos
use App\Models\Category;
use App\Models\Product;
use App\Models\variaciones;
use App\Models\productos_variaciones_datos;

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

use Illuminate\Support\Facades\Log;


trait ValidarImportJob {

// Array para almacenar combinaciones ¨²nicas
public $combinacionesUnicas = [];

//public $comercio_id;

    public function HacerValidaciones($row, $columna, $i, &$validacion_error,$comercio_id)
    {
        $this->comercio_id = $comercio_id;
        
        $validacion_error = $this->ValidarTipoProducto($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarCodVariacion($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarNombre($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarOrigen($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarImagen($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarInvMinimo($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarCosto($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarPrecioInterno($row, $columna, $validacion_error, $i);
    //    $validacion_error = $this->ValidarReglaPrecio($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarPrecio($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarListaDePrecios($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarRepeticionCodVariacion($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarMatchCodVariacion($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarExisteCodVariacion($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarStock($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarStockSucursales($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarIVA($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarIVASucursales($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarManejaStock($row, $columna, $validacion_error, $i);
    //    $validacion_error = $this->ValidarPesables($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarVentaMostrador($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarVentaEcommerce($row, $columna, $validacion_error, $i);
        $validacion_error = $this->ValidarVentaWocommerce($row, $columna, $validacion_error, $i);

        return $validacion_error;
    }



public function ValidarRepeticionCodVariacion($row,$columna,$validacion_error,$i){
      
    // validamos que cada variacion tenga su codigo y viceversa //
    if($columna['cod_variacion'] != "0" || $columna['codigo'] != "0"){
    
    // Verificar si los ¨ªndices existen en $row
    if (isset($row[$columna['codigo']]) && isset($row[$columna['cod_variacion']])) {
            
    $codigo = $row[$columna['codigo']]; 
    $codVariacion = $row[$columna['cod_variacion']];

    // Crear una clave ¨²nica combinando ambos valores
    $combinacion = $codigo . '|' . $codVariacion;

    // Verificar si ya existe la combinaci¨®n
    if (isset($this->combinacionesUnicas[$combinacion])) {
        // Si ya existe, es un duplicado
        //dd($this->combinacionesUnicas);
        //Log::info('Duplicados', $this->combinacionesUnicas);
        $error = 'FILA NRO ' . $i + 2 . ' -> Combinacion de codigo y codigo variable duplicado.';
        $fe = $i;
        array_push($validacion_error, $error);

    } else {
        // Si no existe, lo agregamos al array de combinaciones ¨²nicas
        $this->combinacionesUnicas[$combinacion] = true;
    }
  
    }    
    
    }
      
    return $validacion_error;
}


public function ValidarVentaMostrador($row,$columna,$validacion_error,$i){
      
      if($columna['venta_mostrador'] != "0"){
      if(strtolower($row[$columna['venta_mostrador']] ?? '') != "si" && strtolower($row[$columna['venta_mostrador']] ?? '') != "no"){
        $error = 'FILA NRO ' . $i . '-> Venta mostrador tiene que ser "si" o "no".';
        $fe = $i;
        array_push($validacion_error, $error);
      }    
      }
      
      return $validacion_error;    
}

public function ValidarVentaEcommerce($row,$columna,$validacion_error,$i){
      
      if($columna['venta_ecommerce'] != "0"){
          
      if(strtolower($row[$columna['venta_ecommerce']] ?? '') != "si" && strtolower($row[$columna['venta_ecommerce']] ?? '') != "no"){
        $error = 'FILA NRO ' . $i . '-> Venta ecommerce tiene que ser "si" o "no".';
        $fe = $i;
        array_push($validacion_error, $error);
      }    
      }
      
      return $validacion_error;    
}

public function ValidarVentaWocommerce($row,$columna,$validacion_error,$i){
      
      if($columna['venta_wocommerce'] != "0"){
          
      if(strtolower($row[$columna['venta_wocommerce']] ?? '') != "si" && strtolower($row[$columna['venta_wocommerce']] ?? '') != "no"){
        $error = 'FILA NRO ' . $i . '-> Venta wocommerce tiene que ser "si" o "no".';
        $fe = $i;
        array_push($validacion_error, $error);
      }    
      }
      
      return $validacion_error;    
}

public function ValidarManejaStock($row,$columna,$validacion_error,$i){
      
      if($columna['maneja_stock'] != "0"){
          
      if(strtolower($row[$columna['maneja_stock']] ?? '') != "si" && strtolower($row[$columna['maneja_stock']] ?? '') != "no"){
        $error = 'FILA NRO ' . $i . '-> Maneja stock tiene que ser "si" o "no".';
        $fe = $i;
        array_push($validacion_error, $error);
      }    
      }
      
      return $validacion_error;    
}


public function ValidarTipoProducto($row,$columna,$validacion_error,$i){
    if($columna['tipo_producto'] != "0"){
      if($row[$columna['tipo_producto']] != "simple" && $row[$columna['tipo_producto']] != "variable"){
        $error = 'FILA NRO ' . $i . '-> El tipo de producto tiene que ser "simple" o "variable".';
        $fe = $i;
        array_push($validacion_error, $error);
      }    
    }
      return $validacion_error;    
}


public function BuscarCodigoVariacion($codigo,$codigo_variacion,$comercio_id,$i){
    $product = Product::where("barcode",$codigo)->where("comercio_id",$comercio_id)->where("eliminado",0)->first();

    if($product != null) {
    if($product->producto_tipo == "v"){
    $pvd_datos = productos_variaciones_datos::where('codigo_variacion', $codigo_variacion)->where('product_id', $product->id)->where('comercio_id', $comercio_id)->where('eliminado', 0)->first();    
    if($pvd_datos == null){
    $error = 'FILA NRO ' . $i . ' -> El cod variacion "'.$codigo_variacion.'" no existe en ese producto.';
    $return = $error;
    } else {
    $return = false;    
    }
    return $return;
    } else {$return = false; }
    }
        
    }
    
public function BuscarExistenciaCodigo($codigo,$comercio_id,$i){
    $product = Product::where("barcode",$codigo)->where("comercio_id",Auth::user()->casa_central_user_id)->where("eliminado",0)->first();
    if($product == null){
    $error = 'FILA NRO ' . $i . ' -> El codigo '.$codigo.' no existe';
    return $error;
    } else {
    return false;    
    }
    }

public function ValidarNombre($row,$columna,$validacion_error,$i){
      if($columna['nombre'] != "0"){
      if (empty($row[$columna['nombre']]) || $row[$columna['nombre']] === '' || $row[$columna['nombre']] == null) {
        $error = 'FILA NRO ' . $i . '-> El nombre es requerido.';
        $fe = $i;
        array_push($validacion_error, $error);
      }
      }    
      
      return $validacion_error;
}

public function ValidarOrigen($row,$columna,$validacion_error,$i){
    if($columna['origen'] != "0"){
          
          if ($row[$columna['origen']] != "compra" && $row[$columna['origen']] != "produccion" && $row['origen'] != "ensamblado en la venta") {
            $error = 'FILA NRO ' . $i . '-> El origen debe ser "compra" o "produccion" o "ensamblado en la venta".';
            $fe = $i;
            array_push($validacion_error, $error);
          }
          
          }
    
      return $validacion_error;
    
}

public function ValidarImagen($row,$columna,$validacion_error,$i){

      if($columna['imagen'] != "0"){
      if ($row[$columna['imagen']] != "") {
        $imagen = imagenes::where('name', $row[$columna['imagen']])->where('comercio_id', $this->comercio_id)->where('eliminado', 0)->first();
        if ($imagen == null) {
          $error = 'FILA NRO ' . $i . '-> El nombre de la imagen no coincide con las imagenes en la biblioteca.';
          $fe = $i;
          array_push($validacion_error, $error);
        }
      }
      }
      return $validacion_error;
}

public function ValidarInvMinimo($row,$columna,$validacion_error,$i){

if($columna['inv_minimo'] != "0"){

      if (!empty($row[$columna['inv_minimo']]) &&  !is_numeric($row[$columna['inv_minimo']])) {
      //  dd($row['inv_minimo']);
        $error = 'FILA NRO ' . $i . ' -> El inventario minimo debe ser un numero.';
        $fe = $i;
        array_push($validacion_error, $error);
      }
      
      }
      return $validacion_error;
}

public function ValidarPrecio($row,$columna,$validacion_error,$i){
         
      if($columna['precio'] != "0"){
      if (!is_numeric($row[$columna['precio']]) ) {
        $error = 'FILA NRO ' . $i . ' -> El precio debe ser un numero.';
        $fe = $i;
        array_push($validacion_error, $error);
      }
     }
     return $validacion_error;
}

public function ValidarPrecioInterno($row,$columna,$validacion_error,$i){
         
      if($columna['precio_interno'] != "0"){
      if (!empty($row[$columna['precio_interno']]) && !is_numeric($row[$columna['precio_interno']]) ) {
        $error = 'FILA NRO ' . $i . ' -> El precio interno debe ser un numero.';
        $fe = $i;
        array_push($validacion_error, $error);
      }
     }
     return $validacion_error;
}

public function ValidarCosto($row,$columna,$validacion_error,$i){
         
    if($columna['costo'] != "0"){
      if (!empty($row[$columna['costo']]) && !is_numeric($row[$columna['costo']]) ) {
        $error = 'FILA NRO ' . $i . ' -> El costo debe ser un numero.';
        $fe = $i;
        array_push($validacion_error, $error);
    }
    }
      
     return $validacion_error;
}

public function ValidarExisteCodVariacion($row,$columna,$validacion_error,$i){
         
      if($columna['cod_variacion'] != "0"){

      $return = $this->BuscarCodigoVariacion($row[$columna['codigo']],$row[$columna['cod_variacion']],$this->comercio_id,$i);

      if($return != false){
      $fe = $i;
      array_push($validacion_error, $return);
      }
      
      }
      
     return $validacion_error;
}



public function ValidarMatchCodVariacion($row,$columna,$validacion_error,$i){
      
      // validamos que cada variacion tenga su codigo y viceversa //
      if($columna['cod_variacion'] != "0" || $columna['variacion'] != "0"){

      if (($row[$columna['variacion']] != null && $row[$columna['cod_variacion']] == null) || ($row[$columna['variacion']] == null && $row[$columna['cod_variacion']] != null)) {
        $error = 'FILA NRO ' . $i . ' -> Variacion o Cod variacion estan vacias.';
        $fe = $i;
        array_push($validacion_error, $error);
      }
      
      // validamos que las variaciones ingresadas existan //
      /*
      if ($row[$columna['variacion']] != null) {

        $variaciones = explode("-", $row[$columna['variacion']]);

        foreach ($variaciones as $vari => $j) {

          // si la variacion tiene un campo vacio al inicio

          if ($j[0] == ' ') {

            $nombre_var = ltrim($j, " "); // Eliminar el primer campo vacio

          } else {
            $nombre_var = $j;
          }

          // si la variacion tiene un campo vacio al final

          if ($j[strlen($j) - 1] == ' ') {

            $nombre_var = substr($nombre_var, 0, -1); // Eliminar ultimo campo

          } else {
            $nombre_var = $nombre_var;
          }

          //    Buscamos si existen las variaciones

          $variacion = variaciones::join('atributos', 'atributos.id', 'variaciones.atributo_id')
            ->select('variaciones.*')
            ->where('variaciones.nombre', $nombre_var)
            ->where('variaciones.comercio_id', $this->casa_central_id)
            ->where('variaciones.eliminado', 0)
            ->where('atributos.eliminado', 0)
            ->first();

          if ($variacion == null) {
            $error = 'FILA NRO ' . $i . ' -> La variacion  "' . $nombre_var . '" no existe.';
            $fe = $i;
            array_push($validacion_error, $error);
          }
        }
      }
      */
      }    
      
      return $validacion_error;
}

public function ValidarStock($row,$columna,$validacion_error,$i){
if($columna['stock'] != "0"){

      if (!empty($row[$columna['stock']]) && !is_numeric($row[$columna['stock']])) {
        $error = 'FILA NRO ' . $i . ' -> El stock debe ser un numero.';
        $fe = $i;
        array_push($validacion_error, $error);
      }
      
      }    
return $validacion_error;
}

public function ValidarPesables($row,$columna,$validacion_error,$i){
     
     if($columna['pesable'] != "0"){
     if($row[$columna['pesable']] != "si" && $row[$columna['pesable']] != "no"){
        $error = 'FILA NRO ' . $i . ' -> El valor de pesable debe ser "si" o "no".';
        $fe = $i;
        array_push($validacion_error, $error); 
      }
      
      if (($row[$columna['pesable']] == "si") && ($this->digitos_codigo != strlen($row[$columna['codigo']]))) {
        $error = 'FILA NRO ' . $i . ' -> El codigo debe tener '.$this->digitos_codigo.' digitos para productos pesables.';
        $fe = $i;
        array_push($validacion_error, $error);
      }
      
      if ( ($row[$columna['pesable']] == "si") && (!ctype_digit($row[$columna['codigo']]) ) ) {
        $error = 'FILA NRO ' . $i . ' -> El codigo debe contener solo numeros para codigos pesables';
        $fe = $i;
        array_push($validacion_error, $error);
      }
      }
      
      return $validacion_error;
}


public function ValidarReglaPrecio($row,$columna,$validacion_error,$i){
    if($columna['regla_precio'] != "0"){
     if($row[$columna['regla_precio']] != "% sobre el costo" && $row[$columna['regla_precio']] != "precio fijo"){
        $error = 'FILA NRO ' . $i . ' -> La regla precio debe ser "% sobre el costo"  o  "precio fijo".';
        $fe = $i;
        array_push($validacion_error, $error);
        }
     }
     
     return $validacion_error;
}

public function ValidarCodigo($row,$columna,$validacion_error,$i) {

    if (($row[$columna['codigo']] != null) && (20 < strlen($row[$columna['codigo']]))) {
        $error = 'FILA NRO ' . $i . ' -> El codigo no puede contener mas de 20 digitos.';
        $fe = $i;
        array_push($validacion_error, $error);
      }
      
      return $validacion_error;
}

public function ValidarCodVariacion($row,$columna,$validacion_error,$i) {

    if($columna['cod_variacion'] != "0"){
    if (($row[$columna['cod_variacion']] != null) && (20 < strlen($row[$columna['cod_variacion']]))) {
        $error = 'FILA NRO ' . $i . ' -> El cod variacion no puede contener mas de 20 digitos.';
        $fe = $i;
        array_push($validacion_error, $error);
    }          
    }
      return $validacion_error;
}

public function ValidarIVA($row,$columna,$validacion_error,$i){
      
      //dd($columna['iva']);
      if($columna['iva'] != "0") {
      if (!preg_match('/^\d+(\.\d+)?%?$/', $row[$columna['iva']])) {
        $error = 'FILA NRO ' . $i . ' -> El iva debe ser un numero o un numero seguido del signo %';
        $fe = $i;
        array_push($validacion_error, $error); 
      }
      
        $iva = trim($row[$columna['iva']]); // Eliminar espacios en blanco al inicio y final

        // ExpresiÃ³n regular para validar los formatos permitidos
        $regex = '/^(?:21%|0,21|0(?:\.00|,00)?|0%|0\.21|0\.105|10,5%|10\.5%|0,105|0\.105|27%|0,27|0\.27)$/';
   
        // Validar si $iva cumple con alguno de los formatos permitidos
        if (!preg_match($regex, $iva)) {
        $error = 'FILA NRO ' . $i . ' -> El iva debe ser del 0%, 10,5%, 21% o 27%';
        $fe = $i;
        array_push($validacion_error, $error);
        }
          
      }
      
      return $validacion_error;
}

public function ValidarListaDePrecios($row,$columna,$validacion_error,$i){

    foreach ($row as $key => $r) {
        $var = explode("_", $key);
        if (count($var) > 2) {
        if (is_array($var) && $var[1] == "precio" && is_numeric($var[0])) {
       
        if (!empty($r) && !is_numeric($r)) {
        $error = 'FILA NRO ' . $i . ' -> El valor asignado a '.$key.' debe ser un numero.';
        $fe = $i;
        array_push($validacion_error, $error);
        }
        
        }}
        }
        
    return $validacion_error;
        
}
public function ValidarStockSucursales($row,$columna,$validacion_error,$i){

    foreach ($row as $key => $r) {
        $var = explode("_", $key);
        if (count($var) > 2) {
        if (is_array($var) && $var[1] == "stock" && is_numeric($var[0])) {
       
        if (!empty($r) && !is_numeric($r)) {
        $error = 'FILA NRO ' . $i . ' -> El '.$key. ' debe ser un numero.';
        $fe = $i;
        array_push($validacion_error, $error);
        }
        
        }}
        }
        
    return $validacion_error;
        
}

public function ValidarIVASucursales($row,$columna,$validacion_error,$i){

    foreach ($row as $key => $r) {
        $var = explode("_", $key);
        if (count($var) > 2) {
        if (is_array($var) && $var[1] == "iva" && is_numeric($var[0])) {
        // ExpresiÃ³n regular para validar los formatos permitidos
        $regex = '/^(?:21%|0,21|0(?:\.00|,00)?|0%|0\.21|0\.105|10,5%|10\.5%|0,105|0\.105|27%|0,27|0\.27)$/';
   
        // Validar si $iva cumple con alguno de los formatos permitidos
        if (!preg_match($regex, $r)) {
        $error = 'FILA NRO ' . $i . ' -> El valor asociado a '.$key.' debe ser del 0%, 10,5%, 21% o 27%';
        $fe = $i;
        array_push($validacion_error, $error);
        }        
            
        }}
        }
        
    return $validacion_error;
        
}

}
