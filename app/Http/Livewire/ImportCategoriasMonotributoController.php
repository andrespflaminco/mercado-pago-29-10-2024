<?php

namespace App\Http\Livewire;

use App\Imports\CategoriasMonotributoImport;
use App\Imports\PagosImport;
use App\Imports\RecetasImport;
use App\Imports\InsumosImport;
use App\Imports\ProductsImport;
use App\Imports\SaleImport;
use App\Imports\SaleDetailsImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\categorias_monotributo;
use App\Models\pagos_facturas;
use App\Models\Product;

class ImportCategoriasMonotributoController extends Component
{
    use WithFileUploads;

    public $contCategories, $contProducts, $fileCategoriasMonotributo, $fileProducts, $fileVentas, $fileDetalleVentas, $fileInsumos, $fileRecetas;

    public function render()
    {
        
        $this->monotributo = categorias_monotributo::get();
        
        return view('livewire.import-categorias-monotributo.component',[
            'monotributo' => $this->monotributo
            ])
            ->extends('layouts.theme-pos.app')
            ->section('content');
    }

    public function uploadCategoriasMonotributo()
    {

      ini_set('memory_limit', '1024M');
      set_time_limit(3000000);

        $this->validate([
            'fileCategoriasMonotributo' => 'required|mimes:xlsx,xls'
        ]);
        $import = new CategoriasMonotributoImport();
        Excel::import($import, $this->fileCategoriasMonotributo);
        $this->fileCategoriasMonotributo = '';
        $this->emit('import', "REGISTROS IMPORTADOS");
    }

}
