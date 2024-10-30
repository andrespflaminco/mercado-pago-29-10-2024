<?php

namespace App\Http\Livewire;


// Trait
use App\Traits\VentasTrait;
use App\Traits\BancosTrait;
use App\Traits\PagosTrait;


use App\Traits\DeduccionesTrait; // 30-6-2024
//

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use App\Models\pagos_facturas;
use App\Models\sucursales;

use App\Models\SalesInsumos;
use App\Models\Sale;

use App\Models\saldos_iniciales;
use App\Models\bancos;
use App\Models\movimiento_dinero_cuentas_detalle;

use App\Models\cajas;
use App\Models\metodo_pago;
use Carbon\Carbon;
use Illuminate\Http\Request;



use App\Models\Product;
use App\Models\datos_facturacion;
use App\Models\User;
use App\Models\productos_ivas;

use Illuminate\Support\Facades\DB;


class PagosController extends Component
{

	use WithFileUploads;
    use BancosTrait;
    use PagosTrait;
	use WithPagination;
    use DeduccionesTrait; // 30-6-2024
    use VentasTrait;

	public $name, $search, $image,$forma_pago, $agregar,$operacion_filtro,$id_check,$selected_id, $pageTitle, $componentName, $wc_category_id;
	private $pagination = 25;
	private $wc_category;
	public $banco_filtro,$metodo_pago_filtro;
	public $comercio_id,$sucursales_lista,$sucursal_id;
	public $dateFrom, $dateTo,$tipo_movimiento_filtro,$estado_pago,$tipos_pago,$tipo_pago,$metodo_pago_agregar,$formato_ver;
	
	public $nro_comprobante,$comprobante,$pago_selected,$nuevo_estado,$mostrarInputFile;
	
	public $metodo_pago, $bancos, $id_pago, $caja, $tipo_pago_seleccionado,$tipo;
	
	public $tipos_pago_sucursal,$tipo_pago_sucursal, $datos_cliente,$sucursales_todas;

	public function mount(Request $request)
	{
	    
	    
        $this->tipos_pago = [];
        $this->metodo_pago_agregar = [];
        $this->fecha_ap = Carbon::now();
        
        
	    $this->estado_filtro = 0;
	    $this->accion_lote = 'Elegir';
		$this->pageTitle = 'Listado';
		$this->componentName = 'Categorías';

        //https://app.flamincoapp.com.ar/pagos?operacion=Venta

        $estado_pago = $request->input('estado_pago');
        $operacion_filtro = $request->input('operacion');
        $banco_filtro = $request->input('banco');
        $metodo_pago_filtro = $request->input('metodo_pago');
        $dateFrom = $request->input('dateFrom');
        $dateTo = $request->input('dateTo');
        $tipo_movimiento = $request->input('tipo_movimiento');
        $sucursal_id = $request->input('sucursal_id');

        $this->estado_pago = $estado_pago;
        $this->tipo_movimiento_filtro = $tipo_movimiento;
        $this->operacion_filtro = $operacion_filtro;
        $this->banco_filtro = $banco_filtro;
        $this->metodo_pago_filtro = $metodo_pago_filtro;
        // Obtener el primer día de este año
        $this->dateFrom  = $dateFrom ?? Carbon::now()->firstOfYear();
        // hasta
        $this->dateTo = $dateTo ?? Carbon::now()->format('d-m-Y');    
        
        $this->emit("set-fecha",$this->dateFrom,$this->dateTo);
        
        $casa_central_id = Auth::user()->casa_central_user_id;

        
      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

      
      $this->sucursal_id = $sucursal_id ?? $comercio_id;

       // dd($this->sucursales);
       
       $this->caja = cajas::where('estado',0)->where('eliminado',0)->where('user_id',Auth::user()->id)->max('id');
       $this->caja_seleccionada = cajas::find($this->caja);
        
	}

	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}

    protected function redirectLogin()
    {
        return \Redirect::to("login");
    }

    public $mostrarFiltros = false;

    public function MostrarFiltro()
    {
        $this->mostrarFiltros = !$this->mostrarFiltros;
    }


	protected $listeners =[
    'FechaElegida' => 'FechaElegida',
    'deletePago' => 'SwitchDeletePago',
	];

    public function FechaElegida($startDate, $endDate)
    {
      // Manejar las fechas seleccionadas aquí
      $this->dateFrom  = $startDate;
      $this->dateTo = $endDate;
      
      //dd($startDate,$endDate);
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
      
      $this->sucursales_lista = User::where('casa_central_user_id',$casa_central_id)
      ->get();
      
        $this->sucursales_todas = User::where('casa_central_user_id', $casa_central_id)
        ->pluck('id') // Extrae solo los IDs de los usuarios
        ->toArray(); // Convierte la colección en un array si lo prefieres
    
        // Agrega $casa_central_id al array de IDs
        array_push($this->sucursales_todas, $casa_central_id);

      $this->ultimas_cajas = cajas::where('comercio_id', $comercio_id)->where('eliminado',0)->orderby('created_at','desc')->limit(5)->get();
        
      $this->comercio_id = $this->sucursal_id;
      $sucursal_id = $this->sucursal_id;

     
      $this->tipos_pago = bancos::join('bancos_muestra_sucursales','bancos_muestra_sucursales.banco_id','bancos.id')
      ->select('bancos.*')
      ->where('bancos_muestra_sucursales.sucursal_id', $this->sucursal_id)
       ->where('bancos_muestra_sucursales.muestra', 1)
      ->orderBy('bancos.nombre','asc')->get();


      $this->metodo_pago_agregar =  metodo_pago::join('bancos','bancos.id','metodo_pagos.cuenta')
       ->join('metodo_pagos_muestra_sucursales','metodo_pagos_muestra_sucursales.metodo_id','metodo_pagos.id')
      ->select('metodo_pagos.*','bancos.nombre as nombre_banco')
      ->where('metodo_pagos_muestra_sucursales.sucursal_id', 'like', $this->sucursal_id)
      ->where('metodo_pagos_muestra_sucursales.muestra', 1)
      ->where('metodo_pagos.cuenta', $this->tipo_pago)
      ->get();
      
        if($this->dateFrom !== '' || $this->dateTo !== '')
        {
          $from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
          $to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';
    
        }

        // Realiza la primera consulta 
        $datos_pagos_query = $this->GetPagos($from,$to,$sucursal_id);
        
        // Realiza la segunda consulta
        
        $saldos_iniciales_query = $this->GetSaldosIniciales($sucursal_id);

        // Realizamos la tercer consulta
        $saldos_proveedores_clientes_query = $this->GetPagosSaldosClientesProveedores($from, $to,$sucursal_id);

        $movimiento_cuentas = $this->GetMovimientosEntreCuentas($from, $to,$sucursal_id);
         
        // Une las dos consultas
        $combined_query = $datos_pagos_query->union($saldos_iniciales_query)->union($saldos_proveedores_clientes_query)->union($movimiento_cuentas);
        
        // Ordena y pagina los resultados combinados
        $combined_results = DB::table(DB::raw("({$combined_query->toSql()}) as combined"))
            ->mergeBindings($combined_query->getQuery())
            ->orderBy('created_at', 'desc')
            ->paginate($this->pagination);
        
        // Devuelve los resultados paginados
        $datos_pagos = $combined_results;
        
      // Obtenemos los bancos
      
      $this->bancos = $this->GetBancosTrait($sucursal_id);
      
      // Obtenemos los metodos de pagos
        $this->metodo_pago =  metodo_pago::join('bancos','bancos.id','metodo_pagos.cuenta')
        ->join('metodo_pagos_muestra_sucursales','metodo_pagos_muestra_sucursales.metodo_id','metodo_pagos.id')
        ->where('metodo_pagos_muestra_sucursales.muestra', 1)
        ->where('metodo_pagos_muestra_sucursales.sucursal_id', $sucursal_id)
        ->where('metodo_pagos.eliminado',0)
        ->select('metodo_pagos.*','bancos.nombre as nombre_banco',metodo_pago::raw('CONCAT(bancos.nombre," - ",metodo_pagos.nombre) as nombre_metodo'))
        ->orderBy('bancos.nombre','asc')
        ->orderBy('metodo_pagos.nombre','asc')
        ->get();
       
		return view('livewire.pagos.component', [
		    'sucursales_lista' => $this->sucursales_lista,
		    'datos_pagos' => $datos_pagos,
            'bancos' =>   $this->bancos,
            'tipos_pago' => $this->tipo_pago,
            'metodo_pago_agregar' => $this->metodo_pago_agregar,
            'metodo_pago' => $this->metodo_pago
		    ])
		->extends('layouts.theme-pos.app')
		->section('content');
	}


public function GetPagos($from,$to,$sucursal_id){
    
            // Realiza la primera consulta
        $datos_pagos_query = pagos_facturas::select(
            'users.name as sucursal',
            'pagos_facturas.id',
            DB::raw('CASE
                        WHEN pagos_facturas.id_venta_insumos IS NOT NULL AND pagos_facturas.id_venta_insumos <> 0 THEN "ingreso"
                        WHEN pagos_facturas.id_factura IS NOT NULL AND pagos_facturas.id_factura <> 0 THEN "ingreso"
                        WHEN pagos_facturas.id_compra IS NOT NULL AND pagos_facturas.id_compra <> 0 THEN "egreso"
                        WHEN pagos_facturas.id_gasto IS NOT NULL AND pagos_facturas.id_gasto <> 0 THEN "egreso"
                        WHEN pagos_facturas.id_ingresos_retiros IS NOT NULL AND pagos_facturas.id_ingresos_retiros <> 0 AND pagos_facturas.monto_ingreso_retiro < 0 THEN "egreso"
                        WHEN pagos_facturas.id_ingresos_retiros IS NOT NULL AND pagos_facturas.id_ingresos_retiros <> 0 AND pagos_facturas.monto_ingreso_retiro > 0 THEN "ingreso"
                        WHEN pagos_facturas.id_cobro_rapido IS NOT NULL AND pagos_facturas.id_cobro_rapido <> 0 THEN "ingreso"
                        WHEN pagos_facturas.id_compra_insumos IS NOT NULL AND pagos_facturas.id_compra_insumos <> 0 THEN "egreso"
                        ELSE NULL
                    END AS tipo_movimiento'),
            DB::raw('CASE
                        WHEN pagos_facturas.id_venta_insumos IS NOT NULL AND pagos_facturas.id_venta_insumos <> 0 THEN "venta_insumos"
                        WHEN pagos_facturas.id_factura IS NOT NULL AND pagos_facturas.id_factura <> 0 THEN "venta"
                        WHEN pagos_facturas.id_compra IS NOT NULL AND pagos_facturas.id_compra <> 0 THEN "compra"
                        WHEN pagos_facturas.id_gasto IS NOT NULL AND pagos_facturas.id_gasto <> 0 THEN "gasto"
                        WHEN pagos_facturas.id_ingresos_retiros IS NOT NULL AND pagos_facturas.id_ingresos_retiros <> 0 THEN "ingreso_retiro"
                        WHEN pagos_facturas.id_cobro_rapido IS NOT NULL AND pagos_facturas.id_cobro_rapido <> 0 THEN "cobro rapido"
                        WHEN pagos_facturas.id_compra_insumos IS NOT NULL AND pagos_facturas.id_compra_insumos <> 0 THEN "compra insumos"
                        ELSE NULL
                    END AS tipo'),
            DB::raw('CASE
                        WHEN pagos_facturas.id_venta_insumos IS NOT NULL AND pagos_facturas.id_venta_insumos <> 0 THEN sales_insumos.nro_venta
                        WHEN pagos_facturas.id_factura IS NOT NULL AND pagos_facturas.id_factura <> 0 THEN sales.nro_venta
                        WHEN pagos_facturas.id_compra IS NOT NULL AND pagos_facturas.id_compra <> 0 THEN compras_proveedores.nro_compra
                        WHEN pagos_facturas.id_gasto IS NOT NULL AND pagos_facturas.id_gasto <> 0 THEN pagos_facturas.id_gasto
                        WHEN pagos_facturas.id_ingresos_retiros IS NOT NULL AND pagos_facturas.id_ingresos_retiros <> 0 THEN pagos_facturas.id_ingresos_retiros
                        WHEN pagos_facturas.id_cobro_rapido IS NOT NULL AND pagos_facturas.id_cobro_rapido <> 0 THEN pagos_facturas.id_cobro_rapido
                        WHEN pagos_facturas.id_compra_insumos IS NOT NULL AND pagos_facturas.id_compra_insumos <> 0 THEN pagos_facturas.id_compra_insumos
                        ELSE NULL
                    END AS referencia_id'),
            DB::raw('CASE
                        WHEN pagos_facturas.id_venta_insumos IS NOT NULL AND pagos_facturas.id_venta_insumos <> 0 THEN (pagos_facturas.monto + pagos_facturas.recargo + pagos_facturas.iva_pago + pagos_facturas.iva_recargo)
                        WHEN pagos_facturas.id_factura IS NOT NULL AND pagos_facturas.id_factura <> 0 THEN (pagos_facturas.monto + pagos_facturas.recargo + pagos_facturas.iva_pago + pagos_facturas.iva_recargo)
                        WHEN pagos_facturas.id_compra IS NOT NULL AND pagos_facturas.id_compra <> 0 THEN (pagos_facturas.monto_compra + pagos_facturas.actualizacion)
                        WHEN pagos_facturas.id_gasto IS NOT NULL AND pagos_facturas.id_gasto <> 0 THEN pagos_facturas.monto_gasto
                        WHEN pagos_facturas.id_ingresos_retiros IS NOT NULL AND pagos_facturas.id_ingresos_retiros <> 0 THEN pagos_facturas.monto_ingreso_retiro
                        WHEN pagos_facturas.id_cobro_rapido IS NOT NULL AND pagos_facturas.id_cobro_rapido <> 0 THEN pagos_facturas.monto_cobro_rapido
                        WHEN pagos_facturas.id_compra_insumos IS NOT NULL AND pagos_facturas.id_compra_insumos <> 0 THEN pagos_facturas.monto_compra
                        ELSE NULL
                    END AS monto'),
            DB::raw('CASE
                        WHEN pagos_facturas.caja IS NOT NULL AND pagos_facturas.caja <> 0 THEN cajas.nro_caja
                        ELSE "no asociado a caja"
                    END AS caja'),
            DB::raw('CASE
                        WHEN pagos_facturas.banco_id IS NOT NULL AND pagos_facturas.banco_id <> 0 THEN bancos.nombre
                        ELSE NULL
                    END AS banco'),
            DB::raw('CASE
                        WHEN pagos_facturas.metodo_pago IS NOT NULL AND pagos_facturas.metodo_pago <> 0 THEN metodo_pagos.nombre
                        ELSE NULL
                    END AS metodo_pago'),
            DB::raw('CASE
                        WHEN pagos_facturas.cliente_id IS NOT NULL AND pagos_facturas.cliente_id <> 0 THEN clientes_mostradors.nombre
                        ELSE NULL
                    END AS cliente'),
            DB::raw('CASE
                        WHEN pagos_facturas.proveedor_id IS NOT NULL AND pagos_facturas.proveedor_id <> 0 THEN proveedores.nombre
                        ELSE NULL
                    END AS proveedor'),
            'pagos_facturas.estado_pago',
            'pagos_facturas.created_at',
            'pagos_facturas.nro_comprobante',
            'pagos_facturas.url_comprobante',
            'pagos_facturas.deducciones'
        )
        ->leftJoin('sales_insumos', 'sales_insumos.id', '=', 'pagos_facturas.id_venta_insumos')
        ->leftJoin('sales', 'sales.id', '=', 'pagos_facturas.id_factura')
        ->leftJoin('compras_proveedores', 'compras_proveedores.id', '=', 'pagos_facturas.id_compra')
        ->leftJoin('bancos', 'bancos.id', '=', 'pagos_facturas.banco_id')
        ->leftJoin('metodo_pagos', 'metodo_pagos.id', '=', 'pagos_facturas.metodo_pago')
        ->leftJoin('cajas', 'cajas.id', '=', 'pagos_facturas.caja')
        ->leftJoin('proveedores', 'proveedores.id', '=', 'pagos_facturas.proveedor_id')
        ->leftJoin('clientes_mostradors', 'clientes_mostradors.id', '=', 'pagos_facturas.cliente_id')
        ->join('users','users.id','pagos_facturas.comercio_id')
        ->where('pagos_facturas.eliminado', 0)
        ->whereBetween('pagos_facturas.created_at', [$from, $to]);
        if($sucursal_id != 0){
        $datos_pagos_query = $datos_pagos_query->where('pagos_facturas.comercio_id', $sucursal_id);    
        } else {
        $datos_pagos_query = $datos_pagos_query->whereIn('pagos_facturas.comercio_id', $this->sucursales_todas);        
        }
        
        
        // Aplica los filtros a la primera consulta
        $datos_pagos_query = $this->FiltroEstadoPago($datos_pagos_query);
        $datos_pagos_query = $this->FiltroTipoMovimiento($datos_pagos_query);
        $datos_pagos_query = $this->FiltrosOperacion($datos_pagos_query);
        $datos_pagos_query = $this->FiltrosBanco($datos_pagos_query);
        $datos_pagos_query = $this->FiltrosMetodoPago($datos_pagos_query);
        
        return $datos_pagos_query;
}

public function GetSaldosIniciales($sucursal_id){
            $saldos_iniciales_query = saldos_iniciales::
            join('users','users.id','saldos_iniciales.comercio_id')
            ->join('bancos','bancos.id','saldos_iniciales.referencia_id')
            ->select(
            'users.name as sucursal',
            DB::raw('null as id'),
            DB::raw('"Saldo inicial" as tipo_movimiento'),
            DB::raw('"Saldo inicial" as tipo'),
            'saldos_iniciales.referencia_id as referencia_id',
            'saldos_iniciales.monto as monto',
            DB::raw('null as caja'),
            'bancos.nombre as banco',
            DB::raw('null as metodo_pago'),
            DB::raw('null as cliente'),
            DB::raw('null as proveedor'),
            DB::raw('2 as estado_pago'),
            DB::raw('null as created_at'),
            DB::raw('null as nro_comprobante'),
            DB::raw('null as url_comprobante'),
            DB::raw('null as deducciones')
        );
        if($sucursal_id != 0){
        $saldos_iniciales_query = $saldos_iniciales_query->where('saldos_iniciales.comercio_id', $sucursal_id);
        } else {
        $saldos_iniciales_query = $saldos_iniciales_query->whereIn('saldos_iniciales.comercio_id', $this->sucursales_todas);        
        }
        
        $saldos_iniciales_query = $this->FiltrosBancoSaldoInicial($saldos_iniciales_query);
        $saldos_iniciales_query = $this->FiltrosEstadoPagoSaldoInicial($saldos_iniciales_query); // Aca funciona mal chequear
        $saldos_iniciales_query = $this->FiltrosOperacionSaldoInicial($saldos_iniciales_query);
        
        $saldos_iniciales_query = $saldos_iniciales_query->where('saldos_iniciales.tipo', 'Banco')
        ->where('saldos_iniciales.concepto', 'Saldo inicial');
        
        return $saldos_iniciales_query;
}

public function GetPagosSaldosClientesProveedores($from, $to,$sucursal_id){
    
            $saldos_proveedores_clientes_query = saldos_iniciales::
            join('users','users.id','saldos_iniciales.comercio_id')
            ->leftJoin('bancos', 'bancos.id', 'saldos_iniciales.metodo_pago')
            ->leftJoin('cajas', 'cajas.id', 'saldos_iniciales.caja_id')
            ->leftJoin('proveedores', function ($join) {
                $join->on('saldos_iniciales.referencia_id', '=', 'proveedores.id')
                     ->where('saldos_iniciales.tipo', '=', 'proveedor');
            })
            ->leftJoin('clientes_mostradors', function ($join) {
                $join->on('saldos_iniciales.referencia_id', '=', 'clientes_mostradors.id')
                     ->where('saldos_iniciales.tipo', '=', 'cliente');
            })
            ->select(
                DB::raw('users.name as sucursal'),
                DB::raw('saldos_iniciales.id as id'),
                DB::raw('CASE 
                    WHEN saldos_iniciales.tipo = "proveedor" THEN "egreso" 
                    WHEN saldos_iniciales.tipo = "cliente" THEN "ingreso" 
                    ELSE null 
                 END as tipo_movimiento'),
                 DB::raw('CASE 
                    WHEN saldos_iniciales.tipo = "proveedor" THEN "Pago saldo proveedor" 
                    WHEN saldos_iniciales.tipo = "cliente" THEN "Pago saldo cliente" 
                    ELSE null 
                 END as tipo'),
                'saldos_iniciales.referencia_id as referencia_id',
                DB::raw('-1*saldos_iniciales.monto as monto'),
                DB::raw('cajas.nro_caja as caja'),
                'bancos.nombre as banco',
                DB::raw('null as metodo_pago'),
                DB::raw('CASE 
                            WHEN saldos_iniciales.tipo = "proveedor" THEN proveedores.nombre 
                            WHEN saldos_iniciales.tipo = "cliente" THEN clientes_mostradors.nombre 
                            ELSE null 
                         END as nombre'),
                DB::raw('null as proveedor'),
                DB::raw('saldos_iniciales.estado_pago'),
                DB::raw('saldos_iniciales.created_at'),
                DB::raw('null as nro_comprobante'),
                DB::raw('null as url_comprobante'),
                DB::raw('null as deducciones')
            );
            
        
        if($sucursal_id != 0){
        $saldos_proveedores_clientes_query = $saldos_proveedores_clientes_query->where('saldos_iniciales.comercio_id', $sucursal_id);
        } else {
        $saldos_proveedores_clientes_query = $saldos_proveedores_clientes_query->whereIn('saldos_iniciales.comercio_id', $this->sucursales_todas);        
        }
        
        $saldos_proveedores_clientes_query = $this->FiltrosBancoClienteProveedor($saldos_proveedores_clientes_query);
        $saldos_proveedores_clientes_query = $this->FiltrosEstadoPagoPagosSaldoInicial($saldos_proveedores_clientes_query);
        $saldos_proveedores_clientes_query = $this->FiltrosOperacionPagoSaldoInicial($saldos_proveedores_clientes_query);
        $saldos_proveedores_clientes_query = $this->FiltroTipoMovimientoPagosSaldosIniciales($saldos_proveedores_clientes_query);
        
        
        $saldos_proveedores_clientes_query = $saldos_proveedores_clientes_query
        ->where('saldos_iniciales.eliminado',0)
        ->whereBetween('saldos_iniciales.created_at', [$from, $to])
        ->where( function($query) {
		 $query->where('saldos_iniciales.concepto', 'like', 'Pago')
		->orWhere('saldos_iniciales.concepto', 'like', 'Cobro');
		});

        return $saldos_proveedores_clientes_query;
}   

public function GetMovimientosEntreCuentas($from, $to,$sucursal_id){
            $movimientos_query = movimiento_dinero_cuentas_detalle::
            //join('users','users.id','movimiento_dinero_cuentas_detalle.comercio_id')
            join('bancos','bancos.id','movimiento_dinero_cuentas_detalles.banco_id')
            ->select(
            DB::raw('null as sucursal'),
            DB::raw('null as id'),
            DB::raw('CASE
                        WHEN movimiento_dinero_cuentas_detalles.monto IS NOT NULL AND  movimiento_dinero_cuentas_detalles.monto > 0 THEN "ingreso"
                        WHEN movimiento_dinero_cuentas_detalles.monto IS NOT NULL AND movimiento_dinero_cuentas_detalles.monto < 0 THEN "egreso"
                        ELSE NULL
                    END AS tipo_movimiento'),
            DB::raw('"Movimiento entre cuentas" as tipo'),
            'movimiento_dinero_cuentas_detalles.movimiento_dinero_cuenta_id as referencia_id',
            'movimiento_dinero_cuentas_detalles.monto as monto',
            DB::raw('null as caja'),
            'bancos.nombre as banco',
            DB::raw('null as metodo_pago'),
            DB::raw('null as cliente'),
            DB::raw('null as proveedor'),
            DB::raw('3 as estado_pago'),
            DB::raw('movimiento_dinero_cuentas_detalles.created_at as created_at'),
            DB::raw('null as nro_comprobante'),
            DB::raw('null as url_comprobante'),
            DB::raw('null as deducciones') 
        )
        ->whereBetween('movimiento_dinero_cuentas_detalles.created_at', [$from, $to]);
        
        if($sucursal_id != 0){
        $movimientos_query = $movimientos_query->where('movimiento_dinero_cuentas_detalles.comercio_id', $sucursal_id);
        } else {
        $movimientos_query = $movimientos_query->whereIn('movimiento_dinero_cuentas_detalles.comercio_id', $this->sucursales_todas);        
        }
        
        
        $movimientos_query = $movimientos_query->where('movimiento_dinero_cuentas_detalles.eliminado',0);
        
        $movimientos_query = $this->FiltrosBancoMovimiento($movimientos_query);
        $movimientos_query = $this->FiltroTipoMovimientoMovimiento($movimientos_query); 
        $movimientos_query = $this->FiltrosOperacionMovimiento($movimientos_query);
        
        
        return $movimientos_query;
}

    public function FiltrosOperacionMovimiento($datos){
        if($this->operacion_filtro != "Movimiento" && $this->operacion_filtro != null && $this->operacion_filtro != 0){
            $datos = $datos->where('movimiento_dinero_cuentas_detalles.monto',0);  
        }
      return $datos;
	}
	
	public function FiltrosBancoMovimiento($datos_pagos){
	  if($this->banco_filtro != "0" && $this->banco_filtro != null){
        $datos_pagos = $datos_pagos->where('movimiento_dinero_cuentas_detalles.banco_id',$this->banco_filtro);
      }	    
      return $datos_pagos;
	}
	
	public function FiltroTipoMovimientoMovimiento($datos)
{
    if ($this->tipo_movimiento_filtro) {
        // Filtrar por tipo_movimiento basado en el valor seleccionado
        if ($this->tipo_movimiento_filtro == "ingreso") {
            $datos = $datos->where('movimiento_dinero_cuentas_detalles.monto','>',0);
        } elseif ($this->tipo_movimiento_filtro == "egreso") {
            $datos = $datos->where('movimiento_dinero_cuentas_detalles.monto','<',0);
        }
    }

    return $datos;
}
    public function FiltroEstadoPago($datos_pagos){
	  if($this->estado_pago != null){
        $datos_pagos = $datos_pagos->where('pagos_facturas.estado_pago',$this->estado_pago);
      }        
      
      return $datos_pagos;
    }  
    
    public function FiltrosOperacionSaldoInicial($datos_pagos){
	  if($this->operacion_filtro != null){
        $datos_pagos = $datos_pagos->where('saldos_iniciales.eliminado', 1);
      }

      return $datos_pagos;
	}
	
	public function FiltrosOperacionPagoSaldoInicial($datos_pagos){
	  if($this->operacion_filtro){
        if($this->operacion_filtro == "Pago saldo cliente"){
            $datos_pagos = $datos_pagos->where('saldos_iniciales.tipo','like','cliente');  
        }
        elseif($this->operacion_filtro == "Pago saldo proveedor"){
            $datos_pagos = $datos_pagos->where('saldos_iniciales.tipo','like','proveedor');  
        } else {
           $datos_pagos = $datos_pagos->where('saldos_iniciales.eliminado',1);   
        }
        
      }

      return $datos_pagos;
	}
	
	public function FiltrosOperacion($datos_pagos){
	  if($this->operacion_filtro){
	  if($this->operacion_filtro == "Venta"){
        $datos_pagos = $datos_pagos->where('pagos_facturas.id_factura','>', 0)
        ->whereNotNull('pagos_facturas.id_factura');          
      } 
      elseif($this->operacion_filtro == "Compra"){
        $datos_pagos = $datos_pagos->where('pagos_facturas.id_compra','>', 0)
        ->whereNotNull('pagos_facturas.id_compra');  
      }
      elseif($this->operacion_filtro == "Gastos"){
        $datos_pagos = $datos_pagos->where('pagos_facturas.id_gasto','>', 0)
        ->whereNotNull('pagos_facturas.id_gasto');  
      }
      elseif($this->operacion_filtro == "Ingresos"){
        $datos_pagos = $datos_pagos->where('pagos_facturas.id_ingresos_retiros','>', 0)
        ->whereNotNull('pagos_facturas.id_ingresos_retiros');  
      } else {
        $datos_pagos = $datos_pagos->where('pagos_facturas.eliminado',1);  
      }
	      
	  }
      
      return $datos_pagos;
	}
	
	public function FiltrosBanco($datos_pagos){
	  if($this->banco_filtro){
        $datos_pagos = $datos_pagos->where('pagos_facturas.banco_id',$this->banco_filtro);
      }	    
      return $datos_pagos;
	}

    public function FiltrosBancoClienteProveedor($datos_pagos){
	  if($this->banco_filtro){
        $datos_pagos = $datos_pagos->where('saldos_iniciales.metodo_pago',$this->banco_filtro);
      }	    
      return $datos_pagos;        
    }
	public function FiltrosBancoSaldoInicial($datos_pagos){
	  if($this->banco_filtro){
        $datos_pagos = $datos_pagos->where('saldos_iniciales.referencia_id',$this->banco_filtro);
      }	    
      return $datos_pagos;
	}
	
	public function FiltrosEstadoPagoSaldoInicial($datos_pagos){
	  if($this->estado_pago == "0" && $this->estado_pago != null){
        $datos_pagos = $datos_pagos->where('saldos_iniciales.referencia_id',null);
      }	    
      return $datos_pagos;	    
	}
	
	public function FiltrosEstadoPagoPagosSaldoInicial($datos_pagos){
	  if($this->estado_pago != null){
        $datos_pagos = $datos_pagos->where('saldos_iniciales.estado_pago',$this->estado_pago);
      }	    
      return $datos_pagos;	    
	}

	
	public function FiltrosMetodoPago($datos_pagos){
	  //dd($this->metodo_pago_filtro);
	  if($this->metodo_pago_filtro){
        $datos_pagos = $datos_pagos->where('pagos_facturas.metodo_pago',$this->metodo_pago_filtro);
      }	    
      return $datos_pagos;
	}

public function FiltroTipoMovimiento($datos_pagos)
{
    if ($this->tipo_movimiento_filtro) {
        // Filtrar por tipo_movimiento basado en el valor seleccionado
        if ($this->tipo_movimiento_filtro == "ingreso") {
            $datos_pagos->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('pagos_facturas.id_factura')
                        ->where('pagos_facturas.id_factura', '<>', 0);
                })
                ->orWhere(function ($q) {
                    $q->whereNotNull('pagos_facturas.id_ingresos_retiros')
                        ->where('pagos_facturas.id_ingresos_retiros', '<>', 0)
                        ->where('pagos_facturas.monto_ingreso_retiro', '>', 0);
                })
                ->orWhere(function ($q) {
                    $q->whereNotNull('pagos_facturas.id_cobro_rapido')
                        ->where('pagos_facturas.id_cobro_rapido', '<>', 0);
                });
            });
        } elseif ($this->tipo_movimiento_filtro == "egreso") {
            $datos_pagos->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('pagos_facturas.id_compra')
                        ->where('pagos_facturas.id_compra', '<>', 0);
                })
                ->orWhere(function ($q) {
                    $q->whereNotNull('pagos_facturas.id_gasto')
                        ->where('pagos_facturas.id_gasto', '<>', 0);
                })
                ->orWhere(function ($q) {
                    $q->whereNotNull('pagos_facturas.monto_ingreso_retiro')
                        ->where('pagos_facturas.monto_ingreso_retiro', '<', 0);
                })
                ->orWhere(function ($q) {
                    $q->whereNotNull('pagos_facturas.id_compra_insumos')
                        ->where('pagos_facturas.id_compra_insumos', '<>', 0);
                });
                
            });
        }
    }

    return $datos_pagos;
}

public function FiltroTipoMovimientoPagosSaldosIniciales($datos)
{
    if ($this->tipo_movimiento_filtro) {
        // Filtrar por tipo_movimiento basado en el valor seleccionado
        if ($this->tipo_movimiento_filtro == "ingreso") {
            $filtro = "cliente";
            $datos = $datos->where('saldos_iniciales.tipo','like',$filtro);
        } elseif ($this->tipo_movimiento_filtro == "egreso") {
            $filtro = "proveedor";
            $datos = $datos->where('saldos_iniciales.tipo','like',$filtro);
        }
    }

    return $datos;
}

    public function ExportarExcel() {
        $hora = " ".Carbon::now()->format('d-m-Y H:i:s');
        
        if($this->estado_pago == null || $this->estado_pago == ""){$estado_pago = 2;}else {$estado_pago = $this->estado_pago;}
        if( $this->tipo_movimiento_filtro == null ||  $this->tipo_movimiento_filtro == ""){$tipo_movimiento_filtro = 0;}else {$tipo_movimiento_filtro =  $this->tipo_movimiento_filtro;}
        if($this->operacion_filtro == null || $this->operacion_filtro == ""){$operacion_filtro = 0;}else {$operacion_filtro = $this->operacion_filtro;}
        if($this->banco_filtro == null || $this->banco_filtro == ""){$banco_filtro = 0;}else {$banco_filtro = $this->banco_filtro;}
        if($this->metodo_pago_filtro == null || $this->metodo_pago_filtro == ""){$metodo_pago_filtro = 0;}else {$metodo_pago_filtro = $this->metodo_pago_filtro;}
        
        $url = 'report-pagos/excel/'.$tipo_movimiento_filtro.'/'.$estado_pago.'/'.$operacion_filtro.'/'.$banco_filtro.'/'.$metodo_pago_filtro.'/'.$this->comercio_id.'/'. $hora." hs";
        //dd($url);
        
        return redirect($url);
    }
    
    public function LimpiarFiltros(){
    
    $this->search = null;
    $this->operacion_filtro = null;
    $this->banco_filtro = null;
    $this->tipo_movimiento_filtro = null;
    $this->metodo_pago_filtro = null;
    $this->dateFrom  = Carbon::now()->firstOfYear()->format('Y-m-d');
    $this->dateTo = Carbon::now()->format('Y-m-d');
    $this->emit("set-fecha",$this->dateFrom,$this->dateTo);

    }
    
    public function SetIvaProductos(){
        // Obtenemos los usuarios que son casa central
        $usuarios = User::select('*')->where('comercio_id',1)->get();
        
        foreach($usuarios as $u){
        $df = datos_facturacion::where("comercio_id",$u->id)->first();    
        
        if($df != null){
        if($df->iva_defecto != null){
        $iva = $df->iva_defecto;    
        } else {
        $iva = 0;    
        }
        } else {
        $iva = 0;    
        }
        
        $products = Product::where('comercio_id',$u->casa_central_user_id)->get();
        
        foreach($products as $product){
            productos_ivas::create([
                'product_id' => $product->id,
                'iva' => $iva,
                'comercio_id' => $u->casa_central_user_id,
                'sucursal_id' => $u->id
                ]);
        }
            
        }
     
     
     $this->emit("msg-error","REGISTRO COMPLETADO");   
        
    }
    
    
     public function EditarPago($id,$tipo){
     
     $this->formato_ver = 1;
     $this->tipo_pago_seleccionado = $tipo;
     $this->id_pago = $id; 
     $this->SetearDatosVistaPago($id,$tipo);  
     $pagos = pagos_facturas::find($id);
     $this->VerDeducciones($pagos); // 30-6-2024
     }
       
     public function SetearDatosVistaPago($id,$tipo){

     if($tipo == "Pago saldo cliente"){
        $pago = saldos_iniciales::find($id);
        $this->tipo_pago = $pago->metodo_pago;
        $this->metodo_pago_agregar_pago = null;
        $this->monto_ap = -1*$pago->monto; 
        $this->recargo = 0;
        $this->caja = $pago->caja_id;
     }
     
      if($tipo == "Pago saldo proveedor"){
        $pago = saldos_iniciales::find($id);
        $this->tipo_pago = $pago->metodo_pago;
        $this->metodo_pago_agregar_pago = null;
        $this->monto_ap = -1*$pago->monto; 
        $this->recargo = 0;
        $this->caja = $pago->caja_id;
     }
     if($tipo == 'ingreso_retiro'){
        $pago = pagos_facturas::find($id);
        $this->metodo_pago_agregar_pago = $pago->metodo_pago;
        $this->monto_ap = $pago->monto_ingreso_retiro; 
        $this->recargo = 0;
        $this->tipo_pago = $pago->banco_id;
        $this->caja = $pago->caja;
     }     
     if($tipo == 'gasto'){
        $pago = pagos_facturas::find($id);
        $this->metodo_pago_agregar_pago = $pago->metodo_pago;
        $this->monto_ap = $pago->monto_gasto; 
        $this->recargo = 0;
        $this->tipo_pago = $pago->banco_id;
        $this->caja = $pago->caja;
     }
     if($tipo == 'compra'){
        $pago = pagos_facturas::find($id);
        $this->metodo_pago_agregar_pago = $pago->metodo_pago;
        $this->monto_ap = $pago->monto_compra; 
        $this->recargo = 0;
        $this->tipo_pago = $pago->banco_id;
        $this->caja = $pago->caja;
     }
     if($tipo == 'venta'){
        $pago = pagos_facturas::find($id);
        $this->metodo_pago_agregar_pago = $pago->metodo_pago;
        $this->monto_ap = $pago->monto + $pago->iva_pago;
        $this->id_pedido = $pago->id_factura; 
        $this->id_pago = $id; 
        $metodo_pago = metodo_pago::find($this->metodo_pago_agregar_pago);
        $this->recargo = $metodo_pago->recargo/100;
        $this->tipo_pago = $pago->banco_id;
        $this->caja = $pago->caja;
        $venta = Sale::find($pago->id_factura);
        $this->SetPagosClienteSucursal($venta->cliente_id,$pago->id);
        
     }   
     if($tipo == 'venta_insumos'){
        $pago = pagos_facturas::find($id);
        $this->metodo_pago_agregar_pago = $pago->metodo_pago;
        $this->monto_ap = $pago->monto + $pago->iva_pago;
        $this->id_pedido = $pago->id_venta_insumos; 
        $this->id_pago = $id; 
        $metodo_pago = metodo_pago::find($this->metodo_pago_agregar_pago);
        $this->recargo = $metodo_pago->recargo/100;
        $this->tipo_pago = $pago->banco_id;
        $this->caja = $pago->caja;
        $venta = SalesInsumos::find($pago->id_venta_insumos);
        $this->SetPagosClienteSucursal($venta->cliente_id,$pago->id);
        
        
     }   
        
        $this->fecha_ap = Carbon::parse($pago->created_at)->format('d-m-Y');
        $this->caja_seleccionada = cajas::find($this->caja);
        
        
        $this->recargo_total = $pago->recargo + $pago->iva_recargo;
        $this->total_pago = $this->recargo_total + $this->monto_ap;
        
        $this->pago_selected = $pago->id;
        $this->nro_comprobante = $pago->nro_comprobante;
        $this->comprobante = $pago->comprobante;
        
        
        $this->emit('ver-pago','');    
         
     } 
     
     public function VerPago($id,$tipo){
     $this->formato_ver = 0;
     $this->tipo_pago_seleccionado = $tipo;
     $this->SetearDatosVistaPago($id,$tipo);
     $pagos = pagos_facturas::find($id);
     $this->VerDeducciones($pagos); // 30-6-2024
     }

     
    public function CerrarVerPago(){
        $this->emit('ver-pago-hide','');    
    }
     
    public function CambiarEstadoPago($id,$nuevo_estado,$tipo){
        
        $this->deducciones = [];
        
        $this->nuevo_estado = $nuevo_estado;
        
        if($tipo == 'Pago saldo cliente' || $tipo == 'Pago saldo proveedor'){
        $pago = saldos_iniciales::find($id);
        $this->pago_selected = $pago->id;
        $this->nro_comprobante = null;
        $this->comprobante = null;
        $this->tipo = $tipo;
        } else {
        $this->id_pago = $id;     
        $pago = pagos_facturas::find($id);
        $this->pago_selected = $pago->id;
        $this->nro_comprobante = $pago->nro_comprobante;
        $this->comprobante = $pago->comprobante;
        $this->tipo = $tipo;
        $this->tipo_pago_seleccionado = $tipo;
    //    $this->SetearDatosVistaPago($id,$tipo);  
        $this->total_pago = $pago->monto + $pago->recargo + $pago->iva_pago + $pago->iva_recargo;
        
        $this->VerDeducciones($pago); // 30-6-2024
        }

        $this->emit('cambiar-estado','');
    }
 
    
     public function CerrarCambiarEstado(){
        $this->pago_selected = null;
        $this->nro_comprobante = null;
        $this->comprobante = null;
        $this->nuevo_estado = null;
        $this->emit('cambiar-estado-hide',''); 
     }
     
     public function StoreCambiarEstado(){
         
        if($this->tipo == 'Pago saldo cliente' || $this->tipo == 'Pago saldo proveedor'){
        $pago = saldos_iniciales::find($this->pago_selected);
        
        $pago->update([
            'estado_pago' => $this->nuevo_estado
            ]);    

        } else {

        $pago = pagos_facturas::find($this->pago_selected);
        
        $pago->update([
            'nro_comprobante' => $this->nro_comprobante,
            'comprobante' => $this->comprobante,
            'estado_pago' => $this->nuevo_estado
            ]);     
            
        $this->guardarDeducciones($pago); // 30-6-2024
        
        }

            
        $this->CerrarCambiarEstado();
        $this->emit('noty-estado','Estado modificado');
     }
     
     
     public function SetearPagosEfectivo(){
     $pf = pagos_facturas::get(); 
     
     foreach($pf as $p){
        $pago = pagos_facturas::find($p->id);
        $estado_pago = $this->GetPlazoAcreditacionPago($pago->metodo_pago);
        $pago->estado_pago = $estado_pago;
        $pago->save();
     }
     }
     
    //18-5-2024
    public function GetPlazoAcreditacionPago($id){
        return $id == 1 ? 1 : 0;
    }
     
     public function SetearSaldosIniciales(){
     $bancos = bancos::orderBy('id','desc')->get();    
     
     foreach($bancos as $banco){

     $saldos_iniciales = saldos_iniciales::where('tipo','Banco')->where('concepto','Saldo inicial')->where('referencia_id',$banco->id)->first();
     
     if($saldos_iniciales == null){
     $array_saldos = [
        'tipo' => 'Banco',
        'concepto' => 'Saldo inicial',
        'referencia_id' => $banco->id,
        'comercio_id' => $banco->comercio_id,
        'eliminado' => 0,
        'monto' => $banco->saldo_inicial,
        'metodo_pago' => $banco->id,
        'fecha' => $banco->created_at
        ];
    
    $s = saldos_iniciales::create($array_saldos);         
     }
         
    }
     
     $this->emit('noty-estado','Saldos iniciales seteados');    
     }
     
     
    public function UpdatePago($id,$tipo_pago){
        
        if($this->tipo_pago == "Elegir"){
            $this->emit("msg-error","Debe elegir un tipo de pago");
            return;
        }
        
        if($tipo_pago === "venta_insumos"){
        if($this->metodo_pago_agregar_pago == "Elegir"){
            $this->emit("msg-error","Debe elegir un tipo de pago");
            return;
        }
        $this->ActualizarPagoVentaInsumos($id,$this->monto_ap,$this->recargo,$this->recargo_total,$this->caja,$this->fecha_ap,$this->tipo_pago,$this->metodo_pago_agregar_pago,$this->nro_comprobante,$this->comprobante); 
        
        $this->CerrarModalPago();
        $this->emit('noty-estado','Cobro Actualizado');
        }
        
        
        if($tipo_pago === "venta"){
        if($this->metodo_pago_agregar_pago == "Elegir"){
            $this->emit("msg-error","Debe elegir un tipo de pago");
            return;
        }
        $this->ActualizarPagoVenta($id,$this->monto_ap,$this->recargo,$this->recargo_total,$this->caja,$this->fecha_ap,$this->tipo_pago,$this->metodo_pago_agregar_pago,$this->nro_comprobante,$this->comprobante); 
        
        $this->CerrarModalPago();
        $this->emit('noty-estado','Cobro Actualizado');
        }
        
        
        if($tipo_pago === "compra"){
       
        $this->ActualizarPagoCompraTrait($id,$this->monto_ap,$this->tipo_pago,$this->fecha_ap,$this->caja);
        $this->CerrarModalPago();
        $this->emit('noty-estado','Pago Actualizado');
        }
        
        // 14-8-2024
        if($tipo_pago === "gasto"){
       
        $this->ActualizarPagoGasto($id,$this->monto_ap,$this->tipo_pago,$this->fecha_ap,$this->caja);
        $this->CerrarModalPago();
        $this->emit('noty-estado','Pago Actualizado');
        }
        
        if($tipo_pago == "Pago saldo cliente"){
        $si = saldos_iniciales::find($id);
        $this->ActualizarPagoSaldoInicial($id,'cliente',$si->referencia_id,$si->comercio_id,$this->monto_ap,$this->tipo_pago,$this->caja); 
        $this->CerrarModalPago();
        $this->emit('noty-estado','Pago Actualizado');
        }
         
        if($tipo_pago == "Pago saldo proveedor"){
        $si = saldos_iniciales::find($id);
        $this->ActualizarPagoSaldoInicial($id,'proveedor',$si->referencia_id,$si->comercio_id,$this->monto_ap,$this->tipo_pago,$this->caja); 
        $this->CerrarModalPago();
        $this->emit('noty-estado','Pago Actualizado');
        }        
          


    }
    
    public function CerrarModalPago(){
        $this->emit("ver-pago-hide","");
    }
    
    public function TipoPagoForm($value,$tipo_pago){
        if($tipo_pago == 'venta'){
            $this->TipoPago($value);
        }
    }
    
    public function MetodoPagoForm($value,$tipo_pago){
        if($tipo_pago == 'venta'){
            $this->MetodoPago($value);
        }
    }
    
    public function MontoPagoEditarPagoForm($value,$tipo_pago){
       // dd($value,$tipo_pago);
        if($tipo_pago == 'venta'){
            $this->MontoPagoEditarPago($value);
        } else {
            $this->recargo_total = 0;
            $this->total_pago = $value;
        }       
    }
    
    public function EliminarPagosEnLote($comercio_id){
        $pagos = pagos_facturas::where('comercio_id',$comercio_id)->get();
        foreach($pagos as $pago){
            $pago->eliminado = 1;
            $pago->save();
        }
    }
    
}
