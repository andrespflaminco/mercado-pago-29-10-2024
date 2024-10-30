<?php

namespace App\Exports;


use App\Models\saldos_iniciales;
use App\Models\proveedores;
use App\Models\Sale;
use App\Models\compras_proveedores;
use App\Models\ClientesMostrador;
use Illuminate\Support\Facades\Auth;


use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas
use Carbon\Carbon;


class CtaCteProveedoresExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{

    protected $search;

    function __construct($search) {
        $this->search = $search;
    }




    public function GetDataCtaCte($comercio_id) {
        
    // Consulta para obtener todos los proveedores con sus datos
    $proveedores_query = proveedores::select(
        'proveedores.id',
        'proveedores.id_proveedor',
        'proveedores.nombre as nombre_proveedor'
    )
    ->where('proveedores.comercio_id', $comercio_id);
    
    // Aplica el filtro de búsqueda si existe
    if ($this->search) {
        $proveedores_query = $proveedores_query->where('proveedores.nombre', 'like', '%' . $this->search . '%');
    }
    
    $proveedores = $proveedores_query->get();
    
    // Consulta para obtener las deudas agrupadas por proveedor
    $deudas_query = compras_proveedores::select(
        'compras_proveedores.proveedor_id',
        compras_proveedores::raw('SUM(CASE WHEN compras_proveedores.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN compras_proveedores.deuda ELSE 0 END) as deuda_30_dias'),
        compras_proveedores::raw('SUM(CASE WHEN compras_proveedores.created_at >= DATE_SUB(NOW(), INTERVAL 61 DAY) AND compras_proveedores.created_at < DATE_SUB(NOW(), INTERVAL 31 DAY) THEN compras_proveedores.deuda ELSE 0 END) as deuda_60_dias'),
        compras_proveedores::raw('SUM(CASE WHEN compras_proveedores.created_at < DATE_SUB(NOW(), INTERVAL 61 DAY) THEN compras_proveedores.deuda ELSE 0 END) as deuda_90_dias'),
        compras_proveedores::raw('SUM(compras_proveedores.deuda) as total')
    )
    ->where('compras_proveedores.eliminado', 0)
    ->groupBy('compras_proveedores.proveedor_id');
    
    $deudas = $deudas_query->get()->keyBy('proveedor_id');
    
    // Consulta para obtener los saldos iniciales agrupados por proveedor
    $saldos_iniciales = saldos_iniciales::select(
        Sale::raw('SUM(saldos_iniciales.monto) as saldo_inicial_cuenta_corriente'),
        'referencia_id as proveedor_id'
    )
    ->where('saldos_iniciales.tipo', 'proveedor')
    ->where('saldos_iniciales.eliminado', 0)
    ->where('saldos_iniciales.comercio_id', $comercio_id)
    ->groupBy('referencia_id')
    ->get()
    ->keyBy('proveedor_id');
    
    
    // Combina los resultados
    foreach ($proveedores as $proveedor) {
        $proveedor_id = $proveedor->id;
    
        // Agrega las deudas al proveedor si existen
        if (isset($deudas[$proveedor_id])) {
            
             if (isset($saldos_iniciales[$proveedor_id])) {
                 $saldo_inicial = $saldos_iniciales[$proveedor_id]->saldo_inicial_cuenta_corriente;
             } else {
                 $saldo_inicial = 0;
             }
             
            $proveedor->deuda_30_dias = $deudas[$proveedor_id]->deuda_30_dias;
            $proveedor->deuda_60_dias = $deudas[$proveedor_id]->deuda_60_dias;
            $proveedor->deuda_90_dias = $deudas[$proveedor_id]->deuda_90_dias;
            $proveedor->total = $deudas[$proveedor_id]->total +  $saldo_inicial;
        } else {
            $proveedor->deuda_30_dias = 0;
            $proveedor->deuda_60_dias = 0;
            $proveedor->deuda_90_dias = 0;
            $proveedor->total = 0;
        }
    
        // Agrega el saldo inicial al proveedor si existe
        if (isset($saldos_iniciales[$proveedor_id])) {
            $proveedor->saldo_inicial_cuenta_corriente = $saldos_iniciales[$proveedor_id]->saldo_inicial_cuenta_corriente;
        } else {
            $proveedor->saldo_inicial_cuenta_corriente = 0;
        }
    }
    
    return $proveedores;

    } 

    public function GetDataCtaCteOld($comercio_id) {
       $data = compras_proveedores::select(
            'proveedores.id',
            'proveedores.id_proveedor',
            'proveedores.nombre as nombre_proveedor',
            \DB::raw('0 as saldo_inicial_cuenta_corriente'),
            \DB::raw('ROUND(IFNULL(SUM(CASE WHEN compras_proveedores.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN compras_proveedores.deuda ELSE 0 END), 0), 2) as deuda_30_dias'),
            \DB::raw('ROUND(IFNULL(SUM(CASE WHEN compras_proveedores.created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND compras_proveedores.created_at < DATE_SUB(NOW(), INTERVAL 31 DAY) THEN compras_proveedores.deuda ELSE 0 END), 0), 2) as deuda_60_dias'),
            \DB::raw('ROUND(IFNULL(SUM(CASE WHEN compras_proveedores.created_at < DATE_SUB(NOW(), INTERVAL 60 DAY) THEN compras_proveedores.deuda ELSE 0 END), 0), 2) as deuda_90_dias'),
            \DB::raw('ROUND(IFNULL(SUM(compras_proveedores.deuda), 0), 2) as total')
        )
            ->join('proveedores', 'proveedores.id', 'compras_proveedores.proveedor_id')
            ->where('compras_proveedores.comercio_id', $comercio_id);
        
        if ($this->search != 0) {
            $data->where('proveedores.nombre', 'like', '%' . $this->search . '%');
        }
        
        $data = $data->groupBy( 'proveedores.id','proveedores.id_proveedor', 'proveedores.nombre')
            ->orderBy('proveedores.id', 'desc')
            ->get();
        
    }
    
    
    public function collection()
    {
        $data =[];

      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;
        
     $data = $this->GetDataCtaCte($comercio_id);
       
    foreach ($data as $dato_cta_cte) {
        unset($dato_cta_cte->id);
    }

    return $data;

    }



    //cabeceras del reporte
    public function headings() : array
    {
      return ["COD PROVEEDOR","NOMBRE","SALDO INICIAL","DEUDA 0 A 30 DIAS","DEUDA 30 A 60 DIAS","DEUDA MAS 60 DIAS","TOTAL DEUDA"];
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
        return 'Reporte de Ventas';
    }


}
