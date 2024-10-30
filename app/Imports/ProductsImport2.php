<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Models\proveedores;
use App\Models\historico_stock;
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

class ProductsImport implements OnEachRow, SkipsEmptyRows, WithHeadingRow,  WithValidation, SkipsOnError, WithBatchInserts, WithChunkReading, WithCalculatedFormulas
{
    use SkipsErrors;

    public $comercio_id, $prov, $proveed, $alm, $al, $categ, $movimiento_stock, $cat, $costo, $stock, $st, $cost, $inv_minimo, $in_minimo, $inv_ideal, $in_ideal, $prec, $pre, $maneja_stock;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */





   public function onRow(Row $row)
    {

        $rowIndex = $row->getIndex();
        $row      = $row->toArray();

        $usuario_id = Auth::user()->id;

        if(Auth::user()->comercio_id != 1)
    		$comercio_id = Auth::user()->comercio_id;
    		else
    		$comercio_id = Auth::user()->id;

        if(Product::where('barcode',$row['codigo'])->where('comercio_id', $comercio_id)->where('eliminado',0)->exists())
          {

            //////////// STOCK //////////////

            $st = $row['stock'];

            if($st == '') {
              $stock = 0;
            }
            else {
              $stock = $row['stock'];
            }






            $product = Product::where('barcode',$row['codigo'])->where('comercio_id', $comercio_id)->where('eliminado',0)->first();

            $movimiento_stock = $stock - $product->stock;

            $historico = historico_stock::create([

              'tipo_movimiento' => 8,
              'producto_id' => $product->id,
              'cantidad_movimiento' => $movimiento_stock,
              'stock' => $stock,
              'usuario_id' => $usuario_id,
              'comercio_id'  => $comercio_id
            ]);


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


            //////////// INV MINIMO //////////////

            $in_minimo = $row['inv_minimo'];

            if($in_minimo == '') {
              $inv_minimo = 0;
            }
            else {
              $inv_minimo = $row['inv_minimo'];
            }
            //////////// INV IDEAL //////////////

            $in_ideal = $row['inv_ideal'];

            if($in_ideal == '') {
              $inv_ideal = 0;
            }
            else {
              $inv_ideal = $row['inv_ideal'];
            }
            //////////// PRECIO //////////////

            $pre = $row['precio'];

            if($pre == '') {
              $prec = 0;
            }
            else {
              $prec = $row['precio'];
            }
            //////////// COSTO //////////////

            $cost = $row['costo'];

            if($cost == '') {
              $costo = 0;
            }
            else {
              $costo = $row['costo'];
            }

            //////////// STOCK DESCUBIERTO //////////////

            $maneja = $row['maneja_stock'];

            if($maneja == '') {
              $maneja_stock = 'no';
            }
            else {
              $maneja_stock = $row['maneja_stock'];
            }

            //////////// IMAGEN //////////////

            $imagen = $row['imagen'];

            if($imagen == '') {
              $img = null;
            }
            else {
              $img = $row['imagen'];
            }

            //////////// DESCRIPCION //////////////

            $descripcion = $row['descripcion'];

            if($descripcion == '') {
              $descrip = null;
            }
            else {
              $descrip = $row['descripcion'];
            }

            //////////// VENTA MOSTRADOR //////////////

            $vm = $row['venta_mostrador'];

            if($vm == 'no') {
            $venta_mostrador = 0;
            }
            else {
            $venta_mostrador = 1;
            }


            //////////// VENTA ECOMMERCE //////////////

            $ve = $row['venta_ecommerce'];

            if($ve == 'no') {
            $venta_ecommerce = 0;
            }
            else {
            $venta_ecommerce = 1;
            }

            //////////// VENTA WOOCOMMERCE //////////////

            $vw = $row['venta_wocommerce'];

            if($vw == 'no') {
            $venta_wocommerce = 0;
            }
            else {
            $venta_wocommerce = 1;
            }


            $group = Product::updateOrCreate(
                [

                  'barcode'            => $row['codigo'],
                  'comercio_id'         => $comercio_id,
                ],[
                    'name'            => $row['nombre'],
                  'barcode'         => $row['codigo'],
                  'cost'            => $costo,
                  'price'           => $prec,
                  'stock'           => $stock,
                  'inv_ideal'       => $inv_ideal,
                  'alerts'          => $inv_minimo,
                  'image'          => $img,
                  'descripcion'          => $descripcion,
                  'mostrador_canal' => $venta_mostrador,
                  'ecommerce_canal' => $venta_ecommerce,
                  'wocommerce_canal' => $venta_wocommerce,
                  'stock_descubierto'          => $maneja_stock,
                  'seccionalmacen_id'     => seccionalmacen::where('nombre', $alm)->where('comercio_id', $comercio_id)->first()->id,
                  'category_id'     => Category::where('name', $categ)->where('comercio_id', $comercio_id)->first()->id,
                  'proveedor_id'     => proveedores::where('nombre', $proved)->where('comercio_id', $comercio_id)->first()->id,
                  'eliminado' => '0'
                 ]
            );

          } else {

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

            //////////// STOCK //////////////

            $st = $row['stock'];

            if($st == '') {
              $stock = 0;
            }
            else {
              $stock = $row['stock'];
            }
            //////////// INV MINIMO //////////////

            $in_minimo = $row['inv_minimo'];

            if($in_minimo == '') {
              $inv_minimo = 0;
            }
            else {
              $inv_minimo = $row['inv_minimo'];
            }
            //////////// INV IDEAL //////////////

            $in_ideal = $row['inv_ideal'];

            if($in_ideal == '') {
              $inv_ideal = 0;
            }
            else {
              $inv_ideal = $row['inv_ideal'];
            }
            //////////// PRECIO //////////////

            $pre = $row['precio'];

            if($pre == '') {
              $prec = 0;
            }
            else {
              $prec = $row['precio'];
            }
            //////////// COSTO //////////////

            $cost = $row['costo'];

            if($cost == '') {
              $costo = 0;
            }
            else {
              $costo = $row['costo'];
            }

            //////////// STOCK DESCUBIERTO //////////////

            $maneja = $row['maneja_stock'];

            if($maneja == '') {
              $maneja_stock = 'no';
            }
            else {
              $maneja_stock = $row['maneja_stock'];
            }

            //////////// IMAGEN //////////////

            $imagen = $row['imagen'];

            if($imagen == '') {
              $img = null;
            }
            else {
              $img = $row['imagen'];
            }

            //////////// DESCRIPCION //////////////

            $descripcion = $row['descripcion'];

            if($descripcion == '') {
              $descrip = null;
            }
            else {
              $descrip = $row['descripcion'];
            }

            //////////// VENTA MOSTRADOR //////////////

            $vm = $row['venta_mostrador'];

            if($vm == 'no') {
            $venta_mostrador = 0;
            }
            else {
            $venta_mostrador = 1;
            }


            //////////// VENTA ECOMMERCE //////////////

            $ve = $row['venta_ecommerce'];

            if($ve == 'no') {
            $venta_ecommerce = 0;
            }
            else {
            $venta_ecommerce = 1;
            }

            $group = Product::updateOrCreate(
                [

                  'barcode'            => $row['codigo'],
                  'comercio_id'            => $comercio_id,
                  'eliminado'            => 0,
                ],[
                    'name'            => $row['nombre'],
                  'barcode'         => $row['codigo'],
                  'cost'            => $costo,
                  'price'           => $prec,
                  'stock'           => $stock,
                  'inv_ideal'       => $inv_ideal,
                  'mostrador_canal' => $venta_mostrador,
                  'ecommerce_canal' => $venta_ecommerce,
                  'descripcion'          => $descripcion,
                  'alerts'          => $inv_minimo,
                  'image'          => $img,
                  'stock_descubierto'          => $maneja_stock,
                  'seccionalmacen_id'     => seccionalmacen::where('nombre', $alm)->where('comercio_id', $comercio_id)->first()->id,
                  'category_id'     => Category::where('name', $categ)->where('comercio_id', $comercio_id)->first()->id,
                  'proveedor_id'     => proveedores::where('nombre', $proved)->where('comercio_id', $comercio_id)->first()->id,
                  'eliminado' => '0'
                 ]
            );

            $historico = historico_stock::create([

              'tipo_movimiento' => 8,
              'producto_id' => $group->id,
              'cantidad_movimiento' => $stock,
              'stock' => $stock,
              'usuario_id' => $usuario_id,
              'comercio_id'  => $comercio_id
            ]);
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

    public function batchSize(): int
    {
        return 1500;
    }
    
    public function chunkSize(): int
    {
        return 1500;
    }







}
