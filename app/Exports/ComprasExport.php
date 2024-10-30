<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\compras_proveedores;
use App\Models\proveedores;
use App\Models\ClientesMostrador;
use Illuminate\Support\Facades\Auth;


use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas
use Carbon\Carbon;


class ComprasExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{

    protected $id_compra, $proveedor_id,$estado_pago, $dateFrom, $dateTo;

    function __construct($id_compra, $proveedor_id, $estado_pago, $f1, $f2) {
        $this->proveedor_id = $proveedor_id;
        $this->id_compra = $id_compra;
        $this->estado_pago_id = $estado_pago;
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

          $this->estado_pago_buscar = ' compras_proveedores.deuda > 0 ';
        } else {
          $this->estado_pago_buscar = ' compras_proveedores.deuda <= 0';
        }

      }


      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;

      $data = compras_proveedores::select('compras_proveedores.nro_compra', compras_proveedores::raw("DATE_FORMAT(compras_proveedores.created_at,'%d-%m-%Y %H:%i') as fecha"),'proveedores.nombre as nombre_proveedor','compras_proveedores.items','compras_proveedores.subtotal','compras_proveedores.iva','compras_proveedores.total','compras_proveedores.deuda','compras_proveedores.observaciones')
     ->join('proveedores','proveedores.id','compras_proveedores.proveedor_id')
     ->where('compras_proveedores.eliminado',0)
     ->where('compras_proveedores.comercio_id', 'like', $comercio_id)
     ->whereBetween('compras_proveedores.created_at', [$from, $to]);

     if($this->proveedor_id != 0) {
       $data = $data->where('proveedores.id',$this->proveedor_id);
     }

     if($this->id_compra != 0) {
       $data = $data->where('compras_proveedores.id', $this->id_compra);
     }

     if($this->estado_pago != 0) {
       $data = $data->whereRaw($this->estado_pago);

     }

     $data = $data->orderBy('compras_proveedores.id','desc')->get();

    return $data;

    }



    //cabeceras del reporte
    public function headings() : array
    {
      return ["ID","FECHA","PROVEEDOR","CANT. ITEMS","SUBTOTAL","IVA","TOTAL","DEUDA","OBSERVACIONES"];
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
