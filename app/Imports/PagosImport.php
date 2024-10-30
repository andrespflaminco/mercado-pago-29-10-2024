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
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\OnEachRow;

class PagosImport implements OnEachRow, SkipsEmptyRows, WithHeadingRow,  WithValidation, SkipsOnError
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



        $pagos = pagos_facturas::updateOrCreate(
            [

              'id_factura'            => $row['id_factura']
            ],[
              'monto'            => $row['monto'],
              'metodo_pago'         => $row['metodo_pago'],
              'updated_at'            => $row['updated_at'],
              'created_at'            => $row['created_at'],
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
