<?php

namespace App\Exports;

use App\Models\facturacion;
use App\Models\detalle_facturacion;
use App\Models\Sale;
use App\Models\ClientesMostrador;
use Illuminate\Support\Facades\Auth;


use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas
use Carbon\Carbon;


class FacturasExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{

    protected $sucursal_id,$tipo_comprobante_buscar,$facturas_repetidas, $clienteId,$estado_pago, $dateFrom, $dateTo;

    function __construct($sucursal_id,$tipo_comprobante_buscar,$facturas_repetidas, $clienteId,$estado_pago, $f1, $f2) {
        
        //dd($sucursal_id,$tipo_comprobante_buscar,$facturas_repetidas, $clienteId,$estado_pago, $f1, $f2);
        $this->sucursal_id = $sucursal_id;
        $this->cliente_id = $clienteId;
        $this->tipo_comprobante_buscar = $tipo_comprobante_buscar;
        $this->estado_pago_id = $estado_pago;
        $this->facturas_repetidas = $facturas_repetidas;

        $this->cliente_seleccionado = explode(",", $clienteId);

        $this->dateFrom = $f1;
        $this->dateTo = $f2;

    }


    public function collection()
    {
        $data =[];

            if($this->dateFrom !== '' || $this->dateTo !== '')
            {
              $from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
              $to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';

            }

            if($this->estado_pago !== '' )
            {
              if($this->estado_pago !== 'Pago' )
              {

                $this->estado_pago_buscar = ' sales.deuda > 0 ';
              } else {
                $this->estado_pago_buscar = ' sales.deuda = 0';
              }

            }


              if(Auth::user()->comercio_id != 1)
              $comercio_id = Auth::user()->comercio_id;
              else
              $comercio_id = Auth::user()->id;

             $facturasRepetidas = facturacion::select('sale_id')
                ->groupBy('sale_id')
                ->havingRaw('COUNT(sale_id) > 1')
                ->pluck('sale_id');
                    
            $facturasNoRepetidas = facturacion::select('sale_id')
                ->groupBy('sale_id')
                ->havingRaw('COUNT(sale_id) < 2')
                ->pluck('sale_id');
                

            $data = facturacion::join('sales','sales.id','facturacions.sale_id')
                   ->join('users as u', 'u.id', 'sales.user_id')
                   ->join('metodo_pagos as m','m.id','sales.metodo_pago')
                   ->join('bancos as b','b.id','m.cuenta')
                   ->join('clientes_mostradors as cm','cm.id','sales.cliente_id')
                   ->select('facturacions.created_at as fecha_facturacion','sales.created_at','facturacions.tipo_comprobante','facturacions.nro_factura','cm.nombre as nombre_cliente','facturacions.cuit_comprador','facturacions.nota_credito','facturacions.subtotal','facturacions.iva','facturacions.total')
                   ->whereBetween('sales.created_at', [$from, $to])
                   ->where('sales.comercio_id', $this->sucursal_id);
                   
                    if(0 < $this->clienteId) {
                    dd($this->clienteId);
                    $data = $data->whereIn('facturacions.cliente_id', $this->clienteId); //  el cliente
        
                    }
                    
                    if(0 < $this->estado_pago) {
                    
                    $data = $data->whereRaw($this->estado_pago_buscar);

                    }
                    
                    if(0 < $this->facturas_repetidas) {
                    
                    if($this->facturas_repetidas == 1) {
                    $data = $data->whereIn('facturacions.sale_id', $facturasRepetidas); // Filtra solo aquellos que se repiten
                    }
                    
                    if($this->facturas_repetidas == 2) {
                    $data = $data->whereIn('facturacions.sale_id', $facturasNoRepetidas); // Filtra solo aquellos que no se repiten
                    }
                    
                        
                    }
                    
                    
                    if(0 < $this->tipo_comprobante_buscar) {
                    
                    if($this->tipo_comprobante_buscar != "NC") {    
                    $data = $data->where('facturacions.tipo_comprobante', $this->tipo_comprobante_buscar); // Filtra por tipo de comprobante
                    } else {
                    $data = $data->whereRaw('facturacions.nota_credito IS NOT NULL'); // Filtra por tipo de comprobante    
                    }
                        
                    }
                    

                   $data = $data->whereRaw('sales.nro_factura IS NOT NULL')
                   ->groupBy('facturacions.id','facturacions.nro_factura','facturacions.nota_credito','facturacions.created_at','sales.created_at','sales.nro_venta','sales.id','facturacions.total','facturacions.subtotal','facturacions.iva','u.name','sales.cash','sales.estado_pago','sales.deuda','m.nombre','cm.nombre','cm.email','b.nombre','m.nombre','facturacions.tipo_comprobante',
                    'facturacions.cuit_comprador')
                    ->get();
                   
                   //->toSql();
                   
                    //dd($data);
                    
                    return $data;


    }



    //cabeceras del reporte
    public function headings() : array
    {
      return ["FECHA FACTURACION","FECHA VENTA","TIPO","NUMERO","NOMBRE CLIENTE","CUIT CLIENTE","NOTA CREDITO","NETO GRAVADO","IVA","TOTAL"];
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
