<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Events\UpdateProgressImport;
use Illuminate\Support\Facades\Event;

use App\Models\importaciones;

use App\Models\Category;
use App\Models\Product;
use App\Models\proveedores;
use App\Models\historico_stock;
use App\Models\productos_variaciones;
use App\Models\productos_variaciones_datos;
use App\Models\variaciones;
use App\Models\productos_stock_sucursales;
use App\Models\productos_lista_precios;
use App\Models\seccionalmacen;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\OnEachRow;
use App\Models\imagenes;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Carbon\Carbon;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


use Livewire\Component;


class ImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $import_id, $data, $comercio_id_import, $filePath, $fileName;

    public $comercio_id, $prov, $proveed, $alm, $al, $categ, $movimiento_stock, $cat, $costo, $stock, $st, $cost, $inv_minimo, $in_minimo, $inv_ideal, $in_ideal, $prec, $pre, $maneja_stock;

   
    public $tries = 1;

    public $timeout = 6000;

    protected $datosImportar;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
   /* public function __construct()
    {
        
    }*/

    public function __construct($importId, $data, $comercioId, $filePath, $fileName) 
    {
        $this->import_id = $importId;
        $this->data = $data;
        $this->comercio_id_import = $comercioId;
        $this->filePath = $filePath;
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

     
      $filasTotales = count($this->data);
      $rows = $this->data;

      $comercio_id = $this->comercio_id_import;   

      $importacionActual = importaciones::where('id', $this->import_id)->first();

     try {

            for($i = 0; $i < $filasTotales; $i++){  
              
              $prov = $rows[$i]['proveedor'];            

             

              if($prov == '') {
                  $proved = "Sin proveedor";
              }
              else {
                  $proved = $rows[$i]['proveedor'];
              }

              $proveedor = proveedores::updateOrCreate(
                  [ 'nombre' => $proved,
                    'comercio_id' => $comercio_id
                  ],
                  [
                    'nombre' => $proved,
                    'comercio_id' => $comercio_id
                  ]
              );

              //////////// PROVEEDOR //////////////

              $cat = $rows[$i]['categoria'];

              if($cat == '') {
                  $categ = "Sin categoria";
              }
              else {
                  $categ = $rows[$i]['categoria'];
              }

              $categoria = Category::updateOrCreate(
                  [
                    'name'   => $categ,
                    'eliminado' => 0,
                    'comercio_id'     => $comercio_id
                  ],
                  [
                    'name'   => $categ,
                    'comercio_id'     => $comercio_id
                  ]
              );

              //////////// ALMACEN //////////////

              $al = $rows[$i]['almacen'];

              if($al == '') {
                $alm = "Sin almacen";
              }
              else {
                $alm = $rows[$i]['almacen'];
              }

              $almacen = seccionalmacen::updateOrCreate(
              [
                'nombre'   => $alm,
                'comercio_id'     => $comercio_id
              ],[
                'nombre'   => $alm,
                'comercio_id'     => $comercio_id
                  ]
              );

              //////////// STOCK DESCUBIERTO //////////////

              $maneja = $rows[$i]['maneja_stock'];

              if($maneja == '') {
                $maneja_stock = 'si';
              }
              else {
                $maneja_stock = $rows[$i]['maneja_stock'];
              }

            
              
              //////////// ORIGEN //////////////

              $origen = $rows[$i]['origen'];

              if($origen == '') {
                $origen = 1;
              }
              if($origen == 'compra') {
                $origen = 1;
              }
              if($origen == 'produccion') {
                $origen = 2;
              }

              //////////// VENTA MOSTRADOR //////////////

              $vm = $rows[$i]['venta_mostrador'];

              if($vm == 'no') {
                    $venta_mostrador = 0;
              }
              if($vm == 'si') {
                    $venta_mostrador = 1;
              }
              if($vm == '') {
                    $venta_mostrador = 0;
              }


              //////////// VENTA ECOMMERCE //////////////

              $ve = $rows[$i]['venta_ecommerce'];

              if($ve == 'no') {
                    $venta_ecommerce = 0;
              }
              if($ve == 'si') {
                    $venta_ecommerce = 1;
              }

              if($ve == '') {
                    $venta_ecommerce = 0;
              }

              //////////// VENTA WOOCOMMERCE //////////////

              $vw = $rows[$i]['venta_wocommerce'];

              if($vw == 'no') {
                    $venta_wocommerce = 0;
              }
              if($vw == 'si') {
                  $venta_wocommerce = 1;
              }

              if($vw == '') {
                  $venta_wocommerce = 0;
              }
          
              //////////// IMAGENES //////////////

          
              $img = $rows[$i]['imagen'];

              if($img == '') {
                  $imagen = null;
              }

              if($img != '') {
              
              //var_dump($rows[$i]['imagen'], $comercio_id);
              
                $imagen = imagenes::where('name',$rows[$i]['imagen'])->where('comercio_id',$comercio_id)->where('eliminado',0)->first();

                            
                $imagen = $imagen->url;
            
              }

            


              if(Product::where('barcode',$rows[$i]['codigo'])->where('comercio_id', $comercio_id)->where('eliminado',0)->exists())
              { 
                  $this->product_exist = 1; 
                  
                  $product = Product::where('barcode',$rows[$i]['codigo'])->where('comercio_id', $comercio_id)->where('eliminado',0)->first();
                  
                  if($product->producto_tipo == "s") {
                      $pi = $rows[$i]['precio_interno'];
                  } else {
                      $pi = null;
                  }
                  
                  $product->update([
                    'name'            => $rows[$i]['nombre'],
                    'barcode'         => $rows[$i]['codigo'],
                    'cost'            => $rows[$i]['costo'] ?? 0,
                    'tipo_producto' => $origen,
                    'producto_tipo' => $rows[$i]['variacion'] ? 'v' : 's' ,
                    'price'           => 0,
                    'precio_interno' => $pi,
                    'stock'           => 0,
                    'alerts'          => empty($rows[$i]['inv_minimo']) ? 0 : $rows[$i]['inv_minimo'],
                    'mostrador_canal' => $venta_mostrador,
                    'ecommerce_canal' => $venta_ecommerce,
                    'wc_canal' => $venta_wocommerce,
                    'comercio_id' => $comercio_id,
                    'stock_descubierto'          => $maneja_stock,
                    'seccionalmacen_id'     => seccionalmacen::where('nombre', $alm)->where('comercio_id', $comercio_id)->first()->id,
                    'category_id'     => Category::where('name', $categ)->where('eliminado',0)->where('comercio_id', $comercio_id)->first()->id,
                    'proveedor_id'     => proveedores::where('nombre', $proved)->where('eliminado',0)->where('comercio_id', $comercio_id)->first()->id,
                    'eliminado' => '0',
                    'image' => $imagen
                    ]);              
                  
              } else {
                  $this->product_exist = 0;
                  
                  if($product->producto_tipo == "s") {
                      $pi = $rows[$i]['precio_interno'];
                  } else {
                      $pi = null;
                  }
                  
                  $product = Product::create([
                    'name'            => $rows[$i]['nombre'],
                    'barcode'         => $rows[$i]['codigo'],
                    'cost'            => $rows[$i]['costo'] ?? 0,
                    'tipo_producto' => 1,
                    'comercio_id' => $comercio_id,
                    'producto_tipo' => $rows[$i]['variacion'] ? 'v' : 's' ,
                    'price'           => 0,
                    'precio_interno' => $pi,
                    'stock'           => 0,
                    'alerts'          => empty($rows[$i]['inv_minimo']) ? 0 : $rows[$i]['inv_minimo'],
                    'mostrador_canal' => $venta_mostrador,
                    'ecommerce_canal' => $venta_ecommerce,
                    'wc_canal' => $venta_wocommerce,
                    'stock_descubierto'          => $maneja_stock,
                    'seccionalmacen_id'     => seccionalmacen::where('nombre', $alm)->where('comercio_id', $comercio_id)->first()->id,
                    'category_id'     => Category::where('name', $categ)->where('eliminado',0)->where('comercio_id', $comercio_id)->first()->id,
                    'proveedor_id'     => proveedores::where('nombre', $proved)->where('eliminado',0)->where('comercio_id', $comercio_id)->first()->id,
                    'eliminado' => '0',
                    'image' => $imagen
                    ]);
                  
              }
              
              // ACA CHEQUEA SI EL PRODUCTO ES VARIABLE O NO //
              
              $pvd_datos = productos_variaciones_datos::where('codigo_variacion',$rows[$i]['cod_variacion'])->where('product_id', $product->id)->where('comercio_id', $comercio_id)->where('eliminado',0)->first();
              
            
              
              if($pvd_datos != null) {
                  $this->referencia_id = $pvd_datos->referencia_variacion;
              } else {
              // $this->referencia_id = Carbon::now()->format('dmYHis').'i'. $rowsIndex.'-'.$comercio_id;  
              
                $this->referencia_id = Carbon::now()->format('dmYHis').'i'. $i .'-'.$comercio_id; 
              }

              ///
            
              foreach ($rows[$i] as $key => $r) {
                  //
                  //$this->increaseProgress();
                  //
                  $var = explode("_", $key);

                ////// VARIACIONES //////

                ///// ACA VALIDA SI LA COLUMNA "VARIACION" Y "COD VARIACION" DEL EXCEL CONTIENEN REGISTROS  ////

              
              if(isset($rows[$i]['variacion']) && isset($rows[$i]['cod_variacion'])) {
                  
                  $this->cod_vari = $rows[$i]['cod_variacion'];
                  
                  if( is_array($var) && $var[0] == "variacion" ){

                      $variaciones = explode("-", $r);

                      $v_arr = [];
                      $v_id_arr = [];
          
                
              
                  
                if($pvd_datos == null) {            
                
                      //   NO EXISTE LA VARIACION (CONVINACION DE VARIACIONES)  //
                    
                      foreach($variaciones as $vari => $j) {
                            
                        // $nombre_var = trim($j);

                        if ($j[0] == ' ') {            
                            $nombre_var = ltrim($j, " ");// Eliminar primer campo      
                            
                        } else {
                          $nombre_var = $j;
                        }
                    
                        if($j[strlen($j)-1] == ' '){            
                              $nombre_var = substr($nombre_var, 0, -1); // Eliminar ultimo campo            
                        } else {
                            $nombre_var = $nombre_var;
                        }

                      
                        //$nombre_var =str_replace(' ', '', $j);

                        $variationes = variaciones::join('atributos','atributos.id','variaciones.atributo_id')
                        ->select('variaciones.*')
                        ->where('variaciones.nombre',$nombre_var)
                        ->where('variaciones.comercio_id',$comercio_id)
                        ->where('variaciones.eliminado', 0)
                        ->where('atributos.eliminado', 0)
                        ->first();

                                          

                        if($variationes != null) {                
                        
                            $productos_variaciones = productos_variaciones::where('codigo_variacion', $rows[$i]['cod_variacion'])
                            ->where('variacion_id', $variationes->id)
                            ->where('producto_id',$product->id)
                            ->first();        
                            
                            
                            
                            if($productos_variaciones == null) {

                             

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
 
                        }else{
                          $id_var = null;
                        }

                       /*if($id_var !== null ){
                        array_push($v_id_arr,$id_var);
                       }*/
                    
                        array_push($v_id_arr,$id_var);
                        array_push($v_arr,$nombre_var);
                      
                      }

                    
            
              
                      natsort($v_id_arr);
                      $v_id_arr = implode("," , $v_id_arr);
                      $v_arr = implode(" - " , $v_arr);

                        productos_variaciones_datos::create([
                              'product_id' => $product->id,
                              'codigo_variacion' => $this->cod_vari,
                              'referencia_variacion' => $this->referencia_id,
                              'cost' => $rows[$i]['costo'] ?? 0,
                              'precio_interno' => $rows[$i]['precio_interno'] ?? 0,
                              'variaciones' => $v_arr,
                              'variaciones_id' => $v_id_arr,
                              'imagen' => $imagen ?? null,
                              'comercio_id' => $product->comercio_id
                          ]);

                    } else {
                    
              
                    $pvd_datos->update([
                        'codigo_variacion' => $rows[$i]['cod_variacion'],
                        'cost' => $rows[$i]['costo'] ?? 0,
                        'precio_interno' => $rows[$i]['precio_interno'] ?? 0,
                        'imagen' => $imagen ?? null
                        
                        ]);                   
                  }
                }
          
              
              } else {
                $this->referencia_id = 0;
              }

            
                ////// STOCK CASA CENTRAL ////
                if( count($var) < 2 ){

                if( is_array($var) && $var[0] == "stock" ){

                $pss_stock = productos_stock_sucursales::where('product_id',$product->id)
                ->where('comercio_id',$comercio_id)
                ->where('sucursal_id',0)
                ->where('referencia_variacion',$this->referencia_id)
                ->where('eliminado',0)
                ->first();
                
                $dif = $pss_stock->stock_real - $pss_stock->stock;
                $this->sd1 = (empty($r) ? 0 : $r) - $dif;

                $group = productos_stock_sucursales::updateOrCreate(
                      [
                        'product_id' => $product->id,
                        'comercio_id' => $comercio_id,
                        'sucursal_id' => 0,
                        'eliminado' => 0,
                        'referencia_variacion' => $this->referencia_id,
                      ],[
                        'stock' => $this->sd1,
                        'stock_real' => empty($r) ? 0 : $r,
                        'referencia_variacion'   => $this->referencia_id,
                        'product_id'  => $product->id,
                        'comercio_id' => $comercio_id,
                        ]
                      );


              }

              }

              
                ///// STOCK SUCURSALES   /////

                  if( count($var) > 2 ){


                if( is_array($var) && $var[1] == "stock" && is_numeric($var[0]) ){

                $pss_stock = productos_stock_sucursales::where('product_id',$product->id)
                ->where('comercio_id',$comercio_id)
                ->where('sucursal_id',$var[0])
                ->where('referencia_variacion',$this->referencia_id)
                ->where('eliminado',0)
                ->first();
                
                $dif = $pss_stock->stock_real - $pss_stock->stock;
                $this->sd2 = (empty($r) ? 0 : $r) - $dif;

                  $group = productos_stock_sucursales::updateOrCreate(
                        [
                          'product_id' => $product->id,
                          'comercio_id' => $comercio_id,
                          'sucursal_id' => $var[0],
                          'referencia_variacion' => $this->referencia_id,
                        ],[
                          'stock' => $this->sd2,    
                          'stock_real' => empty($r) ? 0 : $r,
                          'referencia_variacion'   => $this->referencia_id,
                          'product_id'  => $product->id,
                          'comercio_id' => $comercio_id,
                          ]
                        );
                }



              }


            
                ///// PRECIOS LISTA BASE ////////

                if( count($var) < 2 ){


                if( is_array($var) && $var[0] == "precio" ){

                  $group = productos_lista_precios::updateOrCreate(
                      [
                        'product_id'          => $product->id,
                        'referencia_variacion' => $this->referencia_id,
                        'lista_id'               => 0,
                      ],[
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

              if( count($var) > 2 ){


              if( is_array($var) && $var[1] == "precio" && is_numeric($var[0]) ){


                $group = productos_lista_precios::updateOrCreate(
                    [
                      'product_id'          => $product->id,
                      'referencia_variacion' => $this->referencia_id,
                      'lista_id'               => $var[0],
                    ],[
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
                  
                $importacionActual->proceso =  ($i + 1) . "/" . $filasTotales . "/procesando";               

                if($i === ($filasTotales - 1)){
                  $importacionActual->estado = 2;      
                  $newLocation = base_path("resources/excel-guardados/". $this->fileName .".xlsx");
                  $moved = rename($this->filePath, $newLocation);                      
                }
                $importacionActual->save();
            
            }
      } catch (\Exception $e) {
          //Log::error('Error al procesar el trabajo: ' . $e->getMessage());
          \Log::error('Error al procesar el trabajo: ' . $e->getMessage());
      }     

  
    }
}

