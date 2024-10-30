<?php

namespace App\Exports;

use App\Models\pagos_facturas;
use App\Models\movimiento_dinero_cuentas_detalle;

use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


use App\Models\saldos_iniciales;
use App\Models\productos_stock_sucursales;


class PagosExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{


    protected $tipo_movimiento_filtro,$estado_pago,$operacion_filtro,$banco_filtro,$metodo_pago_filtro,$sucursal_id;
    public $casa_central_id;

    function __construct($tipo_movimiento_filtro,$estado_pago,$operacion_filtro,$banco_filtro,$metodo_pago_filtro,$sucursal_id) {
        $this->tipo_movimiento_filtro = $tipo_movimiento_filtro;
        $this->estado_pago = $estado_pago;
        $this->operacion_filtro = $operacion_filtro;
        $this->banco_filtro = $banco_filtro;
        $this->metodo_pago_filtro = $metodo_pago_filtro;
        $this->sucursal_id = $sucursal_id;

    }
    
    public function collection()
    {
      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

      $sucursal_id = $comercio_id;
        
      $data =[];

      // Realiza la primera consulta 
      $datos_pagos_query = $this->GetPagos($sucursal_id);
        
      // Realiza la segunda consulta
       
      $saldos_iniciales_query = $this->GetSaldosIniciales($sucursal_id);

      // Realizamos la tercer consulta
      $saldos_proveedores_clientes_query = $this->GetPagosSaldosClientesProveedores($sucursal_id);

      $movimiento_cuentas = $this->GetMovimientosEntreCuentas($sucursal_id);
         
      // Une las dos consultas
      $combined_query = $datos_pagos_query->union($saldos_iniciales_query)->union($saldos_proveedores_clientes_query);
        
      // Ordena y pagina los resultados combinados
      $combined_results = DB::table(DB::raw("({$combined_query->toSql()}) as combined"))
          ->mergeBindings($combined_query->getQuery())
          ->orderBy('created_at_formatted', 'desc')
          ->get();
                
      
      $data = $combined_results;
      
      return $data;
    }

	
	    public function FiltrosOperacionSaldoInicial($datos_pagos){
	  if($this->operacion_filtro != "0"){
        $datos_pagos = $datos_pagos->where('saldos_iniciales.eliminado', 1);
      }

      return $datos_pagos;
	}
	
	public function FiltrosOperacionPagoSaldoInicial($datos_pagos){
	  if($this->operacion_filtro != "0"){
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
	  if($this->operacion_filtro != "0"){
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
	
	public function FiltrosBanco($data){
	  if($this->banco_filtro != 0){
        $data = $data->where('pagos_facturas.banco_id',$this->banco_filtro);
      }	    
      return $data;
	}

	public function FiltrosMetodoPago($data){
	  if($this->metodo_pago_filtro != 0){
        $data = $data->where('pagos_facturas.metodo_pago',$this->metodo_pago_filtro);
      }	    
      return $data;
	}


public function FiltroTipoMovimiento($datos_pagos)
{
    if ($this->tipo_movimiento_filtro != "0") {
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
                        ->where('pagos_facturas.id_gasto', '<>', 0)
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

    //cabeceras del reporte
    public function headings() : array
    {
      return ["ID","ESTADO PAGO","TIPO","OPERACION","REFERENCIA OPERACION","PAGO","DEDUCCION/COMISIONES","MONTO FINAL","CAJA","BANCO","METODO DE PAGO","CLIENTE","PROVEEDOR","FECHA","NRO COMPROBANTE","URL COMPROBANTE"];
    }


    public function startCell(): string
    {
        return 'A1';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true ]],
        ];
    }

    public function title(): string
    {
        return 'Pagos';
    }

    public function FiltroEstadoPago($datos_pagos){
	  if($this->estado_pago != "2"){
        $datos_pagos = $datos_pagos->where('pagos_facturas.estado_pago',$this->estado_pago);
      }        
      
      return $datos_pagos;
    } 
    
    
public function GetPagos($sucursal_id){
    
            // Realiza la primera consulta
        $datos_pagos_query = pagos_facturas::select(
            'pagos_facturas.id',
            DB::raw('CASE
                        WHEN pagos_facturas.estado_pago IS NOT NULL AND pagos_facturas.estado_pago = 1 THEN "Acreditado"
                        ELSE "Pendiente"
                    END AS estado_pago'),
            DB::raw('CASE
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
                        WHEN pagos_facturas.id_factura IS NOT NULL AND pagos_facturas.id_factura <> 0 THEN "venta"
                        WHEN pagos_facturas.id_compra IS NOT NULL AND pagos_facturas.id_compra <> 0 THEN "compra"
                        WHEN pagos_facturas.id_gasto IS NOT NULL AND pagos_facturas.id_gasto <> 0 THEN "gasto"
                        WHEN pagos_facturas.id_ingresos_retiros IS NOT NULL AND pagos_facturas.id_ingresos_retiros <> 0 THEN "ingreso_retiro"
                        WHEN pagos_facturas.id_cobro_rapido IS NOT NULL AND pagos_facturas.id_cobro_rapido <> 0 THEN "cobro rapido"
                        WHEN pagos_facturas.id_compra_insumos IS NOT NULL AND pagos_facturas.id_compra_insumos <> 0 THEN "compra insumos"
                        ELSE NULL
                    END AS tipo'),
            DB::raw('CASE
                        WHEN pagos_facturas.id_factura IS NOT NULL AND pagos_facturas.id_factura <> 0 THEN sales.nro_venta
                        WHEN pagos_facturas.id_compra IS NOT NULL AND pagos_facturas.id_compra <> 0 THEN compras_proveedores.nro_compra
                        WHEN pagos_facturas.id_gasto IS NOT NULL AND pagos_facturas.id_gasto <> 0 THEN pagos_facturas.id_gasto
                        WHEN pagos_facturas.id_ingresos_retiros IS NOT NULL AND pagos_facturas.id_ingresos_retiros <> 0 THEN pagos_facturas.id_ingresos_retiros
                        WHEN pagos_facturas.id_cobro_rapido IS NOT NULL AND pagos_facturas.id_cobro_rapido <> 0 THEN pagos_facturas.id_cobro_rapido
                        WHEN pagos_facturas.id_compra_insumos IS NOT NULL AND pagos_facturas.id_compra_insumos <> 0 THEN pagos_facturas.id_compra_insumos
                        ELSE NULL
                    END AS referencia_id'),
            DB::raw('CASE
                        WHEN pagos_facturas.id_factura IS NOT NULL AND pagos_facturas.id_factura <> 0 THEN (pagos_facturas.monto + pagos_facturas.recargo + pagos_facturas.iva_pago + pagos_facturas.iva_recargo)
                        WHEN pagos_facturas.id_compra IS NOT NULL AND pagos_facturas.id_compra <> 0 THEN (pagos_facturas.monto_compra + pagos_facturas.actualizacion)
                        WHEN pagos_facturas.id_gasto IS NOT NULL AND pagos_facturas.id_gasto <> 0 THEN pagos_facturas.monto_gasto
                        WHEN pagos_facturas.id_ingresos_retiros IS NOT NULL AND pagos_facturas.id_ingresos_retiros <> 0 THEN pagos_facturas.monto_ingreso_retiro
                        WHEN pagos_facturas.id_cobro_rapido IS NOT NULL AND pagos_facturas.id_cobro_rapido <> 0 THEN pagos_facturas.monto_cobro_rapido
                        WHEN pagos_facturas.id_compra_insumos IS NOT NULL AND pagos_facturas.id_compra_insumos <> 0 THEN pagos_facturas.monto_compra
                        ELSE NULL
                    END AS monto'),
            DB::raw('CASE
                        WHEN pagos_facturas.id_factura IS NOT NULL AND pagos_facturas.id_factura <> 0 THEN (pagos_facturas.deducciones)
                        ELSE 0
                    END AS deduccion'),
            DB::raw('CASE
                        WHEN pagos_facturas.id_factura IS NOT NULL AND pagos_facturas.id_factura <> 0 THEN (pagos_facturas.monto + pagos_facturas.recargo + pagos_facturas.iva_pago + pagos_facturas.iva_recargo - pagos_facturas.deducciones)
                        WHEN pagos_facturas.id_compra IS NOT NULL AND pagos_facturas.id_compra <> 0 THEN (pagos_facturas.monto_compra + pagos_facturas.actualizacion)
                        WHEN pagos_facturas.id_gasto IS NOT NULL AND pagos_facturas.id_gasto <> 0 THEN pagos_facturas.monto_gasto
                        WHEN pagos_facturas.id_ingresos_retiros IS NOT NULL AND pagos_facturas.id_ingresos_retiros <> 0 THEN pagos_facturas.monto_ingreso_retiro
                        WHEN pagos_facturas.id_cobro_rapido IS NOT NULL AND pagos_facturas.id_cobro_rapido <> 0 THEN pagos_facturas.monto_cobro_rapido
                        WHEN pagos_facturas.id_compra_insumos IS NOT NULL AND pagos_facturas.id_compra_insumos <> 0 THEN pagos_facturas.monto_compra
                        ELSE NULL
                    END AS monto_final'),
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
            DB::raw('DATE_FORMAT(pagos_facturas.created_at, "%d-%m-%Y %H:%i:%s") as created_at_formatted'),
            'pagos_facturas.nro_comprobante',
            'pagos_facturas.url_comprobante'
        )
        ->leftJoin('sales', 'sales.id', '=', 'pagos_facturas.id_factura')
        ->leftJoin('compras_proveedores', 'compras_proveedores.id', '=', 'pagos_facturas.id_compra')
        ->leftJoin('bancos', 'bancos.id', '=', 'pagos_facturas.banco_id')
        ->leftJoin('metodo_pagos', 'metodo_pagos.id', '=', 'pagos_facturas.metodo_pago')
        ->leftJoin('cajas', 'cajas.id', '=', 'pagos_facturas.caja')
        ->leftJoin('proveedores', 'proveedores.id', '=', 'pagos_facturas.proveedor_id')
        ->leftJoin('clientes_mostradors', 'clientes_mostradors.id', '=', 'pagos_facturas.cliente_id')
        ->where('pagos_facturas.eliminado', 0)
        ->where('pagos_facturas.comercio_id', $sucursal_id);
        
        // Aplica los filtros a la primera consulta
        $datos_pagos_query = $this->FiltroEstadoPago($datos_pagos_query);
        $datos_pagos_query = $this->FiltroTipoMovimiento($datos_pagos_query);
        $datos_pagos_query = $this->FiltrosOperacion($datos_pagos_query);
        $datos_pagos_query = $this->FiltrosBanco($datos_pagos_query);
        $datos_pagos_query = $this->FiltrosMetodoPago($datos_pagos_query);
        
        return $datos_pagos_query;
}

public function GetSaldosIniciales($sucursal_id){
            $saldos_iniciales_query = saldos_iniciales::join('bancos','bancos.id','saldos_iniciales.referencia_id')
            ->select(
            DB::raw('null as id'),
            DB::raw('"Saldo inicial" as estado_pago'),
            DB::raw('"Saldo inicial" as tipo_movimiento'),
            DB::raw('"Saldo inicial" as tipo'),
            'saldos_iniciales.referencia_id as referencia_id',
            'saldos_iniciales.monto as monto',
            DB::raw('0 as deducciones'),
            'saldos_iniciales.monto as monto_final',
            DB::raw('null as caja'),
            'bancos.nombre as banco',
            DB::raw('null as metodo_pago'),
            DB::raw('null as cliente'),
            DB::raw('null as proveedor'),
            DB::raw('null as created_at'),
            DB::raw('null as nro_comprobante'),
            DB::raw('null as url_comprobante')
        )
        ->where('saldos_iniciales.comercio_id', $sucursal_id);
        
        $saldos_iniciales_query = $this->FiltrosBancoSaldoInicial($saldos_iniciales_query);
        $saldos_iniciales_query = $this->FiltrosOperacionSaldoInicial($saldos_iniciales_query);
        $saldos_iniciales_query = $this->FiltrosEstadoPagoSaldoInicial($saldos_iniciales_query); // Aca funciona mal chequear
        
        $saldos_iniciales_query = $saldos_iniciales_query->where('saldos_iniciales.tipo', 'Banco')
        ->where('saldos_iniciales.concepto', 'Saldo inicial');
        
        return $saldos_iniciales_query;
}

public function GetPagosSaldosClientesProveedores($sucursal_id){
    
            $saldos_proveedores_clientes_query = saldos_iniciales::leftJoin('bancos', 'bancos.id', 'saldos_iniciales.metodo_pago')
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
                DB::raw('saldos_iniciales.id as id'),
            DB::raw('CASE
                        WHEN saldos_iniciales.estado_pago IS NOT NULL AND saldos_iniciales.estado_pago = 1 THEN "Acreditado"
                        ELSE "Pendiente"
                    END AS estado_pago'),                
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
                DB::raw('0 as deducciones'),
                DB::raw('-1*saldos_iniciales.monto as monto_final'),
                DB::raw('cajas.nro_caja as caja'),
                'bancos.nombre as banco',
                DB::raw('null as metodo_pago'),
                DB::raw('CASE 
                            WHEN saldos_iniciales.tipo = "proveedor" THEN proveedores.nombre 
                            WHEN saldos_iniciales.tipo = "cliente" THEN clientes_mostradors.nombre 
                            ELSE null 
                         END as nombre'),
                DB::raw('null as proveedor'),
                DB::raw('DATE_FORMAT(saldos_iniciales.created_at, "%d-%m-%Y %H:%i:%s") as created_at_formatted'),
                DB::raw('null as nro_comprobante'),
                DB::raw('null as url_comprobante')
            )
            ->where('saldos_iniciales.comercio_id', $sucursal_id);
        
        $saldos_proveedores_clientes_query = $this->FiltrosBancoClienteProveedor($saldos_proveedores_clientes_query);
        $saldos_proveedores_clientes_query = $this->FiltrosEstadoPagoPagosSaldoInicial($saldos_proveedores_clientes_query);
        $saldos_proveedores_clientes_query = $this->FiltrosOperacionPagoSaldoInicial($saldos_proveedores_clientes_query);
        $saldos_proveedores_clientes_query = $this->FiltroTipoMovimientoPagosSaldosIniciales($saldos_proveedores_clientes_query);
        
        
        $saldos_proveedores_clientes_query = $saldos_proveedores_clientes_query
        ->where('saldos_iniciales.eliminado',0)
        ->where( function($query) {
		 $query->where('saldos_iniciales.concepto', 'like', 'Pago')
		->orWhere('saldos_iniciales.concepto', 'like', 'Cobro');
		});

        return $saldos_proveedores_clientes_query;
        }   

    
        public function FiltrosBancoClienteProveedor($datos_pagos){
	  if($this->banco_filtro != "0"){
        $datos_pagos = $datos_pagos->where('saldos_iniciales.metodo_pago',$this->banco_filtro);
      }	    
      return $datos_pagos;        
    }
	public function FiltrosBancoSaldoInicial($datos_pagos){
	  if($this->banco_filtro != "0"){
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
	  if($this->estado_pago != "2"){
        $datos_pagos = $datos_pagos->where('saldos_iniciales.estado_pago',$this->estado_pago);
      }	    
      return $datos_pagos;	    
	}

public function FiltroTipoMovimientoPagosSaldosIniciales($datos)
{

    if ($this->tipo_movimiento_filtro != "0") {
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


public function GetMovimientosEntreCuentas($sucursal_id){
            $movimientos_query = movimiento_dinero_cuentas_detalle::join('bancos','bancos.id','movimiento_dinero_cuentas_detalles.banco_id')
            ->select(
            DB::raw('null as id'),
            DB::raw('"Movimiento entre cuentas" as tipo_movimiento'),
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
        ->where('movimiento_dinero_cuentas_detalles.comercio_id', $sucursal_id)
        ->where('movimiento_dinero_cuentas_detalles.eliminado',0);
        
        $movimientos_query = $this->FiltrosBancoMovimiento($movimientos_query);
        $movimientos_query = $this->FiltroTipoMovimientoMovimiento($movimientos_query); // Aca funciona mal chequear
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



}
