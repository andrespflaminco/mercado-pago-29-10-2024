<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;
use App\Models\gastos;
use App\Models\Sale;
use App\Models\saldos_iniciales;
use App\Models\cajas;
use App\Models\proveedores;
use App\Models\compras_proveedores;
use App\Models\pagos_facturas;
use App\Models\beneficios;
use App\Models\sucursales;
use App\Models\UsersController;
use App\Models\bancos;
use App\Models\movimiento_dinero_cuentas_detalle;

use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
//use Codexshaper\WooCommerce\Facades\Product AS ProductWC;
use Automattic\WooCommerce\Client;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class ConsolidadoController extends Component
{
      use WithPagination;
      use WithFileUploads;

      public $es_sucursal,$app_url,$nombre,$recargo,$descripcion,$caja_es_activa, $estado_caja, $detalle_nro_caja, $numero_caja_ca, $monto_final_ca, $monto_inicial_ca, $fecha_inicial_ca, $fecha_final_ca, $price,$stock,$alerts,$categoryid,$search,$sucursal_id ,$image,$selected_id,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $metodos, $categoria, $monto, $dateFrom, $dateTo, $categoria_filtro, $suma_totales, $gastos_total, $etiquetas_gastos, $etiqueta, $nombre_etiqueta, $etiqueta_form, $etiquetas_filtro, $total_faltante, $cajas, $cajas_total, $etiquetas, $cajas_cajas_total,$details, $caja_id, $details_efectivo, $details_total, $details_bancos, $details_plataformas, $details_a_cobrar, $count_bancos, $count_a_cobrar, $count_efectivo, $count_plataformas, $total_efectivo_inicial, $total_efectivo_final , $details_efectivo_inicial, $total_bancos, $total_a_cobrar, $total_efectivo, $total_plataformas, $caja_activa, $monto_final, $monto_inicial, $ultimo;
    	private $pagination = 25;


    	public function paginationView()
    	{
    		return 'vendor.livewire.bootstrap';
    	}


    	public function mount()
    	{

        $this->details_bancos =[];
        $this->details_efectivo = [];
        $this->details_efectivo_inicial = [];
        $this->details_a_cobrar = [];
        $this->details_total = [];
        $this->detalle_nro_caja = [];
        $this->details_plataformas = [];
    	$this->pageTitle = 'Listado';
    	$this->componentName = 'Cajas';
    	$this->categoria = 'Elegir';
        $this->categoria_filtro = '';
    	$this->almacen = 'Elegir';
        $this->etiqueta_form = 'Sin etiqueta';
        $this->etiquetas_filtro = '';
    	$this->stock_descubierto = 'Elegir';
        $this->dateFrom = '01-01-2000';
        $this->dateTo = Carbon::now()->format('d-m-Y');


    	}


	protected $listeners =[
		'deleteCaja' => 'EliminarCaja'
	];



    	public function render()
    	{
            // O usar config()
            $this->app_url = config('app.url');
        
        	if(Auth::user()->comercio_id != 1)
            $comercio_id = Auth::user()->comercio_id;
            else
            $comercio_id = Auth::user()->id;
            
    	  if(Auth::user()->sucursal == 1){
    	  $this->es_sucursal = 1;    
    	  } else {
    	  $this->es_sucursal = 0;        
    	  }
    	  
    	  
    	  // CUENTA CORRIENTE CLIENTES 
    	  $cta_cte_clientes = $this->GetCtaCteClientes($comercio_id);
    	  
    	  // CUENTA CORRIENTE PROVEEDORES 
    	  $cta_cte_proveedores = $this->GetCtaCteProveedores($comercio_id);
    	  
    	  // EFECTIVO 
    	  
    	  //$efectivo_disponible = $this->GetEfectivoDisponible($comercio_id);
    	  
    	  $efectivo_disponible = $this->GetSaldo($comercio_id,1,1);

          // BANCOS -- ACREDITADO
          
          $bancos_disponible = $this->GetSaldo($comercio_id,2,1);

          // PLATAFORMAS  -- ACREDITADO
          
          $plataformas_disponible = $this->GetSaldo($comercio_id,3,1);
          
          
          // BANCOS -- PENDIENTE A COBRAR
          
          $bancos_pendiente = $this->GetSaldo($comercio_id,2,0);

          // BANCOS -- PENDIENTE A PAGAR

          //$bancos_pendiente_pagar = $this->GetBancosPendientePagar($comercio_id);
          
          $bancos_pendiente_pagar = $this->GetSaldo($comercio_id,2,0);
          
          // PLATAFORMAS -- PENDIENTE A COBRAR

          $plataformas_pendiente = $this->GetSaldo($comercio_id,3,0);

          // PLATAFORMAS -- PENDIENTE A PAGAR
          
          $plataformas_pendiente_pagar = $this->GetSaldo($comercio_id,3,0);

        
          //
          
    	return view('livewire.consolidado.component', [
    	    'bancos_pendiente_pagar' => $bancos_pendiente_pagar,
            'plataformas_pendiente_pagar' => $plataformas_pendiente_pagar,
    	    'cta_cte_clientes' => $cta_cte_clientes,
    	    'cta_cte_proveedores' => $cta_cte_proveedores,
            'efectivo_disponible' => $efectivo_disponible,
            'bancos_disponible' => $bancos_disponible,
            'bancos_pendiente' => $bancos_pendiente,
            'plataformas_pendiente' => $plataformas_pendiente,
            'plataformas_disponible' => $plataformas_disponible

    		])
    		->extends('layouts.theme-pos.app')
    		->section('content');

    	}

    	public function GetCtaCteClientes($comercio_id){
    	    
    	$datos_cta_cte = Sale::select(
          Sale::raw('SUM(sales.deuda) as total'))
		  ->where('sales.comercio_id', $comercio_id)
		  ->where('sales.status','<>', 'Cancelado')
		  ->where('sales.eliminado',0)
		  ->first()->total;
		  
      $saldos_iniciales = saldos_iniciales::select(
          Sale::raw('SUM(saldos_iniciales.monto) as saldo_inicial_cuenta_corriente'))
          ->where('saldos_iniciales.tipo','cliente')
          ->where('saldos_iniciales.eliminado',0)
          ->where('saldos_iniciales.comercio_id',$comercio_id)
          ->first()->saldo_inicial_cuenta_corriente;
      
        return $datos_cta_cte + $saldos_iniciales;
        

    	}
    	
    	// 14-8-2024
    	public function GetCtaCteProveedores($comercio_id){
        
        $compras_proveedores = compras_proveedores::
        select(
            compras_proveedores::raw('SUM(compras_proveedores.deuda) as total')
        )
        ->where('compras_proveedores.comercio_id', $comercio_id)
        ->where('compras_proveedores.eliminado',0)
        ->first()->total;
        
        $gastos_proveedores = gastos::
        select(
            gastos::raw('SUM(gastos.deuda) as total')
        )
        ->where('gastos.comercio_id', $comercio_id)
        ->where('gastos.eliminado',0)
        ->first()->total;

      $saldos_iniciales = saldos_iniciales::select(
          Sale::raw('SUM(saldos_iniciales.monto) as saldo_inicial_cuenta_corriente'))
          ->where('saldos_iniciales.tipo','proveedor')
          ->where('saldos_iniciales.eliminado',0)
          ->where('saldos_iniciales.comercio_id',$comercio_id)
          ->first()->saldo_inicial_cuenta_corriente;

        return $compras_proveedores + $gastos_proveedores + $saldos_iniciales;
        
    	}

public function GetSaldo($comercio_id, $tipo, $estado_pago) {
    // Consulta para obtener todos los bancos con sus saldos iniciales
    $bancos_query = bancos::select(
        'bancos.id',
        'bancos.nombre as banco',
        'bancos.saldo_inicial'
    )
    ->where('bancos.tipo', $tipo);
    if($tipo != 1) {
        $bancos_query = $bancos_query->where('bancos.comercio_id', $comercio_id);    
    } else {
        $bancos_query = $bancos_query->where('bancos.id', 1);    
    }
    
    $bancos = $bancos_query->get()->keyBy('id');
    
    // Extraer los IDs de los bancos
    $banco_ids = $bancos->keys();

    if ($banco_ids->isEmpty()) {
        // Si no hay bancos, retorna un arreglo vacío
        return collect();
    }
    
    // Consulta para obtener los pagos agrupados por banco
    $pagos_query = pagos_facturas::select(
        'pagos_facturas.banco_id',
        DB::raw('SUM(monto + iva_pago - deducciones) as total'),
        DB::raw('SUM(monto_compra) as total_compras'),
        DB::raw('SUM(monto_gasto) as total_gastos'),
        DB::raw('SUM(pagos_facturas.recargo + pagos_facturas.iva_recargo) as recargo'),
        DB::raw('SUM(monto_ingreso_retiro) as total_ingreso_retiro')
    )
    ->where('pagos_facturas.eliminado', 0);
    if($tipo == 1){
        $pagos_query = $pagos_query->where('pagos_facturas.comercio_id', $comercio_id);
    }
    $pagos_query = $pagos_query->whereIn('pagos_facturas.banco_id', $banco_ids)
    ->where('pagos_facturas.estado_pago', $estado_pago)
    ->groupBy('pagos_facturas.banco_id');
    
    $pagos = $pagos_query->get()->keyBy('banco_id');

    // Consulta para obtener los saldos iniciales agrupados por banco
    $pagos_saldo_inicial_query = saldos_iniciales::select(
        'saldos_iniciales.metodo_pago as banco_id',
        DB::raw('SUM(CASE WHEN saldos_iniciales.tipo = "cliente" THEN -1 * monto ELSE 0 END) as total'),
        DB::raw('SUM(CASE WHEN saldos_iniciales.tipo = "proveedor" THEN -1 * monto ELSE 0 END) as total_compras'),
        DB::raw('0 as total_gastos'),
        DB::raw('0 as recargo'),
        DB::raw('0 as total_ingreso_retiro')
    )
    ->where('saldos_iniciales.eliminado', 0);
    if($tipo == 1){
    $pagos_saldo_inicial_query = $pagos_saldo_inicial_query->where('saldos_iniciales.comercio_id', $comercio_id);
    }
    $pagos_saldo_inicial_query = $pagos_saldo_inicial_query->whereIn('saldos_iniciales.metodo_pago', $banco_ids)
    ->where('saldos_iniciales.estado_pago', $estado_pago)
    ->groupBy('saldos_iniciales.metodo_pago')
    ->get()->keyBy('banco_id');
    
    // Consulta para obtener los movimientos agrupados por banco
    $pagos_movimientos = movimiento_dinero_cuentas_detalle::select(
        'movimiento_dinero_cuentas_detalles.banco_id as banco_id',
        DB::raw('SUM(movimiento_dinero_cuentas_detalles.monto) as total'),
        DB::raw('0 as total_compras'),
        DB::raw('0 as total_gastos'),
        DB::raw('0 as recargo'),
        DB::raw('0 as total_ingreso_retiro')
    )
    ->where('movimiento_dinero_cuentas_detalles.eliminado', 0)
    ->where('movimiento_dinero_cuentas_detalles.banco_id', $banco_ids);
    if($tipo == 1){
    $pagos_movimientos = $pagos_movimientos->where('movimiento_dinero_cuentas_detalles.comercio_id', $comercio_id);
    }
    $pagos_movimientos = $pagos_movimientos->where('movimiento_dinero_cuentas_detalles.estado_pago', $estado_pago)
    ->groupBy('movimiento_dinero_cuentas_detalles.banco_id')
    ->get()->keyBy('banco_id');

    // Combina los resultados
    foreach ($bancos as $banco) {
        $banco_id = $banco->id;

        // Agrega los pagos al banco si existen
        if (isset($pagos[$banco_id])) {
            $banco->total = $pagos[$banco_id]->total;
            $banco->total_compras = $pagos[$banco_id]->total_compras;
            $banco->total_gastos = $pagos[$banco_id]->total_gastos;
            $banco->recargo = $pagos[$banco_id]->recargo;
            $banco->total_ingreso_retiro = $pagos[$banco_id]->total_ingreso_retiro;
        } else {
            $banco->total = 0;
            $banco->total_compras = 0;
            $banco->total_gastos = 0;
            $banco->recargo = 0;
            $banco->total_ingreso_retiro = 0;
        }

        // Agrega los saldos iniciales al banco si existen
        if (isset($pagos_saldo_inicial_query[$banco_id])) {
            $banco->total += $pagos_saldo_inicial_query[$banco_id]->total;
            $banco->total_compras += $pagos_saldo_inicial_query[$banco_id]->total_compras;
            // Los demás campos (total_gastos, recargo, total_ingreso_retiro) ya son 0 en saldos_iniciales
        }

        // Agrega los movimientos al banco si existen
        if (isset($pagos_movimientos[$banco_id])) {
            $banco->total += $pagos_movimientos[$banco_id]->total;
        }
    }

    return $bancos->values();
}

        public function GetSaldoOld($comercio_id,$tipo,$estado_pago) {
            // Consulta para obtener todos los bancos con sus saldos iniciales
            $bancos_query = bancos::select(
                'bancos.id',
                'bancos.nombre as banco',
                'bancos.saldo_inicial'
            )
            ->where('bancos.tipo', $tipo);
            if($tipo != 1){
            $bancos_query = $bancos_query->where('bancos.comercio_id', $comercio_id);    
            }
            
            
            $bancos = $bancos_query->get()->keyBy('id');
            
            // Consulta para obtener los pagos agrupados por banco
            $pagos_query = pagos_facturas::select(
                'pagos_facturas.banco_id',
                DB::raw('SUM(monto + iva_pago - deducciones) as total'),
                DB::raw('SUM(monto_compra) as total_compras'),
                DB::raw('SUM(monto_gasto) as total_gastos'),
                DB::raw('SUM(pagos_facturas.recargo + pagos_facturas.iva_recargo) as recargo'),
                DB::raw('SUM(monto_ingreso_retiro) as total_ingreso_retiro')
            )
            ->where('pagos_facturas.eliminado', 0)
            ->where('pagos_facturas.comercio_id', $comercio_id)
            ->where('pagos_facturas.estado_pago', $estado_pago)
            ->groupBy('pagos_facturas.banco_id');
            
            $pagos = $pagos_query->get()->keyBy('banco_id');
        
            // Consulta para obtener los saldos iniciales agrupados por banco
            $pagos_saldo_inicial_query = saldos_iniciales::select(
                'saldos_iniciales.metodo_pago as banco_id',
                DB::raw('SUM(CASE WHEN saldos_iniciales.tipo = "cliente" THEN -1 * monto ELSE 0 END) as total'),
                DB::raw('SUM(CASE WHEN saldos_iniciales.tipo = "proveedor" THEN -1 * monto ELSE 0 END) as total_compras'),
                DB::raw('0 as total_gastos'),
                DB::raw('0 as recargo'),
                DB::raw('0 as total_ingreso_retiro')
            )
            ->where('saldos_iniciales.eliminado', 0)
            ->where('saldos_iniciales.comercio_id', $comercio_id)
            ->where('saldos_iniciales.estado_pago', $estado_pago)
            ->groupBy('saldos_iniciales.metodo_pago')
            ->get()->keyBy('banco_id');
            
            // Consulta para obtener los movimientos agrupados por banco
            
            $pagos_movimientos = movimiento_dinero_cuentas_detalle::select(
                'movimiento_dinero_cuentas_detalles.banco_id as banco_id',
                DB::raw('SUM(movimiento_dinero_cuentas_detalles.monto) as total'),
                DB::raw('0 as total_compras'),
                DB::raw('0 as total_gastos'),
                DB::raw('0 as recargo'),
                DB::raw('0 as total_ingreso_retiro')
            )
            ->where('movimiento_dinero_cuentas_detalles.eliminado', 0)
            ->where('movimiento_dinero_cuentas_detalles.comercio_id', $comercio_id)
            ->groupBy('movimiento_dinero_cuentas_detalles.banco_id')
            ->get()->keyBy('banco_id');
        
            // Combina los resultados
            foreach ($bancos as $banco) {
                $banco_id = $banco->id;
        
                // Agrega los pagos al banco si existen
                if (isset($pagos[$banco_id])) {
                    $banco->total = $pagos[$banco_id]->total;
                    $banco->total_compras = $pagos[$banco_id]->total_compras;
                    $banco->total_gastos = $pagos[$banco_id]->total_gastos;
                    $banco->recargo = $pagos[$banco_id]->recargo;
                    $banco->total_ingreso_retiro = $pagos[$banco_id]->total_ingreso_retiro;
                } else {
                    $banco->total = 0;
                    $banco->total_compras = 0;
                    $banco->total_gastos = 0;
                    $banco->recargo = 0;
                    $banco->total_ingreso_retiro = 0;
                }
        
                // Agrega los saldos iniciales al banco si existen
                if (isset($pagos_saldo_inicial_query[$banco_id])) {
                    $banco->total += $pagos_saldo_inicial_query[$banco_id]->total;
                    $banco->total_compras += $pagos_saldo_inicial_query[$banco_id]->total_compras;
                    // Los demás campos (total_gastos, recargo, total_ingreso_retiro) ya son 0 en saldos_iniciales
                }
            }
        
            return $bancos->values();
        }

/*
    	
    	
    	public function GetEfectivoDisponible($comercio_id){
          $efectivo_disponible = pagos_facturas::join('bancos','bancos.id','pagos_facturas.banco_id')
          ->select('bancos.id','bancos.nombre as banco',
          pagos_facturas::raw('SUM(monto) as total'),
          pagos_facturas::raw('SUM(monto_compra) as total_compras'),
          pagos_facturas::raw('SUM(monto_gasto) as total_gastos'),
          pagos_facturas::raw('SUM(pagos_facturas.recargo) as recargo'),
          pagos_facturas::raw('SUM(monto_ingreso_retiro) as total_ingreso_retiro')
          )
          ->where('pagos_facturas.eliminado',0)
          ->where('bancos.tipo',1)
          ->where('pagos_facturas.comercio_id',$comercio_id)
          ->where('pagos_facturas.estado_pago',1)
          ->groupBy('bancos.nombre','bancos.id')
          ->get(); 
        
        return $efectivo_disponible;   	    
    	}
    	
    	public function GetBancosPendiente($comercio_id){
    	              
          $bancos_pendiente = pagos_facturas::join('bancos','bancos.id','pagos_facturas.banco_id')
          ->select('bancos.id','bancos.nombre as banco',pagos_facturas::raw('SUM(monto) as total'),pagos_facturas::raw('SUM(pagos_facturas.recargo) as recargo'),pagos_facturas::raw('SUM(monto_ingreso_retiro) as total_ingreso_retiro'))
          ->where('pagos_facturas.eliminado',0)
          ->where('bancos.tipo',2)
          ->where('pagos_facturas.comercio_id',$comercio_id)
          ->where('pagos_facturas.estado_pago',0)
          ->groupBy('bancos.nombre','bancos.id')
          ->get();
          
          return $bancos_pendiente;
    	}
    	
    	public function GetBancosPendientePagar($comercio_id){
          $bancos_pendiente_pagar = pagos_facturas::join('bancos','bancos.id','pagos_facturas.banco_id')
          ->select('bancos.id','bancos.nombre as banco',pagos_facturas::raw('SUM(monto_compra) as total_compra'),pagos_facturas::raw('SUM(monto_gasto) as total_gasto'))
          ->where('pagos_facturas.eliminado',0)
          ->where('bancos.tipo',2)
          ->where('pagos_facturas.comercio_id',$comercio_id)
          ->where('pagos_facturas.estado_pago',0)
          ->groupBy('bancos.nombre','bancos.id')
          ->get();
          
          return $bancos_pendiente_pagar;
    	}
          
        public function GetPlataformasPendiente($comercio_id){
          $plataformas_pendiente = pagos_facturas::join('bancos','bancos.id','pagos_facturas.banco_id')
          ->select('bancos.id','bancos.nombre as banco',pagos_facturas::raw('SUM(monto) as total'),pagos_facturas::raw('SUM(pagos_facturas.recargo) as recargo'),pagos_facturas::raw('SUM(monto_ingreso_retiro) as total_ingreso_retiro'))
          ->where('pagos_facturas.eliminado',0)
          ->where('bancos.tipo',3)
          ->where('pagos_facturas.comercio_id',$comercio_id)
          ->where('pagos_facturas.estado_pago',0)
          ->groupBy('bancos.nombre','bancos.id')
          ->get();
          
          return $plataformas_pendiente;
        }
          
        public function GetPlataformasPendientePagar($comercio_id){
                  
                  $plataformas_pendiente_pagar = pagos_facturas::join('bancos','bancos.id','pagos_facturas.banco_id')
                  ->select('bancos.id','bancos.nombre as banco',pagos_facturas::raw('SUM(monto_compra) as total_compra'),pagos_facturas::raw('SUM(monto_gasto) as total_gasto'))
                  ->where('pagos_facturas.eliminado',0)
                  ->where('bancos.tipo',3)
                  ->where('pagos_facturas.comercio_id',$comercio_id)
                  ->where('pagos_facturas.estado_pago',0)
                  ->groupBy('bancos.nombre','bancos.id')
                  ->get();
                  
                  return $plataformas_pendiente_pagar;
        }

*/

}
