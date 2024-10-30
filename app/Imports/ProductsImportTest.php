<?php

namespace App\Imports;

use App\Models\importaciones;

use App\Models\Category;
use App\Models\Product;
use App\Models\proveedores;
use App\Models\historico_stock;
use App\Models\productos_variaciones;
use App\Models\productos_variaciones_datos;
use App\Models\variaciones;
use App\Models\productos_stock_sucursales;
use App\Models\sucursales;
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


class ProductsImportTest implements OnEachRow, SkipsEmptyRows, WithHeadingRow,  WithValidation, SkipsOnError
{
    use SkipsErrors;

    public $comercio_id, $prov, $proveed, $alm, $al, $categ, $cat, $costo, $stock, $st, $cost, $inv_minimo, $in_minimo, $inv_ideal, $in_ideal, $prec, $pre, $maneja_stock;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */




   public function onRow(Row $row)
    {
        
     
        if(Auth::user()->comercio_id != 1)
        $comercio_id = Auth::user()->comercio_id;
        else
        $comercio_id = Auth::user()->id;
        
        // Aca en el job se pasaria el id del comercio que viene de la base de datos
        $comercio_id = $comercio_id;

        $rowIndex = $row->getIndex();
        $rows    = $row->toArray();
        
              // SUCURSALES 
              
              $sucursales = sucursales::where('casa_central_id',$comercio_id)->where('eliminado',0)->get();

              if(0 < $sucursales->count()) {
                $precio_interno =   $rows['precio_interno'] ?? 0; 
              } else {
                 $precio_interno = null;  
              }
              //////////// PROVEEDOR //////////////
              
              if( ($rows['proveedor'] == '') ||  $rows['proveedor'] == 'Sin proveedor') {
                  $nombre_proveedor = "Sin proveedor";
                  $proveedor_id = 1;
              }
              else {
                  $nombre_proveedor = $rows['proveedor'];
              
                  // si tiene otro nombre
                  $p = proveedores::where('nombre',$nombre_proveedor)->where('comercio_id',$comercio_id)->where('eliminado',0)->first();
                  
                  // si existe usa ese
                  
                  if($p != null){
                  $proveedor_id = $p->id;  
                  } else {
                
                  $ultimo_id = proveedores::where('comercio_id',$comercio_id)->max('id');
                  $ultimo_proveedor = proveedores::find($ultimo_id);
                    
                  if($ultimo_proveedor != null){
                  $cod_proveedor = $ultimo_proveedor->id_proveedor + 1;
                  } else {
                  $cod_proveedor = 1;    
                  }
                  
                  // si no existe lo crea
                  $p = proveedores::create([
                      'comercio_id' => $comercio_id,
                      'nombre' => $nombre_proveedor,
                      'eliminado' => 0,
                      'id_proveedor' => $cod_proveedor
                      ]);
                  
                  if($p != null) {
                  $proveedor_id = $p->id;      
                  } else {
                  $proveedor_id = 1;      
                  }
                        
                  }
                  
              }
              
              
              //////////// FIN PROVEEDOR ////////////// 
            
              //////////// CATEGORIA //////////////
              
              if( ($rows['categoria'] == '') ||  $rows['categoria'] == 'Sin categoria') {
                  $nombre_categoria = "Sin categoria";
                  $categoria_id = 1;
              }
              else {
                  
                  
                  $nombre_categoria = $rows['categoria'];
              
                  // si tiene otro nombre
                  $c = Category::where('nombre',$nombre_categoria)->where('comercio_id',$comercio_id)->where('eliminado',0)->first();
                  
                  // si existe usa ese
                  
                  if($c != null){
                  $categoria_id = $c->id;  
                  } else {
                      
                  // si no existe lo crea
                  $c = Category::create([
                      'comercio_id' => $comercio_id,
                      'nombre' => $nombre_categoria,
                      'eliminado' => 0
                      ]);
                  
                  if($c != null){
                  $categoria_id = $c->id;      
                  } else {$categoria_id = 1;}
                        
                  }
                  
              }
              
              
              //////////// FIN CATEGORIA ////////////// 
            
              //////////// ALMACEN //////////////
              
              if( ($rows['almacen'] == '') ||  $rows['almacen'] == 'Sin almacen') {
                  $nombre_almacen = "Sin almacen";
                  $almacen_id = 1;
              }
              else {
                  $nombre_almacen = $rows['almacen'];
              
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
                  
                  if($a != null){
                  $almacen_id = $a->id;       
                  } else {
                  $almacen_id = 1;      
                  }
                        
                  }
                  
              }
              
              //////////// FIN ALMACEN ////////////// 
              
              //////////// STOCK DESCUBIERTO //////////////

              $maneja = $rows['maneja_stock'];

              if($maneja == '') {
                $maneja_stock = 'si';
              }
              else {
                $maneja_stock = $rows['maneja_stock'];
              }

            
              
              //////////// ORIGEN //////////////

              $origen = $rows['origen'];

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

              $vm = $rows['venta_mostrador'];

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

              $ve = $rows['venta_ecommerce'];

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

              $vw = $rows['venta_wocommerce'];

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

          
              $img = $rows['imagen'];

              if($img == '') {
                  $imagen = null;
              }

              if($img != '') {
              
              //var_dump($rows['imagen'], $comercio_id);
              
                $imagen = imagenes::where('name',$rows['imagen'])->where('comercio_id',$comercio_id)->where('eliminado',0)->first();

                if($imagen != null){            
                $imagen = $imagen->url;
                } else {
                $imagen = null;
                }
            
              }

            
              $producto_tipo = $rows['variacion'] ? 'v' : 's';

              if(Product::where('barcode',$rows['codigo'])->where('comercio_id', $comercio_id)->where('eliminado',0)->exists())
              { 
                  $this->product_exist = 1; 
                  
                  $product = Product::where('barcode',$rows['codigo'])->where('comercio_id', $comercio_id)->where('eliminado',0)->first();
                  
                  
                  $product->update([
                    'name'            => $rows['nombre'],
                    'barcode'         => $rows['codigo'],
                    'cost'            => $rows['costo'] ?? 0,
                    'tipo_producto' => $origen,
                    'producto_tipo' => $producto_tipo,
                    'price'           => 0,
                    'precio_interno' => $producto_tipo == "s"? $precio_interno : 0,
                    'stock'           => 0,
                    'alerts'          => empty($rows['inv_minimo']) ? 0 : $rows['inv_minimo'],
                    'mostrador_canal' => $venta_mostrador,
                    'ecommerce_canal' => $venta_ecommerce,
                    'wc_canal' => $venta_wocommerce,
                    'comercio_id' => $comercio_id,
                    'stock_descubierto'          => $maneja_stock,
                    'seccionalmacen_id'     => $almacen_id,
                    'category_id'     =>$categoria_id,
                    'proveedor_id'     => $proveedor_id,
                    'eliminado' => '0',
                    'image' => $imagen
                    ]);              
                  
              } else {
                  $this->product_exist = 0;
                  
                  
                  $product = Product::create([
                    'name'            => $rows['nombre'],
                    'barcode'         => $rows['codigo'],
                    'cost'            => $rows['costo'] ?? 0,
                    'tipo_producto' => 1,
                    'comercio_id' => $comercio_id,
                    'producto_tipo' => $producto_tipo,
                    'price'           => 0,
                    'precio_interno' => $producto_tipo == "s"? $precio_interno : 0,
                    'stock'           => 0,
                    'alerts'          => empty($rows['inv_minimo']) ? 0 : $rows['inv_minimo'],
                    'mostrador_canal' => $venta_mostrador,
                    'ecommerce_canal' => $venta_ecommerce,
                    'wc_canal' => $venta_wocommerce,
                    'stock_descubierto'          => $maneja_stock,
                    'seccionalmacen_id'     => $almacen_id,
                    'category_id'     => $categoria_id,
                    'proveedor_id'     => $proveedor_id,
                    'eliminado' => '0',
                    'image' => $imagen
                    ]);
                  
              }
              
              // ACA CHEQUEA SI EL PRODUCTO ES VARIABLE O NO //
              
              $pvd_datos = productos_variaciones_datos::where('codigo_variacion',$rows['cod_variacion'])->where('product_id', $product->id)->where('comercio_id', $comercio_id)->where('eliminado',0)->first();
              
              
              if($pvd_datos != null) {
                  $this->referencia_id = $pvd_datos->referencia_variacion;
              } else {
              // $this->referencia_id = Carbon::now()->format('dmYHis').'i'. $rowsIndex.'-'.$comercio_id;  
              
                $this->referencia_id = Carbon::now()->format('dmYHis').'i'. $rowIndex .'-'.$comercio_id; 
              }

              ///
            
              foreach ($rows as $key => $r) {
                  
                  //
                  //$this->increaseProgress();
                  //
                  $var = explode("_", $key);

                ////// VARIACIONES //////

                ///// ACA VALIDA SI LA COLUMNA "VARIACION" Y "COD VARIACION" DEL EXCEL CONTIENEN REGISTROS  ////

             // dd(isset($rows['variacion']) && isset($rows['cod_variacion'])); 
              
              if(isset($rows['variacion']) && isset($rows['cod_variacion'])) {
                  
                  $this->cod_vari = $rows['cod_variacion'];
                  
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
                        
                            $productos_variaciones = productos_variaciones::where('codigo_variacion', $rows['cod_variacion'])
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
                              'cost' => $rows['costo'] ?? 0,
                              'precio_interno' => $producto_tipo == "v"? $precio_interno : 0,
                              'variaciones' => $v_arr,
                              'variaciones_id' => $v_id_arr,
                              'imagen' => $imagen ?? null,
                              'comercio_id' => $product->comercio_id
                          ]);

                    } else {
                    
              
                    $pvd_datos->update([
                        'codigo_variacion' => $rows['cod_variacion'],
                        'cost' => $rows['costo'] ?? 0,
                        'precio_interno' => $producto_tipo == "v"? $precio_interno : 0,
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
                
                if($pss_stock != null) {
                $dif = $pss_stock->stock_real - $pss_stock->stock;
                $this->sd1 = (empty($r) ? 0 : $r) - $dif;
                } else {
                $this->sd1 = (empty($r) ? 0 : $r);
                }

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

                
                $pss_stock = productos_stock_sucursales::join('sucursales','sucursales.sucursal_id','productos_stock_sucursales.sucursal_id')
                ->where('productos_stock_sucursales.product_id',$product->id)
                ->where('productos_stock_sucursales.comercio_id',$comercio_id)
                ->where('productos_stock_sucursales.sucursal_id',$var[0])
                ->where('productos_stock_sucursales.referencia_variacion',$this->referencia_id)
                ->where('productos_stock_sucursales.eliminado',0)
                ->where('sucursales.eliminado',0)
                ->first();
                
                if($pss_stock != null) {
                $dif = $pss_stock->stock_real - $pss_stock->stock;
                $this->sd2 = (empty($r) ? 0 : $r) - $dif;
                } else {
                $this->sd2 = (empty($r) ? 0 : $r);    
                }

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
         
    }

    //encabezados del archivo excel
    public function rules(): array
    {
        return [
        ];
    }

    public function customValidationMessages()
{
    return [

    ];
}







}
