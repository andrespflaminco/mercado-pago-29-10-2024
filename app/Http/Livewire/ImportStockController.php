<?php

namespace App\Http\Livewire;

use App\Imports\CategoriesImport;
use App\Imports\PagosImport;
use App\Imports\ProductsImport;
use App\Imports\StockSucursalImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Category;
use App\Models\pagos_facturas;
use App\Models\Product;
use App\Models\sucursales;
use Illuminate\Support\Facades\Auth;
use App\Models\lista_precios;

class ImportStockController extends Component
{
    use WithFileUploads;

    public $contCategories, $contProducts, $fileCategories, $fileStockSucursales, $comercio_id, $sucursal_id;

    public function render()
    {

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;



        $sucursales = sucursales::where('casa_central_id', $comercio_id)
        ->join('users','users.id','sucursales.sucursal_id')
        ->select('sucursales.*','users.name')
        ->get();

        return view('livewire.import-stock.component',[
          'sucursales' => $sucursales
        ])
            ->extends('layouts.theme-pos.app')
            ->section('content');
    }

    public function uploadLista()
    {
      $sucursal_id = $this->sucursal_id;

        
		$rules  =[
			'fileStockSucursales' => 'required|mimes:xlsx,xls',
			'sucursal_id' => 'required|not_in:Elegir'
		];

		$messages = [
		    'fileStockSucursales.required' => 'Debe insertar un archivo excel',
			'sucursal_id.required' => 'Elija una sucursal para actualizar el stock',
			'sucursal_id.not_in' => 'Elija una sucursal para actualizar el stock',
			];

		$this->validate($rules, $messages);
		
        $cantBefore = Product::count();
        $import = new StockSucursalImport($sucursal_id);
        Excel::import($import, $this->fileStockSucursales);
        //$this->contProducts = $import->getRowCount();
        $this->fileProducts = '';
        $cantAfter = Product::count() - $cantBefore;
        $this->emit('import', "REGISTROS IMPORTADOS");
    }
    
    

}
