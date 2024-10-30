<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\saldos_iniciales;
use App\Models\ClientesMostrador;
use Illuminate\Support\Facades\Auth;


use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas
use Carbon\Carbon;


class CtaCteClientesExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{

    protected $search;

    function __construct($search) {
        $this->search = $search;
    }


    public function collection()
    {
    $datos_cta_cte =[];

     if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;


      $datos_cta_cte = ClientesMostrador::leftjoin('sales','sales.cliente_id','clientes_mostradors.id')
        ->select(
          'clientes_mostradors.id',
          'clientes_mostradors.id_cliente',
          'clientes_mostradors.nombre as nombre_cliente',
          Sale::raw('0 as saldo_inicial_cuenta_corriente'),
          Sale::raw('SUM(CASE WHEN sales.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN sales.deuda ELSE 0 END) as deuda_30_dias'),
          Sale::raw('SUM(CASE WHEN sales.created_at >= DATE_SUB(NOW(), INTERVAL 61 DAY) AND sales.created_at < DATE_SUB(NOW(), INTERVAL 31 DAY) THEN sales.deuda ELSE 0 END) as deuda_60_dias'),
          Sale::raw('SUM(CASE WHEN sales.created_at < DATE_SUB(NOW(), INTERVAL 61 DAY) THEN sales.deuda ELSE 0 END) as deuda_mas_60_dias'),
          Sale::raw('SUM(sales.deuda) as total')
          )
		  ->where('sales.comercio_id', 'like', $comercio_id)
		  ->where('sales.status','<>', 'Cancelado')
		  ->where('clientes_mostradors.id','<>',1);

      if($this->search != 0) {
        $datos_cta_cte = $datos_cta_cte->where('clientes_mostradors.nombre', 'like', '%' . $this->search . '%');
      }

      $datos_cta_cte = $datos_cta_cte
      ->groupBy('clientes_mostradors.id_cliente','clientes_mostradors.id','clientes_mostradors.nombre')
      ->orderBy('clientes_mostradors.id','desc')->get();

      $saldos_iniciales = saldos_iniciales::select(
          Sale::raw('SUM(saldos_iniciales.monto) as saldo_inicial_cuenta_corriente'),'referencia_id as cliente_id')
          ->where('saldos_iniciales.tipo','cliente')
          ->where('saldos_iniciales.eliminado',0)
          ->where('saldos_iniciales.comercio_id',$comercio_id)
          ->groupBy('referencia_id')
          ->orderBy('referencia_id','desc')
          ->get();
      
      foreach ($datos_cta_cte as $dato_cta_cte) {
        foreach ($saldos_iniciales as $saldoInicial) {
            // Verificar si el ID del cliente mostrador coincide con el cliente_id del saldo inicial
            if ($dato_cta_cte->id == $saldoInicial->cliente_id) {
                // Agregar el valor de saldo_inicial_cuenta_corriente al objeto ClientesMostrador
                $dato_cta_cte->saldo_inicial_cuenta_corriente = $saldoInicial->saldo_inicial_cuenta_corriente;
                // Romper el bucle interior ya que hemos encontrado el saldo inicial para este cliente mostrador
                break;
            }
        }
        }
        
        foreach ($datos_cta_cte as $dato_cta_cte) {
            unset($dato_cta_cte->id);
        }

        return $datos_cta_cte;


    }



    //cabeceras del reporte
    public function headings() : array
    {
      return ["COD CLIENTE","NOMBRE","SALDO INICIAL","DEUDA 0 A 30 DIAS","DEUDA 30 A 60 DIAS","DEUDA MAS DE 60 DIAS","DEUDA TOTAL"];
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
