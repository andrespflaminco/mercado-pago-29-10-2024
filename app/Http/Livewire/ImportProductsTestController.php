<?php

namespace App\Http\Livewire;

use App\Imports\ProductsImportTest;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Category;
use App\Models\pagos_facturas;
use App\Models\Product;

class ImportProductsTestController extends Component
{
    use WithFileUploads;

    public $contCategories, $contProducts, $fileCategories, $fileProducts, $fileVentas, $fileDetalleVentas;

    public function render()
    {
        return view('livewire.import-products-test.component')
            ->extends('layouts.theme-pos.app')
            ->section('content');
    }

    public function uploadProducts()
    {

      ini_set('memory_limit', '1024M');
      set_time_limit(3000000);

        $this->validate([
            'fileProducts' => 'required|mimes:xlsx,xls'
        ]);
        $import = new ProductsImportTest();
        Excel::import($import, $this->fileProducts);
        //$this->contProducts = $import->getRowCount();
        $this->fileProducts = '';
        $this->emit('import', "REGISTROS IMPORTADOS");
    }

}
