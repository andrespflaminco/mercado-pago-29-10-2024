<?php

namespace App\Imports;

use App\Models\categorias_monotributo;
use App\Models\paises;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Spatie\Permission\Models\Role;
use \WeDevs\ORM\Eloquent\Facades\DB;


class CategoriasMonotributoImport implements WithHeadingRow,  WithValidation, SkipsOnError, OnEachRow
{
    use SkipsErrors;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function onRow(Row $row)
     {

        $rowIndex = $row->getIndex();
        $row      = $row->toArray();
        
       // $pais_id = paises::where('nombre','Argentina')->first()->id;
        
        // Condiciones para buscar el registro existente
        $condiciones = ['pais' => 1 , 'categoria' => $row['categoria']];

        // Datos a crear o actualizar
        $datos = ['pais' => 1 , 'categoria' => $row['categoria'],'minimo' => $row['minimo'] , 'maximo' => $row['maximo']];

        // Utilizar updateOrCreatee
        categorias_monotributo::updateOrCreate($condiciones, $datos);
    
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
