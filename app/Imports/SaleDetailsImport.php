<?php

namespace App\Imports;

use App\Models\pagos_facturas;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Illuminate\Validation\Rule;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\cajas;
use App\Models\metodo_pago;
use App\Models\seccionalmacen;
use App\Models\Product;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\OnEachRow;

class SaleDetailsImport implements OnEachRow, SkipsEmptyRows, WithHeadingRow,  WithValidation, SkipsOnError
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

        $rowIndex = $row->getIndex();
        $row      = $row->toArray();


        if(Auth::user()->comercio_id != 1)
        $comercio_id = Auth::user()->comercio_id;
        else
        $comercio_id = Auth::user()->id;


        if(($row['metodo'] != "Efectivo") && ($row['metodo'] != "Pago dividido")) {

        $metodo_pago = metodo_pago::where('nombre', $row['metodo'])->where('comercio_id', $comercio_id)->first()->id;

      }


      if($row['metodo'] == "Efectivo") {

      $metodo_pago = 1;

    }
    if($row['metodo'] == "Pago dividido") {

    $metodo_pago = 2;

  }

  $seccion_almacen = seccionalmacen::where('nombre', $row['almacen'])->where('comercio_id', $comercio_id)->first()->id;

  $venta = SaleDetail::create([
        'sale_id'            => $row['id_venta'],
        'created_at'            => $row['fecha'],
        'product_barcode'            => $row['codigo_prod'],
        'product_name' =>  $row['producto'],
        'price' => $row['precio'],
        'quantity' => $row['cantidad'],
        'iva' => 0,
        'referencia_variacion' => 0,
        'descuento' => 0,
        'recargo' => 0,
        'metodo_pago' => $metodo_pago,
        'cliente_id' => 1,
        'seccion_almacen' => $seccion_almacen,
        'caja' => cajas::where('nro_caja', $row['caja'])->where('comercio_id', $comercio_id)->first()->id,
        'canal_venta' => 'Mostrador',
        'product_id' => Product::where('barcode', $row['codigo_prod'])->where('comercio_id', $comercio_id)->where('eliminado',0)->first()->id,
        'comercio_id'            => $comercio_id,
        'eliminado'            => 0,
       ]
  );

    }

    //encabezados del archivo excel
    public function rules(): array
    {
        return [
            'name' => Rule::unique('id_factura', 'name'),
        ];
    }

    public function customValidationMessages()
{
    return [

    ];
}







}
