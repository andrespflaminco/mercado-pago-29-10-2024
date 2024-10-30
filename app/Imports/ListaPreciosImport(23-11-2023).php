<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Models\productos_lista_precios;
use App\Models\proveedores;
use App\Models\historico_stock;
use App\Models\productos_variaciones;
use App\Models\variaciones;
use App\Models\atributos;
use App\Models\productos_variaciones_datos;
use App\Models\seccionalmacen;
use Illuminate\Support\Facades\Auth;
use App\Models\wocommerce;
use Automattic\WooCommerce\Client;
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
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ListaPreciosImport implements OnEachRow, SkipsEmptyRows, ShouldQueue, WithHeadingRow,  WithValidation, SkipsOnError, WithBatchInserts, WithChunkReading, WithCalculatedFormulas, WithMultipleSheets
{
    use SkipsErrors;

    public $comercio_id, $prov, $proveed, $alm, $al, $categ, $movimiento_stock, $cat, $costo, $stock, $st, $cost, $inv_minimo, $in_minimo, $inv_ideal, $in_ideal, $prec, $pre, $maneja_stock, $stock_descubierto, $manage_stock;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

     public function __construct($lista_id)
        {
         $this->lista_id = $lista_id; // errro en en linea
        }





   public function onRow(Row $row)
    {

        $rowIndex = $row->getIndex();
        $row      = $row->toArray();

        $usuario_id = Auth::user()->id;

        if(Auth::user()->comercio_id != 1)
    		$comercio_id = Auth::user()->comercio_id;
    		else
    		$comercio_id = Auth::user()->id;

        //////////// PRECIO //////////////

        $pre = $row['precio'];

        if($pre == '') {
          $prec = 0;
        }
        else {
          $prec = $row['precio'];
        }

        //////////// VARIACIONES //////////////

        $cod_variacion = $row['cod_variacion'];

        if($cod_variacion == '') {
          $cod_variacion = 0;

          $product_cost = Product::where('barcode', $row['codigo'])->where('comercio_id',$comercio_id)->where('eliminado',0)->first();

          if($product_cost != null) {

            $product_cost->update([
            'cost' => $row['costo'] ?? 0
            	]);
          }

        }
        else {
          $cod_variacion = $row['cod_variacion'];

          $product_cost = productos_variaciones_datos::where('referencia_variacion', $cod_variacion)
          ->where('comercio_id',$comercio_id)
          ->first();

          if($product_cost != null) {
            $product_cost->update([
            'cost' => $row['costo'] ?? 0
              ]);
          }

        }

        if($this->lista_id == 1) {
          $this->lista_id = 0;
        } else {
          $this->lista_id = $this->lista_id;
        }

        ////////// LISTA DE PRECIOS //////////////

          $product = Product::where('barcode', $row['codigo'])->where('comercio_id',$comercio_id)->where('eliminado',0)->first();

          $group = productos_lista_precios::updateOrCreate(
              [

                'product_id'            => $product->id,
                'comercio_id'            => $comercio_id,
                'lista_id'            => $this->lista_id,
                'referencia_variacion' => $cod_variacion
              ],[
                'name'            => $row['nombre'],
                'precio_lista'           => $prec
               ]
          );



        }



    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }
    //encabezados del archivo excel
    public function rules(): array
    {
        return [
            'precio' => 'numeric|nullable',
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
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }










}
