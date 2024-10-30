<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\importaciones;
use App\Models\Category;
use App\Models\Product;
use App\Models\sucursales;
use App\Models\User;
use App\Models\productos_ivas;
use App\Models\datos_facturacion;

use App\Models\etiquetas;
use App\Models\unidad_medida;
use App\Models\etiquetas_relacion;
use App\Models\historico_stock;
use App\Models\imagenes;
use App\Models\proveedores;
use App\Models\productos_variaciones;
use App\Models\productos_variaciones_datos;
use App\Models\variaciones;
use App\Models\productos_stock_sucursales;
use App\Models\productos_lista_precios;
use App\Models\seccionalmacen;
use App\Models\marcas; // Marcas


use App\Imports\ProductsImport;


use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ImportJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $import_id, $data, $comercio_id_import, $filePath, $fileName, $accion,$acciones_2,$columna;

  public $productos_lista_precios,$regla,$comercio_id, $sd1, $sd2, $cod_vari, $referencia_id, $prov, $proveed, $alm, $al, $categ, $movimiento_stock, $cat, $costo, $stock, $st, $cost, $inv_minimo, $in_minimo, $inv_ideal, $in_ideal, $prec, $pre, $maneja_stock;

  public $regla_precios_lista;

  public $tries = 1;

  public $timeout = 60000;
  
  private $rowsCache = null;

  protected $datosImportar;
  protected $product_exist;

  /**
   * Create a new job instance.
   *
   * @return void
   */
  /* public function __construct()
    {
        
    }*/

  public function __construct($importId, $data, $comercioId, $filePath, $fileName, $accion,$acciones_2,$columna)
  {
    $this->import_id = $importId;
    $this->data = $data;
    $this->comercio_id_import = $comercioId;
    $this->filePath = $filePath;
    $this->fileName = $fileName;
    $this->accion = $accion;
    $this->acciones_2 = $acciones_2;
    $this->columna = $columna;
    
  }


  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
 //   $filasTotales = $this->data;
    $comercio_id = $this->comercio_id_import;
    $accion = $this->accion;
    $columna = $this->columna;
    $fileName = $this->fileName;
    $importId = $this->import_id;
    $filePath = $this->filePath;
    
    try {

    $filasTotales = $this->getDataImportJob($this->filePath,$this->import_id);
    
    importaciones::where('id', $importId)->update([
       'proceso_validacion' => count($filasTotales) . "/" . count($filasTotales) . "/procesando",
    ]);
    
    $imp = importaciones::where('id', $importId)->first();
    $comercio_id = $imp->comercio_id;
    
    if($imp->terminado == 0){
        
      for ($i = 0; $i < count($filasTotales) ; $i++) {
    
       // Determinamos si el producto debe ser creado o actualizado
       $columna_codigo = $columna['codigo'];
       var_dump($filasTotales[$i][$columna_codigo]);
       
       $exists = Product::where('barcode', $filasTotales[$i][$columna_codigo])->where('comercio_id', $comercio_id)->where('eliminado', 0)->first();
       
       $existe_producto_validacion = Product::where('barcode', $filasTotales[$i][$columna_codigo])->where('comercio_id', $comercio_id)->where('eliminado', 0)->exists();

       if(isset($filasTotales[$i]['precio_interno'])) { $precio_interno = $filasTotales[$i]['precio_interno']; } else {$precio_interno = 0;}

       if($this->accion == 1){

        //$configuracion_precio_interno = $this->GetConfiguracionPrecios($comercio_id); Ver esto ---
        $configuracion_precio_interno = 0;
        $id_almacen         = $this->almacen($filasTotales[$i], $comercio_id,$exists,$columna); //////////// ALMACEN //////////////
        
        $barcode = $filasTotales[$i][$columna_codigo];
        $array = ['barcode' => $barcode,'comercio_id' => $comercio_id, 'eliminado' => 0, 'price' => 0 , 'stock' => 0];
        
        $array       = $this->nombre($filasTotales[$i], $comercio_id,$exists,$columna,$array,$barcode); //////////// NOMBRE //////////////
        $array       = $this->alerts($filasTotales[$i], $comercio_id,$exists,$columna,$array); //////////// INVENTARIO MINIMO //////////////
        $array       = $this->cost($filasTotales[$i], $comercio_id,$exists,$columna,$array); //////////// COSTO //////////////
        $array       = $this->proveedores($filasTotales[$i], $comercio_id,$exists,$columna,$array); //////////// PROVEEDOR //////////////
        $array       = $this->categorias($filasTotales[$i], $comercio_id,$exists,$columna,$array); //////////// CATEGORIA //////////////
        $array       = $this->manejaStock($filasTotales[$i],$comercio_id,$exists,$columna,$array); //////////// STOCK DESCUBIERTO //////////////
        $array       = $this->origen($filasTotales[$i],$comercio_id,$exists,$columna,$array); //////////// ORIGEN //////////////
        $array      = $this->ventaMostrador($filasTotales[$i],$comercio_id,$exists,$columna,$array); //////////// VENTA MOSTRADOR //////////////
        $array    = $this->ventaEcommerce($filasTotales[$i],$comercio_id,$exists,$columna,$array); //////////// VENTA ECOMMERCE //////////////
        $array   = $this->ventaWocommerce($filasTotales[$i],$comercio_id,$exists,$columna,$array); //////////// VENTA WOOCOMMERCE ////////////// 
        $array             = $this->imagen($filasTotales[$i],$comercio_id,$exists,$columna,$array); //////////// IMAGENES //////////////
        $array      = $this->ProductoTipo($filasTotales[$i],$comercio_id,$exists,$columna,$array);
        
        $array       = $this->pesable($filasTotales[$i],$comercio_id,$exists,$columna,$array); //////////// PESABLE //////////////
        $array       = $this->marcas($filasTotales[$i], $comercio_id,$exists,$columna,$array); //////////// MARCA //////////////
        
        // Faltan "regla_precio","margen_regla_precio",
        // Faltan "regla_precio_interno","margen_regla_precio_interno"
        
        
        //"marca","pesable" ---> faltan agregar abajo

        if ($existe_producto_validacion)
        {
          $this->product_exist = 1;
          $product = Product::where('barcode', $filasTotales[$i][$columna_codigo])->where('comercio_id', $comercio_id)->where('eliminado', 0)->first();

          if($product != null) {          
            if($array['producto_tipo'] == "s"){
            $array  = $this->PrecioInternoArray($filasTotales[$i],$configuracion_precio_interno,$comercio_id,$exists,$columna,$array,$product);    
            }
        
          Log::info('Datos del producto a actualizar:', $array);
          $product->update($array);
          $this->etiquetas($filasTotales[$i],$columna,$product,$comercio_id);
          }
          
        } else {
          $this->product_exist = 0;
          $product = Product::where('barcode', $filasTotales[$i][$columna_codigo])->where('comercio_id', $comercio_id)->where('eliminado', 0)->first();
          
            if($array['producto_tipo'] == "s"){
            $array  = $this->PrecioInternoArray($filasTotales[$i],$configuracion_precio_interno,$comercio_id,$exists,$columna,$array,$product);    
            }
          
          
          Log::info('Datos del producto a crear:', $array);
          
          $product = Product::create($array);
          
          $this->etiquetas($filasTotales[$i],$columna,$product,$comercio_id);
          
        }
        
        if($product != null){
        
        $this->SetearReferenciaVariacion($filasTotales[$i],$i,$product,$comercio_id,$columna);

        $this->FuncionesVariaciones($filasTotales[$i],$i,$product,$comercio_id,$columna,$exists,$array,$configuracion_precio_interno);

        $this->StoreUpdateStock($filasTotales[$i],$i,$product,$comercio_id,$id_almacen,$columna,$exists);

        $this->StoreUpdatePrecios($filasTotales[$i],$i,$product,$comercio_id,$columna,$exists);
        
        $this->StoreIVAProducto($filasTotales[$i],$i,$product,$comercio_id,$id_almacen,$columna,$exists);

            
        // Obtenemos las sucursales
        $sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
        ->select('users.name','users.id')
        ->where('sucursales.casa_central_id',$comercio_id)
        ->get();
        

        foreach ($columna as $columna_base_datos => $columna_excel) {
            if (preg_match('/^\d+_stock/', $columna_base_datos)) {
                $titulosConStock[$columna_base_datos] = $columna_excel;
                $this->StoreUpdateStockSucursales($filasTotales[$i], $columna_excel, $columna_base_datos, $product, $comercio_id, $id_almacen, $exists);
            }
        }
         
        foreach ($columna as $columna_base_datos => $columna_excel) {

            if (preg_match('/^\d+_precio_/', $columna_base_datos)) {
                $datos_col = explode("_", $columna_base_datos);
                $lista_id = $datos_col[0]; // Asegurarse de que $lista_id sea un entero
                $remaining_parts = array_slice($datos_col, 2);
                $nombre_lista = implode('_', $remaining_parts);
                $nombre_regla = $lista_id."_regla_precio_".$nombre_lista;
            
                $productos_lista_precios = $this->GetProductosListaPrecios($product, $this->referencia_id, $lista_id);
            //  $regla_precios_lista = $this->GetReglaPreciosLista($filasTotales[$i], $columna, $nombre_regla, $product, $comercio_id, $exists, $productos_lista_precios);
                $regla_precios_lista = 1;
                $this->StoreUpdatePreciosListas($filasTotales[$i], $columna, $columna_excel, $columna_base_datos, $product, $comercio_id, $exists, $regla_precios_lista,$lista_id,$nombre_lista,$productos_lista_precios);
            }
        }

        foreach ($columna as $columna_base_datos => $columna_excel) {
            if (preg_match('/^\d+_iva/', $columna_base_datos)) {
                $titulosConStock[$columna_base_datos] = $columna_excel;
                $this->StoreIvaProductoSucursales($filasTotales[$i], $columna_excel, $columna_base_datos, $product, $comercio_id, $id_almacen, $exists);
            }
        }
        
        // Seteo de almacen para casa central
        $nombre_columna_almacen = $columna['almacen'];
        $this->StoreUpdateAlmacenSucursales($filasTotales[$i],$nombre_columna_almacen,"almacen",$product,$comercio_id,$id_almacen,$exists);
        
        // Seteo de almacen para las sucursales
        foreach ($columna as $columna_base_datos => $columna_excel) {
            if (preg_match('/^\d+_almacen/', $columna_base_datos)) {
                $titulosConStock[$columna_base_datos] = $columna_excel;
             $this->StoreUpdateAlmacenSucursales($filasTotales[$i],$columna_excel,$columna_base_datos,$product,$comercio_id,$id_almacen,$exists);
            }
        }
        
        
        }

       }      
       
        //dump($i);
        importaciones::where('id', $this->import_id)->update([
          'proceso' => ($i + 1) . "/" . count($filasTotales) . "/procesando",
          'estado' => 2
        ]);
        
        if ($i === (count($filasTotales) - 1)) {
          $newLocation = base_path("resources/excel-guardados/" . $this->fileName . ".xlsx");
          if (file_exists($newLocation)) {
          $moved = rename($this->filePath, $newLocation);
          }

        importaciones::where('id', $this->import_id)->update([
          'terminado' => 1
        ]);
        }


      }
      
      
    }
    } catch (\Exception $e) {
      //Log::error('Error al procesar el trabajo: ' . $e->getMessage());
      \Log::error('Error al procesar el trabajo: ' . $e);
     // 14-8-2024
     $filasTotales = $this->getDataImportJob($this->filePath,$this->import_id);
     $this->EscribirErrores($this->import_id,$filasTotales,$e,$comercio_id);
    }
  }

    public function getDataImportJob($path, $importId)
    {
        // Verifica si ya se ha cargado anteriormente
        if ($this->rowsCache !== null) {
            return $this->rowsCache;
        }
    
        $this->filePath = $path;
        $rows = [];
    
        $fileList = glob($path);
        
        if (!empty($fileList)) {
    
        $file = array_slice($fileList, 0, 2)[0];
    
        $file_array = explode("/", $file);
        $file_archive = $file_array[2];
        $file_name = explode(".", $file_archive);
        $comercio_id = $this->comercio_id;
    
        $headings = (new HeadingRowImport)->toArray($file);
        $import = new ProductsImport();
        $rows = (Excel::toArray($import, $file))[0];
    
        $this->fila_error = [];
        $indexesToDelete = array_map(function ($index) {
            return $index - 2;
        }, $this->fila_error);
    
        $rows = array_map(function ($key, $row) use ($indexesToDelete) {
            return in_array($key, $indexesToDelete) ? null : $row;
        }, array_keys($rows), $rows);
    
        $rows = array_values(array_filter($rows));
        $this->totalRows = count($rows);
    
        importaciones::where('id', $importId)->update([
            'proceso_validacion' => 0 . "/" . $this->totalRows . "/procesando",
            'estado' => 1
        ]);   
        
        var_dump("1");
        // Guarda los datos en la cach¨¦
        $this->rowsCache = $rows;
        
        }
    
        return $rows;
    }

  public function getDataImportJobOld($path,$importId)
  {
    $rows = [];
    $this->filePath = $path;

    $fileList = glob($path);
    
    if (empty($fileList)) {
    $file = array_slice(glob($path), 0, 2)[0];

    $currentLocation = $file;

    $file_array = explode("/", $file);

    $file_archive = $file_array[2];

    $file_name = explode(".", $file_archive);

    $comercio_id = $this->comercio_id;

    $headings = (new HeadingRowImport)->toArray($file);

    $import = new ProductsImport();

    $rows = (Excel::toArray($import, $file))[0];
    $this->fila_error = [];
    
    $indexesToDelete = array_map(function ($index) {
      return $index - 2;
    }, $this->fila_error);

    $rows = array_map(function ($key, $row) use ($indexesToDelete) {
      return in_array($key, $indexesToDelete) ? null : $row;
    }, array_keys($rows), $rows);

    $rows = array_values(array_filter($rows));

    $this->totalRows = count($rows);

    importaciones::where('id', $importId)->update([
      'proceso_validacion' => 0 . "/" . $this->totalRows . "/procesando",
      'estado' => 1
    ]);   
    
    }
    return $rows;

  }
//                         TODAS LAS VARIABLES                             //

public function nombre($row,$comercio_id,$exists,$columna,$array,$barcode)
  {
    $nombre_columna = $columna['nombre'];

    if($nombre_columna != "0"){
    $nombre = $row[$nombre_columna];
    $array = $this->agregarElementoAlArray($array, 'name', $nombre);
    } else {
    if($exists == null){ $array = $this->agregarElementoAlArray($array, 'name', "Producto nuevo - codigo: ".$barcode);}
    }
    
    return $array;
  }
  
  public function alerts($row,$comercio_id,$exists,$columna,$array)
  {
    $nombre_columna = $columna['inv_minimo'];

    if($nombre_columna != "0"){
    $inv_minimo = $row[$nombre_columna];
    if(empty($inv_minimo) || $inv_minimo == null || $inv_minimo == ""){$inv_minimo = 0;}
    $array = $this->agregarElementoAlArray($array, 'alerts', $inv_minimo);
    } else {
    if($exists == null){ $array = $this->agregarElementoAlArray($array, 'alerts', 0);}
    }
    
    return $array;
  }  

  public function cost($row,$comercio_id,$exists,$columna,$array)
  {
    $nombre_columna = $columna['costo'];

    if($nombre_columna != "0"){
    $costo = $row[$nombre_columna];
    if($costo == ""){$costo = 0;}
    $array = $this->agregarElementoAlArray($array, 'cost', $costo);
    } else {
    if($exists == null){ $array = $this->agregarElementoAlArray($array, 'cost', 0);}
    }
    
    return $array;
  }
  
  
public function proveedores($row,$comercio_id,$exists,$columna,$array)
  {
    $nombre_columna = $columna['proveedor'];

    if($nombre_columna != "0"){

    $nombre_proveedor = $row[$nombre_columna];
    $proveedor_id = 1; 
    
    $p = proveedores::where('nombre',$nombre_proveedor)->where('comercio_id',$comercio_id)->where('eliminado',0)->first();
    // si existe usa ese

    if($nombre_proveedor != ""){
    if($p != null){
    $proveedor_id = $p->id;  
    } else {
    $proveedor_id = $this->CrearProveedor($nombre_proveedor,$comercio_id);
    }        
    } 


    $array = $this->agregarElementoAlArray($array, 'proveedor_id', $proveedor_id);

    } else {
    if($exists == null){ $array = $this->agregarElementoAlArray($array, 'proveedor_id', 1);}
    }
    
    return $array;
  }

public function marcas($row,$comercio_id,$exists,$columna,$array)
  {
    $nombre_columna = $columna['marca'];

    if($nombre_columna != "0"){

    $nombre_marca = $row[$nombre_columna];
    $marca_id = 1; 
    
    $m = marcas::where('name',$nombre_marca)->where('comercio_id',$comercio_id)->where('eliminado',0)->first();
    // si existe usa ese
    
    if($nombre_marca != ""){
    if($m != null){
    $marca_id = $m->id;  
    } else {
    $marca_id = $this->CrearMarca($nombre_marca,$comercio_id);
    }
    }

    $array = $this->agregarElementoAlArray($array, 'marca_id', $marca_id);

    } else {
    if($exists == null){ $array = $this->agregarElementoAlArray($array, 'marca_id', 1);}
    }
    
    return $array;
  }


public function CrearMarca($nombre_marca,$comercio_id){
            
    // si no existe lo crea
    $m = marcas::create([
        'comercio_id' => $comercio_id,
        'name' => $nombre_marca,
        'eliminado' => 0
    ]);

     return $m->id;
}

public function CrearProveedor($nombre_proveedor,$comercio_id){

    $ultimo_proveedor = proveedores::where('comercio_id',$comercio_id)->orderBy('id_proveedor','desc')->first();
                    
    if($ultimo_proveedor != null){
    $cod_proveedor = $ultimo_proveedor->id_proveedor + 1;
    } else {
    $cod_proveedor = 1;    
    }
                  
    // si no existe lo crea
    $p = proveedores::create([
    'comercio_id' => $comercio_id,
    'creador_id' => $comercio_id,
    'nombre' => $nombre_proveedor,
    'eliminado' => 0,
    'id_proveedor' => $cod_proveedor
     ]);
     
     return $p->id;
    
}


public function categorias($row,$comercio_id,$exists,$columna,$array)
  {
    $nombre_columna = $columna['categoria'];
    
    if($nombre_columna != "0"){

    $nombre_categoria = $row[$nombre_columna];
    $categoria_id = 1; 
    
    $c = Category::where('name',$nombre_categoria)->where('comercio_id',$comercio_id)->where('eliminado',0)->first();    // si existe usa ese

    if($nombre_categoria != ""){
    if($c != null){
    $categoria_id = $c->id;  
    } else {
    $categoria_id = $this->CrearCategoria($nombre_categoria,$comercio_id);
    }
    }

    $array = $this->agregarElementoAlArray($array, 'category_id', $categoria_id);

    } else {
    if($exists == null){ $array = $this->agregarElementoAlArray($array, 'category_id', 1);}
    }
    
    return $array;
  }
 
 
public function CrearCategoria($nombre_categoria,$comercio_id){
    // si no existe lo crea
    $c = Category::create([
        'comercio_id' => $comercio_id,
        'name' => $nombre_categoria,
        'eliminado' => 0
    ]);
                  
    return $c->id;      
} 

public function almacen($row,$comercio_id,$exists,$columna)
  {
    $nombre_columna = $columna['almacen'];

    if($nombre_columna != "0"){
    $nombre_almacen = $row[$nombre_columna];

    if(empty($nombre_almacen) || $nombre_almacen == ""){ $almacen_id = 1;} else { 
    $a = seccionalmacen::where('nombre',$nombre_almacen)->where('comercio_id',$comercio_id)->where('eliminado',0)->first();    // si existe usa ese

    if($a != null){
    $almacen_id = $a->id;  
    } else {
    $almacen_id = $this->CrearAlmacen($nombre_almacen,$comercio_id);
    }
    }

    return $almacen_id;        
    }
 
  }

  public function CrearAlmacen($nombre_almacen,$comercio_id){
    // si no existe lo crea
      $a = seccionalmacen::create([
          'comercio_id' => $comercio_id,
          'nombre' => $nombre_almacen,
          'eliminado' => 0
      ]);
      
    return $a->id;      
   }  

  public function manejaStock($row,$comercio_id,$exists,$columna,$array)
  {
    $nombre_columna = $columna['maneja_stock'];
    
    if($nombre_columna != "0"){
    $nombre_maneja_stock = $row[$nombre_columna];
    if ($nombre_maneja_stock == '' || empty($nombre_maneja_stock)) {
      $maneja_stock = "no";
    } else {
      $maneja_stock = $nombre_maneja_stock;
    }
    $array = $this->agregarElementoAlArray($array, 'stock_descubierto', $maneja_stock);
    } else {
    if($exists == null){ $array = $this->agregarElementoAlArray($array, 'stock_descubierto', "no");}    
    }
    
    return $array;
  }
  
    public function pesable($row,$comercio_id,$exists,$columna,$array)
  {
    $nombre_columna = $columna['pesable'];
    
    if($nombre_columna != "0"){
    $nombre_origen = $row[$nombre_columna];
    if ($nombre_origen == 'si') {
      $pesable = 1;
    } else {
      $pesable = 9; 
    }
    $array = $this->agregarElementoAlArray($array, 'unidad_medida', $pesable);
    } else {
    if($exists == null){ $array = $this->agregarElementoAlArray($array, 'unidad_medida', 9);}    
    }
    
    return $array;
  }
  
    public function origen($row,$comercio_id,$exists,$columna,$array)
  {
    $nombre_columna = $columna['origen'];
    $origen = "1";
    
    if($nombre_columna != "0"){
    $nombre_origen = $row[$nombre_columna];
    if ($nombre_origen == 'compra') {
      $origen = "1";
    }
    if ($nombre_origen == 'produccion') {
        $origen = 2;
    }
    if ($nombre_origen == 'ensamblado en la venta') {
        $origen = 3;
    }
    $array = $this->agregarElementoAlArray($array, 'tipo_producto', $origen);
    } else {
    if($exists == null){ $array = $this->agregarElementoAlArray($array, 'tipo_producto', 1);}    
    }
    
    return $array;
  }
  
    public function ventaMostrador($row,$comercio_id,$exists,$columna,$array)
  {
    $nombre_columna = $columna['venta_mostrador'];
    $vm = 0;
    
    if($nombre_columna != "0"){
    $nombre_venta_mostrador = $row[$nombre_columna];
    if ($nombre_venta_mostrador == 'si') {
      $vm = 1;
    }
    if ($nombre_venta_mostrador == 'no') {
        $vm = 0;
    }
    $array = $this->agregarElementoAlArray($array, 'mostrador_canal', $vm);
    } else {
    if($exists == null){ $array = $this->agregarElementoAlArray($array, 'mostrador_canal', 1);}    
    }

    return $array;
  }
  
    public function ventaEcommerce($row,$comercio_id,$exists,$columna,$array)
  {
    $nombre_columna = $columna['venta_ecommerce'];
    $ve = 0;
    
    if($nombre_columna != "0"){
    $nombre_venta_ecommerce = $row[$nombre_columna];
    if ($nombre_venta_ecommerce == 'si') {
      $ve = "1";
    }
    if ($nombre_venta_ecommerce == 'no') {
        $ve = 0;
    }
    $array = $this->agregarElementoAlArray($array, 'ecommerce_canal', $ve);
    } else {
    if($exists == null){ $array = $this->agregarElementoAlArray($array, 'ecommerce_canal', 0);}    
    }
    
    return $array;
  }

  public function ventaWocommerce($row,$comercio_id,$exists,$columna,$array)
  {
    $nombre_columna = $columna['venta_wocommerce'];
    $vw = 0;
    
    if($nombre_columna != "0"){
    $nombre_venta_wocommerce = $row[$nombre_columna];
    if ($nombre_venta_wocommerce == 'si') {
      $vw = "1";
    }
    if ($nombre_venta_wocommerce == 'no') {
        $vw = 0;
    }
    $array = $this->agregarElementoAlArray($array, 'wc_canal', $vw);
    $array = $this->agregarElementoAlArray($array, 'wc_push', $vw);
    } else {
    if($exists == null){ 
        $array = $this->agregarElementoAlArray($array, 'wc_canal', 0);    
        $array = $this->agregarElementoAlArray($array, 'wc_push', 0);
        
    }    
    }
    
    return $array;
  }
  
  public function imagen($row,$comercio_id,$exists,$columna,$array)
  {
    $nombre_columna = $columna['imagen'];
 
    if($nombre_columna != "0"){
    $nombre_imagen = $row[$nombre_columna];
    if ($nombre_imagen == '' || $nombre_imagen == null) {
      $img = null;
    }
    if ($nombre_imagen != '' || $nombre_imagen != null) {
     $img = imagenes::where('name', $rows)->where('comercio_id', $comercio_id)->where('eliminado', 0)->first()->url;
    }
    $array = $this->agregarElementoAlArray($array, 'image', $img);
    } else {
    if($exists == null){ $array = $this->agregarElementoAlArray($array, 'image', null);}   
    }
    
    return $array;
  }

  public function ProductoTipo($row,$comercio_id,$exists,$columna,$array) {
  
  $nombre_columna = $columna['cod_variacion'];

  if($nombre_columna != "0"){
  $variacion = $row[$nombre_columna];
  $array = $this->agregarElementoAlArray($array, 'producto_tipo', $variacion ? 'v' : 's');
  } else {
  $array = $this->agregarElementoAlArray($array, 'producto_tipo', 's');
  }
  return $array;
  }

  public function PrecioInternoArray($row,$configuracion,$comercio_id,$exists,$columna,$array,$product) {
  
  // Obtener regla de precios interno
  
  // Si no tiene la columna regla
  /*
  
  if ($columna['regla_precio_interno'] == "0") {
      $regla = $product ? $product->regla_precio_interno : 1;
  } else {
      $regla = $row[$columna['regla_precio_interno']] == "precio fijo" ? 1 : 2;
  }
  
  */
  
  // si es precio fijo
  
  $regla = 1;
  $precio_interno = 0;
  
  if($regla == 1){

  if ($columna['precio_interno'] == "0") {
    $precio_interno = $product ? $product->precio_interno : 0;
  } else {
    if($row[$columna['precio_interno']] != ""){
    $precio_interno = $row[$columna['precio_interno']];
    } else {
    $precio_interno = 0;    
    }
  }
  
  //$array = $this->agregarElementoAlArray($array, 'porcentaje_regla_precio_interno', 0);
  //$array = $this->agregarElementoAlArray($array, 'regla_precio_interno', $regla);
  $array = $this->agregarElementoAlArray($array, 'precio_interno', $precio_interno);
  }
  
  
  // Si es margen sobre el costo
  
  /*
  if($regla == 2){
  $costo = $this->GetCostoParaReglaPrecios($columna,$row,$product);    
  
  if($columna['margen_regla_precio_interno'] == "0"){
  $porcentaje = $product ? $product->porcentaje_regla_precio_interno/100 : 0;    
  } else {
  $porcentaje = $row[$columna['margen_regla_precio_interno']]/100;
  }
  
  $precio_interno = $costo * (1 + $porcentaje);
  $porcentaje_pasar = $porcentaje * 100;
  
  $array = $this->agregarElementoAlArray($array, 'porcentaje_regla_precio_interno', $porcentaje_pasar);
  $array = $this->agregarElementoAlArray($array, 'regla_precio_interno', $regla);
  $array = $this->agregarElementoAlArray($array, 'precio_interno', $precio_interno);
  }
  */
  
  return $array;
  }
  
  public function TipoUnidadMedida($row){
      if($row == ''){
          return 9;
      } else {
         $unidad_medida = unidad_medida::where("nombre_completo",$row)->first();
         if($unidad_medida != null){ return $unidad_medida->id;} else {return 9;}
      }
  }
  

  
  public function PrecioInternoSimple($precio_interno,$costo,$producto_tipo,$configuracion) {
      if($producto_tipo == "s") {
          if($configuracion == 1) {$valor = $costo;} else {$valor = $precio_interno;}
          return $valor ?? 0;
      } else {
          return 0;
      }
  }
  
  // 22-4-2024
  public function PrecioInternoVariable($row,$configuracion,$columna,$array){
       
  $columna_costo = $columna['costo'];
  $producto_tipo = $array['producto_tipo'];
  
  if($producto_tipo == "v") {
      
  if($configuracion == 1 && $columna_costo != "0") {
  $costo = $row[$columna_costo];
  $precio_interno = $costo;
  }

  if($configuracion == 0 && $columna['precio_interno'] != "0") {
  $columna_precio_interno = $columna['precio_interno'];      
  $precio_interno = $row[$columna_precio_interno];
  }
  }
  
  
  return $precio_interno ?? 0;
  }
  
  
  public function PrecioInternoVariableOld($precio_interno,$costo,$producto_tipo,$configuracion) {
      if($producto_tipo == "v") {
          if($configuracion == 1) {$valor = $costo;} else {$valor = $precio_interno;}
          return $valor ?? 0;
      } else {
          return 0;
      }
  }
  
  public function HistoricoStock($product_id,$referencia_variacion,$cantidad_movimiento,$stock_real,$comercio_id){
        
        $historico_stock = historico_stock::create([
        'producto_id' => $product_id,
        'referencia_variacion' => $referencia_variacion,
        'tipo_movimiento' => 8,
        'cantidad_movimiento' => $cantidad_movimiento,
        'stock' => $stock_real,
        'usuario_id' => $comercio_id,
        'comercio_id'  => $comercio_id
        ]);
        
        return $historico_stock;
        
  }
  
    public function GetEtiquetas($etiq,$comercio_id){
      $nombre_etiquetas = $this->GetNombreEtiquetasProductos($etiq);
      $etiquetas = [];
      foreach($nombre_etiquetas as $ne) {
         $id_etiqueta = $this->obtenerIdEtiqueta($ne,$comercio_id);
         $et =  ['nombre' => $ne, 'id' => $id_etiqueta];
      array_push($etiquetas,$et);
      }
      
      return $etiquetas;
  }
  
  public function GetNombreEtiquetasProductos($etiq){
      
       //dump($etiq);
      
       $etiquetas = explode("|", $etiq);
       
       $etiquetas_n = [];
       foreach($etiquetas as $j) {
       
       if ($j[0] == ' ') {
       $nombre_var = ltrim($j, " "); // Eliminar primer campo      
       } else {
       $nombre_var = $j;
       }
       if ($j[strlen($j) - 1] == ' ') {
       $nombre_var = substr($nombre_var, 0, -1); // Eliminar ultimo campo            
       } else {
       $nombre_var = $nombre_var;
       }
       
       array_push($etiquetas_n,$nombre_var); 
       }
                  
        
       return $etiquetas_n;
  }
  
  // FunciÃ³n para obtener el ID de la etiqueta
function obtenerIdEtiqueta($nombre_etiqueta,$comercio_id)
{
    // Hacer la consulta a la base de datos
    $etiqueta = etiquetas::where('nombre', $nombre_etiqueta)->where('origen','productos')->where('comercio_id',$comercio_id)->first(); // Suponiendo que solo deseas obtener el primer resultado
    
    if($etiqueta == null) {
        $etiqueta = etiquetas::create([
            'nombre' => $nombre_etiqueta,
            'origen' => 'productos',
            'comercio_id' => $comercio_id,
            'color' => 'success'
            ]);
    }
    
    // Retornar el ID si se encontrÃ³ la etiqueta, de lo contrario, retornar null
    return $etiqueta ? $etiqueta->id : null;
}


    public function StoreUpdateEtiquetas($relacion_id,$accion,$origen,$comercio_id,$array_etiquetas) {
        
        //dd($this->etiquetas_seleccionadas2);
             
        // Si $accion es 1 crea si es 2 actualiza
        $this->etiquetas_seleccionadas = $array_etiquetas;
        
        if($accion == 1) {
        
        foreach($this->etiquetas_seleccionadas as $e){
    
        etiquetas_relacion::create([
           'relacion_id' => $relacion_id,
           'etiqueta_id' => $e['id'],
           'nombre_etiqueta' => $e['nombre'],
           'estado' => 1,
           'origen' => $origen,
           'comercio_id' => $comercio_id
            ]);
        }
        
            
        } 
        else {
        
        $etique = etiquetas_relacion::where('relacion_id', $relacion_id)->get();

        // Ids de las etiquetas encontradas en la consulta
        $idsEncontrados = $etique->pluck('etiqueta_id')->toArray();
        
        // Array de etiquetas
        $etiquetas = $this->etiquetas_seleccionadas;
        
        // Iterar sobre el resultado de la consulta
        foreach ($etique as $et) {
            // Verificar si la etiqueta_id est¨¢ en el array de etiquetas
            $estado = in_array($et->etiqueta_id, array_column($etiquetas, 'id')) ? 1 : 0;
        
            // Actualizar el estado en la base de datos
            $et->update(['estado' => $estado]);
        }
        
        // Buscar ids en $etiquetas que no est¨¦n en $ep
        $nuevosIds = array_diff(array_column($etiquetas, 'id'), $idsEncontrados);
        
        //dd($nuevosIds);
        
        // Crear nuevas filas en la tabla etiquetas_productos
        foreach ($nuevosIds as $nuevoId) {
            $nuevaEtiqueta = null;
            foreach ($etiquetas as $etiqueta) {
                if ($etiqueta['id'] == $nuevoId) {
                    $nuevaEtiqueta = $etiqueta;
                    break;
                }
            }
        
            if ($nuevaEtiqueta) {
                etiquetas_relacion::create([
                    'relacion_id' => $relacion_id,
                    'comercio_id' => $comercio_id,
                    'origen' => $origen,
                    'etiqueta_id' => $nuevoId,
                    'nombre_etiqueta' => $nuevaEtiqueta['nombre'],
                    'estado' => 1 // O el valor que desees
                ]);
            }
        }
        }
        
           // Realizar la consulta para obtener las etiquetas
        $etiquetas = etiquetas_relacion::where('relacion_id', $relacion_id)->where('estado', 1)->get();
        
        // Obtener un array de los valores 'nombre_etiqueta'
        $nombres_etiquetas = $etiquetas->pluck('nombre_etiqueta')->toArray();
        
        // Realizar el implode
        $etiquetas_implode = implode(' | ', $nombres_etiquetas);

        $product = Product::find($relacion_id);
        
        $product->update([
            'etiquetas' => $etiquetas_implode
            ]); 
            

        $this->etiquetas_seleccionadas = [];    
            
    }  

    public function GetProductosListaPrecios($product,$referencia_variacion,$lista_id){
    $plp = productos_lista_precios::where('product_id',$product->id)->where('referencia_variacion',$referencia_variacion)->where('lista_id',$lista_id)->first();
    return $plp;    
    }    
    
    public function GetReglaPrecios($columna, $filasTotales, $productos_lista_precios, $variable) {
        // Si no tiene la columna regla
        if ($columna[$variable] == "0") {
            return $productos_lista_precios ? $productos_lista_precios->regla_precio : 1;
        } else {
            return $filasTotales[$columna[$variable]] == "precio fijo" ? 1 : 2;
        }
    }
    public function GetReglaPreciosLista($row,$columna,$nombre_columna_base_datos,$product,$comercio_id,$exists,$productos_lista_precios) {
        
        $nombre_columna = $columna[$nombre_columna_base_datos];
        
        if ($nombre_columna == "0") {
             $regla = $productos_lista_precios ? $productos_lista_precios->regla_precio : 1;
        } else {
            $regla = $row[$nombre_columna] == "precio fijo" ? 1 : 2;
        }
        
        if($regla == null){$regla = 1;}
        return $regla; 
    }
    
    public function StoreUpdatePreciosListas($row,$columna,$columna_excel,$columna_base_datos,$product,$comercio_id,$exists,$regla,$lista_id,$lista_nombre,$productos_lista_precios) {

    $r = $row[$columna_excel] ?? 0;
    $datos_col = explode("_",$columna_base_datos);
    
    if($regla == 1){
      $this->StoreUpdatePrecioFijo($row,$datos_col,$product,$comercio_id,$r,$columna,$exists,$columna_base_datos,$lista_id);  
    }
        
    if($regla == 2){
        
      // tenemos que obtener el costo  ---> del excel o BD
      $costo = $this->GetCostoParaReglaPrecios($columna,$row,$product);
          
      // tenemos que obtener el % del margen ---> del excel o BD
      $porcentaje_regla_precios = $this->GetMargenParaReglaPrecios($columna,$row,$productos_lista_precios,$lista_id,$lista_nombre);          
        
      //var_dump('costo '.$costo , 'porcentaje_regla_precios '.$porcentaje_regla_precios);
      $this->StoreUpdatePrecioPorcentaje($row,$datos_col,$product,$comercio_id,$r,$columna,$exists,$costo,$porcentaje_regla_precios,$lista_id,$lista_nombre);  
    } 
      
    }

    
    public function GetCostoParaReglaPrecios($columna,$filasTotales,$product){
    if($columna['costo'] == "0"){
    return $product ? $product->cost : 0;    
    } else {
    return $filasTotales[$columna['costo']];
    }   
    }
    
    public function SetNombreColumnaMargenPrecio($lista_id,$nombre_lista,$columna){
    if(0 < $lista_id){
    $margen_regla_precio_col = $lista_id."_porcentaje_regla_precio_".$nombre_lista;
    return $columna[$margen_regla_precio_col]; // Columna del margen_regla_precio 
    } else {
    return $columna['margen_regla_precio']; // Columna del margen_regla_precio     
    }
    }
    
    public function GetMargenParaReglaPrecios($columna,$filasTotales,$productos_lista_precios,$lista_id,$nombre_lista){
    $nombre_columna_margen_regla_precio = $this->SetNombreColumnaMargenPrecio($lista_id,$nombre_lista,$columna);
 
    if($nombre_columna_margen_regla_precio == "0"){
    return $productos_lista_precios ? $productos_lista_precios->porcentaje_regla_precio/100 : 0;    
    } else {
    return $filasTotales[$nombre_columna_margen_regla_precio]/100;
    }   
    }

    public function StoreUpdatePrecioPorcentaje($filasTotales,$var,$product,$comercio_id,$r,$columna,$exists,$costo,$porcentaje_regla_precios,$lista_id,$nombre_lista){

        if($lista_id == "0"){
        $nombre_columna_margen_regla_precio = $this->SetNombreColumnaMargenPrecio($lista_id,$nombre_lista,$columna); // Columna del margen_regla_precio 
        $nombre_columna_costo = $columna['costo']; 
        $porcentaje_regla_precio_columna = $porcentaje_regla_precios * 100;    
        $this->StoreUpdatePrecioPorcentajeDB($exists,$nombre_columna_costo,$nombre_columna_margen_regla_precio,$lista_id,$product,$comercio_id,$costo,$porcentaje_regla_precio_columna,$porcentaje_regla_precios);
        }
              
        if(0 < $lista_id){
        $nombre_columna_margen_regla_precio = $this->SetNombreColumnaMargenPrecio($lista_id,$nombre_lista,$columna);
        $nombre_columna_costo = $columna['costo']; 
        $porcentaje_regla_precio_columna = $porcentaje_regla_precios * 100;    
        $this->StoreUpdatePrecioPorcentajeDB($exists,$nombre_columna_costo,$nombre_columna_margen_regla_precio,$lista_id,$product,$comercio_id,$costo,$porcentaje_regla_precio_columna,$porcentaje_regla_precios);
        }

    }
    
    public function StoreUpdatePrecioPorcentajeDB($exists,$nombre_columna_costo,$nombre_columna_margen_regla_precio,$lista_id,$product,$comercio_id,$costo,$porcentaje_regla_precio_columna,$porcentaje_regla_precios){
        // Si no existe el producto y no tiene alguna de las dos columnas
          
          //var_dump($porcentaje_regla_precio_columna);
          
          if($exists == null){
          if($nombre_columna_costo == "0" || $nombre_columna_margen_regla_precio == "0"){
          $group = productos_lista_precios::create([
                  'precio_lista'           => 0,
                  'lista_id'               => $lista_id,
                  'regla_precio' => 2,
                  'porcentaje_regla_precio' => $nombre_columna_margen_regla_precio != "0" ? $row[$nombre_columna_margen_regla_precio]  : 0,
                  'referencia_variacion'   => $this->referencia_id,
                  'product_id'            => $product->id,
                  'comercio_id'         => $comercio_id,                
                ]);            
              
          } else {
          $group = productos_lista_precios::create([
                  'precio_lista'           => $costo * (1 + $porcentaje_regla_precios),
                  'lista_id'               => $lista_id,
                  'regla_precio' => 2,
                  'porcentaje_regla_precio' => $porcentaje_regla_precios * 100,
                  'referencia_variacion'   => $this->referencia_id,
                  'product_id'            => $product->id,
                  'comercio_id'         => $comercio_id,                
                ]);  
          }
          var_dump($group);
          }
        
        // Si tenemos que actualizar el producto
        if($exists != null){
        $group = productos_lista_precios::updateOrCreate(
                [
                  'product_id'          => $product->id,
                  'referencia_variacion' => $this->referencia_id,
                  'lista_id'               => $lista_id,
                ],
                [
                  'precio_lista'           => $costo * (1 + $porcentaje_regla_precios),
                  'lista_id'               => $lista_id,
                  'regla_precio' => 2,
                  'porcentaje_regla_precio' => $porcentaje_regla_precios * 100,
                  'referencia_variacion'   => $this->referencia_id,
                  'product_id'            => $product->id,
                  'comercio_id'         => $comercio_id,
                ]
              ); 
              
        //var_dump($group);
        
        
        }        
    }

    public function StoreUpdatePrecioFijo($filasTotales,$var,$product,$comercio_id,$r,$columna,$exists,$nombre_columna_precio,$lista_id){
        
        $nombre_columna_precio = $columna[$nombre_columna_precio];
        // si la columna es 0 y no existe tiene que crearlo como valor 0
        if($nombre_columna_precio == "0"){
          if($exists == null){
          $p_price = productos_lista_precios::where('lista_id',$lista_id)->where('comercio_id',$comercio_id)->where('product_id',$product->id)->first();
          if($p_price == null){
        
          $group = productos_lista_precios::create([
                  'precio_lista'           => 0,
                  'lista_id'               => $lista_id,
            //    'regla_precio' => 1,
            //    'porcentaje_regla_precio' => 0,
                  'referencia_variacion'   => $this->referencia_id,
                  'product_id'            => $product->id,
                  'comercio_id'         => $comercio_id,                
                ]);       
                
          }
        }
        }
        
        // si tiene titulo para importar ---> Hay que ver si tiene el titulo de regla de precios

            
          ///// PRECIOS LISTA BASE ////////
          if (count($var) < 2) {
            if (is_array($var) && $var[0] == $nombre_columna_precio) {
            
            if($nombre_columna_precio != "0"){ 
                        
              $group = productos_lista_precios::updateOrCreate(
                [
                  'product_id'          => $product->id,
                  'referencia_variacion' => $this->referencia_id,
                  'lista_id'               => 0,
                ],
                [
                  'precio_lista'           => empty($r) ? 0 : $r,
                  'lista_id'               => $lista_id,
            //    'regla_precio' => 1,
            //    'porcentaje_regla_precio' => 0,
                  'referencia_variacion'   => $this->referencia_id,
                  'product_id'            => $product->id,
                  'comercio_id'         => $comercio_id,
                ]
              );
            }
          }
 
        }
 
           ///// PRECIOS OTRAS LISTAS //////// ----> se duplica cuando tiene que actualizarse
          if (count($var) > 2) {
            if (is_array($var) && $var[1] == "precio" && is_numeric($var[0])) {

            if($nombre_columna_precio != "0"){  // aca cambiar por el nombre de la columna de cada lista de precios
              
              $lista_id = $var[0];

              $group = productos_lista_precios::updateOrCreate(
                [
                  'product_id'          => $product->id,
                  'referencia_variacion' => $this->referencia_id,
                  'lista_id'               => $lista_id,
                ],
                [
                  'precio_lista'           => empty($r) ? 0 : $r,
                  'lista_id'               => $var[0],
                  'referencia_variacion'   => $this->referencia_id,
                  'product_id'            => $product->id,
                  'comercio_id'         => $comercio_id,
                ]
              );
              
              
            }
          }    
          
        }
 
    }
    
    
    
    public function StoreUpdatePrecios($filasTotales,$i,$product,$comercio_id,$columna,$exists){
    
        $nombre_columna_precio = 'precio'; // Columna del precio
        $nombre_lista = 'base';
        $var = explode("_",$nombre_columna_precio);
        $r = $filasTotales[$nombre_columna_precio] ?? 0;
        
        $productos_lista_precios = $this->GetProductosListaPrecios($product,$this->referencia_id,0);
        
        // PRIMERO TENEMOS QUE VER SI TIENE LA COLUMNA REGLA PRECIOS O TIENE LA REGLA 
        
        //$variable = 'regla_precio';
        //$regla = $this->GetReglaPrecios($columna,$filasTotales,$productos_lista_precios,$variable);
        
        $regla = 1;
        
        $lista_id = 0;
        
        if($regla == "1"){
          $this->StoreUpdatePrecioFijo($filasTotales,$var,$product,$comercio_id,$r,$columna,$exists,$nombre_columna_precio,$lista_id);  
        }
        
        /*
        if($regla == "2"){
          // tenemos que obtener el costo  ---> del excel o BD
          $costo = $this->GetCostoParaReglaPrecios($columna,$filasTotales,$product);
          
          // tenemos que obtener el % del margen ---> del excel o BD
          $porcentaje_regla_precios = $this->GetMargenParaReglaPrecios($columna,$filasTotales,$productos_lista_precios,$lista_id,$nombre_lista);          
        
          //var_dump('costo '.$costo , 'porcentaje_regla_precios '.$porcentaje_regla_precios);
          $this->StoreUpdatePrecioPorcentaje($filasTotales,$var,$product,$comercio_id,$r,$columna,$exists,$costo,$porcentaje_regla_precios,$lista_id,$nombre_lista);  
        }
        */
    
    }

    public function StoreUpdateStock($row,$i,$product,$comercio_id,$id_almacen,$columna,$exists){
        
        ////// STOCK CASA CENTRAL ////
        $nombre_columna_stock = $columna['stock'];
        
        // si no se tiene que importar y el prooducto no existe
        if($nombre_columna_stock == "0"){
            if($exists == null){
            $group = productos_stock_sucursales::create([
                  'stock' => 0,
                  'stock_real' => 0,
                  'referencia_variacion'   => $this->referencia_id,
                  'product_id'  => $product->id,
                  'comercio_id' => $comercio_id,
                  'almacen_id' => $id_almacen,
                  'sucursal_id' => 0,
                  'eliminado' => 0
                ]);
            }
        }
        // si tiene titulo para importar 
        if($nombre_columna_stock != "0"){ 
        $r = $row[$nombre_columna_stock];
        
              $pss_stock = productos_stock_sucursales::where('product_id', $product->id)
                ->where('comercio_id', $comercio_id)
                ->where('sucursal_id', 0)
                ->where('referencia_variacion', $this->referencia_id)
                ->where('eliminado', 0)
                ->first();
             
              if ($pss_stock != null) {
                $dif = $pss_stock->stock_real - $pss_stock->stock;
                $cantidad_movimiento = (empty($r) ? 0 : $r) - $pss_stock->stock_real;
                $this->sd1 = (empty($r) ? 0 : $r) - $dif;
              } else {
                $this->sd1 = (empty($r) ? 0 : $r);
                $cantidad_movimiento = (empty($r) ? 0 : $r);
              }
    
              $group = productos_stock_sucursales::updateOrCreate(
                [
                  'product_id' => $product->id,
                  'comercio_id' => $comercio_id,
                  'sucursal_id' => 0,
                  'eliminado' => 0,
                  'referencia_variacion' => $this->referencia_id,
                ],
                [
                  'stock' => $this->sd1,
                  'stock_real' => empty($r) ? 0 : $r,
                  'referencia_variacion'   => $this->referencia_id,
                  'product_id'  => $product->id,
                  'comercio_id' => $comercio_id,
                  'almacen_id' => $id_almacen
                ]
              );
              
              // Poner el historico
              
              $this->HistoricoStock($product->id,$this->referencia_id,$cantidad_movimiento,(empty($r) ? 0 : $r),$comercio_id);
		
		
            
        }

    }
    
    
        public function StoreUpdateAlmacenSucursales($row,$columna_excel,$columna_base_datos,$product,$comercio_id,$id_almacen,$exists){
        
        $datos_col = explode("_",$columna_base_datos);
        $sucursal_id = $datos_col[0];

        // 
        if($columna_excel != "0"){
        $r = $row[$columna_excel];
        
                $actualizar_almacen = productos_stock_sucursales::where('productos_stock_sucursales.product_id', $product->id)
                ->where('productos_stock_sucursales.comercio_id', $comercio_id)
                ->where('productos_stock_sucursales.sucursal_id', $sucursal_id)
                ->where('productos_stock_sucursales.referencia_variacion', $this->referencia_id)
                ->where('productos_stock_sucursales.eliminado', 0)
                ->select('productos_stock_sucursales.almacen_id','productos_stock_sucursales.id')
                ->first();
                
                $id_almacen_sucursal = $this->almacenSucursal($r, $comercio_id); //////////// ALMACEN ////////////// ES PROVISORIO HASTA HACER ESTA PARTE
                $actualizar_almacen->update([
                    'almacen_id' => $id_almacen_sucursal
                ]);

        }

        }    

        public function StoreUpdateStockSucursales($row,$columna_excel,$columna_base_datos,$product,$comercio_id,$id_almacen,$exists){

            
        $datos_col = explode("_",$columna_base_datos);
        $sucursal_id = $datos_col[0];
        
        // si la columna es 0 y no existe tiene que crearlo como valor 0
        if($columna_excel == "0"){
        if($exists == null){
            $group = productos_stock_sucursales::create([
               'stock' => 0,
              'stock_real' => 0,
              'referencia_variacion'   => $this->referencia_id,
              'product_id'  => $product->id,
              'comercio_id' => $comercio_id,
              'almacen_id' => 1,                   
              'comercio_id' => $comercio_id,
              'sucursal_id' => $sucursal_id,
              ]);
            }    
            
        $this->HistoricoStock($product->id,$this->referencia_id,0,0,$sucursal_id);
        }
                
        // si la columna no es cero y existe o no existe lo busca y crea o actualiza 
        
        if($columna_excel != "0"){
        $r = $row[$columna_excel]; // cuando pasa en 0 la $columna_excel, salta como no definido

          ///// STOCK SUCURSALES   /////

         $pss_stock = productos_stock_sucursales::join('sucursales', 'sucursales.sucursal_id', 'productos_stock_sucursales.sucursal_id')
                ->where('productos_stock_sucursales.product_id', $product->id)
                ->where('productos_stock_sucursales.comercio_id', $comercio_id)
                ->where('productos_stock_sucursales.sucursal_id', $sucursal_id)
                ->where('productos_stock_sucursales.referencia_variacion', $this->referencia_id)
                ->where('productos_stock_sucursales.eliminado', 0)
                ->where('sucursales.eliminado', 0)
                ->first();
    
            // Convertir $r a un n¨²mero
            $r = (float) $r;
            
            if ($pss_stock != null) {
                // Convertir los valores a n¨²meros si no lo son
                $pss_stock->stock_real = (float) $pss_stock->stock_real;
                $pss_stock->stock = (float) $pss_stock->stock;
            
                $dif = $pss_stock->stock_real - $pss_stock->stock;
                $this->sd2 = $r - $dif;
                $cantidad_movimiento = $r - $pss_stock->stock_real;
            } else {
                $this->sd2 = $r;
                $cantidad_movimiento = $r;
            }
            // si esa columna del excel esta para importar se crea o actualiza
            
                if($columna_excel != "0"){
              $group = productos_stock_sucursales::updateOrCreate(
                [
                  'product_id' => $product->id,
                  'comercio_id' => $comercio_id,
                  'sucursal_id' => $sucursal_id,
                  'referencia_variacion' => $this->referencia_id,
                ],
                [
                  'stock' => $this->sd2,
                  'stock_real' => empty($r) ? 0 : $r,
                  'referencia_variacion'   => $this->referencia_id,
                  'product_id'  => $product->id,
                  'comercio_id' => $comercio_id,
                  'almacen_id' => 1
                ]
                );
                                  
                }
                

              // Poner el historico
              
              $this->HistoricoStock($product->id,$this->referencia_id,$cantidad_movimiento,(empty($r) ? 0 : $r),$sucursal_id);

        } 
    
    }
    
    public function StoreUpdateCostosYPrecioInterno($product,$costo,$precio_interno,$producto_tipo,$pvd_datos,$configuracion_precio_interno) {
     
     if($product->producto_tipo == "s"){
         if ((isset($this->acciones_2) && $this->acciones_2[0] == 1 && $this->accion == 2) || $this->accion == 1) {
         $product->cost = $costo;
         }
         
         if($configuracion_precio_interno == 1){
         $product->precio_interno = $costo;     
         } else {
         if ((isset($this->acciones_2) && $this->acciones_2[1] == 1 && $this->accion == 2) || $this->accion == 1) {
         $product->precio_interno = $precio_interno;    
         }
         }
         $product->save();
     } else {
         
         if ((isset($this->acciones_2) && $this->acciones_2[0] == 1 && $this->accion == 2) || $this->accion == 1) {
         $pvd_datos->cost = $costo;
         }
         if($configuracion_precio_interno == 1){
         $pvd_datos->precio_interno = $costo;
         } else {
         if ((isset($this->acciones_2) && $this->acciones_2[1] == 1 && $this->accion == 2) || $this->accion == 1) {
         $pvd_datos->precio_interno = $precio_interno;     
         }}
         $pvd_datos->save();
     }
     
        
    }

    public function SetReferenciaId($product,$codigo_variacion,$comercio_id){
    $this->referencia_id = 0;    
 
    // producto variable
    if($product->producto_tipo == "v") {
    $pvd_datos = productos_variaciones_datos::where('codigo_variacion', $codigo_variacion)->where('product_id', $product->id)->where('comercio_id', $comercio_id)->where('eliminado', 0)->first();
    //var_dump($codigo_variacion,$product->id,$comercio_id);
    $this->referencia_id = $pvd_datos->referencia_variacion;
    }
    
    return $this->referencia_id;
        
    }
    
    public function SetPreciosInternosYCostos($precios_internos,$costo,$product,$referencia_id,$comercio_id,$configuracion_precio_interno){
            
        // producto simple
        if($product->producto_tipo == "s") {
        $precio_interno  = $this->PrecioInternoSimple($precios_internos,$product->producto_tipo,$product->producto_tipo,$configuracion_precio_interno);
        $this->StoreUpdateCostosYPrecioInterno($product,$costo ?? 0,$precio_interno,$product->producto_tipo,null,$configuracion_precio_interno);
        }

        // producto variable
        if($product->producto_tipo == "v") {
        $precio_interno = $this->PrecioInternoVariable($precios_internos,$product->producto_tipo,$product->producto_tipo,$configuracion_precio_interno);
        $pvd_datos = productos_variaciones_datos::where('referencia_variacion', $referencia_id)->where('product_id', $product->id)->where('comercio_id', $comercio_id)->where('eliminado', 0)->first();
        $this->StoreUpdateCostosYPrecioInterno($product,$costo ?? 0,$precio_interno,$product->producto_tipo,$pvd_datos,$configuracion_precio_interno);
        }
        
    }
    
    public function GetConfiguracionPrecios($comercio_id){
    $u = User::find($comercio_id);
    if($u != null){
    $configuracion_precio_interno = $u->costo_igual_precio;    
    } else {
    $configuracion_precio_interno = 0;    
    }
    
    return $configuracion_precio_interno;
    }

    function agregarElementoAlArray($array, $titulo, $valor) {
        $array[$titulo] = $valor;
        return $array;
    }    
    
      public function almacenSucursal($nombre_almacen, $comercio_id)
  {
              
    if( ($nombre_almacen == '') ||  $nombre_almacen == 'Sin almacen') {
    $almacen_id = 1;
    }
    else {
    // si tiene otro nombre
    $a = seccionalmacen::where('nombre',$nombre_almacen)->where('comercio_id',$comercio_id)->where('eliminado',0)->first();
              
    // si existe usa ese
                  
    if($a != null){
      $almacen_id = $a->id;  
    } else {
                      
    // si no existe lo crea
      $a = seccionalmacen::create([
          'comercio_id' => $comercio_id,
          'nombre' => $nombre_almacen,
          'eliminado' => 0
      ]);
                  
      $almacen_id = $a->id;       
                        
      }
                  
      }
    
    return $almacen_id;   
  }
  
  public function FuncionesVariaciones($row,$i,$product,$comercio_id,$columna,$exists,$array,$configuracion_precio_interno){
          
          // tenemos que determinar el nombre de la columna de cod_variacion,variacion, cantidad_unidad_medida y tipo_unidad_medida
          $nombre_columna_cod_variacion = $columna['cod_variacion'];
          $nombre_columna_variacion = $columna['variacion'];
          
          // 22-4-2023
          $precio_interno_variable = $this->PrecioInternoVariable($row,$configuracion_precio_interno,$columna,$array);
          //dump($precio_interno_variable);
          
          // si se importa la columna cod variacion y variacion.
          if($nombre_columna_cod_variacion != "0" && $nombre_columna_variacion != "0"){

          // valores de cod variacion y variacion 
          $variacion = $row[$nombre_columna_variacion];
          $cod_variacion = $row[$nombre_columna_cod_variacion];
          
          if (isset($variacion) && isset($cod_variacion)) {
            $this->cod_vari = $cod_variacion;
            
              $variaciones = explode("-", $variacion);
              $v_arr = [];
              $v_id_arr = [];
              $pvd_datos = productos_variaciones_datos::where('codigo_variacion', $cod_variacion)->where('product_id', $product->id)->where('comercio_id', $comercio_id)->where('eliminado', 0)->first();
              if ($pvd_datos == null) {
                //   NO EXISTE LA VARIACION (CONVINACION DE VARIACIONES)  //
                foreach ($variaciones as $vari => $j) {
                  if ($j[0] == ' ') {
                    $nombre_var = ltrim($j, " "); // Eliminar primer campo      
                  } else {
                    $nombre_var = $j;
                  }
                  if ($j[strlen($j) - 1] == ' ') {
                    $nombre_var = substr($nombre_var, 0, -1); // Eliminar ultimo campo            
                  } else {
                    $nombre_var = $nombre_var;
                  }
                  
                  $variationes = variaciones::join('atributos', 'atributos.id', 'variaciones.atributo_id')
                    ->select('variaciones.*')
                    ->where('variaciones.nombre', $nombre_var)
                    ->where('variaciones.comercio_id', $comercio_id)
                    ->where('variaciones.eliminado', 0)
                    ->where('atributos.eliminado', 0)
                    ->first();
                  if ($variationes != null) {
                    $productos_variaciones = productos_variaciones::where('codigo_variacion', $cod_variacion)
                      ->where('variacion_id', $variationes->id)
                      ->where('producto_id', $product->id)
                      ->first();
                    if ($productos_variaciones == null) {
                      $variations = productos_variaciones::create([
                        'producto_id' => $product->id,
                        'variacion_id' => $variationes->id,
                        'comercio_id' => $comercio_id,
                        'atributo_id' => $variationes->atributo_id,
                        'referencia_id' => $this->referencia_id,
                      ]);
                      $id_var = $variations->variacion_id;
                    } else {
                      $id_var = null;
                    }
                  } else {
                    $id_var = null;
                  }
                  array_push($v_id_arr, $id_var);
                  array_push($v_arr, $nombre_var);
                }
                natsort($v_id_arr);
                $v_id_arr = implode(",", $v_id_arr);
                $v_arr = implode(" - ", $v_arr);
                
                productos_variaciones_datos::create([
                  'product_id' => $product->id,
                  'codigo_variacion' => $this->cod_vari,
                  'referencia_variacion' => $this->referencia_id,
                  'cost' => $array['cost'] ?? 0,
                  'precio_interno' => $precio_interno_variable,
                  'variaciones' => $v_arr,
                  'variaciones_id' => $v_id_arr,
                  'imagen' => $array['image'] ?? 0,
                  'comercio_id' => $product->comercio_id,
                  'wc_push' => $array['wc_canal'] ?? 0,
                ]);
              } else {
                // aca tenemos que ver si esta seleccionado o no la columna costo, precio_interno, imagen y wc_push
                
                $array_vble = [
                  'codigo_variacion' => $cod_variacion,
                ];
                
                if(isset($array['cost'])){
                    $array_vble = $this->agregarElementoAlArray($array_vble, 'cost', $array['cost']);
                }
                if(isset($array['venta_wocommerce'])){
                    $array_vble = $this->agregarElementoAlArray($array_vble, 'wc_push', $array['wc_canal']);
                }
                if(isset($array['image'])){
                    $array_vble = $this->agregarElementoAlArray($array_vble, 'image', $array['image']);
                }
        
                if($array['producto_tipo'] == "v"){
                $array_vble  = $this->PrecioInternoArray($row,$configuracion_precio_interno,$comercio_id,$exists,$columna,$array_vble,$product);    
                }
                
                //dump($array_vble);
                
                $pvd_datos->update($array_vble);
              }
            
          } else {
            $this->referencia_id = 0;
          }   
          
          } else {
            $this->referencia_id = 0;
          }   

  }
  
  public function etiquetas($row,$columna,$product,$comercio_id){
    $nombre_columna = $columna['etiquetas'];

    if($nombre_columna != "0"){
    $r = $row[$nombre_columna];
        if($r != null){
          $array_etiquetas = $this->GetEtiquetas($r,$comercio_id);
          $this->StoreUpdateEtiquetas($product->id,2,"productos",$comercio_id,$array_etiquetas);
        }
    }
   }
   
   public function SetearReferenciaVariacion($row,$i,$product,$comercio_id,$columna){
    
    $nombre_columna = $columna['cod_variacion'];
    if($nombre_columna != "0"){
    $r = $row[$nombre_columna];
        $pvd_datos = productos_variaciones_datos::where('codigo_variacion', $r)->where('product_id', $product->id)->where('comercio_id', $comercio_id)->where('eliminado', 0)->first();
        if ($pvd_datos != null) {
          $this->referencia_id = $pvd_datos->referencia_variacion;
        } else {
          $this->referencia_id = Carbon::now()->format('dmYHis') . 'i' . $i . '-' . $comercio_id;
        }       
    }
   }
   
   public function ObtenerIVADefecto($comercio_id){
       $df = datos_facturacion::where('comercio_id',$comercio_id)->where('predeterminado',1)->where('eliminado',0)->first();
       if($df != null){$iva = $df->iva_defecto;} else {$iva = 0;}
       if($iva == null){$iva = 0;}
       return $iva;
   }

   
   public function StoreIVAProducto($row,$i,$product,$comercio_id,$id_almacen,$columna,$exists){
    
    // Casa central
            
    $nombre_columna_iva = $columna['iva'];
    $iva_defecto = $this->ObtenerIVADefecto($comercio_id);
    $r = $row[$nombre_columna_iva] ?? 0;
    
    // si no se tiene que importar y el prooducto no existe
    if($nombre_columna_iva == "0"){
    if($exists == null){
        $p_iva = productos_ivas::where('sucursal_id',$comercio_id)->where('comercio_id',$comercio_id)->where('product_id',$product->id)->first();
        if($p_iva == null){
            
        productos_ivas::create([
          'product_id' => $product->id,
          'comercio_id' => $comercio_id,
          'sucursal_id' => $comercio_id,
          'iva' => $iva_defecto
        ]);
      
      
      }
    }
    }
    
    if($nombre_columna_iva != "0"){
         $group = productos_ivas::updateOrCreate(
        [
          'product_id' => $product->id,
          'comercio_id' => $comercio_id,
          'sucursal_id' => $comercio_id
        ],
        [
          'product_id' => $product->id,
          'comercio_id' => $comercio_id,
          'sucursal_id' => $comercio_id,
          'iva' => empty($r) ? $iva_defecto : $r,
        ]
    ); 
    }
    
    
   }

        public function StoreIvaProductoSucursales($row,$columna_excel,$columna_base_datos,$product,$comercio_id,$id_almacen,$exists){

        $datos_col = explode("_",$columna_base_datos);
        $sucursal_id = $datos_col[0];
        
        $iva_defecto = $this->ObtenerIVADefecto($sucursal_id);
        
        // si la columna es 0 y no existe tiene que crearlo como valor 0
        if($columna_excel == "0"){
        if($exists == null){
        $group = productos_ivas::create(
        [
          'product_id' => $product->id,
          'comercio_id' => $comercio_id,
          'sucursal_id' => $sucursal_id,
          'iva' => $iva_defecto
        ]); 

        }    
            
        }
                
        // si la columna no es cero y existe o no existe lo busca y crea o actualiza 
        
        if($columna_excel != "0"){
        $r = $row[$columna_excel]; // cuando pasa en 0 la $columna_excel, salta como no definido

            
                if($columna_excel != "0"){
 
                  $group = productos_ivas::updateOrCreate(
                        [
                          'product_id' => $product->id,
                          'comercio_id' => $comercio_id,
                          'sucursal_id' => $sucursal_id
                        ],
                        [
                          'product_id' => $product->id,
                          'comercio_id' => $comercio_id,
                          'sucursal_id' => $sucursal_id,
                          'iva' => empty($r) ? $iva_defecto : $r,
                        ]
                    ); 
                                  
                }

        } 
        
    }
    

// 14-8-2024
public function EscribirErrores($import_id, $filasTotales, $e, $comercio_id) {
    // Actualizar el estado de la importaci¨®n y registrar los errores
    importaciones::where('id', $import_id)->update([
        'estado' => 3,
        'errores_bug' => $e
    ]);

    // Actualizar el progreso de la importaci¨®n
    $progreso = count($filasTotales) . "/" . count($filasTotales) . "/procesando";
    importaciones::where('id', $import_id)->update([
        'proceso' => $progreso,
        'proceso_validacion' => $progreso,
    ]);
}  
}
