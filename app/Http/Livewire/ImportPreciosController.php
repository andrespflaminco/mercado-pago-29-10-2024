<?php

namespace App\Http\Livewire;

use App\Imports\CategoriesImport;
use App\Imports\PagosImport;
use App\Imports\ProductsImport;
use App\Imports\ListaPreciosImport;
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

class ImportPreciosController extends Component
{
    use WithFileUploads;

    public $contCategories, $contProducts, $stock_descubierto, $fileCategories, $fileListaPrecio, $comercio_id, $lista_id;

    public function render()
    {

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

        $listas = lista_precios::where('comercio_id', 'like', $comercio_id)
        ->get();

        return view('livewire.import-precios.component',[
          'listas' => $listas
        ])
            ->extends('layouts.theme-pos.app')
            ->section('content');
    }

    public function uploadCategories()
    {
        $this->validate([
            'fileCategories' => 'required|mimes:xlsx,xls'
        ]);
        $cantBefore = Category::count();
        $import = new CategoriesImport();
        Excel::import($import, $this->fileCategories);
        //$this->contCategories = $import->getRowCount();
        $this->fileCategories = '';
        $cantAfter = Category::count() - $cantBefore;
        $this->emit('global-msg', "REGISTROS IMPORTADOS");
    }


    public function uploadLista()
    {
        $lista_id = $this->lista_id;
              

        $import = new ListaPreciosImport($this->lista_id);
        Excel::import($import, $this->fileListaPrecio); 
        //$this->contProducts = $import->getRowCount();
        $this->fileListaPrecio = '';
        $this->emit('global-msg', "REGISTROS IMPORTADOS");
    }

    	protected $listeners =[
		'wc' => 'WocommerceUpdateProducts'
	];

        public function import()
        {
            request()->validate([
                'file' => ['required', 'mimes:xlsx'],
                'lista_id' => 'required|not_in:Elegir'
            ]);

            $id = now()->unix();
            session([ 'import' => $id ]);

            Excel::queueImport(new ListaPreciosImport($lista_id), request()->file('file')->store('temp'));

            return redirect()->back();
        }


        public function WocommerceUpdateProducts() {

          ini_set('memory_limit', '-1');
          set_time_limit(0);



        	if(Auth::user()->comercio_id != 1)
        	$comercio_id = Auth::user()->comercio_id;
        	else
        	$comercio_id = Auth::user()->id;

        	$wc = wocommerce::where('comercio_id', $comercio_id)->first();

        	if($wc != null){

        	$woocommerce = new Client(
        		$wc->url,
        		$wc->ck,
        		$wc->cs,

        			[
        					'version' => 'wc/v3',
        			]
        	);


        $lista_productos = Product::where('comercio_id', $comercio_id)
        ->where('wc_canal', 1)
        ->where('eliminado', 0)
        ->where('wc_product_id','<>', null)
        ->get();


        foreach($lista_productos as $lp) {

        $product = Product::find($lp->id);

        $product_id = $lp->id;

        if($product->wc_canal == 1 && $product->wc_canal != null) {
        $atribut = [];
        $v = [];
        	// DATOS DE VARIACIONES //
        	$atributos =	productos_variaciones::join('atributos','atributos.id','productos_variaciones.atributo_id')
        	->select('atributos.id','atributos.nombre')
        	->where('productos_variaciones.producto_id', $product_id )
        	->groupBy('atributos.id','atributos.nombre')
        	->get();

        $x = 0;

        foreach ($atributos as $atributes) {
        ///     VARIACIONES          ///

        $variaciones = variaciones::where('atributo_id',$atributes->id)->select('nombre','atributo_id')->get();

        foreach ($variaciones as $var) {

        	if($var->atributo_id == $atributes->id) {

        	$var = $var->nombre;
        	array_push($v,$var);

        	}

        }
        	///     VARIACIONES          ///


        		$atributos =
        		array(
        	   "name" => $atributes->nombre,
        		'position' => 0,
         		'visible' => true,
         		'variation' => true,
        		'options' => $v,

        		);

        		array_push($atribut,$atributos);

        }


        	$data_p = [
            'name' => $product->name,
            'type' => 'variable',
            'attributes' => $atribut
        ];


        $data_p = $woocommerce->put('products/'.$product->wc_product_id, $data_p);

        $a = [];
        $lista_precios_array = [];
        $datex = [];

        $datos_origen =	productos_variaciones::where('productos_variaciones.producto_id', $product_id)
        ->select('productos_variaciones.referencia_id')
        ->groupBy('productos_variaciones.referencia_id')
        ->get();

        $i = 0;

        foreach ($datos_origen as $d) {

        // LISTA DE PRECIO BASE //
        $this->precio_origen = productos_lista_precios::where('referencia_variacion', $d->referencia_id )->where('lista_id',0)->first();

        // DATOS DE VARIACIONES DE WOCOMMERCE//

        $datos_variacion_wocommerce = productos_variaciones_datos::where('referencia_variacion', $d->referencia_id)->first();
        // DATOS DE VARIACIONES //

        $datos =	productos_variaciones::join('variaciones','variaciones.id','productos_variaciones.variacion_id')
        ->join('atributos','atributos.id','productos_variaciones.atributo_id')
        ->select('atributos.nombre as name', 'variaciones.nombre as option')
        ->where('referencia_id', $d->referencia_id )->get();

        $datos = $datos->toArray();
        array_push($a,$datos);


        // DATOS DE LISTAS DE PRECIOS //
        $lista_precios = productos_lista_precios::join('lista_precios','lista_precios.id','productos_lista_precios.lista_id')
        ->where('lista_id','<>',0)
        ->where('referencia_variacion', $d->referencia_id )
        ->select('productos_lista_precios.precio_lista', 'lista_precios.wc_key')
        ->first();

        if($lista_precios != null) {

        	$lista_precios->wc_key."_wholesale_price";
        	$lista_precios->wc_key."_have_wholesale_price";

        	$list =
        	array(
        	array(
            "key" => $lista_precios->wc_key."_wholesale_price",
            "value" => $lista_precios->precio_lista,
        	),	array(
            "key" => $lista_precios->wc_key."_have_wholesale_price",
            "value" => "yes",
        	)
        	);

        } else {
            $list = [];
        }
        /////////////////////////////////


        $data = [
        		'regular_price' => $this->precio_origen->precio_lista,
        		'attributes' => $a[$i++],
        		'meta_data' =>  $list
        ];


        $wc = $woocommerce->post('products/'.$product->wc_product_id.'/variations/'.$datos_variacion_wocommerce->wc_variacion_id, $data);

        }

        }


        }


        }


        $this->emit('import', "REGISTROS IMPORTADOS");

        }
}
