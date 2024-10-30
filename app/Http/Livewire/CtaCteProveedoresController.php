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
use App\Models\metodo_pago;
use App\Models\seccionalmacen;
use App\Models\cajas;
use App\Models\historico_stock;
use App\Models\pagos_facturas;
use App\Models\Product;

use App\Models\gastos; // 14-8-2024


////////////////////////
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\hoja_ruta;
use App\Models\User;
use App\Models\saldos_iniciales;

use App\Traits\PagosTrait;

//////////////////////
use Carbon\Carbon;
use App\Models\bancos;
use App\Models\compras_proveedores;
use App\Models\detalle_compra_proveedores;
use DB;

class CtaCteProveedoresController extends Component
{
  use WithPagination;
  use WithFileUploads;
  use PagosTrait;

  public $name,$barcode,$ver,$cost,$price,$pago, $proveedor_id, $total_total, $caja_seleccionada, $estado_pago, $caja, $proveedor_elegido, $stock,$alerts,$categoryid, $codigo, $monto_total, $search, $image, $selected_id, $pageTitle,$componentName,$comercio_id,$data_Cart, $dateFrom, $dateTo, $Cart, $deuda,$metodo_pago_elegido, $product,$total, $itemsQuantity, $cantidad, $carrito, $qty, $detalle_cliente, $detalle_facturacion, $ventaId, $style, $style2, $pagos2, $estado2, $estado, $listado_hojas_ruta, $suma_monto, $suma_cash, $suma_deuda, $rec, $tot, $usuario,$tipo_pago,$monto_ap, $recargo_total,$total_pago,$NroVenta, $id_pago, $tipos_pago, $detalle_venta, $detalle_compra, $dci, $details, $saleId, $countDetails, $sumDetails, $formato_modal, $metodo_pago_agregar_pago, $fecha_ap, $fecha_editar, $detalle_proveedor;
  private $pagination = 25;
  public $saldos_iniciales;
  public $sum_si;

  public $sucursales_elegidas;
  public $selectedSucursales = [];
  public $selectedSucursalesStock = [];
  public $selectedSucursalesCheckbox = [];

  public $valor;

  public function mount()
  {
    $this->valor = "por_sucursal";
    $this->lista_cajas_dia = [];
    $this->saldos_iniciales = [];
    $this->ver = 0;
    $this->caja = cajas::select('*')->where('estado',0)->where('user_id',Auth::user()->id)->max('id');
    $fecha_editar = Carbon::now()->format('d-m-Y');
    $this->fecha_ap = Carbon::now()->format('d-m-Y');
    $this->tipos_pago = [];
    $this->detalle_compra = [];
    $this->pagos2 = [];
    $this->detalle_proveedor = [];
    $this->dci = [];
    $this->total = [];
    $this->details =[];
    $this->pageTitle = 'Listado';
    $this->cantidad = 1;
    $this->monto_ap = 0;
    $this->tipo_pago = 1;
    $this->metodo_pago_agregar_pago = 1;
    $this->componentName = 'Productos';
    $this->metodo_pago_elegido = 'Elegir';
    $this->categoryid = 'Elegir';
    $this->dateFrom = Carbon::parse('2000-01-01 00:00:00')->format('d-m-Y');
    $this->dateTo = Carbon::now()->format('d-m-Y');
    //31-5-2024
    $this->columnaOrden = 'id_proveedor';
    $this->direccionOrden = 'asc';

    $this->selectedSucursales = [auth()->user()->id];
    $this->sucursales_elegidas = auth()->user()->id;    
    $this->selectedSucursalesCheckbox[auth()->user()->id] = true;
    if(auth()->user()->id == auth()->user()->casa_central_user_id){$this->selectedSucursalesStock["0"] = true;} else {$this->selectedSucursalesStock[auth()->user()->id] = true;}

  }


//31-5-2024
  public function OrdenarColumna($columna)
    {
    if ($this->columnaOrden == $columna) {
        // Cambiar la dirección de orden si la columna es la misma
        $this->columnaOrden = $columna;
        $this->direccionOrden = $this->direccionOrden == 'asc' ? 'desc' : 'asc';
    } else {
        // Si es una columna diferente, cambiar a la nueva columna y establecer la dirección predeterminada
        $this->columnaOrden = $columna;
        $this->direccionOrden = 'asc';
        
       // dd($this->columnaOrden,$this->direccionOrden);
    }
    
    $this->render();
}


    protected function redirectLogin()
    {
        return \Redirect::to("login");
    }

// 14-8-2024
public function GetDataCtaCte($comercio_id, $casa_central_id) {
    // Consulta para obtener todos los proveedores con sus datos
    $proveedores_query = proveedores::select(
        'proveedores.id',
        'proveedores.id_proveedor',
        'proveedores.nombre as nombre_proveedor'
    )
    ->where('proveedores.comercio_id', $casa_central_id);
    
    // Aplica el filtro para agregar a la casa central como proveedor si es sucursal
    if ($comercio_id != $casa_central_id) {
        $proveedores_query = $proveedores_query->orWhere('proveedores.id', 2);    
    }
    
    // Aplica el filtro de búsqueda si existe
    if ($this->search) {
        $proveedores_query = $proveedores_query->where('proveedores.nombre', 'like', '%' . $this->search . '%');
    }
    
    $proveedores = $proveedores_query->orderBy('proveedores.nombre', 'asc')->get();
    
    // Consulta para obtener las deudas agrupadas por proveedor en `compras_proveedores`
    $deudas_compras = compras_proveedores::select(
        'compras_proveedores.proveedor_id',
        compras_proveedores::raw('SUM(CASE WHEN compras_proveedores.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN compras_proveedores.deuda ELSE 0 END) as deuda_30_dias'),
        compras_proveedores::raw('SUM(CASE WHEN compras_proveedores.created_at >= DATE_SUB(NOW(), INTERVAL 61 DAY) AND compras_proveedores.created_at < DATE_SUB(NOW(), INTERVAL 31 DAY) THEN compras_proveedores.deuda ELSE 0 END) as deuda_60_dias'),
        compras_proveedores::raw('SUM(CASE WHEN compras_proveedores.created_at < DATE_SUB(NOW(), INTERVAL 61 DAY) THEN compras_proveedores.deuda ELSE 0 END) as deuda_90_dias'),
        compras_proveedores::raw('SUM(compras_proveedores.deuda) as total_compras')
    )
    ->where('compras_proveedores.eliminado', 0)
    ->where('compras_proveedores.comercio_id', $comercio_id)
    ->groupBy('compras_proveedores.proveedor_id')
    ->get()
    ->keyBy('proveedor_id');

    // Consulta para obtener las deudas agrupadas por proveedor en `gastos`
    $deudas_gastos = gastos::select(
        'gastos.proveedor_id',
        gastos::raw('SUM(CASE WHEN gastos.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN gastos.deuda ELSE 0 END) as deuda_30_dias'),
        gastos::raw('SUM(CASE WHEN gastos.created_at >= DATE_SUB(NOW(), INTERVAL 61 DAY) AND gastos.created_at < DATE_SUB(NOW(), INTERVAL 31 DAY) THEN gastos.deuda ELSE 0 END) as deuda_60_dias'),
        gastos::raw('SUM(CASE WHEN gastos.created_at < DATE_SUB(NOW(), INTERVAL 61 DAY) THEN gastos.deuda ELSE 0 END) as deuda_90_dias'),
        gastos::raw('SUM(gastos.deuda) as total_gastos')
    )
    ->where('gastos.eliminado', 0)
    ->where('gastos.comercio_id', $comercio_id)
    ->groupBy('gastos.proveedor_id')
    ->get()
    ->keyBy('proveedor_id');
    
    // Consulta para obtener los saldos iniciales agrupados por proveedor
    $saldos_iniciales = saldos_iniciales::select(
        Sale::raw('SUM(saldos_iniciales.monto) as saldo_inicial_cuenta_corriente'),
        'referencia_id as proveedor_id'
    )
    ->where('saldos_iniciales.tipo', 'proveedor')
    ->where('saldos_iniciales.eliminado', 0)
    ->where('saldos_iniciales.comercio_id', $comercio_id)
    ->groupBy('referencia_id')
    ->get()
    ->keyBy('proveedor_id');
    
    // Combina los resultados
    foreach ($proveedores as $proveedor) {
        $proveedor_id = $proveedor->id;

        // Inicializa las deudas en 0
        $proveedor->deuda_30_dias = 0;
        $proveedor->deuda_60_dias = 0;
        $proveedor->deuda_90_dias = 0;
        $proveedor->total = 0;

        // Agrega las deudas de `compras_proveedores` al proveedor si existen
        if (isset($deudas_compras[$proveedor_id])) {
            $proveedor->deuda_30_dias += $deudas_compras[$proveedor_id]->deuda_30_dias;
            $proveedor->deuda_60_dias += $deudas_compras[$proveedor_id]->deuda_60_dias;
            $proveedor->deuda_90_dias += $deudas_compras[$proveedor_id]->deuda_90_dias;
            $proveedor->total += $deudas_compras[$proveedor_id]->total_compras;
        }

        // Agrega las deudas de `gastos` al proveedor si existen
        if (isset($deudas_gastos[$proveedor_id])) {
            $proveedor->deuda_30_dias += $deudas_gastos[$proveedor_id]->deuda_30_dias;
            $proveedor->deuda_60_dias += $deudas_gastos[$proveedor_id]->deuda_60_dias;
            $proveedor->deuda_90_dias += $deudas_gastos[$proveedor_id]->deuda_90_dias;
            $proveedor->total += $deudas_gastos[$proveedor_id]->total_gastos;
        }

        // Agrega el saldo inicial al proveedor si existe
        if (isset($saldos_iniciales[$proveedor_id])) {
            $proveedor->saldo_inicial_cuenta_corriente = $saldos_iniciales[$proveedor_id]->saldo_inicial_cuenta_corriente;
            $proveedor->total += $saldos_iniciales[$proveedor_id]->saldo_inicial_cuenta_corriente;
        } else {
            $proveedor->saldo_inicial_cuenta_corriente = 0;
        }
    }

    // Ordenar los proveedores según la columna y dirección especificadas
    $proveedores = $proveedores->sortBy(function($proveedor) {
        return $proveedor->{$this->columnaOrden};
    }, SORT_REGULAR, $this->direccionOrden === 'desc');
    
    return $proveedores;
}

    public function GetDataCtaCteOld2($comercio_id,$casa_central_id) {
        
    // Consulta para obtener todos los proveedores con sus datos
    $proveedores_query = proveedores::select(
        'proveedores.id',
        'proveedores.id_proveedor',
        'proveedores.nombre as nombre_proveedor'
    )
    ->where('proveedores.comercio_id', $casa_central_id);
    
    // Aplica el filtro para agregar a la casa central como proveedor si es sucursal
    if ($comercio_id != $casa_central_id) {
    $proveedores_query = $proveedores_query->orWhere('proveedores.id',2);    
    }
    
    // Aplica el filtro de búsqueda si existe
    if ($this->search) {
        $proveedores_query = $proveedores_query->where('proveedores.nombre', 'like', '%' . $this->search . '%');
    }
    
    $proveedores = $proveedores_query->orderBy('proveedores.nombre','asc')->get();
    
    // Consulta para obtener las deudas agrupadas por proveedor
    $deudas_query = compras_proveedores::select(
        'compras_proveedores.proveedor_id',
        compras_proveedores::raw('SUM(CASE WHEN compras_proveedores.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN compras_proveedores.deuda ELSE 0 END) as deuda_30_dias'),
        compras_proveedores::raw('SUM(CASE WHEN compras_proveedores.created_at >= DATE_SUB(NOW(), INTERVAL 61 DAY) AND compras_proveedores.created_at < DATE_SUB(NOW(), INTERVAL 31 DAY) THEN compras_proveedores.deuda ELSE 0 END) as deuda_60_dias'),
        compras_proveedores::raw('SUM(CASE WHEN compras_proveedores.created_at < DATE_SUB(NOW(), INTERVAL 61 DAY) THEN compras_proveedores.deuda ELSE 0 END) as deuda_90_dias'),
        compras_proveedores::raw('SUM(compras_proveedores.deuda) as total')
    )
    ->where('compras_proveedores.eliminado', 0)
    ->where('compras_proveedores.comercio_id',$comercio_id)
    ->groupBy('compras_proveedores.proveedor_id');
    
    $deudas = $deudas_query->get()->keyBy('proveedor_id');
    
    // Consulta para obtener los saldos iniciales agrupados por proveedor
    $saldos_iniciales = saldos_iniciales::select(
        Sale::raw('SUM(saldos_iniciales.monto) as saldo_inicial_cuenta_corriente'),
        'referencia_id as proveedor_id'
    )
    ->where('saldos_iniciales.tipo', 'proveedor')
    ->where('saldos_iniciales.eliminado', 0)
    ->where('saldos_iniciales.comercio_id', $comercio_id)
    ->groupBy('referencia_id')
    ->get()
    ->keyBy('proveedor_id');
    
    // Combina los resultados
    foreach ($proveedores as $proveedor) {
        $proveedor_id = $proveedor->id;
    
        // Agrega las deudas al proveedor si existen
        if (isset($deudas[$proveedor_id])) {
            $proveedor->deuda_30_dias = $deudas[$proveedor_id]->deuda_30_dias;
            $proveedor->deuda_60_dias = $deudas[$proveedor_id]->deuda_60_dias;
            $proveedor->deuda_90_dias = $deudas[$proveedor_id]->deuda_90_dias;
            $proveedor->total = $deudas[$proveedor_id]->total;
        } else {
            $proveedor->deuda_30_dias = 0;
            $proveedor->deuda_60_dias = 0;
            $proveedor->deuda_90_dias = 0;
            $proveedor->total = 0;
        }
    
        // Agrega el saldo inicial al proveedor si existe
        if (isset($saldos_iniciales[$proveedor_id])) {
            $proveedor->saldo_inicial_cuenta_corriente = $saldos_iniciales[$proveedor_id]->saldo_inicial_cuenta_corriente;
            $proveedor->total = $proveedor->total + $saldos_iniciales[$proveedor_id]->saldo_inicial_cuenta_corriente;
        } else {
            $proveedor->saldo_inicial_cuenta_corriente = 0;
             $proveedor->total = $proveedor->total;
        }
    }

    // Ordenar los clientes según la columna y dirección especificadas
    $proveedores = $proveedores->sortBy(function($proveedor) {
        return $proveedor->{$this->columnaOrden};
    }, SORT_REGULAR, $this->direccionOrden === 'desc');
    
    return $proveedores;

    } 
    
    public function GetDataCtaCteOld($comercio_id){
                $compras_proveedores = proveedores::
        leftjoin('compras_proveedores','compras_proveedores.proveedor_id','proveedores.id')
        ->select(
            compras_proveedores::raw('SUM(CASE WHEN compras_proveedores.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN compras_proveedores.deuda ELSE 0 END) as deuda_30_dias'),
            compras_proveedores::raw('SUM(CASE WHEN compras_proveedores.created_at >= DATE_SUB(NOW(), INTERVAL 61 DAY) AND compras_proveedores.created_at < DATE_SUB(NOW(), INTERVAL 31 DAY) THEN compras_proveedores.deuda ELSE 0 END) as deuda_60_dias'),
            compras_proveedores::raw('SUM(CASE WHEN compras_proveedores.created_at < DATE_SUB(CURDATE(), INTERVAL 60 DAY) THEN compras_proveedores.deuda ELSE 0 END) as deuda_90_dias'),
            compras_proveedores::raw('SUM(compras_proveedores.deuda) as total'),
            'proveedores.id',
            'proveedores.id_proveedor',
            'proveedores.nombre as nombre_proveedor'
        )
        ->where('proveedores.comercio_id', $comercio_id);
          if($this->search) {
            $compras_proveedores = $compras_proveedores->where('proveedores.nombre', 'like', '%' . $this->search . '%');
          }

        $compras_proveedores = $compras_proveedores
        ->groupBy('proveedores.id_proveedor','proveedores.id', 'proveedores.nombre')
        ->orderBy('proveedores.id', 'desc')
        ->get();
        

      $saldos_iniciales = saldos_iniciales::select(
          Sale::raw('SUM(saldos_iniciales.monto) as saldo_inicial_cuenta_corriente'),'referencia_id as proveedor_id')
          ->where('saldos_iniciales.tipo','proveedor')
          ->where('saldos_iniciales.eliminado',0)
          ->where('saldos_iniciales.comercio_id',$comercio_id)
          ->groupBy('referencia_id')
          ->orderBy('referencia_id','desc')
          ->get();
      
      foreach ($compras_proveedores as $compras_proveedor) {
        foreach ($saldos_iniciales as $saldoInicial) {
            // Verificar si el ID del cliente mostrador coincide con el proveedor_id del saldo inicial
            if ($compras_proveedor->id == $saldoInicial->proveedor_id) {
                // Agregar el valor de saldo_inicial_cuenta_corriente al objeto ClientesMostrador
                $compras_proveedor->saldo_inicial_cuenta_corriente = $saldoInicial->saldo_inicial_cuenta_corriente;
                // Romper el bucle interior ya que hemos encontrado el saldo inicial para este cliente mostrador
                break;
            }
        }
        }

    }


	public function render()
	{
        if (!Auth::check()) {
            // Redirigir al inicio de sesión y retornar una vista vacía
            $this->redirectLogin();
            return view('auth.login');
        }
        
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;
    $casa_central_id = Auth::user()->casa_central_user_id;
    
    $compras_proveedores = $this->GetDataCtaCte($comercio_id,$casa_central_id);
    
        
    $this->caja_seleccionada = cajas::find($this->caja);
    
    $this->ultimas_cajas = cajas::where('comercio_id', $comercio_id)->where('eliminado',0)->orderby('created_at','desc')->limit(5)->get();
    
    $this->comercio_id = $comercio_id;

    //$metodo_pagos = bancos::where('bancos.comercio_id', 'like', $comercio_id)->get();
    $metodo_pagos = $this->getBancos($comercio_id);
    $this->metodo_pago = $metodo_pagos;
      
    return view('livewire.ctacte-proveedores.component',[
      'data' => $compras_proveedores
    ])
    ->extends('layouts.theme-pos.app')
    ->section('content');
  }




  public function showCart() {
      return view('livewire.ventas-fabrica.cart')
      ->extends('layouts.theme.app')
      ->section('content');
  }





  public function Agregar() {

    $cart = new Cart;
    $items = $cart->getContent();


 if ($items->contains('id', $this->selected_id)) {

   $cart = new Cart;
   $items = $cart->getContent();

   $product = Product::find($this->selected_id);

   foreach ($items as $i)
{
       if($i['id'] === $product['id']) {

         $cart->removeProduct($i['id']);

         $product = array(
             "id" => $i['id'],
             "name" => $i['name'],
             "price" => $i['price'],
             "cost" => $i['cost'],
             "qty" => $i['qty']+1,
         );

         $cart->addProduct($product);

     }
}

    $this->resetUI();

    $this->emit('product-added','Producto agregado');

   return back();

} else {

      $cart = new Cart;

      $product = array(
          "id" => $this->selected_id,
          "barcode" => $this->barcode,
          "name" => $this->name,
          "price" => $this->price,
          "cost" => $this->cost,
          "qty" => $this->cantidad,
      );

      $cart->addProduct($product);

      $this->resetUI();

      $this->emit('product-added','Producto agregado');

  }

}

    public function removeProductFromCart(Product $product) {
        $cart = new Cart;
        $cart->removeProduct($product->id);
        session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
        return back();
    }


  public function Incrementar(Product $product) {
      $cart = new Cart;
      $items = $cart->getContent();


      foreach ($items as $i)
   {
          if($i['id'] === $product['id']) {

            $cart->removeProduct($product['id']);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "price" => $i['price'],
                "cost" => $i['cost'],
                "qty" => $i['qty']+1,
            );

            $cart->addProduct($product);

        }
   }

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }

  public function Decrecer(Product $product) {
      $cart = new Cart;
      $items = $cart->getContent();


      foreach ($items as $i)
   {
          if($i['id'] === $product['id']) {

            $cart->removeProduct($product['id']);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "price" => $i['price'],
                "cost" => $i['cost'],
                "qty" => $i['qty']-1,
            );

            $cart->addProduct($product);

        }
   }

      session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
      return back();
  }

  // escuchar eventos
  protected $listeners = [
    'scan-code'  =>  'BuscarCode',
  ];

  public function BuscarCode($barcode)
  {


    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $record = Product::where('barcode',$barcode)->where('comercio_id', $comercio_id)->first();

    if($record == null || empty($record))
    {

    $this->emit('scan-notfound','El producto no está registrado');

    $this->codigo = '';

    }  else {

    $this->selected_id = $record->id;
    $this->name = $record->name;
    $this->barcode = $record->barcode;
    $this->cost = $record->cost;
    $this->price = $record->price;
    $this->stock = $record->stock;
    $this->categoryid = $record->categorias_fabrica_id;
    $this->image = null;

    $this->emit('show-modal','Show modal!');

    $this->codigo = '';
  }

}

  public function AbrirModal($id)
	{
		$record = Product::find($id);

		$this->selected_id = $record->id;
		$this->name = $record->name;
		$this->barcode = $record->barcode;
		$this->cost = $record->cost;
		$this->price = $record->price;
		$this->stock = $record->stock;
		$this->categoryid = $record->categorias_fabrica_id;
		$this->image = null;

		$this->emit('show-modal','Show modal!');
	}

  public function Edit2($monto_total)
	{


    $this->monto_total = $monto_total;

		$this->emit('show-modal2','Show modal!');
	}

  public function MontoPago()
  {


    $this->deuda = $this->monto_total - $this->pago;

    $this->emit('show-modal2','Show modal!');
  }

  public function orders()
  {
      $orders = auth()->user()->processedOrders();
      $suma = 0;
      return view('products.orders', compact('orders', 'suma'));
  }

  // reset values inputs
  public function resetUI()
  {
    $this->name ='';
    $this->cantidad =1;
    $this->barcode ='';
    $this->cost ='';
    $this->price ='';
    $this->stock ='';
    $this->alerts ='';
    $this->search ='';
    $this->categoryid = 'Elegir';
    $this->image = null;
    $this->selected_id = 0;

  }

  // guardar venta
  public function saveSale()
  {

    if($this->metodo_pago_elegido == "Elegir")
    {
      $this->emit('sale-error','DEBE ELEGIR LA FORMA DE PAGO');
      return;
    }

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $this->caja = cajas::where('estado',0)->where('comercio_id',$comercio_id)->max('id');

    $cart = new Cart;

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;


    DB::beginTransaction();

    try {

            $sale = compras_proveedores::create([
              'total' => $cart->totalAmount(),
              'items' => $cart->totalCantidad(),
              'pago' => $this->pago,
              'proveedor_id' => 1,
              'comercio_id' => $comercio_id,
            ]);

            $pagos = pagos_facturas::create([
              'monto_compra' => $this->pago,
              'id_compra' => $sale->id,
              'comercio_id' => $comercio_id,
              'caja' => $this->caja,
              'metodo_pago'  => $this->metodo_pago_elegido,
              'eliminado' => 0
            ]);

      if($sale)
      {
          $items = $cart->getContent();

        foreach ($items as  $item) {
          detalle_compra_proveedores::create([
            'producto_id' => $item['id'],
            'precio' => $item['cost'],
            'cantidad' => $item['qty'],
            'comercio_id' => $comercio_id,
            'compra_id' => $sale->id
          ]);

          //update stock
          $product = Product::find($item['id']);
          $product->stock = $product->stock + $item['qty'];
          $product->save();

          $historico_stock = historico_stock::create([
            'tipo_movimiento' => 9,
            'producto_id' => $item['id'],
            'cantidad_movimiento' => $item['qty'],
            'stock' => $product->stock,
            'comercio_id'  => $comercio_id,
            'usuario_id'  => Auth::user()->id
          ]);
        }

      }


      DB::commit();

      $this->pago = 0;
      $this->monto = 0;
      $this->metodo_pago_elegido = 'Elegir';

      $cart->clear();
      $this->emit('sale-ok','Compra registrada con éxito');



    } catch (Exception $e) {
      DB::rollback();
      $this->emit('sale-error', $e->getMessage());
    }

  }












  public function MontoPagoEditarPago($value)
  {
  $metodo_pago = metodo_pago::find($this->metodo_pago_agregar_pago);

  $this->MontoPagoEditarPago = $value;

  $this->recargo = $metodo_pago->recargo/100;

  $this->recargo_total = $this->MontoPagoEditarPago * $this->recargo;

  $this->total_pago = $this->recargo_total + $this->MontoPagoEditarPago;

  }



  ////////////// FACTURA ///////////////




public function ActualizarEstadoDeuda($ventaId)
{
  /////////////////   ACTUALIZAR ESTADO DE PAGO   /////////////////////


       $this->data_total = compras_proveedores::select('compras_proveedores.total','compras_proveedores.deuda')
       ->where('compras_proveedores.id', $ventaId)
       ->get();

       $this->pagos2 = pagos_facturas::join('metodo_pagos as mp','mp.id','pagos_facturas.metodo_pago')
       ->select('mp.nombre as metodo_pago','pagos_facturas.id_compra','pagos_facturas.monto_compra','pagos_facturas.created_at as fecha_pago')
       ->where('pagos_facturas.id_compra', $ventaId)
       ->where('pagos_facturas.eliminado',0)
       ->get();


       $this->suma_monto = $this->pagos2->sum('monto_compra');

       $this->tot = $this->data_total->sum('total');


       $deuda = $this->tot - $this->suma_monto;



      $this->deuda_vieja = compras_proveedores::find($ventaId);

       $this->deuda_vieja->update([
         'deuda' => $deuda
         ]);


       ///////////////////////////////////////////////////////////////////
}


public function DeletePago($id)
{
      $this->pago_viejo = pagos_facturas::find($id);

       $ventaId = $this->pago_viejo->id_compra;

       $ventas_vieja = compras_proveedores::find($ventaId);


        $this->pago_viejo->delete();

         $this->beneficios_viejo->delete();

          $this->emit('pago-eliminado', 'El pago fue eliminado.');

           $this->ActualizarEstadoDeuda($ventaId);

          $this->RenderFactura($ventaId);

          $this->estado = "display: block;";
}



public function RenderDetalle($id){
    $this->ver = 1;
    
}

public function RenderSaldoInicial($value){
$this->selected_id = $value;

$this->saldos_iniciales = [];

$this->saldos_iniciales = saldos_iniciales::join('bancos','bancos.id','saldos_iniciales.metodo_pago')
->select('saldos_iniciales.*','bancos.nombre as nombre_banco')
->where("saldos_iniciales.tipo","proveedor")
->where("saldos_iniciales.referencia_id",$value)
->where('saldos_iniciales.eliminado',0)
->get();

$this->sum_si = $this->saldos_iniciales->sum('monto');

$this->emit("show-modal-saldos-iniciales",$value);    
}

public function CerrarSaldoInicial(){
$this->emit("hide-modal-saldos-iniciales","");        
}

public function ModalAgregarEditarPago($valor,$proveedor_id){

//dd($valor);
$this->formato_modal = $valor;
$this->id_pago = $valor;
$this->selected_id = $proveedor_id;

if(0 < $valor){
$value = saldos_iniciales::where("tipo","proveedor")->where("id",$valor)->first();  
$this->monto_ap = abs($value->monto); 
$this->metodo_pago_agregar_pago = $value->metodo_pago;
} else {
$this->monto_ap = 0; 
$this->metodo_pago_agregar_pago = 1;
}

$this->emit("show-agregar-editar","");        
}

function CreatePago(){
    $this->CrearPagoSaldoInicial('proveedor',$this->selected_id,$this->comercio_id,$this->monto_ap,$this->metodo_pago_agregar_pago,$this->caja);
    $this->RenderSaldoInicial($this->selected_id);
	$this->emit("hide-agregar-editar","");
}

public function ActualizarPago($pago_id){
    $this->ActualizarPagoSaldoInicial($pago_id,'proveedor',$this->selected_id,$this->comercio_id,$this->monto_ap,$this->metodo_pago_agregar_pago,$this->caja);
    $this->RenderSaldoInicial($this->selected_id);
	$this->emit("hide-agregar-editar","");
}

public function DeletePagoSaldo($pago_id) {
    $this->DeletePagoSaldoInicial($pago_id,'proveedor',$this->selected_id);	
	$this->CerrarSaldoInicial();
    $this->RenderSaldoInicial($this->selected_id);
}

public function ResetPago() {
  $this->metodo_pago_agregar_pago = 1;
  $this->monto_ap = 0;
  $this->formato_modal = 0;
  $this->selected_id = 0;
  $this->metodo_pago_ap = 1;
  $this->fecha = Carbon::now()->format('d-m-Y');
}

function CerrarAgregarPago($proveedor_id) {

  $this->emit('hide-modal-saldos-iniciales','details loaded');
  $this->ResetPago();

  $this->RenderSaldoInicial($proveedor_id);

}


  public function RenderFactura($ventaId)
     {

        $this->GetEtiquetasEdit($ventaId,"compras",$this->comercio_id);
        
        $this->accion = 1;
        $this->NroVenta = $ventaId;

       //////////////// PAGOS //////////////
             $this->pagos2 = pagos_facturas::join('bancos as mp','mp.id','pagos_facturas.banco_id')
             ->leftjoin('cajas','cajas.id','pagos_facturas.caja')
             ->select('mp.nombre as metodo_pago','pagos_facturas.id','cajas.nro_caja','pagos_facturas.monto_compra','pagos_facturas.actualizacion','pagos_facturas.actualizacion','pagos_facturas.created_at as fecha_pago')
             ->where('pagos_facturas.id_compra', $ventaId)
             ->where('pagos_facturas.eliminado',0)
             ->get();

             $this->detalle_proveedor = proveedores::join('compras_proveedores','compras_proveedores.proveedor_id','proveedores.id')
             ->select('proveedores.*')
             ->where('compras_proveedores.id', $ventaId)
             ->get();
    
            $suma_monto = 0;
            
            // pagos        
            foreach ($this->pagos2 as $pago) {
                $montoCompra = $pago->monto_compra;
                $actualizacion = $pago->actualizacion;
            
                // Realizar el cálculo y agregar al total
                $suma_monto += $montoCompra * (1 + $actualizacion);
            }

             $this->suma_monto = $suma_monto;

             $this->ventaId = $ventaId;

             $this->estado = "display: none;";
             $this->estado2 = "display: none;";


  /////////////// DETALLE DE VENTA /////////////////////7
         $this->dci = detalle_compra_proveedores::where('detalle_compra_proveedores.compra_id', $ventaId)->where('detalle_compra_proveedores.eliminado', 0)->get();

         $this->total = compras_proveedores::where('compras_proveedores.id', $ventaId)->get();

         foreach($this->total as $t) {
         $proveedor_id = $t->proveedor_id;
         $iva_compra = $t->alicuota_iva;
         $fecha_compra = $t->created_at;
         $nro_compra = $t->nro_compra;
         $actualizacion = $t->actualizacion;
         $descuento = $t->descuento;
         $observacion = $t->observaciones;
         $porcentaje_descuento = $t->porcentaje_descuento;
         }        
         
         $fechaFormateada = Carbon::parse($fecha_compra)->format('Y-m-d');
     
         $this->actualizacion = $actualizacion;
         $this->selected_id = $proveedor_id;
         $this->iva_compra = $iva_compra;
         $this->fecha_compra = $fechaFormateada;
         $this->Nro_Compra = $nro_compra;
         $this->descuento = $descuento;
         $this->observacion = $observacion;
         $this->porcentaje_descuento = $porcentaje_descuento*100;
            
         //dd($this->iva_compra);
            
          $this->emit('modal-show','Show modal');

        //
     }


public function ExportarReporte() {
    
    $search = $this->search ?? 0;
    $url = 'report-cta-cte-proveedor/excel/'. $search .'/'. Carbon::now()->format('d_m_Y_H_i_s');
    return redirect($url);

}


public function CrearPago(){
   if($this->metodo_pago_agregar_pago == 1) {
    $mp = 1;
    } else {$mp = 0;}
    
    $estado_pago = $this->GetPlazoAcreditacionPago($this->metodo_pago_agregar_pago); //18-5-2024
    
    $pago_factura =   pagos_facturas::create([
      'estado_pago' => $estado_pago,
      'monto_compra' => $this->monto_ap,
      'caja' => null,
      'banco_id' => $this->metodo_pago_agregar_pago,
      'metodo_pago' => $mp,
      'created_at' => Carbon::now(),
      'proveedor_id' => $this->selected_id,
      'comercio_id' => $this->comercio_id,
      'id_compra' => 0,
      'eliminado' => 0
    ]);
	    
}


//18-5-2024
public function GetPlazoAcreditacionPago($id){
    return $id == 1 ? 1 : 0;
}

public function ElegirCaja($caja_id)
{
$this->caja = $caja_id;
$this->emit('listado-cajas-hide','close');
}

public function CambioCaja() {

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $this->fecha_pedido_desde = $this->fecha_ap.' 00:00:00';

  $this->fecha_pedido_hasta = $this->fecha_ap.' 23:59:50';

  $this->emit('listado-cajas-show','');

  $this->lista_cajas_dia = cajas::where('comercio_id', $comercio_id)->where('eliminado',0)->whereBetween('fecha_inicio',[$this->fecha_pedido_desde, $this->fecha_pedido_hasta])->get();

}   
   
public function ModalAbrirCaja() {
//$this->emit('agregar-pago-hide','');
$this->emit('abrir-caja-show','');
}


public function AplicarElegirSucursal(){
    // Obtener las claves donde el valor es true
    $clavesTrue = array_keys($this->selectedSucursalesCheckbox, true);
    
    foreach($this->selectedSucursalesCheckbox as $key => $value){
      $this->selectedSucursalesCheckbox[$key] = $value;  
    }
      
    // Reindexar el array
    $this->selectedSucursales = array_values($clavesTrue);
    $selectedSucursalesStock = array_values($clavesTrue);
    
    // Reemplazar 295 con 0 si está presente en el array
    foreach ($selectedSucursalesStock as $key => $value) {
        if ($value == $this->casa_central_id) {
            $selectedSucursalesStock[$key] = 0;
        }
    }
    
    $this->selectedSucursalesStock = $selectedSucursalesStock;
    $this->sucursales_elegidas = implode(",",$this->selectedSucursales);
}

   public function getBancos($comercio_id){
    return bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
    ->where('bancos_muestra_sucursales.muestra', 1)
    ->where('bancos_muestra_sucursales.sucursal_id', $comercio_id)
    ->orderBy('bancos.nombre','asc')
    ->get();
    
   
  }
  
  
}
