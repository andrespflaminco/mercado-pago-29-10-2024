<?php

namespace App\Imports;

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
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductsImport implements OnEachRow, SkipsEmptyRows,ShouldQueue, WithHeadingRow,  WithValidation, SkipsOnError, WithBatchInserts, WithChunkReading, WithCalculatedFormulas
{
    use SkipsErrors;

    public $comercio_id, $prov, $proveed, $alm, $al, $categ, $movimiento_stock, $cat, $costo, $stock, $st, $cost, $inv_minimo, $in_minimo, $inv_ideal, $in_ideal, $prec, $pre, $maneja_stock;

    public $i = 1;

    public function __construct($comercio_id, $headings)
    {

      $this->comercio_id = $comercio_id;
      $this->headings = $headings;

    }


    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null

     */

   public function onRow(Row $row)
    {
      $rowIndex = $row->getIndex();
      $row      = $row->toArray();

        $comercio_id = intval($this->comercio_id);
        $usuario_id = intval($this->comercio_id);


        //////////// PROVEEDOR //////////////

        $prov = $row['proveedor'];

        if($prov == '') {
        $proved = "Sin proveedor";
                        }
                        else {
                          $proved = $row['proveedor'];
                        }


                        $proveedor = proveedores::updateOrCreate(
                            [
                              'nombre'   => $proved,
                              'comercio_id'     => $comercio_id
                            ],[
                              'nombre'   => $proved,
                              'comercio_id'     => $comercio_id
                                ]
                        );

            //////////// PROVEEDOR //////////////

                $cat = $row['categoria'];

                if($cat == '') {
                $categ = "Sin categoria";
                }
                else {
                $categ = $row['categoria'];
                }

                $categoria = Category::updateOrCreate(
                [
                  'name'   => $categ,
                  'comercio_id'     => $comercio_id
                    ],[
                  'name'   => $categ,
                  'comercio_id'     => $comercio_id
                    ]
            );

            //////////// ALMACEN //////////////

            $al = $row['almacen'];

            if($al == '') {
              $alm = "Sin almacen";
            }
            else {
              $alm = $row['almacen'];
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

            $maneja = $row['maneja_stock'];

            if($maneja == '') {
              $maneja_stock = 'si';
            }
            else {
              $maneja_stock = $row['maneja_stock'];
            }

            //////////// VENTA MOSTRADOR //////////////

            $vm = $row['venta_mostrador'];

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

            $ve = $row['venta_ecommerce'];

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

            $vw = $row['venta_wocommerce'];

            if($vw == 'no') {
            $venta_wocommerce = 0;
            }
            if($vw == 'si') {
            $venta_wocommerce = 1;
            }

            if($vw == '') {
            $venta_wocommerce = 0;
            }




            if(Product::where('barcode',$row['codigo'])->where('comercio_id', $comercio_id)->where('eliminado',0)->exists())
            { $this->product_exist = 1; } else { $this->product_exist = 0;}


            $product = Product::updateOrCreate(
                [
                  'barcode'            => $row['codigo'],
                  'comercio_id'            => $comercio_id,
                  'eliminado'            => 0,
                ],[
                  'name'            => $row['nombre'],
                  'barcode'         => $row['codigo'],
                  'cost'            => 0,
                  'tipo_producto' => 1,
                  'producto_tipo' => $row['variacion'] ? 'v' : 's' ,
                  'price'           => 0,
                  'stock'           => 0,
                  'alerts'          => empty($row['inv_minimo']) ? 0 : $row['inv_minimo'],
                  'mostrador_canal' => $venta_mostrador,
                  'ecommerce_canal' => $venta_ecommerce,
                  'wc_canal' => $venta_wocommerce,
                  'stock_descubierto'          => $maneja_stock,
                  'seccionalmacen_id'     => seccionalmacen::where('nombre', $alm)->where('comercio_id', $comercio_id)->first()->id,
                  'category_id'     => Category::where('name', $categ)->where('comercio_id', $comercio_id)->first()->id,
                  'proveedor_id'     => proveedores::where('nombre', $proved)->where('comercio_id', $comercio_id)->first()->id,
                  'eliminado' => '0'
                 ]
            );

            $this->referencia_id = Carbon::now()->format('dmYHis').'i'.$rowIndex.'-'.$comercio_id;

            foreach ($row as $key => $r) {

            $var = explode("_", $key);

            ////// VARIACIONES //////

            ///// ACA EN VEZ DE VALIDAR SI ESTAN LLENOS O NO, VALIDAR SI LA CONVINACION DE VARIACIONES EXISTE, EN CASO DE EXISTIR ACTUALIZAR, SINO CREARLA ////

            if(isset($row['variacion'] && empty($row['cod_variacion'])) {

            if( count($var) < 2 ){

            if( is_array($var) && $var[0] == "variacion" ){

            $variaciones = explode("-", $r);

            $v_arr = [];

            foreach($variaciones as $vari => $j) {

            $nombre_var = trim($j);

//            $nombre_var =str_replace(' ', '', $j);

            $variationes = variaciones::join('atributos','atributos.id','variaciones.atributo_id')
            ->select('variaciones.*')
            ->where('variaciones.nombre',$nombre_var)
            ->where('variaciones.comercio_id',$comercio_id)
            ->where('variaciones.eliminado', 0)
            ->where('atributos.eliminado', 0)
            ->first();


            if($variationes != null) {

            $productos_variaciones = productos_variaciones::where('referencia_id', $row['cod_variacion'])
            ->where('variacion_id', $variationes->id)
            ->where('producto_id',$product->id)
            ->first();


            if($productos_variaciones == null) {

            $variations = productos_variaciones::create([
                      'producto_id' => $product->id,
                      'variacion_id' => $variationes->id,
                      'comercio_id' => $comercio_id,
                      'atributo_id' => $variationes->atributo_id,
                      'referencia_id' => $row['cod_variacion'] ?? $this->referencia_id,
                     ]);

            }


            }

            array_push($v_arr,$nombre_var);

            }

            $v_arr = implode(" - " , $v_arr);


              productos_variaciones_datos::create([
            		'product_id' => $product->id,
            		'referencia_variacion' => $this->referencia_id,
            		'variaciones' => $v_arr,
            		'comercio_id' => $product->comercio_id
            	]);

            }
            }

          } else {
            $this->referencia_id = $row['cod_variacion'] ?? 0;
          }
            ////// STOCK CASA CENTRAL ////
            if( count($var) < 2 ){

            if( is_array($var) && $var[0] == "stock" ){


              $group = productos_stock_sucursales::updateOrCreate(
                    [
                      'product_id' => $product->id,
                      'comercio_id' => $comercio_id,
                      'sucursal_id' => 0,
                      'referencia_variacion' => $this->referencia_id,
                    ],[
                      'stock' => empty($r) ? 0 : $r,
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


              $group = productos_stock_sucursales::updateOrCreate(
                    [
                      'product_id' => $product->id,
                      'comercio_id' => $comercio_id,
                      'sucursal_id' => $var[0],
                      'referencia_variacion' => $this->referencia_id,
                    ],[
                      'stock' => empty($r) ? 0 : $r,
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
            'name' => Rule::unique('products', 'name'),
            'stock' => 'numeric|nullable',
            'precio' => 'numeric|nullable',
            'costo' => 'numeric|nullable',
            'inv_ideal' => 'numeric|nullable',
        ];
    }

    public function customValidationMessages()
{
    return [
        'stock.numeric' => 'La columna stock debe ser solo numeros.',
        'costo.numeric' => 'La columna costos debe ser solo numeros.',
        'precio.numeric' => 'La columna precios debe ser solo numeros.',
        'inv_ideal.numeric' => 'La columna inventario ideal debe ser solo numeros.',
    ];
}

public function chunkSize(): int
  {
      return 100;
  }


    public function batchSize(): int
    {
        return 1500;
    }


}
