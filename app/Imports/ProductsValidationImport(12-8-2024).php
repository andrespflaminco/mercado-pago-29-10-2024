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

class ProductsValidationImport implements OnEachRow, SkipsEmptyRows, WithHeadingRow,  WithValidation, SkipsOnError
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
