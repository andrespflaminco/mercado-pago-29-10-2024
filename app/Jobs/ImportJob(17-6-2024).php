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
use App\Models\User;
use App\Models\etiquetas;
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

use App\Models\sucursales;
use App\Models\datos_facturacion;
use App\Models\productos_ivas;




use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ImportJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $import_id, $data, $comercio_id_import, $filePath, $fileName, $accion;

  public $comercio_id, $sd1, $sd2, $cod_vari, $referencia_id, $prov, $proveed, $alm, $al, $categ, $movimiento_stock, $cat, $costo, $stock, $st, $cost, $inv_minimo, $in_minimo, $inv_ideal, $in_ideal, $prec, $pre, $maneja_stock;


  public $tries = 1;

  public $timeout = 60000;

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

  public function __construct($importId, $data, $comercioId, $filePath, $fileName, $accion)
  {
    $this->import_id = $importId;
    $this->data = $data;
    $this->comercio_id_import = $comercioId;
    $this->filePath = $filePath;
    $this->fileName = $fileName;
    $this->accion = $accion;
    
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $filasTotales = $this->data;
    $comercio_id = $this->comercio_id_import;
    $accion = $this->accion;
    
    try {
      for ($i = 0; $i < count($filasTotales) ; $i++) {
    
       if(isset($filasTotales[$i]['precio_interno'])) { $precio_interno = $filasTotales[$i]['precio_interno']; } else {$precio_interno = 0;}
       
       if($this->accion != 1){
        $product = Product::where('barcode', $filasTotales[$i]['codigo'])->where('comercio_id', $comercio_id)->where('eliminado', 0)->first();
        if($product != null){
            
        $this->referencia_id = $this->SetReferenciaId($product,$filasTotales[$i]['cod_variacion'],$comercio_id);
        
        if($this->accion == 2){
        $configuracion_precio_interno = $this->GetConfiguracionPrecios($comercio_id);
        $this->SetPreciosInternosYCostos($precio_interno,$filasTotales[$i]['costo'],$product,$this->referencia_id,$comercio_id,$configuracion_precio_interno);
        }

        foreach ($filasTotales[$i] as $key => $r) {
          $var = explode("_", $key);
          if($this->accion == 2){
          $this->StoreUpdatePrecios($var,$product,$comercio_id,$r);
          }
          if($this->accion == 3){
          $id_almacen         = $this->almacen($filasTotales[$i]['almacen'], $comercio_id);
          $this->StoreUpdateStock($var,$product,$comercio_id,$r,$id_almacen);  
          }
        }
       }
       
       }
       
       if($this->accion == 1){
        $id_proveedor       = $this->proveedores($filasTotales[$i]['proveedor'], $comercio_id); //////////// PROVEEDOR //////////////
        $id_categoria       = $this->categorias($filasTotales[$i]['categoria'], $comercio_id); //////////// CATEGORIA //////////////
        $id_almacen         = $this->almacen($filasTotales[$i]['almacen'], $comercio_id); //////////// ALMACEN //////////////
        $maneja_stock       = $this->manejaStock($filasTotales[$i]['maneja_stock']); //////////// STOCK DESCUBIERTO //////////////
        $origen             = $this->origen($filasTotales[$i]['origen']); //////////// ORIGEN //////////////
        $venta_mostrador    = $this->ventaMostrador($filasTotales[$i]['venta_mostrador']); //////////// VENTA MOSTRADOR //////////////
        $venta_ecommerce    = $this->ventaEcommerce($filasTotales[$i]['venta_ecommerce']); //////////// VENTA ECOMMERCE //////////////
        $venta_wocommerce   = $this->ventaWocommerce($filasTotales[$i]['venta_wocommerce']); //////////// VENTA WOOCOMMERCE //////////////        
        $imagen             = $this->imagen($filasTotales[$i]['imagen'], $filasTotales[$i]['imagen'],  $comercio_id); //////////// IMAGENES //////////////
        $pesable             = $this->Pesable($filasTotales[$i]['pesable'], $filasTotales[$i]['pesable'],  $comercio_id); //////////// PESABLES //////////////
        $producto_tipo      = $this->ProductoTipo($filasTotales[$i]['variacion']);
        $configuracion_precio_interno = $this->GetConfiguracionPrecios($comercio_id);
        $precio_interno_simple  = $this->PrecioInternoSimple($precio_interno,$filasTotales[$i]['costo'],$producto_tipo,$configuracion_precio_interno);
        $precio_interno_variable = $this->PrecioInternoVariable($precio_interno,$filasTotales[$i]['costo'],$producto_tipo,$configuracion_precio_interno);

        $exists = Product::where('barcode', $filasTotales[$i]['codigo'])->where('comercio_id', $comercio_id)->where('eliminado', 0)->first();
        if ($exists != null)
        {
          $this->product_exist = 1;
          $product = Product::where('barcode', $filasTotales[$i]['codigo'])->where('comercio_id', $comercio_id)->where('eliminado', 0)->first();
          
          if($product != null) {
          $product->update([
            'name'            => $filasTotales[$i]['nombre'],
            'barcode'         => $filasTotales[$i]['codigo'],
            'cost'            => $filasTotales[$i]['costo'] ?? 0,
            'tipo_producto' => $origen,
            'producto_tipo' => $filasTotales[$i]['variacion'] ? 'v' : 's',
            'price'           => 0,
            'precio_interno' => $precio_interno_simple,
        //  'precio_interno' => 0,
            'stock'           => 0,
            'alerts'          => empty($filasTotales[$i]['inv_minimo']) ? 0 : $filasTotales[$i]['inv_minimo'],
            'mostrador_canal' => $venta_mostrador,
            'ecommerce_canal' => $venta_ecommerce,
            'wc_canal' => $venta_wocommerce,
            'wc_push' => $venta_wocommerce,
            'comercio_id' => $comercio_id,
            'stock_descubierto'          => $maneja_stock,
            'seccionalmacen_id'     => $id_almacen,
            'category_id'     => $id_categoria,
            'proveedor_id'     => $id_proveedor,
            'eliminado' => '0',
            'image' => $imagen,
            'unidad_medida' => $pesable
          ]);
          
            if($filasTotales[$i]['etiquetas'] != null){
            $array_etiquetas = $this->GetEtiquetas($filasTotales[$i]['etiquetas'],$comercio_id);
            dump($array_etiquetas);
           
           // StoreUpdateEtiquetas($relacion_id,$accion,$origen,$comercio_id)
           
            $this->StoreUpdateEtiquetas($product->id,2,"productos",$comercio_id,$array_etiquetas);
            }
            
          }
        } else {
          $this->product_exist = 0;
          $product = Product::where('barcode', $filasTotales[$i]['codigo'])->where('comercio_id', $comercio_id)->where('eliminado', 0)->first();
          Log::info($product);
          
          $product = Product::create([
            'name'            => $filasTotales[$i]['nombre'],
            'barcode'         => $filasTotales[$i]['codigo'],
            'cost'            => $filasTotales[$i]['costo'] ?? 0,
            'tipo_producto' => $origen,
            'comercio_id' => $comercio_id,
            'producto_tipo' => $filasTotales[$i]['variacion'] ? 'v' : 's',
            'price'           => 0,
        //  'precio_interno' => 0,
            'precio_interno' => $precio_interno_simple,
            'stock'           => 0,
            'alerts'          => empty($filasTotales[$i]['inv_minimo']) ? 0 : $filasTotales[$i]['inv_minimo'],
            'mostrador_canal' => $venta_mostrador,
            'ecommerce_canal' => $venta_ecommerce,
            'wc_canal' => $venta_wocommerce,
            'wc_push' => $venta_wocommerce,
            'stock_descubierto'          => $maneja_stock,
            'seccionalmacen_id'     => $id_almacen,
            'category_id'     => $id_categoria,
            'proveedor_id'     => $id_proveedor,
            'eliminado' => '0',
            'image' => $imagen
          ]);
          
          if($filasTotales[$i]['etiquetas'] != null){
          $array_etiquetas = $this->GetEtiquetas($filasTotales[$i]['etiquetas'],$comercio_id);
          dump($array_etiquetas);
          $this->StoreUpdateEtiquetas($product->id,2,"productos",$comercio_id,$array_etiquetas);
          }
          
        }
        
        if($product != null){
        // ACA CHEQUEA SI EL PRODUCTO ES VARIABLE O NO //
        $pvd_datos = productos_variaciones_datos::where('codigo_variacion', $filasTotales[$i]['cod_variacion'])->where('product_id', $product->id)->where('comercio_id', $comercio_id)->where('eliminado', 0)->first();
        if ($pvd_datos != null) {
          $this->referencia_id = $pvd_datos->referencia_variacion;
        } else {
          $this->referencia_id = Carbon::now()->format('dmYHis') . 'i' . $i . '-' . $comercio_id;
        }


        
        
        foreach ($filasTotales[$i] as $key => $r) {
          $var = explode("_", $key);
          ////// VARIACIONES //////

          ///// ACA VALIDA SI LA COLUMNA "VARIACION" Y "COD VARIACION" DEL EXCEL CONTIENEN REGISTROS  ////
          if (isset($filasTotales[$i]['variacion']) && isset($filasTotales[$i]['cod_variacion'])) {
            $this->cod_vari = $filasTotales[$i]['cod_variacion'];
            if (is_array($var) && $var[0] == "variacion") {
              $variaciones = explode("-", $r);
              $v_arr = [];
              $v_id_arr = [];
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
                    $productos_variaciones = productos_variaciones::where('codigo_variacion', $filasTotales[$i]['cod_variacion'])
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
                  'cost' => $filasTotales[$i]['costo'] ?? 0,
                  'precio_interno' => $precio_interno_variable,
                  'variaciones' => $v_arr,
                  'variaciones_id' => $v_id_arr,
                  'imagen' => $imagen ?? null,
                  'comercio_id' => $product->comercio_id,
                  'wc_push' => $venta_wocommerce,
                ]);
              } else {
                $pvd_datos->update([
                  'codigo_variacion' => $filasTotales[$i]['cod_variacion'],
                  'cost' => $filasTotales[$i]['costo'] ?? 0,
                  'precio_interno' => $precio_interno_variable,
                   'wc_push' => $venta_wocommerce,
                  'imagen' => $imagen ?? null
                ]);
              }
            }
          } else {
            $this->referencia_id = 0;
          }

          
          $this->StoreUpdateStock($var,$product,$comercio_id,$r,$id_almacen);
              
          $this->StoreUpdatePrecios($var,$product,$comercio_id,$r);
          
          $this->SetIvaProducto($product,$comercio_id);

        }
        
        }

       }      
       
       
        if ($i === (count($filasTotales) - 1)) {
          $newLocation = base_path("resources/excel-guardados/" . $this->fileName . ".xlsx");
          $moved = rename($this->filePath, $newLocation);
        }
        dump($i);
        importaciones::where('id', $this->import_id)->update([
          'proceso' => ($i + 1) . "/" . count($filasTotales) . "/procesando",
          'estado' => 2
        ]);


        

      }
    } catch (\Exception $e) {
      //Log::error('Error al procesar el trabajo: ' . $e->getMessage());
      \Log::error('Error al procesar el trabajo: ' . $e);
    }
  }


//  public function proveedores($rows, $comercio_id)
//  {
//      return 1;
//  }
//  public function categorias($rows, $comercio_id)
//  {
//    return 1;
//  }
//  public function almacen($rows, $comercio_id)
//  {
//    return 1;
//  }

public function proveedores($nombre_proveedor, $comercio_id)
  {
      
    if( ($nombre_proveedor == '') ||  $nombre_proveedor == 'Sin proveedor') {
    $nombre_proveedor = "Sin proveedor";
    $proveedor_id = 1;
    }
    else {
    $p = proveedores::where('nombre',$nombre_proveedor)->where('comercio_id',$comercio_id)->where('eliminado',0)->first();
    // si existe usa ese
                  
    if($p != null){
    $proveedor_id = $p->id;  
    } else {
                
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
                  
     $proveedor_id = $p->id;      
                        
     }
                  
  }

    return $proveedor_id;
  }

  public function categorias($nombre_categoria, $comercio_id)
  {
               
    if( ($nombre_categoria == '') ||  $nombre_categoria == 'Sin categoria') {
    $categoria_id = 1;
    }
     else {
    // si tiene otro nombre
    $c = Category::where('name',$nombre_categoria)->where('comercio_id',$comercio_id)->where('eliminado',0)->first();
    // si existe usa ese
                  
    if($c != null){
    $categoria_id = $c->id;  
    } else {
                      
    // si no existe lo crea
    $c = Category::create([
        'comercio_id' => $comercio_id,
        'name' => $nombre_categoria,
        'eliminado' => 0
    ]);
                  
    $categoria_id = $c->id;      
    }
                  
    }
    
    return $categoria_id;
    }
    
    
  public function almacen($nombre_almacen, $comercio_id)
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
  
  public function manejaStock($rows)
  {
    if ($rows == '') {
      return 'si';
    } else {
      return $rows;
    }
  }
  public function origen($origen)
  {
    if ($origen == '') {
      return 1;
    }
    if ($origen == 'compra') {
      return 1;
    }
    if ($origen == 'produccion') {
      return 2;
    }
  }
  public function ventaMostrador($vm)
  {
    if ($vm == 'no') {
      return 0;
    }
    if ($vm == 'si') {
      return 1;
    }
    if ($vm == '') {
      return 0;
    }
  }
  public function ventaEcommerce($ve)
  {
    if ($ve == 'no') {
      return 0;
    }
    if ($ve == 'si') {
      return 1;
    }
    if ($ve == '') {
      return 0;
    }
  }
  public function ventaWocommerce($vw)
  {

    if ($vw == 'no') {
      return 0;
    }
    if ($vw == 'si') {
      return 1;
    }
    if ($vw == '') {
      return 0;
    }
  }
  public function imagen($img, $rows, $comercio_id)
  {

        if ($img == '' || $img == null ) {
          return null;
        }

        if ($img != '' || $img == null) {
          return imagenes::where('name', $rows)->where('comercio_id', $comercio_id)->where('eliminado', 0)->first()->url;
        }
  }
  
  public function Pesable($row){
       if($row == "si"){$response = 1;}
       if($row == "no"){$response = 9;}
       if($row == null){$response = 9;}
       if(empty($row)){$response = 9;}
       var_dump($response);
       return $response;
  }
  
  
  public function ProductoTipo($row) {
  return $row ? 'v' : 's';
  }
  
  public function PrecioInternoSimple($precio_interno,$costo,$producto_tipo,$configuracion) {
      if($producto_tipo == "s") {
          if($configuracion == 1) {$valor = $costo;} else {$valor = $precio_interno;}
          return $valor ?? 0;
      } else {
          return 0;
      }
  }
  public function PrecioInternoVariable($precio_interno,$costo,$producto_tipo,$configuracion) {
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
    
    public function StoreUpdatePrecios($var,$product,$comercio_id,$r){
     
     
          ///// PRECIOS LISTA BASE ////////
          if (count($var) < 2) {
            if (is_array($var) && $var[0] == "precio") {

              $group = productos_lista_precios::updateOrCreate(
                [
                  'product_id'          => $product->id,
                  'referencia_variacion' => $this->referencia_id,
                  'lista_id'               => 0,
                ],
                [
                  'precio_lista'           => empty($r) ? 0 : $r,
                  'lista_id'               => 0,
                  'referencia_variacion'   => $this->referencia_id,
                  'product_id'            => $product->id,
                  'comercio_id'         => $comercio_id,
                ]
              );
            }
          }

          ///// PRECIOS OTRAS LISTAS ////////
          if (count($var) > 2) {
            if (is_array($var) && $var[1] == "precio" && is_numeric($var[0])) {


              $group = productos_lista_precios::updateOrCreate(
                [
                  'product_id'          => $product->id,
                  'referencia_variacion' => $this->referencia_id,
                  'lista_id'               => $var[0],
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
    
    public function StoreUpdateCostosYPrecioInterno($product,$costo,$precio_interno,$producto_tipo,$pvd_datos,$configuracion_precio_interno) {
     
     if($product->producto_tipo == "s"){
         $product->cost = $costo;
         if($configuracion_precio_interno == 1){
         $product->precio_interno = $costo;     
         } else {
         $product->precio_interno = $precio_interno;    
         }
         $product->save();
     } else {
         $pvd_datos->cost = $costo;
         if($configuracion_precio_interno == 1){
         $pvd_datos->precio_interno = $costo;
         } else {
         $pvd_datos->precio_interno = $precio_interno;     
         }
         $pvd_datos->save();
     }
     
        
    }
    
    public function StoreUpdateStock($var,$product,$comercio_id,$r,$id_almacen){
    
              ////// STOCK CASA CENTRAL ////
          if (count($var) < 2) {
            if (is_array($var) && $var[0] == "stock") {

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
          
          
          ///// STOCK SUCURSALES   /////
          if (count($var) > 2) {
            if (is_array($var) && $var[1] == "stock" && is_numeric($var[0])) {


              $pss_stock = productos_stock_sucursales::join('sucursales', 'sucursales.sucursal_id', 'productos_stock_sucursales.sucursal_id')
                ->where('productos_stock_sucursales.product_id', $product->id)
                ->where('productos_stock_sucursales.comercio_id', $comercio_id)
                ->where('productos_stock_sucursales.sucursal_id', $var[0])
                ->where('productos_stock_sucursales.referencia_variacion', $this->referencia_id)
                ->where('productos_stock_sucursales.eliminado', 0)
                ->where('sucursales.eliminado', 0)
                ->first();
    
              if ($pss_stock != null) {
                $dif = $pss_stock->stock_real - $pss_stock->stock;
                $this->sd2 = (empty($r) ? 0 : $r) - $dif;
                $cantidad_movimiento = (empty($r) ? 0 : $r) - $pss_stock->stock_real;
              } else {
                $this->sd2 = (empty($r) ? 0 : $r);
                $cantidad_movimiento = (empty($r) ? 0 : $r);
              }
    
              $group = productos_stock_sucursales::updateOrCreate(
                [
                  'product_id' => $product->id,
                  'comercio_id' => $comercio_id,
                  'sucursal_id' => $var[0],
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
              
              // Poner el historico
              
              $this->HistoricoStock($product->id,$this->referencia_id,$cantidad_movimiento,(empty($r) ? 0 : $r),$var[0]);
		
            }
          }
          
          ////// ALMACEN SUCURSALES ////
          if (count($var) > 2) {
              
            if (is_array($var) && $var[1] == "almacen" && is_numeric($var[0])) {
                
                $actualizar_almacen = productos_stock_sucursales::where('productos_stock_sucursales.product_id', $product->id)
                ->where('productos_stock_sucursales.comercio_id', $comercio_id)
                ->where('productos_stock_sucursales.sucursal_id', $var[0])
                ->where('productos_stock_sucursales.referencia_variacion', $this->referencia_id)
                ->where('productos_stock_sucursales.eliminado', 0)
                ->select('productos_stock_sucursales.almacen_id','productos_stock_sucursales.id')
                ->first();
                
                $id_almacen_sucursal = $this->almacen($r, $comercio_id); //////////// ALMACEN //////////////
                $actualizar_almacen->update([
                    'almacen_id' => $id_almacen_sucursal
                ]);
                dump($actualizar_almacen);
                
                
            }
          }
          
    }
    
    public function SetReferenciaId($product,$codigo_variacion,$comercio_id){
    $this->referencia_id = 0;    
 
    // producto variable
    if($product->producto_tipo == "v") {
    $pvd_datos = productos_variaciones_datos::where('codigo_variacion', $codigo_variacion)->where('product_id', $product->id)->where('comercio_id', $comercio_id)->where('eliminado', 0)->first();
    var_dump($codigo_variacion,$product->id,$comercio_id);
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
    $configuracion_precio_interno = $u->costo_igual_precio;
    return $configuracion_precio_interno;
    }
    
    public function SetIvaProducto($product,$comercio_id){
    
    // sucursales
    $sucursales = sucursales::where('casa_central_id',$comercio_id)->get();
    $df_casa_central = datos_facturacion::where('comercio_id',$comercio_id)->first();
    if($df_casa_central != null){
        if($df_casa_central->iva_defecto  == null) {$iva_casa_central = 0;}  else {$iva_casa_central = $df_casa_central->iva_defecto;}
    } else {$iva_casa_central = 0;}
    
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
          'iva' => $iva_casa_central
        ]
    );  
    
    foreach($sucursales as $sucursal){
        // iva defecto de casa sucursal
        $df = datos_facturacion::where('comercio_id',$sucursal->sucursal_id)->first();
        if($df != null){
            if($df->iva_defecto  == null) {$iva = 0;}  else {$iva = $df->iva_defecto;}
        } else {$iva = 0;}
        
        $group = productos_ivas::updateOrCreate(
        [
          'product_id' => $product->id,
          'comercio_id' => $sucursal->casa_central_id,
          'sucursal_id' => $sucursal->sucursal_id
        ],
        [
          'product_id' => $product->id,
          'comercio_id' => $sucursal->casa_central_id,
          'sucursal_id' => $sucursal->sucursal_id,
          'iva' => $iva
        ]
    );        
    }

    }
    
}
