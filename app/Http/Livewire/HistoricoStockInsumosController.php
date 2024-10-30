<?php

namespace App\Http\Livewire;


use App\Http\Livewire\Scaner;
use App\Models\Category;
use App\Models\tipo_movimiento_stock;
use App\Models\proveedores;
use App\Models\pagos_facturas;
use App\Models\Product;
use App\Models\hoja_ruta;
use App\Models\User;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\historico_stock_insumo;
use Livewire\Component;
use App\Models\seccionalmacen;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Traits\CartTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HistoricoStockInsumosController extends Scaner //Component
{

	use WithPagination;
	use WithFileUploads;
	use CartTrait;


	public $name,$barcode,$countDetails, $sumDetails,$cost,$price,$stock,$details, $alerts,$NroVenta, $detalle_cliente, $categoryid,$search,$tipo_movimiento_id, $image,$selected_id,$pageTitle,$ventaId, $style, $estado2,$estado,$estado_estado, $saleId, $detalle_facturacion, $suma_monto, $rec, $tot, $componentName,$comercio_id, $almacen, $stock_descubierto, $inv_ideal, $proveedor, $cod_proveedor, $proveedor_elegido;
	public $id_almacen;
	public $id_categoria;
	public $pagos2 = [];
	public $id_proveedor;
	private $pagination = 25;

	public $SelectedProducts = [];
	public $selectedAll = FALSE;



	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function mount()
	{
		$this->details = [];
		$this->sumDetails =0;
		$this->countDetails =0;
		$this->reportType = 0;
		$this->userId = 0;
		$this->saleId = 0;
		$this->pageTitle = 'Listado';
		$this->componentName = 'Movimientos de Stock de Insumos';
		$this->usuario_id = 'Elegir';



	}





	public function render()
	{



				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;

				$this->tipo_usuario = User::find(Auth::user()->id);


				if($this->tipo_usuario->sucursal != 1) {

			$historico = historico_stock_insumo::join('tipo_movimiento_stocks','tipo_movimiento_stocks.id','historico_stock_insumos.tipo_movimiento')
			->join('insumos','insumos.id','historico_stock_insumos.insumo_id')
			->join('tipo_unidad_medidas','tipo_unidad_medidas.id','insumos.tipo_unidad_medida')
			->join('unidad_medidas','unidad_medidas.id','insumos.unidad_medida')
			->select('historico_stock_insumos.*','tipo_movimiento_stocks.nombre as tipo_movimiento','insumos.*','unidad_medidas.nombre as unidad_medida')
			->where('historico_stock_insumos.comercio_id', $comercio_id);

			}

			if($this->tipo_movimiento_id) {
				$historico = $historico->where('historico_stock_insumos.tipo_movimiento',$this->tipo_movimiento_id);
			}

			if(strlen($this->search) > 0) {

			$historico = $historico->where( function($query) {
					 $query->where('insumos.name', 'like', '%' . $this->search . '%')
						->orWhere('insumos.barcode', 'like',$this->search . '%');
					});

				}


			$historico = $historico->orderBy('historico_stock_insumos.id','desc')
			->paginate($this->pagination);

			return view('livewire.historico-stock-insumos.component', [
				'data' => $historico,
				'usuario' => User::where('comercio_id', $comercio_id)->select('*')->get(),
				'tipo_movimiento' => tipo_movimiento_stock::select('*')->get()
			])
			->extends('layouts.theme.app')
			->section('content');



}

	public function Store()
	{
		$rules  =[
			'name' => 'required|min:3',
			'barcode' => 'required|min:3',
			'cost' => 'required',
			'price' => 'required',
			'stock' => 'required',
			'almacen' => 'not_in:Elegir',
			'proveedor' => 'required',
			'alerts' => 'required',
			'categoryid' => 'required|not_in:Elegir',
			'stock_descubierto' => 'required|not_in:Elegir'
		];

		$messages = [
			'name.required' => 'Nombre del producto requerido',
			'name.min' => 'El nombre debe tener al menos 3 caracteres',
			'name.required' => 'El codigo del producto requerido',
			'barcode.required' => 'El codigo del producto requerido',
			'barcode.min' => 'El codigo debe tener al menos 3 caracteres',
			'cost.required' => 'El costo es requerido',
			'price.required' => 'Precio de venta requerido',
			'proveedor.required' => 'El proveedor es un campo requerido',
			'stock.required' => 'Ingresa las existencias',
			'almacen.not_in' => 'Ingresa el almacen',
			'alerts.required' => 'Falta el valor para las alertas',
			'categoryid.not_in' => 'Elige una categoría válida',
			'stock_descubierto.not_in' => 'Elige si maneja stock o no.'
		];

		$this->validate($rules, $messages);

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$product = Product::create([
			'name' => $this->name,
			'cost' => $this->cost,
			'price' => $this->price,
			'barcode' => $this->barcode,
			'stock' => $this->stock,
			'alerts' => $this->alerts,
			'inv_ideal' => $this->inv_ideal,
			'stock_descubierto' => $this->stock_descubierto,
			'seccionalmacen_id' => $this->almacen,
			'category_id' => $this->categoryid,
			'proveedor_id' => $this->proveedor,
			'comercio_id' => $comercio_id,
			'cod_proveedor' => $this->cod_proveedor
		]);

		if($product) {

			$usuario_id = Auth::user()->id;

			$historico = historico_stock::create([

				'tipo_movimiento' => 6,
				'producto_id' => $product->id,
				'cantidad_movimiento' => $this->stock,
				'stock' => $this->stock,
				'usuario_id' => $usuario_id,
				'comercio_id'  => $comercio_id
			]);
		}

		if($this->image)
		{
			$customFileName = uniqid() . '_.' . $this->image->extension();
			$this->image->storeAs('public/products', $customFileName);
			$product->image = $customFileName;
			$product->save();
		}

		$this->resetUI();
		$this->emit('product-added', 'Producto Registrado');


	}


	////////////////////// DETALLE DEL PEDIDO /////////////////////////



	public function getDetails($saleId)
	{



		$this->details = SaleDetail::join('products as p','p.id','sale_details.product_id')
		->select('sale_details.id','sale_details.price','sale_details.quantity','p.name as product')
		->where('sale_details.sale_id',$saleId)
		->get();

	// funcion anonima mejor conocida en laravel como Closure  (en javascript callback)
	$suma = $this->details->sum(function($item) {
		return $item->price * $item->quantity;
	});

	$this->sumDetails = $suma;
	$this->countDetails = $this->details->sum('quantity');
	$this->saleId = $saleId;

		$this->emit('show-modal','details loaded');
	}



}
