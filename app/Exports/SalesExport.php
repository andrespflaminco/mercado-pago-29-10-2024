<?php

namespace App\Exports;

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


class SalesExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{

    protected $usuarioSeleccionado, $clienteId,$estado_pago,$estado,$metodo_pago, $estado_facturacion,$dateFrom, $dateTo;

    function __construct($sucursal_id,$usuarioSeleccionado, $clienteId, $estado_pago,$estado, $metodo_pago,$estado_facturacion, $f1, $f2) {
        $this->sucursal_id = $sucursal_id;
        $this->cliente_id = $clienteId;
        $this->usuario_id = $usuarioSeleccionado;
        $this->estado_pago_id = $estado_pago;
        $this->estado_id = $estado;
        $this->metodo_pago_id = $metodo_pago;
        $this->estado_facturacion = $estado_facturacion;

        $this->usuarioId = explode(",", $usuarioSeleccionado);
        $this->metodopagoId = explode(",", $metodo_pago);
        $this->clienteId = explode(",", $clienteId);
        $this->EstadoId = explode(",", $estado);


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

        } else {
          $from = Carbon::parse('2020-01-01')->format('Y-m-d').' 00:00:00';
          $to   = Carbon::parse(Carbon::now())->format('Y-m-d') . ' 23:59:59';
        }


      if($this->estado_pago_id !== '' )
      {
        if($this->estado_pago_id !== 'Pago' )
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

      $data = Sale::join('users as u','u.id','sales.user_id')
      ->join('metodo_pagos as m','m.id','sales.metodo_pago')
      ->join('clientes_mostradors as cm','cm.id','sales.cliente_id')
      ->select('sales.nro_venta', Sale::raw("DATE_FORMAT(sales.created_at,'%d-%m-%Y %H:%i') as fecha"),'cm.nombre as nombre_cliente','sales.status','sales.items',Sale::raw("(sales.subtotal) as subtotal"),Sale::raw("IFNULL(sales.descuento,0) as descuento"),Sale::raw("(IFNULL(sales.recargo,0) ) as recargo"),Sale::raw("(IFNULL(sales.iva,0) ) as iva"),Sale::raw("(sales.subtotal - IFNULL(sales.descuento,0) + IFNULL(sales.recargo,0) + IFNULL(sales.iva,0) ) as total"),'m.nombre as metodo_pago','u.name as user','sales.deuda','sales.nro_factura')
      ->whereBetween('sales.created_at', [$from, $to]);

      if($this->usuario_id > 0) {
      $data = $data->whereIn('sales.user_id', $this->usuarioId);
      }

      if($this->cliente_id > 0) {
      $data = $data->whereIn('sales.cliente_id', $this->clienteId);
      }

      if($this->estado_pago_id > 0) {
        $data = $data->whereRaw($this->estado_pago_buscar);
      }

      if($this->metodo_pago_id > 0) {
        $data = $data->whereIn('sales.metodo_pago', $this->metodopagoId);
      }

      if($this->estado_id > 0){
      $data = $data->whereIn('sales.status', $this->EstadoId);
      }
      
      if($this->estado_facturacion !== "all"){
      
      if($this->estado_facturacion == 1){
      $data = $data->where('sales.nro_factura','<>', null);
      }
      if($this->estado_facturacion == 0){
      $data = $data->where('sales.nro_factura', null);
      }
      
      }

      $data = $data->whereBetween('sales.created_at', [$from, $to])
      ->where('sales.comercio_id', $this->sucursal_id)
      ->where('sales.eliminado', 0)
      ->orderBy('sales.id','desc')
      ->get();

        return $data;


    }



    //cabeceras del reporte
    public function headings() : array
    {
      return ["NRO VENTA","FECHA","CLIENTE","ESTADO","CANT. ITEMS","SUBTOTAL","DESCUENTO","RECARGO","IVA","TOTAL","FORMA DE PAGO","USUARIO","DEUDA","FACTURA"];
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
