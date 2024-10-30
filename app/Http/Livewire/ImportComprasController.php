<?php

namespace App\Http\Livewire;

use App\Imports\CategoriesImport;
use App\Imports\PagosImport;
use App\Imports\ProductsImport;
use App\Services\Cart;
use App\Imports\CompraImport;
use Livewire\Component;
use App\Models\wocommerce;
use Automattic\WooCommerce\Client;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Category;
use App\Models\productos_variaciones;
use App\Models\productos_variaciones_datos;
use App\Models\productos_lista_precios;
use App\Models\variaciones;
use App\Models\atributos;
use App\Models\pagos_facturas;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\lista_precios;

class ImportComprasController extends Component
{
    use WithFileUploads;

    public $contCategories, $actualizar_costos, $contProducts, $stock_descubierto, $fileCategories, $fileCompra, $comercio_id, $lista_id;

    public function render()
    {

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;


        return view('livewire.import-compra.component', [])
            ->extends('layouts.theme.app')
            ->section('content');
    }

    public function uploadCompra()
    {
		$rules  =[
			'fileCompra' => 'required|mimes:xlsx,xls'
		];

		$messages = [
		    'fileCompra.required' => 'Debe insertar un archivo excel',
		];


		$this->validate($rules, $messages);
		
		$actualizar_costos = $this->actualizar_costos;

            $cart = new Cart;
            $cart->clear();
            
        $import = Excel::queueImport(new CompraImport($actualizar_costos), $this->fileCompra->store('temp'));
        //$this->contProducts = $import->getRowCount();
        $this->fileCompra = '';
    }
    
}
