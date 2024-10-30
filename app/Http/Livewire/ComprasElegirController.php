<?php

namespace App\Http\Livewire;

use App\Services\Cart;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\Session;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\proveedores;
use App\Models\Category;
use App\Models\User;
use App\Models\sucursales;
use App\Models\metodo_pago;
use App\Models\cajas;
use App\Models\productos_stock_sucursales;
use App\Models\historico_stock;
use App\Models\pagos_facturas;
use App\Models\Product;
use Carbon\Carbon;
use App\Models\bancos;
use App\Models\productos_variaciones_datos;
use App\Models\atrobutos;
use App\Models\variaciones;
use App\Models\compras_proveedores;
use App\Models\productos_variaciones;
use App\Models\detalle_compra_proveedores;
use DB;

class ComprasElegirController extends Component
{
  use WithPagination;
  use WithFileUploads;

  public $name,$barcode,$cost,$price,$pago,$id_cart,  $proveedor_id, $caja, $stock,$alerts,$categoryid,$monto_inicial, $caja_abierta, $codigo, $monto_total, $search, $image, $product_id, $pageTitle,$componentName,$comercio_id,$data_Cart, $observaciones, $query_product, $products_s , $Cart, $deuda,$metodo_pago_elegido, $product,$total, $iva, $iva_general, $itemsQuantity, $cantidad,  $carrito, $qty, $tipo_factura, $numero_factura, $orderby_id;
  private $pagination = 25;


  public $productos_variaciones_datos = [];

  public function mount()
  {
  
  }

  public function render() {
 
    return view('livewire.compras_elegir.component',[
    ])
    ->extends('layouts.theme-pos.app')
    ->section('content');
  }


}
