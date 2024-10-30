<?php

namespace App\Http\Livewire;

use Darryldecode\Cart\Facades\CartFacade as Cart;
use App\Models\Denomination;
use Illuminate\Support\Facades\Auth;
use App\Models\SaleDetail;
use App\Models\beneficios;
use App\Models\User;
use App\Models\pagos_facturas;
use App\Models\hoja_ruta;
use App\Models\ClientesMostrador;
use App\Models\metodo_pago;
use Livewire\Component;
use Carbon\Carbon;
use App\Traits\CartTrait;
use App\Models\Product;
use App\Models\Sale;
use DB;

class PosAltoController extends Component
{
	use CartTrait;

	public $total,$itemsQuantity, $beneficios, $comprobante, $ventaIdEmail, $ventaId, $id_venta_email, $id_venta , $efectivo, $change, $metodo_pago, $comercio_id, $usuario_id, $comentarios,$canal_venta, $coment, $nota_interna, $usuario_activo, $componentName, $selected_id, $pago_parcial, $check, $estado_pedido, $id_pedido, $estado_estado, $NroVenta, $suma_monto, $suma_cash, $tot, $hojar, $hoja_ruta, $monto, $estado, $estado2, $nombre_hr, $tipo, $fecha_hr, $turno, $observaciones_hr;


	public $mail = [];
	public $listado_hojas_ruta = [];
	public $pagos1 = [];
	public $pagos2 = [];
	public $total_total = [];
	public $usuario = [];
	public $fecha = [];
	public $detalle_cliente = [];
	public $detalle_venta = [];

	public $Id_cart;
	public $query;
	public $date;
	public $query_id;
	public $products_s;
	public $query_product;
	public $recargo;
	public $metodo_pago_nuevo;
	public $contacts;
	public $observaciones;
	public $highlightIndex;

	public $nombre,$telefono,$email,$status,$image,$provincia,$localidad,$fileLoaded,$direccion,$barrio,$dni, $cliente, $clientes;
	public $pageTitle, $search;

public function AgregarClienteModal() {

	$this->emit('agregar-cliente', 'agregar');
}

	public function Store()
	{
		$rules  =[
			'nombre' => 'required|min:3',
			'email' => 'required|email',
			'status' => 'required|not_in:Elegir',
			'telefono' => 'required|min:3',
			'dni' => 'required|min:6',
			'provincia' => 'required|min:3',
			'localidad' => 'required|min:3',
			'direccion' => 'required|min:3'

		];

		$messages = [
			'nombre.required' => 'Nombre del cliente es requerido',
			'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
			'email.required' => 'Ingresa el correo ',
			'email.email' => 'Ingresa un correo válido',
			'dni.required' => 'El dni es requerido',
			'dni.min:8' => 'El dni debe tener minimo 6 digitos',
			'status.required' => 'Selecciona el estatus del usuario',
			'status.not_in' => 'Selecciona el estatus',
			'provincia.required' => 'Ingrese la provincia',
			'provincia.min' => 'La provincia debe tener al menos 3 caracteres',
			'localidad.required' => 'Ingrese la localidad',
			'localidad.min' => 'La localidad debe tener al menos 3 caracteres',
			'direccion.required' => 'Ingrese la dirección',
			'direccion.min' => 'La dirección debe tener al menos 3 caracteres'
		];

		$this->validate($rules, $messages);

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$cliente = ClientesMostrador::create([
			'nombre' => $this->nombre,
			'telefono' => $this->telefono,
			'email' => $this->email,
			'direccion' => $this->direccion,
			'barrio' => $this->barrio,
			'localidad' => $this->localidad,
			'provincia' => $this->provincia,
			'status' => $this->status,
			'dni' => $this->dni,
			'comercio_id' => $comercio_id
			]);

		if($this->image)
		{
			$customFileName = uniqid() . '_.' . $this->image->extension();
			$this->image->storeAs('public/products', $customFileName);
			$cliente->image = $customFileName;
			$cliente->save();
		}

		$this->query = $cliente->nombre;
		$this->query_id = $cliente->id;

		$this->resetCliente();
		$this->resetUICliente();
		$this->emit('cliente-agregado', 'Cliente Registrado');


	}



	public function mount()
	{
		$this->usuario_activo = Auth::user()->id;
		$this->comprobante = Auth::user()->comprobante;
		$this->pago_parcial = Auth::user()->pago_parcial;
		$this->efectivo =0;
		$this->canal_venta = 'Mostrador';
		$this->change =0;
		$this->itemsQuantity = Cart::getTotalQuantity();
		$this->query = '';
		$this->recargo = 1;
		$componentName = 'Agregar cliente';
		$this->contacts = [];
		$this->highlightIndex = 0;
		$this->usuario_activo = Auth::user()->id;
		$this->observaciones = '';
		$this->date = Carbon::parse(Carbon::now())->format('Y-m-d');


		$this->metodo_pago = session('MetodoPago');
		$this->query = session('IdCliente');
		$this->query_id = session('NombreCliente');

		if($this->query_id == null) {
			$this->query = '';
			$this->query_id = '';

		} else {
			$this->query = session('NombreCliente');
			$this->query_id = session('IdCliente');
		}



		if($this->metodo_pago == null) {
			$this->metodo_pago = 1;
			$this->metodo_pago_nuevo = 1;
			$recargo = 1;

			$this->total  = Cart::getTotal()*$recargo;

			$this->efectivo = 0;
			$this->change = 0;

		} else {
			$this->metodo_pago = session('MetodoPago');
			$this->metodo_pago_nuevo = session('MetodoPago');

			$metodo_pago = metodo_pago::find($this->metodo_pago);

			$this->recargo = 1+($metodo_pago->recargo/100);

		$recargo = 1+($metodo_pago->recargo/100);

		$this->total  = Cart::getTotal()*$recargo;

		$this->efectivo = ($metodo_pago->nombre != 'Efectivo' && $this->metodo_pago != '0' ? $this->total : 0);
		$this->change = ($metodo_pago->nombre != 'Efectivo' && $this->metodo_pago != '0' ? $this->efectivo - $this->total : 0 );
		}




		if (Auth::user()->pago_parcial == 1) {
			$this->check = 'checked';
			$this->estado_pedido = '';
			$this->query_id = '';

			$this->pago_parcial = 1;

		} else {
			$this->check = '';
			$this->estado_pedido = 'Entregado';
			$this->query_id = 1;

			$this->pago_parcial = 0;

		}

	}

	public function render()
	{
		$usuario_id = Auth::user()->id;

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		return view('livewire.posalto.component', [
			'denominations' => Denomination::orderBy('value','desc')->get(),
			'metodos' => metodo_pago::where('metodo_pagos.comercio_id', 'like', $comercio_id)
			->orderBy('metodo_pagos.nombre','asc')->get(),
			'user' => User::where('comercio_id', 'like', $comercio_id)
			->orWhere('id', 'like', $usuario_id)->orderBy('id','asc')->get(),
			'cart' => Cart::getContent()->sortBy('name')
		])
		->extends('layouts.theme.app')
		->section('content');

		$this->usuario = Auth::user()->name;
	}

	// agregar efectivo / denominations
	public function ACash($value)
	{
		$this->efectivo = ($value == 0 ? $this->total : $value);
		$this->change = ($this->efectivo - $this->total);
	}

	public function cambio($value)
	{
		if(empty($value)) {
			$this->efectivo = 0;
			$this->change = 0;
		} else {
		$this->efectivo = $value;
		$this->change = ($this->efectivo - $this->total);
	}

	}

	public function EliminarMoneda($value)
	{
		$this->efectivo = $value;
		$this->change = 0;
	}


	public function UpdateEstado($estado_id)
	{

	$this->estado_pedido = $estado_id;

	 session(['EstadoPedido' => $this->estado_pedido]);

	 	$this->emit('modal-estado-hide','');

	}



	public function MetodoPago($value)
	{

$metodo_pago = metodo_pago::find($value);

$this->recargo = 1+($metodo_pago->recargo/100);

 session(['MetodoPago' => $value]);

$this->metodo_pago_nuevo = $metodo_pago->id;
$this->total = Cart::getTotal() * (1+($metodo_pago->recargo/100));

 $this->efectivo = ($metodo_pago->nombre != 'Efectivo' && $value != '0' ? $this->total : 0);
 $this->change = ($metodo_pago->nombre != 'Efectivo' && $value != '0' ? $this->efectivo - $this->total : 0 );



	}


		public function MetodoComprobante($value)
	{

//update metodo comprobante
					$user = User::find(Auth::user()->id);
					$user->comprobante = $value;
					$user->save();


	}

	public function CheckPagoParcial($value)
{

if($value == "1") {
	$user = User::find(Auth::user()->id);
	$user->pago_parcial = "0";
	$user->save();
	$this->pago_parcial = "0";
	$this->check = '';
}

if($value == "0") {
	$user = User::find(Auth::user()->id);
	$user->pago_parcial = "1";
	$user->save();
	$this->pago_parcial = "1";
	$this->check = 'checked';

}



}
	// escuchar eventos
	protected $listeners = [
		'deletePago' => 'DeletePago',
		'scan-code'  =>  'ScanCode',
		'removeItem' => 'removeItem',
		'clearCart'  => 'clearCart',
		'saveSale'   => 'saveSale',
		'refresh' => '$refresh',
		'print-last' => 'printLast'
	];

	public function MetodoPagoSession(){

		$this->metodo_pago = session('MetodoPago');

		if($this->metodo_pago == null) {
			$this->metodo_pago = 1;
			$this->metodo_pago_nuevo = 1;
			$recargo = 1;

			$this->total  = Cart::getTotal()*$recargo;

			$this->efectivo =  0;
			$this->change = 0;

		} else {
			$this->metodo_pago = session('MetodoPago');
			$this->metodo_pago_nuevo = session('MetodoPago');

			$metodo_pago = metodo_pago::find($this->metodo_pago);

			$this->recargo = 1+($metodo_pago->recargo/100);

		$recargo = 1+($metodo_pago->recargo/100);

		$this->total  = Cart::getTotal()*$recargo;


		$this->efectivo = ($metodo_pago->nombre != 'Efectivo' && $this->metodo_pago != '0' ? $this->total : 0);
		$this->change = ($metodo_pago->nombre != 'Efectivo' && $this->metodo_pago != '0' ? $this->efectivo - $this->total : 0 );
		}
	}


	// buscar y agregar producto por escaner y/o manual
	public function ScanCode($barcode, $cant = 1)
	{
		$this->ScanearCode($barcode, $cant);

		$this->MetodoPagoSession();
	}

	// incrementar cantidad item en carrito
	public function increaseQty(Product $product, $cant = 1)
	{
		$this->IncreaseQuantity($product, $cant);

		$this->MetodoPagoSession();
	}


	// actualizar cantidad item en carrito
	public function updateQty(Product $product, $cant = 1)
	{
		if($cant <=0)
			$this->removeItem($product->id);
		else
			$this->UpdateQuantity($product, $cant);

			$this->MetodoPagoSession();
	}

	// actualizar el precio del item en carrito
	public function updatePrice(Product $product, $cant = 1)
	{
			$this->UpdatePrecio($product, $cant);

	}

	// actualizar el precio del item en carrito
	public function updateAlt(Product $product, $cant = 1)
	{
			$this->UpdateAlto($product, $cant);

	}

	// actualizar el precio del item en carrito
	public function updateAn(Product $product, $cant = 1)
	{
			$this->UpdateAncho($product, $cant);

	}






	// decrementar cantidad item en carrito
	public function decreaseQty($productId)
	{
		$this->decreaseQuantity($productId);

		$this->MetodoPagoSession();
	}



	// vaciar carrito
	public function clearCart()
	{
		$this->trashCart();
	}



	// guardar venta
	public function saveSale()
	{
		if($this->total <=0)
		{
			$this->emit('sale-error','AGREGA PRODUCTOS A LA VENTA');
			return;
		}
			if($this->efectivo <=0)
		{
			$this->emit('sale-error','INGRESA EL EFECTIVO');
			return;
		}

		if($this->pago_parcial  == 1)
		{
			if($this->query_id == '')
			{
				$this->emit('sale-error','AGREGA UN CLIENTE A LA VENTA');
				return;
			}

			if($this->estado_pedido == '')
			{
				$this->emit('sale-error','AGREGA UN ESTADO A LA VENTA');
				return;
			}
		}


		if($this->query_id == '')
		{
			$this->query_id = 1 ;
		}

		if($this->estado_pedido == '')
		{
			$this->estado_pedido = 'Entregado';
		}

		DB::beginTransaction();

		try {


						if(Auth::user()->comercio_id != 1)
						$comercio_id = Auth::user()->comercio_id;
						else
						$comercio_id = Auth::user()->id;

						$this->deuda = $this->total - $this->efectivo;


						if($this->efectivo >= $this->total) {

						$sale = DB::table('sales')->create([
							'total' => $this->total,
							'items' => $this->itemsQuantity,
							'cash' => $this->efectivo,
							'change' => $this->change,
							'metodo_pago'  => $this->metodo_pago_nuevo,
							'comercio_id' => $comercio_id,
							'cliente_id' => $this->query_id,
							'user_id' => $this->usuario_activo,
							'observaciones' => $this->observaciones,
							'fecha_entrega' => $this->date,
							'canal_venta' => $this->canal_venta,
							'estado_pago' => 'Pago',
							'deuda' => $this->deuda,
							'status' => $this->estado_pedido,
							'nota_interna' => $this->nota_interna
						]);



					} else {
						$sale = DB::table('sales')->create([
							'total' => $this->total,
							'items' => $this->itemsQuantity,
							'cash' => $this->efectivo,
							'change' => $this->change,
							'metodo_pago'  => $this->metodo_pago_nuevo,
							'comercio_id' => $comercio_id,
							'cliente_id' => $this->query_id,
							'user_id' => $this->usuario_activo,
							'observaciones' => $this->observaciones,
							'fecha_entrega' => $this->date,
							'canal_venta' => $this->canal_venta,
							'estado_pago' => 'Pendiente',
							'deuda' => $this->deuda,
							'status' => $this->estado_pedido,
							'nota_interna' => $this->nota_interna
						]);



					}

			if($sale)
			{
				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$beneficios = DB::table('beneficios')::create([
					'ventas' => $this->total,
					'id_padre' => $sale->id,
					'comercio_id' => $comercio_id,
				]);

				$items = Cart::getContent();


				foreach ($items as  $item) {
					DB::table('sale_details')->create([
						'price' => $item->price*$this->recargo,
						'quantity' => $item->quantity,
						'metodo_pago'  => $this->metodo_pago_nuevo,
						'product_id' => $item->id,
						'seccionalmacen_id' => $item->attributes->seccionalmacen_id,
						'comercio_id' => $item->attributes->comercio_id,
						'comentario' => $item->attributes->comentario,
						'sale_id' => $sale->id,
						'canal_venta' => $this->canal_venta,
						'cliente_id' => $this->query_id
					]);

					//update stock
					$product = DB::table('products')->find($item->id);
					$product->stock = $product->stock - $item->quantity;
					$product->save();
				}

			}


			DB::commit();

			Cart::clear();

			$this->usuario_activo = Auth::user()->id;
			$this->efectivo =0;
			$this->metodo_pago =1;
			$this->metodo_pago_nuevo = 1;
			$this->change =0;
			$this->query = '';
			session(['MetodoPago' => '']);
			session(['IdCliente' => '']);
			session(['NombreCliente' => '']);
			$this->query_id = '';
			$this->recargo = 1;
			$componentName = 'Agregar cliente';
			$this->contacts = [];
			$this->highlightIndex = 0;
			$this->usuario_activo = Auth::user()->id;
			$this->date = Carbon::parse(Carbon::now())->format('Y-m-d');
			$this->canal_venta = 'Mostrador';
			$this->efectivo =0;
			$this->change =0;
			$this->observaciones ='';
			$this->total = Cart::getTotal();
			$this->itemsQuantity = Cart::getTotalQuantity();

			if (Auth::user()->pago_parcial == 1) {
				$this->check = 'checked';
				$this->estado_pedido = '';

			} else {
				$this->check = '';
				$this->estado_pedido = 'Entregado';

			}


            if(Auth::user()->comprobante == 1) {


			$this->emit('sale-ok','Venta registrada con Exito');

			$this->emit('print-ticket', $sale->id);

			} else {

			$this->Factura($sale->id);

			}


		} catch (Exception $e) {
			DB::rollback();
			$this->emit('sale-error', $e->getMessage());
		}

	}



	public function printTicket($ventaId)
	{
		return \Redirect::to("print://$ventaId");

	}

	public function Factura($ventaId)
	{

		$this->RenderFactura($ventaId);

	}

	public function resetUI()
	{
		$this->comentario = '';

	}

	public function resetUICliente()
	{

	$this->name ='';
	$this->barcode ='';
	$this->cost ='';
	$this->price ='';
	$this->stock ='';
	$this->alerts ='';
	$this->almacen ='';
	$this->proveedor ='';
	$this->search ='';
	$this->inv_ideal ='';
	$this->categoryid ='Elegir';
	$this->image = null;
	$this->selected_id = 0;
	$this->stock_descubierto = 'si';

}
	public function comentario($saleId)
	{

		$this->Id_cart = $saleId;

		$item = Cart::get($saleId);

		$this->comentarios = $item->attributes['comentario'];



		$this->emit('show-modal','details loaded');

	}

	public function guardarComentario($productId)
	{
		$item = Cart::get($productId);
		Cart::remove($productId);

		// si el producto no tiene imagen, mostramos una default
		$img = (count($item->attributes) > 0 ? $item->attributes['image'] : Product::find($productId)->imagen);

		$newQty = ($item->quantity);
		$coment = $this->comentarios;

		Cart::add(array(
		'id' => $item->id,
		'name' => $item->name,
		'price' => $item->price,
		'quantity' => $newQty,
		'attributes' => array(
		'image' => $img,
		'seccionalmacen_id' => $item->attributes['seccionalmacen_id'],
		'comercio_id' => $item->attributes['comercio_id'],
		'stock' => $item->attributes['stock'],
		'stock_descubierto' => $item->attributes['stock_descubierto'],
		'comentario' => $coment
		)));


						//Cart::add($item->id, $item->name, $item->price, $newQty, $item->attributes[0]);

		$metodopago = metodo_pago::find($this->metodo_pago);

		$this->total = Cart::getTotal() * (1+($metodopago->recargo/100));

		$this->itemsQuantity = Cart::getTotalQuantity();
		$this->emit('hide-modal','details loaded');
		$this->emit('scan-ok', 'Comentario añadido.');

	}








	public function printLast()
	{
		$lastSale = Sale::latest()->first();

		if($lastSale)
			$this->emit('print-last-id', $lastSale->id);
	}


	    public function resetCliente()
	    {
	      $this->contacts = [];
	    }

	      public function selectContact(ClientesMostrador $cliente)
	      {


	          $this->query = $cliente->nombre;
						$this->query_id = $cliente->id;

						session(['NombreCliente' => $this->query]);
						session(['IdCliente' => $this->query_id]);

	          $this->resetCliente();
	      }

	      public function updatedQuery()
	      {
					if(Auth::user()->comercio_id != 1)
					$comercio_id = Auth::user()->comercio_id;
					else
					$comercio_id = Auth::user()->id;

	          $this->contacts = ClientesMostrador::where('nombre', 'like', '%' . $this->query . '%')
								->where('comercio_id', 'like', $comercio_id)
								->orWhere('comercio_id', 'like', 1)
								->limit(8)
	              ->get()
	              ->toArray();
	      }

				public function resetProduct()
			 {
				 $this->products_s = [];
			 }

				public function selectProduct()
	      {
	          $this->query_product = '';

	          $this->resetProduct();
	      }

	      public function updatedQueryProduct()
	      {
					if(Auth::user()->comercio_id != 1)
					$comercio_id = Auth::user()->comercio_id;
					else
					$comercio_id = Auth::user()->id;



	          $this->products_s = 	Product::where('comercio_id', 'like', $comercio_id)->where( function($query) {
							    $query->where('eliminado',0)->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
							})
								->limit(5)
	              ->get()
	              ->toArray();


	      }


				////////////// FACTURA ///////////////



				public function RenderFactura($ventaId)
				   {

				     $this->NroVenta = $ventaId;


				     if(Auth::user()->comercio_id != 1)
				     $comercio_id = Auth::user()->comercio_id;
				     else
				     $comercio_id = Auth::user()->id;

				     $this->data_monto = Sale::leftjoin('pagos_facturas as p','p.id_factura','sales.id')
				     ->select('sales.cash','sales.created_at as fecha_factura','p.monto as monto','p.created_at as fecha_pago')
				     ->where('sales.id', $ventaId)
				     ->where('p.eliminado',0)
				     ->get();


				     $this->data_cash = Sale::select('sales.cash','sales.created_at as fecha_factura')
				     ->where('sales.id', $ventaId)
				     ->get();

				     $this->data_total = Sale::select('sales.total')
				     ->where('sales.id', $ventaId)
				     ->get();

				       $this->ventaId = $ventaId;
				       $this->hojar = hoja_ruta::join('sales','sales.hoja_ruta','hoja_rutas.id')->select('hoja_rutas.id')->where('sales.id', $ventaId)->first();
				       $this->suma_monto = $this->data_monto->sum('monto');
				       $this->suma_cash= $this->data_cash->sum('cash');
				       $this->tot = $this->data_total->sum('total');
				       $this->detalle_venta = SaleDetail::join('products as p','p.id','sale_details.product_id')
				       ->select('sale_details.id','sale_details.comentario','sale_details.price','sale_details.quantity','p.name as product', SaleDetail::raw('sale_details.price*sale_details.quantity as total'))
				       ->where('sale_details.sale_id', $ventaId)
				       ->where('sale_details.eliminado', 0)
				       ->get();
				       $this->total_total = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
				       ->select('sales.total','sales.created_at as fecha','sales.observaciones','sales.nota_interna', 'm.nombre as metodo_pago')
				       ->where('sales.id', $ventaId)
				       ->get();

				       $this->usuario = User::select('users.image','users.name')
				       ->where('users.id', $comercio_id)
				       ->get();

				       $this->fecha = Sale::select('sales.created_at')
				       ->where('sales.id', $ventaId)
				       ->get();
				       $this->detalle_cliente = Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
				       ->select('c.id','c.nombre','c.telefono','c.email','c.dni','c.direccion','c.barrio','c.localidad','c.provincia','sales.observaciones','sales.metodo_pago','sales.fecha_entrega')
				       ->where('sales.id', $ventaId)
				       ->get();
				       $this->mail = Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
				        ->select('c.email', 'sales.cash','sales.status')
				        ->where('sales.id', $ventaId)
				        ->get();
				        $this->pagos1 = Sale::select('sales.cash','sales.created_at as fecha_factura')
				        ->where('sales.id', $ventaId)
				        ->get();
				        $this->pagos2 = Sale::join('pagos_facturas as p','p.id_factura','sales.id')
				        ->select('sales.cash','sales.created_at as fecha_factura','p.id','p.monto','p.created_at as fecha_pago')
				        ->where('sales.id', $ventaId)
				        ->where('p.eliminado',0)
				        ->get();
				        $this->listado_hojas_ruta = hoja_ruta::where('hoja_rutas.comercio_id', $comercio_id)->where('hoja_rutas.fecha', '>', Carbon::now())->orderBy('hoja_rutas.fecha','desc')->get();
				        $this->hoja_ruta = hoja_ruta::join('sales','sales.hoja_ruta','hoja_rutas.id')->select('hoja_rutas.*')->where('sales.id', $ventaId)->get();


				$this->estado = "display: none;";
				$this->estado2 = "display: none;";

					$this->emit('modal-show','Show modal');
				              //
				   }

					 public function ActualizarEstadoDeuda($ventaId)
					 {
						 /////////////////   ACTUALIZAR ESTADO DE PAGO   /////////////////////

									$this->data_monto = Sale::leftjoin('pagos_facturas as p','p.id_factura','sales.id')
									->select('sales.cash','sales.created_at as fecha_factura','p.monto as monto','p.created_at as fecha_pago')
									->where('sales.id', $ventaId)
									->where('p.eliminado',0)
									->get();


									$this->data_cash = Sale::select('sales.cash','sales.created_at as fecha_factura')
									->where('sales.id', $ventaId)
									->get();

									$this->data_total = Sale::select('sales.total')
									->where('sales.id', $ventaId)
									->get();

									$this->suma_monto = $this->data_monto->sum('monto');
									$this->suma_cash= $this->data_cash->sum('cash');
									$this->tot = $this->data_total->sum('total');




									$deuda = $this->tot - ($this->suma_monto+$this->suma_cash);

								 $this->deuda_vieja = Sale::find($ventaId);

									$this->deuda_vieja->update([
										'deuda' => $deuda
										]);


									///////////////////////////////////////////////////////////////////
					 }


				   public function UpdatePago($id_pago, $cant = 1)
				   {


				           $this->pago_viejo = pagos_facturas::find($id_pago);

				          $ventaId = $this->pago_viejo->id_factura;


				           $this->pago_viejo->update([
				             'monto' => $cant
				             ]);

				             $this->emit('pago-actualizado', 'El pago fue actualizado.');

										 $this->ActualizarEstadoDeuda($ventaId);

				             $this->RenderFactura($ventaId);



				             $this->estado = "display: block;";

				   }


				   public function CreatePago($ventaId)
				   {


				     pagos_facturas::create([
				       'monto' => $this->monto,
				       'id_factura' => $ventaId,
				       'eliminado' => 0
				     ]);

				     $this->monto = '';

				      $this->emit('pago-creado', 'El pago fue guardado.');

							$this->ActualizarEstadoDeuda($ventaId);


				     $this->RenderFactura($ventaId);



				     $this->estado = "display: block;";

				   }

				   public function DeletePago($id)
				   {


				           $this->pago_viejo = pagos_facturas::find($id);

				          $ventaId = $this->pago_viejo->id_factura;


				           $this->pago_viejo->update([
				             'eliminado' => 1
				             ]);

				             $this->emit('pago-eliminado', 'El pago fue eliminado.');

										 $this->ActualizarEstadoDeuda($ventaId);

				             $this->RenderFactura($ventaId);



				             $this->estado = "display: block;";

				   }


				   public function AsignarHojaRuta($HojaRutaElegida, $ventaId)
				   {

				       $Hruta = Sale::find($ventaId);

				       $Hruta->update([
				         'hoja_ruta' => $HojaRutaElegida
				       ]);

				       $this->RenderFactura($ventaId);

				       $this->emit('hr-asignada', 'El pedido fue agregado a la Hoja de Ruta.');


				     }

				     public function SinAsignarHojaRuta($ventaId)
				     {

				         $Hruta = Sale::find($ventaId);

				         $Hruta->update([
				           'hoja_ruta' => null
				         ]);

				         $this->RenderFactura($ventaId);


				       }


				       public function GuardarHojaDeRuta($ventaId)
				       {

				         $rules  =[
				           'fecha' => 'required',
				           'tipo' => 'not_in:Elegir'

				         ];

				         $messages = [
				           'fecha.required' => 'La fecha es requerida',
				           'tipo.not_in' => 'Elija el tipo de transporte'

				         ];

				         $this->validate($rules, $messages);

				         if(Auth::user()->comercio_id != 1)
				         $comercio_id = Auth::user()->comercio_id;
				         else
				         $comercio_id = Auth::user()->id;

				         $ultimo = hoja_ruta::where('hoja_rutas.comercio_id', 'like', $comercio_id)->select('hoja_rutas.nro_hoja','hoja_rutas.id')->latest('nro_hoja')->first();

				         $hoja = $ultimo->nro_hoja + 1;
				         $hoja_ulti = $ultimo->id + 1;

				         $product = hoja_ruta::create([
				           'nro_hoja' => $hoja,
				           'fecha' => Carbon::parse($this->fecha_hr)->format('Y-m-d'),
				           'nombre' => $this->nombre,
				           'tipo' => $this->tipo,
				           'observaciones' => $this->observaciones_hr,
				           'turno' => $this->turno,
				           'comercio_id' => $comercio_id
				         ]);




				           if(Auth::user()->comercio_id != 1)
				           $comercio_id = Auth::user()->comercio_id;
				           else
				           $comercio_id = Auth::user()->id;



				         $Hruta = Sale::find($ventaId);

				         $Hruta->update([
				           'hoja_ruta' => $hoja_ulti
				         ]);

				         $this->turno = 'Elegir';
				          $this->selected_id = '';
				          $this->fecha = Carbon::now()->format('d-m-Y');

				          $this->RenderFactura($ventaId);


				          $this->emit('hr-added', 'Hoja de ruta registrada y agregado el pedido.');

				          $this->emit('modal-hr-hide', '');




				       }


				           public function getDetails3($saleId)
				           {
				             $this->id_pedido = $saleId;

				               $this->emit('show-modal3','details loaded');

				               $this->RenderFactura($saleId);
				           }

									 public function selectEstado()
									 {

											 $this->emit('modal-estado','details loaded');

									 }



				           public function Update2($estado_id)
				           {

				             $pedido = Sale::find($this->id_pedido);

				             $pedido->update([
				               'status' => $estado_id
				             ]);



				             if($estado_id == 4)
				             {
				               $items = SaleDetail::where('sale_details.sale_id',$this->id_pedido)->get();

				                 foreach ($items as  $item) {
				                   //update stock
				                   $product = Product::find($item->product_id);
				                   $product->stock = $product->stock + $item->quantity;
				                   $product->save();
				                 }


				             }
				              $this->RenderFactura($this->id_pedido);

				             $this->emit('hide-modal3','details loaded');


				           }


									 public function CerrarFactura() {
										 $this->emit('cerrar-facruta','details loaded');
									 }




}
