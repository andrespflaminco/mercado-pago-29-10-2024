<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;
use App\Models\gastos;
use App\Models\compras_proveedores;
use App\Models\compras_insumos;
use App\Models\Sale;
use App\Models\ingresos_retiros;
use App\Models\wocommerce;
use App\Models\cajas;
use App\Models\pagos_facturas;
use App\Models\beneficios;
use App\Models\UsersController;
use App\Models\EtiquetaGastos;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
//use Codexshaper\WooCommerce\Facades\Product AS ProductWC;
use Automattic\WooCommerce\Client;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class CajasDetalleController extends Component
{
      use WithPagination;
      use WithFileUploads;

      public $nombre,$recargo, $caja_elegida, $descripcion,$price,$stock,$alerts,$categoryid,$search,$image,$selected_id,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $metodos, $categoria, $monto, $dateFrom, $dateTo, $categoria_filtro, $suma_totales, $gastos_total, $etiquetas_gastos, $etiqueta, $nombre_etiqueta, $etiqueta_form, $etiquetas_filtro, $cajas, $cajas_total, $etiquetas, $cajas_cajas_total,$details, $caja_id, $details_efectivo, $details_total, $details_bancos, $details_plataformas, $details_a_cobrar, $count_bancos, $count_a_cobrar, $count_efectivo, $count_plataformas, $total_bancos, $total_a_cobrar, $total_efectivo, $total_plataformas, $caja_activa, $monto_final, $monto_inicial, $ultimo, $cajaId;
    	private $pagination = 25;


	public function paginationView()
	{
		return '../vendor.livewire.bootstrap';
	}
	
 
    	public function render($cajaId)
    	{

        $this->caja_elegida = $cajaId;

        if(Auth::user()->comercio_id != 1)
        $comercio_id = Auth::user()->comercio_id;
        else
        $comercio_id = Auth::user()->id;



        if($this->dateFrom !== '' || $this->dateTo !== '')
        {
          $from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
          $to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';

        }

    	$cajas = cajas::leftjoin('users','users.id','cajas.user_id')
        ->leftjoin('pagos_facturas','pagos_facturas.caja','cajas.id')
        ->leftjoin('bancos','bancos.id','pagos_facturas.banco_id')
        ->leftjoin('metodo_pagos','metodo_pagos.id','pagos_facturas.metodo_pago')
        ->select('pagos_facturas.id as id_id','pagos_facturas.id_ingresos_retiros' ,'pagos_facturas.id as id_pago','pagos_facturas.id_factura','pagos_facturas.id_compra','pagos_facturas.id_compra_insumos','bancos.nombre as nombre_banco','pagos_facturas.id_gasto','pagos_facturas.monto_gasto','pagos_facturas.monto_compra','cajas.nro_caja','cajas.fecha_inicio','cajas.fecha_cierre','cajas.created_at','cajas.estado','cajas.id','users.name','pagos_facturas.created_at','pagos_facturas.monto','pagos_facturas.recargo','pagos_facturas.iva_recargo','pagos_facturas.iva_pago','metodo_pagos.nombre as metodo_pago')
        ->where('cajas.id', 'like', $cajaId)
        ->where('pagos_facturas.eliminado',0)
        ->orderBy('pagos_facturas.id','desc')
    	->paginate(1000);


        // recopilar los id_factura, id_compra y id_gastos
        
		$item_ids = $cajas->items();

		$id_venta = [];
		$id_compra = [];
		$id_compra_insumos = [];
		$id_gasto = [];
		$id_ingresos_retiros = [];
			
	    foreach ($item_ids as $i_id) {
	        
	        // acumular los id de venta
		    
		    $id_v = $i_id->id_factura;
		    if($id_v != 0) {
		    array_push($id_venta, $id_v);
		    }
		    
		    // acumular los id de gastos
		    
		    $id_g = $i_id->id_gasto;
		    if($id_g != 0) {
		    array_push($id_gasto, $id_g);	
		    
		    }
		    
		    // acumular los id de compras
		    
		   
		    
		    $id_c = $i_id->id_compra;
		    
		    if($id_c != 0) {
		         
		    array_push($id_compra, $id_c);
		    
		        
		    }
		    		    
		    // acumular los id de compras de insumos
		    
		   
		    
		    $id_c_i = $i_id->id_compra_insumos;
		    
		    if($id_c_i != 0) {
		         
		    array_push($id_compra_insumos, $id_c_i);
		    
		        
		    }
		    
		    $id_i_r = $i_id->id_ingresos_retiros;
		    
		    if($id_i_r != null) {
		         
		    array_push($id_ingresos_retiros, $id_i_r);
		    
		        
		    }
		} 
		
		//dd($id_compra_insumos);
		
		$ventas = Sale::join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')->select('sales.id','sales.nro_venta','clientes_mostradors.nombre')->whereIn('sales.id', $id_venta)->get();
		$gastos = gastos::whereIn('id', $id_gasto)->get();
		$compras = compras_proveedores::join('proveedores','proveedores.id','compras_proveedores.proveedor_id')->select('compras_proveedores.id','compras_proveedores.nro_compra','proveedores.nombre')->whereIn('compras_proveedores.id', $id_compra)->get();
		$compras_insumos = compras_insumos::join('proveedores','proveedores.id','compras_insumos.proveedor_id')->select('compras_insumos.id','compras_insumos.nro_compra','proveedores.nombre')->whereIn('compras_insumos.id', $id_compra_insumos)->get();
		$ingresos_retiros = ingresos_retiros::whereIn('ingresos_retiros.id', $id_ingresos_retiros)->get();
		
		
		 //dd($compras_insumos);
		    
    		return view('livewire.cajas-detalle.component', [
    		'data' => $cajas,
    		'ventas' => $ventas,
    		'gastos' => $gastos,
    		'compras' => $compras,
    		'compras_insumos' => $compras_insumos,
    		'ingresos_retiros' => $ingresos_retiros,
            'caja_elegida' => $this->caja_elegida
    		])
    		->extends('layouts.theme-pos.app')
    		->section('content');

    	}
    	
    	public function filtrar(Request $request)
        {
        // Obtén los datos de los filtros del request
        $cajaId = $request->input('caja_id');
        $filtro_search = $request->input('filtro_busqueda');
        $filtro_metodo_pago = $request->input('filtro_metodo_pago');
        
        // Realiza acciones con los filtros, por ejemplo, aplicar el filtro a tus datos
        $this->caja_elegida = $cajaId;

        if(Auth::user()->comercio_id != 1)
        $comercio_id = Auth::user()->comercio_id;
        else
        $comercio_id = Auth::user()->id;



        if($this->dateFrom !== '' || $this->dateTo !== '')
        {
          $from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
          $to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';

        }

    	$cajas = cajas::leftjoin('users','users.id','cajas.user_id')
        ->leftjoin('pagos_facturas','pagos_facturas.caja','cajas.id')
        ->leftjoin('metodo_pagos','metodo_pagos.id','pagos_facturas.metodo_pago')
        ->leftjoin('bancos','bancos.id','metodo_pagos.cuenta')
        ->select('pagos_facturas.id as id_id','pagos_facturas.id as id_pago','pagos_facturas.id_factura','pagos_facturas.id_compra','bancos.nombre as nombre_banco','pagos_facturas.id_gasto','pagos_facturas.monto_gasto','pagos_facturas.monto_compra','cajas.nro_caja','cajas.fecha_inicio','cajas.fecha_cierre','cajas.created_at','cajas.estado','cajas.id','users.name','pagos_facturas.created_at','pagos_facturas.monto','pagos_facturas.recargo','metodo_pagos.nombre as metodo_pago')
        ->where('cajas.id', 'like', $cajaId)
    	->paginate(1000);

        // recopilar los id_factura, id_compra y id_gastos
        
		$item_ids = $cajas->items();

		$id_venta = [];
		$id_compra = [];
		$id_gasto = [];
			
	    foreach ($item_ids as $i_id) {
	        
	        // acumular los id de venta
		    
		    $id_v = $i_id->id_factura;
		    if($id_v != 0) {
		    array_push($id_venta, $id_v);
		    }
		    
		    // acumular los id de gastos
		    
		    $id_g = $i_id->id_gasto;
		    if($id_g != 0) {
		    array_push($id_gasto, $id_g);	
		    
		    }
		    
		    // acumular los id de compras
		    
		   
		    
		    $id_c = $i_id->id_compra;
		    
		    if($id_c != 0) {
		         
		    array_push($id_compra, $id_c);
		    
		        
		    }
		} 
		
		
		$ventas = Sale::join('clientes_mostradors','clientes_mostradors.id','sales.cliente_id')->select('sales.id','clientes_mostradors.nombre')->whereIn('sales.id', $id_venta)->get();
		$gastos = gastos::whereIn('id', $id_gasto)->get();
		$compras = compras_proveedores::join('proveedores','proveedores.id','compras_proveedores.proveedor_id')->select('compras_proveedores.id','proveedores.nombre')->whereIn('compras_proveedores.id', $id_compra)->get();
		
		// dd($compras);
		    
    		return view('livewire.cajas-detalle.component', [
    		'data' => $cajas,
    		'ventas' => $ventas,
    		'gastos' => $gastos,
    		'compras' => $compras,
            'caja_elegida' => $this->caja_elegida
    		])
    		->extends('layouts.theme-pos.app')
    		->section('content');
    }



}
