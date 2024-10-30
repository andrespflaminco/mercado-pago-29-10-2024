<?php

namespace App\Exports;

use App\Models\ClientesMostrador;
use Illuminate\Support\Facades\Auth;
use App\Models\gastos;
use App\Models\Sale;
use App\Models\wocommerce;
use App\Models\cajas;
use App\Models\pagos_facturas;
use App\Models\beneficios;
use App\Models\UsersController;
use App\Models\EtiquetaGastos;


use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas
use Carbon\Carbon;


class CajaExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{

    protected $cajaid;

    function __construct($cajaid) {
        $this->cajaid = $cajaid;


    }


    public function collection()
    {
        $data =[];


      $data = pagos_facturas::leftjoin('bancos','bancos.id','pagos_facturas.banco_id')
      ->leftjoin('metodo_pagos','metodo_pagos.id','pagos_facturas.metodo_pago')
      ->select('pagos_facturas.id',
              pagos_facturas::raw('
            CASE
                WHEN pagos_facturas.id_factura IS NOT NULL THEN "VENTA"
                WHEN pagos_facturas.id_compra IS NOT NULL THEN "COMPRA"
                WHEN pagos_facturas.id_gasto <> 0 THEN "GASTO"
                WHEN pagos_facturas.id_ingresos_retiros IS NOT NULL THEN (
                    SELECT tipo FROM ingresos_retiros WHERE id = pagos_facturas.id_ingresos_retiros
                )
                ELSE ""
            END AS tipo_operacion'
        ),
        pagos_facturas::raw('
            CASE
                WHEN pagos_facturas.id_factura IS NOT NULL THEN (
                    SELECT nro_venta FROM sales WHERE id = pagos_facturas.id_factura
                )
                WHEN pagos_facturas.id_compra IS NOT NULL THEN (
                    SELECT nro_compra FROM compras_proveedores WHERE id = pagos_facturas.id_compra
                )
                WHEN pagos_facturas.id_gasto IS NOT NULL THEN (
                    SELECT id FROM gastos WHERE id = pagos_facturas.id_gasto
                )
                WHEN pagos_facturas.id_ingresos_retiros IS NOT NULL THEN (
                    SELECT id FROM ingresos_retiros WHERE id = pagos_facturas.id_ingresos_retiros
                )
                ELSE ""
            END AS numero_operacion'
        ),
      pagos_facturas::raw("DATE_FORMAT(pagos_facturas.created_at,'%d-%m-%Y %H:%i') as fecha"),
      pagos_facturas::raw('(pagos_facturas.monto + IFNULL(pagos_facturas.monto_ingreso_retiro, 0)  + IFNULL(pagos_facturas.recargo, 0) + IFNULL(pagos_facturas.iva_recargo, 0) + IFNULL(pagos_facturas.iva_pago, 0) - IFNULL(pagos_facturas.monto_gasto, 0) - IFNULL(pagos_facturas.monto_compra, 0) ) AS monto'),  
      pagos_facturas::raw('bancos.nombre AS banco'),
      pagos_facturas::raw('metodo_pagos.nombre AS metodo_pago'))
      ->where('pagos_facturas.eliminado',0)
      ->where('pagos_facturas.caja', $this->cajaid)->get();

      //dd($data);
      return $data;


    }



    //cabeceras del reporte
    public function headings() : array
    {
      return ["ID","OPERACION","NRO OPERACION","FECHA","MONTO","BANCO","METODO DE PAGO"];
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
        return 'Detalle de caja_ ';
    }


}
