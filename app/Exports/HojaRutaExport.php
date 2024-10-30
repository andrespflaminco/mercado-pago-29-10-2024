<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\ClientesMostrador;
use App\Models\pagos_facturas;
use App\Models\SaleDetail;
use DB;

use Illuminate\Support\Facades\Auth;


use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas
use Carbon\Carbon;


class HojaRutaExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{

    protected $id_hoja_ruta;

    function __construct($id_hoja_ruta) {
        $this->id_hoja_ruta = $id_hoja_ruta;

    }


    public function collection()
    {
        $data =[];


            if(Auth::user()->comercio_id != 1)
            $comercio_id = Auth::user()->comercio_id;
            else
            $comercio_id = Auth::user()->id;

            if($this->id_hoja_ruta !== 0) {


              /////////////////////////////////////////////////////////////////////////////////////////////////////////

              $data = SaleDetail::join('products','products.id','sale_details.product_id')
              ->join('sales', 'sales.id', 'sale_details.sale_id')
              ->join('users as u', 'u.id', 'sales.user_id')
              ->join('clientes_mostradors as cm','cm.id','sales.cliente_id')
              ->leftjoin('hoja_rutas as hr','hr.id','sales.hoja_ruta')
              ->select('sales.id','cm.nombre as nombre_cliente','cm.telefono','cm.direccion','cm.localidad','cm.provincia','sales.items',Sale::raw('(sales.total + sales.recargo) as total'),'u.name as user','sales.deuda',DB::raw('GROUP_CONCAT(" ",sale_details.quantity," ",products.name," ",sale_details.comentario," ") as prod'),'sales.observaciones')->whereIn('sale_id', function($query) {
                $query->select(DB::raw('id'))
                      ->from('sales')
                      ->whereRaw('sale_details.sale_id = sales.id');

              })
              ->where('sale_details.eliminado',0)
              ->where('sales.comercio_id', $comercio_id)
              ->where('sales.hoja_ruta', $this->id_hoja_ruta)
              ->groupBy('sale_details.sale_id','sales.id','cm.nombre','cm.telefono','cm.direccion','cm.localidad','cm.provincia','sales.items','sales.total','u.name','sales.created_at','sales.observaciones','sales.deuda','sales.recargo','sales.cash')
              ->orderBy('sales.created_at','desc')
              ->get();



        return $data;

      } else {




          /////////////////////////////////////////////////////////////////////////////////////////////////////////

          $data = SaleDetail::join('products','products.id','sale_details.product_id')
          ->join('sales', 'sales.id', 'sale_details.sale_id')
          ->join('users as u', 'u.id', 'sales.user_id')
          ->leftjoin('hoja_rutas as hr','hr.id','sales.hoja_ruta')
          ->join('metodo_pagos as m','m.id','sales.metodo_pago')
          ->join('clientes_mostradors as cm','cm.id','sales.cliente_id')
          ->leftjoin('pagos_facturas','pagos_facturas.id_factura','sales.id')
          ->select('sales.id','cm.nombre as nombre_cliente','cm.telefono','cm.direccion','cm.localidad','cm.provincia','sales.items','sales.total','u.name as user',Sale::raw('(sales.total - sales.cash - IFNULL(SUM(pagos_facturas.monto), 0)) AS monto'),DB::raw('GROUP_CONCAT(" ",sale_details.quantity," ",products.name," ",sale_details.comentario," ") as prod'),'sales.observaciones')
          ->whereIn('sale_id', function($query) {
            $query->select(DB::raw('id'))
                  ->from('sales')
                  ->whereRaw('sale_details.sale_id = sales.id');

          })
          ->where('sale_details.eliminado',0)
          ->where('sales.comercio_id', $comercio_id)
          ->groupBy('sale_details.sale_id','sales.id','cm.nombre','cm.telefono','cm.direccion','cm.localidad','cm.provincia','sales.items','sales.total','u.name','sales.created_at','sales.observaciones','sales.cash')
          ->orderBy('sales.created_at','desc')
          ->get();




    return $data;


      }
    }

    //cabeceras del reporte
    public function headings() : array
    {
      return ["ID PEDIDO","CLIENTE","TELEFONO","DIRECCION","CIUDAD","PROVINCIA","CANT. ITEMS","IMPORTE","VENDEDOR","A COBRAR","PRODUCTOS","OBSERVACION"];
    }


    public function startCell(): string
    {
        return 'A2';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2 => ['font' => ['bold' => true ]],
        ];
    }

    public function title(): string
    {
        return 'Reporte de Ventas';
    }


}
