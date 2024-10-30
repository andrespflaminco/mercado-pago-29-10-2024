<?php
namespace App\Traits;


// Trait


// Modelos


// services 

use App\Services\CartVariaciones;

// Otros

use Illuminate\Support\Facades\Storage;
use Notification;
use App\Notifications\NotificarCambios;
use Illuminate\Validation\Rule;
use DB;
use Intervention\Image\Facades\Image;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use Carbon\Carbon;

use App\Models\bancos;
use App\Models\forma_pagos;
use App\Models\pagos_facturas;
use App\Models\cajas;


trait CajasTrait {

public function GetResumenCaja($caja_id)
{
      // 26-6-2024
      $this->detalle_nro_caja = cajas::select('cajas.id','cajas.nro_caja','cajas.fecha_inicio','cajas.estado','cajas.fecha_cierre','users.name as nombre_usuario')
      ->join('users','users.id','cajas.user_id')
      ->where('cajas.id', 'like', $caja_id)
      ->get();

      // EFECTIVO 
        
      $this->GetEfectivoCaja($caja_id);
    
      // BANCOS
      
      $this->GetBancosCaja($caja_id);
      
      // PLATAFORMAS DE PAGO 

      $this->GetPlataformasCaja($caja_id);

      $this->total_ventas_totales = $this->total_ventas_efectivo + $this->total_ventas_plataformas + $this->total_ventas_bancos;

}
    
public function GetEfectivoCaja($caja_id) {
    
      $this->details_efectivo = cajas::join('pagos_facturas','pagos_facturas.caja','cajas.id')
      ->join('bancos','bancos.id','pagos_facturas.banco_id')
      ->select('cajas.estado','cajas.monto_inicial','cajas.monto_final',pagos_facturas::raw('SUM(monto) as total'),pagos_facturas::raw('SUM(monto_gasto) as total_gasto'),pagos_facturas::raw('SUM(monto_compra) as total_compras'),pagos_facturas::raw('SUM(pagos_facturas.recargo) as recargo'),pagos_facturas::raw('SUM(pagos_facturas.iva_pago) as iva_pago'),pagos_facturas::raw('SUM(pagos_facturas.iva_recargo) as iva_recargo'))
      ->where('pagos_facturas.caja', $caja_id)
      ->where('bancos.id',1)
       ->where('pagos_facturas.eliminado',0)
      ->groupBy('cajas.estado','cajas.monto_inicial','cajas.monto_final')
      ->get();
      
      $this->ingresos_efectivo = pagos_facturas::join('ingresos_retiros','ingresos_retiros.id','pagos_facturas.id_ingresos_retiros')
      ->select('pagos_facturas.caja','ingresos_retiros.tipo',pagos_facturas::raw('SUM(pagos_facturas.monto_ingreso_retiro) as total'))
      ->where('id_ingresos_retiros','<>',null)
      ->where('pagos_facturas.caja', $caja_id)
      ->where('ingresos_retiros.tipo',"Ingreso")
      ->where('pagos_facturas.banco_id',1)
      ->where('pagos_facturas.eliminado',0)
      ->groupBy('pagos_facturas.caja','ingresos_retiros.tipo')
      ->get();
      
        $this->retiros_efectivo = pagos_facturas::join('ingresos_retiros','ingresos_retiros.id','pagos_facturas.id_ingresos_retiros')
      ->select('pagos_facturas.caja','ingresos_retiros.tipo',pagos_facturas::raw('SUM(pagos_facturas.monto_ingreso_retiro) as total'))
      ->where('id_ingresos_retiros','<>',null)
      ->where('pagos_facturas.caja', $caja_id)
      ->where('ingresos_retiros.tipo',"Retiro")
      ->where('pagos_facturas.banco_id',1)
      ->where('pagos_facturas.eliminado',0)
      ->groupBy('pagos_facturas.caja','ingresos_retiros.tipo')
      ->get();

      $this->caja = cajas::find($caja_id);
      
      // Ingresos y retiros 

      $this->total_ingresos_efectivo = $this->ingresos_efectivo->sum('total');
      $this->total_retiros_efectivo = -1*$this->retiros_efectivo->sum('total');
      
      // Efectivo inicial y final
      
      $this->total_efectivo_inicial = $this->caja->monto_inicial;

      $this->total_efectivo_final = $this->caja->monto_final;

      $this->count_efectivo = $this->details_efectivo->count('total');
      
            
      // Compras y Gastos 

      $this->total_compras_efectivo = $this->details_efectivo->sum('total_compras');
      $this->total_gastos_efectivo = $this->details_efectivo->sum('total_gasto');
      
      // Efectivo vendido 
      $this->total_ventas_efectivo = $this->details_efectivo->sum('total') + $this->details_efectivo->sum('recargo') + $this->details_efectivo->sum('iva_pago') + $this->details_efectivo->sum('iva_recargo');

      $this->total_efectivo = $this->total_ventas_efectivo - $this->total_compras_efectivo - $this->total_gastos_efectivo  + $this->total_ingresos_efectivo - $this->total_retiros_efectivo + $this->total_efectivo_inicial - $this->total_efectivo_final;
 

}

public function GetBancosCaja($caja_id) {
    
      // VENTAS, COMPRAS, GASTOS, INGRESOS Y RETIROS DE BANCOS 

      $this->listado_bancos = bancos::join('pagos_facturas','pagos_facturas.banco_id','bancos.id')
      ->select('bancos.id','bancos.nombre',bancos::raw('COUNT(pagos_facturas.id) AS count'))
      ->where('bancos.tipo',2)
      ->where('pagos_facturas.eliminado',0)
      ->where('pagos_facturas.caja', 'like', $caja_id)
      ->where('pagos_facturas.eliminado',0)
      ->groupBy('bancos.id','bancos.nombre')
      ->get();
      
    //  dd($this->listado_bancos);

      $this->details_bancos = pagos_facturas::join('metodo_pagos','metodo_pagos.id','pagos_facturas.metodo_pago')
      ->join('cajas','cajas.id','pagos_facturas.caja')
      ->join('bancos','bancos.id','metodo_pagos.cuenta')
      ->select('bancos.nombre as banco','pagos_facturas.banco_id','metodo_pagos.nombre as metodo_pago',pagos_facturas::raw('SUM(monto) as total'),pagos_facturas::raw('SUM(pagos_facturas.recargo) as recargo'),pagos_facturas::raw('SUM(pagos_facturas.iva_recargo) as iva_recargo'),pagos_facturas::raw('SUM(pagos_facturas.iva_pago) as iva_pago'),pagos_facturas::raw('SUM(pagos_facturas.monto + pagos_facturas.recargo + pagos_facturas.iva_pago + pagos_facturas.iva_recargo ) as total_banco'))
      ->where('pagos_facturas.caja', 'like', $caja_id)
      ->where('pagos_facturas.id_factura','<>',null)
      ->where('pagos_facturas.eliminado',0)
      ->where('metodo_pagos.categoria',2)
      ->groupBy('bancos.nombre','pagos_facturas.banco_id','metodo_pagos.nombre')
      ->get();
      
      
       $this->compras_bancos = pagos_facturas::join('cajas','cajas.id','pagos_facturas.caja')
      ->join('bancos','bancos.id','pagos_facturas.banco_id')
      ->select('bancos.id as banco_id','bancos.nombre as banco',pagos_facturas::raw('SUM(monto_compra) as total'))
      ->where('pagos_facturas.caja', 'like', $caja_id)
      ->where('pagos_facturas.eliminado',0)
      ->where('pagos_facturas.monto_compra','<>',null)
      ->where('bancos.tipo',2)
      ->groupBy('bancos.id','bancos.nombre')
      ->get();
      
      
      $this->gastos_bancos = pagos_facturas::join('cajas','cajas.id','pagos_facturas.caja')
      ->join('bancos','bancos.id','pagos_facturas.banco_id')
      ->select('bancos.id as banco_id','bancos.nombre as banco',pagos_facturas::raw('SUM(monto_gasto) as total'))
      ->where('pagos_facturas.caja', 'like', $caja_id)
      ->where('pagos_facturas.id_gasto','<>',0)
      ->where('pagos_facturas.eliminado',0)
      ->where('bancos.tipo',2)
      ->groupBy('bancos.id','bancos.nombre')
      ->get();
      
      // dd($this->gastos_bancos);

      $this->ingresos_bancos = pagos_facturas::join('ingresos_retiros','ingresos_retiros.id','pagos_facturas.id_ingresos_retiros')
      ->select('pagos_facturas.id','pagos_facturas.caja','pagos_facturas.caja','pagos_facturas.banco_id','ingresos_retiros.tipo',pagos_facturas::raw('SUM(pagos_facturas.monto_ingreso_retiro) as total'))
      ->where('id_ingresos_retiros','<>',null)
      ->where('pagos_facturas.caja', 'like', $caja_id)
      ->where('ingresos_retiros.tipo',"Ingreso")
      ->where('ingresos_retiros.categoria',2)
      ->where('pagos_facturas.eliminado',0)
      ->groupBy('pagos_facturas.id','pagos_facturas.caja','pagos_facturas.banco_id','ingresos_retiros.tipo')
      ->get();
      
      $this->retiros_bancos = pagos_facturas::join('ingresos_retiros','ingresos_retiros.id','pagos_facturas.id_ingresos_retiros')
      ->select('pagos_facturas.id','pagos_facturas.caja','pagos_facturas.banco_id','ingresos_retiros.tipo',pagos_facturas::raw('SUM(pagos_facturas.monto_ingreso_retiro) as total'))
      ->where('id_ingresos_retiros','<>',null)
      ->where('pagos_facturas.caja', 'like', $caja_id)
      ->where('ingresos_retiros.tipo',"Retiro")
      ->where('ingresos_retiros.categoria',2)
      ->where('pagos_facturas.eliminado',0)
      ->groupBy('pagos_facturas.id','pagos_facturas.caja','pagos_facturas.banco_id','ingresos_retiros.tipo')
      ->get();
      
      $this->totales_bancos = pagos_facturas::join('bancos','bancos.id','pagos_facturas.banco_id')
      ->select('pagos_facturas.caja','pagos_facturas.banco_id',pagos_facturas::raw('( IFNULL(SUM(monto), 0) + IFNULL(SUM(recargo), 0) + IFNULL(SUM(iva_pago), 0) + IFNULL(SUM(iva_recargo), 0) + IFNULL(SUM(pagos_facturas.monto_ingreso_retiro), 0)  - IFNULL(SUM(monto_compra), 0)  - IFNULL(SUM(monto_gasto), 0) ) as total'))
      ->where('pagos_facturas.caja', 'like', $caja_id)
      ->where('bancos.tipo',2)
      ->where('pagos_facturas.eliminado',0)
      ->groupBy('pagos_facturas.caja','pagos_facturas.banco_id')
      ->get();
      
      //dd($this->totales_bancos);

      $this->count_bancos = $this->details_bancos->count('total');
      $this->total_bancos = $this->details_bancos->sum('total') + $this->details_bancos->sum('recargo') + $this->details_bancos->sum('iva_pago') + $this->details_bancos->sum('iva_recargo')  - $this->compras_bancos->sum('total') - $this->gastos_bancos->sum('total') + $this->retiros_bancos->sum('total') + $this->ingresos_bancos->sum('total');
      $this->total_ventas_bancos = $this->details_bancos->sum('total') + $this->details_bancos->sum('recargo') + $this->details_bancos->sum('iva_pago') + $this->details_bancos->sum('iva_recargo');
      //dd($this->totales_bancos);

}

public function GetPlataformasCaja($caja_id){
    
      $this->listado_plataformas = bancos::join('pagos_facturas','pagos_facturas.banco_id','bancos.id')
      ->select('bancos.id','bancos.nombre',bancos::raw('COUNT(pagos_facturas.id) AS count'))
      ->where('bancos.tipo',3)
      ->where('pagos_facturas.eliminado',0)
      ->where('pagos_facturas.caja', 'like', $caja_id)
      ->where('pagos_facturas.eliminado',0)
      ->groupBy('bancos.id','bancos.nombre')
      ->get();

      $this->details_plataformas = pagos_facturas::join('metodo_pagos','metodo_pagos.id','pagos_facturas.metodo_pago')
      ->join('cajas','cajas.id','pagos_facturas.caja')
      ->join('bancos','bancos.id','metodo_pagos.cuenta')
      ->select('bancos.nombre as banco','pagos_facturas.banco_id','metodo_pagos.nombre as metodo_pago',pagos_facturas::raw('SUM(monto) as total'),pagos_facturas::raw('SUM(pagos_facturas.recargo) as recargo'),pagos_facturas::raw('SUM(pagos_facturas.iva_recargo) as iva_recargo'),pagos_facturas::raw('SUM(pagos_facturas.iva_pago) as iva_pago'),pagos_facturas::raw('SUM(pagos_facturas.monto + pagos_facturas.recargo + pagos_facturas.iva_recargo + pagos_facturas.iva_pago) as total_plataforma'))
      ->where('pagos_facturas.caja', 'like', $caja_id)
      ->where('pagos_facturas.id_factura','<>',null)
      ->where('pagos_facturas.eliminado',0)
      ->where('metodo_pagos.categoria',3)
      ->groupBy('bancos.nombre','pagos_facturas.banco_id','metodo_pagos.nombre')
      ->get();
      
      
       $this->compras_plataformas = pagos_facturas::join('cajas','cajas.id','pagos_facturas.caja')
      ->join('bancos','bancos.id','pagos_facturas.banco_id')
      ->select('bancos.id as banco_id','bancos.nombre as banco',pagos_facturas::raw('SUM(monto_compra) as total'))
      ->where('pagos_facturas.caja', 'like', $caja_id)
      ->where('pagos_facturas.eliminado',0)
      ->where('pagos_facturas.monto_compra','<>',null)
      ->where('bancos.tipo',3)
      ->groupBy('bancos.id','bancos.nombre')
      ->get();
      
      $this->gastos_plataformas = pagos_facturas::join('cajas','cajas.id','pagos_facturas.caja')
      ->join('bancos','bancos.id','pagos_facturas.banco_id')
      ->select('bancos.id as banco_id','bancos.nombre as banco',pagos_facturas::raw('SUM(monto_gasto) as total'))
      ->where('pagos_facturas.caja', 'like', $caja_id)
      ->where('pagos_facturas.id_gasto','<>',0)
      ->where('pagos_facturas.eliminado',0)
      ->where('bancos.tipo',3)
      ->groupBy('bancos.id','bancos.nombre')
      ->get();
         

      $this->ingresos_plataformas = pagos_facturas::join('ingresos_retiros','ingresos_retiros.id','pagos_facturas.id_ingresos_retiros')
      ->select('pagos_facturas.id','pagos_facturas.caja','pagos_facturas.caja','pagos_facturas.banco_id','ingresos_retiros.tipo',pagos_facturas::raw('SUM(pagos_facturas.monto_ingreso_retiro) as total'))
      ->where('id_ingresos_retiros','<>',null)
      ->where('pagos_facturas.caja', 'like', $caja_id)
      ->where('ingresos_retiros.tipo',"Ingreso")
      ->where('ingresos_retiros.categoria',3)
      ->where('pagos_facturas.eliminado',0)
      ->groupBy('pagos_facturas.id','pagos_facturas.caja','pagos_facturas.banco_id','ingresos_retiros.tipo')
      ->get();
      
      $this->retiros_plataformas = pagos_facturas::join('ingresos_retiros','ingresos_retiros.id','pagos_facturas.id_ingresos_retiros')
      ->select('pagos_facturas.id','pagos_facturas.caja','pagos_facturas.banco_id','ingresos_retiros.tipo',pagos_facturas::raw('SUM(pagos_facturas.monto_ingreso_retiro) as total'))
      ->where('id_ingresos_retiros','<>',null)
      ->where('pagos_facturas.caja', 'like', $caja_id)
      ->where('ingresos_retiros.tipo',"Retiro")
      ->where('ingresos_retiros.categoria',3)
      ->where('pagos_facturas.eliminado',0)
      ->groupBy('pagos_facturas.id','pagos_facturas.caja','pagos_facturas.banco_id','ingresos_retiros.tipo')
      ->get();
      
      $this->totales_plataformas = pagos_facturas::join('bancos','bancos.id','pagos_facturas.banco_id')
      ->select('pagos_facturas.caja','pagos_facturas.banco_id',pagos_facturas::raw('( IFNULL(SUM(monto), 0) + IFNULL(SUM(recargo), 0)  + IFNULL(SUM(iva_pago), 0) + IFNULL(SUM(iva_recargo), 0) + IFNULL(SUM(pagos_facturas.monto_ingreso_retiro), 0)  - IFNULL(SUM(monto_compra), 0)  - IFNULL(SUM(monto_gasto), 0) ) as total'))
      ->where('pagos_facturas.caja', 'like', $caja_id)
      ->where('bancos.tipo',3)
      ->where('pagos_facturas.eliminado',0)
      ->groupBy('pagos_facturas.caja','pagos_facturas.banco_id')
      ->get();

      $this->count_plataformas = $this->details_plataformas->count('total');

      $this->total_plataformas = $this->details_plataformas->sum('total') - $this->compras_plataformas->sum('total') - $this->gastos_plataformas->sum('total') + $this->retiros_plataformas->sum('total') + $this->ingresos_plataformas->sum('total')  + $this->details_plataformas->sum('recargo') + $this->details_plataformas->sum('iva_pago') + $this->details_plataformas->sum('iva_recargo');
    
    $this->total_ventas_plataformas = $this->details_plataformas->sum('total') + $this->details_plataformas->sum('recargo') + $this->details_plataformas->sum('iva_recargo') + $this->details_plataformas->sum('iva_pago');
    
}

}
