<?php
namespace App\Traits;


// Trait


// Modelos
use App\Models\metodo_pago_deducciones;
use App\Models\pagos_facturas;
use App\Models\detalle_deducciones;

use Illuminate\Support\Facades\Storage;
use Notification;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait DeduccionesTrait {
  
  // 30-6-2024
  
  public $deducciones = [];
  public $mostrarDeducciones = false;

  public function toggleMostrarDeducciones()
  {
     $this->mostrarDeducciones = !$this->mostrarDeducciones;
  }
    
  public function removeDeduccion($index)
  {
       unset($this->deducciones[$index]);
       $this->deducciones = array_values($this->deducciones); // Reindexar el array
  }
  
    public function AgregarDeduccion()
    {
        $this->deducciones[] = [
            'id' => 0,
            'concepto' => '',
            'porcentaje' => '',
            'monto' => '',
        ];
    }
    
  public function RecalcularDeduccionesMetodoPago($pago_id,$metodo_pago_id,$monto_total){
        
        
        $this->deducciones = metodo_pago_deducciones::where('metodo_id', $metodo_pago_id)->where('eliminado',0)
            ->get()
            ->map(function ($deduccion) use ($monto_total) {
                
                return [
                    'id' => $deduccion->id,
                    'concepto' => $deduccion->nombre,
                    'porcentaje' => $deduccion->deduccion,
                    'monto' => $monto_total * ($deduccion->deduccion/100)
                ];
            })
            ->toArray();

            
  }
  
  public function RecalcularDeduccionesMonto($pago_id,$monto_total){
        
        
        // aca tengo que ver si se cambia que % tiene de deduccion el metodo de pago y aplicar eso 
        if(0 < $pago_id){
        $this->deducciones = detalle_deducciones::where('pago_id', $pago_id)->where('eliminado',0)
            ->get()
            ->map(function ($deduccion) use ($monto_total)  {
                return [
                    'id' => $deduccion->deduccion_id,
                    'concepto' => $deduccion->concepto,
                    'porcentaje' => $deduccion->porcentaje,
                    'monto' => $monto_total * ($deduccion->porcentaje/100),
                ];
            })
            ->toArray();            
        }

        // dd($this->deducciones,$monto_total);   
  }
  
      
    public function recalcularMontoDeduccion($index)
    {
        $porcentaje = str_replace(',', '.', $this->deducciones[$index]['porcentaje']);
        $numero = $this->total_pago * ($porcentaje / 100);
        $this->deducciones[$index]['monto'] = number_format($numero,2,",",".");
    }

    public function recalcularPorcentajeDeduccion($index)
    {
        $monto = str_replace(',', '.', $this->deducciones[$index]['monto']);
        $numero = ($monto / $this->total_pago) * 100;
        $this->deducciones[$index]['porcentaje'] = number_format($numero,2,",",".");
    }
  public function VerDeducciones($pago){
    
        $this->deducciones = detalle_deducciones::where('pago_id', $pago->id)->where('eliminado',0)
            ->get()
            ->map(function ($deduccion) {
                return [
                    'id' => $deduccion->deduccion_id,
                    'concepto' => $deduccion->concepto,
                    'porcentaje' => number_format($deduccion->porcentaje,2,",","."),
                    'monto' => number_format($deduccion->monto,2,",","."),
                ];
            })
            ->toArray();
        
    //    dd($this->deducciones);    
  }
  
  
  public function guardarDeducciones($pago)
  {
      $monto_total = 0;
        foreach ($this->deducciones as $deduccion) {
        // Convertir el valor de monto a un formato numérico
        $monto = $deduccion['monto']; // Eliminamos los puntos de separación de miles
        $monto = str_replace('.', '', $monto); // Eliminamos los puntos de separación de miles
        $monto = str_replace('.', ',', $monto); // Reemplazamos comas por puntos para el separador decimal
        $monto = floatval($monto); // Convertimos a flotante
        
       if($deduccion['id'] == 0){
            detalle_deducciones::create([
                    'comercio_id' => $pago->comercio_id,
                    'pago_id' => $pago->id,
                    'monto' => $monto,
                    'concepto' => $deduccion['concepto'],
                    'porcentaje' => $deduccion['porcentaje'],
                    'eliminado' => $deduccion['eliminado'] ?? false,                
                ]);
       } else {
            detalle_deducciones::updateOrCreate(
                [
                    'deduccion_id' => $deduccion['id'],
                    'comercio_id' => $pago->comercio_id,
                    'pago_id' => $pago->id,
                ],
                [
                    'monto' => $monto,
                    'concepto' => $deduccion['concepto'],
                    'porcentaje' => $deduccion['porcentaje'],
                    'eliminado' => $deduccion['eliminado'] ?? false,
                ]
            );
       }
           
            
        $monto_total += $monto; 
        }
    

    $pf = pagos_facturas::find($pago->id);
    $pf->update([
     'deducciones' => $monto_total
    ]);
    
    $this->deducciones = [];
  }
    
    
  public function SetComisionesPagos($metodoPago,$array_pagos,$estado_pago,$comercio_id,$pago,$sale){
    $existingDeducciones = metodo_pago_deducciones::where('metodo_id', $metodoPago)->where('eliminado',0)->get();
    
    $monto_total = 0;
    foreach($existingDeducciones as $ed){
    $monto = ($array_pagos['monto'] + $array_pagos['recargo'] + $array_pagos['iva_pago'] + $array_pagos['iva_recargo']) * ($ed->deduccion/100);  
    
    $array_deducciones = [
    'deduccion_id' => $ed->id,
    'comercio_id' => $comercio_id,
    'pago_id' => $pago,
    'monto' => $monto,
    'concepto' => $ed->nombre,
    'porcentaje' => $ed->deduccion,
    'eliminado' => 0
    ];
    
	// $pago;
	//$gasto = gastos::create($array_deducciones);
    $gasto_id = DB::table('detalle_deducciones')->insertGetId($array_deducciones);
    $monto_total += $monto; 
    }
    
    $pf = pagos_facturas::find($pago);
    $pf->deducciones = $monto_total;
    $pf->save();
    
  }
  
  

}
