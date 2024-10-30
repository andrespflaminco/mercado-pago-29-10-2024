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

class ProductsValidationImport implements OnEachRow, SkipsEmptyRows, WithHeadingRow
{
    protected $rows = [];
    protected $maxRows = 5; // Límite de filas a procesar
    protected $rowCount = 0; // Contador de filas procesadas

    public function onRow(Row $row)
    {
        if ($this->rowCount < $this->maxRows) {
            $this->rows[] = $row->toArray();
            $this->rowCount++;
        } else {
            return false; // Detener la importación después de $maxRows filas
        }
    }

    public function getRows()
    {
        return $this->rows;
    }



}
