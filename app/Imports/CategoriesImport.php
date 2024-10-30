<?php

namespace App\Imports;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class CategoriesImport implements WithHeadingRow,  WithValidation, SkipsOnError, OnEachRow
{
    use SkipsErrors;

    public $comercio_id;

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

         $group = Category::updateOrCreate(
             [
               'name'   => $row['nombre'],
               'comercio_id'     => $comercio_id
             ],[
               'name'   => $row['nombre'],
               'comercio_id'     => $comercio_id
                 ]
         );
     }

















    public function rules(): array
    {
        return [
            'name' => Rule::unique('categories', 'name'),
        ];
    }

    public function customValidationMessages()
    {
        //'name.required' => 'Nombre de categoría requerido',
        return [
            'name.unique' => 'Ya existe la categoría'
        ];
    }
}
