<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\gastos;
use App\Models\pagos_facturas;
use App\Models\beneficios;
use App\Models\compras_proveedores;
use App\Models\Sale;
use App\Models\sucursales;
use Illuminate\Support\Facades\Auth;
use App\Models\SaleDetail;
use Carbon\Carbon;

class Dashboard2 extends Component
{
  public $componentName, $data, $details, $sumDetails, $countDetails,
  $reportType, $userId, $dateFrom, $dateTo, $saleId, $comercio_id, $selected_id, $detalle_ingresos, $Id, $productos, $categorias, $metodos_pago, $ventas_total, $ventas_cliente, $distances, $seriesData, $mensualizado, $data_total, $data_deuda, $data_mes, $total_mes, $mes, $gastos_total_mes, $gastos_mes, $total_gastos, $categorias_total, $categorias_nombre, $total_canal, $canal, $sucursal_id;


  public $total_ingresos;
  public $mensual;


  public function mount()
  {

      $this->componentName ='Reportes de Ventas';
      $this->data =[];
      $this->details =[];
      $this->sumDetails =0;
      $this->countDetails =0;
      $this->reportType =0;
      $this->userId =0;
      $this->saleId =0;
      $this->usuarioSeleccionado = 0;
      $this->ClienteSeleccionado = 0;
      $this->clienteId =0;
      $this->clientesSelectedName = [];
      $this->dateFrom = '01-01-2000';
      $this->dateTo = Carbon::now()->format('d-m-Y');



  }

  public function render()
  {
    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $this->comercio_id = $comercio_id;

    if($this->sucursal_id != null) {
      $this->sucursal_id = $this->sucursal_id;
    } else {
      $this->sucursal_id = $comercio_id;
    }


    $this->SalesByDate();

    $usuario_id = Auth::user()->id;




    $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->select('users.name','sucursales.sucursal_id')->where('casa_central_id', $comercio_id)->get();


    return view('livewire.dashboard3.component', [
    'sucursales' => $this->sucursales,
     'users' => User::where('comercio_id', 'like', $this->sucursal_id)
     ->orWhere('id', 'like', $usuario_id)
     ->orderBy('name','asc')->get(),

 ])->extends('layouts.theme.app')
 ->section('content');
  }


  public function SalesByDate()
  {

  if($this->dateFrom !== '' || $this->dateTo !== '')
  {
    $from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
    $to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';

  }

  if($this->sucursal_id != null) {
    $this->sucursal_id = $this->sucursal_id;
  } else {
    $this->sucursal_id = $comercio_id;
  }

  $usuario_id = Auth::user()->id;

         if(Auth::user()->comercio_id != 1)
         $comercio_id = Auth::user()->comercio_id;
         else
         $comercio_id = Auth::user()->id;

         $this->metodos_pago = Sale::join('metodo_pagos as m','m.id','sales.metodo_pago')
         ->select('m.id as id_metodo_pago','m.nombre', Sale::raw('SUM(sales.total) as total'))
         ->where('sales.comercio_id', 'like', $this->sucursal_id)
         ->where('sales.status','<>', 'Cancelado')
         ->whereBetween('sales.created_at', [$from, $to])
         ->groupby('m.id','m.nombre')
         ->get();
         
         
         $this->ventas_total = Sale::select('sales.comercio_id', Sale::raw('SUM(IFNULL(sales.total,0)) as total'),Sale::raw('SUM(sales.deuda) as deuda'),Sale::raw('COUNT(sales.id) as cantidad'),Sale::raw('SUM(sales.items) as items'))
         ->where('sales.comercio_id', 'like', $this->sucursal_id)
         ->whereBetween('sales.created_at', [$from, $to])
         ->where('sales.status','<>', 'Cancelado')
         ->groupby('sales.comercio_id')
         ->first();
         
         // Ventas total 
         if($this->ventas_total != null) {
         $this->ventas_total_total =     $this->ventas_total->total;
         $this->ventas_total_cantidad =     $this->ventas_total->cantidad;
         $this->ventas_total_deuda =     $this->ventas_total->deuda;
         } else {
         $this->ventas_total_total  = null;  
         $this->ventas_total_cantidad  = null;  
         $this->ventas_total_deuda  = null;  
         }
         
         
         

       // dd($this->ventas_total);


         $this->ventas_cliente = Sale::join('clientes_mostradors as c','c.id','sales.cliente_id')
         ->select('c.id as id_cliente','c.nombre', Sale::raw('SUM(sales.subtotal - IFNULL(sales.descuento,0) + IFNULL(sales.recargo,0) + IFNULL(sales.iva,0) ) as total'),Sale::raw('SUM(sales.items) as quantity'))
         ->where('sales.comercio_id', 'like', $this->sucursal_id)
         ->where('sales.status','<>', 'Cancelado')
         ->whereBetween('sales.created_at', [$from, $to])
         ->groupby('c.id','c.nombre')
         ->orderby('total','desc')
         ->get();


         $this->productos = SaleDetail::join('products as p','p.id','sale_details.product_id')
         ->join('sales','sales.id','sale_details.sale_id')
         ->select('p.id as id_producto','p.barcode','p.name as product', SaleDetail::raw('SUM( (sale_details.price*sale_details.quantity) - IFNULL(sale_details.descuento,0) ) as total'),SaleDetail::raw('SUM(sale_details.quantity) as quantity'), SaleDetail::raw('SUM(sale_details.recargo) as recargo'), SaleDetail::raw('SUM(((sale_details.price*sale_details.quantity) - IFNULL(sale_details.descuento,0) + IFNULL(sale_details.recargo,0) )*sale_details.iva) as iva'))
         ->where('sale_details.comercio_id', 'like', $this->sucursal_id)
         ->where('sales.status','<>', 'Cancelado')
         ->whereBetween('sale_details.created_at', [$from, $to])
         ->where('sale_details.eliminado', 0)
         ->orderby('quantity','desc')
         ->groupby('p.id','p.barcode','p.name')
         ->get();
         
          $this->productos_mas_rentables = SaleDetail::join('products as p','p.id','sale_details.product_id')
         ->join('sales','sales.id','sale_details.sale_id')
         ->select('p.id as id_producto','p.barcode','p.name as product',SaleDetail::raw('SUM( (sale_details.price*sale_details.quantity) - (sale_details.cost*sale_details.quantity) ) as ganancia_total') , SaleDetail::raw('SUM( (sale_details.price*sale_details.quantity) - IFNULL(sale_details.descuento,0) ) as total'),SaleDetail::raw('SUM(sale_details.quantity) as quantity'), SaleDetail::raw('SUM(sale_details.recargo) as recargo'), SaleDetail::raw('SUM(((sale_details.price*sale_details.quantity) - IFNULL(sale_details.descuento,0) + IFNULL(sale_details.recargo,0) )*sale_details.iva) as iva'))
         ->where('sale_details.comercio_id', 'like', $this->sucursal_id)
         ->where('sales.status','<>', 'Cancelado')
         ->whereBetween('sale_details.created_at', [$from, $to])
         ->where('sale_details.eliminado', 0)
         ->orderByRaw('(SUM( (sale_details.price*sale_details.quantity) - (sale_details.cost*sale_details.quantity) )) desc')
         ->groupby('p.id','p.barcode','p.name')
         ->get();
         
         


         $this->total_ingresos = pagos_facturas::select(
             pagos_facturas::raw('(IFNULL(SUM(pagos_facturas.monto),0) + IFNULL(SUM(pagos_facturas.recargo) , 0) - IFNULL( SUM(pagos_facturas.monto_gasto) , 0) - IFNULL( SUM(pagos_facturas.monto_compra), 0) ) as total')

         )
             ->where('pagos_facturas.comercio_id','like',$this->sucursal_id)
             ->where('pagos_facturas.tipo_pago',1)
             ->whereBetween('pagos_facturas.created_at', [$from, $to])
             ->get();


             $this->detalle_ingresos = pagos_facturas::select(
             pagos_facturas::raw('SUM(pagos_facturas.monto) as total, SUM(pagos_facturas.recargo) as recargos,  SUM(pagos_facturas.monto_gasto) as gastos, SUM(pagos_facturas.monto_compra) as compras')

         )
             ->where('pagos_facturas.comercio_id','like',$this->sucursal_id)
             ->where('pagos_facturas.tipo_pago',1)
             ->whereBetween('pagos_facturas.created_at', [$from, $to])
             ->get();

            $this->total_cuentas_corrientes_venta = Sale::select(Sale::raw('SUM(sales.deuda) as deuda'))
            ->where('sales.comercio_id', 'like', $this->sucursal_id)
            ->where('sales.status','<>', 'Cancelado')
            ->first();
            
            $this->total_cuentas_corrientes_proveedores = compras_proveedores::select(compras_proveedores::raw('SUM(compras_proveedores.deuda) as deuda'))
            ->where('compras_proveedores.comercio_id', 'like', $this->sucursal_id)
            ->first();
            



           $this->total_gastos = pagos_facturas::select(
             pagos_facturas::raw('(SUM(pagos_facturas.monto_gasto) + SUM(pagos_facturas.monto_compra)) AS total')

         )
             ->where('pagos_facturas.comercio_id','like',$this->sucursal_id)
             ->whereBetween('pagos_facturas.created_at', [$from, $to])
             ->get();


             $this->total_beneficios_query = beneficios::select(beneficios::raw('(SUM(beneficios.ingresos) - SUM(beneficios.gastos) ) as total_beneficios'))
             ->where('beneficios.comercio_id','like',$this->sucursal_id)
             ->whereBetween('beneficios.created_at', [$from, $to])
             ->get();

         $this->mensual = pagos_facturas::select(
           pagos_facturas::raw('(SUM(pagos_facturas.monto) + SUM(pagos_facturas.recargo) ) as total_ventas'),
           pagos_facturas::raw('(SUM(pagos_facturas.monto_gasto) + SUM(pagos_facturas.monto_compra) ) as total_gastos'),
           pagos_facturas::raw('(SUM(pagos_facturas.monto) - SUM(pagos_facturas.monto_gasto) ) as total_pagos_facturas'),
           pagos_facturas::raw("DATE_FORMAT(created_at,'%d-%m-%Y') as months"),

       )
           ->where('pagos_facturas.comercio_id','like',$this->sucursal_id)
           ->whereBetween('pagos_facturas.created_at', [$from, $to])
           ->groupBy('months')
           ->orderBy('pagos_facturas.created_at','asc')
           ->get();



           $this->data = Sale::select(
             Sale::raw('(SUM(sales.subtotal) - SUM(sales.descuento) + SUM(sales.recargo) + SUM(sales.iva) - SUM(sales.deuda) ) as total_pago'),
             Sale::raw('SUM(sales.deuda) as total_deuda'),
             Sale::raw("DATE_FORMAT(sales.created_at,'%m-%Y') as months")
             )
           ->whereBetween('sales.created_at', [$from, $to])
           ->where('sales.comercio_id', $this->sucursal_id)
           ->where('sales.status', '<>' , 'Cancelado')
           ->groupBy('months')
           ->orderBy('sales.created_at','asc')
           ->get();




           $this->ventas_canal = Sale::select('sales.canal_venta',Sale::raw('SUM(sales.subtotal - IFNULL(sales.descuento,0) + IFNULL(sales.recargo,0) + IFNULL(sales.iva,0) ) as total_canal'))
           ->where('sales.comercio_id', 'like', $this->sucursal_id)
           ->whereBetween('sales.created_at', [$from, $to])
           ->groupby('sales.canal_venta')
           ->get();

           $this->categorias = SaleDetail::join('products as p','p.id','sale_details.product_id')
           ->join('categories as c','c.id','p.category_id')
           ->select('c.name as categoria', SaleDetail::raw('FORMAT(SUM(sale_details.price*sale_details.quantity),2) as total'),SaleDetail::raw('SUM(sale_details.quantity) as cantidad'))
           ->where('sale_details.comercio_id','like',$this->sucursal_id)
           ->where('sale_details.eliminado', 0)
           ->whereBetween('sale_details.created_at', [$from, $to])
           ->groupby('c.name')
           ->orderBy('cantidad','desc')
           ->limit(10)
           ->get();





        $mensualizado = $this->mensual;
        $this->total_mes = $mensualizado->pluck('total_ventas');
        $this->gastos_total_mes = $mensualizado->pluck('total_gastos');
        $this->total_beneficios = $this->total_beneficios_query->pluck('total_beneficios');
        $this->mes = $mensualizado->pluck('months');

        $this->total_canal = $this->ventas_canal->pluck('total_canal');
        $this->canal = $this->ventas_canal->pluck('canal_venta');


        $categorias = $this->categorias;
        $this->categorias_nombre = $categorias->pluck('categoria');
        $this->categorias_total = $this->categorias->pluck('cantidad');

        $data = $this->data;
        $this->data_total = $data->pluck('total_pago');
        $this->data_recargo = $data->pluck('recargo');
        $this->data_deuda = $data->pluck('total_deuda');
        $this->data_mes = $data->pluck('months');



        if($this->dateFrom !== '' || $this->dateTo !== '')
        {

            $this->emit('mes', [
           'seriesData' => $this->total_mes,
           'totalingresos' => $this->total_beneficios,
            'seriesData2' => $this->gastos_total_mes,
           'categories' => $this->mes,
           'data_total' => $this->data_total,
           'data_deuda' => $this->data_deuda,
           'data_mes' => $this->data_mes,
           'total_canal' => $this->total_canal,
           'canal' => $this->canal,
           'categorias_total' => $this->categorias_total,
           'categorias_nombre' => $this->categorias_nombre,
           'seriesName' => 'Ganancias por mes',
       ]);


        }





}


public function ElegirSucursal($sucursal_id) {

  $this->sucursal_id = $sucursal_id;

}

  }
