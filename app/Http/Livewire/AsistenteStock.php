<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\proveedores;
use App\Models\Product;
use App\Models\seccionalmacen;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;


class AsistenteStock extends Component
{

  use WithPagination;
  use WithFileUploads;


  public $name,$barcode,$cost,$price,$stock,$tipo_producto,$alerts,$categoryid,$search,$image,$selected_id,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $inv_ideal, $id_proveedor, $tipo;
  private $pagination = 25;


  public function paginationView()
  {
    return 'vendor.livewire.bootstrap';
  }


  public function mount()
  {
    $this->pageTitle = 'Asistente de compras';
    $this->componentName = 'Stock';
    $this->categoryid = 'Elegir';
    $this->almacen = 'Elegir';
    $this->stock_descubierto = 'Elegir';
    $this->tipo = 1;
  }



  public function render()
  {
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;



    $products = Product::join('categories as c','c.id','products.category_id')
    ->leftjoin('seccionalmacens as a','a.id','products.seccionalmacen_id')
    ->leftjoin('proveedores as pr','pr.id','products.proveedor_id')
    ->select('products.*','c.name as category','a.nombre as almacen','pr.nombre as nombre_proveedor',Product::raw('(products.alerts - products.stock) as comprar'))
    ->where('products.comercio_id', 'like', $comercio_id)
    ->where('products.eliminado', 0);

    if(strlen($this->search) > 0) {

    $products = $products->where( function($query) {
	$query->where('products.name', 'like', '%' . $this->search . '%')
	->orWhere('products.barcode', 'like',$this->search . '%');
	});

    }

    if(($this->id_proveedor) != 0) {
    $products = $products->where('products.proveedor_id', 'like', $this->id_proveedor);

  }


    $products = $products->orderBy('products.name','asc')
    ->get();



    return view('livewire.asistente_stock.component', [
      'data' => $products,
      'categories' => Category::orderBy('name','asc')->where('comercio_id', 'like', $comercio_id)->get(),
      'almacenes' => seccionalmacen::orderBy('nombre','asc')->where('comercio_id', 'like', $comercio_id)->get(),
      'prov' => proveedores::orderBy('nombre','asc')->where('comercio_id', 'like', $comercio_id)->get()
    ])
    ->extends('layouts.theme.app')
    ->section('content');
  }
}
