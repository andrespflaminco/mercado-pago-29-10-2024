<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas

//use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
//use PhpOffice\PhpSpreadsheet\Shared\Date;
//use Maatwebsite\Excel\Concerns\WithColumnFormatting;
//use Maatwebsite\Excel\Concerns\WithEvents;
//use Maatwebsite\Excel\Events\BeforeExport;
//use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use App\Models\SaleDetail;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProduccionExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle,WithStyles
							//ShouldAutoSize, WithEvents //WithColumnFormatting
{
	// propiedades
	protected $ClienteSeleccionado, $usuarioSeleccionado,$metodopagoSeleccionado, $productoSeleccionado, $categoriaSeleccionado, $almacenSeleccionado, $f1, $f2;


	//constructor
	function __construct($estadoSeleccionado, $ClienteSeleccionado,$metodopagoSeleccionado, $productoSeleccionado, $categoriaSeleccionado, $almacenSeleccionado, $f1, $f2) {

		$this->cliente_seleccionado = $ClienteSeleccionado;
		$this->mpago_seleccionado = $metodopagoSeleccionado;
		$this->prod_seleccionado = $productoSeleccionado;
		$this->cat_seleccionado = $categoriaSeleccionado;
		$this->alm_seleccionado = $almacenSeleccionado;
		$this->us_seleccionado = $estadoSeleccionado;


		$this->locationUsers = explode(",", $ClienteSeleccionado);
		$this->metodopago_seleccionado = explode(",", $metodopagoSeleccionado);
		$this->producto_seleccionado = explode(",", $productoSeleccionado);
		$this->categoria_seleccionado = explode(",", $categoriaSeleccionado);
		$this->almacen_seleccionado = explode(",", $almacenSeleccionado);
		$this->estado_seleccionado = explode(",", $estadoSeleccionado);
		$this->dateFrom   = $f1;
		$this->dateTo     = $f2;


	}


   // método para obtener de base de datos la info a exportar
    public function collection()
    {
    	$data = [];

    		$from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
    		$to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';


				if(($this->prod_seleccionado == 0) && ($this->us_seleccionado == 0) && ($this->cliente_seleccionado == 0) && ($this->mpago_seleccionado == 0) && ($this->cat_seleccionado == 0))
				{



				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->where('sale_details.comercio_id',$comercio_id)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

				if(($this->prod_seleccionado > 0) && ($this->us_seleccionado > 0) && ($this->cliente_seleccionado > 0) && ($this->mpago_seleccionado > 0)  && ($this->alm_seleccionado > 0))
				{



				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->where('sale_details.comercio_id',$comercio_id)
				->whereIn('sale_details.product_id',$this->producto_seleccionado)
				->whereIn('sale_details.metodo_pago',$this->metodopago_seleccionado)
				->whereIn('sale_details.seccionalmacen_id',$this->almacen_seleccionado)
				->whereIn('estados.id',$this->estado_seleccionado)
				->whereIn('sale_details.cliente_id',$this->locationUsers)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}


				if(($this->alm_seleccionado > 0) && ($this->us_seleccionado > 0) && ($this->cliente_seleccionado > 0) && ($this->mpago_seleccionado > 0))
				{



				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->where('sale_details.comercio_id',$comercio_id)
				->whereIn('sale_details.seccionalmacen_id',$this->almacen_seleccionado)
				->whereIn('sale_details.metodo_pago',$this->metodopago_seleccionado)
				->whereIn('estados.id',$this->estado_seleccionado)
				->whereIn('sale_details.cliente_id',$this->locationUsers)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;


			}



				if(($this->prod_seleccionado > 0) && ($this->us_seleccionado > 0) && ($this->cliente_seleccionado > 0) && ($this->mpago_seleccionado > 0))
				{



				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->where('sale_details.comercio_id',$comercio_id)
				->whereIn('sale_details.product_id',$this->producto_seleccionado)
				->whereIn('sale_details.metodo_pago',$this->metodopago_seleccionado)
				->whereIn('estados.id',$this->estado_seleccionado)
				->whereIn('sale_details.cliente_id',$this->locationUsers)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}



				///////////////// VARIOS (3 JUNTOS) ///////////////////////////

				if(($this->cliente_seleccionado > 0) && ($this->mpago_seleccionado > 0) && ($this->us_seleccionado > 0))
				{

				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->whereIn('sale_details.metodo_pago',$this->metodopago_seleccionado)
				->whereIn('sale_details.cliente_id',$this->locationUsers)
				->whereIn('estados.id',$this->estado_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

				if(($this->prod_seleccionado > 0) && ($this->us_seleccionado > 0) && ($this->cliente_seleccionado > 0))
				{

				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->where('sale_details.comercio_id',$comercio_id)
				->whereIn('sale_details.product_id',$this->producto_seleccionado)
				->whereIn('sale_details.cliente_id',$this->locationUsers)
				->whereIn('estados.id',$this->estado_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

				if(($this->cat_seleccionado > 0) && ($this->us_seleccionado > 0) && ($this->cliente_seleccionado > 0))
				{

				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->where('sale_details.comercio_id',$comercio_id)
				->whereIn('p.category_id',$this->categoria_seleccionado)
				->whereIn('sale_details.cliente_id',$this->locationUsers)
				->whereIn('estados.id',$this->estado_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

				if(($this->alm_seleccionado > 0) && ($this->us_seleccionado > 0) && ($this->cliente_seleccionado > 0))
				{

				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->where('sale_details.comercio_id',$comercio_id)
				->whereIn('sale_details.seccionalmacen_id',$this->almacen_seleccionado)
				->whereIn('sale_details.cliente_id',$this->locationUsers)
				->whereIn('estados.id',$this->estado_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

				if(($this->prod_seleccionado > 0) && ($this->mpago_seleccionado > 0) && ($this->us_seleccionado > 0))
				{

				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->where('sale_details.comercio_id',$comercio_id)
				->whereIn('sale_details.product_id',$this->producto_seleccionado)
				->whereIn('sale_details.metodo_pago',$this->metodopago_seleccionado)
				->whereIn('estados.id',$this->estado_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

				if(($this->us_seleccionado > 0) && ($this->mpago_seleccionado > 0) && ($this->alm_seleccionado > 0 ))
				{

				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->where('sale_details.comercio_id',$comercio_id)
				->whereIn('estados.id',$this->estado_seleccionado)
				->whereIn('sale_details.metodo_pago',$this->metodopago_seleccionado)
				->whereIn('sale_details.seccionalmacen_id',$this->almacen_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

				if(($this->prod_seleccionado > 0) && ($this->mpago_seleccionado > 0) && ($this->cliente_seleccionado > 0 ))
				{

				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->where('sale_details.comercio_id',$comercio_id)
				->whereIn('sale_details.product_id',$this->producto_seleccionado)
				->whereIn('sale_details.metodo_pago',$this->metodopago_seleccionado)
				->whereIn('sale_details.cliente_id',$this->locationUsers)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

				if(($this->prod_seleccionado > 0) && ($this->mpago_seleccionado > 0) && ($this->alm_seleccionado > 0))
				{

				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->where('sale_details.comercio_id',$comercio_id)
				->whereIn('sale_details.product_id',$this->producto_seleccionado)
				->whereIn('sale_details.metodo_pago',$this->metodopago_seleccionado)
				->whereIn('sale_details.seccionalmacen_id',$this->almacen_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

				if(($this->prod_seleccionado > 0) && ($this->mpago_seleccionado > 0) && ($this->cat_seleccionado > 0 ))
				{

				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->where('sale_details.comercio_id',$comercio_id)
				->whereIn('sale_details.product_id',$this->producto_seleccionado)
				->whereIn('sale_details.metodo_pago',$this->metodopago_seleccionado)
				->whereIn('p.category_id',$this->categoria_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

				///////////////////// PRODUCTO CON TODOS /////////////////

				if(($this->alm_seleccionado > 0) && ($this->prod_seleccionado > 0))
				{


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->whereIn('sale_details.seccionalmacen_id',$this->almacen_seleccionado)
				->whereIn('sale_details.product_id',$this->producto_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

				if(($this->prod_seleccionado > 0) && ($this->us_seleccionado > 0))
				{

				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->where('sale_details.comercio_id',$comercio_id)
				->whereIn('sale_details.product_id',$this->producto_seleccionado)
				->whereIn('estados.id',$this->estado_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

				if(($this->prod_seleccionado > 0) && ($this->cat_seleccionado > 0))
				{

				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->where('sale_details.comercio_id',$comercio_id)
				->whereIn('sale_details.product_id',$this->producto_seleccionado)
				->whereIn('p.category_id',$this->categoria_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

				if(($this->prod_seleccionado > 0) && ($this->cliente_seleccionado > 0))
				{

				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->where('sale_details.comercio_id',$comercio_id)
				->whereIn('sale_details.product_id',$this->producto_seleccionado)
				->whereIn('sale_details.cliente_id',$this->locationUsers)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

				if(($this->prod_seleccionado > 0) && ($this->mpago_seleccionado > 0))
				{

				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->where('sale_details.comercio_id',$comercio_id)
				->whereIn('sale_details.product_id',$this->producto_seleccionado)
				->whereIn('sale_details.metodo_pago',$this->metodopago_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}


				////////////////////////////// USUARIO CON TODOS ///////////////////77
				if(($this->alm_seleccionado > 0) && ($this->us_seleccionado > 0))
				{


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->whereIn('sale_details.seccionalmacen_id',$this->almacen_seleccionado)
				->whereIn('estados.id',$this->estado_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

				if(($this->us_seleccionado > 0 && $this->cat_seleccionado > 0))
				{

				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->where('sale_details.comercio_id',$comercio_id)
				->whereIn('estados.id',$this->estado_seleccionado)
				->whereIn('p.category_id',$this->categoria_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

				if(($this->us_seleccionado > 0) && ($this->cliente_seleccionado > 0))
				{

				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->whereIn('sale_details.cliente_id',$this->locationUsers)
				->whereIn('estados.id',$this->estado_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();


				return $data;

				}

				if(($this->us_seleccionado > 0) && ($this->mpago_seleccionado > 0))
				{

				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->whereIn('sale_details.metodo_pago',$this->metodopago_seleccionado)
				->whereIn('estados.id',$this->estado_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}
				//////////////////  CLIENTES CON TODOS///////////////////
				if(($this->cliente_seleccionado > 0) && ($this->alm_seleccionado > 0))
				{


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->whereIn('sale_details.seccionalmacen_id',$this->almacen_seleccionado)
				->whereIn('sale_details.cliente_id',$this->locationUsers)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}
				if(($this->cliente_seleccionado > 0) && ($this->cat_seleccionado > 0))
				{

				if(Auth::user()->comercio_id != 1)
				$comercio_id = Auth::user()->comercio_id;
				else
				$comercio_id = Auth::user()->id;


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->where('sale_details.comercio_id',$comercio_id)
				->whereIn('sale_details.cliente_id',$this->locationUsers)
				->whereIn('p.category_id',$this->categoria_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

				if(($this->cliente_seleccionado > 0) && ($this->mpago_seleccionado > 0))
				{



				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->whereIn('sale_details.metodo_pago',$this->metodopago_seleccionado)
				->whereIn('sale_details.cliente_id',$this->locationUsers)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

				////////////// METODOS DE PAGO ///////////
				if(($this->mpago_seleccionado > 0) && ($this->alm_seleccionado > 0))
				{


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->whereIn('sale_details.seccionalmacen_id',$this->almacen_seleccionado)
				->whereIn('sale_details.metodo_pago',$this->metodopago_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}


				if(($this->mpago_seleccionado > 0) && ($this->cat_seleccionado > 0))
				{

				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->whereIn('sale_details.metodo_pago',$this->metodopago_seleccionado)
				->whereIn('p.category_id',$this->categoria_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

				if(($this->cat_seleccionado > 0) && ($this->alm_seleccionado > 0))
				{


				$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
				->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
				->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
				->join('sales as sa','sa.id','sale_details.sale_id')
				->join('users','users.id','sa.user_id')
				->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
				->join('categories','categories.id','p.category_id')
				->join('estados','estados.id','sale_details.estado')
				->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
				->whereIn('sale_details.seccionalmacen_id',$this->almacen_seleccionado)
				->whereIn('p.category_id',$this->categoria_seleccionado)
				->whereBetween('sale_details.created_at', [$from, $to])
				->orderBy('sale_details.sale_id','desc')
				->get();

				return $data;

				}

			if($this->cliente_seleccionado > 0)
			{


							if(Auth::user()->comercio_id != 1)
							$comercio_id = Auth::user()->comercio_id;
							else
							$comercio_id = Auth::user()->id;


							$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
							->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
							->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
							->join('sales as sa','sa.id','sale_details.sale_id')
							->join('users','users.id','sa.user_id')
							->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
							->join('categories','categories.id','p.category_id')
							->join('estados','estados.id','sale_details.estado')
							->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
							->orderBy('sale_details.sale_id','desc')
							->whereIn('sale_details.cliente_id',$this->locationUsers)
							->whereBetween('sale_details.created_at', [$from, $to])
							->get();


								return $data;


			}




			if($this->us_seleccionado > 0)
			{

			if(Auth::user()->comercio_id != 1)
			$comercio_id = Auth::user()->comercio_id;
			else
			$comercio_id = Auth::user()->id;


			$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
			->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
			->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
			->join('sales as sa','sa.id','sale_details.sale_id')
			->join('users','users.id','sa.user_id')
			->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
			->join('categories','categories.id','p.category_id')
			->join('estados','estados.id','sale_details.estado')
			->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
			->where('sale_details.comercio_id',$comercio_id)
			->whereIn('estados.id',$this->estado_seleccionado)
			->whereBetween('sale_details.created_at', [$from, $to])
			->orderBy('sale_details.sale_id','desc')
			->get();

			return $data;

			}

			if($this->mpago_seleccionado > 0)
			{

			if(Auth::user()->comercio_id != 1)
			$comercio_id = Auth::user()->comercio_id;
			else
			$comercio_id = Auth::user()->id;


			$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
			->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
			->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
			->join('sales as sa','sa.id','sale_details.sale_id')
			->join('users','users.id','sa.user_id')
			->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
			->join('categories','categories.id','p.category_id')
			->join('estados','estados.id','sale_details.estado')
			->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
			->where('sale_details.comercio_id',$comercio_id)
			->whereIn('sale_details.metodo_pago',$this->metodopago_seleccionado)
			->whereBetween('sale_details.created_at', [$from, $to])
			->orderBy('sale_details.sale_id','desc')
			->get();

			return $data;

			}




			if($this->prod_seleccionado > 0)
			{

			if(Auth::user()->comercio_id != 1)
			$comercio_id = Auth::user()->comercio_id;
			else
			$comercio_id = Auth::user()->id;


			$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
			->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
			->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
			->join('sales as sa','sa.id','sale_details.sale_id')
			->join('users','users.id','sa.user_id')
			->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
			->join('categories','categories.id','p.category_id')
			->join('estados','estados.id','sale_details.estado')
			->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
			->where('sale_details.comercio_id',$comercio_id)
			->whereIn('sale_details.product_id',$this->producto_seleccionado)
			->whereBetween('sale_details.created_at', [$from, $to])
			->orderBy('sale_details.sale_id','desc')
			->get();

			return $data;

			}



			if($this->cat_seleccionado > 0)
			{


			$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
			->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
			->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
			->join('sales as sa','sa.id','sale_details.sale_id')
			->join('users','users.id','sa.user_id')
			->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
			->join('categories','categories.id','p.category_id')
			->join('estados','estados.id','sale_details.estado')
			->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
			->whereIn('p.category_id',$this->categoria_seleccionado)
			->whereBetween('sale_details.created_at', [$from, $to])
			->orderBy('sale_details.sale_id','desc')
			->get();

			return $data;


			}

			if($this->alm_seleccionado > 0)
			{


			$data = SaleDetail::join('products as p','p.id','sale_details.product_id')
			->join('metodo_pagos as m','m.id','sale_details.metodo_pago')
			->join('seccionalmacens as s','s.id','sale_details.seccionalmacen_id')
			->join('sales as sa','sa.id','sale_details.sale_id')
			->join('users','users.id','sa.user_id')
			->join('clientes_mostradors as cl','cl.id','sale_details.cliente_id')
			->join('categories','categories.id','p.category_id')
			->join('estados','estados.id','sale_details.estado')
			->select('sale_details.sale_id','p.name as product','categories.name as nombre_categoria','sale_details.price','sale_details.quantity','cl.nombre as nombre_cliente','users.name as nombre_usuario','m.nombre as nombre_metodo_pago','s.nombre as almacen','estados.nombre as nombre_estado')
			->whereIn('sale_details.seccionalmacen_id',$this->almacen_seleccionado)
			->whereBetween('sale_details.created_at', [$from, $to])
			->orderBy('sale_details.sale_id','desc')
			->get();

			return $data;

			}


    }

    //personalizar el nombre de las cabeceras
    public function headings() :array
    {
    	return ["ID PEDIDO","PRODUCTO","CATEGORIA","PRECIO", "CANTIDAD","CLIENTE" ,"VENDEDOR","METODO DE PAGO","ALMACEN","ESTADO DE FABRICACIÓN"];
    }

    //especificar celda a partir de la cual se va llenar el excel con la información del reporte
    public function startCell(): string
    {
    	return 'A2';
    }

	// establecemos los encabezados con texto bold
    public function styles(Worksheet $sheet)
    {
    	return [
    		2    => ['font' => ['bold' => true]],
    	];
    }

    //nombre de la hoja de excel
    public function title(): string
    {
    	return 'Reporte de Ventas por Producto';
    }


	/*
    public function map($data): array
    {
    	return [
    		Date::dateTimeToExcel($data->created_at)
    	];
    }
	*/
	 //formato columnas moneda
    /*
	public function columnFormats(): array
    {
    	return [
    		'B' => '"$ "#,##0.00_-',
    		'F' => 'm/d/Y h:i:s'
    	];
    }
	*/


/*
 public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            BeforeExport::class => function(BeforeExport $event) {
                $event->writer->getProperties()->setTitle('LFax');
            },
        ];
    }
*/


}
