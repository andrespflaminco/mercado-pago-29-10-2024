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
use App\Models\cajas;
use App\Models\metodo_pago;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\OnEachRow;

class SaleImport implements OnEachRow, SkipsEmptyRows, WithHeadingRow,  WithValidation, SkipsOnError
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

        if(($row['metodo_pago'] != "Efectivo") && ($row['metodo_pago'] != "Pago dividido")) {

        $metodo_pago = metodo_pago::where('nombre', $row['metodo_pago'])->where('comercio_id', $comercio_id)->first()->id;

      }
      if($row['metodo_pago'] == "Efectivo") {

      $metodo_pago = 1;

    }
    if($row['metodo_pago'] == "Pago dividido") {

    $metodo_pago = 2;

  }


        $venta = Sale::updateOrCreate(
            [

              'id'            => $row['folio'],
              'comercio_id'            => $comercio_id,
            ],[
              'created_at'            => $row['fecha'],
              'metodo_pago'           => $metodo_pago,
              'caja'           => cajas::where('nro_caja', $row['caja'])->where('comercio_id', $comercio_id)->first()->id,
              'items'            => $row['cant_items'],
              'total'                 => $row['importe'],
              'subtotal'                 => $row['importe'],
              'cliente_id' => 1,
              'recargo'                 => 0,
              'descuento'                 => 0,
              'deuda' => 0,
              'iva' => 0,
              'tipo_comprobante'            => $row['tipo_comprobante'],
              'status'            => $row['estado'],
              'cash' => 0,
              'comercio_id'            => $comercio_id,
              'user_id'            => $comercio_id,
             ]
        );


        $pagos = pagos_facturas::create(
            [
              'id_factura' => $venta->id,
              'monto'            => $row['importe'],
              'metodo_pago'         =>  $metodo_pago,
              'caja'           => cajas::where('nro_caja', $row['caja'])->where('comercio_id', $comercio_id)->first()->id,
            
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
