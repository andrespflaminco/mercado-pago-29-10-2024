<?php

namespace App\Http\Livewire;

use App\Imports\CategoriesImport;
use App\Imports\PagosImport;
use App\Imports\ProductsImport;
use App\Imports\SaleImport;
use App\Imports\SaleDetailsImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Category;
use App\Models\pagos_facturas;
use App\Models\Product;

class ImportVentasController extends Component
{
    use WithFileUploads;

    public $contCategories, $contProducts, $fileCategories, $fileProducts, $fileVentas, $fileDetalleVentas;

    public function render()
    {
        return view('livewire.import-ventas.component')
            ->extends('layouts.theme.app')
            ->section('content');
    }

    public function uploadVentas()
    {

      ini_set('memory_limit', '1024M');
      set_time_limit(3000000);

        $this->validate([
            'fileVentas' => 'required|mimes:xlsx,xls'
        ]);
        $import = new SaleImport();
        Excel::import($import, $this->fileVentas);
        //$this->contProducts = $import->getRowCount();
        $this->fileVentas = '';
        $this->emit('import', "REGISTROS IMPORTADOS");
    }

    public function uploadDetalleVentas()
    {

      ini_set('memory_limit', '1024M');
      set_time_limit(3000000);

        $this->validate([
            'fileDetalleVentas' => 'required|mimes:xlsx,xls'
        ]);
        $import = new SaleDetailsImport();
        Excel::import($import, $this->fileDetalleVentas);
        //$this->contProducts = $import->getRowCount();
        $this->fileDetalleVentas = '';
        $this->emit('import', "REGISTROS IMPORTADOS");
    }
}
