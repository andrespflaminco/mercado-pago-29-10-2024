<?php

namespace App\Http\Livewire;

use App\Services\Cart;
use App\Imports\CategoriesImport;
use App\Imports\PagosImport;
use Illuminate\Support\Facades\Auth;
use App\Imports\ProductsImport;
use App\Imports\ProductsValidationImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Category;
use App\Models\variaciones;
use App\Models\imagenes;
use App\Models\lista_precios;
use App\Models\User;
use Carbon\Carbon;
use App\Models\importaciones;
use App\Models\configuracion_codigos;

use App\Models\pagos_facturas;
use Illuminate\Http\Request;
use App\Models\Product;

use App\Models\productos_descuentos; // Actualizacion descuentos
use App\Models\listas_descuentos; // Actualizacion descuentos

use Illuminate\Support\Facades\Storage;

//
use Illuminate\Support\Facades\Bus;
use App\Jobs\IncrementCounterJob;
use Maatwebsite\Excel\HeadingRowImport;

use App\Models\proveedores;
use App\Models\historico_stock;
use App\Models\productos_variaciones;
use App\Models\productos_variaciones_datos;
use App\Models\productos_stock_sucursales;
use App\Models\sucursales;
use App\Models\productos_lista_precios;
use App\Models\seccionalmacen;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;



use Illuminate\Support\Facades\Event;

use App\Jobs\ImportJob;
use App\Jobs\ValidateImportProductsJob;

use Illuminate\Queue\InteractsWithQueue;

use App\Events\UpdateProgressImport;
use App\Listeners\UpdateProgressImportListener;


use Illuminate\Support\Facades\Artisan;

use App\Jobs\ImportStartQueue;

use Mail; // 14-8-2024

class ImportController extends Component
{
  use WithFileUploads;
  
  public $respuesta; // 14-8-2024

  public $ayuda, $contCategories, $contProducts,$columna, $array_excel,$columna_excel,$lista_precios,$nro_paso,$actualizar_costos_4, $fila_procesadas,$fila_totales,$fileCategories, $fileProducts, $saltear_errores, $fila_error, $toImport, $file, $importaciones,$logo_paso1,$logo_paso2,$logo_paso3;
  
  public $columnas_excel = [];
  public $columnas_base = [];
  // 1-3-2024
  public $dataForView = [];
  public $importar_costos,$importar_precio_interno,$importar_precio,$importar_listas_precios,$acciones_2;
  
  public $paso2,$paso1,$paso3;
  
  public $validacion_errores = [];
  
  public $tipo_procesando;

  public $progress = 0;

  public $filePath;

  public $rowsImport;

  public $totalRows;

  public $importRowPosition = 0;

  public $progressByStep = 0;

  public $inicioRow;

  public $finalRow;

  public $estadoImportacion = 0;

  //
  public $accion;
  public $progressTest;
  public $comercio_id;
  public $tipo_usuario;
  public $sucursal_id;
  public $casa_central_id;
  public $casa_central;
  public $nombre_archivo;
  public $registerImportacion;

  //public $registerImportacionId;
  private $registerImportacionId;


  public function resetVariables()
  {
    $this->contCategories = null;
    $this->contProducts = null;
    $this->fileCategories = null;
    $this->fileProducts = null;
    $this->saltear_errores = null;
    $this->fila_error = null;
    $this->toImport = null;
    $this->file = null;
    $this->importaciones = null;
    $this->validacion_errores = [];
    $this->progress = 0;
    $this->filePath = 0;;
    $this->rowsImport = 0;
    $this->totalRows = 0;
    //$this->importRowPosition = 0;  
    $this->progressByStep = 0;
    $this->inicioRow;
    $this->finalRow;
    //private $registerImportacionId;

  }

  public function mount(Request $request)
  {
    $this->respuesta = null; // 14-8-2024
    
    if (Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    
    $casa_central_id = Auth::user()->casa_central_user_id;
    
    $this->configuracion_precios = $this->GetConfiguracionPrecios($casa_central_id);
    
    $importaciones = importaciones::where('comercio_id', $comercio_id)->orderBy('created_at', 'desc')->get();
    
    $this->product_count = Product::where('comercio_id',$casa_central_id)->where('eliminado',0)->get();
    $this->tipo_procesando = 1;
    
    if(0 < $importaciones->count()) {
        $this->nro_paso = 3;
        
        $this->paso3 = "display:block;";
        $this->paso2 = "display:none;";
        $this->paso1 = "display:none;";
        
    } else {
        $this->nro_paso = 1;
    
        $this->paso1 = "display:block;";
        $this->paso2 = "display:none;";
        $this->paso3 = "display:none;";
        
        if(Auth::user()->sucursal != 1) {
            $this->accion = 1;
        }
    
    }
    
    if( (Auth::user()->sucursal == 1)  && 0 < $this->product_count->count()){
        $this->nro_paso = 3;
        
        $this->paso3 = "display:block;";
        $this->paso2 = "display:none;";
        $this->paso1 = "display:none;";        
    }
    
    
    $importaciones = importaciones::where('comercio_id', $comercio_id)->orderBy('created_at', 'desc')->get();

    
    $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->where('sucursales.casa_central_id',$comercio_id)->where('sucursales.eliminado',0)->get();
    $this->lista_precios = [];
    
    //28-5-2024 -- mount
    $u = User::find($comercio_id);
    if($u->configuracion_codigos == 1){
        $configuracion_codigos = configuracion_codigos::where('comercio_id',$comercio_id)->first(); 
        $this->digitos_codigo = $configuracion_codigos->digitos_codigo;
    } else {
        $this->digitos_codigo = 0;
    }
    
        if(0 < $importaciones->count()){
        $ultima_importacion = $importaciones->first();
        if($ultima_importacion->terminado == 1){
        $import = importaciones::find($ultima_importacion->id);
        $import->estado = 2;
        $import->save();
        } 
    }  
    
    $importacion_id = $request->input('respuesta'); 
    $importacion_id = $importacion_id ?? 0;
    if($importacion_id != 0){
        $this->CheckImportacion($importacion_id);
    }        
      
      
   // $this->checkLastImport();      
  }

    protected function redirectLogin()
    {
        return \Redirect::to("login");
    }
    
	public function render()
	{
	   
        if (!Auth::check()) {
            // Redirigir al inicio de sesión y retornar una vista vacía
            $this->redirectLogin();
            return view('auth.login');
        }
        
    if (Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
    else
      $comercio_id = Auth::user()->id;

    $this->comercio_id = $comercio_id;

    $this->importaciones = importaciones::where('comercio_id', $comercio_id)->where('estado','<>',1)->orderBy('created_at', 'desc')->get();

    return view('livewire.import.component', ['importaciones' => $this->importaciones])
      ->extends('layouts.theme-pos.app')
      ->section('content');

    //$this->aumentarBarra();


  }





public function Descargar($id)
{
    // Buscar el registro de importación por ID
    $descarga = importaciones::find($id);

    // Obtener el nombre del archivo
    $file = $descarga->nombre;

    // Definir los paths posibles del archivo
    $path_guardados = base_path("resources/excel-guardados/" . $file . ".xlsx");
    $path_pendientes = base_path("resources/excel-pendientes/" . $file . ".xlsx");

    // Verificar si el archivo existe en 'excel-guardados'
    if (file_exists($path_guardados)) {
        return response()->download($path_guardados);
    }

    // Verificar si el archivo existe en 'excel-pendientes'
    if (file_exists($path_pendientes)) {
        return response()->download($path_pendientes);
    }

    // Si el archivo no se encuentra en ninguno de los paths, retornar un mensaje de error
    $this->emit('msg-error', 'Archivo no encontrado');
}


  // --------------- FUNCION QUE LEE EL EXCEL Y VALIDA LOS DATOS ---------- //
    public function GetMatchColumnas()
    {
        $this->validate([
            'fileProducts' => 'required|mimes:xlsx,xls|max:10240' // 10MB Max
        ]);

        // Obtener el path del archivo subido
        $path = $this->fileProducts->getRealPath();

        // Obtener las cabeceras del archivo Excel
        $headings = (new HeadingRowImport)->toArray($path);
        $this->columnas_excel = array_map('strtolower', $headings[0][0]);

        // Obtener los nombres de las columnas base
        $this->columnas_base = array_map('strtolower', $this->GetNombreTitulos());

        // Importar las primeras 5 filas del archivo Excel
        $import = new ProductsValidationImport();
        Excel::import($import, $path);
        $this->array_excel = $import->getRows();
        $this->dataForView = $this->array_excel;
    
        //$this->dataForView = [];

        // Asignar columnas base a las columnas del Excel
        foreach ($this->columnas_excel as $ce) {
            $this->columna_excel[$ce] = in_array($ce, $this->columnas_base) ? $ce : 0;
        }

    }

    public function SetMatchColumnasNew(){
        dd("hola");
    }
  
      
    public function SetMatchColumnas()
    {

    $matches = [];
    
    $alertas = $this->verificarValoresDuplicados($this->columna_excel);
    
    if(0 < count($alertas)){
        $alerta = implode(",",$alertas);
        $this->emit("msg-error","Los siguientes datos estan asignados dos veces: ".$alerta);
        return; // Sale del bucle foreach
    }
    
    foreach ($this->columna_excel as $ce => $selectedColumn) {
        $matches[$ce] = $selectedColumn;
    }
    
    
    $alternadoArray_excel = array_flip($matches);  
    $alternadoArray_base = array_flip($this->columnas_base);  
    
    $mergedArray = [];

    foreach ($alternadoArray_base as $titulo => $valor) {
        // Verificar si el título está presente en $alternadoArray_excel
        if (array_key_exists($titulo, $alternadoArray_excel)) {
            // Si está presente, utilizar el valor de $alternadoArray_excel
            $mergedArray[$titulo] = $alternadoArray_excel[$titulo];
        } else {
            // Si no está presente, utilizar 0
            $mergedArray[$titulo] = 0;
        }
    }
    
    $this->columna = $mergedArray;
    
    $this->ValidateProducts($this->columna);
    // $mergedArray contendrá el resultado deseado
    }

  
  public function GetMatchColumnasOld(){

    $this->validate([
      'fileProducts' => 'required|mimes:xlsx,xls'
    ]);

    $import = new ProductsValidationImport();
    $this->array_excel = Excel::toArray($import, $this->fileProducts);

    $this->dataForView = array_slice($this->array_excel[0], 0, 10);
    //dd($this->dataForView);
    
    $this->columnas_base =  $this->GetNombreTitulos();
    
    foreach ($this->array_excel[0] as $row) {

    // primero valida los titulos del excel 
    $titulos = array_keys($row); 
    }
    
    array_map('strtolower', $this->columnas_excel);
    array_map('strtolower', $this->columnas_base);
    
    $this->columnas_excel = $titulos;
    foreach($this->columnas_excel as $ce){
        $this->columna_excel[$ce] = in_array($ce, $this->columnas_base) ? $ce : 0;
    }
    
  }

public function verificarValoresDuplicados($columna_excel) {
    $contador = [];
    $alertas = [];
    
    // Recorres el array $columna_excel
    foreach ($columna_excel as $columna => $valor) {
        
        if($valor != 0){
        // Si el valor ya existe en el contador, significa que es un valor duplicado
        if (in_array($valor, $contador)) {
            $alertas[] = $valor;
        } else {
            // Si el valor no existe en el contador, lo agregas
            $contador[] = $valor;
        }
        }
        
    }
    
    return $alertas;
}
    



  public function GetNombreTitulos(){

     $this->casa_central_id = $this->comercio_id;
     
      $SS = sucursales::join('users','users.id','sucursales.sucursal_id')
      ->where('casa_central_id', $this->casa_central_id)->where('eliminado',0)->get();

      $LP = lista_precios::where('comercio_id', $this->casa_central_id)->get();

      $LD = listas_descuentos::where('comercio_id', $this->casa_central_id)->get();
      
      $header = ["nombre","tipo_producto","variacion","codigo","cod_variacion","costo","porcentaje_utilidad_precio","precio","porcentaje_utilidad_precio_interno","precio_interno"];

      $i = 10;
      foreach($LP as $lp) {
        $lista = strtolower($lp->nombre);
        $lista = str_replace(' ', '_', $lista);
        $header[$i++] = $lp->id."_precio_".$lista;
    //    $header[$i++] = $lp->id."_regla_precio_".$lista;
        $header[$i++] = $lp->id."_porcentaje_utilidad_precio_".$lista;
     }
     
      foreach($LD as $ld) {
            $descuento = strtolower($ld->nro_descuento);
            $descuento = str_replace(' ', '_', $descuento);
            $header[$i++] = $ld->id."_descuento_costo_".$descuento;
         }
      
      $header[$i++] = "iva";
      
      foreach($SS as $ss) {
        $sucursal = strtolower($ss->name);
        $sucursal = str_replace(' ', '_', $sucursal);
        $header[$i++] =  $ss->id."_iva_".$sucursal;
      }
      
      $header[$i++] = "stock";

      $header[$i++] = "almacen";

      foreach($SS as $ss) {
        $sucursal = strtolower($ss->name);
        $sucursal = str_replace(' ', '_', $sucursal);
        $header[$i++] =  $ss->id."_stock_".$sucursal;
      }
      
      foreach($SS as $ss) {
        $sucursal = strtolower($ss->name);
        $sucursal = str_replace(' ', '_', $sucursal);
        $header[$i++] =  $ss->id."_almacen_".$sucursal;
      }

      array_push($header, "inv_minimo","maneja_stock","categoria","subcategoria","proveedor","etiqueta","origen","venta_mostrador","venta_ecommerce","venta_wocommerce","imagen","etiquetas");
     
      // Nuevos 
      array_push($header, "marca"); // 8-8-2024
      
      // Nuevos 
      array_push($header, "cantidad_por_unidad","unidad_de_medida","codigo_proveedor","es_insumo"); // 29-8-2024
      
      
    //  array_push($header, "regla_precio","margen_regla_precio","regla_precio_interno","margen_regla_precio_interno","marca","pesable"); // 1-7-2024
      
    //  dd($header);
      
     return $header;
  }
  
    
  public function ValidateProducts($columna)
  {
    
    $comercio_id = $this->GetComercioId();
    
    if($columna['codigo'] == "0"){
        $this->emit("msg-error","Es necesario asignar el 'codigo' del producto");
        return; // Sale del bucle foreach
    }
    
    if(in_array("cod_variacion", $columna) || in_array("variacion", $columna)){

        if ($columna['cod_variacion'] == "0" && $columna['variacion'] != "0") {
            $this->emit("msg-error","Si se importa la columna 'variacion', debe importarse la columna 'cod variacion'");
            return; // Sale del bucle foreach
        }   
        if ($columna['cod_variacion'] != "0" && $columna['variacion'] == "0") {
            $this->emit("msg-error","Si se importa la columna 'cod variacion', debe importarse la columna 'variacion'");
            return; // Sale del bucle foreach
        }
    }

    $i = 1;
    $fila_error = [];
    $validacion_error = [];

    $this->validacion_errores = $validacion_error;
    $this->fila_error = $fila_error;

    $this->saveFile();

//    if ($this->validacion_errores == []) {
//      $this->getDataImport();
//    } else {
//      $this->estadoImportacion = 1;
//    }
  }
 
    public function ValidateTitulos($row)
  {
    $titulos = array_keys($row); 
    
    $error_titulos = [];

    // titulos para 4
    
    if($this->accion == 4 ){
   
    if (!in_array("costo", $titulos)) {
        $return = "La columna 'costo' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    
    if (!in_array("codigo", $titulos)) {
        $return = "La columna 'codigo' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 

    if (!in_array("cantidad", $titulos)) {
        $return = "La columna 'cantidad' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    
    
    }
    return $error_titulos;
  }
  
  /*
  public function ValidateTitulosOld($row)
  {
    $titulos = array_keys($row); 
    
    $titulos_correcto = $this-> GetNombreTitulos();
    
    $diferencias = array_diff($titulos_correcto, $titulos);

    $error_titulos = [];
    
    // titulos para todos

    if (!in_array("codigo", $titulos)) {
        $return = "La columna 'codigo' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    
    if (!in_array("cod_variacion", $titulos)) {
        $return = "La columna 'cod variacion' tiene que estar presente en el titulo, aun que no tuviera datos.";
        array_push($error_titulos, $return);
    } 
    
    // titulos para 1 y 2
    
    if($this->accion == 1 || $this->accion == 2 ){

    if ((isset($this->acciones_2) && $this->acciones_2[0] == 1 && $this->accion == 2) || $this->accion == 1) {
    if (!in_array("costo", $titulos)) {
        $return = "La columna 'costo' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    }
    
    if ((isset($this->acciones_2) && $this->acciones_2[2] == 1 && $this->accion == 2) || $this->accion == 1) {
    if (!in_array("precio", $titulos)) {
        $return = "La columna 'precio' no está presente en el excel.";
        array_push($error_titulos, $return);
    }
    } 
    
    if ((isset($this->acciones_2) && $this->acciones_2[1] == 1 && $this->accion == 2) || $this->accion == 1) {
    if($this->configuracion_precios != 1){
    if (!in_array("precio_interno", $titulos)) {
        $return = "La columna 'precio interno' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    }
    }
    
    // aca tengo que hacer el validador para cuando tiene listas de precios
    if ((isset($this->acciones_2) && $this->acciones_2[3] == 1 && $this->accion == 2) || $this->accion == 1) {
    foreach($this->lista_precios as $lp){
    if (!in_array($lp->id."_precio", $titulos)) {
        $return = "La columna ".$lp->id."_precio no está presente en el excel.";
        array_push($error_titulos, $return);
    }
    } 
        
    }
    
    
    }
    
    // titulos para 1 y 3
    
    if($this->accion == 1 || $this->accion == 3 ){
    if (!in_array("stock", $titulos)) {
        $return = "La columna 'stock' no está presente en el excel.";
        array_push($error_titulos, $return);
    }
    if (!in_array("almacen", $titulos)) {
        $return = "La columna 'almacen' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    
    }
    
    // titulos para 1
    
     if($this->accion == 1) {

    if (!in_array("tipo_producto", $titulos)) {
        $return = "La columna 'tipo producto' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    
    if (!in_array("nombre", $titulos)) {
        $return = "La columna 'nombre' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 

    
    if (!in_array("variacion", $titulos)) {
        $return = "La columna 'variacion' tiene que estar presente en el titulo, aun que no tuviera datos.";
        array_push($error_titulos, $return);
    } 
    
        
    if (!in_array("inv_minimo", $titulos)) {
        $return = "La columna 'inv minimo' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    if (!in_array("maneja_stock", $titulos)) {
        $return = "La columna 'maneja stock' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    
    if (!in_array("categoria", $titulos)) {
        $return = "La columna 'categoria' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    
    if (!in_array("proveedor", $titulos)) {
        $return = "La columna 'proveedor' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    
    
    if (!in_array("origen", $titulos)) {
        $return = "La columna 'origen' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    
    if (!in_array("venta_mostrador", $titulos)) {
        $return = "La columna 'venta mostrador' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    
    if (!in_array("venta_ecommerce", $titulos)) {
        $return = "La columna 'venta ecommerce' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    if (!in_array("venta_wocommerce", $titulos)) {
        $return = "La columna 'venta wocommerce' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    if (!in_array("imagen", $titulos)) {
        $return = "La columna 'imagen' no está presente en el excel.";
        array_push($error_titulos, $return);
    }
    if (!in_array("etiquetas", $titulos)) {
        $return = "La columna 'etiquetas' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    
     }

    // titulos para 4
    
    if($this->accion == 4 ){
   
    if (!in_array("costo", $titulos)) {
        $return = "La columna 'costo' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    
    if (!in_array("codigo", $titulos)) {
        $return = "La columna 'codigo' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 

    if (!in_array("cantidad", $titulos)) {
        $return = "La columna 'cantidad' no está presente en el excel.";
        array_push($error_titulos, $return);
    } 
    
    
    }
    return $error_titulos;
  }
  */
  
  public function ValidateSucursales($row,$sucursales)
  {
      
      
      $titulos = array_keys($row);
      
      // Identificar títulos con números
        $titulos_con_numeros = array_filter($titulos, function ($titulo) {
            return preg_match('/\d/', $titulo); // Verificar si el título contiene al menos un número
        });
      
      // Encontrar títulos que siguen el patrón "stock_sucursal" después del número inicial
        $titulos_con_patron = array_filter($titulos_con_numeros, function ($titulo) {
            return preg_match('/^\d+_stock/', $titulo); // Verificar el patrón
        });
      
      // Obtener los números antes del primer guion bajo
        $numeros_antes_del_guion = array_map(function ($titulo) {
            preg_match('/^(\d+)_/', $titulo, $matches); // Buscar el número antes del guion bajo
            return $matches[1]; // Obtener el número encontrado
        }, $titulos_con_patron);

        $sucursales_id = [];
        foreach($sucursales as $s) {
            array_push($sucursales_id,$s->sucursal_id);
        }
        
        // Encontrar diferencias entre los dos arrays
        $diferencias = array_diff($numeros_antes_del_guion, $sucursales_id);
        
        return $diferencias;

          
  }
  public function DeleteFile()
  {
     if (file_exists($this->filePath)) {
      unlink($this->filePath);
      $this->validacion_errores = [];
      $this->estadoImportacion = 0;
      $this->columna_excel = [];
      $this->columnas_excel = [];
    }
  }


  public function import()
  {
    $this->getDataImport();
  }


  public function saveFile()
  {
    if ($this->validacion_errores != []) {
      $this->saltear_errores = implode(",", $this->fila_error);
    }

    if (Auth::user()->comercio_id != 1) {
      $this->comercio_id = Auth::user()->comercio_id;
    } else {
      $this->comercio_id = Auth::user()->id;
    }

    $this->tipo_usuario = User::find($this->comercio_id);

    $this->sucursal_id = $this->comercio_id;

    if ($this->tipo_usuario->sucursal != 1) {
      $this->casa_central_id = $this->comercio_id;
    } else {
      $this->casa_central = sucursales::where('sucursal_id', $this->comercio_id)->first();
      $this->casa_central_id = $this->casa_central->casa_central_id;
    }

    $path = $this->fileProducts->getRealPath();

    $data = file($path);

    $this->nombre_archivo = 'Import_' . $this->comercio_id . '_' . str_replace(':', '_', (Carbon::now()->format('d_m_Y_H:i:s')));


    $this->filePath = base_path('resources/excel-pendientes/' . $this->nombre_archivo . '.xlsx');


    file_put_contents($this->filePath, $data);
    
    $nombre_tipo = $this->SetTipo($this->accion);
    
    $this->registerImportacion = importaciones::create([
      'user_id' => $this->comercio_id,
      'comercio_id' => $this->comercio_id,
      'tipo' => $nombre_tipo,
      'saltear_errores' => 0,
      'nombre' => $this->nombre_archivo,
      'errores' => null,
      'estado' => 0,
      'proceso' =>  null
    ]);    

    ValidateImportProductsJob::dispatch($this->registerImportacion->id,$this->filePath, $this->comercio_id, $this->columna, $this->nombre_archivo);
    
    $this->progress = 0;
    $this->estadoImportacion = 2;
    
    $this->checkProgressValidacion();
    
    //

  }


  
  public function getDataImport()
  {
    $path =  $this->filePath;
    
    //dd($path);
    
    $file = array_slice(glob($path), 0, 2)[0];

    $currentLocation = $file;

    $file_array = explode("/", $file);

    $file_archive = $file_array[2];

    $file_name = explode(".", $file_archive);

    $comercio_id = $this->comercio_id;

    $headings = (new HeadingRowImport)->toArray($file);

    $import = new ProductsImport();

    $rows = (Excel::toArray($import, $file))[0];

    $indexesToDelete = array_map(function ($index) {
      return $index - 2;
    }, $this->fila_error);

    $rows = array_map(function ($key, $row) use ($indexesToDelete) {
      return in_array($key, $indexesToDelete) ? null : $row;
    }, array_keys($rows), $rows);

    $rows = array_values(array_filter($rows));

    $this->rowsImport =  $rows;

    $this->totalRows = count($rows);

    $this->progress = 0;

    $this->estadoImportacion = 2;
    
    $nombre_tipo = $this->SetTipo($this->accion);
    
    $this->registerImportacion = importaciones::create([
      'user_id' => $this->comercio_id,
      'comercio_id' => $this->comercio_id,
      'tipo' => $nombre_tipo,
      'estado' => 0,
      'nombre' => $this->nombre_archivo,
      'saltear_errores' => 0,
      'errores' => null,
      'proceso' =>  "0/" . $this->totalRows . "/procesando",
    ]);

    if($this->accion == 4){
    $response = $this->ImportarCompra($rows,$this->totalRows,$this->comercio_id);
    if($response == 1){
    importaciones::where('id', $this->registerImportacion->id)->update([
      'proceso' => $this->totalRows . "/" . $this->totalRows . "/procesando",
      'estado' => 2
    ]);
    
    }
    } 
    

    $this->estadoImportacion = 2;
    
    ValidateImportProductsJob::dispatch($this->registerImportacion->id, $this->rowsImport, $this->comercio_id, $this->filePath, $this->nombre_archivo, $this->accion,$this->acciones_2,$this->columna,$this->comercio_id);
    $this->checkProgressValidacion();

    //$this->tipo_procesando = 2;
    //ImportJob::dispatch($this->registerImportacion->id, $this->rowsImport, $this->comercio_id, $this->filePath, $this->nombre_archivo, $this->accion,$this->acciones_2,$this->columna);
    //$this->checkProgress();

    
    
 
  }

  protected $listeners = [
    'getDataImport' => 'getDataImport',
    'comenzarImportacion' => 'procesarImportacion',
    'prosesarImportacion' => 'procesarImportacion',
    'checkProgress' => 'checkProgress',
    'checkProgressValidacion' => 'checkProgressValidacion',
    'checkValidacion' => 'checkValidacion',
    'checkLastImport' => 'checkLastImport',
    'checkImportacion' => 'checkImportacion',
    'checkLastImportCompleted' =>  'checkLastImportCompleted'
  ];


  // 14-8-2024
  public function checkImportacion($import_id){
    $importacion = importaciones::find($import_id);
    //dd($importacion);
    if ($importacion->estado == 2) {
        $this->respuesta = "success";
    }
    
    if ($importacion->estado == 3) {
        
    // Configurar los detalles del correo
    $title = "Error de importación";
    $created_at = Carbon::now()->format("d/m/Y H:i");
    $user = User::find($importacion->comercio_id); // Encontrar el usuario correspondiente al comercio

    // Email destinatario (puedes cambiar este email por el deseado)
    $email = "andrespasquetta@gmail.com";
    $e = $importacion->errores_bug;
    
    // Enviar el correo electrónico
    Mail::send('emails.errores', compact('title', 'user', 'created_at', 'e'), function ($message) use ($email, $title) {
        $message->to($email)
                ->subject($title);
    });

    $this->respuesta = "error";
    }


  }
  public function checkValidacion($import_id){
      $impotaciones = importaciones::find($import_id);
      $errores = $impotaciones->errores;
      
      if($errores == [] || $errores == null){
          $this->tipo_procesando = 2;
          $this->estadoImportacion = 2;
           
          ImportJob::dispatch($this->registerImportacion->id, $this->rowsImport, $this->comercio_id, $this->filePath, $this->nombre_archivo, $this->accion,$this->acciones_2,$this->columna);
          $this->checkProgress();
    
      } else {
        // Aca cuando tiene errores tenemos que enviar por url que hay error, con el id de la importacion
        $this->estadoImportacion = 1;
        $this->validacion_errores = json_decode($errores);       
      }
      
  }
  
  
  public function accionEvento()
  {
    $this->progressTest = session('progressbar');
  }

  public function CheckLastImportCompleted(){
    $lastImportation = importaciones::where('user_id', Auth::user()->id)
      ->latest('created_at')
      ->first();      
      $this->checkImportacion($lastImportation->id);
  }
  
  public function checkLastImport()
  {
    // esto hay que chequear ---> aca es 
    $lastImportation = importaciones::where('user_id', Auth::user()->id)
      ->latest('created_at')
      ->first();
    
    
    if ($lastImportation !== null && $lastImportation->estado === 2) {
     
        $proceso = explode('/', $lastImportation->proceso);
        $filaActual = $proceso[0];
        $filaTotales = $proceso[1];
    
        if($filaActual < $filaTotales){
            $this->registerImportacion = $lastImportation;
            $this->accion = 1;
            $this->estadoImportacion = 2;
            $this->tipo_procesando = 2;
            $this->checkProgress();            
        }
    }
    
    if ($lastImportation !== null && $lastImportation->estado === 1) {
        
        $proceso = explode('/', $lastImportation->proceso_validacion);
        $filaActual = $proceso[0];
        $filaTotales = $proceso[1];
    
        if($filaActual < $filaTotales){
            $this->registerImportacion = $lastImportation;
            $this->accion = 1;
            $this->estadoImportacion = 2;
            $this->tipo_procesando = 1;
            $this->checkProgressValidacion();   
        }
      }
     
      
      
    }
  

  public function checkProgressValidacion()
  {

ini_set('max_execution_time', '300');
ini_set('memory_limit', '512M');

   $importacionActual =  importaciones::find($this->registerImportacion->id);

    if ($importacionActual !== null && $importacionActual->proceso_validacion !== null) {
      $importacionActual->proceso_validacion;
      $proceso = explode('/', $importacionActual->proceso_validacion);
      $filaActual = $proceso[0];
      $filaTotales = $proceso[1];
      $progresoActual = ($filaActual * 100)  / $filaTotales;
      $errores = json_decode($importacionActual->errores);
    } else {
      $filaActual = 0;
      $filaTotales = 1;
      $progresoActual = 0;
      $errores = [];
    }
    
    $this->fila_procesadas = $filaActual;
    $this->fila_totales = $filaTotales;

    $this->emit('estatus-proceso-validacion',  $filaActual, ($filaTotales - 1), $importacionActual->id);
    $this->emit('progressUpdated', $progresoActual);

  }
  
  public function checkProgress()
  {
    $importacionActual =  importaciones::find($this->registerImportacion->id);
    if ($importacionActual !== null && $importacionActual->proceso !== null) {
      $importacionActual->proceso;
      $proceso = explode('/', $importacionActual->proceso);
      $filaActual = $proceso[0];
      $filaTotales = $proceso[1];
      $progresoActual = ($filaActual * 100)  / $filaTotales;
    } else {
      $filaActual = 0;
      $filaTotales = 1;
      $progresoActual = 0;
    }
    
    $this->fila_procesadas = $filaActual;
    $this->fila_totales = $filaTotales;

    $this->emit('estatus-proceso-importacion',  $filaActual, ($filaTotales - 1), $importacionActual->id); // 14-8-2024
    $this->emit('progressUpdated', $progresoActual);

    if ($importacionActual->estado  === 2 && $filaActual == $filaTotales) {
      $this->estadoImportacion = 3;
      if($this->accion == 4){
      return redirect('/compras');
      }
      return redirect()->back();
    }
  }
  
  public function Paso1(){
    $this->nro_paso = 1;
    
    $this->paso1 = "display:block;";
    $this->paso2 = "display:none;";
    $this->paso3 = "display:none;";
    
    $this->logo_paso1 = "background-color: rgba(81,86,190,.2); color: #5156be; border-color: rgba(81,86,190,.2);";
    $this->logo_paso2 = "";
    $this->logo_paso3 = "";
}

public function Paso2(){
    
        
    $this->paso2 = "display:block;";
    $this->paso1 = "display:none;";
    $this->paso3 = "display:none;";
    
    $this->logo_paso2= "background-color: rgba(81,86,190,.2); color: #5156be; border-color: rgba(81,86,190,.2);";
    $this->logo_paso1 = "";
    $this->logo_paso3 = "";
    
    $this->nro_paso = 2;
}


public function Paso3(){
    $this->nro_paso = 3;
    
    $this->paso3 = "display:block;";
    $this->paso2 = "display:none;";
    $this->paso1 = "display:none;";
    
    $this->logo_paso3 = "background-color: rgba(81,86,190,.2); color: #5156be; border-color: rgba(81,86,190,.2);";
    $this->logo_paso2 = "";
    $this->logo_paso1 = "";
}

    public function ExportarEjemplo() {


    return redirect('reporte-productos-ejemplo/excel/'. $this->comercio_id . '/Catalogo_ejemplo_' . Carbon::now()->format('d_m_Y_H_i_s').'.xlsx');

}

public function SetAccion($value){
    $this->accion = $value;
    // 1-3-2024
    if($value == 2){
        $this->importar_costos = 1;
        $this->importar_precio_interno = 1;
        $this->importar_precio = 1;
        $this->importar_listas_precios = 1;
    }
    if($value == 0){$this->DeleteFile();}
}

public function AbrirAyuda($value){
    $this->lista_precios = lista_precios::where('lista_precios.comercio_id',$this->comercio_id)->where('lista_precios.eliminado',0)->get();
    $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->where('sucursales.casa_central_id',$this->comercio_id)->where('sucursales.eliminado',0)->get();
    $this->ayuda = $value;
    $this->emit("show-modal","");
}

public function CerrarAyuda(){
    $this->emit("hide-modal","");
}


public function ImportarCompra($rows,$total_rows,$comercio_id){
        
        $casa_central_id = Auth::user()->casa_central_user_id;
        $user = User::find($comercio_id);
        $this->costo_igual_precio = $user->costo_igual_precio;
        
        $x = 0;
        foreach($rows as $row) {
        $product = Product::where('barcode', $row['codigo'])->where('comercio_id', $casa_central_id)->where('eliminado', 0)->first();
        if($product) {
        
        $costo = $row['costo'];
        if(empty($costo)){$costo = $product->cost;}
        
        $cantidad = $row['cantidad'];
        if(empty($cantidad)){$cantidad = 0;}
        
        $datos_variables = $this->SetDatos($product,$row['cod_variacion'],$casa_central_id,$row['costo']);
        
        $referencia_variacion = $datos_variables['referencia_variacion'];
        $nombre_variacion = $datos_variables['nombre'];
        
        // aca tenemos que buscar como esta configurado y accionar
        if($this->actualizar_costos_4 == true){
        $this->ActualizarCosto($product,$referencia_variacion,$costo);
        if($this->costo_igual_precio == true){
        $this->ActualizarPrecioInterno($product,$referencia_variacion,$costo);
        }
        }
    
        $nombre_producto = $this->SetNombreProducto($product,$nombre_variacion);
        
        $codigo_compuesto = $product->id.'-'.$referencia_variacion;
        
        $cart = new Cart;
        $items = $cart->getContent();
        
        if ($items->contains('codigo_compuesto', $codigo_compuesto)) {
        
           $maxIndex = 0;
           foreach ($items as $i) {
                if ($i['codigo_compuesto'] === $codigo_compuesto) {
                    $maxIndex = max($maxIndex, $i['index']);
                    // Resto del código...
                }
            }
            
            $index = $maxIndex + 1;
    
        } else {
            $index = 1;
        }
    
              $array = array(
                  "id" => $codigo_compuesto."-".$index,
                  "index" => $index,
                  "codigo_compuesto" => $codigo_compuesto,
                  "barcode" => $product->barcode,
                  "name" => $nombre_producto,
                  "product_id" => $product->id,
                  "referencia_variacion" => $referencia_variacion,
                  "price" => 0,
                  "descuento" => 0,
                  "iva" => 0,
                  "cost" => $costo,
                  "qty" => $cantidad,
                  "orderby_id"=> $x++
              );
            
            //dd($array);
            $cart->addProduct($array);   
           
        }
        }
    
        if($x == $total_rows){
        
        return 1;    
        }
    
    }

public function SetDatos($product,$codigo_variacion,$comercio_id,$costo){
    $referencia_id = 0;    
    $nombre = "";
    
    // producto variable
    if($product->producto_tipo == "v") {
    $pvd_datos = productos_variaciones_datos::where('codigo_variacion', $codigo_variacion)->where('product_id', $product->id)->where('comercio_id', $comercio_id)->where('eliminado', 0)->first();
    $referencia_id = $pvd_datos->referencia_variacion;
    $nombre = $pvd_datos->variaciones;
    if($this->actualizar_costos_4 == true) {
        if($this->configuracion_precios == 1){$pvd_datos->precio_interno = $costo;}
        $pvd_datos->cost = $costo;
        $pvd_datos->save();
    }   
    } else {
    if($this->actualizar_costos_4 == true) {
        if($this->configuracion_precios == 1){$product->precio_interno = $costo;}
        $product->cost = $costo;
        $product->save();
    }    
    }
    
    $array = ['nombre' => $nombre, 'referencia_variacion' => $referencia_id];

    return $array;
        
    }
    
public function SetTipo($accion){
    if($accion == 1){ return "importar_catalogo";}
    if($accion == 2){ return "actualizacion_precios";}
    if($accion == 3){ return "actualizacion_stock";}
    if($accion == 4){ return "importar_compra";}
}

public function SetNombreProducto($product,$nombre_variacion){

    if($product->producto_tipo == "v") {
    $nombre_producto = $product->name." - ".$nombre_variacion;    
    } else {
    $nombre_producto = $product->name;    
    }
    
    return $nombre_producto;
}

    public function GetConfiguracionPrecios($comercio_id){
    $u = User::find($comercio_id);
    $configuracion_precio_interno = $u->costo_igual_precio;
    return $configuracion_precio_interno;
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
    
    
    public function ActualizarCosto($product,$referencia_variacion,$costo){

	   $p = Product::find($product->id);
	   
	   if($p->producto_tipo == "s"){
	   $p->cost = $costo;
	   $p->save();
	   } else {
	   $pvd = productos_variaciones_datos::where('product_id',$product->id)
	   ->where('referencia_variacion',$referencia_variacion)
	   ->where('comercio_id',$this->comercio_id)
	   ->where('eliminado',0)->first();
	   $pvd->cost = $costo;
	   $pvd->save();
	   
	   }
	   
}

public function ActualizarPrecioInterno($product,$referencia_variacion,$costo){
    	// actualizar costo
	    
	   $p = Product::find($product->id);
	   
	   if($p->producto_tipo == "s"){
	   $p->precio_interno = $costo;
	   $p->save();
	   } else {
	   $pvd = productos_variaciones_datos::where('product_id',$product->id)
	   ->where('referencia_variacion',$referencia_variacion)
	   ->where('comercio_id',$this->comercio_id)
	   ->where('eliminado',0)->first();
	   
	   
	   $pvd->precio_interno = $costo;
	   $pvd->save();
	   
	   }
	   
	
}

public function CancelarMatchColumnas(){
 return redirect('/import');
}

public function GetComercioId(){
    
    if (Auth::user()->comercio_id != 1) {
      $comercio_id = Auth::user()->comercio_id;
      $this->comercio_id = Auth::user()->comercio_id;
    } else {
      $comercio_id = Auth::user()->id;
      $this->comercio_id = Auth::user()->comercio_id;
    }

    $this->tipo_usuario = User::find($comercio_id);

    if ($this->tipo_usuario->sucursal != 1) {
      $this->casa_central_id = $comercio_id;
    } else {

      $this->casa_central = sucursales::where('sucursal_id', $comercio_id)->first();
      $this->casa_central_id = $this->casa_central->casa_central_id;
    }
    
    return $comercio_id;
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
        $error = 'FILA NRO ' . $i . ' -> El valor de pesable debe ser si o no';
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

        // Expresión regular para validar los formatos permitidos
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
        $error = 'FILA NRO ' . $i . ' -> El stock debe ser un numero.';
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
        // Expresión regular para validar los formatos permitidos
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
